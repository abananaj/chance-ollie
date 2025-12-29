<?php
/**
 * Chance Theater WordPress Template
 *
 * @package    wordpress/wordpress
 * @subpackage chancetheater/website
 * @author     Brodkin CyberArts <info@brodkinca.com>
 * @copyright  2015 Brodkin CyberArts
 * @version    Git: $Id: a87b9a4ac5316e33670ffb6d841ab49a1c0c2397 $
 * @link       http://chancetheater.com/
 */

namespace BCA\ChanceTheater;

use \DateTime;
use \WP_Query;
use BCA\ChanceTheater\Models\Artists as ArtistsModel;
use BCA\ChanceTheater\Models\Productions as ProductionsModel;

/**
 * Shortcodes
 */
class Shortcodes
{
    /**
     * Constructor.
     */
    public function __construct()
    {
        add_shortcode('donor_list', [$this, 'donorList']);
        add_shortcode('board_member_list', [$this, 'boardMemberList']);
        add_shortcode('page_menu', [$this, 'pageMenu']);
        add_shortcode('resident_artist_list', [$this, 'residentArtistList']);
        add_shortcode('season', [$this, 'season']);
        add_shortcode('season_row', [$this, 'seasonRow']);
        add_shortcode('staff_list', [$this, 'staffList']);
        add_shortcode('subscribe_cta', [$this, 'subscribeCta']);
        add_shortcode('oembed', [$this, 'oEmbed']);
        add_shortcode('cards', [$this, 'cardContainer']);
        add_shortcode('card', [$this, 'card']);
    }

    /**
     * Board Member Definition List
     *
     * @param string|array $attributes WP Attributes.
     *
     * @return string
     */
    public function boardMemberList($attributes)
    {
        // Retrieve Attributes.
        extract(shortcode_atts(['display' => 'horizontal'], $attributes));

        // Retrieve Board.
        $query = new WP_Query(
            [
                'post_type' => 'ct-supporter',
                'meta_key' => 'board-position',
                'meta_compare' => 'EXISTS',
                'orderby' => 'title',
                'posts_per_page'=> 50
            ]
        );

        $board_members = [];
        while ($query->have_posts()) {
            $query->the_post();
            $post = get_post();
            $board_position = get_term(
                $post->__get('board-position'),
                'ct-board-positions'
            );
            $board_members[$board_position->slug.$post->post_title] = $post;
        }

        ksort($board_members);

        $output = '<dl class="sc-person">';

        foreach ($board_members as $board_position_tax_id => $board_member) {
            $board_position = get_term(
                $board_member->__get('board-position'),
                'ct-board-positions'
            );
            $output.= '<div class="sc-person-member col-sm-6 col-xl-4 row">';
            $output.= get_the_post_thumbnail(
                $board_member->ID,
                'square-100',
                ['class' => 'pull-left sc-person-thumb']
            );
            $output.= '<div class="sc-person-heading">';
            $output.= '<dt class="sc-person-heading-name">';
            $output.= '<a href="'.get_permalink($board_member->ID).'">';
            $output.= $board_member->post_title;
            $output.= '</a>';
            $output.= '</dt>';
            $output.= '<small>';
            $output.= '<dd class="sc-person-heading-position">';
            $output.= $board_position->name.' ';
            $output.= '</dd>';
            $output.= '<dd class="sc-person-excerpt">';
            $output.= $board_member->post_excerpt;
            $output.= '</dd>';
            $output.= '</small>';
            $output.= '</div>';
            $output.= '</div>';
            $output.= '<div class="visible-xs clearfix"></div>';
        }

        $output .= '</dl><div class="clearfix"></div>';

        return $output;
    }

    /**
     * Donor Definition List
     *
     * @param array|string $attributes WP Attributes.
     *
     * @return string
     */
    public function donorList($attributes)
    {
        // Retrieve Attributes.
        extract(shortcode_atts(['type' => false], $attributes));

        // Build Query.
        $meta_query = [];
        $meta_query[] = ['key'=> 'donor-level', 'compare'=> 'EXISTS'];
        if ($type) {
            $meta_query[] = ['key'=> 'supporter-type', 'value'=> $type];
        }

        // Retrieve Donors.
        $query = new WP_Query(
            [
                'post_type' => 'ct-supporter',
                'meta_query' => $meta_query
            ]
        );

        $data = [];
        while ($query->have_posts()) {
            $query->the_post();
            $data[get_post_meta(get_the_ID(), 'donor-level', true)][] = get_post();
        }

        $output = '<dl class="sc-donors">';

        foreach ($data as $donor_tax_id => $donors) {
            $donor_level = get_term($donor_tax_id, 'ct-supporter-level');
            $output.= '<dt class="sc-donors-level">';
            $output.= $donor_level->name.' ';
            $output.= '<span class="sc-donors-level-desc">';
            $output.= $donor_level->description;
            $output.= '</span>';
            $output.= '</dt>';
            foreach ($donors as $donor) {
                $output.= '<dd class="sc-donors-donor">';
                $output.= '<a href="'.get_permalink($donor->ID).'">';
                $output.= $donor->post_title;
                $output.= '</a>';
                $output.= '</dd>';
            }
        }

        $output .= '</dl>';

        return $output;
    }

    /**
     * Get HTML to render a menu for the given page.
     *
     * @return string
     */
    public function pageMenu()
    {
        global $post;

        $parent = get_the_ID();
        if ($post->post_parent) {
            $parent = $post->post_parent;
        }

        $nav = wp_page_menu(
            [
                'child_of' => $parent,
                'echo' => false
            ]
        );
        $nav = preg_replace('/<ul>/', '<ul class="nav nav-pills nav-stacked">', $nav, 1);

        return $nav;
    }

    /**
     * Display a detailed view of a season.
     *
     * @param array $attributes Array of attributes.
     *
     * @return string
     */
    public function season(array $attributes)
    {
        extract(shortcode_atts(['slug' => false], $attributes));

        if (!$slug) {
            return '';
        }

        $productions = ProductionsModel::getProductionsSeason($slug);

        $output = '<div class="sc-season">';

        while ($productions->have_posts()) {
            $production = $productions->next_post();

            $date_opening = new DateTime('@'.$production->__get('date-opening'));
            $date_opening->setTimezone(Helpers::getTimezone());
            $date_closing = new DateTime('@'.$production->__get('date-closing'));
            $date_closing->setTimezone(Helpers::getTimezone());

            $output.= '<div class="sc-season-production"><div class="sc-season-production-inner">';
            $output.= '<a href="'.get_permalink($production->ID).'" class="sc-season-production-img">';

            $thumb = get_the_post_thumbnail($production->ID, 'postcard-sm');
            if (!$thumb) {
                $thumb = '<img src="/app/assets/img/postcard-default.png">';
            }

            $output .= $thumb;

            $output.= '</a>';
            $output.= '<div class="sc-season-production-info">';
            $output.= '<header class="sc-season-production-header">';
            $output.= '<a href="'.get_permalink($production->ID).'" class="sc-season-production-title">';
            $output.= Helpers::bootstrapHeading($production->post_title);
            $output.= '</a>';

            $output .= '</header>';
            // Header.
            $artists = ArtistsModel::getProduction($production->ID);
            if ($artists->have_posts()) {
                $output.= '<div class="sc-season-production-credits">';
                $groups = [
                  'writer' => 'By',
                  'composer' => 'Music by',
                  'writer-lyrics' => 'Lyrics by',
                  'writer-book' => 'Book by'
                ];
                foreach ($groups as $group => $byline) {
                    $credits = Helpers::getArtistsOneline($artists, $byline, $group);
                    if (!empty($credits)) {
                        $output .= '<div class="credit-'.$group.'">'.$credits.'</div>';
                    }
                }

                $output .= '</div>';
                // Class sc-season-production-credits end.
            }

            if ($production->__get('widget-content')) {
                $output.= '<div class="sc-season-production-extra">';
                $output.= $production->__get('widget-content');
                $output.= '</div>';
            }

            $output.= '<div class="sc-season-production-dates">';
            $output.= '<time datetime="'.$date_opening->format('Y-m-d')
                   .'" class="sc-season-production-date-opening">';
            $output.= $date_opening->format('F j');
            $output.= '</time>';
            if ($date_opening->format('z') !== $date_closing->format('z')) {
                $output.= '&nbsp;&mdash; ';
                $output.= '<time datetime="'.$date_closing->format('Y-m-d')
                       .'"  class="sc-season-production-date-closing">';
                $output.= $date_closing->format('F j');
                $output.= '</time>';
            }

            $output .= '</div>';
            // Class sc-season-production-dates end.
            $output.= '<div class="sc-season-production-buttons">';
            $output.= '<a href="'.get_permalink($production->ID)
                   .'" class="btn btn-primary btn-xs">Learn More</a>';
            $output.= '</div>';
            // Buttons end.
            $output .= '</div>';
            // Info end.
            $output.= '<div class="clearfix"></div>';
            $output.= '</div></div>';
            // Production end.
        }

        $output .= '</div>';
        // Season end.
        return $output;
    }

    /**
     * Get HTML to render a row of thumbnails for a given season.
     *
     * @param array $attributes Array of attributes.
     *
     * @return string
     */
    public function seasonRow(array $attributes)
    {
        extract(shortcode_atts(['slug' => false], $attributes));

        if (!$slug) {
            return '';
        }

        $productions = ProductionsModel::getProductionsSeason($slug);

        $output = '<div class="sc-seasonrow">';

        while ($productions->have_posts()) {
            $production = $productions->next_post();

            $date_opening = new DateTime('@'.$production->__get('date-opening'));
            $date_opening->setTimezone(Helpers::getTimezone());
            $date_closing = new DateTime('@'.$production->__get('date-closing'));
            $date_closing->setTimezone(Helpers::getTimezone());

            $production_class = 'sc-seasonrow-production';

            if ($production->__get('date-closing') < time()) {
                $production_class .= ' sc-seasonrow-production-past';
            }

            $output.= '<div class="'.$production_class.'"><div class="sc-seasonrow-production-inner">';
            $output.= '<a href="'.get_permalink($production->ID).'" class="sc-seasonrow-production-img">';

            $thumb = get_the_post_thumbnail($production->ID, 'postcard-sm');
            if (!$thumb) {
                $thumb = '<img src="/app/assets/img/postcard-default.png">';
            }

            $output .= $thumb;

            $output.= '</a>';
            $output.= '<div class="sc-seasonrow-production-info">';
            $output.= '<header class="sc-seasonrow-production-header">';
            $output.= '<a href="'.get_permalink($production->ID).'" class="sc-seasonrow-production-title">';
            $output.= htmlspecialchars($production->post_title);
            $output.= '</a>';

            $output .= '</header>';
            // Header end.
            $output.= '<div class="sc-seasonrow-production-dates"><nobr>';
            $output.= '<time datetime="'.$date_opening->format('Y-m-d')
                   .'" class="sc-seasonrow-production-date-opening">';
            $output.= $date_opening->format('F j');
            $output.= '</time>';
            if ($production->__get('date-opening') !== $production->__get('date-closing')) {
                $output.= '&nbsp;&mdash;<wbr>';
                $output.= '<time datetime="'.$date_closing->format('Y-m-d')
                       .'"  class="sc-seasonrow-production-date-closing">';
                $output.= $date_closing->format('F j');
                $output.= '</time>';
            }

            $output .= '</nobr></div>';
            // Class production-dates end.
            $output .= '</div>';
            // Class info end.
            $output .= '</div></div>';
            // Class production end.
        }

        $output .= '</div>';
        // Class season end.
        return $output;
    }

    /**
     * Resident Artist Definition List
     *
     * @return string
     */
    public function residentArtistList()
    {
        // Retrieve Board.
        $query = new WP_Query(
            [
                'post_type' => 'ct-artist',
                'meta_key' => 'resident',
                'meta_compare' => 'EXISTS',
                'orderby' => 'menu_order',
                'posts_per_page'=> 50
            ]
        );

        $output = '<dl class="sc-person">';

        while ($query->have_posts()) {
            $query->the_post();
            $artist = $query->post;

            $output.= '<div class="sc-person-member col-sm-6 col-xl-4 row">';
            $output.= get_the_post_thumbnail(
                $artist->ID,
                'square-100',
                ['class' => 'pull-left sc-person-thumb']
            );
            $output.= '<div class="sc-person-heading">';
            $output.= '<dt class="sc-person-heading-name">';
            $output.= '<a href="'.get_permalink($artist->ID).'">';
            $output.= $artist->post_title;
            $output.= '</a>';
            $output.= '</dt>';
            $output.= '<small>';
            $output.= '<dd class="sc-person-heading-position">';
            $output.= $artist->profession.' ';
            $output.= '</dd>';
            $output.= '<dd class="sc-person-excerpt">';
            $output.= $artist->post_excerpt;
            $output.= '</dd>';
            $output.= '</small>';
            $output.= '</div>';
            $output.= '</div>';
            $output.= '<div class="visible-xs clearfix"></div>';
        }

        $output .= '</dl><div class="clearfix"></div>';

        return $output;
    }

    /**
     * Staff Definition List
     *
     * @return string
     */
    public function staffList()
    {
        // Retrieve Board.
        $query = new WP_Query(
            [
                'post_type' => 'ct-staff',
                'orderby' => 'menu_order',
                'posts_per_page'=> 50
            ]
        );

        $output = '<dl class="sc-person">';

        while ($query->have_posts()) {
            $query->the_post();
            $artist = $query->post;

            $output.= '<div class="sc-person-member col-sm-6 col-xl-4 row">';
            $output.= get_the_post_thumbnail(
                $artist->ID,
                'square-100',
                ['class' => 'pull-left sc-person-thumb']
            );
            $output.= '<div class="sc-person-heading">';
            $output.= '<dt class="sc-person-heading-name">';
            $output.= '<a href="'.get_permalink($artist->ID).'">';
            $output.= $artist->post_title;
            $output.= '</a>';
            $output.= '</dt>';
            $output.= '<small>';
            $output.= '<dd class="sc-person-heading-position">';
            $output.= $artist->title.' ';
            $output.= '</dd>';
            $output.= '<dd class="sc-person-excerpt">';
            $output.= $artist->post_excerpt;
            $output.= '</dd>';
            $output.= '</small>';
            $output.= '</div>';
            $output.= '</div>';
            $output.= '<div class="visible-xs clearfix"></div>';
        }

        $output .= '</dl><div class="clearfix"></div>';

        return $output;
    }

    /**
     * Display subscription call-to-action.
     *
     * @return string
     */
    public function subscribeCta()
    {
        ob_start();
        get_template_part('templates/subscribe-cta');

        return ob_get_clean();
    }

    /**
     * Trigger WP's built-in oembed shortcode from anywhere shortcodes are allowed.
     *
     * @param array  $attributes Array of attributes, not allowed but required by API.
     * @param string $content    URL to embedded content.
     *
     * @return string
     */
    public function oEmbed(array $attributes, string $content)
    {
        return apply_filters(
            'embed_oembed_html',
            wp_oembed_get($content),
            []
        );
    }

    /**
     * CARDS
     *
     * @param string $attributes Array of attributes, not allowed but required by API.
     * @param string $content    URL to embedded content.
     *
     * @return string
     */
    public function cardContainer(string $attributes, string $content)
    {
        $output = '<div class="cards">';
        $output.= $content;
        $output.= '</div>';

        return do_shortcode($output);
    }

    /**
     * CARDS
     *
     * @param array  $attributes Array of attributes.
     * @param string $content    URL to embedded content.
     *
     * @return string
     */
    public function card(array $attributes, string $content)
    {
        $heading = @$attributes['heading'];
        $imgSrc = @$attributes['img'];
        $aHref = @$attributes['href'];

        $output = '<a href="'.$aHref.'" class="card">';
        $output.= '  <div href="#" class="card-header">';
        $output.= '    <img src="'.$imgSrc.'">';
        $output.= '    <h3>'.$heading.'</h3>';
        $output.= '  </div>';

        if (!empty($content)) {
            $output .= wpautop($content);
        }
        $output .= '</a>';


        return $output;
    }
}
