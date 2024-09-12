<?php
if (!defined('WP_UNINSTALL_PLUGIN')) {
    die;
}

// Clean up the video URLs when the plugin is uninstalled
global $wpdb;
$wpdb->query("DELETE FROM {$wpdb->postmeta} WHERE meta_key='_video_urls'");
