<?php 

if( function_exists('acf_add_local_field_group') ):

acf_add_local_field_group(array(
	'key' => 'group_open_jobs',
	'title' => 'Open Jobs 💼',
	'fields' => array(
		array(
			'key' => 'field_69dadd006d24c',
			'label' => '',
			'name' => 'open_positions',
			'aria-label' => '',
			'type' => 'repeater',
			'instructions' => 'Appears on https://chancetheater.local/backstage/get-involved/open-positions/',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array(
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'acfe_repeater_stylised_button' => 0,
			'layout' => 'block',
			'pagination' => 0,
			'min' => 0,
			'max' => 0,
			'collapsed' => '',
			'button_label' => 'Add Job',
			'rows_per_page' => 20,
			'sub_fields' => array(
				array(
					'key' => 'field_69dadd196d24d',
					'label' => 'Job Title',
					'name' => 'job_title',
					'aria-label' => '',
					'type' => 'text',
					'instructions' => '',
					'required' => 0,
					'conditional_logic' => 0,
					'wrapper' => array(
						'width' => '',
						'class' => '',
						'id' => '',
					),
					'default_value' => '',
					'maxlength' => '',
					'allow_in_bindings' => 0,
					'placeholder' => '',
					'prepend' => '',
					'append' => '',
					'parent_repeater' => 'field_69dadd006d24c',
				),
				array(
					'key' => 'field_69dade41921ac',
					'label' => 'Description',
					'name' => '',
					'aria-label' => '',
					'type' => 'accordion',
					'instructions' => '',
					'required' => 0,
					'conditional_logic' => 0,
					'wrapper' => array(
						'width' => '',
						'class' => '',
						'id' => '',
					),
					'open' => 0,
					'multi_expand' => 0,
					'endpoint' => 0,
					'parent_repeater' => 'field_69dadd006d24c',
				),
				array(
					'key' => 'field_69dadd286038e',
					'label' => '',
					'name' => 'job_description',
					'aria-label' => '',
					'type' => 'acfe_block_editor',
					'instructions' => '',
					'required' => 0,
					'conditional_logic' => 0,
					'wrapper' => array(
						'width' => '',
						'class' => '',
						'id' => '',
					),
					'min_height' => 175,
					'max_height' => '',
					'autoresize' => 1,
					'topbar' => 1,
					'topbar_tools' => array(
						0 => 'inserter',
						1 => 'undo',
						2 => 'navigation',
					),
					'fixed_toolbar' => 1,
					'allow_code_mode' => 1,
					'allow_lock' => 1,
					'allow_upload' => 1,
					'allow_library' => 1,
					'allowed_blocks' => array(
						'row-0' => array(
							'block' => 'core/paragraph',
						),
						'row-1' => array(
							'block' => 'core/image',
						),
						'row-2' => array(
							'block' => 'core/gallery',
						),
						'row-3' => array(
							'block' => 'core/quote',
						),
						'row-4' => array(
							'block' => 'core/heading',
						),
						'row-5' => array(
							'block' => 'core/list',
						),
						'row-6' => array(
							'block' => 'core/list-item',
						),
						'row-7' => array(
							'block' => 'core/code',
						),
						'row-8' => array(
							'block' => 'core/shortcode',
						),
						'row-9' => array(
							'block' => 'core/html',
						),
						'row-10' => array(
							'block' => 'core/table',
						),
					),
					'allow_in_bindings' => 0,
					'height' => 175,
					'parent_repeater' => 'field_69dadd006d24c',
				),
			),
		),
	),
	'location' => array(
		array(
			array(
				'param' => 'options_page',
				'operator' => '==',
				'value' => 'site-options',
			),
		),
	),
	'menu_order' => 10,
	'position' => 'normal',
	'style' => 'default',
	'label_placement' => 'top',
	'instruction_placement' => 'label',
	'hide_on_screen' => '',
	'active' => true,
	'description' => '',
	'show_in_rest' => 1,
	'display_title' => '',
	'allow_ai_access' => false,
	'ai_description' => '',
	'acfe' => array(
		'autosync' => array(
			0 => 'php',
			1 => 'json',
		),
	),
	'modified' => 1781566506,
));

endif;