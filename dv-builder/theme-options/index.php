<?php 

function theme_options() {

    add_menu_page('Theme page title', 'DV Theme', 'manage_options', 'theme-options', 'dv_options', '', 3);



    add_submenu_page('theme-options', 'Pre-built websites', 'Pre-built websites', 'manage_options', 'pre-built-websites', 'pre_built_websites');

    add_submenu_page('theme-options', 'Theme Options', 'Theme Options', 'manage_options', 'theme-settings', 'theme_r_options');

    add_submenu_page('theme-options', 'Maintenance Mode', 'Maintenance Mode', 'manage_options', 'maintenance-mode', 'maintenance_mode');



        add_submenu_page('theme-options', 'Import Data', 'Import Data', 'manage_options', 'import_data', 'import_data');

    add_submenu_page('theme-options', 'License Key', 'License Key', 'manage_options', 'license-key', 'license_key');

    add_submenu_page('theme-options', 'Manual & Support', 'Manual & Support', 'manage_options', 'manual-support', 'manual_support');

}



add_action('admin_menu', 'theme_options');





add_action( 'admin_init', 'theme_options_init' );

function theme_options_init(){

 register_setting( 'sample_options', 'sample_theme_options');

} 



function import_data() {

?>

<link rel='stylesheet' href='<?php echo get_template_directory_uri() . '/dv-builder/assets/css/theme-options.css'; ?>' />

<div class="theme_option">

    

    <header class="dashboard-head">

        <div class="menu-wrapper">

            <ul class="dashboard-menu">

                <li><a href="<?php echo get_site_url(); ?>/wp-admin/admin.php?page=theme-options"><span class="mfn-icon"><img src="https://dvthemes.com/assets/theme-images/icons/dashboard-icon.svg" /></span>Dashboard</a></li>

                <li><a href="<?php echo get_site_url(); ?>/wp-admin/admin.php?page=pre-built-websites"><span class="mfn-icon"><img src="https://dvthemes.com/assets/theme-images/icons/websites-icon.svg" /></span>Pre-built Websites</a></li>

                <li><a href="<?php echo get_site_url(); ?>/wp-admin/admin.php?page=theme-settings"><span class="mfn-icon"><img src="https://dvthemes.com/assets/theme-images/icons/theme-options-icon.svg" /></span>Theme Options</a></li>

                <li><a href="<?php echo get_site_url(); ?>/wp-admin/admin.php?page=maintenance-mode"><span class="mfn-icon"><img src="https://dvthemes.com/assets/theme-images/icons/maintenance-icon.svg" /></span>Maintenance Mode</a></li>

                <li class="active"><a href="<?php echo get_site_url(); ?>/wp-admin/admin.php?page=import_data"><span class="mfn-icon"><img src="https://dvthemes.com/assets/theme-images/icons/support-icon.svg" /></span>Import Data</a></li>

                <li><a href="<?php echo get_site_url(); ?>/wp-admin/admin.php?page=manual-support"><span class="mfn-icon"><img src="https://dvthemes.com/assets/theme-images/icons/support-icon.svg" /></span>Support</a></li>

                <li><a href="<?php echo get_site_url(); ?>/wp-admin/admin.php?page=license-key"><span class="mfn-icon"><img style="width: 100%;" src="https://dvthemes.com/assets/theme-images/icons/key-icon.svg" /></span>License Key</a></li>

            </ul>

        </div>

    </header>
  <?php 
$saved_license_key = get_option('MaximusGrier');

if (empty($saved_license_key)) {
    echo '<div class="dv_error">ACTIVATE YOUR LICENSE KEY FIRST</div>';
}else{ ?> 
    <div class="tabPanel">
        <div id="general_options" class="cstmTab" style="display:block;">
            <h2>Import Data</h2>
<?php 

$stored_theme_type = get_option('MaximusGrier_theme_type');
if($stored_theme_type=='informative_themes'){
    $plugin_map = [
    'dv-builder'              => 'getwid/getwid.php',
    'contact-form'    => 'contact-form-7/wp-contact-form-7.php',
];
}elseif($stored_theme_type=='ecommerce_themes'){
$plugin_map = [
    'dv-builder'              => 'getwid/getwid.php',
    'contact-form'    => 'contact-form-7/wp-contact-form-7.php',
     'woocommerce'       => 'woocommerce/woocommerce.php',
    'dv-wootoolkit'     => 'dv-wootoolkit/simple-variation-swatches.php',
];
    
}


?>      

<div id="plugins_rcmnd" class="card-content cnt_clrd">
    <h3>Plugin Recommendation!</h3>
    <table class="form-table">
        <tbody>
            <?php
            foreach ($plugin_map as $slug => $plugin_file) {
                $plugin_path = WP_PLUGIN_DIR . '/' . $plugin_file;
                $is_active = is_plugin_active($plugin_file) && file_exists($plugin_path);

                echo '<tr valign="top" class="brder_bot">';
                echo '<th><h4>' . esc_html(str_replace(['-', '.zip'], [' ', ''], $slug)) . '</h4></th>';
                echo '<td>';
                if ($is_active) {
                    echo '<span style="color: green;">Activated</span>';
                } elseif (file_exists($plugin_path)) {
                    echo '<span style="color: orange;">Installed but not active</span>';
                } else {
                    echo '<span style="color: red;">Not Installed</span>';
                }
                echo '</td>';
                echo '</tr>';
            }
            ?>
        </tbody>
    </table>

    <button id="bulk-install-btn" class="button button-primary">Bulk Install & Activate</button>
</div>

<script type="text/javascript">
jQuery(document).ready(function ($) {
    $('#bulk-install-btn').on('click', function (e) {
        e.preventDefault();
        var button = $(this);
        button.text('Installing...').prop('disabled', true);

        $.ajax({
            type: 'POST',
            url: dv_ajax_object.ajax_url,
            data: {
                action: 'dv_bulk_install_activate_plugin',
                theme_type: '<?php echo esc_js($stored_theme_type); ?>',
                security: dv_ajax_object.nonce
            },
            success: function (response) {
                if (response.success) {
                    location.reload();
                } else {
                    alert(response.data || 'Bulk installation failed.');
                    button.text('Failed').prop('disabled', false);
                }
            },
            error: function () {
                alert('AJAX error.');
                button.text('Failed').prop('disabled', false);
            }
        });
    });
});
</script>
            
            <div id="license_key" class="card-content cnt_clrd">
                <h3>Upload Demo File</h3>
                <form id="dv-import-form" enctype="multipart/form-data">
                    <div class="card-box">
                        <input type="file" name="import_file" id="import_file" required />
                        <input type="submit" class="button button-primary btn" id="start-import-btn" value="Start Import">
                    </div>
                </form>
                <div id="import-loader" style="display:none; margin-top:10px;">
                    <span>Importing, please wait...</span>
                </div>
                
                <div id="import-result" style="margin-top:20px;"></div>
            </div>

            <script>
jQuery(document).ready(function($) {
    if (typeof window.dvPluginsActive !== 'undefined' && window.dvPluginsActive === false) {
        $('#start-import-btn')
            .prop('disabled', true)
            .val('Required Plugins')
            .attr('title', 'Please install and activate all required plugins.');
    }
});
</script>
        </div>
    </div>
<?php } ?>

</div>

<?php

wp_enqueue_script(

    'dv-importer-js',

    get_template_directory_uri() . '/dv-builder/assets/js/dv-importer.js',

    ['jquery'],

    null,

    true

);



wp_localize_script('dv-importer-js', 'dv_ajax', [

    'ajax_url' => admin_url('admin-ajax.php'),

    'nonce'    => wp_create_nonce('dv_import_nonce')

]);



 } 



add_action('wp_ajax_dv_handle_import', 'dv_importer_handle_ajax');



function dv_importer_handle_ajax() {

    check_ajax_referer('dv_import_nonce', 'security');

    if (!current_user_can('manage_options')) wp_send_json_error('Unauthorized');



    if (!isset($_FILES['file']) || $_FILES['file']['error'] !== 0) wp_send_json_error('File upload failed.');


    if (class_exists('WooCommerce')) {
        $woocommerce_pages = [
            'woocommerce_shop_page_id',
            'woocommerce_cart_page_id',
            'woocommerce_checkout_page_id',
            'woocommerce_myaccount_page_id'
        ];

        foreach ($woocommerce_pages as $option_name) {
            $page_id = get_option($option_name);
            if ($page_id && get_post($page_id)) {
                wp_delete_post($page_id, true); // true = force delete
                delete_option($option_name);
            }
        }
    }

    global $wpdb;

    $json = file_get_contents($_FILES['file']['tmp_name']);

    $entries = json_decode($json, true);

    if (!$entries) wp_send_json_error('Invalid JSON file.');



    $logs = [];

    $totalTables = 0;

    $processedTables = 0;



    foreach ($entries as $entry) {

        if (!isset($entry['type']) || $entry['type'] !== 'table') continue;

        $totalTables++;

    }



    foreach ($entries as $entry) {

        if (!isset($entry['type']) || $entry['type'] !== 'table') continue;



        $table_name = $entry['name'];

        $rows = $entry['data'] ?? [];

        $site_table = $wpdb->prefix . str_replace('wpdk_', '', $table_name);



        $imported = 0;

        foreach ($rows as $row) {

            $primary = array_key_first($row);

            $exists = $wpdb->get_var($wpdb->prepare(

                "SELECT `$primary` FROM `$site_table` WHERE `$primary` = %s",

                $row[$primary]

            ));

            if (!$exists) {

                $wpdb->insert($site_table, $row);

                $imported++;

            }

        }



        $processedTables++;

        $percent = intval(($processedTables / $totalTables) * 100);

        $logs[] = "Imported $imported row(s) into <code>$site_table</code> ($percent%)";

    }


    $cart_page     = get_page_by_path('cart');
    $checkout_page = get_page_by_path('checkout');
    $account_page  = get_page_by_path('my-account');

    if ($cart_page) {
        update_option('woocommerce_cart_page_id', $cart_page->ID);
    }

    if ($checkout_page) {
        update_option('woocommerce_checkout_page_id', $checkout_page->ID);
    }

    if ($account_page) {
        update_option('woocommerce_myaccount_page_id', $account_page->ID);
    }

     $page = get_page_by_title('Home');

      if ($page) {
         $page_id = $page->ID;
   
        update_option('show_on_front', 'page');
        update_option('page_on_front', $page_id);
      }


    $folder = pathinfo($_FILES['file']['name'], PATHINFO_FILENAME);

    $new_options = [

        'logourl'      => 'https://www.dvthemes.com/demos_logo/'.$folder.'/logo.png',

        'titleOpt'     => 'DV THEMES',

        'tagline'      => 'Everything You Need. All in One Place.',

        'preLoader'    => '0',

        'loaderCode'   => 'whirlpool',

        'metaOptions'  => '0',

        'console_meta' => '',

        'head_script'  => '',

        'body_script'  => '',

    ];

    update_option('sample_theme_options', $new_options);

    // Hardcoded update of site title and tagline
    update_option('blogname', 'DV THEMES');
    update_option('blogdescription', 'Everything You Need. All in One Place.');

    
    // Helper function to download and insert image into media library
    function media_sideload_image_to_library($url, $filename) {
        require_once ABSPATH . 'wp-admin/includes/file.php';
        require_once ABSPATH . 'wp-admin/includes/media.php';
        require_once ABSPATH . 'wp-admin/includes/image.php';

        $tmp = download_url($url);
        if (is_wp_error($tmp)) {
            return false;
        }

        $file_array = [
            'name'     => $filename,
            'tmp_name' => $tmp,
        ];

        // 0 means unattached post
        $id = media_handle_sideload($file_array, 0);

        if (is_wp_error($id)) {
            @unlink($tmp);
            return false;
        }

        return $id;
    }
    // Logo
    $logo_url = 'https://www.dvthemes.com/demos_logo/'.$folder.'/logo.png';
    $logo_id = media_sideload_image_to_library($logo_url, 'logo.png');
    if ($logo_id) {
        update_option('site_logo', $logo_id);
    }

    // Favicon
    $favicon_url = 'https://www.dvthemes.com/demos_logo/'.$folder.'/favicon.png';
    $favicon_id = media_sideload_image_to_library($favicon_url, 'favicon.png');
    if ($favicon_id) {
        update_option('site_icon', $favicon_id);
    }


 // if (get_option('permalink_structure') !== '/%postname%/') {
 //        update_option('permalink_structure', '/%postname%/');
 //        // Flush rewrite rules to apply changes
 //        flush_rewrite_rules();
 //    }

    // === Handle Related Assets ===
global $wp_rewrite; 

//Write the rule
$wp_rewrite->set_permalink_structure('/%postname%/'); 

//Set the option
update_option( "rewrite_rules", FALSE ); 

//Flush the rules and tell it to write htaccess
$wp_rewrite->flush_rules( true );
 

    $remote_base = "https://dvthemes.com/demos_files/$folder";


    require_once ABSPATH . 'wp-admin/includes/file.php';

    $child_theme_dir = get_stylesheet_directory();


    require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';

    require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader-skin.php';

    WP_Filesystem();



    class Silent_Skin extends WP_Upgrader_Skin {

        public function feedback($string, ...$args) { /* suppress output */ }

    }

    $zip_url_main = "$remote_base/2025.zip";

    $zip_filename_main = basename($zip_url_main, '.zip'); // "2025"

    $upload_dir = wp_upload_dir();

    $dest_folder_main = trailingslashit($upload_dir['basedir']) . $zip_filename_main;



    if (!file_exists($dest_folder_main)) {

        mkdir($dest_folder_main, 0755, true);

    }



    $skin = new Silent_Skin();

    $upgrader = new WP_Upgrader($skin);

    $upgrader->run([

        'package' => $zip_url_main,

        'destination' => $dest_folder_main,

        'clear_destination' => false,

        'abort_if_destination_exists' => false,

        'hook_extra' => [],

    ]);

        if (!is_wp_error($result)) {

            $logs[] = "<strong style='color:green;'>2025.zip extracted to <code>wp-content/uploads/{$zip_filename}/</code>.</strong>";

        } else {

            $logs[] = "<span style='color:red;'>Error extracting 2025.zip: " . $result->get_error_message() . "</span>";

        }

        die;

}

function dv_options() {

?>

<link rel='stylesheet' href='<?php echo get_template_directory_uri() . '/dv-builder/assets/css/theme-options.css'; ?>' />

<div class="theme_option">

    

    <header class="dashboard-head">

        <div class="menu-wrapper">

            <ul class="dashboard-menu">

                <li class="active"><a href="<?php echo get_site_url(); ?>/wp-admin/admin.php?page=theme-options"><span class="mfn-icon"><img src="https://dvthemes.com/assets/theme-images/icons/dashboard-icon.svg" /></span>Dashboard</a></li>

                <li><a href="<?php echo get_site_url(); ?>/wp-admin/admin.php?page=pre-built-websites"><span class="mfn-icon"><img src="https://dvthemes.com/assets/theme-images/icons/websites-icon.svg" /></span>Pre-built Websites</a></li>

                <li><a href="<?php echo get_site_url(); ?>/wp-admin/admin.php?page=theme-settings"><span class="mfn-icon"><img src="https://dvthemes.com/assets/theme-images/icons/theme-options-icon.svg" /></span>Theme Options</a></li>

                <li><a href="<?php echo get_site_url(); ?>/wp-admin/admin.php?page=maintenance-mode"><span class="mfn-icon"><img src="https://dvthemes.com/assets/theme-images/icons/maintenance-icon.svg" /></span>Maintenance Mode</a></li>

                <li><a href="<?php echo get_site_url(); ?>/wp-admin/admin.php?page=import_data"><span class="mfn-icon"><img src="https://dvthemes.com/assets/theme-images/icons/support-icon.svg" /></span>Import Data</a></li>

                <li><a href="<?php echo get_site_url(); ?>/wp-admin/admin.php?page=manual-support"><span class="mfn-icon"><img src="https://dvthemes.com/assets/theme-images/icons/support-icon.svg" /></span>Support</a></li>

                <li><a href="<?php echo get_site_url(); ?>/wp-admin/admin.php?page=license-key"><span class="mfn-icon"><img style="width: 100%;" src="https://dvthemes.com/assets/theme-images/icons/key-icon.svg" /></span>License Key</a></li>

            </ul>

        </div>

    </header>
     <?php 
$saved_license_key = get_option('MaximusGrier');

if (empty($saved_license_key)) {
    echo '<div class="dv_error">ACTIVATE YOUR LICENSE KEY FIRST</div>';
}
?>


<?php

$api_url = 'https://dvthemes.com/backend/dv-themes-dashboard/api.php';

$response = wp_remote_get($api_url);

$uploaded_data = null;

if (is_wp_error($response)) {

    error_log('API request failed: ' . $response->get_error_message());

} else {

    $data = json_decode(wp_remote_retrieve_body($response), true);



    if (isset($data['data'])) {

        $uploaded_data = $data['data'];

    }

}

?>

<div class="tabPanel">

    <div id="general_options" class="cstmTab" style="display:block;">

        <h2>Dashboard</h2>

        <div class="dv_dash_box">

            <div class="card-content blue_box">

                <div class="cnt_f">

                    <h3><?php echo $uploaded_data['dv_title'];?></h3>
            <p>

           <?php  echo isset($uploaded_data['description']) ? esc_html($uploaded_data['description'])  : 'Let us guide you through this process. Promise, it won\'t take more than a couple of seconds.'; ?>

            </p>



            <a href="<?php echo $uploaded_data['button_link']?>" class="btn"><?php echo $uploaded_data['button_text'];?></a>

            </div>

          </div>

          <div class="img_box s_box">

                <img src="<?php 

                    echo isset($uploaded_data['image']) ? esc_url($uploaded_data['image']) : 'https://api.muffingroup.com/promo/images/join-fb-community.jpg'; ?>" alt="Promotional Image">

           </div>

       </div>

   </div>

</div>



    

</div>

<?php } 



function pre_built_websites() {

?>

<link rel='stylesheet' href='<?php echo get_template_directory_uri() . '/dv-builder/assets/css/theme-options.css'; ?>' />

<div class="theme_option">

    

    <header class="dashboard-head">

        <div class="menu-wrapper">

            <ul class="dashboard-menu">

                <li><a href="<?php echo get_site_url(); ?>/wp-admin/admin.php?page=theme-options"><span class="mfn-icon"><img src="https://dvthemes.com/assets/theme-images/icons/dashboard-icon.svg" /></span>Dashboard</a></li>

                <li class="active"><a href="<?php echo get_site_url(); ?>/wp-admin/admin.php?page=pre-built-websites"><span class="mfn-icon"><img src="https://dvthemes.com/assets/theme-images/icons/websites-icon.svg" /></span>Pre-built Websites</a></li>

                <li><a href="<?php echo get_site_url(); ?>/wp-admin/admin.php?page=theme-settings"><span class="mfn-icon"><img src="https://dvthemes.com/assets/theme-images/icons/theme-options-icon.svg" /></span>Theme Options</a></li>

                <li><a href="<?php echo get_site_url(); ?>/wp-admin/admin.php?page=maintenance-mode"><span class="mfn-icon"><img src="https://dvthemes.com/assets/theme-images/icons/maintenance-icon.svg" /></span>Maintenance Mode</a></li>

                <li><a href="<?php echo get_site_url(); ?>/wp-admin/admin.php?page=import_data"><span class="mfn-icon"><img src="https://dvthemes.com/assets/theme-images/icons/support-icon.svg" /></span>Import Data</a></li>

                <li><a href="<?php echo get_site_url(); ?>/wp-admin/admin.php?page=manual-support"><span class="mfn-icon"><img src="https://dvthemes.com/assets/theme-images/icons/support-icon.svg" /></span>Support</a></li>

                <li><a href="<?php echo get_site_url(); ?>/wp-admin/admin.php?page=license-key"><span class="mfn-icon"><img style="width: 100%;" src="https://dvthemes.com/assets/theme-images/icons/key-icon.svg" /></span>License Key</a></li>

            </ul>

        </div>

    </header>

     <?php 
$saved_license_key = get_option('MaximusGrier');

if (empty($saved_license_key)) {
    echo '<div class="dv_error">ACTIVATE YOUR LICENSE KEY FIRST</div>';
}
?>


    <?php

$api_url_pre_built = 'https://dvthemes.com/backend/dv-themes-dashboard/pre_built_api.php';

$built_response = wp_remote_get($api_url_pre_built);

$built_uploaded_data = null;

if (is_wp_error($built_response)) {

    error_log('API request failed: ' . $built_response->get_error_message());

} else {

    $pre_built_data = json_decode(wp_remote_retrieve_body($built_response), true);



    if (!empty($pre_built_data['pre_built_data']) && is_array($pre_built_data['pre_built_data'])) {

        $built_uploaded_data = $pre_built_data['pre_built_data'];

    }

}

?>



    <div class="tabPanel">

        <div id="skins" class="cstmTab" style="display:block;">

            <h2>Pre-built Websites</h2>

            <div class="items">

                 <?php if (!empty($built_uploaded_data) && is_array($built_uploaded_data)): ?>

                <?php foreach ($built_uploaded_data as $built_uploaded_datas): ?>

                    <?php

                        $title = $built_uploaded_datas['title'] ?? '';

                        $web_link = $built_uploaded_datas['web_link'] ?? '#';

                        $thumbnail = $built_uploaded_datas['thumbnail'] ?? '';

                    ?>

                    <div class="item skin wordpress">

                        <a href="<?php echo htmlspecialchars($web_link); ?>" target="_blank">

                            <div class="shadow">

                                <p class="image">

                                    <img src="<?php echo htmlspecialchars($thumbnail); ?>" alt="<?php echo htmlspecialchars($title); ?>">

                                </p>

                            </div>

                            <span><?php echo htmlspecialchars($title); ?></span>

                        </a>

                    </div>

                <?php endforeach; ?>

            <?php endif; ?>

            

            </div>

        </div>

    </div>

    

</div>

    

<?php } 



function theme_r_options() {

    require get_template_directory() . '/dv-builder/theme-options/custom-option.php';   

} 





function maintenance_mode() {

    global $select_options; if ( ! isset( $_REQUEST['settings-updated'] ) ) $_REQUEST['settings-updated'] = false; 

?>

<link rel='stylesheet' href='<?php echo get_template_directory_uri() . '/dv-builder/assets/css/theme-options.css'; ?>' />



<div class="theme_option">

    <header class="dashboard-head">

        <div class="menu-wrapper">

            <ul class="dashboard-menu">

                <li><a href="<?php echo get_site_url(); ?>/wp-admin/admin.php?page=theme-options"><span class="mfn-icon"><img src="https://dvthemes.com/assets/theme-images/icons/dashboard-icon.svg" /></span>Dashboard</a></li>

                <li><a href="<?php echo get_site_url(); ?>/wp-admin/admin.php?page=pre-built-websites"><span class="mfn-icon"><img src="https://dvthemes.com/assets/theme-images/icons/websites-icon.svg" /></span>Pre-built Websites</a></li>

                <li><a href="<?php echo get_site_url(); ?>/wp-admin/admin.php?page=theme-settings"><span class="mfn-icon"><img src="https://dvthemes.com/assets/theme-images/icons/theme-options-icon.svg" /></span>Theme Options</a></li>

                <li class="active"><a href="<?php echo get_site_url(); ?>/wp-admin/admin.php?page=maintenance-mode"><span class="mfn-icon"><img src="https://dvthemes.com/assets/theme-images/icons/maintenance-icon.svg" /></span>Maintenance Mode</a></li>

                <li><a href="<?php echo get_site_url(); ?>/wp-admin/admin.php?page=import_data"><span class="mfn-icon"><img src="https://dvthemes.com/assets/theme-images/icons/support-icon.svg" /></span>Import Data</a></li>

                <li><a href="<?php echo get_site_url(); ?>/wp-admin/admin.php?page=manual-support"><span class="mfn-icon"><img src="https://dvthemes.com/assets/theme-images/icons/support-icon.svg" /></span>Support</a></li>

                <li><a href="<?php echo get_site_url(); ?>/wp-admin/admin.php?page=license-key"><span class="mfn-icon"><img style="width: 100%;" src="https://dvthemes.com/assets/theme-images/icons/key-icon.svg" /></span>License Key</a></li>

            </ul>

        </div>

    </header>


 

    <?php if ( false !== $_REQUEST['settings-updated'] ) : ?>

        <div class="saved_alert">

            <strong><?php _e( 'Options saved', 'customtheme' ); ?></strong>

        </div>

    <?php endif; ?> 



    <form method="post" action="options.php">

        <?php 

            settings_fields( 'sample_options' ); $options = get_option( 'sample_theme_options' ); 

            if(function_exists( 'wp_enqueue_media' )){

                wp_enqueue_media();

            }else{

                wp_enqueue_style('thickbox');

                wp_enqueue_script('media-upload');

                wp_enqueue_script('thickbox');

            }

        ?>

         <?php 
$saved_license_key = get_option('MaximusGrier');

if (empty($saved_license_key)) {
    echo '<div class="dv_error">ACTIVATE YOUR LICENSE KEY FIRST</div>';
}else{ ?> 

    <div class="tabPanel">

            <div id="under_construction" class="cstmTab" style="display:block;">

                <h2>Maintenance Modes</h2>

                <table>

                    <tbody>

                        <tr>

                            <th style="width:20%;">Maintenance Mode</th>

                            <td>

                                <div class="switch-options">

                                    <?php 

                                        if(empty($options['dv_mode'])){ 

                                            $dv_mode = 0; 

                                        }else{

                                            $dv_mode = $options['dv_mode'];  

                                        } 

                                    ?>

                                    <label class="cb-enable <?php if($dv_mode == 1){ echo 'selected'; } ?>">Yes</label>

                                    <label class="cb-disable <?php if($dv_mode == 0){ echo 'selected'; } ?>">No</label>

                                    <input type="hidden" class="checkbox checkbox-input" id="dv_mode" name="sample_theme_options[dv_mode]" value="<?php echo $dv_mode; ?>">

                                </div>

                                

                            </td>

                        </tr>

                        <tr>

                            <th style="width:20%;">Edit Maintenance Page</th>

                            <td>                                <a href="<?php echo get_site_url(); ?>/wp-admin/edit.php?s=Under+Construction&post_status=all&post_type=page&action=-1&m=0&paged=1&action2=-1" target="blank" class="btn" style="margin: 0;">Click here.</a> </td>

                            </td>

                        </tr>

                        <!--<tr>

                            <th style="width:20%;">Light Mode</th>

                            <td>

                                <div class="switch-options">

                                    <?php 

                                        if (!isset($options['dv_mode']) || empty($options['dv_mode'])) {

                                            $dl_mode = 0;

                                        }else{

                                            $dl_mode = $options['dl_mode'];  

                                        }

                                    ?>

                                    <label class="cb-enable <?php if($dl_mode == 1){ echo 'selected'; } ?>">On</label>

                                    <label class="cb-disable <?php if($dl_mode == 0){ echo 'selected'; } ?>">Off</label>

                                    <input type="hidden" class="checkbox checkbox-input" id="dl_mode" name="sample_theme_options[dl_mode]" value="<?php echo $dl_mode; ?>">

                                </div>

                            </td>

                        </tr>

                        <tr class="img">

                            <th>Background Image:</th>

                            <td>

                                <?php if(empty($options['dv_mode_img'])){ ?>

                                    <input readonly class="header_logo_url" type="text" name="sample_theme_options[dv_mode_img]" value="">

                                <?php

                                }else{ ?>

                                    <input readonly class="header_logo_url" type="hidden" name="sample_theme_options[dv_mode_img]" value="<?php esc_attr_e( $options['dv_mode_img'] ); ?>">

                                    <img class="header_logo" src="<?php esc_attr_e( $options['dv_mode_img'] ); ?>" height="50" width="50" />

                                <?php } ?><br>

                                <a href="#" class="header_logo_upload btn">Upload</a>

                            </td>

                        </tr>

                        <tr>

                            <th>Title</th>

                            <td><input id="sample_theme_options[under_cons_hd]" type="text" name="sample_theme_options[under_cons_hd]" value="<?php if(!empty($options['under_cons_hd'])){ esc_attr_e( $options['under_cons_hd'] ); } ?>"></td>

                        </tr>

                        <tr>

                            <th>Text</th>

                            <td>

                                <textarea id="sample_theme_options[under_cons_cnt]" class="large-text" name="sample_theme_options[under_cons_cnt]"><?php if(!empty($options['under_cons_cnt'])){ echo esc_textarea( $options['under_cons_cnt'] ); } ?></textarea>

                            </td>

                        </tr>

                        <tr>

                            <th style="width:20%;">Social Icons</th>

                            <td>

                                <div class="switch-options">

                                    <?php 

                                        if(empty($options['socMedia'])){ 

                                            $socMedia = 0; 

                                        }else{

                                            $socMedia = $options['socMedia'];  

                                        } 

                                    ?>

                                    <label class="cb-enable <?php if($socMedia == 1){ echo 'selected'; } ?>">Yes</label>

                                    <label class="cb-disable <?php if($socMedia == 0){ echo 'selected'; } ?>">No</label>

                                    <input type="hidden" class="checkbox checkbox-input" id="socMedia" name="sample_theme_options[socMedia]" value="<?php echo $socMedia; ?>">

                                </div>

                                <p>You can include the social links by <a href="<?php echo get_site_url(); ?>/wp-admin/customize.php" target="blank">clicking here</a> and go to the Social Icons tab. </p>

                            </td>

                        </tr>-->

                    </tbody>

                </table> 

            </div>

        </div>

        <input class="btn" type="submit" value="<?php _e( 'Save Options', 'customtheme' ); ?>" />

    </form>

</div>
<?php } ?>

        



<script>

    jQuery(document).ready(function($) {

        jQuery('.switch-options label.cb-enable').click(function(){

            jQuery(this).closest('.switch-options').find('label').removeClass('selected');

            jQuery(this).addClass('selected'); 

            jQuery(this).closest('.switch-options').find('.checkbox').val(1);

        });

        jQuery('.switch-options label.cb-disable').click(function(){

            jQuery(this).closest('.switch-options').find('label').removeClass('selected');

            jQuery(this).addClass('selected'); 

            jQuery(this).closest('.switch-options').find('.checkbox').val(0);

        });



        jQuery('.tabPanel .cstmTab:first').show();



        jQuery('.header_logo_upload').click(function(e) {

            e.preventDefault();

            var custom_uploader = wp.media({

                title: 'Custom Image',

                button: { text: 'Upload Image' },

                multiple: false 

            }).on('select', function() {

                var attachment = custom_uploader.state().get('selection').first().toJSON();

                jQuery('.header_logo').attr('src', attachment.url);

                jQuery('.header_logo_url').val(attachment.url);

            }).open();

        });

        

    });

( function() { 'use strict';

  let images = document.querySelectorAll('img[data-src]');

              

  document.addEventListener('DOMContentLoaded', onReady);

  function onReady() {

    // Show above-the-fold images first

    showImagesOnView();



    // scroll listener

    window.addEventListener( 'scroll', showImagesOnView, false );

  }

  

  // Show the image if reached on viewport

  function showImagesOnView( e ) {

    

    for( let i of images ) {

      if( i.getAttribute('src') ) { continue; } // SKIP if already displayed

      

      // Compare the position of image and scroll

      let bounding = i.getBoundingClientRect();

      let isOnView = bounding.top >= 0 &&

      bounding.left >= 0 &&

      bounding.bottom <= (window.innerHeight || document.documentElement.clientHeight) &&

      bounding.right <= (window.innerWidth || document.documentElement.clientWidth);

      

      if( isOnView ) {

        i.setAttribute( 'src', i.dataset.src );

        if( i.getAttribute('data-srcset') ) {

          i.setAttribute( 'srcset', i.dataset.srcset );

        }

      }

    }

  }

              

})();

</script>

<?php } 

function manual_support() {

?>

<link rel='stylesheet' href='<?php echo get_template_directory_uri() . '/dv-builder/assets/css/theme-options.css'; ?>' />

<div class="theme_option">

    

    <header class="dashboard-head">

        <div class="menu-wrapper">

            <ul class="dashboard-menu">

                <li><a href="<?php echo get_site_url(); ?>/wp-admin/admin.php?page=theme-options"><span class="mfn-icon"><img src="https://dvthemes.com/assets/theme-images/icons/dashboard-icon.svg" /></span>Dashboard</a></li>

                <li><a href="<?php echo get_site_url(); ?>/wp-admin/admin.php?page=pre-built-websites"><span class="mfn-icon"><img src="https://dvthemes.com/assets/theme-images/icons/websites-icon.svg" /></span>Pre-built Websites</a></li>

                <li><a href="<?php echo get_site_url(); ?>/wp-admin/admin.php?page=theme-settings"><span class="mfn-icon"><img src="https://dvthemes.com/assets/theme-images/icons/theme-options-icon.svg" /></span>Theme Options</a></li>

                <li><a href="<?php echo get_site_url(); ?>/wp-admin/admin.php?page=maintenance-mode"><span class="mfn-icon"><img src="https://dvthemes.com/assets/theme-images/icons/maintenance-icon.svg" /></span>Maintenance Mode</a></li>

                <li><a href="<?php echo get_site_url(); ?>/wp-admin/admin.php?page=import_data"><span class="mfn-icon"><img src="https://dvthemes.com/assets/theme-images/icons/support-icon.svg" /></span>Import Data</a></li>

                <li class="active"><a href="<?php echo get_site_url(); ?>/wp-admin/admin.php?page=manual-support"><span class="mfn-icon"><img src="https://dvthemes.com/assets/theme-images/icons/support-icon.svg" /></span>Support</a></li>

                <li><a href="<?php echo get_site_url(); ?>/wp-admin/admin.php?page=license-key"><span class="mfn-icon"><img style="width: 100%;" src="https://dvthemes.com/assets/theme-images/icons/key-icon.svg" /></span>License Key</a></li>

            </ul>

        </div>

    </header>

     <?php 
$saved_license_key = get_option('MaximusGrier');

if (empty($saved_license_key)) {
    echo '<div class="dv_error">ACTIVATE YOUR LICENSE KEY FIRST</div>';
}
?>

    <div class="tabPanel">

        <div id="general_options" class="cstmTab" style="display:block;">

            <h2>Support</h2>

            <div class="card-content cnt_clrd">

                <div class="card-box">

                    <div class="support-item" style="border-right: 1px solid rgba(48, 64, 80, .1);">

                        <img src="https://dvthemes.com/assets/theme-images/icons/yes-icon.svg" />

                        <h5>Item support <span class="include">includes</span>: </h5>

                        <p>Responding to questions or problems regarding the item and its features.</p>

                        <p>Fixing bugs and reported issues.</p>

                        <p>Providing updates to ensure compatibility with new WordPress versions.</p>

                    </div>

                    <div class="support-item">

                        <img src="https://dvthemes.com/assets/theme-images/icons/no-icon.svg" />

                        <h5>Item support does <span class="include">not include</span>: </h5>

                        <p>Customization and installation services.</p>

                        <p>Support for third party software and plugins.</p>

                    </div>

                </div>

            </div>

            

            <div class="card-content blue_box">

                <div class="cnt_f">

                    <h3>Can't find <br> what you need? </h3>

                    <p>Submit a ticket and get help.</p>

                    <a target="_blank" href="https://dvthemes.com/dv_themes_user_dashboard/login_form.php" class="btn">Create a ticket</a>

                </div>

            </div>

            <!-- <form id="createTicketForm" style="display: none;">

                <label for="ticketDescription">Describe your Query:</label><br>

                <textarea id="ticketDescription" name="ticketDescription" rows="4" cols="50" required></textarea><br><br>

                <button type="submit" class="btn">Submit Ticket</button>

            </form> -->

            

        </div>

    </div>

    

</div>

<script type="text/javascript">

// document.addEventListener('DOMContentLoaded', function () {

//   const formElement = document.getElementById('createTicketForm');

//   if (formElement) {

//     formElement.addEventListener('submit', function (event) {

//       event.preventDefault();

//       const ticketDescription = document.getElementById('ticketDescription').value;

//       fetch(ajax_object.ajaxurl, {

//         method: 'POST',

//         headers: {

//           'Content-Type': 'application/x-www-form-urlencoded',

//         },

//         body: new URLSearchParams({

//           action: 'create_ticket',

//           ticket_description: ticketDescription,

//         }),

//       })

//       .then(response => response.json())

//       .then(data => {

//         alert(data.message);

//         document.getElementById('createTicketModal').style.display = 'none';

//       })

//       .catch(error => console.error('Error:', error));

//     });

//   } else {

//     console.error('Form element createTicketForm not found!');

//   }

// });

// document.addEventListener('DOMContentLoaded', function() {

//   document.getElementById('ticket_box').onclick = function() {

//     const modal = document.getElementById('createTicketForm');

//     if (modal.style.display === 'block') {

//       modal.style.display = 'none';  

//     } else {

//       modal.style.display = 'block'; 

//     }

//   };

//   document.querySelector('.close').onclick = function() {

//     document.getElementById('createTicketForm').style.display = 'none';

//   };

// });

</script>

<?php }



function license_key() {

?>

<link rel='stylesheet' href='<?php echo get_template_directory_uri() . '/dv-builder/assets/css/theme-options.css'; ?>' />

<div class="theme_option">

    

    <header class="dashboard-head">

        <div class="menu-wrapper">

            <ul class="dashboard-menu">

                <li><a href="<?php echo get_site_url(); ?>/wp-admin/admin.php?page=theme-options"><span class="mfn-icon"><img src="https://dvthemes.com/assets/theme-images/icons/dashboard-icon.svg" /></span>Dashboard</a></li>

                <li><a href="<?php echo get_site_url(); ?>/wp-admin/admin.php?page=pre-built-websites"><span class="mfn-icon"><img src="https://dvthemes.com/assets/theme-images/icons/websites-icon.svg" /></span>Pre-built Websites</a></li>

                <li><a href="<?php echo get_site_url(); ?>/wp-admin/admin.php?page=theme-settings"><span class="mfn-icon"><img src="https://dvthemes.com/assets/theme-images/icons/theme-options-icon.svg" /></span>Theme Options</a></li>

                <li><a href="<?php echo get_site_url(); ?>/wp-admin/admin.php?page=maintenance-mode"><span class="mfn-icon"><img src="https://dvthemes.com/assets/theme-images/icons/maintenance-icon.svg" /></span>Maintenance Mode</a></li>

                <li><a href="<?php echo get_site_url(); ?>/wp-admin/admin.php?page=import_data"><span class="mfn-icon"><img src="https://dvthemes.com/assets/theme-images/icons/support-icon.svg" /></span>Import Data</a></li>

                <li><a href="<?php echo get_site_url(); ?>/wp-admin/admin.php?page=manual-support"><span class="mfn-icon"><img src="https://dvthemes.com/assets/theme-images/icons/support-icon.svg" /></span>Support</a></li>

                <li class="active"><a href="<?php echo get_site_url(); ?>/wp-admin/admin.php?page=license-key"><span class="mfn-icon"><img style="width: 100%;" src="https://dvthemes.com/assets/theme-images/icons/key-icon.svg" /></span>License Key</a></li>

            </ul>

        </div>

    </header>

    

    <div class="tabPanel">
        <div id="general_options" class="cstmTab" style="display:block;">
            <h2>License Key</h2>

            <?php
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_license_key'])) {

                $license_key = sanitize_text_field($_POST['license_keyword']);
            
                $response = wp_remote_post('https://dvthemes.com/backend/dv-themes-dashboard/license_api.php', [
                    'method' => 'POST',
                    'body'   => [
                        'license_keyword' => $license_key,
                    ],
                ]);
            
                if (is_wp_error($response)) {
                    echo '<p>Error: Unable to connect to the server.</p>';
                } else {
                    $response_body = wp_remote_retrieve_body($response);
                    $response_data = json_decode($response_body, true);
            
                    if ($response_data['status'] === 'success') {
                        update_option('MaximusGrier', $license_key);
            
                        // Optional: Save theme_type in WP options as well
                        if (isset($response_data['theme_type'])) {
                            update_option('MaximusGrier_theme_type', sanitize_text_field($response_data['theme_type']));
                        }
            
                        echo '<p style="color: green;">' . esc_html($response_data['message']) . '</p>';
            
                        // Display the theme type
                        echo '<p>Theme Type: <strong>' . esc_html($response_data['theme_type']) . '</strong></p>';
                    } else {
                        echo '<p style="color: red;">' . esc_html($response_data['message']) . '</p>';
                    }
                }
            }

            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['dis_license_key'])) {
                delete_option('MaximusGrier');
                echo '<p class="dv_error">License key disconnected successfully.</p>';
            }
            $saved_license_key = get_option('MaximusGrier');
            if (!empty($saved_license_key)) {
                echo '<div id="license_key" class="card-content cnt_clrd">
                        <h3 style="color: green; font-weight: bold;">CONNECTED</h3>
                        <form method="POST">
                            <button type="submit" name="dis_license_key" class="btn">Disconnect</button>
                          </form>
                    </div>';
            } else {
                ?>
                <p class="dv_error">The key is not connected!</p>
                <form method="post" action="">
                    <div id="license_key" class="card-content cnt_clrd">
                        <div class="card-box">
                            <input type="text" name="license_keyword" id="license_keyword" placeholder="Enter Correct License Key">
                            <button type="submit" name="submit_license_key" class="btn">Submit Key</button>
                        </div>
                    </div>
                </form>
                <?php } ?>
        </div>
    </div>
</div>

<?php 
$saved_license_key = get_option('MaximusGrier');
if (!empty($saved_license_key)) {
    $pattern_folder = get_template_directory() . '/patterns';
    if (file_exists($pattern_folder) && count(glob("$pattern_folder/*")) > 0) {
    } else {
        require_once ABSPATH . 'wp-admin/includes/file.php';
        require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
        WP_Filesystem();
        class Silent_Skin extends WP_Upgrader_Skin {
            public function feedback($string, ...$args) {}
        }
        $skin = new Silent_Skin();
        $upgrader = new WP_Upgrader($skin);
        $zip_url = 'https://dvthemes.com/download_plugins/patterns.zip';

        if (!file_exists($pattern_folder)) {
            mkdir($pattern_folder, 0755, true);
        }

        $result = $upgrader->run([

            'package' => $zip_url,

            'destination' => $pattern_folder,

            'clear_destination' => false,

            'abort_if_destination_exists' => false,

            'hook_extra' => [],

        ]);

if (!is_wp_error($result)) {

    echo '<div id="pattern-success-msg" style="color: green; font-weight: bold;">Patterns have been installed successfully!</div>';

    echo '<script>

        setTimeout(function() {

            var msg = document.getElementById("pattern-success-msg");

            if (msg) {

                msg.style.display = "none";

            }

        }, 20000); // 20 seconds

    </script>';

} else {

            echo '<p style="color: red;">Error installing patterns: ' . $result->get_error_message() . '</p>';

        }

    }

}
}
?>