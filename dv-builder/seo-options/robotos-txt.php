<?php
// Add admin menu
add_action('admin_menu', function () {
    add_options_page('Robots.txt Editor', 'Robots.txt Editor', 'manage_options', 'custom-robots-editor', 'crte_render_editor_page');
});

// Hook into virtual robots.txt output
add_filter('robots_txt', function ($output, $public) {
    $saved = get_option('crte_robots_txt');
    return $saved ?: $output;
}, 10, 2);

// Admin page content
function crte_render_editor_page() {
    if (isset($_POST['crte_robots_txt'])) {
        check_admin_referer('crte_save_robots', 'crte_nonce');

        $robots_content = wp_unslash($_POST['crte_robots_txt']);
        update_option('crte_robots_txt', $robots_content);

        // Only create/update file if radio is checked
        if (isset($_POST['crte_create_file']) && $_POST['crte_create_file'] === 'yes') {
            crte_create_robots_file($robots_content);
        }

        echo '<div class="notice notice-success"><p>Robots.txt saved successfully!</p></div>';
    }

    $content = get_option('crte_robots_txt', "User-agent: *\nDisallow: /wp-admin/\nAllow: /wp-admin/admin-ajax.php");
    $file_exists = file_exists(ABSPATH . 'robots.txt');
    ?>
    <div class="wrap">
        <h1>Robots.txt Editor</h1>
        <form method="post">
            <?php wp_nonce_field('crte_save_robots', 'crte_nonce'); ?>
            <textarea name="crte_robots_txt" rows="15" style="width:100%; font-family:monospace;"><?php echo esc_textarea($content); ?></textarea>

            <h3>Physical File Option:</h3>
            <label>
                <input type="radio" name="crte_create_file" value="yes" checked>
                Update a physical robots.txt file in the root directory
            </label>
            <br>
            <small style="color: gray;">(Currently: <?php echo $file_exists ? '<strong style="color: green;">Exists</strong>' : '<strong style="color: red;">Missing</strong>'; ?>)</small>

            <p><input type="submit" class="button button-primary" value="Save Changes"></p>
        </form>
    </div>
    <?php
}

// File creation/updating function
function crte_create_robots_file($content) {
    $robots_path = ABSPATH . 'robots.txt';
    $result = file_put_contents($robots_path, $content);

    if ($result === false) {
        add_action('admin_notices', function () {
            echo '<div class="notice notice-error"><p>Failed to write to robots.txt file. Please check file permissions on the root directory.</p></div>';
        });
    } else {
        add_action('admin_notices', function () {
            echo '<div class="notice notice-success"><p>robots.txt file was successfully created or updated.</p></div>';
        });
    }
}