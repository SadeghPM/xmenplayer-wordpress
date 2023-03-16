<?php

trait list_videos
{
public function videos_list_page()
{
    if ( isset( $_GET['message'] ) && $_GET['message'] == 'video-created' ) {
    printf(
        '<div class="updated notice is-dismissible"><p>%s</p></div>',
        esc_html__( 'Video created successfully.', 'xmenplayer' )
    );
}
?>

<div class="wrap">
	<h1 class="wp-heading-inline"><?php echo __("Your Videos", "xmenplayer"); ?></h1>
	<?php
	$current_page = isset($_GET["paged"]) ? absint($_GET["paged"]) : 1;
	$search_query = isset($_GET["q"]) ? sanitize_text_field($_GET["q"]) : "";
	$response_body = $this->request("/videos?page=" . $current_page . "&q=" . urlencode($search_query));
	?>
	<a href="https://wordpress.test/wp-admin/admin.php?page=xmenplayer-plugin-create" class="page-title-action"><?php echo __("New Video", "xmenplayer"); ?></a>
	<hr class="wp-header-end">
	<form method="get" action="<?php echo admin_url("admin.php"); ?>">
		<p class="search-box">
			<input type="hidden" name="page" value="xmenplayer-videos-list">
			<input type="search" name="q" value="<?php echo esc_attr($search_query); ?>" placeholder="<?php echo __("Search by title", "xmenplayer"); ?>">
			<input type="submit" class="button" value="<?php echo __("Search", "xmenplayer"); ?>">
		</p>
	</form>
	<div class="tablenav top"></div>
	<?php
	if (is_array($response_body) && !empty($response_body["data"])) {
	$pagination = $response_body;
	$videos = $response_body["data"];
	?>
	<table class="wp-list-table widefat fixed striped table-view-list media">
		<thead>
		<tr>
			<th class="manage-column column-cb check-column" scope="col"></th>
			<th class="manage-column column-title column-primary"><?php echo __("Title", "xmenplayer"); ?></th>
			<th class="manage-column column-status"><?php echo __("Status", "xmenplayer"); ?></th>
			<th class="manage-column column-duration"><?php echo __("Duration", "xmenplayer"); ?></th>
			<th class="manage-column column-shortcode"><?php echo __("Shortcode", "xmenplayer"); ?></th>
			<th class="manage-column column-date"><?php echo __("Created At", "xmenplayer"); ?></th>
		</tr>
		</thead>
		<tbody id='the-list'>
		<?php
		foreach ($videos as $video) {
			$date_created = wp_date(
				get_option("date_format"),
				strtotime($video["created_at"])
			);
			?>
			<tr class='author-self status-inherit'>
				<th class="check-column" scope="row"></th>
				<td class="title column-title has-row-actions column-primary">
					<strong class="has-media-icon">
						<a href="<?php echo esc_url(
							add_query_arg(
								[
									"page" => "xmenplayer-plugin-video-detail",
									"id" => $video["id"],
								],
								admin_url("admin.php")
							)
						); ?>">
                    <span class="media-icon video-icon">
                        <img width="48" height="64" src="<?php echo esc_url(
	                        $video["thumbnail_url"] ??
	                        "/wp-includes/images/media/video.png"
                        ); ?>" class="attachment-60x60 size-60x60" alt="" decoding="async" loading="lazy">
                    </span>
							<?php echo esc_html($video["title"]); ?>
						</a>
					</strong>
					<p class="filename"><?php echo esc_html($video["original_file"]["name"] ?? ""); ?></p>
					<div class="row-actions">
                <span class="edit">
                    <a href="<?php echo esc_url(
	                    add_query_arg(
		                    [
			                    "page" => "xmenplayer-plugin-video-detail",
			                    "id" => $video["id"],
		                    ],
		                    admin_url("admin.php")
	                    )
                    ); ?>">
                        <?php _e("View", "xmenplayer"); ?>
                    </a>
                </span>
						|
						<span class="trash">
                    <a href="#" onclick="my_plugin_delete_video(<?php echo $video["id"]; ?>)">
                        <?php _e("Delete", "xmenplayer"); ?>
                    </a>
                </span>
					</div>
				</td>
				<td class="column-status"><?php echo esc_html($video["status"]); ?></td>
				<td class="column-duration"><?php echo esc_html($video["duration_formatted"]); ?></td>
				<td class="shortcode column-shortcode" dir="ltr">
            <span class="shortcode">
                <input type="text" onfocus="this.select();" readonly="readonly" value="[xmenplayer video='<?php echo esc_html($video["id"]); ?>']" class=" code">
            </span>
				</td>
				<td class="date column-date"><?php echo esc_html($date_created); ?></td>
			</tr>
		<?php } ?>

        </tbody>
        </table>

        <div class="tablenav bottom">
            <div class="tablenav-pages" style="margin: 1em 0">
                <?php echo
                paginate_links([
                    "base" => add_query_arg("paged", "%#%"),
                    "format" => "",
                    "prev_text" => __("&laquo;"),
                    "next_text" => __("&raquo;"),
                    "total" => $pagination["last_page"],
                    "current" => $pagination["current_page"],
                ]);
                ?>
        </div>
        </div>
    <?php
        }
    }
}
