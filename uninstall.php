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
    'wp_tracking_consent_site_id',
    'wp_tracking_consent_matomo_url',
    'wp_tracking_consent_token_auth',
    'wp_tracking_consent_description',
    'wp_tracking_consent_accept_button',
    'wp_tracking_consent_decline_button',
    'wp_tracking_consent_read_more_page',
    'wp_tracking_consent_theme_background_color',
    'wp_tracking_consent_theme_text_color',
    'wp_tracking_consent_theme_primary_color',
    'wp_tracking_consent_theme_banner_class',
    'wp_tracking_consent_theme_button_class',
    'wp_tracking_consent_theme_radius',
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