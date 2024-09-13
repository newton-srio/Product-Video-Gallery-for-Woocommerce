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
    wp_enqueue_style('pvg-slick-style', 'https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css');
    wp_enqueue_style('pvg-slick-theme', 'https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick-theme.css');
    wp_enqueue_style('pvg-style', PVG_PLUGIN_URL . 'assets/css/style.css');
    
    wp_enqueue_script('pvg-slick-script', 'https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js', array('jquery'), null, true);
    wp_enqueue_script('pvg-script', PVG_PLUGIN_URL . 'assets/js/script.js', array('jquery'), null, true);
    wp_enqueue_style('font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css');
    wp_enqueue_media();
}
add_action('wp_enqueue_scripts', 'pvg_enqueue_assets');


require_once PVG_PLUGIN_DIR . 'includes/video-meta-box.php';
require_once PVG_PLUGIN_DIR . 'includes/video-display.php';


register_uninstall_hook(__FILE__, 'pvg_uninstall');


function pvg_uninstall() {
    global $wpdb;
    $wpdb->query("DELETE FROM {$wpdb->postmeta} WHERE meta_key='_video_urls'");
}
