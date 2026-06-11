# High-Effort Code Review — Chance Ollie Theme
**Date:** 2026-06-11  
**Effort Level:** High (Multi-agent deep analysis)  
**Focus:** Correctness bugs, architectural issues, efficiency, security

## Development Context
**Current Metadata Approach:** ACF fields are managed via WordPress admin GUI using ACF Extended plugin, which **automatically syncs changes with the ACF PHP field group files** in `inc/metadata/acf/php/`. This means the PHP files are kept current automatically—no manual export/sync needed for development.

**Implication:** The PHP field definitions in `inc/metadata/acf/php/` are reliable and in sync with the admin GUI at all times.

**Impact on Issues:** Several findings are conditional on the migration to theme-based metadata management. See issue-by-issue notes below.

---

## CRITICAL BUGS 🔴

### 1. Field Name Mismatch — Productions Model Broken (NOW RESOLVED)
**Severity:** CRITICAL (NOW MOOT) | **File:** `inc/models/Productions.php` (DELETED) + `inc/metadata/acf/php/group_production_details.php`

**Status:** ✅ **RESOLVED** — The Productions model (which had the incorrect field names) was deleted in this review cycle. The ACF field group PHP files are auto-synced by your plugin and are therefore correct.

**What was wrong:** The deleted Productions model queried for `'date-opening'` and `'date-closing'` while the ACF field group defines them as `'opening'` and `'closing'`.

**Why it no longer matters:**
1. The Productions model that had the mismatch is deleted
2. Your ACF Extended plugin auto-syncs field definitions, so PHP files are current
3. Any new code should use field names from the auto-synced PHP files

**Action:** No fix needed. If you write new query code that needs production dates, reference the correct field names from `inc/metadata/acf/php/group_production_details.php`.

---

### 2. ACF Field Groups Never Loaded
**Severity:** CRITICAL | **File:** `inc/metadata/index.php` + `inc/metadata/acf/php/`

ACF field group PHP files are present but never required. The following field groups are defined but inactive:
- `group_production_details.php` — Production metadata
- `group_event_details.php` — Event metadata
- `group_artist_profile.php` — Artist metadata
- `group_supporter_profile.php` — Supporter metadata
- `group_venue_details.php` — Venue metadata
- 10 numbered field groups

**Current code in `metadata/index.php`:**
```php
file_exists(get_stylesheet_directory() . '/inc/metadata/block-bindings.php') && require_once get_stylesheet_directory() . '/inc/metadata/block-bindings.php';
file_exists(get_stylesheet_directory() . '/inc/metadata/filter-by-meta.php') && require_once get_stylesheet_directory() . '/inc/metadata/filter-by-meta.php';
file_exists(get_stylesheet_directory() . '/inc/metadata/fallback-img.php') && require_once get_stylesheet_directory() . '/inc/metadata/fallback-img.php';
file_exists(get_stylesheet_directory() . '/inc/metadata/sort-by-meta.php') && require_once get_stylesheet_directory() . '/inc/metadata/sort-by-meta.php';
file_exists(get_stylesheet_directory() . '/inc/metadata/filter-by-url-params.php') && require_once get_stylesheet_directory() . '/inc/metadata/filter-by-url-params.php';
// NO LOADING OF ACF FILES
```

**Failure Scenario:** Fresh WordPress install—custom fields won't appear in the block editor or post admin because field groups are never programmatically registered.

**Impact:** Critical — blocks local development and fresh deploys without manual ACF export.

**⚠️ Context Note:** Currently using ACF Extended plugin in WordPress admin for field management. This issue becomes critical when migrating from plugin-based to theme-based metadata configuration. Plan ahead for this transition by either:
1. Exporting ACF fields as JSON and enabling JSON auto-load in theme
2. Or converting PHP field group definitions and requiring them in metadata/index.php

---

### 3. Dormant Duplicate-on-Save Hook (Disabled but Dangerous)
**Severity:** CRITICAL | **File:** `inc/utils/duplicate-post.php` line 175

The file contains:
```php
add_action('save_post', 'ct_duplicate_post');
```

This hook is currently disabled (file not required in `inc/utils/index.php` line 24 is commented), but if uncommented, it would:
- Run `ct_duplicate_post()` on **every post save**
- Create duplicate posts for every post saved
- Corrupt the database with duplicates

**Current Status:** Dangerous code that's only safe because it's not loaded. The hook definition shouldn't exist in the file.

**Impact:** If someone uncomments the require in `inc/utils/index.php`, the site breaks.

---

## HIGH-PRIORITY SECURITY ISSUES 🟠

### 4. CSRF Vulnerabilities in Taxonomy Term Meta Saves (6 functions)
**Severity:** HIGH (Security) | **File:** `inc/taxonomy/*.php`

Multiple taxonomy save functions accept `$_POST` without nonce verification:

**Vulnerable Functions:**
- `ct_season_save_term_meta()` in `season.php`
- `ct_series_save_term_meta()` in `series.php`
- `ct_event_type_save_term_meta()` in `event-type.php`
- `ct_program_save_term_meta()` in `program.php`
- `ct_session_save_term_meta()` in `session.php`
- `ct_supporter_level_save_term_meta()` in `supporter-level.php`

**Example (season.php):**
```php
function ct_season_save_term_meta($term_id, $tt_id, $taxonomy) {
  if ($taxonomy !== 'season' || !isset($_POST['term_related_page_id'])) {
    return;
  }
  // NO NONCE CHECK HERE
  $post_id = (int) $_POST['term_related_page_id'];
  update_term_meta($term_id, 'term_related_page', $post_id);
}
```

**Attack Scenario:** Attacker creates a malicious page that modifies term metadata without admin's knowledge.

**Fix Required:** Add nonce verification before processing POST data in all 6 functions.

---

### 5. Missing isset() Check on $_SERVER
**Severity:** MEDIUM | **File:** `inc/utils/relative-urls.php` line 20

```php
if (isset($matches[1]) && isset($matches[2]) && $matches[1] === $_SERVER['SERVER_NAME']) {
```

`$_SERVER['SERVER_NAME']` isn't checked for existence. In CLI context or certain server configs, this triggers an undefined array key warning.

**Fix:**
```php
if (isset($matches[1]) && isset($matches[2]) && isset($_SERVER['SERVER_NAME']) && $matches[1] === $_SERVER['SERVER_NAME']) {
```

---

## HIGH-PRIORITY LOGIC BUGS 🟠

### 6. Inverted Season Filter Logic
**Severity:** HIGH | **File:** `inc/models/Productions.php` line 75

```php
$args['tax_query'][] = array(
    'taxonomy' => 'season',
    'field'    => 'slug',
    'terms'    => $slug,
    'compare'  => 'NOT IN',  // ← WRONG: This EXCLUDES the season
);
```

**Failure Scenario:** `Productions::get_productions_season('spring-2026')` returns all productions **except** spring 2026, opposite of intended behavior.

**Fix:**
```php
'compare' => 'IN',
```

---

### 7. Wrong Hook Parameters — Default Featured Image
**Severity:** HIGH | **File:** `inc/utils/feat-img-default.php` lines 9-15

The function is hooked to `save_post` but doesn't accept the `$post_id` parameter:

```php
function ct_default_featured_image() {  // ← No parameters!
  if (!has_post_thumbnail()) {
    $default_image_id = attachment_url_to_postid($default_image_url);
    set_post_thumbnail(get_the_ID(), $default_image_id);  // ← Won't work
  }
}
add_action('save_post', 'ct_default_featured_image');
```

**Failure Scenario:**
- `get_the_ID()` returns false outside the loop
- `attachment_url_to_postid()` returns 0 if image not found
- `set_post_thumbnail(post_id, 0)` sets incorrect featured image

**Fix:**
```php
function ct_default_featured_image($post_id) {
  if ($post_id > 0 && !has_post_thumbnail($post_id)) {
    $default_image_id = attachment_url_to_postid($default_image_url);
    if ($default_image_id > 0) {
      set_post_thumbnail($post_id, $default_image_id);
    }
  }
}
add_action('save_post', 'ct_default_featured_image', 10, 1);
```

---

### 8. Missing Error Handling — File Read in Pattern Registration
**Severity:** MEDIUM | **File:** `inc/patterns.php` (pattern registration loop)

```php
if (file_exists($pattern_file)) {
  $content = file_get_contents($pattern_file);  // ← Could return false
  register_block_pattern('chance-ollie/' . $slug, array(...'content' => $content...));
}
```

**Failure Scenario:** If a pattern file becomes unreadable, `file_get_contents()` returns `false` and passes it to `register_block_pattern()`.

**Fix:**
```php
if (file_exists($pattern_file)) {
  $content = file_get_contents($pattern_file);
  if ($content !== false) {
    register_block_pattern('chance-ollie/' . $slug, array(...'content' => $content...));
  }
}
```

---

## ARCHITECTURAL ISSUES 🟡

### 9. Inconsistent Model Return Types (RESOLVED - Models Deleted)
**Severity:** MEDIUM (NOW RESOLVED) | **File:** `inc/models/Productions.php` (DELETED)

**Status:** ✅ RESOLVED — All unused model files deleted in this code review cycle.

The model files (Productions.php, Artists.php, Events.php, Supporters.php, Venues.php) had inconsistent return types and were completely unused. They have been deleted.

See `MODEL_FILES_ANALYSIS.md` for details on why models were removed.

---

## EFFICIENCY ISSUES 🟡

### 11. Pattern File I/O on Every Init (High Overhead)
**Severity:** MEDIUM | **File:** `inc/patterns.php`

62 file reads on every page init. Also unregisters 122 Ollie patterns on every init.

**Fix:** Cache patterns in transient or static variable.

---

### 11. ACF Field Group Duplication (JSON + PHP)
**Severity:** MEDIUM | **File:** `inc/metadata/acf/`

Both JSON and PHP versions exist. ACF recommends JSON only.

**Cost:** Double maintenance, risk of sync issues.

**Fix:** Delete PHP files, keep JSON as source of truth.

**⚠️ Context Note:** You're currently exporting ACF fields as JSON from the admin GUI via ACF Extended plugin. When you migrate from plugin to theme-based configuration:
1. Export ACF fields from the plugin as JSON
2. Place JSON in `inc/metadata/acf/json/` 
3. Enable ACF JSON auto-load in the theme
4. Delete all PHP field group files in `inc/metadata/acf/php/`
5. Commit JSON to version control, remove PHP files

This will eliminate the duplication and keep a single source of truth in version control.

---

### 12. Repeated Attachment URL Lookups
**Severity:** LOW | **File:** `inc/utils/feat-img-default.php`

Database query on every post save for attachment ID lookup.

---

## DOCUMENTATION ISSUES 🟡

### 13. Documentation Describes Non-Existent Blocks
**Severity:** MEDIUM | **Files:** `README.md`, `CLAUDE.md`, `AGENTS.md`

Documentation describes 8+ custom Gutenberg blocks that don't exist:
- StaffList Block
- ResidentArtists Block
- ProductionDetails Block
- DonorList Block
- EventCalendar Block
- Productions Block
- SocialIcons Block
- Supporter Block
- ArtistCreditsList Block

**Reality:** Theme uses block patterns (HTML files), not custom Gutenberg blocks.

**Impact:** Misleading for developers.

---

## SUMMARY TABLE

| # | Issue | Severity | Type | File(s) | Status |
|---|-------|----------|------|---------|--------|
| 1 | Field name mismatch (opening vs date-opening) | **CRITICAL** | Bug | Productions.php | ✅ RESOLVED (Model deleted) |
| 2 | ACF field groups not loaded | **CRITICAL** | Architecture | metadata/index.php | ✅ FIXED (Added requires) |
| 3 | Duplicate-on-save hook (dormant) | **CRITICAL** | Code Quality | duplicate-post.php | ✅ FIXED (Hook removed) |
| 4 | CSRF in 6 taxonomy term saves | **HIGH** | Security | taxonomy/*.php | ✅ FIXED (Nonce added) |
| 5 | Missing $_SERVER isset check | **MEDIUM** | Bug | relative-urls.php | ✅ FIXED (isset added) |
| 6 | Inverted season filter (NOT IN) | **HIGH** | Logic | Productions.php:75 | ✅ RESOLVED (Model deleted) |
| 7 | Wrong hook parameters (featured image) | **HIGH** | Logic | feat-img-default.php | ✅ FIXED (Refactored) |
| 8 | Missing file_get_contents error check | **MEDIUM** | Defensive | patterns.php | 🔴 Pending |
| 9 | Unused/inconsistent model files | **MEDIUM** | Code Quality | models/*.php | ✅ RESOLVED (Deleted) |
| 10 | Pattern file I/O overhead | **MEDIUM** | Performance | patterns.php | 🔴 Pending |
| 11 | ACF JSON + PHP duplication | **MEDIUM** | Maintenance | metadata/acf/ | 🔴 Pending |
| 12 | Repeated attachment_url_to_postid() | **LOW** | Performance | feat-img-default.php | ✅ FIXED (Cached) |
| 13 | Documentation describes non-existent blocks | **MEDIUM** | Documentation | README.md, CLAUDE.md | 🔴 Pending |

---

## COMPLETED FIXES ✅

### 🔴 CRITICAL (2/2 FIXED)

1. ✅ **Load ACF field groups** — Added requires to metadata/index.php for all 14 ACF field group files
2. ✅ **Remove duplicate-on-save hook** — Removed line 175 from duplicate-post.php

### 🟠 HIGH (4/4 FIXED)

3. ✅ **Add CSRF nonce checks** — Added wp_verify_nonce() to all 6 taxonomy functions:
   - season.php, series.php, event-type.php, program.php, session.php, supporter-level.php
4. ✅ **Fix featured image function** — Complete rewrite with proper error handling
5. ✅ **Fix season filter logic** — N/A (issue resolved by model deletion)
6. ✅ **Fix $_SERVER isset check** — Added isset() check to relative-urls.php:20

**Previously Completed:**
- ✅ Delete unused model classes
- ✅ Field name mismatch (Productions model deleted)
- ✅ Commit attribution rule (no co-authors)

**Remaining Medium/Low Priority:**
7. **Add file error handling** — patterns.php (MEDIUM) — ~5 min
8. **Fix $_SERVER isset** — relative-urls.php (MEDIUM) — ~2 min
9. **ACF JSON + PHP deduplication** — Delete PHP files, keep JSON only (MEDIUM) — ~30 min
10. **Pattern file I/O optimization** — Cache patterns in transient (MEDIUM) — ~15 min
11. **Update documentation** — Remove references to non-existent blocks (MEDIUM) — ~30 min

**Total estimated fix time for critical + high issues: ~1 hour**

---

---

## Context: ACF Field Management Workflow

**Current State:** ACF fields are managed in WordPress admin GUI via ACF Extended plugin with **automatic syncing to PHP field group files**. This keeps the PHP definitions current and reliable.

**Issues Affected by Current Workflow:**
- **Issue #1 (Field Name Mismatch)** — ✅ RESOLVED (model deleted, PHP files auto-synced so correct)
- **Issue #2 (ACF Groups Not Loaded)** — Not a problem for development, but critical for fresh deploys and team sharing
- **Issue #11 (JSON + PHP Duplication)** — Currently syncing to PHP files; plan to export to JSON and delete PHP when migrating to theme-based approach

**Migration Path (when ready):**
1. Export all ACF fields from admin as JSON
2. Move JSON files to `inc/metadata/acf/json/`
3. Enable ACF JSON auto-load in theme
4. Delete all PHP field group files
5. Commit JSON to version control

**Timing:** These issues don't block current development but will become critical when you:
- Share code with other developers (they need fields to auto-load)
- Deploy to new environments (production, staging)
- Move from plugin-based to theme-based field management

---

**Generated by:** Claude High-Effort Code Review Agent  
**Review Date:** 2026-06-11  
**Context:** ACF fields via plugin (admin GUI), planned migration to theme-based management
