<?php

add_action( 'widgets_init', 'tie_youtube_widget_box' );
function tie_youtube_widget_box() {
	register_widget( 'tie_youtube_widget' );
}
class tie_youtube_widget extends WP_Widget {

	public function __construct(){
		$widget_ops 	= array( 'classname' => 'youtube-widget'  );
		$control_ops 	= array( 'width' => 250, 'height' => 350, 'id_base' => 'youtube-widget' );
		parent::__construct( 'youtube-widget', THEME_NAME .' - '.__( 'YouTube Channel' , 'tie' ) , $widget_ops, $control_ops );
	}

	public function widget( $args, $instance ) {
		extract( $args );

		$title    = apply_filters( 'widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance, $this->id_base );
		$page_url = $instance['page_url'];

		echo $before_widget;
		if ( $title )
			echo $before_title;
			echo $title ;
			echo $after_title;
			wp_enqueue_script( 'tie-google-platform-js', '//apis.google.com/js/platform.js' );

			// Check if it is a channel or a user account
			if ( strpos( $instance['page_url'], 'UC' ) === 0 ){
				$source = 'channelid';
			}
			else{
				$source = 'channel';
			}

			echo '
				<div class="youtube-box fitvidsignore">
					<div class="g-ytsubscribe" data-'.$source.'="' . $page_url . '" data-layout="full" data-count="default"></div>
				</div>
			';
		echo $after_widget;
	}

	public function update( $new_instance, $old_instance ) {
		$instance 				= $old_instance;
		$instance['title'] 		= strip_tags( $new_instance['title'] );
		$instance['page_url'] 	= strip_tags( $new_instance['page_url'] );
		return $instance;
	}

	public function form( $instance ) {
		$defaults = array( 'title' =>__( 'Subscribe to our Channel' , 'tie') );
		$instance = wp_parse_args( (array) $instance, $defaults ); ?>

		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' , 'tie') ?></label>
			<input id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php if( !empty( $instance['title'] ) ) echo $instance['title']; ?>" class="widefat" type="text" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'page_url' ); ?>"><?php _e( 'Channel Name:' , 'tie') ?></label>
			<input id="<?php echo $this->get_field_id( 'page_url' ); ?>" name="<?php echo $this->get_field_name( 'page_url' ); ?>" value="<?php if( !empty( $instance['page_url'] ) ) echo $instance['page_url']; ?>" class="widefat" type="text" />
		</p>


	<?php
	}
}
