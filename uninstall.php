<?php

namespace TrackingConsentWP;

use TrackingConsentWP\includes\AssetServices;
use TrackingConsentWP\includes\ApiServices;
use TrackingConsentWP\admin\AdminServices;

// If uninstall not called from WordPress, exit
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

// Delete options created by the plugin
$options = [
    // Banner
    'tracking_consent_wp_description',
    'tracking_consent_wp_accept_button',
    'tracking_consent_wp_decline_button',
    'tracking_consent_wp_read_more_page',
    // Theme
    'tracking_consent_wp_background_color',
    'tracking_consent_wp_text_color',
    'tracking_consent_wp_primary_color',
    'tracking_consent_wp_banner_class',
    'tracking_consent_wp_button_class',
    'tracking_consent_wp_radius',
    // Setting
    'tracking_consent_wp_enable_matomo',
    'tracking_consent_wp_site_id',
    'tracking_consent_wp_matomo_url',
    'tracking_consent_wp_token_auth',
    'tracking_consent_wp_set_document_title',
    'tracking_consent_wp_set_cookie_domain',
    'tracking_consent_wp_set_domains',
    'tracking_consent_wp_set_do_not_track',
    'tracking_consent_wp_disable_cookies',
    'tracking_consent_wp_track_page_view',
    'tracking_consent_wp_enable_link_tracking'
];

foreach ($options as $option) {
    delete_option($option);
}