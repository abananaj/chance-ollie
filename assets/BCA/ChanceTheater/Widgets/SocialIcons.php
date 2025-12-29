<?php
/**
 * Chance Theater WordPress Template
 *
 * @package    wordpress/wordpress
 * @subpackage chancetheater/website
 * @author     Brodkin CyberArts <info@brodkinca.com>
 * @copyright  2015 Brodkin CyberArts
 * @version    Git: $Id: c82197023a210eb39184e86722aad33e4c10830d $
 * @link       http://chancetheater.com/
 */

namespace BCA\ChanceTheater\Widgets;

/**
 * Widget to display social media icons.
 */
class SocialIcons extends \WP_Widget
{
    /**
     * Register widget with WordPress.
     */
    public function __construct()
    {
        parent::__construct(
            'ct_socialicons',
            // Base ID.
            'CT:Social Icons',
            // Name.
            ['description' => __('Display social icons.', 'roots')]
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
        $title = (isset($instance['title'])) ? $instance['title'] : '';
        $rss_enabled = (isset($instance['rss_enabled'])) ? $instance['rss_enabled'] : '';

        $output = $args['before_widget'];
        $output.= $args['before_title'];
        $output.= $title;
        $output.= $args['after_title'];
        $output.= '<div class="row">';
        $output.= '<div class="col-xs-4">';
        $output.= '<p>'.$title.'</p>';
        $output.= '</div>';
        $output.= '<div class="col-xs-8">';
        if (of_get_option('social_id_facebook')) {
            $output .= '<a href="https://facebook.com/'.of_get_option('social_id_facebook').'" target="_blank">'
                .'<i class="fa fa-facebook-square" title="Facebook" data-toggle="tooltip"></i>'
                .'</a>';
        }

        if (of_get_option('social_id_twitter')) {
            $output .= '<a href="https://twitter.com/'.of_get_option('social_id_twitter').'" target="_blank">'
                .'<i class="fa fa-twitter-square" title="Twitter" data-toggle="tooltip"></i>'
                .'</a>';
        }

        if (of_get_option('social_id_google')) {
            $output .= '<a href="https://plus.google.com/&44;'.of_get_option('social_id_google').'" target="_blank">'
                .'<i class="fa fa-google-plus-square" title="Google+" data-toggle="tooltip"></i>'
                .'</a>';
        }

        if (of_get_option('social_id_youtube')) {
            $output .= '<a href="https://youtube.com/'.of_get_option('social_id_youtube').'" target="_blank">'
                .'<i class="fa fa-youtube-square" title="Youtube" data-toggle="tooltip"></i>'
                .'</a>';
        }

        if (of_get_option('social_id_instagram')) {
            $output .= '<a href="https://instagram.com/'.of_get_option('social_id_instagram').'" target="_blank">'
                .'<i class="fa fa-instagram" title="Instagram" data-toggle="tooltip"></i>'
                .'</a>';
        }

        if ($rss_enabled) {
            $output .= '<a href="'.get_bloginfo('rss2_url').'" target="_blank">'
                .'<i class="fa fa-rss-square" title="Blog RSS Feed" data-toggle="tooltip"></i>'
                .'</a>';
        }

        $output.= '</div>';
        $output.= '</div>';
        $output.= $args['after_widget'];

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
        $rss_enabled = (isset($instance['rss_enabled'])) ? $instance['rss_enabled'] : '';

        $output = '<p>';
        $output.= '<label for="'.$this->get_field_id('title').'">';
        $output.= __('Panel Title:', 'roots');
        $output.= '<input class="widefat" id="'.$this->get_field_id('title').'" name="'.$this->get_field_name('title')
               .'" type="text" value="'.esc_attr($title).'"/>';
        $output.= '</label>';
        $output.= '</p>';

        $output.= '<p>';
        $output.= '<label>';
        $checked = (!empty($rss_enabled)) ? 'checked="checked"' : '';
        $output.= '<input type="checkbox" name="'.$this->get_field_name('rss_enabled').'" '.$checked.' value="1"/>';
        $output.= __('Display RSS Feed', 'roots');
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
     * @return array Updated safe values to be saved.
     */
    public function update($new_instance, $old_instance)
    {
        $instance = [];
        $instance['title'] = (!empty($new_instance['title'])) ? strip_tags($new_instance['title']) : '';
        $instance['rss_enabled'] = (!empty($new_instance['rss_enabled'])) ? strip_tags($new_instance['rss_enabled']) : '';

        return $instance;
    }
}
