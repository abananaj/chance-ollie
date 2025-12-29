<?php
/**
 * Chance Theater WordPress Template
 *
 * @package    wordpress/wordpress
 * @subpackage chancetheater/website
 * @author     Brodkin CyberArts <info@brodkinca.com>
 * @copyright  2015 Brodkin CyberArts
 * @version    Git: $Id: c74607933bde95f24f908efa2a03ec82a8401589 $
 * @link       http://chancetheater.com/
 */

use BCA\ChanceTheater\Helpers;

/**
 * Customize page titles.
 *
 * @return string HTML of title to be displayed.
 */
function roots_title()
{
    if (is_home()) {
        if (get_option('page_for_posts', true)) {
            $title = get_the_title(get_option('page_for_posts', true));
        } else {
            $title = __('Latest Posts', 'roots');
        }
    } elseif (is_archive()) {
        $term = get_term_by('slug', get_query_var('term'), get_query_var('taxonomy'));
        if ($term) {
            $title = $term->name;
        } elseif (is_post_type_archive()) {
            $title = get_queried_object()->labels->name;
        } elseif (is_day()) {
            $title = sprintf(__('Daily Archives: %s', 'roots'), get_the_date());
        } elseif (is_month()) {
            $title = sprintf(__('Monthly Archives: %s', 'roots'), get_the_date('F Y'));
        } elseif (is_year()) {
            $title = sprintf(__('Yearly Archives: %s', 'roots'), get_the_date('Y'));
        } elseif (is_author()) {
            $author = get_queried_object();
            $title = sprintf(__('Author Archives: %s', 'roots'), $author->display_name);
        } else {
            $title = single_cat_title(null, false);
        }
    } elseif (is_search()) {
        $title = sprintf(__('Search Results for %s', 'roots'), get_search_query());
    } elseif (is_404()) {
        $title = __('Not Found', 'roots');
    } else {
        $title = get_the_title();
    }

    return Helpers::bootstrapHeading($title);
}
