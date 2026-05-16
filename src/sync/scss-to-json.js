/**
 * scss-to-json.js
 * Apply SCSS variable values back into settings.json.
 *
 * Strategy:
 *  1. For each SCSS variable that matches a known category pattern, look up
 *     the corresponding JSON token.
 *  2. If values differ, update the JSON object in place (deep clone first).
 *  3. For SCSS variables not found in JSON at all, optionally insert them
 *     as new entries in the correct preset array.
 *
 * Only variables whose value is a raw token (not a reference to another SCSS var
 * starting with $) are considered for sync. Alias vars like `$onstage: $goldenrod`
 * are skipped.
 *
 * The caller decides whether to write the file (--dry-run support).
 */

import { TOKEN_CATEGORIES, categoryForScssVar, normalizeValue, getNestedValue } from './tokens.js';

// ─────────────────────────────────────────────────────────────────────────────
// Main function
// ─────────────────────────────────────────────────────────────────────────────

/**
 * Produce an updated settings.json object with SCSS token values applied.
 *
 * @param {object}                  settingsJson  - The full parsed JSON (with "settings" key)
 * @param {Map<string, JsonToken>}  jsonTokens    - from extractJsonTokens()
 * @param {Map<string, ScssToken>}  scssVarMap    - from extractScssTokens()
 * @param {{ addMissing?: boolean }} options
 * @returns {{ settingsCopy: object, updates: UpdateRecord[], added: AddedRecord[] }}
 *
 * @typedef {object} UpdateRecord
 * @property {string} category
 * @property {string} slug
 * @property {string} oldValue
 * @property {string} newValue
 * @property {string} scssVar
 * @property {number} scssLine   1-based
 *
 * @typedef {object} AddedRecord
 * @property {string} category
 * @property {string} slug
 * @property {string} value
 * @property {string} scssVar
 */
export function applyScssToJson(settingsJson, jsonTokens, scssVarMap, options = {}) {
  const { addMissing = false } = options;

  // Deep-clone so we don't mutate the caller's object
  const settingsCopy = JSON.parse(JSON.stringify(settingsJson));

  const updates = [];
  const added = [];

  // ── Update existing entries ──────────────────────────────────────────────
  for (const [, jsonToken] of jsonTokens) {
    const varName = jsonToken.scssVarName.slice(1);
    const scssToken = scssVarMap.get(varName);

    if (!scssToken) continue;
    if (scssToken.value.startsWith('$')) continue; // skip alias vars

    if (normalizeValue(jsonToken.value) === normalizeValue(scssToken.value)) continue;

    // Find and mutate the entry in the cloned settings
    const category = TOKEN_CATEGORIES.find((c) => c.id === jsonToken.category);
    const array = getNestedValue(settingsCopy, category.jsonPath);
    const entry = array?.find((e) => e[category.slugKey] === jsonToken.slug);

    if (entry) {
      const oldValue = entry[category.valueKey];
      entry[category.valueKey] = scssToken.value;
      updates.push({
        category: jsonToken.category,
        categoryLabel: jsonToken.categoryLabel,
        slug: jsonToken.slug,
        oldValue,
        newValue: scssToken.value,
        scssVar: jsonToken.scssVarName,
        scssLine: scssToken.lineIndex + 1,
      });
    }
  }

  // ── Add SCSS-only vars to JSON (if --add-missing flag) ──────────────────
  if (addMissing) {
    for (const [varName, scssToken] of scssVarMap) {
      if (scssToken.value.startsWith('$')) continue; // skip alias vars

      const match = categoryForScssVar(varName);
      if (!match) continue;

      const key = `${match.category.id}:${match.slug}`;
      if (jsonTokens.has(key)) continue; // already tracked

      // Add a new entry to the preset array
      const array = getNestedValue(settingsCopy, match.category.jsonPath);
      if (!Array.isArray(array)) continue;

      const newEntry = buildJsonEntry(match.category, match.slug, scssToken.value);
      array.push(newEntry);

      added.push({
        category: match.category.id,
        categoryLabel: match.category.label,
        slug: match.slug,
        value: scssToken.value,
        scssVar: `$${varName}`,
      });
    }
  }

  return { settingsCopy, updates, added };
}

// ─────────────────────────────────────────────────────────────────────────────
// Formatting helpers for the change summary
// ─────────────────────────────────────────────────────────────────────────────

/**
 * Format a human-readable summary of what scss-to-json wrote.
 */
export function formatScssToJsonSummary({ updates, added, dryRun }) {
  const prefix = dryRun ? '[DRY RUN] ' : '';
  const lines = [];

  if (updates.length === 0 && added.length === 0) {
    lines.push(`${prefix}✅  settings.json already matches SCSS — nothing to update.`);
    return lines.join('\n');
  }

  if (updates.length > 0) {
    lines.push(`${prefix}✏️  ${updates.length} value(s) updated in settings.json:`);
    for (const u of updates) {
      lines.push(`  [${u.categoryLabel}] "${u.slug}"`);
      lines.push(`    was: ${u.oldValue}`);
      lines.push(`    now: ${u.newValue}  (from ${u.scssVar}, line ${u.scssLine})`);
    }
    lines.push('');
  }

  if (added.length > 0) {
    lines.push(`${prefix}➕  ${added.length} new token(s) added to settings.json:`);
    for (const t of added) {
      lines.push(`  [${t.categoryLabel}] "${t.slug}": ${t.value}`);
    }
  }

  return lines.join('\n');
}

// ─────────────────────────────────────────────────────────────────────────────
// Internal utilities
// ─────────────────────────────────────────────────────────────────────────────

/** Build a minimal JSON preset entry from a category definition + slug + value. */
function buildJsonEntry(category, slug, value) {
  // Most presets follow { name, slug, <valueKey> }; derive a human name from slug.
  const name = slug
    .split('-')
    .map((w) => w.charAt(0).toUpperCase() + w.slice(1))
    .join(' ');

  return {
    name,
    [category.slugKey]: slug,
    [category.valueKey]: value,
  };
}
