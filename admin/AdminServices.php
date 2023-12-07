<?php

namespace WPTrackingConsent\admin;

class AdminServices
{

    public static function register()
    {
        add_action("admin_menu", [self::class, "register_admin_menu"]);
        add_action('admin_enqueue_scripts', function () {
            wp_enqueue_style('wp-tracking-consent-icomoon-style', plugins_url('/assets/stylesheets/icomoon.css', __FILE__));
        });
        add_action('admin_init', [self::class, "register_settings"]);
    }

    public static function unregister()
    {
        remove_action("admin_menu", [self::class, "register_admin_menu"]);
        remove_action('admin_enqueue_scripts', function () {
            wp_enqueue_style('wp-tracking-consent-icomoon-style', plugins_url('/assets/stylesheets/icomoon.css', __FILE__));
        });
    }

    /**
     * Register admin menu.
     */
    public static function register_admin_menu()
    {
        add_menu_page(
            "WP Tracking Consent",
            "WP Tracking Consent",
            "manage_options",
            "wp-tracking-consent",
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
        // Get all current wp-tracking-consent
        $consent = array(
            "site_id" => get_option("wp_tracking_consent_site_id"),
            "matomo_url" => get_option("wp_tracking_consent_matomo_url"),
            "token_auth" => get_option("wp_tracking_consent_token_auth"),
        );
?>
        <div class="wrap">
            <h1>WP Tracking Consent</h1>
            <div class="wp-tracking-tabs">
                <a href="#wp-tracking-consent-main" class="wp-tracking-tab-link active"><?= __("Matomo", "wp-tracking-consent"); ?></a>
                <a href="#wp-tracking-consent-advanced" class="wp-tracking-tab-link"><?= __("Settings", "wp-tracking-consent"); ?></a>
                <a href="#wp-tracking-consent-theme" class="wp-tracking-tab-link"><?= __("Theme", "wp-tracking-consent"); ?></a>
            </div>
            <div class="wp-tracking-tab-container">
                <div class="wp-tracking-tab-content active" id="wp-tracking-consent-main">
                    <div id="wp-tracking-consent-admin" data-consent="<?= htmlentities(json_encode($consent)) ?>"></div>
                </div>
                <div class="wp-tracking-tab-content hide" id="wp-tracking-consent-advanced">
                    <form method="post" action="options.php" class="wp-tracking-consent-form">
                        <?php
                        settings_fields("wp-tracking-consent");
                        do_settings_sections("wp-tracking-consent");
                        submit_button();
                        ?>
                    </form>
                </div>
                <div class="wp-tracking-tab-content hide" id="wp-tracking-consent-theme">
                    <form method="post" action="options.php" class="wp-tracking-consent-form">
                        <?php
                        settings_fields("wp-tracking-consent-theme");
                        do_settings_sections("wp-tracking-consent-theme");
                        submit_button();
                        ?>
                    </form>
                </div>
            </div>
        </div>
        <!-- Toggle Tabs -->
        <script>
            jQuery(document).ready(function($) {
                $(".wp-tracking-tab-link").click(function() {
                    $(".wp-tracking-tab-link").removeClass("active");
                    $(this).addClass("active");

                    if ($(this).attr("href") === "#wp-tracking-consent-main") {
                        $("#wp-tracking-consent-main").removeClass("hide");
                        $("#wp-tracking-consent-main").addClass("active");
                        $("#wp-tracking-consent-advanced").addClass("hide");
                        $("#wp-tracking-consent-advanced").removeClass("active");
                        $("#wp-tracking-consent-theme").addClass("hide");
                        $("#wp-tracking-consent-theme").removeClass("active");
                    } else if ($(this).attr("href") === "#wp-tracking-consent-advanced") {
                        $("#wp-tracking-consent-main").addClass("hide");
                        $("#wp-tracking-consent-main").removeClass("active");
                        $("#wp-tracking-consent-advanced").removeClass("hide");
                        $("#wp-tracking-consent-advanced").addClass("active");
                        $("#wp-tracking-consent-theme").addClass("hide");
                        $("#wp-tracking-consent-theme").removeClass("active");
                    } else if ($(this).attr("href") === "#wp-tracking-consent-theme") {
                        $("#wp-tracking-consent-main").addClass("hide");
                        $("#wp-tracking-consent-main").removeClass("active");
                        $("#wp-tracking-consent-advanced").addClass("hide");
                        $("#wp-tracking-consent-advanced").removeClass("active");
                        $("#wp-tracking-consent-theme").removeClass("hide");
                        $("#wp-tracking-consent-theme").addClass("active");
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
        register_setting("wp-tracking-consent", "wp_tracking_consent_description");
        register_setting("wp-tracking-consent", "wp_tracking_consent_accept_button");
        register_setting("wp-tracking-consent", "wp_tracking_consent_decline_button");
        register_setting("wp-tracking-consent", "wp_tracking_consent_read_more_page");
        register_setting("wp-tracking-consent", "wp_tracking_consent_token_auth");
        register_setting("wp-tracking-consent", "wp_tracking_consent_site_id");
        register_setting("wp-tracking-consent", "wp_tracking_consent_matomo_url");


        add_settings_section(
            "wp-tracking-consent-main",
            "",
            null,
            "wp-tracking-consent"
        );

        add_settings_field(
            "wp_tracking_consent_token_auth",
            __("Token auth", "wp-tracking-consent"),
            [self::class, "render_token_auth_field"],
            "wp-tracking-consent",
            "wp-tracking-consent-main"
        );

        add_settings_field(
            "wp_tracking_consent_site_id",
            __("Site ID", "wp-tracking-consent"),
            [self::class, "render_site_id_field"],
            "wp-tracking-consent",
            "wp-tracking-consent-main"
        );

        add_settings_field(
            "wp_tracking_consent_matomo_url",
            __("Matomo URL", "wp-tracking-consent"),
            [self::class, "render_matomo_url_field"],
            "wp-tracking-consent",
            "wp-tracking-consent-main"
        );

        add_settings_field(
            "wp_tracking_consent_description",
            __("Description", "wp-tracking-consent"),
            [self::class, "render_description_field"],
            "wp-tracking-consent",
            "wp-tracking-consent-main"
        );

        add_settings_field(
            "wp_tracking_consent_accept_button",
            __("Accept Button Text", "wp-tracking-consent"),
            [self::class, "render_accept_button_field"],
            "wp-tracking-consent",
            "wp-tracking-consent-main"
        );

        add_settings_field(
            "wp_tracking_consent_decline_button",
            __("Decline Button Text", "wp-tracking-consent"),
            [self::class, "render_decline_button_field"],
            "wp-tracking-consent",
            "wp-tracking-consent-main"
        );

        add_settings_field(
            "wp_tracking_consent_read_more_page",
            __("Read More Page", "wp-tracking-consent"),
            [self::class, "render_read_more_page_field"],
            "wp-tracking-consent",
            "wp-tracking-consent-main"
        );

        // settings for the theme tab
        register_setting("wp-tracking-consent-theme", "wp_tracking_consent_theme_background_color");
        register_setting("wp-tracking-consent-theme", "wp_tracking_consent_theme_text_color");
        register_setting("wp-tracking-consent-theme", "wp_tracking_consent_theme_primary_color");
        register_setting("wp-tracking-consent-theme", "wp_tracking_consent_theme_banner_class");
        register_setting("wp-tracking-consent-theme", "wp_tracking_consent_theme_button_class");
        register_setting("wp-tracking-consent-theme", "wp_tracking_consent_theme_radius");

        add_settings_section(
            "wp-tracking-consent-theme",
            "",
            null,
            "wp-tracking-consent-theme"
        );

        add_settings_field(
            "wp_tracking_consent_theme_background_color",
            __("Background Color", "wp-tracking-consent"),
            [self::class, "render_theme_background_color_field"],
            "wp-tracking-consent-theme",
            "wp-tracking-consent-theme"
        );

        add_settings_field(
            "wp_tracking_consent_theme_text_color",
            __("Text Color", "wp-tracking-consent"),
            [self::class, "render_theme_text_color_field"],
            "wp-tracking-consent-theme",
            "wp-tracking-consent-theme"
        );

        add_settings_field(
            "wp_tracking_consent_theme_primary_color",
            __("Primary Color", "wp-tracking-consent"),
            [self::class, "render_theme_primary_color_field"],
            "wp-tracking-consent-theme",
            "wp-tracking-consent-theme"
        );

        add_settings_field(
            "wp_tracking_consent_theme_banner_class",
            __("Banner Class", "wp-tracking-consent"),
            [self::class, "render_theme_banner_class_field"],
            "wp-tracking-consent-theme",
            "wp-tracking-consent-theme"
        );

        add_settings_field(
            "wp_tracking_consent_theme_button_class",
            __("Button Class", "wp-tracking-consent"),
            [self::class, "render_theme_button_class_field"],
            "wp-tracking-consent-theme",
            "wp-tracking-consent-theme"
        );

        add_settings_field(
            "wp_tracking_consent_theme_radius",
            __("Radius", "wp-tracking-consent"),
            [self::class, "render_theme_radius_field"],
            "wp-tracking-consent-theme",
            "wp-tracking-consent-theme"
        );
    }

    public static function render_description_field()
    {
        $value = get_option('wp_tracking_consent_description');
        if (!$value) {
            $value = __("We use cookies to ensure that we give you the best experience on our website. If you continue to use this site we will assume that you are happy with it.", "wp-tracking-consent");
        }
        $editor_id = 'wp_tracking_consent_description';
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
        $value = get_option('wp_tracking_consent_accept_button');
        if (!$value) {
            $value = __("Accept", "wp-tracking-consent");
        }
        echo '<input type="text" id="wp_tracking_consent_accept_button" name="wp_tracking_consent_accept_button" value="' . esc_attr($value) . '" class="large-text">';
    }

    public static function render_decline_button_field()
    {
        $value = get_option('wp_tracking_consent_decline_button');
        if (!$value) {
            $value = __("Decline", "wp-tracking-consent");
        }
        echo '<input type="text" id="wp_tracking_consent_decline_button" name="wp_tracking_consent_decline_button" value="' . esc_attr($value) . '" class="large-text">';
    }

    public static function render_read_more_page_field()
    {
        $value = get_option('wp_tracking_consent_read_more_page');
        echo '<select id="wp_tracking_consent_read_more_page" name="wp_tracking_consent_read_more_page" class="large-text">';
        echo '<option value="">' . __("Select a page", "wp-tracking-consent") . '</option>';

        $pages = get_pages();
        foreach ($pages as $page) {
            $selected = selected($value, $page->ID, false);
            echo '<option value="' . esc_attr($page->ID) . '" ' . $selected . '>' . esc_html($page->post_title) . '</option>';
        }

        echo '</select>';
    }

    public static function render_token_auth_field()
    {
        $value = get_option('wp_tracking_consent_token_auth');
        if (!$value) {
            $value = "anonymous";
        }
        echo '<input type="text" id="wp_tracking_consent_token_auth" name="wp_tracking_consent_token_auth" placeholder="anonymous" value="' . esc_attr($value) . '" class="large-text">';
    }

    public static function render_site_id_field()
    {
        $value = get_option('wp_tracking_consent_site_id');
        echo '<input type="text" placeholder="0" id="wp_tracking_consent_site_id" name="wp_tracking_consent_site_id" value="' . esc_attr($value) . '" class="large-text">';
    }


    public static function render_matomo_url_field()
    {
        $value = get_option('wp_tracking_consent_matomo_url');
        echo '<input type="text" id="wp_tracking_consent_matomo_url" placeholder="https://<your-matomo.domain>" name="wp_tracking_consent_matomo_url" value="' . esc_attr($value) . '" class="large-text">';
    }

    public static function render_theme_background_color_field()
    {
        $value = get_option('wp_tracking_consent_theme_background_color');
        if (!$value) {
            $value = "#ffffff";
        }
        echo '<input type="color" id="wp_tracking_consent_theme_background_color" name="wp_tracking_consent_theme_background_color" value="' . esc_attr($value) . '" class="large-text">';
    }

    public static function render_theme_text_color_field()
    {
        $value = get_option('wp_tracking_consent_theme_text_color');
        if (empty($value)) {
            $value = "#000000";
        }

        echo '<input type="color" id="wp_tracking_consent_theme_text_color" name="wp_tracking_consent_theme_text_color" value="' . esc_attr($value) . '" class="large-text">';
    }

    public static function render_theme_primary_color_field()
    {
        $value = get_option('wp_tracking_consent_theme_primary_color');
        if (empty($value)) {
            $value = "#000000";
        }
        echo '<input type="color" id="wp_tracking_consent_theme_primary_color" name="wp_tracking_consent_theme_primary_color" value="' . esc_attr($value) . '" class="large-text">';
    }

    public static function render_theme_banner_class_field()
    {
        $value = get_option('wp_tracking_consent_theme_banner_class');
        echo '<input type="text" id="wp_tracking_consent_theme_banner_class" name="wp_tracking_consent_theme_banner_class" value="' . esc_attr($value) . '" class="large-text">';
    }

    public static function render_theme_button_class_field()
    {
        $value = get_option('wp_tracking_consent_theme_button_class');
        echo '<input type="text" id="wp_tracking_consent_theme_button_class" name="wp_tracking_consent_theme_button_class" value="' . esc_attr($value) . '" class="large-text">';
    }

    public static function render_theme_radius_field()
    {
        $value = get_option('wp_tracking_consent_theme_radius');
        if (empty($value)) {
            $value = "0";
        }
        echo '<input type="number" id="wp_tracking_consent_theme_radius" name="wp_tracking_consent_theme_radius" value="' . esc_attr($value) . '" class="large-text">';
    }
}
