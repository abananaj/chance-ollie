<?php
/**
 * Chance Theater WordPress Template
 *
 * @package    wordpress/wordpress
 * @subpackage chancetheater/website
 * @author     Brodkin CyberArts <info@brodkinca.com>
 * @copyright  2015 Brodkin CyberArts
 * @version    Git: $Id: 24eafd8dbb308fd10f6cfa92febed6eeeff22661 $
 * @link       http://chancetheater.com/
 */

namespace BCA\ChanceTheater\Widgets;

use \DateTime;
use BCA\ChanceTheater\Helpers;
use BCA\ChanceTheater\Models\Artists as ArtistsModel;
use BCA\ChanceTheater\Models\Events as EventsModel;
use BCA\ChanceTheater\Models\Productions as ProductionsModel;

/**
 * Widegt for listing productions.
 */
class Productions extends \WP_Widget
{
    /**
     * Register widget with WordPress.
     */
    public function __construct()
    {
        parent::__construct(
            'ct_productions',
            // Base ID.
            'CT:Productions',
            // Name.
            ['description' => __('Display upcoming productions.', 'roots')]
        );
    }

    /**
     * Front-end display of widget.
     *
     * @param array $args     Widget arguments.
     * @param array $instance Saved values from database.
     *
     * @return string
     */
    public function widget($args, $instance)
    {
        // @codingStandardsIgnoreStart
        $title = apply_filters('widget_title', $instance['title']);
        $qty = (isset($instance['qty'])) ? $instance['qty'] : 1;
        $season = (isset($instance['season'])) ? $instance['season'] : null;
        $btn_subscribe = (isset($instance['btn_subscribe'])) ? (bool) $instance['btn_subscribe'] : true;
        $btn_tickets = (isset($instance['btn_tickets'])) ? (bool) $instance['btn_tickets'] : true;
        $btn_more = (isset($instance['btn_more'])) ? (bool) $instance['btn_more'] : true;
        // @codingStandardsIgnoreEnd

        $data = ProductionsModel::getProductionsFuture($qty, 0, $season);

        // Do not display anything if there are no productions.
        if (!count($data)) {
            return '';
        }

        $output = $args['before_widget'];
        $output.= $args['before_title'];
        $output.= $title;
        $output.= $args['after_title'];

        foreach ($data as $production) {
            $date_opening = new DateTime('@'.$production->__get('date-opening'));
            $date_opening->setTimezone(Helpers::getTimezone());
            $date_closing = new DateTime('@'.$production->__get('date-closing'));
            $date_closing->setTimezone(Helpers::getTimezone());
            $events = EventsModel::getProductionFuture($production->ID, 1);

            $output.= '<div class="production"><div class="production-inner">';
            $output.= '<a href="'.get_permalink($production->ID).'" class="production-img">';

            $thumb = get_the_post_thumbnail($production->ID, 'postcard-sm');
            if (!$thumb) {
                $thumb = '<img src="/app/assets/img/postcard-default.png">';
            }

            $output .= $thumb;

            $output.= '</a>';
            $output.= '<div class="production-info">';
            $output.= '<header class="production-header">';
            if ($production->__get('date-opening') < time()) {
                $output .= '<div class="production-nowplaying">Now Playing</div>';
            }

            $output.= '<a href="'.get_permalink($production->ID).'" class="production-title">';
            $output.= Helpers::bootstrapHeading($production->post_title);
            $output.= '</a>';
            $output.= '</header>';
            // Header end.
            $output .= '<div class="production-credits">';
            foreach (get_post_meta($production->ID, 'byline', false) as $byline) {
                $output.= '<div class="credit">';
                $output.= $byline['role'].' ';
                $output.= '<span class="name">';
                $output.= '<span class="names">'.$byline['name'].'</span>';
                $output.= '</span>';
                $output.= '</div>';
            }

            $output .= '</div>';

            if ($production->__get('widget-content')) {
                $output.= '<div class="production-extra">';
                $output.= wpautop($production->__get('widget-content'));
                $output.= '</div>';
            }

            $output.= '<div class="production-dates">';
            $output.= '<time datetime="'.$date_opening->format('Y-m-d').'" class="production-date-opening">';
            $output.= $date_opening->format('F j');
            $output.= '</time>';
            if ($date_opening->format('z') !== $date_closing->format('z')) {
                $output.= '&nbsp;&mdash; ';
                $output.= '<time datetime="'.$date_closing->format('Y-m-d').'"  class="production-date-closing">';
                $output.= $date_closing->format('F j');
                $output.= '</time>';
            }

            $output .= '</div>';
            // Class production-dates end.
            if ($events->have_posts()) {
                $event = $events->next_post();
                $event_date = new DateTime();
                $event_date->setTimestamp($event->__get('date-start'));
                $event_date->setTimezone(Helpers::getTimezone());
                $output.= '<div class="production-performance">';
                $output.= '<strong>Next Performance</strong><br>';
                $output.= '<a href="'.get_permalink($event->ID).'" class="production-event">';
                $output.= $event_date->format('n/j g:ia');
                $output.= '</a>';
                $output.= '</div>';
            }

            $output .= '<div class="production-buttons btn-group">';
            if ($btn_subscribe
                && of_get_option('subscriptions_btn_enabled')
                && $production->__get('ticketing-link')
            ) {
                $output.= '<a href="'.home_url(get_page_uri(of_get_option('subscriptions_btn_link_id')))
                       .'" class="btn btn-primary btn-xs">';
                $output.= htmlspecialchars(of_get_option('subscriptions_btn_link_text'));
                $output.= '</a> ';
            }

            if ($btn_tickets
                && $production->__get('ticketing-link')
            ) {
                $output .= '<a href="'.$production->__get('ticketing-link')
                       .'" class="btn btn-secondary btn-xs">Buy Tickets</a> ';
            }

            if ($btn_more) {
                $output .= '<a href="'.get_permalink($production->ID)
                       .'" class="btn btn-default btn-xs">Learn More</a>';
            }

            $output .= '</div>';
            // Class buttons end.
            $output .= '</div>';
            // Class info end.
            $output.= '<div class="clearfix"></div>';
            $output.= '</div></div>';
            // Class production end.
        }

        $output .= $args['after_widget'];

        echo $output;
    }

    /**
     * Back-end widget form.
     *
     * @param array $instance Previously saved values from database.
     *
     * @see WP_Widget::form()
     *
     * @return string
     */
    public function form($instance)
    {
        $title = (isset($instance['title'])) ? $instance['title'] : '';
        $qty = (isset($instance['qty'])) ? $instance['qty'] : 1;
        $season = (isset($instance['season'])) ? $instance['season'] : null;
        $btn_subscribe = (isset($instance['btn_subscribe'])) ? (bool) $instance['btn_subscribe'] : true;
        $btn_tickets = (isset($instance['btn_tickets'])) ? (bool) $instance['btn_tickets'] : true;
        $btn_more = (isset($instance['btn_more'])) ? (bool) $instance['btn_more'] : true;

        $seasons = get_terms(
            'season',
            [
                'fields' => 'id=>name',
                'hierarchical' => false,
                'orderby' => 'id',
                'order' => 'DESC'
            ]
        );

        $output = '<p>';
        $output.= '<label for="'.$this->get_field_id('title').'">';
        $output.= __('Panel Title:', 'roots');
        $output.= '<input class="widefat" id="'.$this->get_field_id('title').'" name="'.$this->get_field_name('title')
               .'" type="text" value="'.esc_attr($title).'"/>';
        $output.= '</label>';
        $output.= '</p>';

        $output.= '<p>';
        $output.= '<label for="'.$this->get_field_id('qty').'">';
        $output.= __('Number of Productions:', 'roots');
        $output.= '</label>';
        $output.= '<input id="'.$this->get_field_id('qty').'" name="'.$this->get_field_name('qty')
               .'" type="number" value="'.esc_attr($qty).'"/>';
        $output.= '</p>';

        $output.= '<p>';
        $output.= '<label for="'.$this->get_field_id('filter').'">';
        $output.= __('Display:', 'roots');
        $output.= '</label>';
        $output.= '<select class="widefat" id="'.$this->get_field_id('season').'" name="'
               .$this->get_field_name('season').'">';
        foreach ($seasons as $id => $name) {
            $selected = ((string) $season === (string) $id) ? 'selected' : '';
            $output.= '<option value="'.$id.'" '.$selected.'>'.$name.'</option>';
        }

        $selected = ($season === ProductionsModel::FILTER_NOSEASON) ? 'selected' : '';
        $output.= '<option value="'.ProductionsModel::FILTER_NOSEASON.'" '.$selected.'>Other Productions</option>';
        $output.= '</select>';
        $output.= '</p>';

        $output.= '<p>';
        $output.= '<label>';
        $checked = checked($btn_subscribe, true, false);
        $output.= '<input id="'.$this->get_field_id('btn_subscribe').'" name="'.$this->get_field_name('btn_subscribe')
               .'" type="checkbox" value="1" '.$checked.'>';
        $output.= __('Display Subscribe Buttons', 'roots');
        $output.= '</label>';
        $output.= '<br><small>Displayed only when also enabled in subscription settings.</small>';
        $output.= '</p>';

        $output.= '<p>';
        $output.= '<label>';
        $checked = checked($btn_tickets, true, false);
        $output.= '<input id="'.$this->get_field_id('btn_tickets').'" name="'.$this->get_field_name('btn_tickets')
               .'" type="checkbox" value="1" '.$checked.'>';
        $output.= __('Display Buy Tickets Buttons', 'roots');
        $output.= '</label>';
        $output.= '<br><small>Displayed only when a ticketing link has been declared.</small>';
        $output.= '</p>';

        $output.= '<p>';
        $output.= '<label>';
        $checked = checked($btn_more, true, false);
        $output.= '<input id="'.$this->get_field_id('btn_more').'" name="'.$this->get_field_name('btn_more')
               .'" type="checkbox" value="1" '.$checked.'>';
        $output.= __('Display Learn More Buttons', 'roots');
        $output.= '</label>';
        $output.= '</p>';

        echo $output;
    }

    /**
     * Sanitize widget form values as they are saved.
     *
     * @param array $new_instance Values just sent to be saved.
     * @param array $old_instance Values just sent to be saved.
     *
     * @see WP_Widget::update()
     *
     * @return array Updated safe values to be saved.
     */
    public function update($new_instance, $old_instance)
    {
        $instance = [];
        $instance['title'] = (!empty($new_instance['title'])) ? strip_tags($new_instance['title']) : '';
        $instance['qty'] = (!empty($new_instance['qty'])) ? strip_tags($new_instance['qty']) : '';
        $instance['season'] = (!empty($new_instance['season'])) ? $new_instance['season'] : '';
        $instance['btn_subscribe'] = intval($new_instance['btn_subscribe']);
        $instance['btn_tickets'] = intval($new_instance['btn_tickets']);
        $instance['btn_more'] = intval($new_instance['btn_more']);

        return $instance;
    }
}
