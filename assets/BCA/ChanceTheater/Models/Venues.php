<?php
/**
 * Chance Theater WordPress Template
 *
 * @package    wordpress/wordpress
 * @subpackage chancetheater/website
 * @author     Brodkin CyberArts <info@brodkinca.com>
 * @copyright  2015 Brodkin CyberArts
 * @version    Git: $Id: 6e197c3f9ff765db63ff89d2916d4ce547ef4b45 $
 * @link       http://chancetheater.com/
 */

namespace BCA\ChanceTheater\Models;

/**
 * CT Venues Model
 */
class Venues
{
    /**
     * Get Venue Address
     *
     * @param string|integer $venue_id Venue Post ID.
     *
     * @return array
     */
    public static function getAddress($venue_id)
    {
        $data = get_post_meta($venue_id);

        $venue['street-address'] = @$data['street-address'][0];
        $venue['locality'] = @$data['locality'][0];
        $venue['region'] = @$data['region'][0];
        $venue['postal-code'] = @$data['postal-code'][0];
        $venue['country-name'] = @$data['country-name'][0];

        return $venue;
    }

    /**
     * Get Venue Address
     *
     * @param string|integer $venue_id Venue Post ID.
     *
     * @return array
     */
    public static function getAmenities($venue_id)
    {
        $data = get_post_meta($venue_id, 'amenities', false);

        if (!$data) {
            return [];
        }

        return $data;
    }
}
