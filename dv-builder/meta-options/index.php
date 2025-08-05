<?php
remove_action('wp_head', '_wp_render_title_tag', 1);

// Add the meta box
function seo_meta_box() {
    add_meta_box(
        'seo_meta_box', 'SEO SETTINGS', 'seo_meta_box_callback', array('post', 'page'), 'normal', 'default'                        
    );
}
add_action('add_meta_boxes', 'seo_meta_box');

function seo_meta_box_callback($post) {
    wp_nonce_field('custom_meta_box_nonce', 'custom_meta_box_nonce');
    $seo_title = get_post_meta($post->ID, '_seo_title_key', true);
	$seo_description = get_post_meta($post->ID, '_seo_description_key', true);
	$seo_canonical = get_post_meta($post->ID, '_seo_canonical_key', true);
	$seo_script = get_post_meta($post->ID, '_seo_script_key', true);
	
	?>
		<style>
			#seo_meta_box { background: #dcffef; border: 2px solid #c3ffd9; margin: 0 10px 20px; }
			#seo_meta_box .postbox-header { background: #c3ffd9; margin: 0 0 20px; border: 0; }
			#seo_box .box_wrpr { border-top: 1px solid #ccc; padding: 15px 0; }
			#seo_box .box_wrpr:first-child { border: 0; }
			#seo_box .box_wrpr label { display: block; margin: 0 0 5px; font-weight: 700; font-size: 14px; }
			#seo_box .box_wrpr textarea,
			#seo_box .box_wrpr input[type="text"] { width: 100%; padding: 6px 10px; font-size: 13px; box-sizing: border-box; box-shadow: inset 0 0 10px #ccc; border: 1px solid #ccc; }
			#seo_box .box_wrpr button { cursor:pointer; display: block; background: #afffdc; padding: 10px 30px; border: 1px solid #afffdc; border-radius: 5px; text-transform: uppercase; font-weight: 700; }
			#seo_box .box_wrpr button:hover { color: #fff; background: #7dd3ad; }
		</style>
		<div id="seo_box">
			<div class="box_wrpr">
				<label for="seo_title">TITLE:</label>
				<input type="text" id="seo_title" name="seo_title" value="<?php echo esc_attr($seo_title); ?>">
			</div>
			<div class="box_wrpr">
				<label for="seo_description">META DESCRIPTION:</label>
				<input type="text" id="seo_description" name="seo_description" value="<?php echo esc_attr($seo_description); ?>">
			</div>
			<div class="box_wrpr">
				<label for="seo_canonical">CANONICAL URL:</label>
				<input type="text" id="seo_canonical" name="seo_canonical" value="<?php echo esc_attr($seo_canonical); ?>">
			</div>
			<div class="box_wrpr">
				<label for="seo_slug">SLUG: PAGE URL NAME</label>
				<button id="seo_slug">Change Slug</button>
			</div>
			<!--
			<div class="box_wrpr">
				<label for="seo_script">You can add any script or tag you want to add on your page.</label>
				<textarea id="seo_script" name="seo_script" rows="5" cols="40"><?php echo esc_textarea($seo_script); ?></textarea>
			</div>-->
		</div>
		<script>
			jQuery('#seo_slug').click(function(){
				if(jQuery('.edit-post-post-status').hasClass('is-opened')){
					jQuery('.edit-post-post-url__toggle').trigger('click');
				}else{
					jQuery('.edit-post-post-status .components-panel__body-toggle').trigger('click');
					setTimeout(function(){
						jQuery('.edit-post-post-url__toggle').trigger('click');
					},400);
				}
			});
		</script>
	<?php
}

// Save the meta box data
function save_custom_meta_box_data($post_id) {
    // Check if nonce is set
    if (!isset($_POST['custom_meta_box_nonce'])) {
        return;
    }

    // Verify that the nonce is valid
    if (!wp_verify_nonce($_POST['custom_meta_box_nonce'], 'custom_meta_box_nonce')) {
        return;
    }

    // Check if the user has permission to edit the post
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    // Save the custom meta field
    if (isset($_POST['seo_title'])) {
        update_post_meta($post_id, '_seo_title_key', sanitize_text_field($_POST['seo_title']));
    }
	if (isset($_POST['seo_description'])) {
        update_post_meta($post_id, '_seo_description_key', sanitize_text_field($_POST['seo_description']));
    }
	if (isset($_POST['seo_canonical'])) {
        update_post_meta($post_id, '_seo_canonical_key', sanitize_text_field($_POST['seo_canonical']));
    }
	if (isset($_POST['seo_script'])) {
        // Encode HTML content before saving
        $encoded_content = wp_kses_post($_POST['seo_script']);
        update_post_meta($post_id, '_seo_script_key', $encoded_content);
    }
}
add_action('save_post', 'save_custom_meta_box_data');
