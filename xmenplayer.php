<?php
/*
Plugin Name: XmenPLayer Plugin
Description: We offer the ultimate in online video security, so you can stream your favorite content with confidence. Our platform is designed to protect your videos and memories, so you can enjoy them for years to come.
Version: 1.0
Author: xmenplayer
Author URI: https://xmenplayer.ir
*/

include_once "settings_video.php";
include_once "new_video.php";
include_once "list_videos.php";
include_once "video_detail.php";

class xmenplayer
{
    use settings_video, new_video, list_videos, video_detail;

    const API_URL = "https://video.test/api/v1";
    public $token = "";

    public function __construct()
    {
        $this->token = esc_attr(get_option("xmenplayer_plugin_api_token"));
        add_action("plugins_loaded", [$this, "xmenplayer_load_textdomain"]);
        add_action("admin_menu", [$this, "admin_menu"]);
        add_action("admin_init", [$this, "register_settings"]);
        add_action("admin_enqueue_scripts", [$this, "load_media_library_api"]);
        add_action("admin_post_select_video", [
            $this,
            "process_select_video_form",
        ]);
        //		add_action('wp_loaded', [$this,'register_template']);
        //		add_filter( 'single_template', 'xmenplayer_register_template' );
        add_shortcode("xmenplayer", [$this, "xmenplayer_display_video"]);
        //		add_filter( 'the_content', [$this,'xmenplayer_add_shortcode_to_content'] );
    }

    public function xmenplayer_load_textdomain()
    {
        load_plugin_textdomain(
            "xmenplayer",
            false,
            dirname(plugin_basename(__FILE__)) . "/languages/"
        );
    }

    public function load_media_library_api()
    {
        wp_enqueue_media();
        wp_enqueue_style(
            "xmenplayer-styles",
            plugin_dir_url(__FILE__) . "style.css"
        );
    }

    public function admin_menu()
    {
        add_menu_page(
            __("Xmen Player", "xmenplayer"),
            __("Xmen Player", "xmenplayer"),
            "manage_options",
            "xmenplayer-plugin-videos",
            [$this, "videos_list_page"],
            "dashicons-format-video",
            100
        );

        add_submenu_page(
            "xmenplayer-plugin-videos",
            __("All Videos", "xmenplayer"),
            __("All Videos", "xmenplayer"),
            "manage_options",
            "xmenplayer-plugin-videos",
            [$this, "videos_list_page"]
        );
        add_submenu_page(
            "xmenplayer-plugin-videos",
            __("New Video", "xmenplayer"),
            __("New Video", "xmenplayer"),
            "manage_options",
            "xmenplayer-plugin-create",
            [$this, "create_new_item_page"]
        );
        add_submenu_page(
            "xmenplayer-plugin-videos",
            __("Settings", "xmenplayer"),
            __("Settings", "xmenplayer"),
            "manage_options",
            "xmenplayer-plugin-setting",
            [$this, "settings_page"]
        );
        add_submenu_page(
            null,
            __("Video Detail", "xmenplayer"),
            __("Video Detail", "xmenplayer"),
            "manage_options",
            "xmenplayer-plugin-video-detail",
            [$this, "video_detail_page"]
        );
    }

    public function request($endpoint, $post_data = [])
    {
        $data = [];
        if (empty($post_data)) {
            $response = wp_remote_get(self::API_URL . $endpoint, [
                "sslverify" => false,
                "headers" => ["Authorization" => "Bearer " . $this->token],
            ]);
        } else {
            $response = wp_remote_post(self::API_URL . $endpoint, [
                "sslverify" => false,
                "headers" => ["Authorization" => "Bearer " . $this->token],
                "body" => ($post_data),
            ]);
        }
        if (!is_wp_error($response)) {
            $data = json_decode(wp_remote_retrieve_body($response), true);
        }

        return $data;
    }
}

new xmenplayer();
