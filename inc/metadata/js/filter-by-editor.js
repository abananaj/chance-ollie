(function () {
	const { addFilter } = wp.hooks;
	const { createHigherOrderComponent } = wp.compose;
	const { InspectorControls } = wp.blockEditor;
	const { PanelBody, TextControl, SelectControl, RadioControl } = wp.components;
	const el = wp.element.createElement;
	const { Fragment } = wp.element;

	addFilter('blocks.registerBlockType', 'chance/filter-by-meta-attrs', (settings, name) => {
		if (name !== 'core/query') return settings;
		return {
			...settings,
			attributes: {
				...settings.attributes,
				filterMetaKey:     { type: 'string', default: '' },
				filterMetaType:    { type: 'string', default: 'string' },
				filterMetaCompare: { type: 'string', default: '=' },
				filterMetaValue:   { type: 'string', default: '' },
				dateMetaKey:       { type: 'string', default: '' },
				dateMetaMode:      { type: 'string', default: 'upcoming' },
			},
		};
	});

	const compareOptions = {
		string:      ['=', '!=', 'LIKE', 'NOT LIKE', 'EXISTS', 'NOT EXISTS'],
		number:      ['=', '!=', '>', '>=', '<', '<=', 'EXISTS', 'NOT EXISTS'],
		'date-time': ['=', '!=', '>', '>=', '<', '<=', 'EXISTS', 'NOT EXISTS'],
		boolean:     ['=', '!=', 'EXISTS', 'NOT EXISTS'],
	};

	const typeOptions = [
		{ label: 'String',     value: 'string' },
		{ label: 'Number',     value: 'number' },
		{ label: 'Date / Time', value: 'date-time' },
		{ label: 'Boolean',    value: 'boolean' },
	];

	const withFilterMeta = createHigherOrderComponent((BlockEdit) => {
		return (props) => {
			if (props.name !== 'core/query') return el(BlockEdit, props);

			const { attributes, setAttributes } = props;
			const { filterMetaKey, filterMetaType, filterMetaCompare, filterMetaValue,
				dateMetaKey, dateMetaMode } = attributes;
			const hideValue = ['EXISTS', 'NOT EXISTS'].includes(filterMetaCompare);
			const opts = (compareOptions[filterMetaType] || compareOptions.string).map((v) => ({ label: v, value: v }));

			const valueHelp = filterMetaType === 'date-time'
				? 'Accepts: YYYYMMDD, YYYY-MM-DD, YYYY-MM-DD HH:MM:SS, Unix timestamp'
				: filterMetaType === 'boolean'
				? 'Enter 1 (true) or 0 (false)'
				: '';

			return el(Fragment, null,
				el(BlockEdit, props),
				el(InspectorControls, null,
					el(PanelBody, { title: 'Upcoming / Past Filter', initialOpen: false },
						el(TextControl, {
							label: 'Date Meta Key',
							value: dateMetaKey,
							help: 'e.g. opening — filters by this date field against today',
							onChange: (v) => setAttributes({ dateMetaKey: v }),
						}),
						el(RadioControl, {
							label: 'Show',
							selected: dateMetaMode,
							options: [
								{ label: 'Upcoming (on or after today)', value: 'upcoming' },
								{ label: 'Past (before today)',           value: 'past' },
								{ label: 'All',                          value: 'all' },
							],
							onChange: (v) => setAttributes({ dateMetaMode: v }),
						}),
					),
					el(PanelBody, { title: 'Filter by Meta', initialOpen: false },
						el(TextControl, {
							label: 'Meta Key',
							value: filterMetaKey,
							onChange: (v) => setAttributes({ filterMetaKey: v }),
						}),
						el(SelectControl, {
							label: 'Data Type',
							value: filterMetaType,
							options: typeOptions,
							onChange: (v) => setAttributes({ filterMetaType: v, filterMetaCompare: '=' }),
						}),
						el(SelectControl, {
							label: 'Compare',
							value: filterMetaCompare,
							options: opts,
							onChange: (v) => setAttributes({ filterMetaCompare: v }),
						}),
						!hideValue && el(TextControl, {
							label: 'Value',
							value: filterMetaValue,
							help: valueHelp,
							onChange: (v) => setAttributes({ filterMetaValue: v }),
						}),
					),
				),
			);
		};
	}, 'withFilterMeta');

	addFilter('editor.BlockEdit', 'chance/filter-by-meta-ui', withFilterMeta);
})();
