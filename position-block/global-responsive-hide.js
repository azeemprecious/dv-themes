(function (blocks, element, blockEditor, components, i18n, hooks, compose) {
    var el = element.createElement;
    var InspectorControls = blockEditor.InspectorControls || wp.editor.InspectorControls;
    var PanelBody = components.PanelBody;
    var ToggleControl = components.ToggleControl;
    var Fragment = element.Fragment;
    var __ = i18n.__;
    var addFilter = hooks.addFilter;
    var createHigherOrderComponent = compose.createHigherOrderComponent;

    /**
     * 1. Add hide attributes only to core blocks
     */
    function addHideAttributes(settings, name) {
        // Skip blocks that do not support attributes
        if (typeof settings.attributes === 'undefined') {
            return settings;
        }

        // Apply only to core blocks (avoid conflicts with 3rd party like Getwid)
        if (!name.startsWith('core/')) {
            return settings;
        }

        settings.attributes = Object.assign(settings.attributes, {
            gpsHideOnMobile: { type: 'boolean', default: false },
            gpsHideOnTablet: { type: 'boolean', default: false },
            gpsHideOnDesktop: { type: 'boolean', default: false }
        });

        return settings;
    }
    addFilter('blocks.registerBlockType', 'gps/hide-attributes', addHideAttributes);

    /**
     * 2. Add Hide Controls Panel (Mobile/Tablet/Desktop toggles)
     */
    var withHideControls = createHigherOrderComponent(function (BlockEdit) {
        return function (props) {
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
                            { title: __('Responsive Visibility', 'gps-hide'), initialOpen: false },
                            el(ToggleControl, {
                                label: __('Hide on Mobile', 'gps-hide'),
                                checked: attributes.gpsHideOnMobile,
                                __nextHasNoMarginBottom: true,
                                onChange: function (val) {
                                    setAttributes({ gpsHideOnMobile: val });
                                }
                            }),
                            el(ToggleControl, {
                                label: __('Hide on Tablet', 'gps-hide'),
                                checked: attributes.gpsHideOnTablet,
                                __nextHasNoMarginBottom: true,
                                onChange: function (val) {
                                    setAttributes({ gpsHideOnTablet: val });
                                }
                            }),
                            el(ToggleControl, {
                                label: __('Hide on Desktop', 'gps-hide'),
                                checked: attributes.gpsHideOnDesktop,
                                __nextHasNoMarginBottom: true,
                                onChange: function (val) {
                                    setAttributes({ gpsHideOnDesktop: val });
                                }
                            })
                        )
                    )
                );
            }

            return el(BlockEdit, props);
        };
    }, 'withHideControls');
    addFilter('editor.BlockEdit', 'gps/hide-controls', withHideControls);

    /**
     * 3. Apply responsive hide classes to frontend
     */
    function applyHideClasses(extraProps, blockType, attributes) {
        // Skip if attributes are missing (non-core blocks)
        if (
            attributes.gpsHideOnMobile === undefined &&
            attributes.gpsHideOnTablet === undefined &&
            attributes.gpsHideOnDesktop === undefined
        ) {
            return extraProps;
        }

        var classes = String(extraProps.className || '');

        // Remove any existing responsive classes
        classes = classes
            .replace(/\bsm-hide\b/g, '')
            .replace(/\bmd-hide\b/g, '')
            .replace(/\bdt-hide\b/g, '')
            .trim();

        // Add based on current toggle states
        if (attributes.gpsHideOnMobile) {
            classes += ' sm-hide';
        }
        if (attributes.gpsHideOnTablet) {
            classes += ' md-hide';
        }
        if (attributes.gpsHideOnDesktop) {
            classes += ' dt-hide';
        }

        extraProps.className = classes.trim();
        return extraProps;
    }
    addFilter('blocks.getSaveContent.extraProps', 'gps/hide-classes', applyHideClasses);
    
    var withHideClassesInEditor = createHigherOrderComponent(function (BlockListBlock) {
        return function (props) {
            var attributes = props.attributes;
            var extraClasses = props.className || '';

            if (attributes.gpsHideOnMobile) {
                extraClasses += ' sm-hide';
            }
            if (attributes.gpsHideOnTablet) {
                extraClasses += ' md-hide';
            }
            if (attributes.gpsHideOnDesktop) {
                extraClasses += ' dt-hide';
            }

            return el(BlockListBlock, Object.assign({}, props, { className: extraClasses.trim() }));
        };
    }, 'withHideClassesInEditor');

    addFilter('editor.BlockListBlock', 'gps/hide-classes-editor', withHideClassesInEditor);

})(
    window.wp.blocks,
    window.wp.element,
    window.wp.blockEditor,
    window.wp.components,
    window.wp.i18n,
    window.wp.hooks,
    window.wp.compose
);