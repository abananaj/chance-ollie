# POSTS Meta Key Summary

```sql
SELECT
  meta_key,
  COUNT(*) AS count
FROM ct_postmeta
GROUP BY meta_key
ORDER BY count DESC;
```

## All Post Types (global)

- `subtitle`
- `short_title`
- `related_all`
- `related_posts`
- `related_pages`
- `related_forms`
- `related_artists`
- `related_events`
- `related_productions`
- `related_supporters`
- `related_classes`
- `related_venues`

## Artist

`group_artist_profile`, `group_6a34abaacd1ab`

- profession
- title
- resident_artist_title
- artist_links_name 🔁
- artist_links_url 🔁
- teacher_bio
- related_wp_user

```sql


```

## Supporter

`group_supporter_profile`

- title
- supporter_type
- website
- display-donors
- display-sidebar
- related_wp_user

## Production

`group_production_content`, `group_production_media`

- BASIC ℹ️
  -- opening
  -- closing
  -- runtime
  -- intermissions
  -- venue
  -- venue*room formerly `venue_room`*
  -- ticket-note ❓
  -- ticketing-link ❌ _formerly to choose your ticket page link, now embedded popup_

- FEATURED
  -- bylines_lead 🔁
  -- bylines_text 🔁 eventually  all bylines_artis
  -- bylines_artist 🔁 stored as ID
  -- featured_quote_text group, turn into repeater ???*
  -- featured_quote_cite group, turn into repeater ???*
  -- accolades_lead 🔁
  -- accolades_text 🔁
  -- widget_content formerly `widget_content`*

- ARTWORK
  -- production_preview
  -- production_poster
  -- production_postcard
  -- production_banner
- TICKETS
  -- tickets_best - formerly `url_best`*
  -- tickets_saver - formerly `url_saver`*
  -- tickets_pwyc - formerly `url_pwyc`*

- PERFORMANCES
  -- performances_date 🔁
  -- performances_time 🔁
  -- performances_note 🔁
  -- performances_hide 🔁

- NOTES
  -- notes_icon 🔁
  -- notes_note 🔁

- CONTENT ADVISORY
  -- content_advisory
  -- add_spoilers_popup
  -- full_disclosure_spoilers

- SPECIAL EVENTS
  -- events _array of event IDs_

- BUZZ
  -- quotes_quote-text 🔁
  -- quotes_quote-cite 🔁
  -- awards_title 🔁
  -- awards_text 🔁
  -- press_release formerly `press-release`*
  -- posts _array of post IDs_

- PHOTOS
  -- production_photos
  -- rehearsal_photos
  -- playbill

## Events

_`group_event_details`_ -`date` -`start` -`end` -`ticketing-link` -`is-promo` -`venue` -`venue_room` -`production`

```sql


CREATE OR REPLACE VIEW v_events AS
SELECT
  p.ID,
  p.post_title                                                                   AS title,
  MAX(CASE WHEN pm.meta_key = 'date'     THEN pm.meta_value END)     AS date,
  MAX(CASE WHEN pm.meta_key = 'start'          THEN pm.meta_value END)     AS start,
  MAX(CASE WHEN pm.meta_key = 'end'            THEN pm.meta_value END)     AS end,
  MAX(CASE WHEN pm.meta_key = 'ticketing-link'           THEN pm.meta_value END)     AS ticketing_link,
  MAX(CASE WHEN pm.meta_key = 'is-promo'          THEN pm.meta_value END)     AS is_promo,
  MAX(CASE WHEN pm.meta_key = 'venue'            THEN pm.meta_value END)     AS venue,
  MAX(CASE WHEN pm.meta_key = 'venue_room'   THEN pm.meta_value END)     AS venue_room,
  MAX(CASE WHEN pm.meta_key = 'production'   THEN pm.meta_value END)     AS production
FROM ct_posts p
LEFT JOIN ct_postmeta pm
  ON pm.post_id = p.ID
  AND pm.meta_key IN (
    'date',
    'start',
    'end',
    'ticketing-link',
    'is-promo',
    'venue',
    'venue_room',
    'production'
  )
WHERE p.post_type   = 'event'
  AND p.post_status != 'trash'
GROUP BY p.ID, p.post_title
ORDER BY p.post_title;
SELECT * FROM v_events ORDER BY date;
```

## Venues

_`group_venue_details`_

- `amenities_icon`
- `amenities_desc`
- `address`
- `google_maps`
- `street-address`
- `transit_link`
- `locality`
- `region`
- `postal-code`
- `country-name`

---

## Class

_`group_69c3515383d9f`_

- `program`
- `session`
- `teaching_artist`
- `date_start`
- `date_end`
- `class_day`
- `time_start`
- `time_end`
- `registration_link`

```sql
CREATE OR REPLACE VIEW v_classes AS
SELECT
  p.ID,
  p.post_title                                                                   AS title,
  MAX(CASE WHEN pm.meta_key = 'teaching_artist'     THEN pm.meta_value END)     AS teaching_artist,
  MAX(CASE WHEN pm.meta_key = 'date_start'          THEN pm.meta_value END)     AS date_start,
  MAX(CASE WHEN pm.meta_key = 'date_end'            THEN pm.meta_value END)     AS date_end,
  MAX(CASE WHEN pm.meta_key = 'class_day'           THEN pm.meta_value END)     AS class_day,
  MAX(CASE WHEN pm.meta_key = 'time_start'          THEN pm.meta_value END)     AS time_start,
  MAX(CASE WHEN pm.meta_key = 'time_end'            THEN pm.meta_value END)     AS time_end,
  MAX(CASE WHEN pm.meta_key = 'registration_link'   THEN pm.meta_value END)     AS registration_link
FROM ct_posts p
LEFT JOIN ct_postmeta pm
  ON pm.post_id = p.ID
  AND pm.meta_key IN (
    'teaching_artist',
    'date_start', 'date_end', 'class_day',
    'time_start', 'time_end', 'registration_link'
  )
WHERE p.post_type   = 'class'
  AND p.post_status != 'trash'
GROUP BY p.ID, p.post_title, p.post_status
ORDER BY p.post_title;

SELECT * FROM v_classes;
-- or filter, e.g.:
SELECT * FROM v_classes ORDER BY date_start;
```

# ==========LATER

## Season

_taxonomy: `season`_ | group: `group_season_details`

| meta_key                     | type         | notes                                         |
| ---------------------------- | ------------ | --------------------------------------------- |
| `hide-season`                | true_false   | Hides from frontend                           |
| `season_press_release`       | file         | Multiple files allowed                        |
| `season_image`               | image        |                                               |
| `resident_playwright`        | post_object  | Artist post                                   |
| `related_page`               | post_object  | Page post                                     |
| `season_producers`           | post_object  | Supporter posts (multiple)                    |
| `associate_season_producers` | post_object  | Supporter posts (multiple)                    |
| `season_producer_message`    | wysiwyg      |                                               |
| `otr_sponsors_main`          | post_object  | Primary OTR sponsor (single, displays larger) |
| `otr_sponsors`               | post_object  | OTR sponsors (multiple)                       |
| `otr_sponsors_alt`           | wysiwyg      | Fallback sponsor text                         |
| `main_series`                | relationship | Productions in `series:main`                  |
| `otr_series`                 | relationship | Productions in `series:otr`                   |
| `tya_series`                 | relationship | Productions in `series:tya`                   |
| `holiday_series`             | relationship | Productions in holiday series                 |
| `visiting_companies`         | relationship | Visiting company productions                  |

---

## Event Type

_taxonomy: `event-type`_ | group: `group_event_type_details`

| meta_key       | type         | notes                                                   |
| -------------- | ------------ | ------------------------------------------------------- |
| `schedule`     | wysiwyg      | Schedule display text (in addition to term description) |
| `related_page` | relationship | Linked page                                             |

---

## Position

_post_type: `position`_ | group: `group_6a324859d65f9`

| meta_key             | type       | notes                             |
| -------------------- | ---------- | --------------------------------- |
| `is_active_position` | true_false | Hides from frontend when inactive |

---

## Options Pages

### Board Positions (`board-positions` options page)

| meta_key                               | type        | notes                 |
| -------------------------------------- | ----------- | --------------------- |
| `board_president`                      | post_object | Supporter             |
| `board_vice_president`                 | post_object | Supporter             |
| `board_treasurer`                      | post_object | Supporter             |
| `board_secretary`                      | post_object | Supporter             |
| `board_immediate_past_board_president` | post_object | Supporter             |
| `board_members`                        | post_object | Supporters (multiple) |
| `board_emeritus`                       | post_object | Supporters (multiple) |
| `board_legacy`                         | post_object | Supporters (multiple) |

### Staff / Open Jobs (`chance-staff` options page)

| meta_key         | type     | notes                                                          |
| ---------------- | -------- | -------------------------------------------------------------- |
| `open_positions` | repeater | Sub-keys: `job_title` (text), `job_description` (block editor) |

---

## User Form (WP user profile)

| meta_key            | type         | notes                                                  |
| ------------------- | ------------ | ------------------------------------------------------ |
| `related_artist`    | relationship | Links WP user to artist post (max 1, bidirectional)    |
| `related_supporter` | relationship | Links WP user to supporter post (max 1, bidirectional) |

---

## Artist Bio Submission (post_type: `post`)

_Frontend submission form for cast/crew bios_ | group: `group_6a34b6053a4e7`

| meta_key                      | type     | notes                                   |
| ----------------------------- | -------- | --------------------------------------- |
| `name`                        | text     | Credit name (required)                  |
| `phonetic_pronunciation_name` | text     | Phonetic spelling of name               |
| `pronouns`                    | text     |                                         |
| `for_production`              | text     | Show name                               |
| `role`                        | text     | Character or production role (required) |
| `bio`                         | textarea | Max 160 chars (required)                |
| `upload_headshotphoto`        | file     | High-res color photo (required)         |
| `links`                       | repeater | Sub-keys: `link_name`, `link_url`       |
