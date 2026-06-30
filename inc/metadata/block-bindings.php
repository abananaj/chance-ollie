<?php

// add_action('acf/init', function () {
//     if (!function_exists('acf_get_field_groups')) return;

//     $register_fields = function (array $fields, array $post_types) use (&$register_fields) {
//         foreach ($fields as $field) {
//             if (empty($field['name'])) continue;

//             foreach ($post_types ?: [''] as $post_type) {
//                 register_post_meta($post_type, $field['name'], [
//                     'show_in_rest' => true,
//                     'single'       => true,
//                     'type'         => 'string',
//                 ]);
//             }

//             if (!empty($field['sub_fields'])) {
//                 $register_fields($field['sub_fields'], $post_types);
//             }

//             foreach ($field['layouts'] ?? [] as $layout) {
//                 if (!empty($layout['sub_fields'])) {
//                     $register_fields($layout['sub_fields'], $post_types);
//                 }
//             }
//         }
//     };

//     foreach (acf_get_field_groups() as $group) {
//         $post_types = [];
//         foreach ($group['location'] ?? [] as $rule_group) {
//             foreach ($rule_group as $rule) {
//                 if ($rule['param'] === 'post_type' && $rule['operator'] === '==') {
//                     $post_types[] = $rule['value'];
//                 }
//             }
//         }

//         $register_fields(acf_get_fields($group['key']) ?? [], $post_types);
//     }
// });


$meta_fields = array(
  'all' => array(
    // group_title_alt
    'subtitle',
    'short_title'
  ),
  'post' => array(),
  'page' => array(),
  'artist' => array(
    'profession',
    'title',
    'resident_artist_title',
    'artist_links_name', // repeater subfield A
    'artist_links_url', // repeater subfield B
    'teacher_bio',
  ),
  'class' => array(
    'program',
    'session',
    'teaching_artist',
    'taught_by', // ❌
    'date_start',
    'date_end',
    'time_start',
    'time_end',
    'class_day'
  ),
  'event' => array(
    'date',
    'start',
    'end',
    'ticketing-link',
    'is-promo',
    'venue',
    'venue-room',
    'production'
  ),
  'production' => array(
    // BASIC ℹ️
    'opening',
    'closing',
    'runtime',
    'intermissions',
    'venue',
    'venue_room', // formerly 'venue-room'
    'ticket-note', // ❓
    'ticketing-link', // ❌ formerly to choose your ticket page link, now embedded popup
    // FEATURED
    'bylines_lead', // 🔁 subfield A
    'bylines_text', // 🔁 subfield B
    'bylines_artist', // 🔁 subfield C
    'featured_quote_text', // group, turn into repeater ???
    'featured_quote_cite', // group, turn into repeater ???
    'accolades_lead', // 🔁 subfield A
    'accolades_text', // 🔁 subfield B
    'widget_content', // formerly 'widget-content'
    // ARTWORK
    'production_preview',
    'production_poster',
    'production_postcard',
    'production_banner',
    // TICKETS
    'tickets_best', // formerly 'url_best',
    'tickets_saver', // formerly 'url_saver',
    'tickets_pwyc', // formerly 'url_pwyc',
    // PERFORMANCES
    'performances_date', // repeater subfield A
    'performances_time', // repeater subfield B
    'performances_note', // repeater subfield C
    'performances_hide', // repeater subfield D
    // NOTES
    'notes_icon', // repeater subfield A
    'notes_note', // repeater subfield B
    // CONTENT ADVISORY
    'content_advisory',
    'add_spoilers_popup', // t/f
    'full_disclosure_spoilers', // default hidden
    // SPECIAL EVENTS
    'events', // array of event IDs
    // BUZZ
    'quotes_quote-text', // 🔁 subfield A
    'quotes_quote-cite', // 🔁 subfield B
    'awards_title', // 🔁 subfield A
    'awards_text', // 🔁 subfield B
    'press_release', // formerly press-release?
    'posts', // array of post IDs
    // PHOTOS
    'production_photos',
    'rehearsal_photos',
    'playbill',
  ),
  'supporter' => array(
    'title',
    'supporter_type',
    'website',
    'display-donors',
    'display-sidebar'
  ),
  'venue' => array(
    'ammenities_icon', // 🔁 subfield A
    'ammenities_desc', // 🔁 subfield B
    'address',
    'google_maps',
    'street-address',
    'transit_link',
    'locality',
    'region',
    'postal-code',
    'country-name',
  )
);

// Register meta fields
foreach ($meta_fields as $post_type => $fields) {
  foreach ($fields as $field) {
    register_meta('post', $field, array(
      'object_subtype' => $post_type,
      'show_in_rest'   => true,
      'single'         => true,
      'type'           => 'string',
    ));
  }
}

// // Register quote fields
// foreach (range(1, 5) as $quote_index) {
//   foreach (array('quote-text', 'quote-cite') as $quote_field) {
//     register_meta('post', 'quotes_' . $quote_index . '_' . $quote_field, array(
//       'object_subtype' => 'production',
//       'show_in_rest'   => true,
//       'single'         => true,
//       'type'           => 'string',
//     ));
//   }
// }
