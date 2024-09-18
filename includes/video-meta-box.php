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
        
        echo '<select class="pvg-video-type-dropdown" name="pvg_video_type[' . $index . ']">';
        echo '<option value="youtube"' . selected($video_data['type'], 'youtube', false) . '>YouTube</option>';
        echo '<option value="facebook"' .selected($video_data['type'], 'facebook', false) . '> Facebook</option>';
        echo '<option value="vimeo"' . selected($video_data['type'], 'vimeo', false) . '>Vimeo</option>';
        echo '<option value="wp_library"' . selected($video_data['type'], 'wp_library', false) . '>WP Library</option>';
        echo '<option value="rumble"' . selected($video_data['type'], 'rumble', false) . '>Rumble</option>';
        echo '</select><br>';
        
        echo '<input type="text" id="pvg_video_url_' . $index . '" name="pvg_video_url[' . $index . ']" value="' . esc_attr($video_data['url']) . '" placeholder="Paste video URL or upload" style="width: 80%;">';
        echo '<button class="button pvg_upload_button" data-index="' . $index . '" style="display:none;">Upload from WP Library</button>';
        echo '<span class="pvg_remove_video_icon" style="cursor: pointer; color: red; font-size: 18px;" title="Remove Video">
                    <i class="fas fa-trash-alt"></i>
                </span>';
        echo '</div>';
    }
    
    echo '</div>';
    echo '<button class="button pvg_add_video_button" id="pvg_add_more_videos"><i class="fas fa-plus"></i> Add</button>';
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
function product_page_widget_init() {
    register_sidebar( array(
        'name'          => 'Product Page Widget',
        'id'            => 'product-page-widget',
        'before_widget' => '<div class="product-widget-area">',
        'after_widget'  => '</div>',
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>',
    ));
}
add_action( 'widgets_init', 'product_page_widget_init' );

function pvg_enqueue_admin_scripts($hook) {
    if ($hook == 'post.php' || $hook == 'post-new.php') {
        wp_enqueue_media(); 
        wp_enqueue_script('pvg-admin-script', plugins_url('../assets/js/script.js', __FILE__), ['jquery'], '1.0', true);
        wp_enqueue_style('pvg-admin-style', plugins_url('../assets/css/style.css', __FILE__));
        wp_enqueue_style('pvg-fontawesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css');
    }
}
add_action('admin_enqueue_scripts', 'pvg_enqueue_admin_scripts');
?>
