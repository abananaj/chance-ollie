# Theatrum Blocks Style Audit

Audit of SCSS files in `theatrum-blocks` plugin — identifying styles that should live in the theme rather than the plugin, so the plugin remains neutral and reusable across themes.

## Styles That Should Move to the Theme

### 1. Hard-coded Brand Colors

These blocks use color values specific to the Chance Theater palette that won't make sense in another theme:

| File | Hard-coded values |
|------|-------------------|
| `src/blocks/card-carousel/style.scss` | `#181818`, `#a7a7a7`, `font-variation-settings: "wght" 220` |
| `src/blocks/cover-card/style.scss` | `--wp--preset--color--black`, `--wp--preset--color--light-yellow`, `--wp--preset--color--redorange` |
| `src/blocks/cover-carousel/style.scss` | Hard-coded `rgba()` overlay values, `300px` min-height |
| `src/blocks/production-details/style.scss` | `limegreen` placeholder (incomplete — needs real values in theme) |
| `src/blocks/production-quotes/style.scss` | `--wp--preset--color--main` (theme-specific token) |
| `src/blocks/_variations/heading-toggle/style.scss` | Hard-coded purples: `#8b5cf6`, `#ede9fe`, `#e9d5ff` |

### 2. Theme Font References

| File | Issue |
|------|-------|
| `src/blocks/production-performances/style.scss` | `--wp--preset--font-family--gabarito` — Gabarito is a Chance theme font, not guaranteed to exist elsewhere |

### 3. Site-Specific Layout Assumptions

| File | Issue |
|------|-------|
| `src/blocks/card-carousel/style.scss` | 175px × 175px image dimensions, `500ms` transition hard-coded |
| `src/blocks/cover-carousel/style.scss` | `1180px` breakpoint that doesn't match WP standard breakpoints used elsewhere |

### 4. Theater-Domain-Specific Appearance

These blocks' visual style decisions assume the Chance Theater context and wouldn't be meaningful defaults for a neutral plugin:

- `src/blocks/production-*/style.scss` (all production blocks) — layout/sizing assumptions specific to theater content display
- `src/blocks/season-producer/style.scss`
- `src/blocks/board-member/style.scss`
- `src/blocks/staff-member/style.scss`

---

## Good Pattern to Replicate

`popup/style.scss` is the best example of what plugin styles *should* look like — it uses only CSS custom properties with fallbacks (`var(--foo, fallback-value)`) and no hard-coded theme tokens. Use this as the model when extracting the above to the theme.

---

## Recommended Approach

1. Strip theme-specific values from plugin blocks (leave only structural/functional CSS)
2. Override those styles in the theme using block-specific CSS in `chance-ollie/src/index.scss` or per-block style files, targeting the block's generated class names

---

## Summary by Priority

### High Priority
- `src/blocks/card-carousel/style.scss` — ~150 lines of hard-coded styling
- `src/blocks/cover-card/style.scss` — hard-coded brand colors
- `src/blocks/cover-carousel/style.scss` — hard-coded sizing and breakpoints

### Medium Priority
- `src/blocks/production-*.scss` (all production blocks)
- `src/blocks/_variations/heading-toggle/style.scss`
- `src/blocks/production-performances/style.scss` (font reference)

### Low Priority / Domain-Specific
- `src/blocks/season-producer/style.scss`
- `src/blocks/board-member/style.scss`
- `src/blocks/staff-member/style.scss`
