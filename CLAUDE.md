# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

**Chance Ollie** is a custom WordPress child theme for Chance Theater. It's built on modern WordPress standards with Gutenberg blocks, block patterns, and a custom post type/taxonomy system to manage theater-specific content (productions, artists, events, venues, supporters).

## Build & Development Commands

### Core Scripts

```bash
npm run build          # Production build: compile SCSS, JS, and rebuild theme.json
npm run start          # Dev server with Vite (HMR enabled for localhost)
npm run theme          # Compile theme.json with watch mode
npm run deploy         # Production deployment build
```

### Utilities

```bash
npm run sync:watch     # Monitor JSON/SCSS sync (detects diffs between src/json and dist files)
npm run sync:j2s       # Convert JSON styles to SCSS variables
npm run sync:s2j       # Convert SCSS variables to JSON styles
npm run sync:blocks    # Sync block style definitions
```

**Key**: The build system uses **Vite** with a custom theme.json builder plugin. Watch the npm output for any errors during sync operations—they indicate mismatches between JSON config and compiled outputs.

## Architecture & File Structure

### Core Concept

This theme separates concerns into three layers:

1. **Post Types & Taxonomies** (`inc/post-type/`, `inc/taxonomy/`) — Define custom content types
2. **Metadata & ACF Fields** (`inc/metadata/`) — Attach custom data structures and fields to posts
3. **Blocks & Patterns** (`blocks/`, `patterns/`) — UI components for the block editor using that data

### Directory Map

```
chance-ollie/
├── inc/                          # Server-side configuration & helpers
│   ├── post-type/               # Custom post type defs (production, artist, event, etc.)
│   ├── taxonomy/                # Custom taxonomy defs (season, series, event-type, etc.)
│   ├── models/                  # Query helpers (Artists.php, Productions.php, etc.)
│   ├── utils/                   # Utilities (SVG uploads, relative URLs, etc.)
│   ├── metadata/                # ACF field groups, block bindings, filters
│   ├── block-styles.php         # Block style registration
│   ├── patterns.php             # Pattern registration
│   └── index.php files          # Consolidate multiple files into one require
├── blocks/                       # Gutenberg block implementations
│   ├── StaffList/               # Example block with index.ts, render.php, block.json
│   ├── ResidentArtists/
│   ├── Productions/
│   └── ...                       # Each block is self-contained
├── patterns/                     # Block patterns (.php files with register_block_pattern)
├── dist/                         # Compiled output (CSS, JS)
├── src/                          # Source files
│   ├── index.js                 # JS entry point (imports blocks, utilities)
│   ├── index.scss               # SCSS entry point
│   └── json/                    # Config files for theme.json builder
│       ├── config/              # parts.json, patterns.json, templates.json
│       ├── settings/            # JSONC files merged into theme.json settings
│       └── styles/              # JSONC files merged into theme.json styles
├── templates/                   # WordPress page templates
├── parts/                        # Template parts (header, footer, sidebar)
├── functions.php                # Main theme setup
└── theme.json                   # Block editor configuration (generated)
```

### Block System

Each custom block lives in `blocks/BlockName/` with:

- **block.json** — Metadata, attributes, supported post types
- **index.ts** — Editor UI (React component)
- **render.php** — Server-side rendering (what users see on the frontend)
- **index.css** — Block-specific styles

**Key Query Patterns**: Blocks use helper classes in `inc/models/` (e.g., `Productions::get_posts()`, `Artists::get_posts()`) rather than making raw WP_Query calls. This keeps data logic separate from UI logic.

### Custom Post Types

Defined in `inc/post-type/`. Main types:

- **ct-production** — Theater productions with dates, venues, credits
- **ct-artist** — People with roles, bios, photos
- **ct-event** — Calendar events (shows, workshops)
- **ct-venue** — Performance locations
- **ct-supporter** — Donors, sponsors, supporters
- **ct-class** — Educational offerings

**Admin Views**: Custom columns/sorting defined in `inc/post-type/admin-views/` to improve the post table UX.

### Metadata & ACF

All post metadata is registered in `inc/metadata/` using ACF field groups (JSON stored in `inc/metadata/acf/json/`). Key helpers:

- **block-bindings.php** — Links ACF fields to block editor data sources
- **filter-by-meta.php** — Adds filter dropdowns to admin post lists
- **sort-by-meta.php** — Makes columns sortable by meta values

### theme.json Architecture

Generated from source files in `src/json/` during build:

- **settings/** — Block editor feature toggles, color palette, typography, breakpoints
- **styles/** — Global styles (fonts, buttons, headings, block-specific overrides)
- **config/** — Parts, patterns, templates metadata

**Workflow**: Hand-edit `.jsonc` source files in `src/json/`, then run `npm run build` to generate the final `theme.json`. The Vite plugin merges all config files together.

## Development Workflows

### Adding a New Gutenberg Block

1. Create `blocks/MyBlock/` directory
2. Add `block.json` with metadata and attributes
3. Create `index.ts` for editor UI (React component)
4. Create `render.php` for server-side rendering
5. Add `index.css` for block styles
6. No explicit registration needed—blocks are auto-discovered by WordPress if block.json is in a recognized location

**Template**: Study `blocks/Productions/` or `blocks/StaffList/` for patterns. Use `incur/models/` helper classes for data queries.

### Adding a New Block Pattern

1. Create `patterns/my-pattern.php` with `register_block_pattern()` call
2. Pattern will auto-load on theme init (via `functions.php` → `patterns.php`)

### Theme Styling

1. Edit `src/index.scss` or block-specific `.css` files
2. Import Bootstrap and Bootstrap Icons as needed (they're in node_modules)
3. Run `npm run build` to compile to `dist/main.css`
4. Vite dev server (`npm run start`) has HMR for instant feedback

### Syncing Styles Between JSON & SCSS

If you update theme.json settings or styles and need to reflect those in SCSS variables:

```bash
npm run sync:s2j    # Convert SCSS variables → JSON
npm run sync:j2s    # Convert JSON → SCSS variables
npm run sync:watch  # Monitor and diff the two
```

Use this when colors, spacing, or typography are defined in both places.

## Key Dependencies

- **WordPress 5.8+** (for block support)
- **Vite** — Fast bundler and dev server
- **SCSS** — CSS preprocessor (sass-embedded)
- **Bootstrap 5** — Utility CSS framework
- **Bootstrap Icons** — Icon library
- **GSAP** — Animation library
- **ACF (Advanced Custom Fields)** — Metadata framework (plugin, not bundled)

## Block Editor Configuration (theme.json)

### Color Palette

Defined in `theme.json` settings. Main colors: yellows (goldenrod, peach), teals, greens, blues, grays, blacks. Use `color|slug` syntax in styles to reference them.

### Typography

- **Gabarito** — Headings (display font, sans-serif)
- **Space Grotesk** — Body text (modern sans-serif)
- **BenchNine** — Headlines (heavy/bold option)

Sizes range from `tiny` (0.63rem) to `huge` (4.209rem).

### Breakpoints

Custom breakpoints stored in `theme.json` settings.custom.breakpoint:

- mobile: 475px
- mobileXL: 640px
- tablet: 767px
- laptop: 1140px (content width)
- desktop: 1360px (wide width)

## Common Patterns

### Querying Posts by Meta

```php
$posts = Artists::get_posts([
    'meta_key' => 'resident-artist',
    'meta_value' => 1,
]);
```

All query helpers live in `inc/models/`. Check their signatures before writing raw WP_Query.

### Registering a New Post Type

1. Create `inc/post-type/my-type.php` with `register_post_type()` call
2. Add `require_once $inc_dir . '/post-type/my-type.php'` to the index.php
3. Hook registration to `init` action in `functions.php`

### Adding Custom Block Editor Features

Edit `theme.json` settings to enable/disable features per block type:

```json
"blocks": {
  "core/paragraph": {
    "typography": { "textIndent": "subsequent" }
  }
}
```

## Performance Notes

- **CSS Code Splitting**: Vite splits CSS per entry point to reduce initial payload
- **WordPress Packages Externalized**: `@wordpress/*` packages are loaded via wp_register_script, not bundled
- **HMR in Development**: `npm run start` enables hot module replacement for quick feedback loops

## Deployment

1. Run `npm run deploy` to build production assets
2. Commit `dist/` changes (compiled CSS/JS) alongside code changes
3. theme.json is generated during build, so always rebuild before deploying
4. No post-deploy setup needed—WordPress auto-loads theme.json and blocks

---

**For questions about WordPress best practices or block development, refer to the README.md or check the inline comments in the block render.php files.**
