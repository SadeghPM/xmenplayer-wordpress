<?php

trait new_video{

	public function process_select_video_form() {
		if ( ! wp_verify_nonce( $_POST['select_video_nonce'], 'select_video_nonce' ) ) {
			wp_die( 'Invalid nonce' );
		}

		$data['file_url'] = esc_url( $_POST['video_url'] );
		$data['title'] = esc_attr( $_POST['title'] );
		$data['secured'] = (bool) $_POST['secured'];
        $response_data = $this->request('/video',$data);
		// call endpoint api to save video

		wp_redirect( admin_url( 'admin.php?page=xmenplayer-plugin-videos&message=video-created' ) );
		exit;
	}
	public function create_new_item_page() {
		echo '<h1>' . __( 'Create New Video', 'xmenplayer' ) . '</h1>';
		?>
        <form action="<?php echo esc_url( admin_url('admin-post.php') ); ?>" method="post">
			<?php wp_nonce_field( 'select_video_nonce', 'select_video_nonce' ); ?>
            <input type="hidden" name="action" value="select_video">
            <div>
                <label for="selected_video"><?php _e( 'Select a Video*', 'xmenplayer' ); ?></label><br>
                <input type="text" id="selected_video" name="video_url" value="" class="regular-text" required>
                <input type="button" value="<?php _e( 'Select Video', 'xmenplayer' ); ?>" class="button-secondary" id="select-video-button">
            </div>
            <div style="margin-top: 10px">
                <label for="video_title"><?php _e( 'Video Title*', 'xmenplayer' ); ?></label><br>
                <input type="text" id="video_title" name="title" value="" class="regular-text" required >
            </div>
            <div style="margin-top: 10px">
                <input type="checkbox" id="video_secure" name="secured" class="checkbox-input">
                <label for="video_secure"><?php _e( 'Secure', 'xmenplayer' ); ?></label>
                <div class="howto"><?php _e( 'Video played based on users ip', 'xmenplayer' ); ?></div>
            </div>
            <p class="submit">
                <input type="submit" value="<?php _e( 'Save Changes', 'xmenplayer' ); ?>" class="button-primary">
            </p>
        </form>

        <script type="text/javascript">
            jQuery(document).ready(function($){
                $('#select-video-button').click(function(e) {
                    e.preventDefault();
                    var videoFrame;

                    if (videoFrame) {
                        videoFrame.open();
                        return;
                    }

                    videoFrame = wp.media({
                        title: '<?php _e( 'Select a Video*', 'xmenplayer' ); ?>',
                        button: {
                            text: '<?php _e( 'Use this video', 'xmenplayer' ); ?>'
                        },
                        multiple: false
                    });

                    videoFrame.on('select', function() {
                        var attachment = videoFrame.state().get('selection').first().toJSON();
                        $('#selected_video').val(attachment.url);
                    });

                    videoFrame.open();
                });
            });
        </script>

		<?php
	}
}