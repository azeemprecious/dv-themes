(function (blocks, element, blockEditor, components, i18n, hooks, compose) {
    var el = element.createElement;
    var InspectorControls = blockEditor.InspectorControls || wp.editor.InspectorControls;
    var PanelBody = components.PanelBody;
    var SelectControl = components.SelectControl;
    var TextControl = components.TextControl;
    var Fragment = element.Fragment;
    var __ = i18n.__;
    var addFilter = hooks.addFilter;
    var createHigherOrderComponent = compose.createHigherOrderComponent;

    /**
     * 1. Add new attributes only to core/group block
     */
    function addPositionAttributes(settings, name) {
        if (name !== 'core/group') return settings; // Restrict to core/group

        if (typeof settings.attributes !== 'undefined') {
            settings.attributes = Object.assign(settings.attributes, {
                positionType: { type: 'string', default: 'static' },
                top: { type: 'string', default: '0px' },
                left: { type: 'string', default: '0px' },
                right: { type: 'string', default: 'auto' },
                bottom: { type: 'string', default: 'auto' },
                customWidth: { type: 'string', default: 'auto' }, // NEW width
                zIndex: { type: 'string', default: '0' } // NEW z-index
            });
        }
        return settings;
    }
    addFilter('blocks.registerBlockType', 'myplugin/position-attributes', addPositionAttributes);

    /**
     * 2. Add Position, Width, Z-Index Settings panel
     */
    var withPositionControls = createHigherOrderComponent(function (BlockEdit) {
        return function (props) {
            if (props.name !== 'core/group') return el(BlockEdit, props); // Restrict to core/group

            var attributes = props.attributes;
            var setAttributes = props.setAttributes;

            if (props.isSelected) {
                return el(
                    Fragment,
                    {},
                    el(BlockEdit, props),
                    el(
                        InspectorControls,
                        {},
                        el(
                            PanelBody,
                            { title: __('Position & Size Settings', 'global-position'), initialOpen: false },

                            // Position Type control
                            el('div', { style: { marginBottom: '8px' } },
                                el(SelectControl, {
                                    label: __('Position Type', 'global-position'),
                                    value: attributes.positionType,
                                    options: [
                                        { label: 'Static', value: 'static' },
                                        { label: 'Relative', value: 'relative' },
                                        { label: 'Absolute', value: 'absolute' },
                                        { label: 'Fixed', value: 'fixed' }
                                    ],
									__next40pxDefaultSize: true,
									__nextHasNoMarginBottom: true,
                                    onChange: function (val) {
                                        setAttributes({ positionType: val });
                                    }
                                })
                            ),

                            // Top
                            el('div', { style: { marginBottom: '8px' } },
                                el(TextControl, {
                                    label: __('Top (e.g. 20px, inherit, auto)', 'global-position'),
                                    value: attributes.top,
									__next40pxDefaultSize: true,
									__nextHasNoMarginBottom: true,
                                    onChange: function (val) {
                                        setAttributes({ top: val });
                                    }
                                })
                            ),

                            // Left
                            el('div', { style: { marginBottom: '8px' } },
                                el(TextControl, {
                                    label: __('Left (e.g. 20px, inherit, auto)', 'global-position'),
                                    value: attributes.left,
									__next40pxDefaultSize: true,
									__nextHasNoMarginBottom: true,
                                    onChange: function (val) {
                                        setAttributes({ left: val });
                                    }
                                })
                            ),

                            // Right
                            el('div', { style: { marginBottom: '8px' } },
                                el(TextControl, {
                                    label: __('Right (e.g. 20px, inherit, auto)', 'global-position'),
                                    value: attributes.right,
									__next40pxDefaultSize: true,
									__nextHasNoMarginBottom: true,
                                    onChange: function (val) {
                                        setAttributes({ right: val });
                                    }
                                })
                            ),

                            // Bottom
                            el('div', { style: { marginBottom: '8px' } },
                                el(TextControl, {
                                    label: __('Bottom (e.g. 20px, inherit, auto)', 'global-position'),
                                    value: attributes.bottom,
									__next40pxDefaultSize: true,
									__nextHasNoMarginBottom: true,
                                    onChange: function (val) {
                                        setAttributes({ bottom: val });
                                    }
                                })
                            ),

                            // Width
                            el('div', { style: { marginBottom: '8px' } },
                                el(TextControl, {
                                    label: __('Width (e.g. 100px, 50%, auto)', 'global-position'),
                                    value: attributes.customWidth,
									__next40pxDefaultSize: true,
									__nextHasNoMarginBottom: true,
                                    onChange: function (val) {
                                        setAttributes({ customWidth: val });
                                    }
                                })
                            ),

                            // Z-Index
                            el('div', { style: { marginBottom: '8px' } },
                                el(TextControl, {
                                    label: __('Z-Index (e.g. 10, 999)', 'global-position'),
                                    value: attributes.zIndex,
									__next40pxDefaultSize: true,
									__nextHasNoMarginBottom: true,
                                    onChange: function (val) {
                                        setAttributes({ zIndex: val });
                                    }
                                })
                            )
                        )
                    )
                );
            }
            return el(BlockEdit, props);
        };
    }, 'withPositionControls');

    addFilter('editor.BlockEdit', 'myplugin/position-controls', withPositionControls);

    /**
     * 3. Apply styles on frontend (only core/group)
     */
    function applyExtraStyles(extraProps, blockType, attributes) {
        if (blockType.name !== 'core/group') return extraProps;

        var styleObj = {};

        if (attributes.positionType && attributes.positionType !== 'static') {
            styleObj.position = attributes.positionType;
        }
        if (attributes.top && attributes.top !== '0px') {
            styleObj.top = attributes.top;
        }
        if (attributes.left && attributes.left !== '0px') {
            styleObj.left = attributes.left;
        }
        if (attributes.right && attributes.right !== 'auto') {
            styleObj.right = attributes.right;
        }
        if (attributes.bottom && attributes.bottom !== 'auto') {
            styleObj.bottom = attributes.bottom;
        }

        // Width
        if (attributes.customWidth && attributes.customWidth !== 'auto') {
            styleObj.width = attributes.customWidth;
        }

        // Z-Index
        if (attributes.zIndex && attributes.zIndex !== '0') {
            styleObj.zIndex = attributes.zIndex;
        }

        if (Object.keys(styleObj).length > 0) {
            extraProps.style = Object.assign({}, extraProps.style, styleObj);
        }

        return extraProps;
    }
    addFilter('blocks.getSaveContent.extraProps', 'myplugin/position-style', applyExtraStyles);

})(
    window.wp.blocks,
    window.wp.element,
    window.wp.blockEditor,
    window.wp.components,
    window.wp.i18n,
    window.wp.hooks,
    window.wp.compose
);

