/**
 * wp-preset.js
 * Helpers for resolving WordPress theme.json preset references.
 *
 * theme.json styles use two formats for preset values:
 *   "var:preset|color|dark-blue"   (colon+pipe notation, used in styles objects)
 *   "var(--wp--preset--color--dark-blue)"  (CSS custom property)
 *
 * This module converts either format to the CSS custom property form,
 * which is what SCSS and the browser actually use.
 */

/**
 * Resolve a theme.json preset reference to a CSS custom property string.
 * Returns the input unchanged if it's not a recognisable preset reference.
 *
 * @param {string} ref  e.g. "var:preset|color|dark-blue"
 * @returns {string}    e.g. "var(--wp--preset--color--dark-blue)"
 */
export function resolveWpPresetRef(ref) {
  if (typeof ref !== 'string') return String(ref);

  // Format 1: var:preset|type|slug
  const colonPipe = ref.match(/^var:preset\|([^|]+)\|(.+)$/);
  if (colonPipe) {
    const type = colonPipe[1]; // e.g. "color", "font-size", "font-family"
    const slug = colonPipe[2]; // e.g. "dark-blue"
    return `var(--wp--preset--${type}--${slug})`;
  }

  // Format 2: already a CSS custom property — pass through
  if (ref.startsWith('var(--wp--')) {
    return ref;
  }

  return ref; // raw value, pass through
}

/**
 * Convert a CSS custom property reference back to the colon-pipe notation
 * used in theme.json styles objects.
 *
 * @param {string} cssVar  e.g. "var(--wp--preset--color--dark-blue)"
 * @returns {string}       e.g. "var:preset|color|dark-blue"
 */
export function toWpPresetRef(cssVar) {
  const m = cssVar.match(/^var\(--wp--preset--([a-z-]+)--([a-z0-9-]+)\)$/);
  if (!m) return cssVar;
  return `var:preset|${m[1]}|${m[2]}`;
}
