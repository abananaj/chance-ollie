<?php
/**
 * Chance Theater WordPress Template
 *
 * @package    wordpress/wordpress
 * @subpackage chancetheater/website
 * @author     Brodkin CyberArts <info@brodkinca.com>
 * @copyright  2015 Brodkin CyberArts
 * @version    Git: $Id: 3be965efdfa0af9aa38d9c93f8a7389b6d002710 $
 * @link       http://chancetheater.com/
 */

namespace BCA\ChanceTheater\Widgets;

use \DateTime;
use \WP_Query;
use BCA\ChanceTheater\Helpers;
use BCA\ChanceTheater\Calendar;

/**
 * Widget for calendar events.
 */
class EventCalendar extends \WP_Widget
{
    /**
     * Register widget with WordPress.
     */
    public function __construct()
    {
        parent::__construct(
            'ct_event_calendar',
            // Base ID.
            'CT:Event Calendar',
            // Name.
            ['description' => __('Display upcoming events.', 'roots')]
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
        $title = (isset($instance['title'])) ? $instance['title'] : __('Upcoming Events', 'roots');
        $qty = (isset($instance['qty'])) ? $instance['qty'] : 1;
        $style = (isset($instance['style'])) ? $instance['style'] : Calendar::FORMAT_CALENDAR;
        $cat_include = (isset($instance['cat_include'])) ? (array) $instance['cat_include'] : [];
        $cat_exclude = (isset($instance['cat_exclude'])) ? (array) $instance['cat_exclude'] : [];
        // @codingStandardsIgnoreEnd

        $date_start = new DateTime();
        $date_start->setTimezone(Helpers::getTimezone());
        $date_end = clone $date_start;

        if ($style === Calendar::FORMAT_CALENDAR) {
            $qty_months = $qty;
            $date_start->setDate(
                $date_start->format('Y'),
                $date_start->format('n'),
                1
            );
            $date_start->setTime(0, 0, 0);
            $date_end->modify('+ '.$qty.' months');
        } else {
            $qty_months = ceil($qty / 31);
            $date_start->modify('-1 hours');
            $date_end->modify('+ '.$qty.' days');
        }

        $query = [
            'post_type' => 'ct-event',
            'posts_per_page' => -1,
            'meta_query' => [
                [
                    'key'=> 'date-start',
                    'value'=> $date_start->format('U'),
                    'compare'=> '>='
                ],
                [
                    'key'=> 'date-end',
                    'value'=> $date_end->format('U'),
                    'compare'=> '<='
                ]
            ]
        ];

        if (count($cat_include) > 0) {
            $query['tax_query'][] = [
                'taxonomy' => 'event-type',
                'field' => 'id',
                'terms' => $cat_include,
                'operator' => 'IN'
            ];
        }

        if (count($cat_exclude) > 0) {
            $query['tax_query'][] = [
                'taxonomy' => 'event-type',
                'field' => 'id',
                'terms' => $cat_exclude,
                'operator' => 'NOT IN'
            ];
        }

        $events = new WP_Query($query);

        // Do not display anything if there are no events.
        if (!$events->have_posts() && $style !== Calendar::FORMAT_CALENDAR) {
            return '';
        }

        $calendar = new Calendar();

        while ($events->have_posts()) {
            $events->the_post();
            $calendar->addEvent(get_post());
        }

        $month = $date_start->format('m');
        $year = $date_start->format('Y');

        $output = $args['before_widget'];
        $output.= $args['before_title'];
        $output.= $title;
        $output.= $args['after_title'];

        for ($i= 1; $i <= $qty_months; $i++) {
            $output .= $calendar->render($month, $year, [$style, Calendar::ABBREVIATE_DAYS]);
            $month++;
            if ($month > 12) {
                $month = 1;
                $year++;
            }
        }

        if ($style === Calendar::FORMAT_CALENDAR) {
            $date_month_previous = clone $date_start;
            $date_month_previous->modify('- 1 day');

            $date_month_next = clone $date_end;
            $date_month_next->modify('+ 1 day');

            $calendar_url = get_post_type_archive_link('ct-event');
            $month_current_url = add_query_arg(
                [
                    'cal_month' => $date_start->format('m'),
                    'cal_year' => $date_start->format('Y')
                ],
                $calendar_url
            );
            $month_prev_url = add_query_arg(
                [
                    'cal_month' => $date_month_previous->format('m'),
                    'cal_year' => $date_month_previous->format('Y')
                ],
                $calendar_url
            );
            $month_next_url = add_query_arg(
                [
                    'cal_month' => $date_month_next->format('m'),
                    'cal_year' => $date_month_next->format('Y')
                ],
                $calendar_url
            );

            $output.= '<div class="calendar-nav">';
            $output.= '<div class="btn-group">';
            $output.= '<a href="'.$month_prev_url.'" class="btn btn-xs btn-default calendar-month-prev">';
            $output.= '<i class="fa fa-angle-double-left"></i> ';
            $output.= $date_month_previous->format('F');
            $output.= '</a>';
            $output.= '<a href="'.$month_current_url.'" class="btn btn-xs btn-default active calendar-month-current">';
            $output.= '<i class="fa fa-calendar"></i>';
            $output.= '</a>';
            $output.= '<a href="'.$month_next_url.'" class="btn btn-xs btn-default calendar-month-next">';
            $output.= $date_month_next->format('F');
            $output.= ' <i class="fa fa-angle-double-right"></i>';
            $output.= '</a>';
            $output.= '</div></div>';
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
     * @return void
     */
    public function form($instance)
    {
        // @codingStandardsIgnoreStart
        $title = (isset($instance['title'])) ? $instance['title'] : __('Upcoming Events', 'roots');
        $qty = (isset($instance['qty'])) ? $instance['qty'] : 1;
        $style = (isset($instance['style'])) ? $instance['style'] : Calendar::FORMAT_CALENDAR;
        $cat_include = (isset($instance['cat_include'])) ? (array) $instance['cat_include'] : [];
        $cat_exclude = (isset($instance['cat_exclude'])) ? (array) $instance['cat_exclude'] : [];
        // @codingStandardsIgnoreEnd

        $output = '<p>';
        $output.= '<label for="'.$this->get_field_id('title').'">';
        $output.= __('Panel Title:', 'roots');
        $output.= '<input class="widefat" id="'.$this->get_field_id('title').'" name="'
               .$this->get_field_name('title').'" type="text" value="'.esc_attr($title).'"/>';
        $output.= '</label>';
        $output.= '</p>';

        $output.= '<p>';
        $output.= '<label for="'.$this->get_field_id('style').'">';
        $output.= __('Display Style:', 'roots');
        $output.= '</label>';
        $output.= '<select class="widefat" id="'.$this->get_field_id('style').'" name="'
               .$this->get_field_name('style').'">';
        // @codingStandardsIgnoreStart
        $selected = ($style === Calendar::FORMAT_CALENDAR) ? 'selected' : '';
        $output.= '<option value="'.Calendar::FORMAT_CALENDAR.'" '.$selected.'>Calendar</option>';
        $selected = ($style === Calendar::FORMAT_AGENDA) ? 'selected' : '';
        $output.= '<option value="'.Calendar::FORMAT_AGENDA.'" '.$selected.'>Agenda</option>';
        $selected = ($style === Calendar::FORMAT_PHOTOGRID) ? 'selected' : '';
        $output.= '<option value="'.Calendar::FORMAT_PHOTOGRID.'" '.$selected
               .'>Agenda w/ Images</option>';
        // @codingStandardsIgnoreEnd
        $output.= '</select>';
        $output.= '</p>';

        $output.= '<p>';
        $output.= '<label for="'.$this->get_field_id('qty').'">';
        $output.= __('Display Period:', 'roots');
        $output.= '</label>';
        $output.= '<input id="'.$this->get_field_id('qty').'" name="'.$this->get_field_name('qty')
               .'" type="number" value="'.esc_attr($qty).'"> ';
        // @codingStandardsIgnoreStart
        $output.= ($style === Calendar::FORMAT_CALENDAR) ? 'Months' : 'Days';
        $output.= '</p>';
        // @codingStandardsIgnoreEnd

        $categories = get_terms('event-type');
        if (count($categories) > 0) {
            $output.= '<p>';
            $output.= __('Include Event Categories:', 'roots').'<br>';
            foreach ($categories as $category) {
                $id = $category->term_id;
                // @codingStandardsIgnoreStart
                $checked = (in_array($id, $cat_include)) ? 'checked' : '';
                // @codingStandardsIgnoreEnd
                $output.= '<label>';
                $output.= '<input  class="widefat" id="'.$this->get_field_id('cat_include').'" ';
                $output.= 'name="'.$this->get_field_name('cat_include').'[]" type="checkbox" ';
                $output.= 'value="'.$id.'" '.$checked.'>';
                $output.= $category->name;
                $output.= '</label><br>';
            }

            $output.= 'Select nothing to include everything.';
            $output.= '</p>';
            $output.= '<p>';
            $output.= __('Exclude Event Categories:', 'roots').'<br>';
            foreach ($categories as $category) {
                $id = $category->term_id;
                // @codingStandardsIgnoreStart
                $checked = (in_array($id, $cat_exclude)) ? 'checked' : '';
                // @codingStandardsIgnoreEnd
                $output.= '<label>';
                $output.= '<input  class="widefat" id="'.$this->get_field_id('cat_exclude').'" ';
                $output.= 'name="'.$this->get_field_name('cat_exclude').'[]" type="checkbox" ';
                $output.= 'value="'.$id.'" '.$checked.'>';
                $output.= $category->name;
                $output.= '</label><br>';
            }

            $output .= '</p>';
        }

        echo $output;
    }

    /**
     * Sanitize widget form values as they are saved.
     *
     * @param array $new_instance Values just sent to be saved.
     * @param array $old_instance Values just sent to be saved.
     *
     * @return array Updated safe values to be saved.
     */
    public function update($new_instance, $old_instance)
    {
        // @codingStandardsIgnoreStart
        $instance = [];
        $instance['title'] = (!empty($new_instance['title'])) ? strip_tags($new_instance['title']) : '';
        $instance['qty'] = (!empty($new_instance['qty'])) ? strip_tags($new_instance['qty']) : '';
        $instance['style'] = (!empty($new_instance['style'])) ? strip_tags($new_instance['style']) : '';
        $instance['cat_include'] = (is_array($new_instance['cat_include'])) ? $new_instance['cat_include'] : [];
        $instance['cat_exclude'] = (is_array($new_instance['cat_exclude'])) ? $new_instance['cat_exclude'] : [];
        // @codingStandardsIgnoreEnd

        return $instance;
    }
}
