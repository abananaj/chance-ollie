<?php
/**
 * Chance Theater WordPress Template
 *
 * @package    wordpress/wordpress
 * @subpackage chancetheater/website
 * @author     Brodkin CyberArts <info@brodkinca.com>
 * @copyright  2015 Brodkin CyberArts
 * @version    Git: $Id: 05117a31fbcb3adca40683e9a21dcd09522233df $
 * @link       http://chancetheater.com/
 */

use BCA\ChanceTheater\Helpers;
use BCA\FontAwesomeIterator\Iterator as FontAwesomeIterator;

$venue_rooms = [
    '' => 'Inherit from Production',
    'offsite' => 'Offsite',
    'cripe' => 'Cripe Stage',
    'fydamar' => 'Fyda-Mar Stage',
    'classroom' => 'Classroom',
    'lobby' => 'Lobby'
];

// Disable Guttenberg block editor for now.
add_filter('use_block_editor_for_post', '__return_false', 5);

/**
 * Prevent wpautop() From Modifying Shortcode Output
 *
 * @param string $content Post content.
 *
 * @return string
 */
function ct_fix_shortcodes(string $content)
{
    $array = [
        '<p>[' => '[',
        ']</p>' => ']',
        ']<br />' => ']'
    ];
    $content = strtr($content, $array);
    return $content;
}

add_filter('the_content', 'ct_fix_shortcodes');

/**
 * Change Posts Menu Label
 *
 * @return null
 */
function ct_change_post_menu_label()
{
    global $menu;
    global $submenu;
    $menu[5][0] = 'Blog';
    $submenu['edit.php'][5][0] = 'Blog Posts';
}
add_action('admin_menu', 'ct_change_post_menu_label');

/**
 * Change Post Object Labels
 *
 * @return null
 */
function ct_change_post_object_label()
{
    global $wp_post_types;
    $labels = &$wp_post_types['post']->labels;
    $labels->name = 'Blog Posts';
}
add_action('init', 'ct_change_post_object_label');

/**
 * Update TinyMCE editor layout to include styleselect
 *
 * @param array $buttons Array of pre-defined buttons.
 *
 * @return array
 */
function ct_mce_buttons_2(array $buttons)
{
    array_unshift($buttons, 'styleselect');

    return $buttons;
}
add_filter('mce_buttons_2', 'ct_mce_buttons_2');

/**
 * Populate TinyMCE editor styleselect menu
 *
 * @param array $init_array Array of pre-defined TinyMCE options.
 *
 * @return array
 */
function ct_mce_before_init_insert_formats(array $init_array)
{
    // Define the style_formats array.
    $style_formats = [
        // Each array child is a format with it's own settings.
        [
            'title' => 'Blockquote',
            'block' => 'blockquote'
        ],
        [
            'title' => 'Blockquote Quote',
            'block' => 'p',
            // 'selector' => 'p.quote',
            'classes' => 'quote'
        ],
        [
            'title' => 'Blockquote Citation',
            'block' => 'small',
            'classes' => 'cite'
        ],
        [
            'title' => 'Color (Muted)',
            'inline' => 'span',
            'classes' => 'text-muted'
        ],
        [
            'title' => 'Color (Brand)',
            'inline' => 'span',
            'classes' => 'text-primary'
        ],
        [
            'title' => 'Color (Success)',
            'inline' => 'span',
            'classes' => 'text-success'
        ],
        [
            'title' => 'Color (Info)',
            'inline' => 'span',
            'classes' => 'text-info'
        ],
        [
            'title' => 'Color (Warning)',
            'inline' => 'span',
            'classes' => 'text-warning'
        ],
        [
            'title' => 'Color (Danger)',
            'inline' => 'span',
            'classes' => 'text-danger'
        ],
        [
            'title' => 'Fine Print',
            'inline' => 'small'
        ],
        [
            'title' => 'Lead Text',
            'selector' => 'p',
            'classes' => 'lead'
        ],
        [
            'title' => 'Open in Popup',
            'selector' => 'a',
            'classes' => 'popup'
        ],
    ];

    // Insert the array, JSON ENCODED, into 'style_formats'.
    $init_array['style_formats'] = json_encode($style_formats);

    return $init_array;
}
add_filter('tiny_mce_before_init', 'ct_mce_before_init_insert_formats');

/**
 * Add theme classes to avatars
 *
 * @param string $class Current class value.
 *
 * @return string       Updated class value.
 */
function ct_change_avatar_css(string $class)
{
    $class = str_replace("class='avatar", "class='avatar img-responsive img-circle", $class);

    return $class;
}
add_filter('get_avatar', 'ct_change_avatar_css');

/**
 * Register Custom Post Types
 *
 * @return null
 */
function ct_register_post_types()
{
    register_post_type(
        'ct-artist',
        [
            'labels' => [
                'name' => __('Artists'),
                'singular_name' => __('Artist')
            ],
            'public' => true,
            'show_ui' => true,
            'has_archive' => false,
            'rewrite' => ['slug' => 'artists'],
            'supports' => ['title', 'editor', 'excerpt', 'thumbnail', 'page-attributes']
        ]
    );

    register_post_type(
        'ct-event',
        [
            'labels' => [
                'name' => __('Events'),
                'singular_name' => __('Event')
            ],
            'public' => true,
            'has_archive' => true,
            'rewrite' => ['slug' => 'events'],
            'supports' => ['title', 'thumbnail', 'excerpt']
        ]
    );

    register_post_type(
        'ct-kiosk',
        [
            'labels' => [
                'name' => __('Kiosk Screens'),
                'singular_name' => __('Kiosk Screen')
            ],
            'public' => true,
            'has_archive' => false,
            'rewrite' => ['slug' => 'kiosk'],
            'supports' => ['title', 'editor']
        ]
    );

    register_post_type(
        'ct-production',
        [
            'labels' => [
                'name' => __('Productions'),
                'singular_name' => __('Production')
            ],
            'public' => true,
            'has_archive' => false,
            'rewrite' => ['slug' => 'production'],
            'supports' => ['title', 'editor', 'thumbnail', 'excerpt', 'comments']
        ]
    );

    register_post_type(
        'ct-production-role',
        [
            'labels' => [
                'name' => __('Production Credits'),
                'singular_name' => __('Production Credit')
            ],
            'public' => false,
            'show_ui' => true,
            'has_archive' => false,
            'rewrite' => ['slug' => 'role'],
            'supports' => ['thumbnail', 'page-attributes']
        ]
    );

    register_post_type(
        'ct-staff',
        [
            'labels' => [
                'name' => __('Staff'),
                'singular_name' => __('Staff'),
                'add_new_item' => __('Add Staff Member')
            ],
            'public' => true,
            'has_archive' => false,
            'rewrite' => ['slug' => 'staff'],
            'supports' => ['title', 'thumbnail', 'page-attributes', 'excerpt']
        ]
    );

    register_post_type(
        'ct-supporter',
        [
            'labels' => [
                'name' => __('Supporters'),
                'singular_name' => __('Supporter')
            ],
            'public' => true,
            'has_archive' => false,
            'rewrite' => ['slug' => 'community'],
            'supports' => ['title','editor', 'thumbnail', 'excerpt']
        ]
    );

    register_post_type(
        'ct-venue',
        [
            'labels' => [
                'name' => __('Venues'),
                'singular_name' => __('Venue')
            ],
            'public' => true,
            'has_archive' => false,
            'rewrite' => ['slug' => 'venue'],
            'supports' => ['title', 'editor', 'thumbnail']
        ]
    );
}
add_action('init', 'ct_register_post_types');

/**
 * Register taxonomies.
 *
 * @return void
 */
function ct_register_taxonomies()
{
    register_taxonomy(
        'season',
        ['ct-production'],
        [
            'labels' => [
                'name'                       => 'Seasons',
                'singular_name'              => 'Season',
                'all_items'                  => __('All Seasons'),
                'parent_item'                => null,
                'parent_item_colon'          => null,
                'edit_item'                  => __('Edit Season'),
                'update_item'                => __('Update Season'),
                'add_new_item'               => __('Add New Season'),
                'new_item_name'              => __('New Season Year'),
                'separate_items_with_commas' => __('Enter season year'),
                'add_or_remove_items'        => __('Add or remove production from season'),
                'choose_from_most_used'      => __('Choose from the most used seasons'),
                'not_found'                  => __('No seasons found.'),
                'menu_name'                  => __('Seasons'),
            ],
            'hierarchical' => true,
            'public' => true,
            'show_tagcloud' => false,
            'query_var' => true,
            'rewrite' => ['slug' => 'season'],
        ]
    );

    register_taxonomy(
        'event-type',
        ['ct-event'],
        [
            'labels' => [
                'name'                       => 'Event Categories',
                'singular_name'              => 'Event Category',
                'all_items'                  => __('All Event Categories'),
                'parent_item'                => null,
                'parent_item_colon'          => null,
                'edit_item'                  => __('Edit Event Categories'),
                'update_item'                => __('Update Event Categories'),
                'add_new_item'               => __('Add New Event Category'),
                'new_item_name'              => __('New Event Category'),
                'separate_items_with_commas' => __('Enter new event type'),
                'add_or_remove_items'        => __('Add or remove types from event'),
                'choose_from_most_used'      => __('Choose from the most used event categories'),
                'not_found'                  => __('No event categories found.'),
                'menu_name'                  => __('Categories'),
            ],
            'hierarchical' => false,
            'public' => true,
            'show_tagcloud' => false,
            'query_var' => true,
            'rewrite' => ['slug' => 'event-cat'],
        ]
    );

    register_taxonomy(
        'ct-board-positions',
        ['ct-supporter'],
        [
            'labels' => [
                'name'                       => 'Board Positions',
                'singular_name'              => 'Board Position',
                'all_items'                  => __('All Positions'),
                'parent_item'                => null,
                'parent_item_colon'          => null,
                'edit_item'                  => __('Edit Board Position'),
                'update_item'                => __('Update Board Position'),
                'add_new_item'               => __('Add New Board Position'),
                'new_item_name'              => __('New Board Position'),
                'separate_items_with_commas' => __('Enter new board position'),
                'add_or_remove_items'        => __('Add or remove board positions'),
                'choose_from_most_used'      => __('Choose from existing board positions'),
                'not_found'                  => __('No board positions found.'),
                'menu_name'                  => __('Board Positions'),
            ],
            'hierarchical' => false,
            'public' => true,
            'show_tagcloud' => false,
            'query_var' => false,
            'capabilities' => ['manage_terms', 'edit_terms', 'delete_terms']
        ]
    );

    register_taxonomy(
        'ct-supporter-level',
        ['ct-supporter'],
        [
            'labels' => [
                'name'                       => 'Levels of Support',
                'singular_name'              => 'Level of Support',
                'all_items'                  => __('All Levels'),
                'parent_item'                => null,
                'parent_item_colon'          => null,
                'edit_item'                  => __('Edit Levels of Support'),
                'update_item'                => __('Update Levels of Support'),
                'add_new_item'               => __('Add New Level of Support'),
                'new_item_name'              => __('New Level of Support'),
                'separate_items_with_commas' => __('Enter new level of support'),
                'add_or_remove_items'        => __('Add or remove levels of support'),
                'choose_from_most_used'      => __('Choose from the most used levels of support'),
                'not_found'                  => __('No levels of support found.'),
                'menu_name'                  => __('Levels of Support'),
            ],
            'hierarchical' => false,
            'public' => true,
            'show_tagcloud' => false,
            'query_var' => true,
            'rewrite' => ['slug' => 'event-cat'],
            'capabilities' => ['manage_terms', 'edit_terms', 'delete_terms']
        ]
    );
}
add_action('init', 'ct_register_taxonomies', 0);

/**
 * Register Meta Boxes
 *
 * @param array $meta_boxes Pre-defined meta boxes.
 *
 * @return array            Transformed array of updated meta boxes.
 */

function ct_register_meta_boxes(array $meta_boxes)
{
    global $venue_rooms;

    $fa_iterator = new FontAwesomeIterator(
        TEMPLATEPATH.'/assets/vendor/font-awesome/css/font-awesome.css'
    );

    $fa_icons = [];
    foreach ($fa_iterator as $icon) {
        $fa_icons[$icon->class] = $icon->name;
    }

    // Define artists.
    $meta_boxes[] = [
       'title' => 'Artist Profile',
       'pages' => 'ct-artist',
       'fields' => [
           [
               'id' => 'title-professional',
               'name' => 'Professional Profile',
               'type' => 'title'
           ],
           [
               'id' => 'profession',
               'name' => 'Profession',
               'type' => 'text'
           ],
           [
               'id' => 'resident',
               'name' => 'Resident Artist?',
               'type' => 'checkbox',
               'default' => 0
           ],
           [
               'id' => 'title-contact',
               'name' => 'Contact Information',
               'type' => 'title'
           ],
           [
               'id' => 'website',
               'name' => 'Website',
               'type' => 'url'
           ],
           [
               'id' => 'facebook',
               'name' => 'Facebook',
               'type' => 'url'
           ],
           [
               'id' => 'twitter',
               'name' => 'Twitter',
               'type' => 'url'
           ],
           [
               'id' => 'linkedin',
               'name' => 'LinkedIn',
               'type' => 'url'
           ],
       ]
    ];

    // Define events.
    $meta_boxes[] = [
       'title' => 'Event Details',
       'pages' => 'ct-event',
       'fields' => [
           [
               'id' => 'date-start',
               'name' => 'Event Start Date/Time',
               'type' => 'datetime_unix',
               'cols' => 6
           ],
           [
               'id' => 'date-end',
               'name' => 'Event End Date/Time',
               'type' => 'datetime_unix',
               'cols' => 6
           ],
           [
               'id' => 'production',
               'name' => 'Production (if applicable)',
               'desc'=> 'When chosen the production details and a link to the production\'s page will appear on the event page.',
               'type' => 'post_select',
               'use_ajax' => true,
               'query' => ['post_type' => 'ct-production', 'posts_per_page' => 8],
               'cols' => 4
           ],
           [
               'id' => 'venue',
               'name' => 'Venue/Location',
               'desc'=> 'Required if no production selected.',
               'type' => 'post_select',
               'use_ajax' => true,
               'query' => ['post_type' => 'ct-venue', 'posts_per_page' => 8],
               'cols' => 4
           ],
           [
               'id' => 'venue-room',
               'name' => 'BATAC Room',
               'desc'=> 'Required',
               'type' => 'select',
               'cols'=> 4,
               'options' => $venue_rooms
           ],
           [
               'id' => 'is-promo',
               'name' => 'This event is promotional',
               'type' => 'checkbox',
               'cols' => 12
           ],
           [
               'id' => 'ticketing-link',
               'name' => 'Buy Tickets Link',
               'type' => 'text_url',
               'cols'=> 12
           ],
           [
               'id' => 'description',
               'name' => 'Event Description (optional)',
               'desc'=> 'Will appear above production details when a production has been selected.',
               'type' => 'wysiwyg'
           ],
       ]
    ];

    // Define posts.
    $meta_boxes[] = [
       'title' => 'Assign Related Posts',
       'pages' => 'post',
       'fields' => [
           [
               'id' => 'production',
               'name' => 'Related Production',
               'type' => 'post_select',
               'use_ajax' => true,
               'query' => [
                   'post_type' => 'ct-production',
                   'posts_per_page' => 8
               ],
               'cols'=> 12,
               'allow_none' => true
           ]
       ]
    ];

    // Define productions.
    $meta_boxes[] = [
       'title' => 'Production Details',
       'pages' => 'ct-production',
       'fields' => [
           [
               'id' => 'venue',
               'name' => 'Venue',
               'type' => 'post_select',
               'use_ajax' => true,
               'query' => ['post_type' => 'ct-venue', 'posts_per_page' => 8],
               'cols'=> 7
           ],
           [
               'id' => 'venue-room',
               'name' => 'BATAC Room',
               'type' => 'select',
               'cols'=> 5,
               'options' => array_slice($venue_rooms, 1)
           ],
           [
               'id' => 'ticketing-link',
               'name' => 'Buy Tickets URL',
               'type' => 'text_url',
               'cols'=> 12
           ],
           [
               'id' => 'video-trailer-url',
               'name' => 'Video Trailer URL',
               'type' => 'text_url',
               'cols'=> 12
           ],
           [
               'id' => 'runtime',
               'name' => 'Approx. Length',
               'desc' => '(in minutes)',
               'type' => 'text',
               'cols'=> 3
           ],
           [
               'id' => 'date-opening',
               'name' => 'Opening Date',
               'desc' => 'mm/dd/yyyy',
               'type' => 'date_unix',
               'cols'=> 3
           ],
           [
               'id' => 'date-closing',
               'name' => 'Closing Date',
               'desc' => 'mm/dd/yyyy',
               'type' => 'date_unix',
               'cols'=> 3
           ],

           [
               'id' => 'intermissions',
               'name' => 'Intermissions',
               'desc' => '.',
               'type' => 'select',
               'cols'=> 3,
               'options' => [
                   0 => 'None',
                   1 => 'One',
                   2 => 'Two',
                   3 => 'Three'
               ],
               'default' => 1
           ],
           [
               'id' => 'hide-subscribe',
               'name' => 'Hide Subscribe Button?',
               'type' => 'checkbox',
               'cols'=> 12
           ],

           [
               'id' => 'tabs',
               'name' => 'Tabs',
               'type' => 'group',
               'repeatable' => true,
               'cols'=> 12,
               'fields'=> [
                   [
                       'id' => 'title',
                       'name' => 'Tab Title',
                       'type' => 'text',
                       'cols'=> 8
                   ],
                   [
                       'id' => 'slug',
                       'name' => 'Slug',
                       'type' => 'text',
                       'cols'=> 4
                   ],
                   [
                       'id' => 'content',
                       'name' => 'Tab Content',
                       'type' => 'wysiwyg',
                       'cols'=> 12,
                       'options' => [
                           'textarea_rows' => '5'
                       ]
                   ]
               ]
           ],
           [
               'id' => 'notes',
               'name' => 'Special Notes',
               'type' => 'group',
               'repeatable' => true,
               'cols'=> 12,
               'fields'=> [
                   [
                       'id' => 'icon',
                       'name' => 'Icon',
                       'type' => 'select',
                       'options' => $fa_icons,
                       'cols'=> 2
                   ],
                   [
                       'id' => 'desc',
                       'name' => 'Description',
                       'type' => 'text',
                       'cols'=> 6
                   ],
                   [
                       'id' => 'link',
                       'name' => 'Link to Page',
                       'type' => 'post_select',
                       'use_ajax' => true,
                       'query' => ['posts_per_page' => 8],
                       'cols'=> 4
                   ]
               ]
           ],
           [
               'id' => 'byline',
               'name' => 'Bylines',
               'type' => 'group',
               'repeatable' => true,
               'cols'=> 12,
               'fields'=> [
                   [
                       'id' => 'role',
                       'name' => 'Credited Roles',
                       'type' => 'text',
                       'cols'=> 4
                   ],
                   [
                       'id' => 'name',
                       'name' => 'Name',
                       'type' => 'text',
                       'cols'=> 8
                   ]
               ]
           ]
       ]
    ];
    $meta_boxes[] = [
       'title' => 'Awards and Recognition',
       'pages' => 'ct-production',
       'fields' => [
           [
               'id' => 'awards',
               'name' => 'Awards and Honors',
               'type' => 'group',
               'repeatable' => true,
               'repeatable_max' => 4,
               'cols'=> 12,
               'fields' => [
                   [
                       'id' => 'award-headline',
                       'name' => 'Award Text',
                       'type' => 'text',
                       'cols'=> 6,
                   ],
                   [
                       'id' => 'award-text',
                       'name' => 'Citation',
                       'type' => 'text',
                       'cols'=> 6,
                   ]
               ]
           ],
           [
               'id' => 'quotes',
               'name' => 'Quotes',
               'type' => 'group',
               'repeatable' => true,

               'cols'=> 12,
               'fields' => [
                   [
                       'id' => 'quote-text',
                       'name' => 'Quote',
                       'type' => 'textarea',
                       'cols'=> 12,
                   ],
                   [
                       'id' => 'quote-cite',
                       'name' => 'Citation',
                       'type' => 'text',
                       'cols'=> 6,
                   ],
                   [
                       'id' => 'quote-link',
                       'name' => 'Link to Blog Post',
                       'type' => 'post_select',
                       'use_ajax' => true,
                       'query' => ['post_type' => 'post', 'posts_per_page' => 8],
                       'cols'=> 6
                   ]
               ]
           ]
       ]
    ];
    $meta_boxes[] = [
       'title' => 'Productions Widget',
       'pages' => 'ct-production',
       'fields' => [
           [
               'id' => 'widget-content',
               'name' => 'Additional Widget Content',
               'type' => 'wysiwyg',
               'cols' => 12
           ]
       ]
    ];
    $header_dimensions = Helpers::getImageDimensions(
        'production-season-header',
        'array'
    );
    $meta_boxes[] = [
       'title' => 'Season Display',
       'pages' => 'ct-production',
       'fields' => [
           [
               'id' => 'img-season-header',
               'name' => 'Production Header',
               'type' => 'image',
               'cols' => 12,
               'size' => 'width='.$header_dimensions[0].'&height='.$header_dimensions[1],
               'show_size' => true
           ]
       ]
    ];

    // Define production roles.
    $meta_boxes[] = [
       'title' => 'Assign Artist to Production',
       'pages' => 'ct-production-role',
       'fields' => [
           [
               'id' => 'production',
               'name' => 'Production',
               'type' => 'post_select',
               'use_ajax' => true,
               'query' => ['post_type' => 'ct-production', 'posts_per_page' => 8],
               'cols'=> 6
           ],
           [
               'id' => 'artist',
               'name' => 'Artist Name',
               'type' => 'post_select',
               'use_ajax' => true,
               'query' => [
                   'post_type' => ['ct-artist', 'ct-supporter'],
                   'posts_per_page' => 8],
               'cols'=> 6
           ],
           [
               'id'      => 'role-group',
               'name'    => 'Group',
               'type'    => 'select',
               'options' => [
                   'playwright' => 'Playwright',
                   'actor' => 'Actors',
                   'director' => 'Director',
                   'choreographer' => 'Choreographer',
                   'designer' => 'Designers',
                   'producer' => 'Producers',
                   'other' => 'Others'
               ],
               'cols'=> 6
           ],
           [
               'id' => 'role',
               'name' => 'Credited Role',
               'type' => 'text',
               'cols'=> 6
           ]
       ]
    ];

    // Define staff.
    $meta_boxes[] = [
       'title' => 'Staff Profile',
       'pages' => 'ct-staff',
       'fields' => [
           [
               'id' => 'title',
               'name' => 'Job Title',
               'type' => 'text',
               'use_ajax' => true,
               'cols'=> 12
           ]
       ]
    ];

    // Define supporters.
    $meta_boxes[] = [
       'title' => 'Supporter Profile',
       'pages' => 'ct-supporter',
       'fields' => [
           [
               'id' => 'supporter-type',
               'name' => 'Supporter Type',
               'type' => 'radio',
               'default' => 'individual',
               'options' => [
                   'individual' => 'Individual',
                   'institutional' => 'Institutional'
               ]
           ],
           [
               'id' => 'donor-level',
               'name' => 'Donor Contribution Level',
               'type' => 'taxonomy_select',
               'taxonomy' => 'ct-supporter-level',
               'allow_none' => true
           ],
           [
               'id' => 'website',
               'name' => 'Website',
               'type' => 'text_url'
           ]
       ]
    ];
    $meta_boxes[] = [
       'title' => 'Board of Directors',
       'pages' => 'ct-supporter',
       'fields' => [
           [
               'id' => 'board-position',
               'name' => 'Board Member Position',
               'type' => 'taxonomy_select',
               'taxonomy' => 'ct-board-positions',
               'allow_none' => true
           ],
       ]
    ];
    $meta_boxes[] = [
       'title' => 'Display Settings',
       'pages' => 'ct-supporter',
       'fields' => [
           [
               'id' => 'display-donors',
               'name' => 'Display in list of donors?',
               'type' => 'checkbox'
           ],
           [
               'id' => 'display-sidebar',
               'name' => 'Display in supporters widget?',
               'type' => 'checkbox',
               'default' => true
           ],
       ]
    ];

    // Define venues.
    $meta_boxes[] = [
       'title' => 'Venue Details',
       'pages' => 'ct-venue',
       'fields' => [
           [
               'id' => 'street-address',
               'name' => 'Street Address',
               'type' => 'text'
           ],
           [
               'id' => 'locality',
               'name' => 'City',
               'type' => 'text',
               'cols'=> 6
           ],
           [
               'id' => 'region',
               'name' => 'State',
               'type' => 'text',
               'default'=> 'CA',
               'cols'=> 2
           ],
           [
               'id' => 'postal-code',
               'name' => 'Zip Code',
               'type' => 'text',
               'cols'=> 2
           ],
           [
               'id' => 'country-name',
               'name' => 'Country',
               'type' => 'text',
               'readonly'=> true,
               'default'=> 'US',
               'cols'=> 2
           ],
           [
               'id' => 'amenities',
               'name' => 'Venue Amenities',
               'type' => 'group',
               'repeatable' => true,
               'cols'=> 12,
               'fields'=> [
                   [
                       'id' => 'icon',
                       'name' => 'Icon',
                       'type' => 'select',
                       'options' => $fa_icons,
                       'cols'=> 4
                   ],
                   [
                       'id' => 'desc',
                       'name' => 'Description',
                       'type' => 'text',
                       'cols'=> 8
                   ]
               ]
           ],
           [
               'id' => 'directions',
               'name' => 'Driving Directions',
               'type' => 'wysiwyg'
           ],
       ]
    ];

    // Define pages.
    $meta_boxes[] = [
       'title' => 'Navigation Overrides',
       'pages' => 'page',
       'fields' => [
           [
               'id' => 'title-breadcrumb',
               'name' => 'Breadcrumb Title',
               'type' => 'text'
           ]
       ]
    ];

    // Define universal fields.
    $masthead_dimensions = Helpers::getImageDimensions(
        'masthead',
        'array'
    );
    foreach (get_post_types() as $post_type) {
        $meta_boxes[] = [
          'title' => 'Common Design Elements',
          'pages' => $post_type,
          'fields' => [
              [
                  'id' => 'masthead',
                  'name' => 'Masthead',
                  'type' => 'group',
                  'repeatable' => true,
                  'fields' => [
                      [
                          'id' => 'img',
                          'name' => 'Masthead Image',
                          'type' => 'image',
                          'size' => 'width='.$masthead_dimensions[0].'&height='.$masthead_dimensions[1],
                          'show_size' => true
                      ],
                      [
                          'id' => 'url',
                          'name' => 'Masthead Link',
                          'type' => 'text_url'
                      ],
                  ]
              ],
          ]
        ];
    }

    return $meta_boxes;
}
    add_filter('cmb_meta_boxes', 'ct_register_meta_boxes');

/**
 * Override Title for Custom Post Types
 *
 * @param string $post_title Current post title.
 *
 * @return string $post_title Updated post title.
 */
function ct_set_post_title(string $post_title)
{
    if (!isset($_POST['post_type'])) {
        return $post_title;
    }

    if ($_POST['post_type'] == 'ct-production-role') {
        $post_title = get_the_title($_POST['production']['cmb-field-0']);
        $post_title.= '/';
        $post_title.= get_the_title($_POST['artist']['cmb-field-0']);
    } elseif ($_POST['post_type'] == 'ct-event' && empty($post_title)) {
        $post_title = get_the_title($_POST['production']['cmb-field-0']);
    }

    return $post_title;
}
    add_filter('title_save_pre', 'ct_set_post_title');

/**
 * Disable Post Row Actions for Custom Post Types
 *
 * @param array   $actions Pre-defined options for post row.
 * @param WP_Post $post    Object for current post.
 *
 * @return array           Updated options for post row.
 */
function ct_remove_row_actions(array $actions, WP_Post $post)
{
    global $current_screen;

    if (@$current_screen->post_type !== 'ct-production-role') {
        return $actions;
    }

    unset($actions['view']);
    unset($actions['inline hide-if-no-js']);

    return $actions;
}
    add_filter('post_row_actions', 'ct_remove_row_actions', 10, 2);

/**
 * Define navigation menus.
 *
 * @return void
 */
function ct_nav_menus()
{
    // Register wp_nav_menu() menus.
    $nav_menus = [
    'primary_navigation' => __('Primary Navigation', 'roots'),
    'footer_one' => 'Footer One',
    'footer_two' => 'Footer Two',
    'footer_three' => 'Footer Three',
    'footer_four' => 'Footer Four',
    'kiosk_main' => 'Kiosk Sidebar'
    ];

    // Add a Nav Menu for Each Post Type.
    $post_types_nav_disabled = [
    'ct-production-role',
    'page'
    ];
    foreach (Helpers::getPostTypesAssoc() as $id => $name) {
        if (!in_array($id, $post_types_nav_disabled)) {
            $nav_menus['subnav-'.$id] = 'Post, '.$name;
        }
    }

    // Add a Nav Menu for Each Top Level Page.
    $pages = get_pages(['parent'=> 0]);
    foreach ($pages as $page) {
        $nav_menus['subnav-page-'.$page->ID] = 'Section, '.$page->post_title;
    }

    register_nav_menus($nav_menus);
}
    add_action('init', 'ct_nav_menus', 999);

/**
 * Add/remove columns for events in admin view.
 *
 * @param array $columns Pre-defined columns for view.
 *
 * @return array          Transformed columns for view.
 */
function ct_admin_event_columns(array $columns)
{
    $columns['date-start'] = __('Event Date', 'roots');
    $columns['time-start'] = __('Event Time', 'roots');
    unset($columns['date']);

    return $columns;
}
    add_filter('manage_edit-ct-event_columns', 'ct_admin_event_columns');

/**
 * Define custom columns for events in admin view.
 *
 * @param string|integer $column  ID of column.
 * @param string|integer $post_id ID of post being affected.
 *
 * @return void
 */
function ct_admin_event_custom_column($column, $post_id)
{
    $time = get_post_meta($post_id, 'date-start', true);
    $date_start = new DateTime('@'.$time);
    $date_start->setTimezone(Helpers::getTimezone());

    if ($column === 'date-start') {
        echo $date_start->format('m/d/Y');
    } elseif ($column === 'time-start') {
        echo $date_start->format('g:i a');
    }
}
    add_action('manage_ct-event_posts_custom_column', 'ct_admin_event_custom_column', 10, 2);

/**
 * Define sorting for custom columns in admin events view.
 *
 * @param array $columns Pre-defined columns.
 *
 * @return array          Transformed columns.
 */
function ct_admin_event_sortable_columns(array $columns)
{
    $columns['date-start'] = 'date-start';
    $columns['time-start'] = 'time-start';

    return $columns;
}
    add_filter('manage_edit-ct-event_sortable_columns', 'ct_admin_event_sortable_columns');

/**
 * Override DB Request Defaults
 *
 * @param array $vars Request variables.
 *
 * @return array      Transformed request variables.
 */
function ct_request(array $vars)
{
    // Force events to order by date.
    if (isset($vars['post_type']) && $vars['post_type'] === 'ct-event') {
        return array_merge($vars, [
            'meta_key' => 'date-start',
            'orderby' => 'meta_value_num'
        ]);
    } elseif (isset($vars['season'])) {
        return array_merge($vars, [
            'meta_key' => 'date-opening',
            'orderby' => 'meta_value_num',
            'order' => 'ASC'
        ]);
    }

    return $vars;
}
    add_filter('request', 'ct_request');

/**
 * Whitelist custom query variables.
 *
 * @param array $vars Pre-defined variables.
 *
 * @return array      Transformed variables.
 */
function ct_query_vars(array $vars)
{
    $vars[] = 'cal_month';
    $vars[] = 'cal_year';
    $vars[] = 'production';
    $vars[] = 'referrer';
    $vars[] = 'kiosk';

    return $vars;
}
    add_filter('query_vars', 'ct_query_vars');

/**
 * Append referer as query string to generated URLs.
 *
 * URL will only be transformed when the ID of the post being linked to is
 * different than that of the current post.
 *
 * @param string  $url  URL to post.
 * @param WP_Post $post Post object for URL being generated.
 *
 * @return string Transformed URL to post.
 */
function ct_append_qs_referrer(string $url, WP_Post $post)
{
    if ($post->ID !== get_the_ID()) {
        return add_query_arg('referrer', get_the_ID(), $url);
    }

    return $url;
}
    add_filter('post_type_link', 'ct_append_qs_referrer', 10, 3);

/**
 * Remove undesired meta boxes from select post types.
 *
 * @return void
 */
function ct_remove_metabox()
{
    remove_meta_box('tagsdiv-ct-board-positions', 'ct-supporter', 'side');
    remove_meta_box('tagsdiv-ct-supporter-level', 'ct-supporter', 'side');
}
    add_action('admin_menu', 'ct_remove_metabox');

/**
 * Use ellipsis to indicate that an excerpt is truncated.
 *
 * @return string String to be appended to excerpts.
 */
function ct_excerpt_more()
{
    return '...';
}
    add_filter('excerpt_more', 'ct_excerpt_more');

/**
 * Disable Control of EBS enqueue settings.
 *
 * @return boolean Set to TRUE for custom and FALSE for EBS enqueued.
 */
function ct_ebs_enqueue()
{
    return true;
}
    add_filter('ebs_custom_option', 'ct_ebs_enqueue');

/**
 * Add custom stylesheet for login page.
 *
 * @return void
 */
function ct_enqueue_scripts()
{
    echo '<link rel="stylesheet" href="/app/assets/css/login.min.css" type="text/css" media="all" />';
}
    add_action('login_enqueue_scripts', 'ct_enqueue_scripts');

/**
 * Set custom link for login page logo.
 *
 * @return string URL to which login page logo should be linked.
 */
function ct_login_headerurl()
{
    return get_bloginfo('url');
}
    add_filter('login_headerurl', 'ct_login_headerurl');

/**
 * Set custom title for login page header.
 *
 * @return string Title for login page header.
 */
function ct_login_headertext()
{
    return 'Chance Theater';
}
    add_filter('login_headertext', 'ct_login_headertext');

/**
 * Define custom TinyMCE options.
 *
 * @param array $options Pre-defined TinyMCE options.
 *
 * @return array          Transformed TinyMCE options.
 */
function ct_tiny_mce_before_init(array $options)
{
    $options['apply_source_formatting'] = true;
    $options['cleanup_on_startup'] = true;
    $options['convert_newlines_to_brs'] = true;
    $options['element_format'] = 'html';
    $options['fix_list_elements'] = true;
    $options['indentation'] = '1em';
    $options['paste_auto_cleanup_on_paste'] = true;
    $options['remove_redundant_brs'] = false;
    $options['remove_linebreaks'] = false;
    $options['schema'] = 'html5';
//    $options['valid_styles'] = "{
//        '*' : 'padding-left',
//        'p' : 'text-align, text-decoration'
//    }";
    $options['verify_html'] = true;

    return $options;
}
    add_filter('tiny_mce_before_init', 'ct_tiny_mce_before_init');

/**
 * Define custom HTML wrapper for embeds.
 *
 * @param string $html HTML for embed.
 *
 * @return string       Transformed HTML for embed.
 */
function ct_embed_oembed_html(string $html)
{
    $output = '<figure class="oembed">';

    $css_class = 'oembed';
    if (strpos($html, 'youtube')
        || strpos($html, 'vimeo')
    ) {
        $css_class .= ' oembed-video';
    }

    $output.= '<div class="'.$css_class.'">';
    $output.= $html;
    $output.= '</div>';

    if (strpos($html, 'youtube')) {
        $output.= '<figcaption class="oembed-caption">';
        $output.= '<div class="g-ytsubscribe" data-channel="'.of_get_option('social_id_youtube').'" data-layout="full" data-count="hidden"></div>';
        $output.= '</figcaption>';
    }

    $output .= '</figure>';

    return $output;
}
    add_filter('embed_oembed_html', 'ct_embed_oembed_html');

/**
 * Define enabled Facebook plugin features.
 *
 * @param array $features Pre-defined Facebook plugin features.
 *
 * @return array           Transformed Facebook plugin features.
 */
function ct_facebook_features(array $features)
{
    unset(
        $features['like'],
        $features['share'],
        $features['send'],
        $features['follow'],
        $features['recommendations_bar'],
        $features['comments']
    );

    return $features;
}
    add_filter('facebook_features', 'ct_facebook_features');

/**
 * Define paths to Options Framework definition files.
 *
 * @return array Array of root-relative file paths.
 */
function ct_options_framework_location()
{
    return ['lib/BCA/ChanceTheater/options.php'];
}
    add_filter('options_framework_location', 'ct_options_framework_location');

/**
 * Define custom AJAX response for event info.
 *
 * @return void
 */
function ct_ajax_get_event()
{
    $event_id = @$_POST['id'];
    if (!$event_id) {
        return;
    }

    $post = get_post($event_id);

    if (!$post || get_post_type($post->ID) !== 'ct-event') {
        return;
    }

    if ($post->__get('production')) {
        $production = get_post($post->__get('production'));
        $event['production']['summary'] = $production->post_excerpt;
    }

    $date_start = new DateTime('@'.$post->__get('date-start'));
    $date_start->setTimezone(Helpers::getTimezone());

    $date_end = new DateTime('@'.$post->__get('date-end'));
    $date_end->setTimezone(Helpers::getTimezone());

    $description = $post->description;

    // Append Production Summary to Description.
    if (is_numeric($post->production)) {
        $production = get_post($post->production);
        $production_summary = $production->post_content;
        if ($production->post_excerpt) {
            $production_summary = $production->post_excerpt;
        }

        $description .= ' '.$production_summary;
    }

    $description = strip_tags(do_shortcode($description));

    $event['name'] = $post->post_title;
    $event['summary'] = wp_html_excerpt($description, 200, '...');
    $event['date_start']['date'] = $date_start->format('l, F j, Y');
    $event['date_start']['time'] = $date_start->format('g:ia');
    $event['date_end']['date'] = $date_end->format('l, F j, Y');
    $event['date_end']['time'] = $date_end->format('g:ia');
    $event['permalink'] = @get_permalink($post->ID);

    $event['masthead_url'] = false;
    if ($post->__get('img-masthead')) {
        $masthead_src = wp_get_attachment_image_src(
            $post->__get('img-masthead'),
            'masthead'
        );
        $event['masthead_url'] = @array_shift($masthead_src);
    } elseif ($post->__get('production')) {
        $masthead_id = $production->__get('img-masthead');
        $masthead_src = wp_get_attachment_image_src(
            $masthead_id,
            'masthead'
        );
        $event['masthead_url'] = @array_shift($masthead_src);
    }

    header('Content-type: application/json');
    echo json_encode($event);

    // This is required to return a proper result.
    die();
}
    add_action('wp_ajax_nopriv_get_event', 'ct_ajax_get_event');
    add_action('wp_ajax_get_event', 'ct_ajax_get_event');

/**
 * Define custom AJAX response for event info.
 *
 * @return void
 */
function ct_ajax_xibo_events()
{
    $days = 7;
    if (isset($_POST['timespan_days'])) {
        $days = (int) $_POST['timespan_days'];
    }

    $query = new WP_Query([
        'post_type' => 'ct-event',
        'posts_per_page' => 20,
        'meta_key' => 'date-start',
        'orderby' => 'meta_value_num',
        'order' => 'ASC',
        'meta_query' => [
        ['key'=> 'date-start', 'value'=> time(), 'compare'=> '>='],
        ['key'=> 'date-start', 'value'=> (time() + ($days * 86400)), 'compare'=> '<=']
        ]
    ]);

    $events = [];

    foreach ($query->posts as $post) {
        $event = [
            'id' => $post->ID,
            'title' => $post->post_title,
            'description' => $post->description,
            'thumbnail' => get_the_post_thumbnail_url($post),
            'event_start' => $post->__get('date-start'),
            'event_end' => $post->__get('date-end'),
            'location' => $post->__get('venue-room'),
        ];

        if (isset($post->venue)) {
            $event['venue'] = [
            'id' => (int) $post->venue,
            'name' => get_the_title($post->venue),
            ];
        }

        if (isset($post->production)) {
            $production = get_post($post->production);

            $event['production'] = [
            'id' => (int) $post->production,
            'name' => $production->post_title,
            'intermissions' => $production->intermissions,
            ];

            if (!isset($event['location'])) {
                $event['location'] = $production->__get('venue-room');
            }

            if (!isset($post->venue)) {
                $event['venue'] = [
                'id' => (int) $production->venue,
                'name' => get_the_title($production->venue),
                ];
            }
        }

        $events[] = $event;
    }

    header('Content-type: application/json');
    echo json_encode($events);

    // This is required to return a proper result.
    wp_die();
}
    add_action('wp_ajax_nopriv_xibo_events', 'ct_ajax_xibo_events');
    add_action('wp_ajax_xibo_events', 'ct_ajax_xibo_events');

/**
 * Define custom AJAX response for event producers.
 *
 * @return void
 */
function ct_ajax_xibo_credits()
{
    global $venue_rooms;

    $days = 7;
    if (isset($_POST['timespan_days'])) {
        $days = (int) $_POST['timespan_days'];
    }

    $query = new WP_Query([
        'post_type' => 'ct-event',
        'posts_per_page' => 20,
        'meta_key' => 'date-start',
        'orderby' => 'meta_value_num',
        'order' => 'ASC',
        'meta_query' => [
        ['key'=> 'date-start', 'value'=> (time() - (1 * 86400)), 'compare'=> '>='],
        ['key'=> 'date-start', 'value'=> (time() + ($days * 86400)), 'compare'=> '<=']
        ]
    ]);

    $events = [];

    foreach ($query->posts as $post) {
        $event_start = new DateTime('@'.$post->__get('date-start'));
        $event_start->setTimezone(Helpers::getTimezone());

        $event_room = (string) $post->__get('venue-room');

        $event = [
            'event_id' => $post->ID,
            'event_title' => $post->post_title,
            'event_start' => $post->__get('date-start'),
            'event_start_time_friendly' => $event_start->format('g:i').substr($event_start->format('a'), 0, 1),
            'event_end' => $post->__get('date-end'),
            'location' => $event_room,
            'location_friendly' => $venue_rooms[$event_room]
        ];

        if (isset($post->venue)) {
            $event['venue'] = [
            'id' => (int) $post->venue,
            'name' => get_the_title($post->venue),
            ];
        }

        if (isset($post->production)) {
            $production = get_post($post->production);

            if (empty($event['location'])) {
                $event['location'] = $production->__get('venue-room');
                $event['location_friendly'] = $venue_rooms[$production->__get('venue-room')];
            }

            if (!isset($post->venue)) {
                $event['venue'] = [
                'id' => (int) $production->venue,
                'name' => get_the_title($production->venue),
                ];
            }

            $query = new WP_Query([
                'post_type' => 'ct-production-role',
                'posts_per_page' => 50,
                'orderby' => 'menu_order',
                'order' => 'ASC',
                'meta_query' => [
                ['key'=> 'role-group', 'value'=> 'producer'],
                ['key'=> 'production', 'value'=> $post->production]
                ]
            ]);

            if (count($query->posts) > 0) {
                foreach ($query->posts as $credit) {
                    $event['credit_role'] = $credit->role;
                    $event['credit_name'] = get_the_title($credit->artist);
                    $event['credit_order'] = $credit->menu_order;

                    $events[] = $event;
                }
            } else {
                $events[] = $event;
            }
        }
    }

    header('Content-type: application/json');
    echo json_encode($events);

    // This is required to return a proper result.
    wp_die();
}
add_action('wp_ajax_nopriv_xibo_credits', 'ct_ajax_xibo_credits');
add_action('wp_ajax_xibo_credits', 'ct_ajax_xibo_credits');

/**
 * Conditionally redirect posts.
 *
 * @return void
 */
function ct_template_redirect()
{
    $post = get_post();

    if (!is_archive()
        && get_post_type() === 'ct-event'
        && isset($post->production)
        && $post->__get('ticketing-link')
        && is_numeric($post->production)
    ) {
        wp_redirect($post->__get('ticketing-link'));
    } elseif (!is_archive()
        && get_post_type() === 'ct-event'
        && isset($post->production)
        && is_numeric($post->production)
    ) {
        wp_redirect(get_permalink($post->production));
    } elseif (is_tax('season')) {
        // Redirect series if has parent.
        $tax = get_term_by('id', get_queried_object_id(), 'season');
        if (!empty($tax->parent)) {
            wp_redirect(get_term_link($tax->parent, 'season'));
        }
    }
}
    add_action('template_redirect', 'ct_template_redirect');

/**
 * Reinterpret closing date as date @ 22:00pm
 *
 * @param string|integer $timestamp Unix timestamp.
 *
 * @return integer
 */
function ct_cmb_save_date_closing($timestamp)
{
    $datetime = new DateTime('@'.$timestamp);

    if ($datetime) {
        $datetime->modify('+22 hours');

        return $datetime->format('U');
    }

    return $timestamp;
}
    add_filter('sanitize_post_meta_date-closing', 'ct_cmb_save_date_closing');

/**
 * Enable filtering blog posts by production.
 *
 * @param mixed $query Query object.
 *
 * @return WP_Query
 */
function ct_pre_get_posts($query)
{
    global $wp_query;

    if (is_admin() || !$query->is_main_query()) {
        return $query;
    }

    if (is_archive()
        && isset($wp_query->query_vars['production'])
    ) {
        $query->set('meta_key', 'production');
        $query->set(
            'meta_value',
            $wp_query->query_vars['production']
        );
    } elseif (is_tax('season')) {
        $query->set('posts_per_page', 50);
    }

    return $query;
}
    add_action('pre_get_posts', 'ct_pre_get_posts');

/**
 * Restrict Kiosk Pages
 *
 * @return void
 */
function chance_restrict_kiosk()
{
    global $post;
    if ($post->post_type == 'ct-kiosk') {
        // Redirect if not authorized.
        if (!strpos($_SERVER['HTTP_USER_AGENT'], 'Safari') && !current_user_can('edit_posts')) {
            header('HTTP/1.1 401 Unauthorized');
            echo 'Error 401 Unauthorized';
            exit;
        }
    }
}
//add_action('template_redirect', 'chance_restrict_kiosk');

// WP SAML XML Config.
//add_filter('wpsimplesaml_idp_metadata_xml_path', function () {
//    return ABSPATH.'/home/chanceth/sp-metadata.xml';
//});

// WP SAML Attribute Mapping.
add_filter('wpsimplesaml_attribute_mapping', function () {
    return [
       'user_login' => 'uid',
       'user_email' => 'email',
       'first_name' => 'firstName',
       'last_name' => 'lastName'
    ];
});
