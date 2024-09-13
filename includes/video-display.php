<?php
add_action('woocommerce_before_single_product_summary', 'pvg_maybe_remove_product_image', 5);
function pvg_maybe_remove_product_image() {
    global $post;

    $video_urls = get_post_meta($post->ID, '_video_urls', true);
    
    if ($video_urls && is_array($video_urls) && count($video_urls) > 0) {
        remove_action('woocommerce_before_single_product_summary', 'woocommerce_show_product_images', 20);
    }
}


add_action('woocommerce_before_single_product_summary', 'pvg_display_product_videos', 20);
function pvg_display_product_videos() {
    global $post;

    $video_urls = get_post_meta($post->ID, '_video_urls', true);

    if ($video_urls && is_array($video_urls)) {
        echo '<div class="pvg-video-slider">';
        
        foreach ($video_urls as $video_data) {
            if ($video_data['url']) {
                echo '<div class="product-video" style="text-align:center;">';
                
                if ($video_data['type'] == 'youtube') {
                    preg_match("/(?:https?:\/\/)?(?:www\.)?(?:youtube\.com\/(?:watch\?v=|embed\/|v\/)|youtu\.be\/)([a-zA-Z0-9_-]+)/", $video_data['url'], $matches);
                    if (isset($matches[1])) {
                        $youtube_id = $matches[1];
                        echo '<iframe width="640" height="360" src="https://www.youtube.com/embed/' . esc_attr($youtube_id) . '" frameborder="0" allowfullscreen></iframe>';
                    }
                } elseif ($video_data['type'] == 'vimeo') {
                    preg_match("/(?:https?:\/\/)?(?:www\.)?vimeo\.com\/(\d+)/", $video_data['url'], $matches);
                    if (isset($matches[1])) {
                        $vimeo_id = $matches[1];
                        echo '<iframe src="https://player.vimeo.com/video/' . esc_attr($vimeo_id) . '" width="640" height="360" frameborder="0" allow="autoplay; fullscreen" allowfullscreen></iframe>';
                    }
                } elseif ($video_data['type'] == 'wp_library') {
                    echo '<video width="640" height="360" controls>';
                    echo '<source src="' . esc_url($video_data['url']) . '" type="video/mp4">';
                    echo 'Your browser does not support the video tag.';
                    echo '</video>';
                }

                echo '</div>';
            }
        }
        
        echo '</div>';
    }
}
