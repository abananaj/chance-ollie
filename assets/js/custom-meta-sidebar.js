(function (wp) {
    const { registerPlugin } = wp.plugins;
    const { PluginDocumentSettingPanel } = wp.editPost;
    const { TextControl, ToggleControl } = wp.components;
    const { useSelect, useDispatch } = wp.data;
    const { createElement: el } = wp.element;

    const CustomMetaPanel = () => {
        // Get meta values
        const metaValues = useSelect((select) => {
            return {
                customText: select('core/editor').getEditedPostAttribute('meta')?.custom_text_field || '',
                customCheckbox: select('core/editor').getEditedPostAttribute('meta')?.custom_checkbox_field || false,
            };
        }, []);

        // Update meta values
        const { editPost } = useDispatch('core/editor');

        const updateMeta = (field, value) => {
            editPost({ meta: { [field]: value } });
        };

        return el(
            PluginDocumentSettingPanel,
            {
                name: 'custom-meta-panel',
                title: 'Custom Fields',
                className: 'custom-meta-panel',
            },
            el(TextControl, {
                label: 'Custom Text Field',
                value: metaValues.customText,
                onChange: (value) => updateMeta('custom_text_field', value),
            }),
            el(ToggleControl, {
                label: 'Custom Checkbox',
                checked: metaValues.customCheckbox,
                onChange: (value) => updateMeta('custom_checkbox_field', value),
            })
        );
    };

    registerPlugin('custom-meta-sidebar', {
        render: CustomMetaPanel,
        icon: 'admin-settings',
    });
})(window.wp);
