# Model Files Analysis — Chance Ollie Theme

## Overview
The theme has 5 model classes in `inc/models/` that provide data query helpers. **Search results show ZERO callers to any of these model methods across the entire WordPress installation.**

---

## Model Files Breakdown

### 1. **Productions.php** — 5 Methods
**Location:** `inc/models/Productions.php`  
**Namespace:** `Chance\Models\Productions`

#### Methods:
| Method | Returns | Purpose |
|--------|---------|---------|
| `get_productions_future($count=5, $offset=0, $season=null)` | `array` of posts | Get productions with future dates, optionally filtered by season |
| `get_productions_season($slug)` | `WP_Query` object | Get all productions in a given season ⚠️ **HAS BUG: Uses NOT IN instead of IN** |
| `get_production_quotes($post_id, $count=5, $offset=0)` | `array` of posts | Get quotes/reviews for a specific production |
| `get_related_posts($post_id, $count=3, $offset=0)` | `WP_Query` object | Get blog posts related to a production |

**Issues Found:**
- ⚠️ **Critical Bug at line 75:** `'compare' => 'NOT IN'` should be `'IN'` — method returns productions NOT in season
- ⚠️ **Inconsistent return types:** Some return `$query->posts` (array), others return `$query` (WP_Query object)
- ⚠️ **Field name mismatch at line 23, 30:** Queries for `'date-opening'` and `'date-closing'` but ACF defines them as `'opening'` and `'closing'`
- ❌ **Unused:** No callers found anywhere

---

### 2. **Artists.php** — 1 Method
**Location:** `inc/models/Artists.php`  
**Namespace:** `Chance\Models\Artists`

#### Methods:
| Method | Returns | Purpose |
|--------|---------|---------|
| `get_production($prod_id, $group_id='')` | `WP_Query` object | Get artist credits for a production, optionally filtered by role group |

**Details:**
- Queries the `credit` post type (not `artist`)
- Filters by production ID and optionally by role-group meta
- Orders by menu_order and title

**Issues:**
- ❌ **Unused:** No callers found

---

### 3. **Events.php** — 3 Methods
**Location:** `inc/models/Events.php`  
**Namespace:** `Chance\Models\Events`

#### Methods:
| Method | Returns | Purpose |
|--------|---------|---------|
| `get_production($prod_id, $count=10, $offset=0)` | `array` of posts | Get all events for a production |
| `get_production_future($prod_id, $count=10, $offset=0)` | `WP_Query` object | Get upcoming events for a production |
| `get_production_promos($prod_id, $count=10, $offset=0)` | `WP_Query` object | Get promotional events for a production |

**Details:**
- Filters by `production` meta key
- Excludes promo events in `get_production()` using `'compare' => 'NOT EXISTS'`
- Includes future dates only (current time check: `'compare' => '>'`)

**Issues:**
- ⚠️ **Inconsistent return types:** `get_production()` returns array, others return WP_Query
- ❌ **Unused:** No callers found

---

### 4. **Supporters.php** — 1 Method
**Location:** `inc/models/Supporters.php`  
**Namespace:** `Chance\Models\Supporters`

#### Methods:
| Method | Returns | Purpose |
|--------|---------|---------|
| `get_random_supporters($qty=1)` | `WP_Query` object | Get random supporters marked as displayable in sidebar |

**Details:**
- Filters by `display-sidebar` meta
- Uses `'orderby' => 'rand'` for randomization
- No specific order—returns truly random selection

**Issues:**
- ❌ **Unused:** No callers found

---

### 5. **Venues.php** — 2 Methods
**Location:** `inc/models/Venues.php`  
**Namespace:** `Chance\Models\Venues`

#### Methods:
| Method | Returns | Purpose |
|--------|---------|---------|
| `get_address($venue_id)` | `array` with address fields | Extract venue address from post meta (street, city, state, zip, country) |
| `get_amenities($venue_id)` | `array` or empty array | Get list of amenities for a venue |

**Details:**
- `get_address()` extracts structured address data from meta keys:
  - `street-address`
  - `locality` (city)
  - `region` (state)
  - `postal-code` (zip)
  - `country-name`
- `get_amenities()` retrieves an array stored in `amenities` meta key

**Issues:**
- ⚠️ **Missing error handling in get_address():** Direct array access on line 17 doesn't validate $data structure
- ❌ **Unused:** No callers found

---

## Search Results

**Comprehensive grep across entire wp-content/ for method names:**

```
wp-content/themes/chance-ollie/inc/models/Artists.php:    public static function get_production(...)
wp-content/themes/chance-ollie/inc/models/Events.php:    public static function get_production(...)
wp-content/themes/chance-ollie/inc/models/Events.php:    public static function get_production_future(...)
wp-content/themes/chance-ollie/inc/models/Events.php:    public static function get_production_promos(...)
wp-content/themes/chance-ollie/inc/models/Productions.php:    public static function get_productions_future(...)
wp-content/themes/chance-ollie/inc/models/Productions.php:    public static function get_productions_season(...)
wp-content/themes/chance-ollie/inc/models/Productions.php:    public static function get_production_quotes(...)
wp-content/themes/chance-ollie/inc/models/Productions.php:    public static function get_related_posts(...)
wp-content/themes/chance-ollie/inc/models/Supporters.php:    public static function get_random_supporters(...)
wp-content/themes/chance-ollie/inc/models/Venues.php:    public static function get_address(...)
wp-content/themes/chance-ollie/inc/models/Venues.php:    public static function get_amenities(...)
```

**Result:** Only the function definitions found. **Zero callers in entire installation.**

Similar methods in theatrum-blocks plugin are different functions:
- `theatrum_get_production_cast_rest_callback()` — REST endpoint, not model class
- `theatrum_get_production_quotes_rest_callback()` — REST endpoint, not model class
- `get_production_performances_rest_callback()` — REST endpoint, not model class

---

## Conclusion

### Status: **DEAD CODE**

**Evidence:**
1. ✅ 5 complete, well-documented model classes
2. ✅ 11 methods with sensible query logic
3. ❌ **ZERO calls to any method** anywhere in the codebase
4. ❌ Not used by Theatrum Blocks plugin
5. ❌ Not used by Theatrum Admin plugin
6. ❌ Not used by any theme code

### Possible Explanations:
1. **Previous architecture**: Models were used by legacy code that was refactored
2. **Future-proofing**: Written for planned features that were never implemented
3. **Plugin transition**: Code was meant to be in a plugin instead of theme
4. **Incomplete migration**: From old site that these queries were never migrated to

### Recommendation:
**Delete all 5 model files** OR keep them if:
- Plugin developers plan to use them
- They're documentation of query patterns for future development
- They're intentional scaffolding for planned features

Since they're currently **completely unused**, they add maintenance burden without value.

---

## Files to Delete (if proceeding):

```
inc/models/Productions.php
inc/models/Artists.php
inc/models/Events.php
inc/models/Supporters.php
inc/models/Venues.php
inc/models/index.php  (requires all 5 above)
```

---

**Note:** Before deletion, verify with Theatrum Blocks plugin developers that they don't plan to use these, or check git history to understand why they were written.
