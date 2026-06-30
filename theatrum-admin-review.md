# Code Review — theatrum-admin plugin

**Date:** 2026-06-29  
**Scope:** All PHP in `wp-content/plugins/theatrum-admin/` plus uncommitted working-tree changes  
**Effort:** High (8 finder angles × multi-agent, 1-vote verify)

---

## Bugs

### 1. "Grouped Overview" link shown to editors who get `wp_die` on click
**File:** `inc/patterns-admin.php:46`

The visible Patterns menu item now requires only `edit_posts`, so editors land on `edit.php?post_type=wp_block`. The `views_edit-wp_block` filter injects a "Grouped Overview" link with no capability check. Clicking it loads `admin.php?page=chance-patterns`, which is registered with `manage_options`. WordPress fires `wp_die('Sorry, you are not allowed to access this page')`.

**Fix:** Guard the filter before appending the link:
```php
add_filter('views_edit-wp_block', function ($views) {
  if (!current_user_can('manage_options')) return $views;
  $url = admin_url('admin.php?page=chance-patterns');
  $views['ct_grouped_overview'] = '<a href="' . esc_url($url) . '">' . esc_html__('Grouped Overview', 'chance-theater') . '</a>';
  return $views;
});
```

---

### 2. `views_edit-wp_block` filter registered twice — duplicate in both files
**Files:** `inc/design-system.php` (new, added in diff) + `inc/patterns-admin.php:46`

Both files are loaded unconditionally in `theatrum-admin.php`. WordPress fires both callbacks on every wp_block list load. Currently idempotent (same key, same value), but if either copy gets an independent URL or label change, the second callback silently wins with no error. One copy should be removed.

**Fix:** Remove the `add_filter('views_edit-wp_block', ...)` block from `inc/design-system.php` — it already exists in `inc/patterns-admin.php`.

---

### 3. `get_edit_post_link()` returns `null` for inaccessible posts → `href=""` links
**File:** `inc/patterns-admin.php` (~line 214, inside `ct_render_pattern_usage_page`)

`get_edit_post_link($post->ID)` returns `null` when the current user lacks permission to edit that post (e.g. another author's private post). `esc_url(null)` returns an empty string. The rendered link becomes `<a href="">Title</a>`, which navigates to the current admin URL on click.

A user with `edit_posts` but not `edit_others_posts` can legitimately reach this usage page and will see broken links for any post they don't own.

**Fix:**
```php
$edit_link = get_edit_post_link($post->ID);
if ($edit_link) {
  echo '<td><a href="' . esc_url($edit_link) . '">' . esc_html($post->post_title ?: '(no title)') . '</a></td>';
} else {
  echo '<td>' . esc_html($post->post_title ?: '(no title)') . '</td>';
}
```

---

### 4. `strpos($file, '.html')` matches partial filenames — wrong slugs, broken edit links
**File:** `inc/design-system.php:103` (same pattern repeated in `chance_render_patterns_page` and `chance_render_template_parts_page`)

`scandir` returns every file in the directory. `strpos('template.html.bak', '.html')` returns a truthy integer, so backup files and macOS quarantine artifacts are processed. `str_replace('.html', '', 'template.html.bak')` produces `'template.bak'` as the slug, which is then used in de-duplication checks and embedded in site-editor URLs, producing a broken edit link.

**Fix:** Replace `strpos($file, '.html') !== false` with:
```php
str_ends_with($file, '.html')  // PHP 8+
// or
pathinfo($file, PATHINFO_EXTENSION) === 'html'
```
Apply to all three scandir loops (templates, patterns, parts).

---

### 5. `pre_get_posts` injects `tax_query` on all `wp_block` admin main queries, not only the list table
**File:** `inc/patterns-admin.php:55`

The hook checks `is_admin()`, `is_main_query()`, and `post_type === 'wp_block'`, but not `$pagenow === 'edit.php'`. Any admin AJAX handler, block editor autosave, or custom admin screen that fires a main `wp_block` query in the same request where `$_GET['wp_pattern_category']` happens to be set will have an unexpected `tax_query` injected — silently filtering patterns in contexts where filtering is unintended.

**Fix:** Add a `$pagenow` guard:
```php
add_action('pre_get_posts', function ($query) {
  global $pagenow;
  if (!is_admin() || !$query->is_main_query()) return;
  if ($pagenow !== 'edit.php') return;
  if ($query->get('post_type') !== 'wp_block') return;
  // ...
});
```

---

### 6. `sr-only` class injection silently fails for mismatched attribute quote delimiters
**File:** `inc/sr-only-blocks.php:81`

The regex `class=["\']([^"\']*)["\']` uses a character class `["\']` for both the opening and closing delimiter, so it matches across mismatched quotes. For an attribute like `class="my-class'suffix`, it captures `my-class` (stopping at the single quote) and sets `$existing_classes = 'my-class'`. The first `str_replace` then searches for `class="my-class"` (not present — the real attribute has a trailing `'`). The second searches for `class='my-class'` (also not present). Neither replaces anything. The function returns unmodified `$block_content`, silently leaving the element visible even when `srOnly` is `true`.

This is an edge case in practice (WP block rendering uses consistent double quotes), but it fails silently rather than raising any error.

**Fix:** Use a proper attribute parser or anchor the closing delimiter to match the opening one:
```php
preg_match('/class=(["\'])([^"\']*)\1/', $attributes, $class_match)
```

---

## Improvements

### 7. N+1 queries in `chance_render_patterns_page` — 3 DB calls per pattern
**File:** `inc/design-system.php:257`

The pattern loop calls `wp_get_post_terms($pattern->ID, 'wp_pattern_category')`, `wp_get_post_terms($pattern->ID, 'post_tag')`, and `ct_count_pattern_usage($pattern->ID)` per row — 3 queries × N patterns on every page load of the grouped overview.

**Fix:** Batch before the loop:
```php
$all_ids = wp_list_pluck($db_patterns, 'ID');
$all_cat_terms  = wp_get_object_terms($all_ids, 'wp_pattern_category');
$all_tag_terms  = wp_get_object_terms($all_ids, 'post_tag');
// key by object_id, then look up per-pattern inside the loop
```
Usage counts can be batched with a single `SELECT post_content ... IN (ids)` pass or cached via transient.

---

### 8. `ct_render_pattern_usage_page` duplicates LIKE construction and re-queries the DB
**File:** `inc/patterns-admin.php:185`

The render function rebuilds the same two `$like_close`/`$like_comma` strings and fires an identical WHERE clause that `ct_count_pattern_usage` already owns. The count is then derived from the full result set it just fetched, making the separate `ct_count_pattern_usage` call redundant.

**Fix:** Use `count($posts)` after fetching the rows instead of calling the count function, and extract the LIKE construction to a private helper:
```php
function ct_pattern_usage_likes(int $pattern_id): array {
  global $wpdb;
  return [
    '%' . $wpdb->esc_like('"ref":' . $pattern_id . '}') . '%',
    '%' . $wpdb->esc_like('"ref":' . $pattern_id . ',') . '%',
  ];
}
```

---

### 9. Synced and unsynced pattern tables are identical HTML — extract a helper
**File:** `inc/design-system.php:410–533`

The synced (lines 412–469) and unsynced (lines 473–532) sections render the exact same six-column table structure. Any column addition, rename, or CSS change must be applied in both places. They will diverge.

**Fix:** Extract `render_patterns_table(array $grouped): void` and call it twice.

---

### 10. `chance_render_templates_page` and `chance_render_template_parts_page` are near-identical
**File:** `inc/design-system.php` (both functions)

Both functions follow the same six steps — get theme root, scandir, `get_posts`, merge + dedup by slug, sort, render table — differing only in directory name (`templates`/`parts`) and post type (`wp_template`/`wp_template_part`). Bug fixes must be applied twice.

**Fix:** Extract `chance_render_theme_items_page(string $subdir, string $post_type): void` and call it from both page callbacks.

---

## Notes

- `inc/block-custom-css.php` is commented out (`// require_once ...`) and not currently loaded. It has its own issues (using `wp_kses_post` to sanitize CSS, and a `$prefix` parameter that is passed to the recursive call but never incorporated into the generated class name, causing potential CSS selector collisions for nested blocks at the same depth). These are not active concerns but worth fixing before re-enabling the file.
- `submenus.php:209` — `if (isset($_GET['series']))` bypasses the visiting-companies exclusion even when `?series=` is an empty string. Use `if (!empty($_GET['series']))` to close the gap.
- The slug `'chance-patterns'` is hardcoded as a bare string in three places across two files (`design-system.php` registration, `patterns-admin.php` filter URL, `ct_render_pattern_usage_page` back-link). Define it as a constant or shared variable to prevent silent breakage if renamed.
