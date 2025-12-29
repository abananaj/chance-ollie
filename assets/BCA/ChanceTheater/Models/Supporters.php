<?php
/**
 * Chance Theater WordPress Template
 *
 * @package    wordpress/wordpress
 * @subpackage chancetheater/website
 * @author     Brodkin CyberArts <info@brodkinca.com>
 * @copyright  2015 Brodkin CyberArts
 * @version    Git: $Id: 5797d0e22616f46ecacb4ea036387942dae61798 $
 * @link       http://chancetheater.com/
 */

namespace BCA\ChanceTheater\Models;

/**
 * CT Supporters Model
 */
class Supporters
{
    /**
     * Get Artists by Production
     *
     * @param string|integer $qty Number of posts to retrieve.
     *
     * @return WP_Query
     */
    public static function getRandomSupporters($qty = 1)
    {
        $queries[] = ['key'=> 'display-sidebar', 'value'=> true];

        $posts_query = new \WP_Query([
            'orderby' => 'rand',
            'posts_per_page' => $qty,
            'post_type'=> 'ct-supporter',
            'meta_query'=> $queries
        ]);

        return $posts_query;
    }
}
