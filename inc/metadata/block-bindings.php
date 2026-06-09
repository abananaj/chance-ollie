<?php

$meta_fields = array(
  'page' => array('subtitle'),
  'event' => array('date', 'start', 'end'),
  'production' => array(
    'subtitle',
    'opening',
    'closing',
    'ticketing-link',
    'url_best',
    'url_saver',
    'url_pwyc',
    'featured_quote_text',
    'featured_quote_cite',
    'content_advisory',
    'full_disclosure_spoilers',
    'venue-room',
    'ticket-note',
    'widget-content',
    'press-release',
    'target_blank'
  ),
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