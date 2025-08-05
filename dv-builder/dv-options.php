<?php 



require get_template_directory() . '/dv-builder/theme-options/index.php';

require get_template_directory() . '/dv-builder/blocks/add-custom-attribute/attributes-for-blocks.php';

require get_template_directory() . '/dv-builder/seo-options/robotos-txt.php';

require get_template_directory() . '/dv-builder/seo-options/meta-options.php';


add_action('after_switch_theme', function () {
    update_option('dv_theme_show_welcome_notice', true);
});

add_action('switch_theme', function () {
    delete_option('dv_theme_show_welcome_notice');
});

add_action('admin_notices', function () {
    if (get_option('dv_theme_show_welcome_notice')) {
        echo '<div class="notice notice-success is-dismissible">';
        echo '<p><strong>Welcome to DV Themes — your next step towards technology 🚀</strong></p>';
        echo '</div>';
    }
});



add_action('admin_menu', 'dvbuilder_rename_getwid_menu', 999);
function dvbuilder_rename_getwid_menu() {
    global $menu;
    foreach ($menu as $key => $item) {
        if (isset($item[0]) && stripos($item[0], 'Getwid') !== false) {
            $menu[$key][0] = str_ireplace('Getwid', 'DV Builder', $item[0]);
        }
    }
}

add_filter('all_plugins', 'dvbuilder_rename_getwid_plugin_title');
function dvbuilder_rename_getwid_plugin_title($plugins) {
    foreach ($plugins as $key => $plugin) {
        if (stripos($plugin['Name'], 'Getwid') !== false) {
            $plugins[$key]['Name'] = str_ireplace('Getwid', 'DV Builder', $plugin['Name']);
        }
    }
    return $plugins;
}

add_filter('gettext', 'dvbuilder_replace_getwid_texts', 100, 3);
function dvbuilder_replace_getwid_texts($translated_text, $text, $domain) {
    if (is_admin()) {
        $translated_text = str_ireplace('Getwid', 'DV Builder', $translated_text);
    }
    return $translated_text;
}

// Remove the plugin row meta (links like View Details, Support, etc.)
add_filter('plugin_row_meta', 'dv_hide_plugin_meta_links', 10, 2);
function dv_hide_plugin_meta_links($meta, $plugin_file) {
    if ($plugin_file === 'getwid/getwid.php') {
        return []; // Return empty array to hide all links
    }
    return $meta;
}

// Remove plugin version, author etc. from the main listing
add_filter('all_plugins', 'dv_hide_plugin_header_info');
function dv_hide_plugin_header_info($plugins) {
    if (isset($plugins['getwid/getwid.php'])) {
        $plugins['getwid/getwid.php']['Version'] = '';
        $plugins['getwid/getwid.php']['Author'] = '';
        $plugins['getwid/getwid.php']['AuthorURI'] = '';
        $plugins['getwid/getwid.php']['PluginURI'] = '';
    }
    return $plugins;
}


/*=== ALLOW TO UPLOAD SVG FILES ===*/

function add_file_types_to_uploads($file_types){

$new_filetypes = array();

    $new_filetypes['svg'] = 'image/svg+xml';

    $file_types = array_merge($file_types, $new_filetypes );

    return $file_types;

}

add_filter('upload_mimes', 'add_file_types_to_uploads');



/*=== 404 PAGES ===*/

add_action('template_redirect', 'redirect_404_to_homepage');

function redirect_404_to_homepage() {

    if (is_404()) {

        wp_redirect(home_url());

        exit;

    }

}



add_action( 'init', 'register_pattern_categories' );



function register_pattern_categories() {

    register_block_pattern_category(

        'themeslug/fancybox',

        array(

            'label'       => __( 'Fancy Box', 'themeslug' ),

            'description' => __( 'Fancy Box', 'themeslug' ),

        )

    );

}



/*=== ADMIN EDITOR STYLING ===*/

function block_editor_full_width() {    add_theme_support( 'editor-styles' );   add_editor_style( get_template_directory_uri() . '/dv-builder/assets/css/admin-style.css' );}add_action('after_setup_theme', 'block_editor_full_width');



/*=== ADD OR REMOVE SCRIPTS ===*/

function my_custom_script() {   wp_deregister_style('animate'); wp_dequeue_style('wp-block-library');   wp_enqueue_script('plugin-scripts');}add_action('wp_enqueue_scripts', 'my_custom_script');



/*=== REMOVE CONTECT FORM 7 STYLES ===*/

add_filter( 'wpcf7_load_css', '__return_false' );



/*=== REMOVE DEFAULT PATTERNS ===*/

add_action('after_setup_theme', function() {    remove_theme_support('core-block-patterns');});



/*=== SERVICE POST TYPE ===*/

function register_services_post() {

    $labels = array(

        'name' => __('DV Services', 'textdomain'),

        'singular_name' => __('DV Services', 'textdomain'),

        'menu_name' => __('DV Services', 'textdomain'),

        'name_admin_bar' => __('DV Services', 'textdomain'),

        'add_new' => __('Add New', 'textdomain'),

        'add_new_item' => __('Add New DV Services', 'textdomain'),

        'edit_item' => __('Edit DV Services', 'textdomain'),

        'new_item' => __('New DV Services', 'textdomain'),

        'view_item' => __('View DV Services', 'textdomain'),

        'all_items' => __('All DV Services', 'textdomain'),

        'search_items' => __('Search DV Services', 'textdomain'),

        'not_found' => __('No DV Services found.', 'textdomain'),

        'not_found_in_trash' => __('No DV Services found in Trash.', 'textdomain'),

    );

    $args = array( 'labels' => $labels, 'public' => true, 'has_archive' => false, 'menu_position' => 5, 'menu_icon' => 'dashicons-admin-tools', 'supports' => array('title', 'editor', 'thumbnail', 'excerpt', 'custom-fields'), 'taxonomies' => array('service_category'), 'show_in_rest' => true, );

    register_post_type('services', $args);

}

add_action('init', 'register_services_post');



function service_taxonomy() {

    $labels = array(

        'name' => __('DV Services Categories', 'textdomain'),

        'singular_name' => __('DV Services Category', 'textdomain'),

        'search_items' => __('Search Categories', 'textdomain'),

        'all_items' => __('All Categories', 'textdomain'),

        'parent_item' => __('Parent Category', 'textdomain'),

        'parent_item_colon' => __('Parent Category:', 'textdomain'),

        'edit_item' => __('Edit Category', 'textdomain'),

        'update_item' => __('Update Category', 'textdomain'),

        'add_new_item' => __('Add New Category', 'textdomain'),

        'new_item_name' => __('New Category Name', 'textdomain'),

        'menu_name' => __('Categories', 'textdomain'),

    );

    $args = array( 'labels' => $labels, 'hierarchical' => true, 'public' => true, 'show_ui' => true, 'show_in_menu' => true, 'show_in_rest' => true, );

    register_taxonomy('service_category', 'services', $args);

}

add_action('init', 'service_taxonomy');

