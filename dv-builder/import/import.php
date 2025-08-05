<?php
add_action('after_switch_theme', 'auto_install_all_in_one_migration_and_s3_extension');
function auto_install_all_in_one_migration_and_s3_extension() {
    error_log('Theme switched, starting plugin installation process.');
    include_once(ABSPATH . 'wp-admin/includes/plugin-install.php');
    include_once(ABSPATH . 'wp-admin/includes/plugin.php');
    include_once(ABSPATH . 'wp-admin/includes/file.php');
    include_once(ABSPATH . 'wp-admin/includes/misc.php');
    include_once(ABSPATH . 'wp-admin/includes/class-wp-upgrader.php');
    $plugins = [
        'all-in-one-wp-migration' => 'https://designvesta.com/staging/download_plugins/all-in-one-wp-migration.7.87.zip',
        'all-in-one-wp-migration-s3-extension' =>'https://designvesta.com/staging/download_plugins/all-in-one-wp-migration-s3-extension.zip' 
    ];
    foreach ($plugins as $plugin_slug => $plugin_url_or_path) {
        $plugin_file = WP_PLUGIN_DIR . '/' . $plugin_slug . '/' . $plugin_slug . '.php';     
        if (!file_exists($plugin_file)) {
           // error_log("Plugin not installed: $plugin_slug");
            ob_start(); 
            $upgrader = new Plugin_Upgrader();
            if (filter_var($plugin_url_or_path, FILTER_VALIDATE_URL)) {
             //   error_log("Installing from URL: $plugin_url_or_path");
                $result = $upgrader->install($plugin_url_or_path);
            } elseif (file_exists($plugin_url_or_path)) {
                error_log("Installing from local ZIP: $plugin_url_or_path");
                $result = $upgrader->install($plugin_url_or_path);
            } else {
                //error_log("Error: Plugin URL or path is invalid for $plugin_slug");
            }
            ob_end_clean();
            if (isset($result) && is_wp_error($result)) {
                //error_log('Error installing plugin: ' . $result->get_error_message());
            } elseif (isset($result) && !is_wp_error($result)) {
             //   error_log("Plugin installed successfully: $plugin_slug");
               
                if (file_exists($plugin_file) && !is_plugin_active($plugin_slug . '/' . $plugin_slug . '.php')) {
                   // error_log("Activating plugin: $plugin_slug");
                    $activation_result = activate_plugin($plugin_slug . '/' . $plugin_slug . '.php');
                    if (is_wp_error($activation_result)) {
                      //  error_log('Error activating plugin: ' . $activation_result->get_error_message());
                    }
                }
            }
        } else {
            //error_log("Plugin already installed and active: $plugin_slug");
        }
    }
}
add_filter('all_plugins', 'rename_import_demo_plugins');
function rename_import_demo_plugins($plugins) {
    if (isset($plugins['all-in-one-wp-migration/all-in-one-wp-migration.php'])) {
        $plugins['all-in-one-wp-migration/all-in-one-wp-migration.php']['Name'] = 'Import Demo';
        $plugins['all-in-one-wp-migration/all-in-one-wp-migration.php']['Description'] = str_replace('All-in-One WP Migration', 'Import Demo', $plugins['all-in-one-wp-migration/all-in-one-wp-migration.php']['Description']);
    }
    if (isset($plugins['all-in-one-wp-migration-s3-extension/all-in-one-wp-migration-s3-extension.php'])) {
        $plugins['all-in-one-wp-migration-s3-extension/all-in-one-wp-migration-s3-extension.php']['Name'] = 'Import Demo Pro';
        $plugins['all-in-one-wp-migration-s3-extension/all-in-one-wp-migration-s3-extension.php']['Description'] = str_replace('All-in-One WP Migration S3 Extension', 'Import Demo Pro', $plugins['all-in-one-wp-migration-s3-extension/all-in-one-wp-migration-s3-extension.php']['Description']);
    }
    return $plugins;
}
add_filter('gettext', 'rename_all_in_one_texts', 10, 3);

function rename_all_in_one_texts($translated_text, $untranslated_text, $domain) {
    if (strpos($translated_text, 'All-in-One WP Migration') !== false) {
        $translated_text = str_replace('All-in-One WP Migration', 'Import Demo', $translated_text);
    }

    if (strpos($translated_text, 'All-in-One WP Migration S3 Extension') !== false) {
        $translated_text = str_replace('All-in-One WP Migration S3 Extension', 'Import Demo Pro', $translated_text);
    }
    return $translated_text;
}
add_action('admin_menu', 'rename_all_in_one_wp_migration_menu', 999);
function rename_all_in_one_wp_migration_menu() {
    global $menu, $submenu;
    foreach ($menu as $key => $value) {
        if ($value[2] === 'ai1wm_export') {
            $menu[$key][0] = 'Import Demo';
        }
    }
    if (isset($submenu['ai1wm_export'])) {
        foreach ($submenu['ai1wm_export'] as $key => $value) {
            if ($value[0] === 'Backups') {
                $submenu['ai1wm_export'][$key][0] = 'Import Demo Backups';
            }
            if ($value[0] === 'Import') {
                $submenu['ai1wm_export'][$key][0] = 'Import Demo';
            }
        }
    }
}