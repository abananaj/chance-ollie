/**
 * sync.js — Two-way sync between theme.json settings and ct-variables.scss
 *
 * ┌──────────────────────────────────────────────────────────────────────────┐
 * │  USAGE                                                                   │
 * │                                                                          │
 * │  node sync.js                           # diff only (default)           │
 * │  node sync.js --direction=diff          # diff only, explicit           │
 * │  node sync.js --direction=json-to-scss  # update SCSS from JSON         │
 * │  node sync.js --direction=scss-to-json  # update JSON from SCSS         │
 * │  node sync.js --direction=scss-to-json --add-missing                    │
 * │                                         # also add SCSS-only vars to JSON│
 * │  node sync.js --blocks                  # inject hint comments in SCSS  │
 * │                                                                          │
 * │  Add --dry-run to any command to preview without writing files.          │
 * │  Add --watch   to any command to re-run whenever a source file changes.  │
 * └──────────────────────────────────────────────────────────────────────────┘
 *
 * Files involved:
 *   src/json/settings/settings.json  ← source of truth for JSON side
 *   src/scss/ct-variables.scss        ← source of truth for SCSS side
 *   src/json/styles/blocks.json       ← read-only source for --blocks hints
 *   src/sync/block-map.json           ← maps block names to SCSS files
 */

import fs from 'fs';
import path from 'path';
import { fileURLToPath } from 'url';

import { extractJsonTokens, extractScssTokens } from './src/sync/tokens.js';
import { diffTokens, formatDiffReport } from './src/sync/diff.js';
import { applyJsonToScss, formatJsonToScssSummary } from './src/sync/json-to-scss.js';
import { applyScssToJson, formatScssToJsonSummary } from './src/sync/scss-to-json.js';
import { applyBlockHints } from './src/sync/block-hints.js';

// ─────────────────────────────────────────────────────────────────────────────
// Paths
// ─────────────────────────────────────────────────────────────────────────────

const ROOT = path.dirname(fileURLToPath(import.meta.url));

const PATHS = {
  settings: path.join(ROOT, 'src/json/settings/settings.json'),
  scssVars: path.join(ROOT, 'src/scss/ct-variables.scss'),
  blocksJson: path.join(ROOT, 'src/json/styles/blocks.json'),
  blockMap: path.join(ROOT, 'src/sync/block-map.json'),
  scssDir: path.join(ROOT, 'src/scss'),
};

// ─────────────────────────────────────────────────────────────────────────────
// CLI argument parsing
// ─────────────────────────────────────────────────────────────────────────────

const args = process.argv.slice(2);

function getArg(name) {
  const arg = args.find((a) => a.startsWith(`--${name}=`));
  return arg ? arg.split('=')[1] : null;
}

function hasFlag(name) {
  return args.includes(`--${name}`);
}

const direction = getArg('direction') ?? 'diff';
const dryRun = hasFlag('dry-run');
const watchMode = hasFlag('watch');
const addMissing = hasFlag('add-missing');
const doBlocks = hasFlag('blocks');

// ─────────────────────────────────────────────────────────────────────────────
// Core sync logic
// ─────────────────────────────────────────────────────────────────────────────

function readJson(filePath) {
  return JSON.parse(fs.readFileSync(filePath, 'utf8'));
}

function writeJson(filePath, obj) {
  fs.writeFileSync(filePath, JSON.stringify(obj, null, 2) + '\n', 'utf8');
}

function readText(filePath) {
  return fs.readFileSync(filePath, 'utf8');
}

function writeText(filePath, content) {
  fs.writeFileSync(filePath, content, 'utf8');
}

// ─────────────────────────────────────────────────────────────────────────────

function runSync() {
  console.log(`\n${'─'.repeat(64)}`);
  console.log(`  sync.js  [${direction}]${dryRun ? '  --dry-run' : ''}${addMissing ? '  --add-missing' : ''}`);
  console.log(`${'─'.repeat(64)}\n`);

  // ── Load both sources ────────────────────────────────────────────────────
  const settingsJson = readJson(PATHS.settings);
  const scssContent = readText(PATHS.scssVars);

  const jsonTokens = extractJsonTokens(settingsJson);
  const scssVarMap = extractScssTokens(scssContent);

  // ── Dispatch direction ────────────────────────────────────────────────────
  if (direction === 'diff') {
    const result = diffTokens(jsonTokens, scssVarMap);
    console.log(formatDiffReport(result));
  }

  else if (direction === 'json-to-scss') {
    const { content, updates, added } = applyJsonToScss(scssContent, jsonTokens, scssVarMap);

    console.log(formatJsonToScssSummary({ updates, added, dryRun }));

    if (!dryRun && (updates.length > 0 || added.length > 0)) {
      writeText(PATHS.scssVars, content);
      console.log(`\n  Written: ${PATHS.scssVars}`);

      // After updating SCSS, also run diff to show remaining gaps
      const newVarMap = extractScssTokens(content);
      const remaining = diffTokens(jsonTokens, newVarMap);
      if (remaining.conflicts.length > 0 || remaining.missing.inScss.length > 0) {
        console.log('\n  Remaining issues after write:');
        console.log(formatDiffReport(remaining));
      }
    }
  }

  else if (direction === 'scss-to-json') {
    const { settingsCopy, updates, added } = applyScssToJson(
      settingsJson,
      jsonTokens,
      scssVarMap,
      { addMissing }
    );

    console.log(formatScssToJsonSummary({ updates, added, dryRun }));

    if (!dryRun && (updates.length > 0 || added.length > 0)) {
      writeJson(PATHS.settings, settingsCopy);
      console.log(`\n  Written: ${PATHS.settings}`);
      console.log('  Run  npm run theme  to rebuild theme.json from the updated settings.');
    }
  }

  else {
    console.error(`  ✖  Unknown direction "${direction}".`);
    console.error('     Valid values: diff | json-to-scss | scss-to-json');
    process.exit(1);
  }

  // ── Block hints (optional extra, any direction) ──────────────────────────
  if (doBlocks) {
    runBlockHints(dryRun);
  }

  console.log('');
}

// ─────────────────────────────────────────────────────────────────────────────
// Block hints runner
// ─────────────────────────────────────────────────────────────────────────────

function runBlockHints(dry) {
  console.log('\n── Block style hints ─────────────────────────────────────────');

  const blocksJson = readJson(PATHS.blocksJson);
  const blockStyles = blocksJson?.styles?.blocks ?? {};
  const blockMap = readJson(PATHS.blockMap);

  for (const [blockName, relPath] of Object.entries(blockMap)) {
    // Skip comment keys
    if (blockName.startsWith('//')) continue;

    const scssPath = path.join(PATHS.scssDir, relPath);

    if (!fs.existsSync(scssPath)) {
      console.log(`  ⚠️   ${blockName} → ${relPath} (file not found, skipping)`);
      continue;
    }

    const styles = blockStyles[blockName];
    if (!styles || Object.keys(styles).length === 0) {
      console.log(`  ─   ${blockName} has no styles in blocks.json`);
      continue;
    }

    const scssContent = readText(scssPath);
    const { content, changed } = applyBlockHints(scssContent, blockName, styles);

    if (!changed) {
      console.log(`  ✅  ${blockName} → ${relPath} (already up to date)`);
    } else if (dry) {
      console.log(`  [DRY RUN] would update hints in ${relPath}`);
    } else {
      writeText(scssPath, content);
      console.log(`  ✏️   ${blockName} → ${relPath} (hints updated)`);
    }
  }
}

// ─────────────────────────────────────────────────────────────────────────────
// Watch mode
// ─────────────────────────────────────────────────────────────────────────────

const WATCH_FILES = {
  'json-to-scss': [PATHS.settings],
  'scss-to-json': [PATHS.scssVars],
  'diff': [PATHS.settings, PATHS.scssVars],
};

function startWatch() {
  const filesToWatch = WATCH_FILES[direction] ?? [PATHS.settings, PATHS.scssVars];

  // Also watch blocks.json if --blocks flag is set
  if (doBlocks) filesToWatch.push(PATHS.blocksJson);

  runSync(); // initial run

  // Simple debounce: avoid firing twice for one save
  const debounceMap = new Map();

  for (const filePath of filesToWatch) {
    fs.watch(filePath, (eventType) => {
      if (eventType !== 'change') return;

      clearTimeout(debounceMap.get(filePath));
      debounceMap.set(
        filePath,
        setTimeout(() => {
          console.log(`\n  [watch] changed: ${path.relative(ROOT, filePath)}`);
          runSync();
        }, 150)
      );
    });

    console.log(`  👁️   watching ${path.relative(ROOT, filePath)}`);
  }

  console.log('\n  Press Ctrl+C to stop.\n');
}

// ─────────────────────────────────────────────────────────────────────────────
// Entry point
// ─────────────────────────────────────────────────────────────────────────────

if (watchMode) {
  startWatch();
} else {
  runSync();
}
