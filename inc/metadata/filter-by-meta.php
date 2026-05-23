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

// Store meta query config from Query block attrs, keyed by queryId.
// Used by query_loop_block_query_vars below.
global $chance_meta_query_configs;
$chance_meta_query_configs = array();

// Apply meta_query to the Query block's WP_Query via the dedicated filter.
// (query_loop_block_query_vars fires inside post-template render, after the
//  Query block's render_block_data has already run and stored our config.)
add_filter('query_loop_block_query_vars', function ($query, $block, $page) {
  global $chance_meta_query_configs;

  $query_id = $block->context['queryId'] ?? null;

  if (null === $query_id || empty($chance_meta_query_configs[$query_id])) {
    return $query;
  }

  $meta    = $chance_meta_query_configs[$query_id];
  $meta_q  = array();

  // Handle "is empty" and "is not empty" operators
  if ('empty' === $meta['compare']) {
    $meta_q = array(
      'key'     => $meta['key'],
      'compare' => 'NOT EXISTS',
    );
  } elseif ('not_empty' === $meta['compare']) {
    $meta_q = array(
      'key'     => $meta['key'],
      'compare' => 'EXISTS',
    );
  } else {
    $meta_q = array(
      'key'     => $meta['key'],
      'value'   => $meta['value'],
      'compare' => $meta['compare'],
      'type'    => $meta['type'],
    );
  }

  $existing = isset($query['meta_query']) && is_array($query['meta_query'])
    ? $query['meta_query']
    : array();

  $existing[] = $meta_q;

  if (count($existing) > 1) {
    $existing['relation'] = 'AND';
  }

  $query['meta_query'] = $existing;

  return $query;
}, 10, 3);

// Capture meta query config from Query block attrs before it renders.
// Stored in $chance_meta_query_configs, keyed by queryId, so that
// the query_loop_block_query_vars filter above can find it.
add_filter('render_block_data', function ($parsed_block, $source_block) {
  global $chance_meta_query_configs;

  if ('core/query' !== $parsed_block['blockName']) {
    return $parsed_block;
  }

  $attrs = $parsed_block['attrs'] ?? array();

  if (! ($attrs['enableMetaQuery'] ?? false)) {
    return $parsed_block;
  }

  $meta_key = $attrs['metaKey'] ?? '';

  if (empty($meta_key)) {
    return $parsed_block;
  }

  $query_id = $attrs['queryId'] ?? null;

  if (null === $query_id) {
    return $parsed_block;
  }

  $chance_meta_query_configs[$query_id] = array(
    'key'     => $meta_key,
    'value'   => $attrs['metaValue'] ?? '',
    'compare' => $attrs['metaCompare'] ?? '=',
    'type'    => $attrs['metaType'] ?? 'CHAR',
  );

  return $parsed_block;
}, 10, 2);

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
