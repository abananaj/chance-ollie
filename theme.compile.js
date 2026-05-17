
import fs from 'fs';
import path from 'path';
import { fileURLToPath } from 'url';

const themeDir = path.dirname(fileURLToPath(import.meta.url));
const jsonDir = path.join(themeDir, 'src', 'json');

/**
 * Strips JS-style comments and trailing commas from a JSONC string, then parses as JSON.
 * Uses a state machine to avoid stripping // inside string literals (e.g. URLs).
 */
function parseJsonc(content) {
  let result = '';
  let i = 0;

  while (i < content.length) {
    // String literal — copy verbatim, handling escape sequences
    if (content[i] === '"') {
      result += content[i++];
      while (i < content.length) {
        if (content[i] === '\\') {
          result += content[i++];
          if (i < content.length) result += content[i++];
        } else if (content[i] === '"') {
          result += content[i++];
          break;
        } else {
          result += content[i++];
        }
      }
    }
    // Line comment — skip to end of line
    else if (content[i] === '/' && content[i + 1] === '/') {
      while (i < content.length && content[i] !== '\n') i++;
    }
    // Block comment — skip to */
    else if (content[i] === '/' && content[i + 1] === '*') {
      i += 2;
      while (i < content.length && !(content[i] === '*' && content[i + 1] === '/')) i++;
      i += 2;
    }
    else {
      result += content[i++];
    }
  }

  // Remove trailing commas before } or ]
  result = result.replace(/,(\s*[}\]])/g, '$1');
  return JSON.parse(result);
}

/**
 * Stage 1: Compile styles/blocks/*.jsonc → src/json/styles/blocks.json
 * Merges the styles.blocks content from each file.
 */
function buildBlocksJson() {
  const blocksDir = path.join(jsonDir, 'styles', 'blocks');
  const files = fs.readdirSync(blocksDir)
    .filter(f => f.endsWith('.jsonc'))
    .sort();

  const combined = {};
  for (const file of files) {
    const content = fs.readFileSync(path.join(blocksDir, file), 'utf8');
    const data = parseJsonc(content);
    if (data.styles?.blocks) {
      Object.assign(combined, data.styles.blocks);
    }
  }

  const outPath = path.join(jsonDir, 'styles', 'blocks.json');
  fs.writeFileSync(outPath, JSON.stringify({ blocks: combined }, null, 2));
  console.log('✓ styles/blocks.json written');
  return combined;
}

/**
 * Stage 2: Combine blocks.json + global.jsonc + elements.jsonc → src/json/styles.json
 */
function buildStylesJson(blocks) {
  const stylesDir = path.join(jsonDir, 'styles');

  const globalData = parseJsonc(fs.readFileSync(path.join(stylesDir, 'global.jsonc'), 'utf8'));
  const elementsData = parseJsonc(fs.readFileSync(path.join(stylesDir, 'elements.jsonc'), 'utf8'));

  const combined = {
    styles: {
      ...globalData.styles,
      ...elementsData.styles,
      blocks,
    }
  };

  const outPath = path.join(jsonDir, 'styles.json');
  fs.writeFileSync(outPath, JSON.stringify(combined, null, 2));
  console.log('✓ styles.json written');
  return combined;
}

/**
 * Stage 3: Combine settings/*.jsonc → src/json/settings.json
 */
function buildSettingsJson() {
  const settingsDir = path.join(jsonDir, 'settings');
  const files = fs.readdirSync(settingsDir)
    .filter(f => f.endsWith('.jsonc'))
    .sort();

  const combined = {};
  for (const file of files) {
    const content = fs.readFileSync(path.join(settingsDir, file), 'utf8');
    const data = parseJsonc(content);
    if (data.settings) {
      Object.assign(combined, data.settings);
    }
  }

  const output = { settings: combined };
  const outPath = path.join(jsonDir, 'settings.json');
  fs.writeFileSync(outPath, JSON.stringify(output, null, 2));
  console.log('✓ settings.json written');
  return output;
}

/**
 * Stage 4: Combine config/parts.json + patterns.json + templates.json → src/json/config.json
 */
function buildConfigJson() {
  const configDir = path.join(jsonDir, 'config');
  const files = ['parts.json', 'patterns.json', 'templates.json'];
  const combined = {};

  for (const file of files) {
    const data = JSON.parse(fs.readFileSync(path.join(configDir, file), 'utf8'));
    for (const key of Object.keys(data)) {
      if (key !== '$schema' && key !== 'version') {
        combined[key] = data[key];
      }
    }
  }

  const outPath = path.join(jsonDir, 'config.json');
  fs.writeFileSync(outPath, JSON.stringify(combined, null, 2));
  console.log('✓ config.json written');
  return combined;
}

/**
 * Final assembly: merge settings.json + styles.json + config.json → theme.json
 */
export function buildThemeJson() {
  const blocks = buildBlocksJson();
  const stylesData = buildStylesJson(blocks);
  const settingsData = buildSettingsJson();
  const configData = buildConfigJson();

  const themeJson = {
    "$schema": "https://schemas.wp.org/trunk/theme.json",
    "version": 3,
    "title": "Chance Ollie",
    "description": "Custom child theme of Ollie",
    "slug": "chance-ollie",
    ...settingsData,
    ...stylesData,
    ...configData,
  };

  const outputPath = path.join(themeDir, 'theme.json');
  fs.writeFileSync(outputPath, JSON.stringify(themeJson, null, 2));
  console.log(`✓ theme.json written to ${outputPath}`);
}

// Run when executed directly as a script
if (import.meta.url.endsWith(process.argv[1]) || process.argv[1]?.endsWith('theme.compile.js')) {
  buildThemeJson();

  // Watch mode: rebuild on any JSON/JSONC changes in src/json
  if (process.argv.includes('--watch')) {
    console.log('👁️ Watching src/json for changes...');
    fs.watch(jsonDir, { recursive: true }, (eventType, filename) => {
      if (filename && (filename.endsWith('.json') || filename.endsWith('.jsonc'))) {
        console.log(`📝 ${filename} changed, rebuilding...`);
        buildThemeJson();
      }
    });
  }
}