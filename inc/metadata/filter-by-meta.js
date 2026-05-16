/**
 * WordPress dependencies
 */
import { addFilter } from '@wordpress/hooks';
import { InspectorControls } from '@wordpress/block-editor';
import { PanelBody, TextControl, SelectControl, ToggleControl } from '@wordpress/components';
import { Fragment } from '@wordpress/element';

/**
 * Add meta query filter controls to the Query block inspector
 */
addFilter(
  'editor.BlockEdit',
  'theatrum-blocks/add-meta-query-filter',
  (BlockEdit) => {
    return (props) => {
      // Only apply to the core/query block
      if (props.name !== 'core/query') {
        return <BlockEdit {...props} />;
      }

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

      return (
        <Fragment>
          <BlockEdit {...props} />
          <InspectorControls group="advanced">
            <PanelBody title="Meta Query Filter">
              <ToggleControl
                label="Enable Meta Query Filter"
                checked={enableMetaQuery}
                onChange={(value) => setAttributes({ enableMetaQuery: value })}
                help={enableMetaQuery ? 'Meta query filter is active' : 'Click to enable meta query filtering'}
              />
              {enableMetaQuery && (
                <Fragment>
                  <TextControl
                    label="Meta Key"
                    value={metaKey}
                    onChange={(value) => setAttributes({ metaKey: value })}
                    placeholder="Enter the meta key to filter by"
                    help="The post meta key to use for filtering"
                  />
                  <SelectControl
                    label="Comparison"
                    value={metaCompare}
                    options={comparisonOperators}
                    onChange={(value) => setAttributes({ metaCompare: value })}
                    help="How to compare the meta value"
                  />
                  {metaCompare !== 'empty' && metaCompare !== 'not_empty' && (
                    <Fragment>
                      <TextControl
                        label="Meta Value"
                        value={metaValue}
                        onChange={(value) => setAttributes({ metaValue: value })}
                        placeholder="Enter the value to compare against"
                        help="The value to compare with the meta key"
                      />
                      <SelectControl
                        label="Meta Type"
                        value={metaType}
                        options={metaTypes}
                        onChange={(value) => setAttributes({ metaType: value })}
                        help="The data type of the meta value for comparison"
                      />
                    </Fragment>
                  )}
                </Fragment>
              )}
            </PanelBody>
          </InspectorControls>
        </Fragment>
      );
    };
  }
);
