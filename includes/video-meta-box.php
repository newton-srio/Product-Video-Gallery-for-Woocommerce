<?php
function pvg_add_video_meta_box() {
    add_meta_box(
        'pvg_video_meta_box',
        'Product Videos',
        'pvg_video_meta_box_callback',
        'product',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'pvg_add_video_meta_box');

function pvg_video_meta_box_callback($post) {
    $video_urls = get_post_meta($post->ID, '_video_urls', true) ?: [];
    wp_nonce_field('pvg_save_video_meta_box_data', 'pvg_video_meta_box_nonce');
    
    echo '<div id="pvg_video_list">';
    
    foreach ($video_urls as $index => $video_data) {
        echo '<div class="pvg_video_item">';
        echo '<label>Choose Video Type:</label><br>';
        echo '<input type="radio" class="pvg-video-type-radio" name="pvg_video_type[' . $index . ']" value="youtube"' . checked($video_data['type'], 'youtube', false) . '> YouTube';
        echo '<input type="radio" class="pvg-video-type-radio" name="pvg_video_type[' . $index . ']" value="vimeo"' . checked($video_data['type'], 'vimeo', false) . '> Vimeo';
        echo '<input type="radio" class="pvg-video-type-radio" name="pvg_video_type[' . $index . ']" value="wp_library"' . checked($video_data['type'], 'wp_library', false) . '> WP Library<br>';
        echo '<input type="text" id="pvg_video_url_' . $index . '" name="pvg_video_url[' . $index . ']" value="' . esc_attr($video_data['url']) . '" placeholder="Paste video URL or upload" style="width: 80%;">';
        echo '<button class="button pvg_upload_button" data-index="' . $index . '" style="display:none;">Upload from WP Library</button>';
        echo '<button class="button pvg_remove_video">Remove Video</button>';
        echo '</div>';
    }
    
    echo '</div>';
    echo '<button class="button" id="pvg_add_more_videos">Add More Videos</button>';
}

function pvg_save_video_meta_box_data($post_id) {
    if (!isset($_POST['pvg_video_meta_box_nonce']) || !wp_verify_nonce($_POST['pvg_video_meta_box_nonce'], 'pvg_save_video_meta_box_data')) {
        return;
    }

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    if (isset($_POST['pvg_video_url']) && is_array($_POST['pvg_video_url'])) {
        $videos = [];
        foreach ($_POST['pvg_video_url'] as $index => $url) {
            $videos[] = [
                'type' => sanitize_text_field($_POST['pvg_video_type'][$index]),
                'url' => esc_url_raw($url),
            ];
        }
        update_post_meta($post_id, '_video_urls', $videos);
    }
}

add_action('save_post', 'pvg_save_video_meta_box_data');

function pvg_enqueue_admin_scripts($hook) {
    if ($hook == 'post.php' || $hook == 'post-new.php') {
        wp_enqueue_media(); 
        wp_enqueue_script('pvg-admin-script', plugins_url('../assets/js/script.js', __FILE__), ['jquery'], '1.0', true);
        wp_enqueue_style('pvg-admin-style', plugins_url('../assets/css/style.css', __FILE__));
    }
}
add_action('admin_enqueue_scripts', 'pvg_enqueue_admin_scripts');
