/**
 * block-hints.js
 * Inject read-only "theme.json says:" comment blocks into SCSS block files.
 *
 * This is ONE-DIRECTIONAL: blocks.json → SCSS comments.
 * It does NOT parse SCSS properties back into blocks.json, because block-level
 * SCSS often overrides/extends theme.json styles in ways that are intentional
 * and hard to reconcile automatically.
 *
 * What it does:
 *  - Reads blocks.json (the styles.blocks section)
 *  - For each block mapped in block-map.json, serialises its theme.json styles
 *    as a structured comment block
 *  - Finds an existing AUTO-HINT comment block in the SCSS file (if any) and
 *    replaces it; otherwise prepends it at the top of the file
 *
 * The generated block looks like:
 *
 *   // ╔══════════════════════════════════════════════════════╗
 *   // ║  THEME.JSON → core/button  (auto-generated — do not edit manually)  ║
 *   // ╚══════════════════════════════════════════════════════╝
 *   // color.background : var(--wp--preset--color--dark-blue)
 *   // color.text       : var(--wp--preset--color--white)
 *   // :hover.color.background : var(--wp--preset--color--dark-green)
 *   // ── end theme.json hints ──────────────────────────────────────────────
 */

import { resolveWpPresetRef } from './wp-preset.js';

// Markers used to locate and replace the hint block
const HINT_START = '// ╔══ THEME.JSON →';
const HINT_END = '// ── end theme.json hints';

// ─────────────────────────────────────────────────────────────────────────────
// Main function
// ─────────────────────────────────────────────────────────────────────────────

/**
 * Apply block hints to a SCSS file's content.
 *
 * @param {string} scssContent     - Current content of the SCSS file
 * @param {string} blockName       - e.g. "core/button"
 * @param {object} blockStyles     - The styles object for this block from blocks.json
 * @returns {{ content: string, changed: boolean }}
 */
export function applyBlockHints(scssContent, blockName, blockStyles) {
  const hintBlock = buildHintBlock(blockName, blockStyles);
  const lines = scssContent.split('\n');

  // Find existing hint block (start and end lines)
  const startIdx = lines.findIndex((l) => l.startsWith(HINT_START));
  const endIdx = lines.findIndex((l) => l.startsWith(HINT_END));

  if (startIdx !== -1 && endIdx !== -1 && endIdx >= startIdx) {
    // Replace existing block
    const existing = lines.slice(startIdx, endIdx + 1).join('\n');
    if (existing === hintBlock) {
      return { content: scssContent, changed: false };
    }
    lines.splice(startIdx, endIdx - startIdx + 1, hintBlock);
  } else {
    // Prepend at top of file
    lines.unshift(hintBlock, '');
  }

  return { content: lines.join('\n'), changed: true };
}

/**
 * Remove any existing hint block from a SCSS file's content.
 */
export function removeBlockHints(scssContent) {
  const lines = scssContent.split('\n');
  const startIdx = lines.findIndex((l) => l.startsWith(HINT_START));
  const endIdx = lines.findIndex((l) => l.startsWith(HINT_END));

  if (startIdx === -1) return { content: scssContent, changed: false };

  const removeEnd = endIdx !== -1 ? endIdx + 1 : startIdx + 1;
  // Also remove the blank line immediately after the block if present
  if (lines[removeEnd]?.trim() === '') {
    lines.splice(startIdx, removeEnd - startIdx + 1);
  } else {
    lines.splice(startIdx, removeEnd - startIdx);
  }

  return { content: lines.join('\n'), changed: true };
}

// ─────────────────────────────────────────────────────────────────────────────
// Comment block builder
// ─────────────────────────────────────────────────────────────────────────────

function buildHintBlock(blockName, blockStyles) {
  const entries = flattenStyles(blockStyles);
  const width = 66;

  const header = `${HINT_START} ${blockName}  (auto — do not edit manually)`;
  const top = `// ╔${'═'.repeat(width)}╗`;
  const mid = `// ║  ${header.padEnd(width - 2)}  ║`;
  const bot = `// ╚${'═'.repeat(width)}╝`;

  const body = entries.map(([path, value]) => {
    const resolved = resolveWpPresetRef(value);
    const display = resolved !== value ? `${resolved}  /* ${value} */` : value;
    return `// ${path.padEnd(38)} ${display}`;
  });

  return [top, mid, bot, ...body, `${HINT_END} ─────────────────────────────────────────────────`].join('\n');
}

/**
 * Recursively flatten a block styles object into [dotted.path, value] pairs.
 * Pseudo-selectors (:hover, :focus) are represented as ":hover.color.text".
 */
function flattenStyles(obj, prefix = '') {
  const result = [];

  for (const [key, val] of Object.entries(obj)) {
    const path = prefix ? `${prefix}.${key}` : key;

    if (val !== null && typeof val === 'object' && !Array.isArray(val)) {
      result.push(...flattenStyles(val, path));
    } else {
      result.push([path, String(val)]);
    }
  }

  return result;
}
