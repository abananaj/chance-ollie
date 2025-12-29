<?php
/**
 * Chance Theater WordPress Template
 *
 * @package    wordpress/wordpress
 * @subpackage chancetheater/website
 * @author     Brodkin CyberArts <info@brodkinca.com>
 * @copyright  2015 Brodkin CyberArts
 * @version    Git: $Id: 39cc190542285cec18c3dcdd74132430ae2e8546 $
 * @link       http://chancetheater.com/
 */

namespace BCA\ChanceTheater\Models;

/**
 * CT Artists Model
 */
class Artists
{
    /**
     * Get Artists by Production
     *
     * @param string|integer $prod_id  Production Post ID.
     * @param string         $group_id Production Group (e.g. director).
     *
     * @return array
     */
    public static function getProduction($prod_id, string $group_id = '')
    {
        $artists = [];

        $meta_query = [];
        $meta_query[] = ['key'=> 'production', 'value'=> $prod_id];

        if ($group_id) {
            $meta_query[] = ['key'=> 'role-group', 'value'=> $group_id];
        }

        $artists = new \WP_Query([
            'post_type'=> 'ct-production-role',
            'posts_per_page' => 75,
            'order' => 'ASC',
            'orderby' => 'menu_order title',
            'meta_query'=> $meta_query
        ]);

        return $artists;
    }
}
