import { defineConfig } from "vite";
import fs from "fs";
import path from "path";
import { fileURLToPath } from "url";
import { buildThemeJson } from "./theme.compile.js";

const themeDir = path.dirname(fileURLToPath(import.meta.url));
const jsonDir = path.join(themeDir, "src", "json");

function collectJsoncFiles() {
	// Only watch hand-authored source files, never the generated .json outputs
	const jsoncDirs = [
		path.join(jsonDir, "styles", "blocks"),
		path.join(jsonDir, "styles"),
		path.join(jsonDir, "settings"),
	];
	const files = [];
	for (const dir of jsoncDirs) {
		if (!fs.existsSync(dir)) continue;
		for (const f of fs.readdirSync(dir)) {
			if (f.endsWith(".jsonc")) {
				files.push(path.join(dir, f));
			}
		}
	}
	// Config dir uses hand-authored .json files (parts, patterns, templates)
	const configDir = path.join(jsonDir, "config");
	if (fs.existsSync(configDir)) {
		for (const f of fs.readdirSync(configDir)) {
			if (f.endsWith(".json")) {
				files.push(path.join(configDir, f));
			}
		}
	}
	return files;
}

export default defineConfig({
	plugins: [
		{
			name: 'theme-json-builder',
			apply: 'build',
			buildStart() {
				for (const file of collectJsoncFiles()) {
					this.addWatchFile(file);
				}
			},
			writeBundle() {
				buildThemeJson();
			}
		}
	],
	build: {
		emptyOutDir: true,
		cssCodeSplit: true,
		rollupOptions: {
			input: {
				main: "src/index.js"
			},
			output: {
				entryFileNames: "[name].js",
				assetFileNames: "[name].[ext]",
			},
			external: [
				// Externalize WordPress packages since they're loaded via wp_register_script
				/^@wordpress\//,
			],
		},
	},
	css: {
		preprocessorOptions: {
			scss: {
				silenceDeprecations: [
					"legacy-js-api",
					"import",
					"global-builtin",
					"color-functions",
					"if-function",
				],
			},
		},
	},
	server: {
		host: true,
		"hmr": {
			host: "chancetheater.local",
		}
	},
});
