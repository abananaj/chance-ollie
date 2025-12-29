<?php
/**
 * ACF Field Groups for Chance Theater
 *
 * @package    wordpress/wordpress
 * @subpackage chancetheater/website
 */

use BCA\ChanceTheater\Helpers;
use BCA\FontAwesomeIterator\Iterator as FontAwesomeIterator;

if (!function_exists('acf_add_local_field_group')) {
    return;
}

$venue_rooms = [
    '' => 'Inherit from Production',
    'offsite' => 'Offsite',
    'cripe' => 'Cripe Stage',
    'fydamar' => 'Fyda-Mar Stage',
    'classroom' => 'Classroom',
    'lobby' => 'Lobby'
];

// Get FontAwesome icons
$fa_iterator = new FontAwesomeIterator(
    TEMPLATEPATH.'/assets/vendor/font-awesome/css/font-awesome.css'
);

$fa_icons = [];
foreach ($fa_iterator as $icon) {
    $fa_icons[$icon->class] = $icon->name;
}

/**
 * Artist Profile
 */
acf_add_local_field_group([
    'key' => 'group_artist_profile',
    'title' => 'Artist Profile',
    'fields' => [
        [
            'key' => 'field_artist_profession',
            'label' => 'Profession',
            'name' => 'profession',
            'type' => 'text',
        ],
        [
            'key' => 'field_artist_resident',
            'label' => 'Resident Artist?',
            'name' => 'resident',
            'type' => 'true_false',
            'default_value' => 0,
        ],
        [
            'key' => 'field_artist_website',
            'label' => 'Website',
            'name' => 'website',
            'type' => 'url',
        ],
        [
            'key' => 'field_artist_facebook',
            'label' => 'Facebook',
            'name' => 'facebook',
            'type' => 'url',
        ],
        [
            'key' => 'field_artist_twitter',
            'label' => 'Twitter',
            'name' => 'twitter',
            'type' => 'url',
        ],
        [
            'key' => 'field_artist_linkedin',
            'label' => 'LinkedIn',
            'name' => 'linkedin',
            'type' => 'url',
        ],
    ],
    'location' => [
        [
            [
                'param' => 'post_type',
                'operator' => '==',
                'value' => 'artist',
            ],
        ],
    ],
]);

/**
 * Event Details
 */
acf_add_local_field_group([
    'key' => 'group_event_details',
    'title' => 'Event Details',
    'fields' => [
        [
            'key' => 'field_event_date_start',
            'label' => 'Event Start Date/Time',
            'name' => 'date-start',
            'type' => 'date_time_picker',
            'return_format' => 'U',
            'wrapper' => ['width' => '50'],
        ],
        [
            'key' => 'field_event_date_end',
            'label' => 'Event End Date/Time',
            'name' => 'date-end',
            'type' => 'date_time_picker',
            'return_format' => 'U',
            'wrapper' => ['width' => '50'],
        ],
        [
            'key' => 'field_event_production',
            'label' => 'Production (if applicable)',
            'name' => 'production',
            'type' => 'post_object',
            'instructions' => 'When chosen the production details and a link to the production\'s page will appear on the event page.',
            'post_type' => ['production'],
            'return_format' => 'id',
            'wrapper' => ['width' => '33.33'],
        ],
        [
            'key' => 'field_event_venue',
            'label' => 'Venue/Location',
            'name' => 'venue',
            'type' => 'post_object',
            'instructions' => 'Required if no production selected.',
            'post_type' => ['venue'],
            'return_format' => 'id',
            'wrapper' => ['width' => '33.33'],
        ],
        [
            'key' => 'field_event_venue_room',
            'label' => 'BATAC Room',
            'name' => 'venue-room',
            'type' => 'select',
            'instructions' => 'Required',
            'choices' => $venue_rooms,
            'wrapper' => ['width' => '33.33'],
        ],
        [
            'key' => 'field_event_is_promo',
            'label' => 'This event is promotional',
            'name' => 'is-promo',
            'type' => 'true_false',
        ],
        [
            'key' => 'field_event_ticketing_link',
            'label' => 'Buy Tickets Link',
            'name' => 'ticketing-link',
            'type' => 'url',
        ],
        [
            'key' => 'field_event_description',
            'label' => 'Event Description (optional)',
            'name' => 'description',
            'type' => 'wysiwyg',
            'instructions' => 'Will appear above production details when a production has been selected.',
        ],
    ],
    'location' => [
        [
            [
                'param' => 'post_type',
                'operator' => '==',
                'value' => 'event',
            ],
        ],
    ],
]);

/**
 * Related Posts
 */
acf_add_local_field_group([
    'key' => 'group_related_posts',
    'title' => 'Assign Related Posts',
    'fields' => [
        [
            'key' => 'field_post_production',
            'label' => 'Related Production',
            'name' => 'production',
            'type' => 'post_object',
            'post_type' => ['production'],
            'return_format' => 'id',
            'allow_null' => 1,
        ],
    ],
    'location' => [
        [
            [
                'param' => 'post_type',
                'operator' => '==',
                'value' => 'post',
            ],
        ],
    ],
]);

/**
 * Production Details
 */
acf_add_local_field_group([
    'key' => 'group_production_details',
    'title' => 'Production Details',
    'fields' => [
        [
            'key' => 'field_production_venue',
            'label' => 'Venue',
            'name' => 'venue',
            'type' => 'post_object',
            'post_type' => ['venue'],
            'return_format' => 'id',
            'wrapper' => ['width' => '58.33'],
        ],
        [
            'key' => 'field_production_venue_room',
            'label' => 'BATAC Room',
            'name' => 'venue-room',
            'type' => 'select',
            'choices' => array_slice($venue_rooms, 1),
            'wrapper' => ['width' => '41.67'],
        ],
        [
            'key' => 'field_production_ticketing_link',
            'label' => 'Buy Tickets URL',
            'name' => 'ticketing-link',
            'type' => 'url',
        ],
        [
            'key' => 'field_production_video_trailer_url',
            'label' => 'Video Trailer URL',
            'name' => 'video-trailer-url',
            'type' => 'url',
        ],
        [
            'key' => 'field_production_runtime',
            'label' => 'Approx. Length',
            'name' => 'runtime',
            'type' => 'number',
            'instructions' => '(in minutes)',
            'wrapper' => ['width' => '25'],
        ],
        [
            'key' => 'field_production_date_opening',
            'label' => 'Opening Date',
            'name' => 'date-opening',
            'type' => 'date_picker',
            'instructions' => 'mm/dd/yyyy',
            'return_format' => 'U',
            'wrapper' => ['width' => '25'],
        ],
        [
            'key' => 'field_production_date_closing',
            'label' => 'Closing Date',
            'name' => 'date-closing',
            'type' => 'date_picker',
            'instructions' => 'mm/dd/yyyy',
            'return_format' => 'U',
            'wrapper' => ['width' => '25'],
        ],
        [
            'key' => 'field_production_intermissions',
            'label' => 'Intermissions',
            'name' => 'intermissions',
            'type' => 'select',
            'choices' => [
                0 => 'None',
                1 => 'One',
                2 => 'Two',
                3 => 'Three',
            ],
            'default_value' => 1,
            'wrapper' => ['width' => '25'],
        ],
        [
            'key' => 'field_production_hide_subscribe',
            'label' => 'Hide Subscribe Button?',
            'name' => 'hide-subscribe',
            'type' => 'true_false',
        ],
        [
            'key' => 'field_production_tabs',
            'label' => 'Tabs',
            'name' => 'tabs',
            'type' => 'repeater',
            'layout' => 'block',
            'sub_fields' => [
                [
                    'key' => 'field_production_tab_title',
                    'label' => 'Tab Title',
                    'name' => 'title',
                    'type' => 'text',
                    'wrapper' => ['width' => '66.67'],
                ],
                [
                    'key' => 'field_production_tab_slug',
                    'label' => 'Slug',
                    'name' => 'slug',
                    'type' => 'text',
                    'wrapper' => ['width' => '33.33'],
                ],
                [
                    'key' => 'field_production_tab_content',
                    'label' => 'Tab Content',
                    'name' => 'content',
                    'type' => 'wysiwyg',
                ],
            ],
        ],
        [
            'key' => 'field_production_notes',
            'label' => 'Special Notes',
            'name' => 'notes',
            'type' => 'repeater',
            'layout' => 'table',
            'sub_fields' => [
                [
                    'key' => 'field_production_note_icon',
                    'label' => 'Icon',
                    'name' => 'icon',
                    'type' => 'select',
                    'choices' => $fa_icons,
                    'wrapper' => ['width' => '16.67'],
                ],
                [
                    'key' => 'field_production_note_desc',
                    'label' => 'Description',
                    'name' => 'desc',
                    'type' => 'text',
                    'wrapper' => ['width' => '50'],
                ],
                [
                    'key' => 'field_production_note_link',
                    'label' => 'Link to Page',
                    'name' => 'link',
                    'type' => 'post_object',
                    'return_format' => 'id',
                    'wrapper' => ['width' => '33.33'],
                ],
            ],
        ],
        [
            'key' => 'field_production_bylines',
            'label' => 'Bylines',
            'name' => 'byline',
            'type' => 'repeater',
            'layout' => 'table',
            'sub_fields' => [
                [
                    'key' => 'field_production_byline_role',
                    'label' => 'Credited Roles',
                    'name' => 'role',
                    'type' => 'text',
                    'wrapper' => ['width' => '33.33'],
                ],
                [
                    'key' => 'field_production_byline_name',
                    'label' => 'Name',
                    'name' => 'name',
                    'type' => 'text',
                    'wrapper' => ['width' => '66.67'],
                ],
            ],
        ],
    ],
    'location' => [
        [
            [
                'param' => 'post_type',
                'operator' => '==',
                'value' => 'production',
            ],
        ],
    ],
]);

/**
 * Awards and Recognition
 */
acf_add_local_field_group([
    'key' => 'group_production_awards',
    'title' => 'Awards and Recognition',
    'fields' => [
        [
            'key' => 'field_production_awards',
            'label' => 'Awards and Honors',
            'name' => 'awards',
            'type' => 'repeater',
            'layout' => 'table',
            'max' => 4,
            'sub_fields' => [
                [
                    'key' => 'field_production_award_headline',
                    'label' => 'Award Text',
                    'name' => 'award-headline',
                    'type' => 'text',
                    'wrapper' => ['width' => '50'],
                ],
                [
                    'key' => 'field_production_award_text',
                    'label' => 'Citation',
                    'name' => 'award-text',
                    'type' => 'text',
                    'wrapper' => ['width' => '50'],
                ],
            ],
        ],
        [
            'key' => 'field_production_quotes',
            'label' => 'Quotes',
            'name' => 'quotes',
            'type' => 'repeater',
            'layout' => 'block',
            'sub_fields' => [
                [
                    'key' => 'field_production_quote_text',
                    'label' => 'Quote',
                    'name' => 'quote-text',
                    'type' => 'textarea',
                ],
                [
                    'key' => 'field_production_quote_cite',
                    'label' => 'Citation',
                    'name' => 'quote-cite',
                    'type' => 'text',
                    'wrapper' => ['width' => '50'],
                ],
                [
                    'key' => 'field_production_quote_link',
                    'label' => 'Link to Blog Post',
                    'name' => 'quote-link',
                    'type' => 'post_object',
                    'post_type' => ['post'],
                    'return_format' => 'id',
                    'wrapper' => ['width' => '50'],
                ],
            ],
        ],
    ],
    'location' => [
        [
            [
                'param' => 'post_type',
                'operator' => '==',
                'value' => 'production',
            ],
        ],
    ],
]);

/**
 * Productions Widget
 */
acf_add_local_field_group([
    'key' => 'group_production_widget',
    'title' => 'Productions Widget',
    'fields' => [
        [
            'key' => 'field_production_widget_content',
            'label' => 'Additional Widget Content',
            'name' => 'widget-content',
            'type' => 'wysiwyg',
        ],
    ],
    'location' => [
        [
            [
                'param' => 'post_type',
                'operator' => '==',
                'value' => 'production',
            ],
        ],
    ],
]);

/**
 * Season Display
 */
$header_dimensions = Helpers::getImageDimensions('production-season-header', 'array');
acf_add_local_field_group([
    'key' => 'group_production_season',
    'title' => 'Season Display',
    'fields' => [
        [
            'key' => 'field_production_img_season_header',
            'label' => 'Production Header',
            'name' => 'img-season-header',
            'type' => 'image',
            'instructions' => 'Recommended size: ' . $header_dimensions[0] . 'x' . $header_dimensions[1],
            'return_format' => 'id',
        ],
    ],
    'location' => [
        [
            [
                'param' => 'post_type',
                'operator' => '==',
                'value' => 'production',
            ],
        ],
    ],
]);

/**
 * Production Role
 */
acf_add_local_field_group([
    'key' => 'group_production_role',
    'title' => 'Assign Artist to Production',
    'fields' => [
        [
            'key' => 'field_role_production',
            'label' => 'Production',
            'name' => 'production',
            'type' => 'post_object',
            'post_type' => ['production'],
            'return_format' => 'id',
            'wrapper' => ['width' => '50'],
        ],
        [
            'key' => 'field_role_artist',
            'label' => 'Artist Name',
            'name' => 'artist',
            'type' => 'post_object',
            'post_type' => ['artist', 'supporter'],
            'return_format' => 'id',
            'wrapper' => ['width' => '50'],
        ],
        [
            'key' => 'field_role_group',
            'label' => 'Group',
            'name' => 'role-group',
            'type' => 'select',
            'choices' => [
                'playwright' => 'Playwright',
                'actor' => 'Actors',
                'director' => 'Director',
                'choreographer' => 'Choreographer',
                'designer' => 'Designers',
                'producer' => 'Producers',
                'other' => 'Others',
            ],
            'wrapper' => ['width' => '50'],
        ],
        [
            'key' => 'field_role_credited_role',
            'label' => 'Credited Role',
            'name' => 'role',
            'type' => 'text',
            'wrapper' => ['width' => '50'],
        ],
    ],
    'location' => [
        [
            [
                'param' => 'post_type',
                'operator' => '==',
                'value' => 'production-role',
            ],
        ],
    ],
]);

/**
 * Staff Profile
 */
acf_add_local_field_group([
    'key' => 'group_staff_profile',
    'title' => 'Staff Profile',
    'fields' => [
        [
            'key' => 'field_staff_title',
            'label' => 'Job Title',
            'name' => 'title',
            'type' => 'text',
        ],
    ],
    'location' => [
        [
            [
                'param' => 'post_type',
                'operator' => '==',
                'value' => 'staff',
            ],
        ],
    ],
]);

/**
 * Supporter Profile
 */
acf_add_local_field_group([
    'key' => 'group_supporter_profile',
    'title' => 'Supporter Profile',
    'fields' => [
        [
            'key' => 'field_supporter_type',
            'label' => 'Supporter Type',
            'name' => 'supporter-type',
            'type' => 'radio',
            'choices' => [
                'individual' => 'Individual',
                'institutional' => 'Institutional',
            ],
            'default_value' => 'individual',
        ],
        [
            'key' => 'field_supporter_donor_level',
            'label' => 'Donor Contribution Level',
            'name' => 'donor-level',
            'type' => 'taxonomy',
            'taxonomy' => 'supporter-level',
            'field_type' => 'select',
            'return_format' => 'id',
            'allow_null' => 1,
        ],
        [
            'key' => 'field_supporter_website',
            'label' => 'Website',
            'name' => 'website',
            'type' => 'url',
        ],
    ],
    'location' => [
        [
            [
                'param' => 'post_type',
                'operator' => '==',
                'value' => 'supporter',
            ],
        ],
    ],
]);

/**
 * Board of Directors
 */
acf_add_local_field_group([
    'key' => 'group_board_directors',
    'title' => 'Board of Directors',
    'fields' => [
        [
            'key' => 'field_supporter_board_position',
            'label' => 'Board Member Position',
            'name' => 'board-position',
            'type' => 'taxonomy',
            'taxonomy' => 'board-positions',
            'field_type' => 'select',
            'return_format' => 'id',
            'allow_null' => 1,
        ],
    ],
    'location' => [
        [
            [
                'param' => 'post_type',
                'operator' => '==',
                'value' => 'supporter',
            ],
        ],
    ],
]);

/**
 * Display Settings
 */
acf_add_local_field_group([
    'key' => 'group_supporter_display',
    'title' => 'Display Settings',
    'fields' => [
        [
            'key' => 'field_supporter_display_donors',
            'label' => 'Display in list of donors?',
            'name' => 'display-donors',
            'type' => 'true_false',
        ],
        [
            'key' => 'field_supporter_display_sidebar',
            'label' => 'Display in supporters widget?',
            'name' => 'display-sidebar',
            'type' => 'true_false',
            'default_value' => 1,
        ],
    ],
    'location' => [
        [
            [
                'param' => 'post_type',
                'operator' => '==',
                'value' => 'supporter',
            ],
        ],
    ],
]);

/**
 * Venue Details
 */
acf_add_local_field_group([
    'key' => 'group_venue_details',
    'title' => 'Venue Details',
    'fields' => [
        [
            'key' => 'field_venue_street_address',
            'label' => 'Street Address',
            'name' => 'street-address',
            'type' => 'text',
        ],
        [
            'key' => 'field_venue_locality',
            'label' => 'City',
            'name' => 'locality',
            'type' => 'text',
            'wrapper' => ['width' => '50'],
        ],
        [
            'key' => 'field_venue_region',
            'label' => 'State',
            'name' => 'region',
            'type' => 'text',
            'default_value' => 'CA',
            'wrapper' => ['width' => '16.67'],
        ],
        [
            'key' => 'field_venue_postal_code',
            'label' => 'Zip Code',
            'name' => 'postal-code',
            'type' => 'text',
            'wrapper' => ['width' => '16.67'],
        ],
        [
            'key' => 'field_venue_country_name',
            'label' => 'Country',
            'name' => 'country-name',
            'type' => 'text',
            'default_value' => 'US',
            'readonly' => 1,
            'wrapper' => ['width' => '16.67'],
        ],
        [
            'key' => 'field_venue_amenities',
            'label' => 'Venue Amenities',
            'name' => 'amenities',
            'type' => 'repeater',
            'layout' => 'table',
            'sub_fields' => [
                [
                    'key' => 'field_venue_amenity_icon',
                    'label' => 'Icon',
                    'name' => 'icon',
                    'type' => 'select',
                    'choices' => $fa_icons,
                    'wrapper' => ['width' => '33.33'],
                ],
                [
                    'key' => 'field_venue_amenity_desc',
                    'label' => 'Description',
                    'name' => 'desc',
                    'type' => 'text',
                    'wrapper' => ['width' => '66.67'],
                ],
            ],
        ],
        [
            'key' => 'field_venue_directions',
            'label' => 'Driving Directions',
            'name' => 'directions',
            'type' => 'wysiwyg',
        ],
    ],
    'location' => [
        [
            [
                'param' => 'post_type',
                'operator' => '==',
                'value' => 'venue',
            ],
        ],
    ],
]);

/**
 * Navigation Overrides
 */
acf_add_local_field_group([
    'key' => 'group_navigation_overrides',
    'title' => 'Navigation Overrides',
    'fields' => [
        [
            'key' => 'field_page_title_breadcrumb',
            'label' => 'Breadcrumb Title',
            'name' => 'title-breadcrumb',
            'type' => 'text',
        ],
    ],
    'location' => [
        [
            [
                'param' => 'post_type',
                'operator' => '==',
                'value' => 'page',
            ],
        ],
    ],
]);

/**
 * Common Design Elements - Masthead
 * Applied to all post types
 */
$masthead_dimensions = Helpers::getImageDimensions('masthead', 'array');
foreach (get_post_types() as $post_type) {
    acf_add_local_field_group([
        'key' => 'group_masthead_' . $post_type,
        'title' => 'Common Design Elements',
        'fields' => [
            [
                'key' => 'field_masthead_' . $post_type,
                'label' => 'Masthead',
                'name' => 'masthead',
                'type' => 'repeater',
                'layout' => 'table',
                'sub_fields' => [
                    [
                        'key' => 'field_masthead_img_' . $post_type,
                        'label' => 'Masthead Image',
                        'name' => 'img',
                        'type' => 'image',
                        'instructions' => 'Recommended size: ' . $masthead_dimensions[0] . 'x' . $masthead_dimensions[1],
                        'return_format' => 'id',
                    ],
                    [
                        'key' => 'field_masthead_url_' . $post_type,
                        'label' => 'Masthead Link',
                        'name' => 'url',
                        'type' => 'url',
                    ],
                ],
            ],
        ],
        'location' => [
            [
                [
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => $post_type,
                ],
            ],
        ],
        'menu_order' => 100,
    ]);
}
