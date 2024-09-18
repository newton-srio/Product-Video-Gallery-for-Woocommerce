<?php
/**
 * Plugin Name: Product Video Gallery
 * Description: Add multiple video URLs (YouTube, Vimeo, or self-hosted) to WooCommerce product galleries.
 * Version: 1.0.2
 * Author: Newton
 */

define('PVG_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('PVG_PLUGIN_URL', plugin_dir_url(__FILE__));

function pvg_enqueue_assets() {
    if (is_singular('product') || has_shortcode(get_post()->post_content, 'product_video_gallery_jnewton')) {
        wp_enqueue_style('pvg-slick-style', 'https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css');
        wp_enqueue_style('pvg-slick-theme', 'https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick-theme.css');
        wp_enqueue_style('pvg-style', PVG_PLUGIN_URL . 'assets/css/style.css');

        wp_enqueue_script('pvg-slick-script', 'https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js', array('jquery'), null, true);
        wp_enqueue_script('pvg-script', PVG_PLUGIN_URL . 'assets/js/script.js', array('jquery'), null, true);
        wp_enqueue_style('font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css');
        wp_enqueue_media();
    }
}
add_action('wp_enqueue_scripts', 'pvg_enqueue_assets');

require_once PVG_PLUGIN_DIR . 'includes/video-meta-box.php';
require_once PVG_PLUGIN_DIR . 'includes/video-display.php';


register_uninstall_hook(__FILE__, 'pvg_uninstall');

function pvg_uninstall() {
    global $wpdb;
    $wpdb->query("DELETE FROM {$wpdb->postmeta} WHERE meta_key='_video_urls'");
}

function pvg_product_video_gallery_shortcode($atts) {
    global $post;

    $atts = shortcode_atts(array(
        'product_id' => get_the_ID()
    ), $atts);

    $product_id = $atts['product_id'];
    if (!$product_id) {
        return 'No product ID provided.';
    }

    $video_urls = get_post_meta($product_id, '_video_urls', true);
    $product = wc_get_product($product_id);
    $gallery_image_ids = $product->get_gallery_image_ids();

    ob_start(); 

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
    echo '</div>'; 

    return ob_get_clean(); 
}

add_shortcode('product_video_gallery_jnewton', 'pvg_product_video_gallery_shortcode');
