<?php

/**
 * SVG Upload Support
 *
 * Allows SVG uploads for administrator users only.
 * Sanitization is enforced by restricting access to trusted roles.
 */

/**
 * Add SVG to allowed MIME types for administrators only.
 *
 * @param array $mimes Allowed MIME types.
 * @return array
 */
function ct_svg_allowed_mimes($mimes)
{
    if (current_user_can('manage_options')) {
        $mimes['svg']  = 'image/svg+xml';
        $mimes['svgz'] = 'image/svg+xml';
    }
    return $mimes;
}
add_filter('upload_mimes', 'ct_svg_allowed_mimes');

/**
 * Fix SVG file type detection for administrators only.
 *
 * @param array  $data     File data.
 * @param string $file     File path.
 * @param string $filename File name.
 * @param array  $mimes    Allowed MIME types.
 * @return array
 */
function ct_svg_check_filetype($data, $file, $filename, $mimes)
{
    if (! current_user_can('manage_options')) {
        return $data;
    }
    $ext = pathinfo($filename, PATHINFO_EXTENSION);
    if ('svg' === $ext) {
        $data['type'] = 'image/svg+xml';
        $data['ext']  = 'svg';
    }
    return $data;
}
add_filter('wp_check_filetype_and_ext', 'ct_svg_check_filetype', 10, 4);
