<?php

namespace WPTrackingConsent;

use WPTrackingConsent\includes\AssetServices;
use WPTrackingConsent\includes\ApiServices;
use WPTrackingConsent\admin\AdminServices;

// If uninstall not called from WordPress, exit
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

// Delete options created by the plugin
$options = [
    // Banner
    'wp_tracking_consent_description',
    'wp_tracking_consent_accept_button',
    'wp_tracking_consent_decline_button',
    'wp_tracking_consent_read_more_page',
    // Theme
    'wp_tracking_consent_background_color',
    'wp_tracking_consent_text_color',
    'wp_tracking_consent_primary_color',
    'wp_tracking_consent_banner_class',
    'wp_tracking_consent_button_class',
    'wp_tracking_consent_radius',
    // Setting
    'wp_tracking_consent_enable_matomo',
    'wp_tracking_consent_site_id',
    'wp_tracking_consent_matomo_url',
    'wp_tracking_consent_token_auth',
    'wp_tracking_consent_set_document_title',
    'wp_tracking_consent_set_cookie_domain',
    'wp_tracking_consent_set_domains',
    'wp_tracking_consent_set_do_not_track',
    'wp_tracking_consent_disable_cookies',
    'wp_tracking_consent_track_page_view',
    'wp_tracking_consent_enable_link_tracking'
];

foreach ($options as $option) {
    delete_option($option);
}

// Unregister services created by the plugin
$services = [
    AssetServices::class,
    AdminServices::class,
    ApiServices::class,
];

foreach ($services as $service) {
    $service::unregister();
}