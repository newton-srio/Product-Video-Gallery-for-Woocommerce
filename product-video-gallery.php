<?php
/**
 * Plugin Name: Product Video Gallery
 * Description: Add multiple video URLs (YouTube, Vimeo, or self-hosted) to WooCommerce product galleries.
 * Version: 1.0.1
 * Author: Newton
 */

// Define paths
define('PVG_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('PVG_PLUGIN_URL', plugin_dir_url(__FILE__));

// // Enqueue styles and scripts
// function pvg_enqueue_assets() {
//     wp_enqueue_style('pvg-style', PVG_PLUGIN_URL . 'assets/css/style.css');
//     wp_enqueue_script('pvg-script', PVG_PLUGIN_URL . 'assets/js/script.js', array('jquery'), null, true);
//     wp_enqueue_media();
// }
// add_action('admin_enqueue_scripts', 'pvg_enqueue_assets');

// Include meta box and display files
require_once PVG_PLUGIN_DIR . 'includes/video-meta-box.php';
require_once PVG_PLUGIN_DIR . 'includes/video-display.php';

// Register uninstall hook
register_uninstall_hook(__FILE__, 'pvg_uninstall');

// Uninstall function
function pvg_uninstall() {
    // Here you can add code to clean up any plugin data if needed, such as deleting options or post meta.
    global $wpdb;
    $wpdb->query("DELETE FROM {$wpdb->postmeta} WHERE meta_key='_video_urls'");
}
