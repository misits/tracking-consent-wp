<?php

namespace TrackingConsentWP\admin;

class AdminServices
{

    public static function register()
    {
        add_action("admin_menu", [self::class, "register_admin_menu"]);
        add_action('admin_enqueue_scripts', function () {
            wp_enqueue_style('tracking-consent-wp-icomoon-style', plugins_url('/assets/stylesheets/icomoon.css', __FILE__));
        });
        add_action('admin_init', [self::class, "register_settings"]);
    }

    public static function unregister()
    {
        remove_action("admin_menu", [self::class, "register_admin_menu"]);
        remove_action('admin_enqueue_scripts', function () {
            wp_enqueue_style('tracking-consent-wp-icomoon-style', plugins_url('/assets/stylesheets/icomoon.css', __FILE__));
        });
    }

    /**
     * Register admin menu.
     */
    public static function register_admin_menu()
    {
        add_menu_page(
            "Tracking Consent WP",
            "Tracking Consent WP",
            "manage_options",
            "tracking-consent-wp",
            [self::class, "render_admin_page"],
            "dashicons-icon-admin_panel_settings",
            99
        );
    }

    /**
     * Render admin page.
     */
    public static function render_admin_page()
    {
        // Get all current tracking-consent-wp
        $consent = array(
            "site_id" => get_option("tracking_consent_wp_site_id"),
            "matomo_url" => get_option("tracking_consent_wp_matomo_url"),
            "token_auth" => get_option("tracking_consent_wp_token_auth"),
        );
        $is_matom_enabled = self::is_matomo_enabled();
?>
        <div class="wrap">
            <h1>Tracking Consent WP</h1>
            <div class="tracking-tabs">
                <?php if ($is_matom_enabled) { ?>
                    <a href="#tracking-consent-wp-main" class="tracking-tab-link active">
                        <div class="wp-menu-image dashicons-before dashicons-icon-bar_chart" aria-hidden="true"><br></div><?= __("Matomo", "tracking-consent-wp"); ?>
                    </a>
                <?php } ?>
                <a href="#tracking-consent-wp-banner" class="tracking-tab-link <?= $is_matom_enabled ? '' : 'active' ?>">
                    <div class="wp-menu-image dashicons-before dashicons-icon-domain_verification" aria-hidden="true"><br></div><?= __("Pop-up", "tracking-consent-wp"); ?>
                </a>
                <a href="#tracking-consent-wp-theme" class="tracking-tab-link">
                    <div class="wp-menu-image dashicons-before dashicons-icon-color_lens" aria-hidden="true"><br></div><?= __("Theme", "tracking-consent-wp"); ?>
                </a>
                <a href="#tracking-consent-wp-advanced" class="tracking-tab-link">
                    <div class="wp-menu-image dashicons-before dashicons-icon-tune" aria-hidden="true"><br></div><?= __("Settings", "tracking-consent-wp"); ?>
                </a>
                <a href="https://www.paypal.com/donate/?hosted_button_id=8YDDNMSELC5CS" class="tracking-tab-link tracking-tab-link--donation">
                    <div class="wp-menu-image dashicons-before dashicons-icon-volunteer_activism" aria-hidden="true"><br></div><?= __("Donation", "tracking-consent-wp"); ?>
                </a>
            </div>
            <div class="tracking-tab-container">
                <div class="tracking-tab-content <?= $is_matom_enabled ? 'active' : 'hide' ?>" id="tracking-consent-wp-main">
                    <div id="tracking-consent-wp-admin" data-consent="<?= htmlentities(json_encode($consent)) ?>"></div>
                </div>
                <div class="tracking-tab-content <?= $is_matom_enabled ? 'hide' : 'active' ?>" id="tracking-consent-wp-banner">
                    <form method="post" action="options.php" class="tracking-consent-wp-form">
                        <?php
                        settings_fields("tracking-consent-wp-banner");
                        do_settings_sections("tracking-consent-wp-banner");
                        submit_button();
                        ?>
                    </form>
                </div>
                <div class="tracking-tab-content hide" id="tracking-consent-wp-advanced">
                    <form method="post" action="options.php" class="tracking-consent-wp-form">
                        <?php
                        settings_fields("tracking-consent-wp-settings");
                        do_settings_sections("tracking-consent-wp-settings");
                        submit_button();
                        ?>
                    </form>
                </div>
                <div class="tracking-tab-content hide" id="tracking-consent-wp-theme">
                    <form method="post" action="options.php" class="tracking-consent-wp-form">
                        <?php
                        settings_fields("tracking-consent-wp-theme");
                        do_settings_sections("tracking-consent-wp-theme");
                        submit_button();
                        ?>
                    </form>
                </div>
            </div>
        </div>
        <!-- Toggle Tabs -->
        <script>
            jQuery(document).ready(function($) {
                $(".tracking-tab-link").click(function() {
                    $(".tracking-tab-link").removeClass("active");
                    $(this).addClass("active");

                    if ($(this).attr("href") === "#tracking-consent-wp-main") {
                        $("#tracking-consent-wp-main").removeClass("hide");
                        $("#tracking-consent-wp-main").addClass("active");
                        $("#tracking-consent-wp-advanced").addClass("hide");
                        $("#tracking-consent-wp-advanced").removeClass("active");
                        $("#tracking-consent-wp-theme").addClass("hide");
                        $("#tracking-consent-wp-theme").removeClass("active");
                        $("#tracking-consent-wp-banner").addClass("hide");
                        $("#tracking-consent-wp-banner").removeClass("active");
                    } else if ($(this).attr("href") === "#tracking-consent-wp-advanced") {
                        $("#tracking-consent-wp-main").addClass("hide");
                        $("#tracking-consent-wp-main").removeClass("active");
                        $("#tracking-consent-wp-advanced").removeClass("hide");
                        $("#tracking-consent-wp-advanced").addClass("active");
                        $("#tracking-consent-wp-theme").addClass("hide");
                        $("#tracking-consent-wp-theme").removeClass("active");
                        $("#tracking-consent-wp-banner").addClass("hide");
                        $("#tracking-consent-wp-banner").removeClass("active");
                    } else if ($(this).attr("href") === "#tracking-consent-wp-theme") {
                        $("#tracking-consent-wp-main").addClass("hide");
                        $("#tracking-consent-wp-main").removeClass("active");
                        $("#tracking-consent-wp-advanced").addClass("hide");
                        $("#tracking-consent-wp-advanced").removeClass("active");
                        $("#tracking-consent-wp-theme").removeClass("hide");
                        $("#tracking-consent-wp-theme").addClass("active");
                        $("#tracking-consent-wp-banner").addClass("hide");
                        $("#tracking-consent-wp-banner").removeClass("active");
                    } else if ($(this).attr("href") === "#tracking-consent-wp-banner") {
                        $("#tracking-consent-wp-main").addClass("hide");
                        $("#tracking-consent-wp-main").removeClass("active");
                        $("#tracking-consent-wp-advanced").addClass("hide");
                        $("#tracking-consent-wp-advanced").removeClass("active");
                        $("#tracking-consent-wp-theme").addClass("hide");
                        $("#tracking-consent-wp-theme").removeClass("active");
                        $("#tracking-consent-wp-banner").removeClass("hide");
                        $("#tracking-consent-wp-banner").addClass("active");
                    }
                });
            });
        </script>
<?php
    }

    /**
     * Custom fields to save for the plugin.
     */
    public static function register_settings()
    {
        // settings for the main tab
        register_setting("tracking-consent-wp-banner", "tracking_consent_wp_description");
        register_setting("tracking-consent-wp-banner", "tracking_consent_wp_accept_button");
        register_setting("tracking-consent-wp-banner", "tracking_consent_wp_decline_button");
        register_setting("tracking-consent-wp-banner", "tracking_consent_wp_read_more_page");
        register_setting("tracking-consent-wp-banner", "tracking_consent_wp_token_auth");
        register_setting("tracking-consent-wp-banner", "tracking_consent_wp_script");
        register_setting("tracking-consent-wp-banner", "tracking_consent_wp_site_id");
        register_setting("tracking-consent-wp-banner", "tracking_consent_wp_matomo_url");

        // Adding settings fields
        add_settings_section(
            "tracking-consent-wp-banner",
            "",
            null,
            "tracking-consent-wp-banner"
        );

        add_settings_field(
            "tracking_consent_wp_description",
            __("Description", "tracking-consent-wp"),
            [self::class, "render_description_field"],
            "tracking-consent-wp-banner",
            "tracking-consent-wp-banner"
        );

        add_settings_field(
            "tracking_consent_wp_accept_button",
            __("Accept Button Text", "tracking-consent-wp"),
            [self::class, "render_accept_button_field"],
            "tracking-consent-wp-banner",
            "tracking-consent-wp-banner"
        );

        add_settings_field(
            "tracking_consent_wp_decline_button",
            __("Decline Button Text", "tracking-consent-wp"),
            [self::class, "render_decline_button_field"],
            "tracking-consent-wp-banner",
            "tracking-consent-wp-banner"
        );

        add_settings_field(
            "tracking_consent_wp_read_more_page",
            __("Read More Page", "tracking-consent-wp"),
            [self::class, "render_read_more_page_field"],
            "tracking-consent-wp-banner",
            "tracking-consent-wp-banner"
        );


        // settings for the theme tab
        register_setting("tracking-consent-wp-theme", "tracking_consent_wp_background_color");
        register_setting("tracking-consent-wp-theme", "tracking_consent_wp_text_color");
        register_setting("tracking-consent-wp-theme", "tracking_consent_wp_primary_color");
        register_setting("tracking-consent-wp-theme", "tracking_consent_wp_banner_class");
        register_setting("tracking-consent-wp-theme", "tracking_consent_wp_button_class");
        register_setting("tracking-consent-wp-theme", "tracking_consent_wp_radius");

        add_settings_section(
            "tracking-consent-wp-theme",
            "",
            null,
            "tracking-consent-wp-theme"
        );

        add_settings_field(
            "tracking_consent_wp_background_color",
            __("Background Color", "tracking-consent-wp"),
            [self::class, "render_background_color_field"],
            "tracking-consent-wp-theme",
            "tracking-consent-wp-theme"
        );

        add_settings_field(
            "tracking_consent_wp_text_color",
            __("Text Color", "tracking-consent-wp"),
            [self::class, "render_text_color_field"],
            "tracking-consent-wp-theme",
            "tracking-consent-wp-theme"
        );

        add_settings_field(
            "tracking_consent_wp_primary_color",
            __("Primary Color", "tracking-consent-wp"),
            [self::class, "render_primary_color_field"],
            "tracking-consent-wp-theme",
            "tracking-consent-wp-theme"
        );

        add_settings_field(
            "tracking_consent_wp_banner_class",
            __("Banner Class", "tracking-consent-wp"),
            [self::class, "render_banner_class_field"],
            "tracking-consent-wp-theme",
            "tracking-consent-wp-theme"
        );

        add_settings_field(
            "tracking_consent_wp_button_class",
            __("Button Class", "tracking-consent-wp"),
            [self::class, "render_button_class_field"],
            "tracking-consent-wp-theme",
            "tracking-consent-wp-theme"
        );

        add_settings_field(
            "tracking_consent_wp_radius",
            __("Radius", "tracking-consent-wp"),
            [self::class, "render_radius_field"],
            "tracking-consent-wp-theme",
            "tracking-consent-wp-theme"
        );

        // Adding new checkbox settings for Matomo script options
        register_setting("tracking-consent-wp-settings", "tracking_consent_wp_enable_matomo");
        register_setting("tracking-consent-wp-settings", "tracking_consent_wp_set_document_title");
        register_setting("tracking-consent-wp-settings", "tracking_consent_wp_set_cookie_domain");
        register_setting("tracking-consent-wp-settings", "tracking_consent_wp_set_domains");
        register_setting("tracking-consent-wp-settings", "tracking_consent_wp_set_do_not_track");
        register_setting("tracking-consent-wp-settings", "tracking_consent_wp_disable_cookies");
        register_setting("tracking-consent-wp-settings", "tracking_consent_wp_track_page_view");
        register_setting("tracking-consent-wp-settings", "tracking_consent_wp_enable_link_tracking");
        register_setting("tracking-consent-wp-settings", "tracking_consent_wp_site_id");
        register_setting("tracking-consent-wp-settings", "tracking_consent_wp_matomo_url");
        register_setting("tracking-consent-wp-settings", "tracking_consent_wp_token_auth");

        add_settings_section(
            "tracking-consent-wp-settings",
            "",
            null,
            "tracking-consent-wp-settings"
        );

        add_settings_field(
            "tracking_consent_wp_enable_matomo",
            __("Enable Matomo", "tracking-consent-wp"),
            [self::class, "render_toggle_field"],
            "tracking-consent-wp-settings",
            "tracking-consent-wp-settings"
        );

        if (!self::is_matomo_enabled()) {
            return;
        }
        add_settings_field(
            "tracking_consent_wp_token_auth",
            __("Token auth", "tracking-consent-wp"),
            [self::class, "render_token_auth_field"],
            "tracking-consent-wp-settings",
            "tracking-consent-wp-settings"
        );

        add_settings_field(
            "tracking_consent_wp_site_id",
            __("Site ID", "tracking-consent-wp"),
            [self::class, "render_site_id_field"],
            "tracking-consent-wp-settings",
            "tracking-consent-wp-settings"
        );

        add_settings_field(
            "tracking_consent_wp_matomo_url",
            __("Matomo URL", "tracking-consent-wp"),
            [self::class, "render_matomo_url_field"],
            "tracking-consent-wp-settings",
            "tracking-consent-wp-settings"
        );

        add_settings_field(
            "tracking_consent_wp_set_document_title",
            __("Set Document Title", "tracking-consent-wp"),
            [self::class, "render_checkbox_field"],
            "tracking-consent-wp-settings",
            "tracking-consent-wp-settings",
            [
                'id' => 'tracking_consent_wp_set_document_title',
                'label_for' => 'tracking_consent_wp_set_document_title'
            ]
        );

        add_settings_field(
            "tracking_consent_wp_set_cookie_domain",
            __("Set Cookie Domain", "tracking-consent-wp"),
            [self::class, "render_checkbox_field"],
            "tracking-consent-wp-settings",
            "tracking-consent-wp-settings",
            [
                'id' => 'tracking_consent_wp_set_cookie_domain',
                'label_for' => 'tracking_consent_wp_set_cookie_domain'
            ]
        );

        add_settings_field(
            "tracking_consent_wp_set_domains",
            __("Set Domains", "tracking-consent-wp"),
            [self::class, "render_checkbox_field"],
            "tracking-consent-wp-settings",
            "tracking-consent-wp-settings",
            [
                'id' => 'tracking_consent_wp_set_domains',
                'label_for' => 'tracking_consent_wp_set_domains'
            ]
        );

        add_settings_field(
            "tracking_consent_wp_set_do_not_track",
            __("Set Do Not Track", "tracking-consent-wp"),
            [self::class, "render_checkbox_field"],
            "tracking-consent-wp-settings",
            "tracking-consent-wp-settings",
            [
                'id' => 'tracking_consent_wp_set_do_not_track',
                'label_for' => 'tracking_consent_wp_set_do_not_track'
            ]
        );

        add_settings_field(
            "tracking_consent_wp_disable_cookies",
            __("Disable Cookies", "tracking-consent-wp"),
            [self::class, "render_checkbox_field"],
            "tracking-consent-wp-settings",
            "tracking-consent-wp-settings",
            [
                'id' => 'tracking_consent_wp_disable_cookies',
                'label_for' => 'tracking_consent_wp_disable_cookies'
            ]
        );

        add_settings_field(
            "tracking_consent_wp_track_page_view",
            __("Track Page View", "tracking-consent-wp"),
            [self::class, "render_checkbox_field"],
            "tracking-consent-wp-settings",
            "tracking-consent-wp-settings",
            [
                'id' => 'tracking_consent_wp_track_page_view',
                'label_for' => 'tracking_consent_wp_track_page_view'
            ]
        );

        add_settings_field(
            "tracking_consent_wp_enable_link_tracking",
            __("Enable Link Tracking", "tracking-consent-wp"),
            [self::class, "render_checkbox_field"],
            "tracking-consent-wp-settings",
            "tracking-consent-wp-settings",
            [
                'id' => 'tracking_consent_wp_enable_link_tracking',
                'label_for' => 'tracking_consent_wp_enable_link_tracking'
            ]
        );
    }

    public static function render_description_field()
    {
        $value = get_option('tracking_consent_wp_description');
        if (!$value) {
            $value = __("We use cookies to ensure that we give you the best experience on our website. If you continue to use this site we will assume that you are happy with it.", "tracking-consent-wp");
        }
        $editor_id = 'tracking_consent_wp_description';
        $settings = array(
            'textarea_name' => $editor_id,
            'textarea_rows' => 10,
            'media_buttons' => false,
            'tinymce' => array(
                'toolbar1' => 'bold,italic,underline,|,bullist,numlist,blockquote,|,alignleft,aligncenter,alignright,|,link,unlink,|,undo,redo',
                'toolbar2' => '',
                'toolbar3' => '',
            ),
            'quicktags' =>  false,
        );

        wp_editor(html_entity_decode($value), $editor_id, $settings);
    }

    public static function render_accept_button_field()
    {
        $value = get_option('tracking_consent_wp_accept_button');
        if (!$value) {
            $value = __("Accept", "tracking-consent-wp");
        }
        echo '<input type="text" id="tracking_consent_wp_accept_button" name="tracking_consent_wp_accept_button" value="' . esc_attr($value) . '" class="large-text">';
    }

    public static function render_decline_button_field()
    {
        $value = get_option('tracking_consent_wp_decline_button');
        if (!$value) {
            $value = __("Decline", "tracking-consent-wp");
        }
        echo '<input type="text" id="tracking_consent_wp_decline_button" name="tracking_consent_wp_decline_button" value="' . esc_attr($value) . '" class="large-text">';
    }

    public static function render_read_more_page_field()
    {
        $value = get_option('tracking_consent_wp_read_more_page');
        echo '<select id="tracking_consent_wp_read_more_page" name="tracking_consent_wp_read_more_page" class="large-text">';
        echo '<option value="">' . __("Select a page", "tracking-consent-wp") . '</option>';

        $pages = get_pages();
        foreach ($pages as $page) {
            $selected = selected($value, $page->ID, false);
            echo '<option value="' . esc_attr($page->ID) . '" ' . $selected . '>' . esc_html($page->post_title) . '</option>';
        }

        echo '</select>';
    }

    public static function render_token_auth_field()
    {
        $value = get_option('tracking_consent_wp_token_auth');
        if (!$value) {
            $value = "anonymous";
        }
        echo '<input type="text" id="tracking_consent_wp_token_auth" name="tracking_consent_wp_token_auth" placeholder="anonymous" value="' . esc_attr($value) . '" class="large-text">';
    }

    public static function render_site_id_field()
    {
        $value = get_option('tracking_consent_wp_site_id');
        echo '<input type="text" placeholder="0" id="tracking_consent_wp_site_id" name="tracking_consent_wp_site_id" value="' . esc_attr($value) . '" class="large-text">';
    }


    public static function render_matomo_url_field()
    {
        $value = get_option('tracking_consent_wp_matomo_url');
        echo '<input type="text" id="tracking_consent_wp_matomo_url" placeholder="https://<your-matomo.domain>" name="tracking_consent_wp_matomo_url" value="' . esc_attr($value) . '" class="large-text">';
    }

    public static function render_background_color_field()
    {
        $value = get_option('tracking_consent_wp_background_color');
        if (!$value) {
            $value = "#ffffff";
        }
        echo '<input type="color" id="tracking_consent_wp_background_color" name="tracking_consent_wp_background_color" value="' . esc_attr($value) . '" class="large-text">';
    }

    public static function render_text_color_field()
    {
        $value = get_option('tracking_consent_wp_text_color');
        if (empty($value)) {
            $value = "#000000";
        }

        echo '<input type="color" id="tracking_consent_wp_text_color" name="tracking_consent_wp_text_color" value="' . esc_attr($value) . '" class="large-text">';
    }

    public static function render_primary_color_field()
    {
        $value = get_option('tracking_consent_wp_primary_color');
        if (empty($value)) {
            $value = "#000000";
        }
        echo '<input type="color" id="tracking_consent_wp_primary_color" name="tracking_consent_wp_primary_color" value="' . esc_attr($value) . '" class="large-text">';
    }

    public static function render_banner_class_field()
    {
        $value = get_option('tracking_consent_wp_banner_class');
        echo '<input type="text" id="tracking_consent_wp_banner_class" name="tracking_consent_wp_banner_class" value="' . esc_attr($value) . '" class="large-text">';
    }

    public static function render_button_class_field()
    {
        $value = get_option('tracking_consent_wp_button_class');
        echo '<input type="text" id="tracking_consent_wp_button_class" name="tracking_consent_wp_button_class" value="' . esc_attr($value) . '" class="large-text">';
    }

    public static function render_radius_field()
    {
        $value = get_option('tracking_consent_wp_radius');
        if (empty($value)) {
            $value = "0";
        }
        echo '<input type="number" id="tracking_consent_wp_radius" name="tracking_consent_wp_radius" value="' . esc_attr($value) . '" class="large-text">';
    }

    public static function render_checkbox_field($args)
    {
        $option = get_option($args['id']);
        echo '<input type="checkbox" id="' . esc_attr($args['id']) . '" name="' . esc_attr($args['id']) . '" value="1" ' . checked(1, $option, false) . '/>';
    }

    /**
     * Render toggle field. as switch button
     */
    public static function render_toggle_field()
    {
        $option = get_option('tracking_consent_wp_enable_matomo');
        if (!$option) {
            $option = false;
        }
        echo '<input type="checkbox" id="tracking_consent_wp_enable_matomo" name="tracking_consent_wp_enable_matomo" value="1" ' . checked(1, $option, false) . '/>';
    }

    public static function is_matomo_enabled()
    {
        return get_option('tracking_consent_wp_enable_matomo');
    }
}
