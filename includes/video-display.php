<?php
add_action('woocommerce_before_single_product_summary', 'pvg_maybe_remove_product_image', 5);
function pvg_maybe_remove_product_image() {
    global $post;
    $video_urls = get_post_meta($post->ID, '_video_urls', true);
    
    if ($video_urls && is_array($video_urls) && count($video_urls) > 0) {
        remove_action('woocommerce_before_single_product_summary', 'woocommerce_show_product_images', 20);
    }
}

add_action('woocommerce_before_single_product_summary', 'pvg_display_product_videos_and_theme', 20);
function pvg_display_product_videos_and_theme() {
    global $post;
    $video_urls = get_post_meta($post->ID, '_video_urls', true);
    $product = wc_get_product($post->ID);
    $gallery_image_ids = $product->get_gallery_image_ids();

    echo '<div class="pvg-container">';

    echo '<div class="pvg-gallery">';

    if (($video_urls && is_array($video_urls)) || (!empty($gallery_image_ids))) {
        echo '<div class="pvg-video-slider">';

        if ($video_urls && is_array($video_urls)) {
            foreach ($video_urls as $video_data) {
                if (!empty($video_data['url'])) {
                    echo '<div class="product-video">';

                    if ($video_data['type'] === 'youtube') {
                        $youtube_id = extract_youtube_video_id($video_data['url']);
                        if ($youtube_id) {
                            echo '<iframe src="https://www.youtube.com/embed/' . esc_attr($youtube_id) . '" frameborder="0" allowfullscreen></iframe>';
                        }
                    } elseif ($video_data['type'] === 'vimeo') {
                        preg_match('/vimeo\.com\/(\d+)/', $video_data['url'], $matches);
                        $vimeo_id = $matches[1] ?? '';
                        if ($vimeo_id) {
                            echo '<iframe src="https://player.vimeo.com/video/' . esc_attr($vimeo_id) . '" frameborder="0" allowfullscreen></iframe>';
                        }
                    } elseif ($video_data['type'] === 'wp_library') {
                        echo '<video controls><source src="' . esc_url($video_data['url']) . '" type="video/mp4"></video>';
                    } elseif ($video_data['type'] === 'rumble') {
                        $rumble_id = extract_rumble_video_id($video_data['url']);
                        if ($rumble_id) {
                            echo '<iframe src="https://rumble.com/embed/' . esc_attr($rumble_id) . '" frameborder="0" allowfullscreen></iframe>';
                        }
                    } elseif ($video_data['type'] === 'facebook') {
                        echo '<iframe src="https://www.facebook.com/plugins/video.php?href=' . urlencode($video_data['url']) . '" frameborder="0" allowfullscreen></iframe>';
                    } 

                    echo '</div>'; 
                }
            }
        }

        if (!empty($gallery_image_ids)) {
            foreach ($gallery_image_ids as $image_id) {
                $image_url = wp_get_attachment_url($image_id);
                echo '<div class="pvg-image-item">';
                echo '<img src="' . esc_url($image_url) . '" alt="Gallery Image">';
                echo '</div>';
            }
        }

        echo '</div>'; 
    } else {
        woocommerce_show_product_images();
    }

    echo '</div>'; 

    echo '<div class="pvg-theme">';
    do_action('woocommerce_single_product_summary');
    echo '</div>';
    remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_title', 5);
    remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_rating', 10);
    remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_price', 10);
    remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 20);
    remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30);
    remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40);
    remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_sharing', 50);
    remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40);
    
    echo '</div>'; 
}


function extract_youtube_video_id($url) {
    $pattern = '/(?:youtube\.com\/(?:[^\/\n\s]+\/\S+\/|(?:v|embed)\/|\S*?[?&]v=)|youtu\.be\/)([a-zA-Z0-9_-]{11})/';
    preg_match($pattern, $url, $matches);
    return $matches[1] ?? '';
}

function extract_rumble_video_id($url) {
    $pattern = '/rumble\.com\/(?:v\/|embed\/)([a-zA-Z0-9_-]+)/';
    preg_match($pattern, $url, $matches);
    return $matches[1] ?? '';
}

