<?php

/**
 * TAXONOMIES
 *
 * 1. Event Types
 * 2. Programs
 * 3. Seasons
 * 4. Series
 * 5. Sessions
 * 6. Supporter Levels
 */

$taxonomy_dir = get_stylesheet_directory() . '/inc/taxonomy/';

require_once $taxonomy_dir . 'event-type.php';
require_once $taxonomy_dir . 'program.php';
require_once $taxonomy_dir . 'season.php';
require_once $taxonomy_dir . 'series.php';
require_once $taxonomy_dir . 'session.php';
require_once $taxonomy_dir . 'supporter-level.php';

function ct_register_taxonomies()
{
  ct_register_event_type();
  ct_register_program();
  ct_register_season();
  ct_register_series();
  ct_register_session();
  ct_register_supporter_level();
}
