<?php
function custom_editor_assets() {

    wp_enqueue_style(
        'attributes-for-blocks',
        get_template_directory_uri() . '/dv-builder/blocks/add-custom-attribute/style-index.css', // Adjust the path accordingly
        [],
        '1.0.0', // Replace with your desired version
        'all'
    );

    wp_enqueue_script(
        'attributes-for-blocks',
        get_template_directory_uri() . '/dv-builder/blocks/add-custom-attribute/js-index.js', // Adjust the path accordingly
        [],
        '1.0.0', // Replace with your desired version
        true
    );

    wp_localize_script(
        'attributes-for-blocks',
        'afbData',
        ['unsupportedBlocks' => custom_get_unsupported_blocks()]
    );

    if(function_exists('wp_set_script_translations')) {
        wp_set_script_translations('attributes-for-blocks', 'attributes-for-blocks');
    }
}
add_action('enqueue_block_editor_assets', 'custom_editor_assets', 5);

/**
 * Blocks known to not work properly with Attributes for Blocks.
 *
 * @return array
 */
function custom_get_unsupported_blocks() {
    return apply_filters('afb_unsupported_blocks', [
        'core/freeform',
        'core/html',
        'core/shortcode',
        'core/legacy-widget',
    ]);
}

/**
 * Should additional attributes from this plugin be applied to a block.
 *
 * @param mixed $attributes Block attributes.
 * @return boolean
 */
function custom_has_attributes($attributes) {
    return is_array($attributes)
        && isset($attributes['attributesForBlocks'])
        && is_array($attributes['attributesForBlocks'])
        && count($attributes['attributesForBlocks']) > 0;
}

/**
 * Handle merging custom attribute with existing attribute.
 *
 * @param string $attribute Attribute name.
 * @param string $current Current attribute value.
 * @param string $add Value to merge with.
 * @return string
 */
function custom_merge_attributes($attribute, $current, $add) {
    /** Clean attribute. */
	switch($attribute) {
		case 'style':
			/** Ensure style applied via JS matches input style.  */
			$add = str_replace(': ', ':', $add);
			$add = str_replace('; ', ';', $add);
			$add = rtrim($add, ';');
			$current = rtrim($current, ';');
			/** Fix `WP_HTML_Tag_Processor` stripping leading `-`, causing a mismatch. */
			$current = str_replace('-afb-placeholder', 'afb-placeholder', $current);
			$current = str_replace('afb-placeholder', '-afb-placeholder', $current);
		break;
	}

	$current = trim(
		/** Remove the existing attribute value when it already exists (added via JS while the block also has PHP `render_callback`). */
		str_replace($add, '', $current)
	);
	$add = trim($add);

	/** Nothing to merge. */
	if(empty($current)) {
		return $add;
	}

	/** Determine the separator based on attribute type. */
	$separator = ' ';
	switch($attribute) {
		case 'style':
			if(substr($current, -1) !== ';' && substr($add, 0, 1) !== ';') {
				$separator = ';';
			}
		break;
	}
	$separator = apply_filters('afb_attribute_separator', $separator, $attribute);

	return implode($separator, [$current, $add]);
}

/**
 * @param array $args AFB settings.
 * @param WP_HTML_Tag_Processor $tags
 * @return array Attribute name and value pairs.
 */
function custom_get_attributes($args, $tags) {
    $attributes = [];

	foreach($args as $key => $value) {

		/** Override attribute. */
		if(strpos($key, '@') === 0) {
			$attributes[substr($key, 1)] = $value;
			continue;
		}

		/** Merge attribute. */
		$attributes[$key] = custom_merge_attributes(
			$key,
			$tags->get_attribute($key) ?? '',
			$value
		);
	}

	return $attributes;
}

/**
 * Add attributes to block root element.
 *
 * @param array $args AFB settings.
 * @param string $html Block HTML.
 * @return string Block HTML with additional attributes.
 */
function custom_add_attributes($args, $html) {
    $tags = new WP_HTML_Tag_Processor($html);
	if($tags->next_tag()) {

		/** Add attributes. */
		foreach(custom_get_attributes($args, $tags) as $key => $value) {
			$tags->set_attribute($key, $value);
		}

		return $tags->get_updated_html();
	}

	return $html;
}

/**
 * When registering a block, add AFB argument and wrap `render_callback`.
 *
 * @param array $args
 * @param string $name
 * @return array
 */
function custom_block_args($args, $name) {
    static $not_supported;
	if(!is_array($not_supported)) {
		$not_supported = custom_get_unsupported_blocks();
	}

	if(in_array($name, $not_supported)) {
		return $args;
	}

	if(!isset($args['attributes']) || !is_array($args['attributes'])) {
		$args['attributes'] = [];
	}

	/** Register AFB attributes, this is necessary for `/wp-json/wp/v2/block-renderer` REST endpoint to not throw `rest_additional_properties_forbidden`. */
	$args['attributes']['attributesForBlocks'] = [
		'type' => 'object',
		'default' => [],
	];

	return $args;
}

/**
 * Add attributes to blocks' root HTML element when applicable.
 *
 * @param string $block_content Rendered block.
 * @param string $block Parsed array representation of block.
 * @return string
 */
function custom_render_block($block_content, $block) {
    static $not_supported;
	if(!is_array($not_supported)) {
		$not_supported = custom_get_unsupported_blocks();
	}

	if(in_array($block['blockName'], $not_supported, true)) {
		return $block_content;
	}
	
	if (!isset($block['attrs']) || !is_array($block['attrs'])) {
        return $block_content;
    }
	
	if(!custom_has_attributes($block['attrs'])) {
		return $block_content;
	}

	return custom_add_attributes($block['attrs']['attributesForBlocks'], $block_content);
}

// Add filters and actions

add_filter('register_block_type_args', 'custom_block_args', 10, 2);
add_filter('render_block', 'custom_render_block', 10, 2);