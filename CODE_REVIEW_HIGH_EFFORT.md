# High-Effort Code Review — Chance Ollie Theme
**Date:** 2026-06-11  
**Effort Level:** High (Multi-agent deep analysis)  
**Focus:** Correctness bugs, architectural issues, efficiency, security

---

## CRITICAL BUGS 🔴

### 1. Field Name Mismatch — Productions Model Broken
**Severity:** CRITICAL | **File:** `inc/models/Productions.php` (multiple lines) + `inc/metadata/acf/php/group_production_details.php`

The Productions model queries for `'date-opening'` and `'date-closing'` but the ACF field group defines them as `'opening'` and `'closing'`.

**Code:**
```php
// Productions.php line 23
'meta_key' => 'date-opening',  // WRONG

// group_production_details.php line 31, 55
'name' => 'opening',           // CORRECT
```

**Failure Scenario:** Any call to `Productions::get_productions_future()` or similar returns empty results. Production dates are stored with key `opening`/`closing` but the model queries for `date-opening`/`date-closing`.

**Impact:** High — breaks all production date filtering.

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

### 9. Inconsistent Model Return Types
**Severity:** MEDIUM | **File:** `inc/models/Productions.php`

Methods return different types, forcing callers to type-check:

```php
// Returns array
public static function get_productions_future() { return $query->posts; }

// Returns WP_Query object  
public static function get_productions_season() { return $query; }

// Returns array
public static function get_production_quotes() { return $query->posts; }

// Returns WP_Query
public static function get_related_posts() { return $query; }
```

**Problem:** Unpredictable API.

**Fix:** Standardize all methods to return the same type.

---

### 10. Unused Model Classes
**Severity:** LOW | **File:** `inc/models/*.php`

No calls found to any model methods in the theme:
- `Productions::*` (5 methods)
- `Artists::*` (1 method)
- `Events::*` (3 methods)
- `Supporters::*` (1 method)
- `Venues::*` (2 methods)

**Status:** Dead code or intended for plugin use. **Requires investigation before removal.**

---

## EFFICIENCY ISSUES 🟡

### 11. Pattern File I/O on Every Init (High Overhead)
**Severity:** MEDIUM | **File:** `inc/patterns.php`

62 file reads on every page init. Also unregisters 122 Ollie patterns on every init.

**Fix:** Cache patterns in transient or static variable.

---

### 12. ACF Field Group Duplication (JSON + PHP)
**Severity:** MEDIUM | **File:** `inc/metadata/acf/`

Both JSON and PHP versions exist. ACF recommends JSON only.

**Cost:** Double maintenance, risk of sync issues.

**Fix:** Delete PHP files, keep JSON as source of truth.

---

### 13. Repeated Attachment URL Lookups
**Severity:** LOW | **File:** `inc/utils/feat-img-default.php`

Database query on every post save for attachment ID lookup.

---

## DOCUMENTATION ISSUES 🟡

### 14. Documentation Describes Non-Existent Blocks
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

| # | Issue | Severity | Type | File(s) | Fix Effort |
|---|-------|----------|------|---------|------------|
| 1 | Field name mismatch (opening vs date-opening) | **CRITICAL** | Bug | Productions.php | 5 min |
| 2 | ACF field groups not loaded | **CRITICAL** | Architecture | metadata/index.php | 5 min |
| 3 | Duplicate-on-save hook (dormant) | **CRITICAL** | Code Quality | duplicate-post.php | 5 min |
| 4 | CSRF in 6 taxonomy term saves | **HIGH** | Security | taxonomy/*.php | 30 min |
| 5 | Missing $_SERVER isset check | **MEDIUM** | Bug | relative-urls.php | 2 min |
| 6 | Inverted season filter (NOT IN) | **HIGH** | Logic | Productions.php:75 | 2 min |
| 7 | Wrong hook parameters (featured image) | **HIGH** | Logic | feat-img-default.php | 10 min |
| 8 | Missing file_get_contents error check | **MEDIUM** | Defensive | patterns.php | 5 min |
| 9 | Inconsistent model return types | **MEDIUM** | Architecture | Productions.php | 20 min |
| 10 | Unused model methods | **LOW** | Code Quality | models/*.php | **Investigate first** |
| 11 | Pattern file I/O overhead | **MEDIUM** | Performance | patterns.php | 15 min |
| 12 | ACF JSON + PHP duplication | **MEDIUM** | Maintenance | metadata/acf/ | 30 min |
| 13 | Repeated attachment_url_to_postid() | **LOW** | Performance | feat-img-default.php | 10 min |
| 14 | Documentation describes non-existent blocks | **MEDIUM** | Documentation | README.md, CLAUDE.md | 30 min |

---

## IMMEDIATE ACTION ITEMS (Priority Order)

1. **Fix field name mismatch** — Line 23, 30 in Productions.php (CRITICAL)
2. **Load ACF field groups** — Add requires to metadata/index.php (CRITICAL)
3. **Remove duplicate-on-save hook** — Delete line 175 from duplicate-post.php (CRITICAL)
4. **Add CSRF nonce checks** — All 6 taxonomy functions (HIGH)
5. **Fix featured image function** — Accept $post_id parameter, add error checks (HIGH)
6. **Fix season filter logic** — Change NOT IN to IN (HIGH)
7. **Investigate model classes** — Verify use in plugins before removing (MEDIUM)
8. **Add file error handling** — patterns.php (MEDIUM)
9. **Fix $_SERVER isset** — relative-urls.php (MEDIUM)
10. **Audit and fix inconsistent returns** — Standardize model return types (MEDIUM)

**Total estimated fix time: ~2-3 hours for all critical + high issues**

---

**Generated by:** Claude High-Effort Code Review Agent  
**Review Date:** 2026-06-11
