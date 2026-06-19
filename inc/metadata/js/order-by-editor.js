(function () {
	const { addFilter } = wp.hooks;
	const { createHigherOrderComponent } = wp.compose;
	const { InspectorControls } = wp.blockEditor;
	const { PanelBody, TextControl, SelectControl } = wp.components;
	const el = wp.element.createElement;
	const { Fragment } = wp.element;

	addFilter('blocks.registerBlockType', 'chance/order-by-meta-attrs', (settings, name) => {
		if (name !== 'core/query') return settings;
		return {
			...settings,
			attributes: {
				...settings.attributes,
				orderMetaKey:       { type: 'string', default: '' },
				orderMetaType:      { type: 'string', default: 'string' },
				orderMetaDirection: { type: 'string', default: 'ASC' },
			},
		};
	});

	const typeOptions = [
		{ label: 'String',     value: 'string' },
		{ label: 'Number',     value: 'number' },
		{ label: 'Date / Time', value: 'date-time' },
		{ label: 'Boolean',    value: 'boolean' },
	];

	const directionOptions = [
		{ label: 'Ascending',  value: 'ASC' },
		{ label: 'Descending', value: 'DESC' },
	];

	const withOrderMeta = createHigherOrderComponent((BlockEdit) => {
		return (props) => {
			if (props.name !== 'core/query') return el(BlockEdit, props);

			const { attributes, setAttributes } = props;
			const { orderMetaKey, orderMetaType, orderMetaDirection } = attributes;

			return el(Fragment, null,
				el(BlockEdit, props),
				el(InspectorControls, null,
					el(PanelBody, { title: 'Order by Meta', initialOpen: false },
						el(TextControl, {
							label: 'Meta Key',
							value: orderMetaKey,
							onChange: (v) => setAttributes({ orderMetaKey: v }),
						}),
						el(SelectControl, {
							label: 'Data Type',
							value: orderMetaType,
							options: typeOptions,
							onChange: (v) => setAttributes({ orderMetaType: v }),
						}),
						el(SelectControl, {
							label: 'Direction',
							value: orderMetaDirection,
							options: directionOptions,
							onChange: (v) => setAttributes({ orderMetaDirection: v }),
						}),
					),
				),
			);
		};
	}, 'withOrderMeta');

	addFilter('editor.BlockEdit', 'chance/order-by-meta-ui', withOrderMeta);
})();
