<?php
/**
 * Plugin Name: Tracking Consent WP
 * Description: Adds Matomo tracking code with consent functionality and more.
 * Plugin URI: https://github.com/misits/tracking-consent-wp
 * Version: 1.0
 * Requires at least: 5.2
 * Requires PHP: 8.0
 * Author: Martin IS IT Services
 * Author URI: https://misits.ch
 * License: MIT License
 * Text Domain: tracking-consent-wp
 * Domain Path: /languages
 */
namespace TrackingConsentWP;

use TrackingConsentWP\includes\AssetServices;
use TrackingConsentWP\includes\ApiServices;
use TrackingConsentWP\admin\AdminServices;

// Autoload classes.
spl_autoload_register(function ($class) {
    $filename = explode("\\", $class);
    $namespace = array_shift($filename);

    array_unshift($filename, __DIR__);

    if ($namespace === __NAMESPACE__) {
        include implode(DIRECTORY_SEPARATOR, $filename) . ".php";
    }
});

// Load textdomain.
function custom_load_textdomain() {
    load_plugin_textdomain( 'tracking-consent-wp', false, basename( dirname( __FILE__ ) ) . '/languages/' );
}
add_action( 'plugins_loaded', 'TrackingConsentWP\\custom_load_textdomain' );

// Register classes.
$to_register = [
    AssetServices::class,
    AdminServices::class,
    ApiServices::class,
];

foreach ($to_register as $class) {
    $class::register();
}

// Deactivate plugin.
register_deactivation_hook(__FILE__, function () use ($to_register) {
    foreach ($to_register as $class) {
        $class::unregister();
    }
});