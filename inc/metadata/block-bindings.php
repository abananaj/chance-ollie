<?php

add_action('acf/init', function () {
    if (!function_exists('acf_get_field_groups')) return;

    $register_fields = function (array $fields, array $post_types) use (&$register_fields) {
        foreach ($fields as $field) {
            if (empty($field['name'])) continue;

            foreach ($post_types ?: [''] as $post_type) {
                register_post_meta($post_type, $field['name'], [
                    'show_in_rest' => true,
                    'single'       => true,
                    'type'         => 'string',
                ]);
            }

            if (!empty($field['sub_fields'])) {
                $register_fields($field['sub_fields'], $post_types);
            }

            foreach ($field['layouts'] ?? [] as $layout) {
                if (!empty($layout['sub_fields'])) {
                    $register_fields($layout['sub_fields'], $post_types);
                }
            }
        }
    };

    foreach (acf_get_field_groups() as $group) {
        $post_types = [];
        foreach ($group['location'] ?? [] as $rule_group) {
            foreach ($rule_group as $rule) {
                if ($rule['param'] === 'post_type' && $rule['operator'] === '==') {
                    $post_types[] = $rule['value'];
                }
            }
        }

        $register_fields(acf_get_fields($group['key']) ?? [], $post_types);
    }
});
