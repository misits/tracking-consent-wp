<?php

namespace WPTrackingConsent\includes;

class AssetServices
{
    const ASYNC_SCRIPTS = [];
    const DEFER_SCRIPTS = ["app"];

    public static function register()
    {
        add_action("wp_enqueue_scripts", [self::class, "enqueue_styles"]);
        add_action("wp_enqueue_scripts", [self::class, "enqueue_scripts"]);
        add_action("wp_enqueue_scripts", [self::class, "enqueue_matomo_script"]);
        add_action("admin_enqueue_scripts", [self::class, "enqueue_styles"]);
        add_action("admin_enqueue_scripts", [self::class, "enqueue_scripts"]);
        add_filter("script_loader_tag", [self::class, "loader"], 10, 2);
    }

    public static function unregister()
    {
        remove_action("wp_enqueue_scripts", [self::class, "enqueue_styles"]);
        remove_action("wp_enqueue_scripts", [self::class, "enqueue_scripts"]);
        remove_action("wp_enqueue_scripts", [self::class, "enqueue_matomo_script"]);
        remove_action("admin_enqueue_scripts", [self::class, "enqueue_styles"]);
        remove_action("admin_enqueue_scripts", [self::class, "enqueue_scripts"]);
        remove_filter("script_loader_tag", [self::class, "loader"], 10, 2);
    }

    public static function enqueue_styles()
    {
        if (!self::is_vite_running()) {
            // Production environment (Local build)
            $assets_dir = plugin_dir_path(dirname(__FILE__, 1)) . "dist/assets/css";
            $files = scandir($assets_dir);

            foreach ($files as $file) {
                if (preg_match('/\.css$/', $file)) {
                    wp_enqueue_style(
                        "vite-" . basename($file, ".css"),
                        plugins_url("/dist/assets/css/" . $file, dirname(__FILE__, 1)),
                        [],
                        null
                    );
                }
            }
        }
    }

    public static function enqueue_scripts()
    {
        $head = is_admin() ? 'admin_head' : 'wp_head';

        if (self::is_vite_running()) {
            // Development environment (Vite server)
            add_action($head, [self::class, 'vite_dev_server_scripts']);
        } else {
            remove_action($head, [self::class, 'vite_dev_server_scripts']);
            // Production environment (local build)
            self::enqueue_production_scripts();
        }
    }

    public static function vite_dev_server_scripts()
    {
        if (self::is_vite_running()) {
            echo '
            <!-- Vite Dev Server -->
            <script type="module">
                import RefreshRuntime from "http://localhost:5173/@react-refresh"
                RefreshRuntime.injectIntoGlobalHook(window)
                window.$RefreshReg$ = () => {}
                window.$RefreshSig$ = () => (type) => type
                window.__vite_plugin_react_preamble_installed__ = true
            </script>';
            echo '<script type="module" crossorigin src="http://localhost:5173/@vite/client"></script>';
            echo '<script type="module" crossorigin src="http://localhost:5173/src/main.jsx"></script>';
            echo '<!-- End Vite Dev Server -->';
        } else {
            echo '<!-- Vite Dev Server -->';
            echo '<!-- End Vite Dev Server -->';
        }
    }

    public static function enqueue_matomo_script()
    {
        if (!get_option('wp_tracking_consent_enable_matomo')) {
            return;
        }
        $domain = $_SERVER['SERVER_NAME'];
        $matomoUrl = get_option('wp_tracking_consent_matomo_url');
        // Add a trailing slash if it's not present
        $matomoUrl = rtrim($matomoUrl, '/') . '/';

        echo '<!-- Matomo -->';
        echo '<script>';
        echo 'var _paq = window._paq = window._paq || [];';
        echo '/* tracker methods like "setCustomDimension" should be called before "trackPageView" */';
        /*  Options  */
        if (get_option('wp_tracking_consent_set_document_title')) {
            echo '_paq.push(["setDocumentTitle", document.domain + "/" + document.title]);';
        }
        if (get_option('wp_tracking_consent_set_cookie_domain')) {
            echo '_paq.push(["setCookieDomain", "*.' . $domain . '"]);';
        }
        if (get_option('wp_tracking_consent_set_domains')) {
            echo '_paq.push(["setDomains", ["*.' . $domain . '"]]);';
        }
        if (get_option('wp_tracking_consent_set_do_not_track')) {
            echo '_paq.push(["setDoNotTrack", true]);';
        }
        if (get_option('wp_tracking_consent_disable_cookies')) {
            echo '_paq.push(["disableCookies"]);';
        }
        if (get_option('wp_tracking_consent_track_page_view', true)) {
            echo '_paq.push(["trackPageView"]);';
        }
        if (get_option('wp_tracking_consent_enable_link_tracking', true)) {
            echo '_paq.push(["enableLinkTracking"]);';
        }
        /* End Options */
        echo '_paq.push(["requireCookieConsent"]);';
        echo '(function() {';
        echo '    var u="'. $matomoUrl .'";';
        echo '    _paq.push(["setTrackerUrl", u+"matomo.php"]);';
        echo '    _paq.push(["setSiteId", "'. get_option('wp_tracking_consent_site_id') . '"]);';
        echo '    var d=document, g=d.createElement("script"), s=d.getElementsByTagName("script")[0];';
        echo '    g.async=true; g.src=u+"matomo.js"; s.parentNode.insertBefore(g,s);';
        echo '})();';
        echo '</script>';
        echo '<!-- End Matomo Code -->';
    }

    private static function is_vite_running()
    {
        $dev_file = plugin_dir_path(dirname(__FILE__, 1)) . ".dev";

        if (file_exists($dev_file)) {
            return true;
        }

        return false;
    }

    private static function enqueue_production_scripts()
    {
        $assets_dir = plugin_dir_path(dirname(__FILE__, 1)) . "dist/assets/js";
        $files = scandir($assets_dir);

        foreach ($files as $file) {
            if (preg_match('/\.js$/', $file)) {
                wp_enqueue_script(
                    "vite-" . basename($file, ".js"),
                    plugins_url("/dist/assets/js/" . $file, dirname(__FILE__, 1)),
                    [],
                    null,
                    true
                );
                add_filter(
                    "script_loader_tag",
                    function ($tag, $handle, $src) use ($file) {
                        if ($handle === "vite-" . basename($file, ".js")) {
                            $tag = '<script type="module" src="' . esc_url($src) . '"></script>';
                        }
                        return $tag;
                    },
                    10,
                    3
                );
            }
        }
    }

    public static function loader($tag, $handle)
    {
        if (in_array($handle, self::ASYNC_SCRIPTS)) {
            $tag = str_replace(" src", " async src", $tag);
        }

        if (in_array($handle, self::DEFER_SCRIPTS)) {
            $tag = str_replace(" src", " defer src", $tag);
        }

        return $tag;
    }
}
