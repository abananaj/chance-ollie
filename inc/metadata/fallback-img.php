<?php

/**
 * Fallback Image Utility for Dynamic Media Blocks
 *
 * Provides helper functions and patterns for implementing fallback images
 * in dynamic blocks when primary images are unavailable.
 *
 * @package Chance_Theater
 * @subpackage Metadata
 */

if (! defined('ABSPATH')) {
  exit;
}

/**
 * Get a fallback image URL with options for different scenarios
 *
 * @param string $type Type of fallback: 'stage', 'production', 'artist', 'generic'
 * @return string The URL of the fallback image
 */
function chance_get_fallback_image($type = 'generic')
{
  $fallback_images = array(
    'stage'      => 'https://chancetheater.dev/wp-content/uploads/2025/12/stage-lights-placeholder-4-scaled.jpeg',
    'production' => 'https://chancetheater.dev/wp-content/uploads/2025/12/stage-lights-placeholder-4-scaled.jpeg',
    'artist'     => 'https://chancetheater.dev/wp-content/uploads/placeholder-artist.jpeg',
    'generic'    => 'https://chancetheater.dev/wp-content/uploads/2025/12/stage-lights-placeholder-4-scaled.jpeg',
  );

  /**
   * Allow customization of fallback images
   *
   * @param array $fallback_images Associative array of fallback image URLs
   * @param string $type Type of fallback requested
   */
  $fallback_images = apply_filters('chance_fallback_images', $fallback_images, $type);

  return isset($fallback_images[$type]) ? $fallback_images[$type] : $fallback_images['generic'];
}

/**
 * Resolve an image value to a URL, with optional fallback
 *
 * Handles multiple input formats:
 * - ACF array format: { url, alt, caption, id, sizes, ... }
 * - Attachment ID (numeric)
 * - Direct URL string
 * - null/empty/false returns fallback
 *
 * @param mixed   $value The image value (can be array, ID, URL, or null)
 * @param string  $size  Image size to use ('full', 'medium', 'thumbnail', etc.)
 * @param string  $fallback_type Type of fallback to use if image is missing
 * @param bool    $return_array Return array with url/alt/caption or just URL
 * @return string|array|null The image URL/data or null if cannot resolve
 */
function chance_resolve_image_with_fallback($value, $size = 'full', $fallback_type = 'generic', $return_array = false)
{
  $img_url     = '';
  $img_alt     = '';
  $img_caption = '';
  $attach_id   = 0;

  // Handle ACF array format
  if (is_array($value)) {
    $img_url     = isset($value['url']) ? esc_url($value['url']) : '';
    $img_alt     = isset($value['alt']) ? esc_attr($value['alt']) : '';
    $img_caption = isset($value['caption']) ? wp_kses_post($value['caption']) : '';
    $attach_id   = isset($value['ID']) ? intval($value['ID']) : 0;

    // If a specific size was requested and exists in sizes array
    if ('full' !== $size && isset($value['sizes'][$size])) {
      $img_url = esc_url($value['sizes'][$size]);
    }
  } elseif (is_numeric($value)) {
    // Handle attachment ID
    $attach_id = intval($value);
    $src = wp_get_attachment_image_src($attach_id, $size);
    if ($src) {
      $img_url     = esc_url($src[0]);
      $img_alt     = esc_attr(get_post_meta($attach_id, '_wp_attachment_image_alt', true));
      $img_caption = wp_kses_post(wp_get_attachment_caption($attach_id));
    }
  } elseif (is_string($value)) {
    // Handle direct URL string
    $img_url = esc_url($value);
    $img_alt = ''; // URL doesn't provide alt text
  }

  // If no image found, use fallback
  if (empty($img_url)) {
    $img_url = chance_get_fallback_image($fallback_type);
    $img_alt = 'Fallback image'; // Default alt text for fallback
  }

  if ($return_array) {
    return array(
      'url'     => $img_url,
      'alt'     => $img_alt,
      'caption' => $img_caption,
      'id'      => $attach_id,
    );
  }

  return $img_url;
}

/**
 * Get a post's featured image with fallback
 *
 * @param int    $post_id The post ID
 * @param string $size Image size to return ('full', 'medium', 'thumbnail', etc.)
 * @param string $fallback_type Type of fallback image
 * @param bool   $return_array Return array with url/alt or just URL
 * @return string|array The featured image URL or fallback
 */
function chance_get_featured_image_with_fallback($post_id, $size = 'full', $fallback_type = 'generic', $return_array = false)
{
  $img_url = '';
  $img_alt = '';

  if (has_post_thumbnail($post_id)) {
    $thumbnail_id = get_post_thumbnail_id($post_id);
    $src = wp_get_attachment_image_src($thumbnail_id, $size);
    if ($src) {
      $img_url = esc_url($src[0]);
      $img_alt = esc_attr(get_post_meta($thumbnail_id, '_wp_attachment_image_alt', true));
    }
  }

  // Use fallback if no featured image
  if (empty($img_url)) {
    $img_url = chance_get_fallback_image($fallback_type);
    $img_alt = 'Fallback image';
  }

  if ($return_array) {
    return array(
      'url' => $img_url,
      'alt' => $img_alt,
    );
  }

  return $img_url;
}

/**
 * Get an ACF field value with fallback
 *
 * Similar to chance_resolve_image_with_fallback but specifically for ACF fields
 *
 * @param string $field_name ACF field name/key
 * @param int    $post_id Post ID (defaults to current post)
 * @param string $size Image size
 * @param string $fallback_type Type of fallback
 * @param bool   $return_array Return array or just URL
 * @return string|array Image URL/data with fallback
 */
function chance_get_acf_image_with_fallback($field_name, $post_id = 0, $size = 'full', $fallback_type = 'generic', $return_array = false)
{
  if (! $post_id) {
    $post_id = get_the_ID();
  }

  if (! function_exists('get_field')) {
    // ACF not available, use fallback directly
    $fallback_url = chance_get_fallback_image($fallback_type);
    return $return_array ? array('url' => $fallback_url, 'alt' => 'Fallback image') : $fallback_url;
  }

  $value = get_field($field_name, $post_id);
  return chance_resolve_image_with_fallback($value, $size, $fallback_type, $return_array);
}

/* =====================================================================
   IMPLEMENTATION EXAMPLES FOR BLOCKS
   ===================================================================== */

/**
 * Example 1: Using with meta-image block render.php
 *
 * In: theatrum-blocks/src/blocks/meta-image/render.php
 *
 * $key_input  = isset($attributes['keyInput']) ? sanitize_text_field($attributes['keyInput']) : '';
 * $image_size = isset($attributes['imageSize']) ? sanitize_key($attributes['imageSize']) : 'full';
 * $post_id    = $block->context['postId'] ?? get_the_ID();
 * $fallback_type = isset($attributes['fallbackType']) ? sanitize_text_field($attributes['fallbackType']) : 'generic';
 *
 * if (!$key_input || !$post_id) {
 *   return;
 * }
 *
 * $value = get_field($key_input, $post_id);
 * if (!$value) {
 *   $value = get_post_meta($post_id, $key_input, true);
 * }
 *
 * // Instead of: if (empty($value)) { return; }
 * // Use this:
 * $image_data = chance_resolve_image_with_fallback(
 *   $value,
 *   $image_size,
 *   $fallback_type,
 *   true  // return array with url, alt, caption
 * );
 *
 * $img_url     = $image_data['url'];
 * $img_alt     = $image_data['alt'];
 * $img_caption = $image_data['caption'];
 *
 * // Then render the image normally...
 */

/**
 * Example 2: Using with cover-card block
 *
 * In: theatrum-blocks/src/blocks/cover-card/render.php
 *
 * $featured_image_url = chance_get_featured_image_with_fallback(
 *   $post->ID,
 *   'full',
 *   'production'  // Use production-specific fallback
 * );
 *
 * $bg_style = 'background-image: url(' . esc_url($featured_image_url) . ');';
 */

/**
 * Example 3: Using with ACF image field
 *
 * // Get an ACF image field with fallback
 * $image_data = chance_get_acf_image_with_fallback(
 *   'artist_headshot',  // ACF field name
 *   $post_id,
 *   'medium',
 *   'artist',           // Use artist-specific fallback
 *   true                // Return array
 * );
 *
 * echo '<img src="' . esc_url($image_data['url']) . '" alt="' . esc_attr($image_data['alt']) . '">';
 */

/**
 * Example 4: Direct usage in block attributes
 *
 * To add fallback type selection to block editor:
 *
 * In block.json:
 * "attributes": {
 *   "fallbackType": {
 *     "type": "string",
 *     "default": "generic",
 *     "enum": ["stage", "production", "artist", "generic"]
 *   }
 * }
 *
 * In edit.js (editor UI):
 * <SelectControl
 *   label="Fallback Image Type"
 *   value={attributes.fallbackType}
 *   options={[
 *     { label: 'Generic', value: 'generic' },
 *     { label: 'Stage/Lights', value: 'stage' },
 *     { label: 'Production', value: 'production' },
 *     { label: 'Artist', value: 'artist' },
 *   ]}
 *   onChange={(value) => setAttributes({ fallbackType: value })}
 * />
 */

/**
 * Example 5: Using in a carousel block
 *
 * foreach ($items as $item) {
 *   // For each item, try to get image or use fallback
 *   $image_url = chance_resolve_image_with_fallback(
 *     $item['image'],  // Could be array, ID, or URL
 *     'medium',
 *     'production'
 *   );
 *
 *   echo '<div class="carousel-item" style="background-image: url(' . esc_url($image_url) . ')">';
 *   echo '</div>';
 * }
 */

/**
 * Example 6: Using in a query loop with featured images
 *
 * while (have_posts()) {
 *   the_post();
 *
 *   $image_url = chance_get_featured_image_with_fallback(
 *     get_the_ID(),
 *     'medium_large',
 *     'production'
 *   );
 *
 *   echo '<img src="' . esc_url($image_url) . '" alt="' . esc_attr(get_the_title()) . '">';
 * }
 */
