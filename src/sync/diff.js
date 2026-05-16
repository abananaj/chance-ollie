/**
 * diff.js
 * Compare JSON tokens against SCSS tokens and report conflicts.
 *
 * A "conflict" is when the same slug exists in both sources with different values.
 * "Missing in SCSS" means the JSON has a token that has no matching SCSS variable.
 * "Missing in JSON" means SCSS has a variable that matches a known category pattern
 *   but the slug doesn't exist in the JSON settings.
 */

import { TOKEN_CATEGORIES, categoryForScssVar, normalizeValue } from './tokens.js';

// ─────────────────────────────────────────────────────────────────────────────
// Core diff
// ─────────────────────────────────────────────────────────────────────────────

/**
 * @param {Map<string, JsonToken>} jsonTokens  - from extractJsonTokens()
 * @param {Map<string, ScssToken>} scssVarMap  - from extractScssTokens()
 * @returns {{ conflicts: Conflict[], missing: { inScss: JsonToken[], inJson: ScssOrphan[] } }}
 *
 * @typedef {object} Conflict
 * @property {string} slug
 * @property {string} category
 * @property {string} scssVar
 * @property {string} jsonValue
 * @property {string} scssValue
 * @property {number} scssLine    1-based line number in the SCSS file
 *
 * @typedef {object} ScssOrphan
 * @property {string} slug
 * @property {string} category
 * @property {string} scssVar
 * @property {string} scssValue
 */
export function diffTokens(jsonTokens, scssVarMap) {
  const conflicts = [];
  const missingInScss = [];
  const missingInJson = [];

  // ── JSON → SCSS pass ──────────────────────────────────────────────────────
  for (const [, jsonToken] of jsonTokens) {
    const varName = jsonToken.scssVarName.slice(1); // strip leading $
    const scssToken = scssVarMap.get(varName);

    if (!scssToken) {
      missingInScss.push(jsonToken);
      continue;
    }

    if (normalizeValue(jsonToken.value) !== normalizeValue(scssToken.value)) {
      conflicts.push({
        slug: jsonToken.slug,
        category: jsonToken.category,
        categoryLabel: jsonToken.categoryLabel,
        scssVar: jsonToken.scssVarName,
        jsonValue: jsonToken.value,
        scssValue: scssToken.value,
        scssLine: scssToken.lineIndex + 1,
      });
    }
  }

  // ── SCSS → JSON pass ──────────────────────────────────────────────────────
  // Find SCSS vars that look like a known category token but aren't in JSON.
  // Skip vars whose value is a reference to another SCSS var (starts with $),
  // since those are aliases, not primary token definitions.
  for (const [varName, scssToken] of scssVarMap) {
    if (scssToken.value.startsWith('$')) continue; // alias, not a value

    const match = categoryForScssVar(varName);
    if (!match) continue; // doesn't match any category pattern

    const key = `${match.category.id}:${match.slug}`;
    if (!jsonTokens.has(key)) {
      missingInJson.push({
        slug: match.slug,
        category: match.category.id,
        categoryLabel: match.category.label,
        scssVar: `$${varName}`,
        scssValue: scssToken.value,
        scssLine: scssToken.lineIndex + 1,
      });
    }
  }

  return {
    conflicts,
    missing: { inScss: missingInScss, inJson: missingInJson },
  };
}

// ─────────────────────────────────────────────────────────────────────────────
// Formatting
// ─────────────────────────────────────────────────────────────────────────────

/**
 * Format a human-readable diff report to print to stdout.
 */
export function formatDiffReport({ conflicts, missing }) {
  const lines = [];
  const total = conflicts.length + missing.inScss.length + missing.inJson.length;

  if (total === 0) {
    lines.push('✅  JSON and SCSS are in sync — no conflicts found.');
    return lines.join('\n');
  }

  // ── Value conflicts ──────────────────────────────────────────────────────
  if (conflicts.length > 0) {
    lines.push(`\n⚠️  ${conflicts.length} VALUE CONFLICT(S) — same slug, different value:`);
    lines.push('   To resolve: choose a direction (json-to-scss or scss-to-json).\n');

    const byCategory = groupBy(conflicts, 'categoryLabel');
    for (const [label, group] of Object.entries(byCategory)) {
      lines.push(`  ── ${label} ─────────────────────────`);
      for (const c of group) {
        lines.push(`  ${c.scssVar}`);
        lines.push(`    JSON → ${c.jsonValue}`);
        lines.push(`    SCSS → ${c.scssValue}  (ct-variables.scss line ${c.scssLine})`);
        lines.push('');
      }
    }
  }

  // ── In JSON, missing from SCSS ───────────────────────────────────────────
  if (missing.inScss.length > 0) {
    lines.push(
      `📋  ${missing.inScss.length} JSON token(s) have no SCSS variable:`,
      '    Run  node sync.js --direction=json-to-scss  to add them.\n'
    );
    const byCategory = groupBy(missing.inScss, 'categoryLabel');
    for (const [label, group] of Object.entries(byCategory)) {
      lines.push(`  ── ${label}`);
      for (const t of group) {
        lines.push(`  ${t.scssVarName}: ${t.value};`);
      }
      lines.push('');
    }
  }

  // ── In SCSS, not found in JSON ───────────────────────────────────────────
  if (missing.inJson.length > 0) {
    lines.push(
      `📋  ${missing.inJson.length} SCSS variable(s) have no matching JSON token:`,
      '    Run  node sync.js --direction=scss-to-json  to add them.\n'
    );
    const byCategory = groupBy(missing.inJson, 'categoryLabel');
    for (const [label, group] of Object.entries(byCategory)) {
      lines.push(`  ── ${label}`);
      for (const t of group) {
        lines.push(`  ${t.scssVar}: ${t.scssValue};  (line ${t.scssLine})`);
      }
      lines.push('');
    }
  }

  return lines.join('\n');
}

// ─────────────────────────────────────────────────────────────────────────────
// Utility
// ─────────────────────────────────────────────────────────────────────────────

function groupBy(arr, key) {
  return arr.reduce((acc, item) => {
    const k = item[key] ?? 'Other';
    (acc[k] ??= []).push(item);
    return acc;
  }, {});
}
