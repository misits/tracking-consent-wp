<?php

namespace TrackingConsentWP\includes;

use \WP_REST_Response;

class ApiServices
{
    public static function register()
    {
        add_action("rest_api_init", [self::class, "register_routes"]);
    }

    public static function unregister()
    {
        remove_action("rest_api_init", [self::class, "register_routes"]);
    }

    public static function register_routes()
    {
        register_rest_route("tracking-consent-wp/v1", "/settings", [
            "methods" => "GET",
            "callback" => [self::class, "get_settings"],
            "permission_callback" => "__return_true",
        ]);
    }

    public static function get_settings()
    {
        $theme = array(
            "background_color" => get_option("tracking_consent_wp_background_color", "#ffffff"),
            "text_color" => get_option("tracking_consent_wp_text_color", "#000000"),
            "primary_color" => get_option("tracking_consent_wp_primary_color", "#000000"),
            "banner_class" => get_option("tracking_consent_wp_banner_class", ""),
            "button_class" => get_option("tracking_consent_wp_button_class", ""),
            "radius" => get_option("tracking_consent_wp_radius", "0"),
        );

        $consent = array(
            "is_matomo_enabled" => get_option("tracking_consent_wp_enable_matomo", false),
            "read_more" => get_permalink(get_option('tracking_consent_wp_read_more_page', 0)),
            "accept_text" => get_option('tracking_consent_wp_accept_button', __('Accept', 'wp-tracking-consent')),
            "decline_text" => get_option('tracking_consent_wp_decline_button', __('Decline', 'wp-tracking-consent')),
            "content" => get_option('tracking_consent_wp_description', __('We use cookies to ensure that we give you the best experience on our website. If you continue to use this site we will assume that you are happy with it.', 'wp-tracking-consent')),
        );

        return new WP_REST_Response(
            array(
                "theme" => $theme,
                "consent" => $consent,
            ),
            200
        );
    }
}