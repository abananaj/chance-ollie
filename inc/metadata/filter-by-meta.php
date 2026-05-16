<?php

/**
 * Add meta query filtering to the Query block.
 * 
 * This extends the Query block to allow filtering by meta key and value with various
 * comparison operators (equals, not equals, greater than, less than, etc.) and
 * "is empty" / "is not empty" options.
 * 
 * @package Chance Theater
 */

// Register custom attributes for the Query block
add_filter('register_block_type_args', function ($args, $block_type) {
  // Handle both string and object block type
  $block_name = is_string($block_type) ? $block_type : (isset($block_type->name) ? $block_type->name : '');

  if ('core/query' !== $block_name) {
    return $args;
  }

  if (! isset($args['attributes'])) {
    $args['attributes'] = array();
  }

  $args['attributes'] = array_merge($args['attributes'], array(
    'metaKey'        => array(
      'type'    => 'string',
      'default' => '',
    ),
    'metaValue'      => array(
      'type'    => 'string',
      'default' => '',
    ),
    'metaCompare'    => array(
      'type'    => 'string',
      'default' => '=',
    ),
    'metaType'       => array(
      'type'    => 'string',
      'default' => 'CHAR',
    ),
    'enableMetaQuery' => array(
      'type'    => 'boolean',
      'default' => false,
    ),
  ));

  return $args;
}, 10, 2);

// Modify the query to include meta_query when meta filter is enabled
add_action('pre_get_posts', function ($query) {
  if (is_admin() || ! $query->is_main_query()) {
    return;
  }

  // Get the meta query parameters from the query var
  $meta_key     = $query->get('meta_key_filter');
  $meta_value   = $query->get('meta_value_filter');
  $meta_compare = $query->get('meta_compare_filter');
  $meta_type    = $query->get('meta_type_filter');

  if (empty($meta_key)) {
    return;
  }

  $meta_query = array();

  // Handle "is empty" and "is not empty" operators
  if ('empty' === $meta_compare) {
    $meta_query = array(
      'key'     => $meta_key,
      'compare' => 'NOT EXISTS',
    );
  } elseif ('not_empty' === $meta_compare) {
    $meta_query = array(
      'key'     => $meta_key,
      'compare' => 'EXISTS',
    );
  } else {
    // Standard comparison operators
    $meta_query = array(
      'key'     => $meta_key,
      'value'   => $meta_value,
      'compare' => $meta_compare,
      'type'    => $meta_type,
    );
  }

  // Get existing meta_query args
  $existing_meta_query = $query->get('meta_query');
  if (! is_array($existing_meta_query)) {
    $existing_meta_query = array();
  }

  // Add new meta query
  $existing_meta_query[] = $meta_query;

  // Set the meta_query args
  if (count($existing_meta_query) > 1) {
    $existing_meta_query['relation'] = 'AND';
  }
  $query->set('meta_query', $existing_meta_query);
});

// Render callback to pass meta query data to frontend
add_filter('render_block_data', function ($parsed_block, $source_block) {
  if ('core/query' !== $parsed_block['blockName']) {
    return $parsed_block;
  }

  $attrs = $parsed_block['attrs'] ?? array();

  if (! ($attrs['enableMetaQuery'] ?? false)) {
    return $parsed_block;
  }

  $meta_key     = $attrs['metaKey'] ?? '';
  $meta_value   = $attrs['metaValue'] ?? '';
  $meta_compare = $attrs['metaCompare'] ?? '=';
  $meta_type    = $attrs['metaType'] ?? 'CHAR';

  if (empty($meta_key)) {
    return $parsed_block;
  }

  // Pass meta query parameters via query vars
  if (! isset($parsed_block['attrs']['query'])) {
    $parsed_block['attrs']['query'] = array();
  }

  // Store in a custom context that can be used by inner blocks
  $parsed_block['attrs']['metaQuery'] = array(
    'key'     => $meta_key,
    'value'   => $meta_value,
    'compare' => $meta_compare,
    'type'    => $meta_type,
  );

  return $parsed_block;
}, 10, 2);

// REST API support for meta query parameters
add_filter('rest_query_vars', function ($query_vars) {
  return array_merge($query_vars, array(
    'meta_key_filter',
    'meta_value_filter',
    'meta_compare_filter',
    'meta_type_filter',
  ));
});

/**
 * Enqueue editor scripts to add meta query filter UI to Query block
 */
function chance_enqueue_meta_query_editor_assets()
{
  // Enqueue inline script to add meta query filter to Query block
  wp_enqueue_script(
    'chance-query-meta-filter',
    false,
    array('wp-hooks', 'wp-block-editor', 'wp-components', 'wp-element'),
    null
  );

  // Add inline script
  $script = <<<'JS'
(function() {
  const { addFilter } = wp.hooks;
  const { InspectorControls } = wp.blockEditor;
  const { PanelBody, TextControl, SelectControl, ToggleControl } = wp.components;
  const { Fragment } = wp.element;

  addFilter(
    'editor.BlockEdit',
    'chance-theme/add-meta-query-filter',
    (BlockEdit) => {
      return (props) => {
        if (props.name !== 'core/query') {
          return React.createElement(BlockEdit, props);
        }

        const { attributes, setAttributes } = props;
        const {
          metaKey = '',
          metaValue = '',
          metaCompare = '=',
          metaType = 'CHAR',
          enableMetaQuery = false,
        } = attributes;

        const comparisonOperators = [
          { label: 'Equals', value: '=' },
          { label: 'Not Equals', value: '!=' },
          { label: 'Greater Than', value: '>' },
          { label: 'Greater Than or Equal', value: '>=' },
          { label: 'Less Than', value: '<' },
          { label: 'Less Than or Equal', value: '<=' },
          { label: 'Like', value: 'LIKE' },
          { label: 'Not Like', value: 'NOT LIKE' },
          { label: 'In', value: 'IN' },
          { label: 'Not In', value: 'NOT IN' },
          { label: 'Between', value: 'BETWEEN' },
          { label: 'Not Between', value: 'NOT BETWEEN' },
          { label: 'Is Empty', value: 'empty' },
          { label: 'Is Not Empty', value: 'not_empty' },
        ];

        const metaTypes = [
          { label: 'Character', value: 'CHAR' },
          { label: 'Numeric', value: 'NUMERIC' },
          { label: 'Date', value: 'DATE' },
          { label: 'Datetime', value: 'DATETIME' },
        ];

        return React.createElement(
          Fragment,
          null,
          React.createElement(BlockEdit, props),
          React.createElement(
            InspectorControls,
            { group: 'advanced' },
            React.createElement(
              PanelBody,
              { title: 'Meta Query Filter' },
              React.createElement(ToggleControl, {
                label: 'Enable Meta Query Filter',
                checked: enableMetaQuery,
                onChange: (value) => setAttributes({ enableMetaQuery: value }),
                help: enableMetaQuery ? 'Meta query filter is active' : 'Click to enable meta query filtering',
              }),
              enableMetaQuery && React.createElement(
                Fragment,
                null,
                React.createElement(TextControl, {
                  label: 'Meta Key',
                  value: metaKey,
                  onChange: (value) => setAttributes({ metaKey: value }),
                  placeholder: 'Enter the meta key to filter by',
                  help: 'The post meta key to use for filtering',
                }),
                React.createElement(SelectControl, {
                  label: 'Comparison',
                  value: metaCompare,
                  options: comparisonOperators,
                  onChange: (value) => setAttributes({ metaCompare: value }),
                  help: 'How to compare the meta value',
                }),
                (metaCompare !== 'empty' && metaCompare !== 'not_empty') && React.createElement(
                  Fragment,
                  null,
                  React.createElement(TextControl, {
                    label: 'Meta Value',
                    value: metaValue,
                    onChange: (value) => setAttributes({ metaValue: value }),
                    placeholder: 'Enter the value to compare against',
                    help: 'The value to compare with the meta key',
                  }),
                  React.createElement(SelectControl, {
                    label: 'Meta Type',
                    value: metaType,
                    options: metaTypes,
                    onChange: (value) => setAttributes({ metaType: value }),
                    help: 'The data type of the meta value for comparison',
                  })
                )
              )
            )
          )
        );
      };
    }
  );
})();
JS;

  wp_add_inline_script('chance-query-meta-filter', $script);
}
add_action('enqueue_block_editor_assets', 'chance_enqueue_meta_query_editor_assets');
