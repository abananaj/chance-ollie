<?php

namespace Chance\Models;

class Venues
{

    /**
     * Get Venue Address.
     *
     * @param int|string $venue_id Venue Post ID.
     *
     * @return array
     */
    public static function get_address($venue_id)
    {
        $data = get_post_meta($venue_id);

        $venue['street-address'] = isset($data['street-address'][0]) ? $data['street-address'][0] : '';
        $venue['locality']       = isset($data['locality'][0]) ? $data['locality'][0] : '';
        $venue['region']         = isset($data['region'][0]) ? $data['region'][0] : '';
        $venue['postal-code']    = isset($data['postal-code'][0]) ? $data['postal-code'][0] : '';
        $venue['country-name']   = isset($data['country-name'][0]) ? $data['country-name'][0] : '';

        return $venue;
    }

    /**
     * Get Venue Amenities.
     *
     * @param int|string $venue_id Venue Post ID.
     *
     * @return array
     */
    public static function get_amenities($venue_id)
    {
        $data = get_post_meta($venue_id, 'amenities', false);

        if (! $data) {
            return array();
        }

        return $data;
    }
}
