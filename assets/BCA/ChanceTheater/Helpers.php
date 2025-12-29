<?php
/**
 * Chance Theater WordPress Template
 *
 * @package    wordpress/wordpress
 * @subpackage chancetheater/website
 * @author     Brodkin CyberArts <info@brodkinca.com>
 * @copyright  2015 Brodkin CyberArts
 * @version    Git: $Id: eb0680e46bc328dd1d22f437d71d4f2fa5a69af8 $
 * @link       http://chancetheater.com/
 */

namespace BCA\ChanceTheater;

use \DateTimeZone;
use \WP_Query;
use BCA\ChanceTheater\Models\Venues as VenuesModel;

/**
 * System-wide helpers.
 */
class Helpers
{

    /**
     * Add bootstrap formatting to headings.
     *
     * @param string $str Heading text or HTML.
     *
     * @return string      Reformatted HTML.
     */
    public static function bootstrapHeading(string $str)
    {
        if (strpos($str, ':')) {
            return preg_replace('/:/', '<small>', $str, 1).'</small>';
        }

        return $str;
    }

    /**
     * Get HTML Markup for Artist Headshot
     *
     * @param string|integer $post_id Artist Post ID.
     * @param string         $size    WordPress Image Size.
     *
     * @return string
     */
    public static function getArtistHeadshot($post_id, string $size = 'square-100')
    {
        if (has_post_thumbnail($post_id)) {
            return get_the_post_thumbnail(
                $post_id,
                $size,
                ['class'=> 'img-responsive']
            );
        }

        $html = '<img src="/app/assets/img/icon-mystery-man.png" class="img-responsive">';

        return $html;
    }

    /**
     * Get Artist Definition List w/ Avatar Bubbles
     *
     * @param WP_Query       $credits    Query of posts to be displayed.
     * @param string         $image_size Known Wordpress image size.
     * @param boolean|string $group      Filter to a specific group name.
     *
     * @return string         HTML to render thumbnails.
     */
    public static function getArtistThumbs(WP_Query $credits, string $image_size, $group = false)
    {
        $output = null;
        while ($credits->have_posts()) {
            $credit = $credits->next_post();

            if ($credit->__get('role-group') !== $group) {
                continue;
            }

            $output.= '<div class="sc-artist-thumb">';
            $output.= '<a href="'.get_permalink($credit->artist).'">';
            $output.= self::getArtistHeadshot($credit->artist, $image_size);
            $output.= '</a>';
            $output.= '<a href="'.get_permalink($credit->artist).'" class="sc-artist-thumb-caption">';
            $output.= '<span class="sc-artist-thumb-name">';
            $output.= get_the_title($credit->artist);
            $output.= '</span>';
            if (get_post_meta($credit->artist, 'resident')) {
                $output .= '&nbsp;<sup><i class="fa fa-asterisk"></i></sup>';
            }

            $output.= '<br>';
            $output.= '<span class="sc-artist-thumb-role">'.$credit->__get('role').'</span>';
            $output.= '</a></div>';
        }

        return $output;
    }

    /**
     * Get HTML to display one line list of artists.
     *
     * @param WP_Query       $credits Query of posts to be displayed.
     * @param string         $title   Title to prefix to line.
     * @param boolean|string $group   Filter to a specific group name.
     *
     * @return string        HTML to render names.
     */
    public static function getArtistsOneline(WP_Query $credits, string $title, $group = false)
    {
        $artists = [];
        while ($credits->have_posts()) {
            $credit = $credits->next_post();

            if ($credit->__get('role-group') === $group) {
                $artist = [
                    'name' => get_the_title($credit->artist),
                    'permalink' => get_permalink($credit->artist)
                ];
                $artists[] = $artist;
            }
        }

        if (count($artists) === 0) {
            return '';
        }

        $output = $title.' ';
        if (count($artists) > 1) {
            $title = explode(' ', $title);
            $title_end = array_pop($title);
            $output = implode(' ', $title).' '.Inflect::pluralize($title_end).' ';
        }

        $output .= '<span class="name">';

        $family = false;
        $name_previous = false;
        if (count($artists) > 1) {
            $family = true;
            foreach ($artists as $artist) {
                $name = substr($artist['name'], strrpos($artist['name'], ' '));
                if ($name_previous !== false && $name !== $name_previous) {
                    $family = false;
                    continue;
                }

                $name_previous = $name;
            }
        }

        $end = false;
        foreach ($artists as $artist) {
            $output .= ' ';

            if (count($artists) > 1 && $artist === end($artists)) {
                $output.= 'and ';
                $end = true;
            }

            $output .= '<a href="'.$artist['permalink'].'" class="names">';

            if ($family === true && $end !== true) {
                $name = explode(' ', $artist['name']);
                array_pop($name);
                $output .= implode(' ', $name);
            } else {
                $output .= preg_replace('/\s/', '&nbsp;', $artist['name']);
            }

            $output .= '</a>';

            if (count($artists) > 2 && $end === false) {
                $output .= ',';
            }
        }

        $output .= '</span>';

        return $output;
    }

    /**
     * Get Dimensions of WordPress Named Image Size
     *
     * @param string $image_size WordPress Image Size.
     * @param string $format     Retrieve as 'string' or 'array'.
     *
     * @return string,array Size of image WxH
     */
    public static function getImageDimensions(string $image_size, string $format = 'string')
    {
        global $_wp_additional_image_sizes;

        $size[] = $_wp_additional_image_sizes[$image_size]['width'];
        $size[] = $_wp_additional_image_sizes[$image_size]['height'];

        if ($format === 'string' || $format === 'str') {
            return implode('x', $size);
        }

        return $size;
    }

    /**
     * Get Navigation Menu Title
     *
     * @param string $location Named menu reference.
     *
     * @return string
     */
    public static function getNavMenuTitle(string $location)
    {
        if (!has_nav_menu($location)) {
            return false;
        }

        $menus = get_nav_menu_locations();
        $menu_title = wp_get_nav_menu_object($menus[$location])->name;

        return $menu_title;
    }

    /**
     * Get Post Types as an Associative Array.
     *
     * @return array
     */
    public static function getPostTypesAssoc()
    {
        $post_types = [];
        $post_types_raw = get_post_types(['public'=> true]);
        foreach ($post_types_raw as $post_type_id) {
            $post_type = get_post_type_object($post_type_id);
            $post_types[$post_type_id] = $post_type->label;
        }

        return $post_types;
    }

    /**
     * Get HTML to render button linking to referring page.
     *
     * @return string HTML to render a button linking to referring page.
     */
    public static function getReferrerButton()
    {
        if (get_query_var('referrer')) {
            $output = '<a class="btn btn-primary btn-xs" ';
            $output.= 'href="'.get_permalink(get_query_var('referrer')).'">';
            $output.= '<i class="fa fa-arrow-left"></i> ';
            $output.= 'Back to '.get_the_title(get_query_var('referrer'));
            $output.= '</a>';

            return $output;
        }
    }

    /**
     * Timespan
     *
     * Returns a span of seconds in this format:
     *        10 days 14 hours 36 minutes 47 seconds
     *
     * @param string|integer $seconds Number of seconds.
     * @param string|integer $time    Unix timestamp.
     * @param string|integer $units   Number of display units.
     *
     * @return string
     */
    public static function getTimespan($seconds = 1, $time = '', $units = 7)
    {
        // @codingStandardsIgnoreStart
        if (!is_numeric($seconds)) {
            $seconds = 1;
        }

        if (!is_numeric($time)) {
            $time = time();
        }

        if (!is_numeric($units)) {
            $units = 7;
        }

        if ($time <= $seconds) {
            $seconds = 1;
        } else {
            $seconds = ($time - $seconds);
        }

        $str = [];
        $years = floor($seconds / 31557600);

        if ($years > 0) {
            $str[] = $years.' '.__($years > 1 ? 'years' : 'year');
        }

        $seconds -= ($years * 31557600);
        $months = floor($seconds / 2629743);

        if (count($str) < $units && ($years > 0 or $months > 0)) {
            if ($months > 0) {
                $str[] = $months.' '.__($months > 1 ? 'months' : 'month');
            }

            $seconds -= ($months * 2629743);
        }

        $weeks = floor($seconds / 604800);

        if (count($str) < $units && ($years > 0 or $months > 0 or $weeks > 0)) {
            if ($weeks > 0) {
                $str[] = $weeks.' '.__($weeks > 1 ? 'weeks' : 'week');
            }

            $seconds -= ($weeks * 604800);
        }

        $days = floor($seconds / 86400);

        if (count($str) < $units && ($months > 0 or $weeks > 0 or $days > 0)) {
            if ($days > 0) {
                $str[] = $days.' '.__($days > 1 ? 'days' : 'day');
            }

            $seconds -= ($days * 86400);
        }

        $hours = floor($seconds / 3600);

        if (count($str) < $units && ($days > 0 or $hours > 0)) {
            if ($hours > 0) {
                $str[] = $hours.' '.__($hours > 1 ? 'hours' : 'hour');
            }

            $seconds -= ($hours * 3600);
        }

        $minutes = floor($seconds / 60);

        if (count($str) < $units && ($days > 0 or $hours > 0 or $minutes > 0)) {
            if ($minutes > 0) {
                $str[] = $minutes.' '.__($minutes > 1 ? 'minutes' : 'minute');
            }

            $seconds -= ($minutes * 60);
        }

        if (count($str) === 0) {
            $str[] = $seconds.' '.__($seconds > 1 ? 'seconds' : 'second');
        }
        // @codingStandardsIgnoreEnd
        return implode(', ', $str);
    }

    /**
     * Get configured time zone.
     *
     * @return DateTimeZone Object representing current zone.
     */
    public static function getTimezone()
    {
        $tz = new DateTimeZone('GMT');

        $config_value = get_option('timezone_string');
        if ($config_value) {
            $tz = new DateTimeZone($config_value);
        }


        return $tz;
    }

    /**
     * Get Venue Amenities as an Unordered List
     *
     * @param string|integer $venue_id Venue Post ID.
     *
     * @return str
     */
    public static function getVenueAmenitiesList($venue_id)
    {
        // Retrieve Selected Amenities.
        $amenities = VenuesModel::getAmenities($venue_id);

        if (!$amenities) {
            return '';
        }

        // Generate Unordered List.
        $output = '<ul class="fa-ul">';
        foreach ($amenities as $amenity) {
            $output.= '<li>';
            $output.= '<i class="fa-li fa '.$amenity['icon'].'"></i> ';
            $output.= $amenity['desc'];
            $output.= '</li>';
        }

        $output .= '</ul>';

        return $output;
    }

    /**
     * Get VCard Microformat for a Venue
     *
     * @param string|integer $venue_id Venue Post ID.
     *
     * @return str
     */
    public static function getVenueHcard($venue_id)
    {
        $addr = VenuesModel::getAddress($venue_id);

        $output = '<div class="vcard">';
        $output.= '<div class="fn">'.get_the_title($venue_id).'</div>';
        $output.= '<address class="adr">';
        $output.= '<div class="street-address">'.$addr['street-address'].'</div>';
        $output.= '<span class="locality">'.$addr['locality'].'</span>, ';
        $output.= '<span class="region">'.$addr['region'].'</span> &nbsp;';
        $output.= '<span class="postal-code">'.$addr['postal-code'].'</span>';
        $output.= '</address>';
        $output.= '</div>';

        return $output;
    }

    /**
     * Get Static Map of Venue
     *
     * @param string|integer $venue_id   Venue Post ID.
     * @param array|str      $image_size WordPress Image Size.
     *
     * @return str URL of Static Map
     */
    public static function getVenueMapSrc($venue_id, $image_size)
    {
        $address = implode(' ', VenuesModel::getAddress($venue_id));

        $param['markers'] = 'size:medium|color:red|'.$address;
        $param['size'] = self::getImageDimensions($image_size);
        $param['maptype'] = 'terrain';
        $param['sensor'] = 'false';
        $param['key'] = GOOGLE_API_BROWSER_KEY;

        $api_url = 'https://maps.googleapis.com/maps/api/staticmap?';

        return $api_url.http_build_query($param);
    }
}
