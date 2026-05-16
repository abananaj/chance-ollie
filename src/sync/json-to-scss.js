/**
 * json-to-scss.js
 * Apply JSON token values to ct-variables.scss.
 *
 * Strategy:
 *  1. For each JSON token, find the matching SCSS variable (by name).
 *  2. If the values differ, overwrite the value in place, preserving
 *     the original line's indentation, !default flags, and inline comments.
 *  3. If the SCSS variable doesn't exist at all, append it at the end
 *     of the file, grouped by category, under an auto-added section comment.
 *
 * The file is never partially written — the full updated content is returned
 * so the caller can decide whether to write (--dry-run support).
 */

import { TOKEN_CATEGORIES } from './tokens.js';

// ─────────────────────────────────────────────────────────────────────────────
// Main function
// ─────────────────────────────────────────────────────────────────────────────

/**
 * Produce updated SCSS content with JSON token values applied.
 *
 * @param {string}                  scssContent  - Current content of ct-variables.scss
 * @param {Map<string, JsonToken>}  jsonTokens   - from extractJsonTokens()
 * @param {Map<string, ScssToken>}  scssVarMap   - from extractScssTokens()
 * @returns {{ content: string, updates: UpdateRecord[], added: JsonToken[] }}
 *
 * @typedef {object} UpdateRecord
 * @property {string} varName
 * @property {string} oldValue
 * @property {string} newValue
 * @property {number} lineNumber  1-based
 */
export function applyJsonToScss(scssContent, jsonTokens, scssVarMap) {
  const lines = scssContent.split('\n');
  const updates = [];
  const added = [];

  for (const [, jsonToken] of jsonTokens) {
    const varName = jsonToken.scssVarName.slice(1); // strip $
    const scssToken = scssVarMap.get(varName);

    if (scssToken) {
      // Token exists — check if value differs
      if (scssToken.value === jsonToken.value) continue;

      const originalLine = lines[scssToken.lineIndex];

      // Preserve: leading whitespace, !default/!global flag, inline comment
      const indent = originalLine.match(/^(\s*)/)?.[1] ?? '';
      const flag = originalLine.match(/!(default|global)/)?.[0] ?? '';
      const comment = extractInlineComment(originalLine);

      const newLine = buildScssLine(indent, varName, jsonToken.value, flag, comment);
      lines[scssToken.lineIndex] = newLine;

      updates.push({
        varName,
        oldValue: scssToken.value,
        newValue: jsonToken.value,
        lineNumber: scssToken.lineIndex + 1,
      });
    } else {
      // Token missing from SCSS — collect to append
      added.push(jsonToken);
    }
  }

  // ── Append new tokens ──────────────────────────────────────────────────────
  if (added.length > 0) {
    const byCategory = groupByCategory(added);

    // Ensure file ends with a single newline before appending
    while (lines.length > 0 && lines[lines.length - 1].trim() === '') {
      lines.pop();
    }
    lines.push('');
    lines.push(
      '// ── AUTO-ADDED by sync.js (json-to-scss) ──────────────────────────────────'
    );
    lines.push('// Edit in src/json/settings/settings.json and re-run sync.');
    lines.push('');

    for (const [categoryId, tokens] of Object.entries(byCategory)) {
      const label = tokens[0].categoryLabel;
      lines.push(`// ${label}`);
      for (const t of tokens) {
        lines.push(`${t.scssVarName}: ${t.value};`);
      }
      lines.push('');
    }
  }

  return {
    content: lines.join('\n'),
    updates,
    added,
  };
}

// ─────────────────────────────────────────────────────────────────────────────
// Formatting helpers for the change summary
// ─────────────────────────────────────────────────────────────────────────────

/**
 * Format a human-readable summary of what json-to-scss wrote.
 */
export function formatJsonToScssSummary({ updates, added, dryRun }) {
  const prefix = dryRun ? '[DRY RUN] ' : '';
  const lines = [];

  if (updates.length === 0 && added.length === 0) {
    lines.push(`${prefix}✅  SCSS already matches JSON — nothing to update.`);
    return lines.join('\n');
  }

  if (updates.length > 0) {
    lines.push(`${prefix}✏️  ${updates.length} value(s) updated in ct-variables.scss:`);
    for (const u of updates) {
      lines.push(`  $${u.varName}`);
      lines.push(`    was: ${u.oldValue}`);
      lines.push(`    now: ${u.newValue}  (line ${u.lineNumber})`);
    }
    lines.push('');
  }

  if (added.length > 0) {
    lines.push(`${prefix}➕  ${added.length} new variable(s) appended to ct-variables.scss:`);
    for (const t of added) {
      lines.push(`  ${t.scssVarName}: ${t.value};`);
    }
  }

  return lines.join('\n');
}

// ─────────────────────────────────────────────────────────────────────────────
// Internal utilities
// ─────────────────────────────────────────────────────────────────────────────

/** Extract the inline comment text (after //) from a SCSS line, or null. */
function extractInlineComment(line) {
  const m = line.match(/;\s*\/\/(.*)$/);
  return m ? m[1] : null;
}

/** Build a SCSS variable declaration line. */
function buildScssLine(indent, varName, value, flag, comment) {
  let line = `${indent}$${varName}: ${value}`;
  if (flag) line += ` !${flag}`;
  line += ';';
  if (comment !== null) line += ` //${comment}`;
  return line;
}

/** Group an array of JsonTokens by their category id. */
function groupByCategory(tokens) {
  return tokens.reduce((acc, t) => {
    (acc[t.category] ??= []).push(t);
    return acc;
  }, {});
}
