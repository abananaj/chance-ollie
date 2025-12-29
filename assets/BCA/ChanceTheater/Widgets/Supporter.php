<?php
/**
 * Chance Theater WordPress Template
 *
 * @package    wordpress/wordpress
 * @subpackage chancetheater/website
 * @author     Brodkin CyberArts <info@brodkinca.com>
 * @copyright  2015 Brodkin CyberArts
 * @version    Git: $Id: d4f8d2f1444d83ddacef03770f28c0f958a810e0 $
 * @link       http://chancetheater.com/
 */

namespace BCA\ChanceTheater\Widgets;

use BCA\ChanceTheater\Models\Supporters as SupportersModel;

/**
 * Widget to display supporters.
 */
class Supporter extends \WP_Widget
{
    /**
     * Register widget with WordPress.
     */
    public function __construct()
    {
        parent::__construct(
            'ct_supporter',
            // Base ID.
            'CT:Supporter',
            // Name.
            ['description' => __('Display random supporter.', 'roots')]
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
        $title = apply_filters('widget_title', $instance['title']);
        $qty = (isset($instance['qty'])) ? $instance['qty'] : 1;

        $supporters = SupportersModel::getRandomSupporters($qty);

        $output = $args['before_widget'];
        $output.= $args['before_title'];
        $output.= $args['after_title'];
        $output.= '<div class="supporters-title">'.$title.'</div>';
        while ($supporters->have_posts()) {
            $supporters->the_post();
            if (get_post_meta(get_the_ID(), 'supporter-type', true) === 'institutional') {
                $url = get_post_meta(get_the_ID(), 'website', true);
                $output.= '<div class="supporter-institutional">';
                if ($url) {
                    $output .= '<a href="'.$url.'">';
                }

                $output.= '<span title="'.get_the_title().'" data-toggle="tooltip" data-placement="auto left">';
                $output.= get_the_post_thumbnail(get_the_ID(), 'supporter-logo');
                $output.= '</span>';
                if ($url) {
                    $output .= '</a>';
                }

                $output .= '</div>';
            } elseif (get_post_meta(get_the_ID(), 'supporter-type', true) === 'individual') {
                $output.= '<div class="supporter-individual">';
                $output.= get_the_title();
                $output.= '</div>';
            }
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
        $title = __('Special Thanks', 'roots');
        if (isset($instance['title'])) {
            $title = $instance['title'];
        }

        $qty = '5';
        if (isset($instance['qty'])) {
            $qty = $instance['qty'];
        }

        $output = '<p>';
        $output.= '<label for="'.$this->get_field_id('title').'">';
        $output.= __('Panel Title:', 'roots');
        $output.= '<input class="widefat" id="'.$this->get_field_id('title').'" name="'.$this->get_field_name('title')
               .'" type="text" value="'.esc_attr($title).'"/>';
        $output.= '</label>';
        $output.= '</p>';

        $output.= '<p>';
        $output.= '<label for="'.$this->get_field_id('qty').'">';
        $output.= __('Number of Supporters:', 'roots');
        $output.= '</label>';
        $output.= '<input id="'.$this->get_field_id('qty').'" name="'.$this->get_field_name('qty')
               .'" type="number" value="'.esc_attr($qty).'"/>';
        $output.= '</p>';

        echo $output;
    }

    /**
     * Sanitize widget form values as they are saved.
     *
     * @param array $new_instance Values just sent to be saved.
     * @param array $old_instance Previously saved values from database.
     *
     * @return array Updated safe values to be saved.
     */
    public function update($new_instance, $old_instance)
    {
        $instance = [];
        $instance['title'] = (!empty($new_instance['title'])) ? strip_tags($new_instance['title']) : '';
        $instance['qty'] = (!empty($new_instance['qty'])) ? strip_tags($new_instance['qty']) : '';

        return $instance;
    }
}
