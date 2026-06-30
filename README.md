# 🎭 Chance Ollie Theme

Custom WordPress **child theme** for Chance Theater. Block-first (Gutenberg / FSE) on top of the **Ollie** parent theme, with theater-specific post types, taxonomies, ACF metadata, and a Vite build that also generates `theme.json`.

> 📸 **Snapshot:** This README documents the theme as of **2026-06-30**, captured prior to a round of large changes. It reflects current code, not historical intent.

**Quick facts**

| | |
|---|---|
| Parent theme | `ollie` (child theme via `style.css` → `Template: ollie`) |
| Stack | WordPress FSE · Vite · SCSS · Bootstrap 5 · GSAP · ACF (+ ACF Extended) |
| Companion plugins | `theatrum-blocks` (30+ blocks) · `theatrum-admin` · `theatrum-animation` |
| Custom Gutenberg blocks in theme | **None** — display logic lives in `theatrum-blocks` + block patterns |
| Version | ⚠️ inconsistent — `style.css` `0.1.0`, `package.json` `1.0.0`, `CHANGELOG` `1.1.1` |

🔗 [Parent project CLAUDE.md](../../../CLAUDE.md) · [Theme CLAUDE.md](CLAUDE.md) · [AGENTS.md](AGENTS.md) · [CHANGELOG.md](CHANGELOG.md)

---

## Architecture

Three layers, server-side only (no custom JS blocks in this theme):

1. **Post Types & Taxonomies** (`inc/post-type/`, `inc/taxonomy/`) — content model
2. **Metadata** (`inc/metadata/`) — ACF field groups (JSON), `register_meta`, block bindings, query filters
3. **Presentation** — `theme.json` (generated), `patterns/`, block styles, and blocks supplied by `theatrum-blocks`

### Directory map

```
chance-ollie/
├── inc/
│   ├── post-type/            # CPT defs + admin-views/ (custom list columns)
│   ├── taxonomy/             # custom taxonomy defs
│   ├── metadata/
│   │   ├── acf/json/         # ✅ ACF field groups — source of truth (auto-loaded)
│   │   ├── acf/php/          # ⚠️ stale partial export (orphaned, see Debt)
│   │   ├── js/               # editor scripts (filter-by, order-by, bindings)
│   │   ├── block-bindings.php   # register_meta + ACF→block binding
│   │   ├── fallback-img.php     # image-with-fallback helpers
│   │   ├── filter-by.php / order-by.php  # core/query meta filter + sort panels
│   │   └── index.php
│   ├── utils/                # svg uploads, relative urls, default feat img, archived status, (duplicate-post disabled)
│   ├── block-styles.php      # register_block_style() definitions
│   └── patterns.php          # unregisters Ollie parent patterns
├── patterns/                 # block patterns (only supporter-levels-list.php active)
├── parts/  templates/        # FSE template parts & templates
├── src/                      # SCSS, JS, and src/json/ (theme.json source)
│   └── json/                 # settings/ styles/ config/ → compiled to theme.json
├── dist/                     # compiled CSS/JS (committed)
├── functions.php             # setup, includes, enqueues
├── theme.json                # GENERATED — do not hand-edit
└── style.css                 # child-theme header + font imports
```

> ⚠️ Earlier versions of this README described `resources/php/`, `blocks/`, and `assets/` directories. **Those no longer exist** — PHP lives in `inc/`, compiled output in `dist/`. References corrected here.

---

## Content Model

### Custom Post Types `inc/post-type/`

| Type | Slug | Notes |
|---|---|---|
| 🎬 Production | `production` | archive `chance-productions`; series + season + tags |
| 🎟️ Event | `event` | shows, workshops, special events |
| 👤 Artist | `artist` | cast, crew, resident artists, teaching artists |
| 🎓 Class | `class` | education offerings |
| 🏛️ Venue | `venue` | performance locations |
| 💛 Supporter | `supporter` | donors, sponsors, board |
| 💼 Position | `position` | staff / board roles |

> Post types originally used a `ct-` prefix; renamed to bare slugs. Functions/hooks keep the `ct_` prefix.

### Taxonomies ✨ `inc/taxonomy/`

`season` · `series` · `event-type` · `program` · `session` · `supporter-level` · `position-type`

- **Season** auto-injects its term into secondary queries on any page tagged with a season (`pre_get_posts`) and sorts admin lists year-descending.
- **Season / Series / Supporter-level** add a **"Related Page"** column + Quick Edit selector backed by `term_related_page` term meta (nonce-protected saves).

### Metadata `inc/metadata/`

- **ACF JSON** in `acf/json/` is the source of truth — auto-loaded via `acf/settings/load_json`. **Requires the ACF plugin active** to register fields.
- **`block-bindings.php`** hand-registers a `$meta_fields` map via `register_meta()` (for REST/bindings exposure) — see Debt re: drift.
- **`filter-by.php` / `order-by.php`** add Inspector panels to the **core/query** block for meta filtering and sorting (string/number/date-time/boolean, operator whitelisting, datetime normalization).
- **`fallback-img.php`** resolves ACF arrays / attachment IDs / URLs to an image with a stage-lights placeholder fallback.

📄 Full field reference: [`inc/metadata/metadata-index.md`](inc/metadata/metadata-index.md)

---

## Presentation

- **`theme.json`** is **generated** from `src/json/` (`settings/`, `styles/`, `config/`) by `theme.compile.js`. Edit the `.jsonc` sources, never `theme.json` directly.
- **Fonts:** Gabarito (headings), Space Grotesk (body), BenchNine (headlines).
- **Block styles** (`inc/block-styles.php`): uppercase text, no-padding group, button variants (attention/blur/no-style), image boxed/no-shadow, submenu-heading links, etc.
- **Patterns:** parent **Ollie patterns are unregistered** (`inc/patterns.php`); most reusable layouts are now **synced `wp_block` patterns in the DB**, managed by `theatrum-admin`, not files. Only `patterns/supporter-levels-list.php` ships in-theme.

---

## Build & Development

```bash
npm run start      # Vite dev server (HMR)
npm run build      # production build (CSS/JS) + theme.json
npm run theme      # compile theme.json (one-shot, then watch)
npm run deploy     # production build for deploy
```

**JSON ⇄ SCSS token sync**

```bash
npm run sync        # diff JSON vs SCSS tokens
npm run sync:j2s    # JSON → SCSS variables
npm run sync:s2j    # SCSS variables → JSON
npm run sync:watch  # watch + diff
```

Compiled output (`dist/`) is committed. Always rebuild before deploying so `theme.json` and assets stay in sync.

---

## Code Review — Current State

Reviewed broadly for correctness, WP standards, security, performance, and architecture. Most prior high-severity findings are **resolved**:

✅ ACF field groups now auto-load (JSON) · ✅ CSRF nonces on all 6 term-meta saves · ✅ default-featured-image hook fixed (takes `$post_id`, guards revisions/autosave, caches lookup) · ✅ `$_SERVER['SERVER_NAME']` isset-guarded · ✅ dangerous `save_post` auto-duplicate hook removed · ✅ pattern `file_get_contents` error-checked · ✅ unused/buggy model classes deleted · ✅ SVG uploads gated to `manage_options`.

The items below are what remains.

---

## Next Steps — by severity

> Most of these are intended to be fixed before archiving.

### 🔴 High
1. **ACF hard dependency is silent.** All fields load only if the ACF plugin is active; there's no admin notice or guard. A fresh/clean environment without ACF silently loses every custom field. → Add an `admin_notice` if `class_exists('ACF')` is false.
2. **Two sources of truth for meta keys.** `block-bindings.php`'s hand-maintained `$meta_fields` list can drift from `acf/json/` field groups. It already shows stale/typo'd keys (`ammenities_*` vs `amenities_*`, `taught_by` ❌, `venue-room` vs `venue_room`). → Generate `register_meta` from ACF groups, or trim the list to what bindings actually need.

### 🟠 Medium
3. **Duplicate hook registration.** `archived-status.php`, `svg-uploads.php`, and `cat-tag-support.php` each `add_action()` **both** inside the file *and* again in `inc/utils/index.php`. Idempotent today, but a divergence trap. → Pick one place (prefer the file) and remove the other.
4. **Misnamed post status.** `custom_status_archived()` actually registers a status labeled **"Featured"** (slug `featured`) with no editor-UI integration. Name vs. behavior vs. file ("archived") all disagree. → Rename and decide if it's still needed.
5. **Orphaned ACF PHP exports.** `inc/metadata/acf/php/` holds 13 stale partial field-group files that are never loaded (JSON is canonical). → Delete to remove confusion.
6. **Version drift.** `style.css` (0.1.0) / `package.json` (1.0.0) / `CHANGELOG` (1.1.1) disagree; `style.css` header is placeholder (`Author: Me`, `Description: Ollie child theme.`). → Reconcile to one version + real metadata.
7. **`feedbucket.js` ships to the frontend unconditionally** (`functions.php`). If it's a QA/feedback widget, it shouldn't load in production. → Gate by environment or role.

### 🟡 Low
8. **`build` script runs `vite build` twice** (same for `deploy`) — a workaround that doubles build time. → Investigate and drop the second pass.
9. **`@codingStandardsIgnoreFile`** on `relative-urls.php` hides it from linting; the root-relative-URL feature is gated behind `current_theme_supports('root-relative-urls')`, which is never declared — so the whole file is effectively dormant. → Confirm intent; remove if dead.
10. **Repeated `home_url()` placeholder paths** hard-coded in `fallback-img.php` (all four fallback types point at the same image). → Centralize the placeholder.

---

## Technical Debt

- 🧩 **`inc/utils/duplicate-post.php`** is fully built (nonce'd, capability-checked, transient-guarded) but **not loaded** — the require is commented out in `inc/utils/index.php`. Note: it self-registers its admin hooks when required, so uncommenting wires it up automatically.
- 🗂️ **Patterns are split** between in-theme files and DB `wp_block` posts managed by `theatrum-admin`; there's no single inventory. See [`patterns.md`](patterns.md) for the current WP-CLI rename/cleanup log.
- 🔌 **Tight coupling to `theatrum-blocks`/`theatrum-admin`** — this theme renders almost nothing on its own; block availability and pattern management live in those plugins.
- 🧪 **No automated tests or linting** wired into the build.
- 🎞️ `dist/gsap.min.js` is committed but its enqueue is commented out — animation is handled by `theatrum-animation`.

---

## Dependencies

- **WordPress** 6.x (FSE) · **PHP** 7.4+ (uses `str_ends_with` patterns elsewhere → 8.0+ recommended)
- **Ollie** parent theme
- **ACF** + **ACF Extended** (field management; GUI auto-syncs JSON)
- **Vite 8**, **sass-embedded**, **Bootstrap 5**, **Bootstrap Icons**, **GSAP**
- Companion plugins: `theatrum-blocks`, `theatrum-admin`, `theatrum-animation`

## Resources

- [WordPress Block Editor Handbook](https://developer.wordpress.org/block-editor/)
- [theme.json Reference](https://developer.wordpress.org/themes/global-settings-and-styles/)
- [Block Bindings API](https://developer.wordpress.org/block-editor/reference-guides/block-api/block-bindings/)
- [ACF Local JSON](https://www.advancedcustomfields.com/resources/local-json/)
