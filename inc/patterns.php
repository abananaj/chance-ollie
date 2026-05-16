<?php 

// Register all block patterns from the patterns directory
function chance_register_block_patterns()
{
  $pattern_dir = get_stylesheet_directory() . '/patterns';
  $patterns = array(
    'annual-fund' => array(
      'title'       => __('Annual Fund', 'chance-ollie'),
      'description' => __('Annual Fund Sponsorship Table', 'chance-ollie'),
      'categories'  => array('tables'),
      'synced'      => true,
    ),
    'artist-fund' => array(
      'title'       => __('Artist Fund', 'chance-ollie'),
      'description' => __('Artist Fund Sponsorship Table', 'chance-ollie'),
      'categories'  => array('tables'),
      'synced'      => true,
    ),
    'corporate-sponsorship' => array(
      'title'       => __('Corporate Sponsorship', 'chance-ollie'),
      'description' => __('Corporate Sponsorship Table', 'chance-ollie'),
      'categories'  => array('tables'),
      'synced'      => true,
    ),
    'education-fund' => array(
      'title'       => __('Education Fund', 'chance-ollie'),
      'description' => __('Education Fund Sponsorship Table', 'chance-ollie'),
      'categories'  => array('tables'),
      'synced'      => true,
    ),
    'notes-default' => array(
      'title'       => __('Notes Default', 'chance-ollie'),
      'description' => __('Default Notes Pattern', 'chance-ollie'),
      'categories'  => array('text'),
      'synced'      => true,
    ),
    'otr-fund' => array(
      'title'       => __('OTR Fund', 'chance-ollie'),
      'description' => __('OTR Fund Sponsorship Table', 'chance-ollie'),
      'categories'  => array('tables'),
      'synced'      => true,
    ),
    'producers-circle' => array(
      'title'       => __('Producers Circle', 'chance-ollie'),
      'description' => __('Producers Circle Sponsorship Table', 'chance-ollie'),
      'categories'  => array('tables'),
      'synced'      => true,
    ),
    'series-buttons' => array(
      'title'       => __('Series Buttons', 'chance-ollie'),
      'description' => __('Button Group for Series Selection', 'chance-ollie'),
      'categories'  => array('buttons'),
      'synced'      => true,
    ),
    'video-production-trailer' => array(
      'title'       => __('Video Production Trailer', 'chance-ollie'),
      'description' => __('Video Production Trailer Pattern', 'chance-ollie'),
      'categories'  => array('media'),
      'synced'      => true,
    ),
  );

  foreach ($patterns as $slug => $properties) {
    $pattern_file = $pattern_dir . '/' . $slug . '.html';

    if (file_exists($pattern_file)) {
      $content = file_get_contents($pattern_file);
      $pattern_properties = array_merge(
        $properties,
        array(
          'content' => $content,
        )
      );
      register_block_pattern('chance-ollie/' . $slug, $pattern_properties);
    }
  }
}