<?php

defined( 'ABSPATH' ) || exit;

function gps_enqueue_editor_assets() {
    wp_enqueue_script(
        'global-position-settings',
        get_template_directory_uri() . '/position-block/global-position-settings.js',
        array( 'wp-blocks', 'wp-element', 'wp-block-editor', 'wp-components', 'wp-i18n', 'wp-hooks', 'wp-compose' ),
        null,
        true
    );
}
add_action( 'enqueue_block_editor_assets', 'gps_enqueue_editor_assets' );

function gps_enqueue_responsive_hide_assets() {
    wp_enqueue_script(
        'global-responsive-hide',
        get_template_directory_uri() . '/position-block/global-responsive-hide.js',
        array( 'wp-blocks', 'wp-element', 'wp-block-editor', 'wp-components', 'wp-i18n', 'wp-hooks', 'wp-compose' ),
        null,
        true
    );
}
add_action( 'enqueue_block_editor_assets', 'gps_enqueue_responsive_hide_assets' );

