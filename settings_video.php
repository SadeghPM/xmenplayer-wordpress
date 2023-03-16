<?php

trait settings_video
{
    public function register_settings()
    {
        register_setting(
            "xmenplayer_plugin_options",
            "xmenplayer_plugin_api_token"
        );
    }

    public function settings_page()
    {
        $data = [];
        if (!empty($this->token)) {
            $data = $this->request("/user");
            if (empty($data)) {
                add_settings_error(
                    "xmenplayer_plugin_options",
                    "settings_updated",
                    __("Error in getting token information", "xmenplayer"),
                    "error"
                );
            }
        }
        ?>
        <h1><?php _e("Settings Page", "xmenplayer"); ?></h1>
        <?php settings_errors("xmenplayer_plugin_options"); ?>
        <div><?= isset($data["name"])
            ? __("API user:", "xmenplayer") . " " . $data["name"]
            : "" ?></div>

        <form action="options.php" method="post">
			<?php
               settings_fields("xmenplayer_plugin_options");
               do_settings_sections("xmenplayer_plugin_options");
               ?>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row"><?php _e(
                        "API Token:",
                        "xmenplayer"
                    ); ?></th>
                    <td>
                        <input type="text" name="xmenplayer_plugin_api_token" style="width: 80%" dir="ltr"
                               value="<?php echo esc_attr(
                                   get_option("xmenplayer_plugin_api_token")
                               ); ?>"/>
                    </td>
                </tr>
            </table>
			<?php submit_button(__("Save Changes", "xmenplayer")); ?>
        </form>
		<?php
    }
}
