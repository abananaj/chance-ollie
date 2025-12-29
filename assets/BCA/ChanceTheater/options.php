<?php
/**
 * Chance Theater WordPress Template
 *
 * @package    wordpress/wordpress
 * @subpackage chancetheater/website
 * @author     Brodkin CyberArts <info@brodkinca.com>
 * @copyright  2015 Brodkin CyberArts
 * @version    Git: $Id: 8f5527af919cce31525642523f1a1d45ab24021a $
 * @link       http://chancetheater.com/
 */

use BCA\ChanceTheater\Helpers;

if (!function_exists('of_get_option')) {

    /**
     * Retrieve a value from the database.
     *
     * @param string $name    ID of option.
     * @param mixed  $default Default value.
     *
     * @return mixed
     */
    function of_get_option(string $name, $default = '')
    {
        $optionsframework_settings = get_option('optionsframework');
        
        // Gets the unique option id.
        $option_name = $optionsframework_settings['id'];
        
        if (get_option($option_name)) {
            $options = get_option($option_name);
        }
            
        if (isset($options[$name])) {
            return $options[$name];
        } else {
            return $default;
        }
    }
}

/**
 * Set unique identifier for Options Framework instance.
 *
 * A unique identifier is defined to store the options in the database and reference them from the theme.
 * By default it uses the theme name, in lowercase and without spaces, but this can be changed if needed.
 * If the identifier changes, it'll appear as if the options have been reset.
 *
 * @return void
 */
function optionsframework_option_name()
{
    // This gets the theme name from the stylesheet (lowercase and without spaces).
    $theme = wp_get_theme();
    $themename = $theme->name;
    $themename = preg_replace('/\W/', '_', strtolower($themename));

    $of_settings = get_option('optionsframework');
    $of_settings['id'] = $themename;
    update_option('optionsframework', $of_settings);
}

/**
 * Define options.
 *
 * Defines an array of options that will be used to generate the settings page and be saved in the database.
 * When creating the 'id' fields, make sure to use all lowercase and no spaces.
 *
 * @return array Array of options.
 */
function optionsframework_options()
{
    // Test data.
    $test_array = [
        'one' => __('One', 'options_check'),
        'two' => __('Two', 'options_check'),
        'three' => __('Three', 'options_check'),
        'four' => __('Four', 'options_check'),
        'five' => __('Five', 'options_check')
    ];

    // Multicheck Array.
    $multicheck_array = [
        'one' => __('French Toast', 'options_check'),
        'two' => __('Pancake', 'options_check'),
        'three' => __('Omelette', 'options_check'),
        'four' => __('Crepe', 'options_check'),
        'five' => __('Waffle', 'options_check')
    ];

    // Multicheck Defaults.
    $multicheck_defaults = [
        'one' => '1',
        'five' => '1'
    ];

    // Background Defaults.
    $background_defaults = [
        'color' => '',
        'image' => '',
        'repeat' => 'repeat',
        'position' => 'top center',
        'attachment'=> 'scroll' ];

    // Typography Defaults.
    $typography_defaults = [
        'size' => '15px',
        'face' => 'georgia',
        'style' => 'bold',
        'color' => '#bada55' ];

    // Typography Options.
    $typography_options = [
        'sizes' => [ '6','12','14','16','20' ],
        'faces' => [ 'Helvetica Neue' => 'Helvetica Neue','Arial' => 'Arial' ],
        'styles' => [ 'normal' => 'Normal','bold' => 'Bold' ],
        'color' => false
    ];

    // Pull all the categories into an array.
    $options_categories = [];
    $options_categories_obj = get_categories();
    foreach ($options_categories_obj as $category) {
        $options_categories[$category->cat_ID] = $category->cat_name;
    }

    // Pull all tags into an array.
    $options_tags = [];
    $options_tags_obj = get_tags();
    foreach ($options_tags_obj as $tag) {
        $options_tags[$tag->term_id] = $tag->name;
    }

    // Pull all the pages into an array.
    $options_pages = [];
    $options_pages_obj = get_pages('sort_column=post_parent,menu_order');
    $options_pages[''] = 'Select a page:';
    foreach ($options_pages_obj as $page) {
        $options_pages[$page->ID] = $page->post_title;
    }

    // If using image radio buttons, define a directory path.
    $imagepath = get_template_directory_uri().'/images/';

    $options = [];

    $options[] = [
        'name' => 'Sections',
        'type' => 'heading'
    ];

    $post_types = Helpers::getPostTypesAssoc();
    $title_options = [];
    $type_options = [];
    foreach ($post_types as $post_type_id => $post_type_label) {
        $post_type = get_post_type_object($post_type_id);

        $field = [
            'id' => 'section_title_'.$post_type_id,
            'name' => $post_type_label.' Section Title',
            'type' => 'text',
            'std' => $post_type_label
        ];
        $title_options[] = $field;

        $field = [
            'id' => 'section_type_'.$post_type_id,
            'name' => $post_type_label.' Type Override',
            'type' => 'select',
            'std' => $post_type_id,
            'options' => $post_types
        ];
        $type_options[] = $field;
    }

    $options = array_merge($options, $title_options, $type_options);

    $options[] = [
        'name' => 'Social',
        'type' => 'heading'
    ];

    $options[] = [
        'name' => 'Facebook Username',
        'id' => 'social_id_facebook',
        'type' => 'text'
    ];

    $options[] = [
        'name' => 'Google+ Username',
        'id' => 'social_id_google',
        'type' => 'text'
    ];

    $options[] = [
        'name' => 'Instagram Username',
        'id' => 'social_id_instagram',
        'type' => 'text'
    ];

    $options[] = [
        'name' => 'Twitter Username',
        'id' => 'social_id_twitter',
        'type' => 'text'
    ];

    $options[] = [
        'name' => 'Youtube Username',
        'id' => 'social_id_youtube',
        'type' => 'text'
    ];

    $options[] = [
        'name' => 'Subscriptions',
        'type' => 'heading'
    ];

    $options[] = [
        'name' => 'Display subscribe buttons?',
        'id' => 'subscriptions_btn_enabled',
        'type' => 'radio',
        'std' => 1,
        'options' => [
            '1' => 'Yes',
            '0' => 'No'
        ]
    ];

    $options[] = [
        'name' => 'Link Text',
        'desc' => 'Short and actionable.',
        'id' => 'subscriptions_btn_link_text',
        'type' => 'text',
        'std' => 'Subscribe'
    ];

    $pages_query = new WP_Query([
        'post_type' => 'page',
        'nopaging' => true
    ]);
    $pages = [];
    while ($pages_query->have_posts()) {
        $pages_query->the_post();
        $pages[get_the_ID()] = get_the_title();
    }

    $options[] = [
        'name' => 'Call-To-Action Headline',
        'id' => 'subscriptions_cta_headline',
        'type' => 'text',
        'std' => 'The quick, brown fox jumps over a lazy dog.'
    ];

    $options[] = [
        'name' => 'Call-To-Action Body',
        'desc' => '<strong>Keep it short!</strong> <br>This text will be displayed in '.
            'very large print on the subscriptions page and anywhere that the '.
            'shortcode appears on a page or post.',
        'id' => 'subscriptions_cta_body',
        'type' => 'textarea',
        'std' => 'Junk MTV quiz graced by fox whelps. Bawds jog, flick quartz, '.
            'vex nymphs. Waltz, bad nymph, for quick jigs vex! Fox nymphs '.
            'grab quick-jived waltz.'
    ];

    $options[] = [
        'name' => 'Subscribe Call-To-Action Button Text',
        'desc' => 'Short and actionable.',
        'id' => 'subscriptions_cta_link_text',
        'type' => 'text',
        'std' => 'Become a Subscriber!'
    ];

    $options[] = [
        'name' => 'Subscribe Link Destination',
        'id' => 'subscriptions_btn_link_id',
        'type' => 'select',
        'options' => $pages
    ];

    $options[] = [
        'name' => 'Membership Call-To-Action Button Text',
        'desc' => 'Short and actionable.',
        'id' => 'memberships_cta_link_text',
        'type' => 'text',
        'std' => 'Become a Member!'
    ];

    $options[] = [
        'name' => 'Membership Link Destination',
        'id' => 'memberships_btn_link_id',
        'type' => 'select',
        'options' => $pages
    ];

    $options[] = [
        'name' => 'Event Calendar',
        'type' => 'heading'
    ];

    $event_types = [];
    foreach (get_terms('event-type') as $term) {
        $event_types[$term->term_id] = $term->name;
    }

    $options[] = [
        'name' => 'Event types in calendar key:',
        'desc' => 'Select up to eight event types to appear on the event calendar key.',
        'id' => 'event_types_enabled',
        'type' => 'multicheck',
        'options' => $event_types
    ];

    $options[] = [
        'name' => 'Mailing Lists',
        'type' => 'heading',
        'std' => 'Join Our Mailing Lists!'
    ];

    $options[] = [
        'name' => 'Window Title',
        'id' => 'mailing_list_heading',
        'type' => 'text'
    ];

    $options[] = [
        'name' => 'Window Content',
        'desc' => 'Include all text and shortcodes here.',
        'id' => 'mailing_list_body',
        'type' => 'editor'
    ];

    $options[] = [
        'name' => 'Productions',
        'type' => 'heading'
    ];

    $options[] = [
        'name' => 'TAB TITLES',
        'type' => 'info'
    ];

    $options[] = [
        'name' => 'Synopsis',
        'id' => 'production_tab_synopsis',
        'type' => 'text'
    ];

    $options[] = [
        'name' => 'Video',
        'id' => 'production_tab_video',
        'type' => 'text'
    ];

    $options[] = [
        'name' => 'Buzz',
        'id' => 'production_tab_buzz',
        'type' => 'text'
    ];

    return $options;
}
