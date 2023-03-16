<?php

trait video_detail {
	public function xmenplayer_add_shortcode_to_content( $content ) {
		if ( current_user_can( 'edit_posts' ) ) {
			$shortcode = '<p>[xmenplayer video="VIDEO_ID"]</p>';
			$content   = $content . $shortcode;
		}

		return $content;
	}


	public function xmenplayer_display_video( $atts ) {
		// Extract the VIDEO parameter from the shortcode
		$atts     = shortcode_atts( array(
			'video' => ''
		), $atts );
		$video_id = $atts['video'];

		// Get the video data from the API endpoint
		$video = $this->request( '/videos/' . $video_id );
		// Check if the video is available
		if ( empty( $video ) or ! isset( $video['data'] ) ) {
			return "Error: Video not found";
		}

		$player_html = $video['data']['iframe'];

		return $player_html;
	}


	public function register_template() {
		$template_name = 'Single XmenPlayer Video Template';
		$template_slug = 'single-xmenplayer-video';
		$template_path = plugin_dir_path( __FILE__ ) . 'templates/single-xmenplayer-video.php';

		$post_type = 'video';

		$template = array(
			'post_type'     => $post_type,
			'template_name' => $template_name,
			'template_slug' => $template_slug,
			'template_path' => $template_path,
		);

		$templates = array( $template );

		// Register templates with the WordPress template hierarchy.
		foreach ( $templates as $template ) {
//			xmenplayer_add_template($template);
		}
	}

	public function video_detail_page() {
		$video_id = isset( $_GET['id'] ) ? $_GET['id'] : die( __( 'No video selected', 'xmenplayer' ) );
		$video    = $this->request( '/videos/' . $video_id );

		// Display the video detail
		echo '<div class="wrap">';
		echo '<h1>' . __( 'Video Detail', 'xmenplayer' ) . '</h1>';
		if ( ! empty( $video ) && isset( $video['data'] ) ) {
			$video = $video['data'];
			echo '<table class="wp-list-table widefat  striped">';
			echo '<tbody>';
			echo $this->tr( __( 'Title', 'xmenplayer' ), $video['title'] );
			echo $this->tr( __( 'Status', 'xmenplayer' ), $video['status'] );
			echo $this->tr( __( 'Secured', 'xmenplayer' ), $video['secured'] ? __( 'YES', 'xmenplayer' ) : __( 'NO', 'xmenplayer' ) );
			echo $this->tr( __( 'Size', 'xmenplayer' ), size_format( $video['size'], 2 ) );
			echo $this->tr( __( 'Duration', 'xmenplayer' ), $video['duration_formatted'] ?? 0 );
			echo '<tr>';
			echo '<th>' . __( 'Video Download URL', 'xmenplayer' ) . '</th>';
			echo '<td><input readonly style="width: 100%" type="url" name="video_download" id="video_download" value="' . $video['video_url'] . '"></a></td>';
			echo '</tr>';
			echo '<tr>';
			echo '<th>' . __( 'Player iFrame', 'xmenplayer' ) . '</th>';
			echo '<td><textarea readonly name="player" id="player" cols="30" rows="6" style="width: 100%;direction: ltr" >' . $video['iframe'] . '</textarea></td>';
			echo '</tr>';
			echo '<tr>';
			echo '<th>' . __( 'Preview', 'xmenplayer' ) . '</th>';
			echo '<td style="max-height: 200px;height: 200px">' . $video['iframe'] . '</td>';
			echo '</tr>';
			echo '</tbody>';
			echo '</table>';
		} else {
			echo '<p>' . __( 'No video detail found.', 'xmenplayer' ) . '</p>';
		}
		echo '</div>';
	}

	private function tr( $title, $value ) {
		return '<tr><th>' . esc_html( $title ) . '</th><td>' . esc_html( $value ) . '</td></tr>';
	}

}