<?php 

if( function_exists('acf_add_local_field_group') ):

acf_add_local_field_group(array(
	'key' => 'group_69d9ee6540cad',
	'title' => 'Event Type Details 📅',
	'fields' => array(
		array(
			'key' => 'field_69d9ee654d332',
			'label' => 'Schedule',
			'name' => 'schedule',
			'aria-label' => '',
			'type' => 'wysiwyg',
			'instructions' => 'In addition to Term Description, if needed.',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array(
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'default_value' => '',
			'acfe_wysiwyg_height' => 300,
			'acfe_wysiwyg_max_height' => '',
			'acfe_wysiwyg_valid_elements' => '',
			'acfe_wysiwyg_custom_style' => '',
			'acfe_wysiwyg_disable_wp_style' => 1,
			'acfe_wysiwyg_autoresize' => 0,
			'acfe_wysiwyg_disable_resize' => 0,
			'acfe_wysiwyg_remove_path' => 0,
			'acfe_wysiwyg_menubar' => 0,
			'acfe_wysiwyg_transparent' => 0,
			'acfe_wysiwyg_merge_toolbar' => 0,
			'acfe_wysiwyg_custom_toolbar' => 1,
			'acfe_wysiwyg_toolbar_buttons' => array(
				'acfe_wysiwyg_toolbar_1' => array(
					array(
						'acfe_wysiwyg_toolbar_row' => 'bold',
					),
					array(
						'acfe_wysiwyg_toolbar_row' => 'italic',
					),
					array(
						'acfe_wysiwyg_toolbar_row' => 'bullist',
					),
					array(
						'acfe_wysiwyg_toolbar_row' => 'numlist',
					),
					array(
						'acfe_wysiwyg_toolbar_row' => 'link',
					),
					array(
						'acfe_wysiwyg_toolbar_row' => 'spellchecker',
					),
				),
				'acfe_wysiwyg_toolbar_2' => '',
				'acfe_wysiwyg_toolbar_3' => '',
				'acfe_wysiwyg_toolbar_4' => '',
			),
			'allow_in_bindings' => 1,
			'tabs' => 'all',
			'toolbar' => 'full',
			'media_upload' => 0,
			'delay' => 0,
			'acfe_wysiwyg_auto_init' => 0,
			'acfe_wysiwyg_min_height' => 300,
		),
		array(
			'key' => 'field_69d9ee6550dd2',
			'label' => 'Related Page',
			'name' => 'related_page',
			'aria-label' => '',
			'type' => 'relationship',
			'instructions' => '',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array(
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'post_type' => array(
				0 => 'page',
			),
			'post_status' => '',
			'taxonomy' => '',
			'filters' => array(
				0 => 'search',
			),
			'return_format' => 'object',
			'acfe_add_post' => 0,
			'acfe_edit_post' => 0,
			'acfe_bidirectional' => array(
				'acfe_bidirectional_enabled' => '0',
			),
			'min' => '',
			'max' => '',
			'allow_in_bindings' => 0,
			'elements' => '',
			'bidirectional' => 0,
			'bidirectional_target' => array(
			),
		),
	),
	'location' => array(
		array(
			array(
				'param' => 'taxonomy',
				'operator' => '==',
				'value' => 'event-type',
			),
		),
	),
	'menu_order' => 0,
	'position' => 'normal',
	'style' => 'default',
	'label_placement' => 'left',
	'instruction_placement' => 'label',
	'hide_on_screen' => '',
	'active' => true,
	'description' => '',
	'show_in_rest' => 1,
	'display_title' => '',
	'allow_ai_access' => false,
	'ai_description' => '',
	'acfe_autosync' => array(
		0 => 'php',
		1 => 'json',
	),
	'acfe_form' => 0,
	'acfe_meta' => '',
	'acfe_note' => '',
	'modified' => 1776645120,
));

endif;