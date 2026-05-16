<?php

/**
 * 
 * MODELS
 * 
 * 1. Artists
 * 2. Events
 * 3. Productions
 * 4. Supporters
 * 5. Venues
 * 
 * - Calendar (for events and productions)
 * - Cleanup (for deleting old events and productions)
 * - Inflect (for pluralizing words based on quantity)
 * - Shortcodes (for rendering shortcodes in templates)
 * 
 */

// ========

$models_dir = get_stylesheet_directory() . '/inc/models/';

require_once $models_dir . 'Artists.php';
require_once $models_dir . 'Events.php';
require_once $models_dir . 'Productions.php';
require_once $models_dir . 'Supporters.php';
require_once $models_dir . 'Venues.php';
