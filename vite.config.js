import { defineConfig } from "vite";
import { buildThemeJson } from "./theme.compile.js";

export default defineConfig({
	plugins: [
		{
			name: 'theme-json-builder',
			apply: 'build',
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
