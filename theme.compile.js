
import fs from 'fs';
import path from 'path';
import { fileURLToPath } from 'url';

const __dirname = path.join(path.dirname(fileURLToPath(import.meta.url)), 'src', 'json');

/**
 * Reads and combines all theme sources directly into theme.json
 * Filters out $schema and version from source files
 */
export function buildThemeJson() {
  const themeJson = {
    "version": 3,
    "title": "Chance Ollie",
    "description": "Custom child theme of Ollie",
    "slug": "chance-ollie",
    "$schema": "https://schemas.wp.org/trunk/theme.json"
  };

  // Add settings from settings/settings.json
  const settingsPath = path.join(__dirname, 'settings', 'settings.json');
  const settingsData = JSON.parse(fs.readFileSync(settingsPath, 'utf8'));
  delete settingsData.$schema;
  delete settingsData.version;
  Object.assign(themeJson, settingsData);

  // Combine styles from blocks.json, elements.json, and global.json
  const stylesDir = path.join(__dirname, 'styles');
  const stylesFiles = ['global.json', 'elements.json', 'blocks.json'];
  const combinedStyles = {};

  stylesFiles.forEach(file => {
    const filePath = path.join(stylesDir, file);
    const data = JSON.parse(fs.readFileSync(filePath, 'utf8'));
    if (data.styles) {
      Object.assign(combinedStyles, data.styles);
    }
  });

  themeJson.styles = { ...combinedStyles };

  // Combine config from parts.json, patterns.json, templates.json
  const configDir = path.join(__dirname, 'config');
  const configFiles = ['parts.json', 'patterns.json', 'templates.json'];

  configFiles.forEach(file => {
    const filePath = path.join(configDir, file);
    const data = JSON.parse(fs.readFileSync(filePath, 'utf8'));

    // Merge top-level properties (excluding $schema and version)
    Object.keys(data).forEach(key => {
      if (key !== '$schema' && key !== 'version') {
        themeJson[key] = data[key];
      }
    });
  });

  // Write to theme.json in the parent theme directory
  const outputPath = path.join(__dirname, '..', '..', 'theme.json');
  fs.writeFileSync(outputPath, JSON.stringify(themeJson, null, 2));
  console.log(`✓ Theme.json written to ${outputPath}`);
}

// Run when executed directly as a script
if (import.meta.url.endsWith(process.argv[1]) || process.argv[1]?.endsWith('theme.compile.js')) {
  buildThemeJson();

  // Watch mode: rebuild on any JSON changes in src/json
  if (process.argv.includes('--watch')) {
    console.log('👁️ Watching src/json for changes...');
    const jsonDir = path.join(__dirname);
    fs.watch(jsonDir, { recursive: true }, (eventType, filename) => {
      if (filename && filename.endsWith('.json')) {
        console.log(`📝 ${filename} changed, rebuilding...`);
        buildThemeJson();
      }
    });
  }
}