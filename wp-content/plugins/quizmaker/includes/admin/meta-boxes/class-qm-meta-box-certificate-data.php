<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


class QM_Meta_Box_Certificate_Data {
	
	public static function output( $post ) {
		
		$background		=	qm_get_post_meta($post->ID, 'certificate_background');
		
		$background_src		=	false;
		$background_attr	=	'';
		
		if( $background ) {
			$background_src	=	wp_get_attachment_image_src( $background, 'full' );
			
			$background_attr	=	'width:' . $background_src[1] . 'px; height:' . $background_src[2] . 'px;';
		}

		$user_meta_fields = get_form_data_after_play_test();

?>
		<div class="qm_certificate_compose_action">
			<button type="button" class="button qm_media_upload"><span class="wp-media-buttons-icon"></span> <?php _e('Add Background', 'quizmaker'); ?></button>
			<button type="button" class="button qm_add_text-custom"><?php _e('Custom Text', 'quizmaker'); ?></button>
			<button type="button" class="button qm_add_text-name"><?php _e('Name', 'quizmaker'); ?></button>
			<button type="button" class="button qm_add_text-score"><?php _e('Score', 'quizmaker'); ?></button>
			<button type="button" class="button qm_add_text-date"><?php _e('Date', 'quizmaker'); ?></button>
			<button type="button" class="button qm_add_text-rank"><?php _e('Rank', 'quizmaker'); ?></button>
			<button type="button" class="button qm_add_text-test-name"><?php _e('Test Name', 'quizmaker'); ?></button>
			<button type="button" class="button qm_add_text-id"><?php _e('Cert ID', 'quizmaker'); ?></button>
			<button type="button" class="button qm_add_text-random-cert-id"><?php _e('User Cert ID', 'quizmaker'); ?></button>

			<?php if( $user_meta_fields ): ?>

			<?php foreach( $user_meta_fields as $um ): ?>

				<?php if(in_array($um['type'], array('text', 'number', 'select', 'radio-group', 'checkbox-group'))): ?>
			<button type="button" class="button qm_add_text-usermeta" data-name="<?php echo $um['name']; ?>" data-label="<?php echo $um['label']; ?>"><?php echo $um['label']; ?></button>
				<?php endif; ?>

			<?php endforeach; ?>

			<?php endif; ?>

			<button type="button" class="button qm_certificate_remove"><?php _e('Remove', 'quizmaker'); ?></button>
			<button type="button" class="button button-primary qm_certificate_save"><?php _e('Update', 'quizmaker'); ?></button>
			
			<div class="qm_certificate_compose_action_sub-panel text">
				<div class="qm-form-groups">
					<div class="qm-group-input-label">
						<label><?php _e('Font Size', 'quizmaker'); ?></label>
						<div class="g-input">
							<input type="text" id="qm_certificate_font_size" data-name="font_size" value="20"/>
						</div>
					</div>
					<div class="qm-group-input-label">
						<label><?php _e('Font Family', 'quizmaker'); ?></label>
						<div class="g-input">
							<select id="qm_certificate_font_family" data-name="font_name">
								<option value="alegreya_regular">Alegreya</option>
								<option value="bebasneue_regular">BebasNeue</option>
								<option value="bebasneue_light">BebasNeue Light</option>
								<option value="bebasneue_thin">BebasNeue Thin</option>
								<option value="bebasneue_bold">BebasNeue Bold</option>
								<option value="bebasneue_book">BebasNeue Book</option>
								<option value="greatvibes_regular">Great Vibes</option>
								<option value="notosans_regular">Noto Sans Regular</option>
								<option value="notosans_bold">Noto Sans Bold</option>
								<option value="notosans_italic">Noto Sans Italic</option>
								<option value="notosans_bolditalic">Noto Sans Bold Italic</option>
								<option value="opensans_regular">Open Sans</option>
								<option value="opensans_thin">Open Sans Thin</option>
								<option value="opensans_light">Open Sans Light</option>
								<option value="opensans_bold">Open Sans Bold</option>
								<option value="opensans_italic">Open Sans Italic</option>
								<option value="opensans_lightitalic">Open Sans Light Italic</option>
								<option value="opensans_bolditalic">Open Sans Bold Italic</option>
								<option value="opensans_extrabold">Open Sans Extra Bold</option>
								<option value="opensans_extrabolditalic">Open Sans Extra Bold Italic</option>
								
							</select>
						</div>
					</div>
					<div class="qm-group-input-label">
						<label><?php _e('Text Align', 'quizmaker'); ?></label>
						<div class="g-input">
							<select id="qm_certificate_text_align" data-name="text_align">
								<option value="left"><?php _e('Align Left', 'quizmaker'); ?></option>
								<option value="center"><?php _e('Align Center', 'quizmaker'); ?></option>
								<option value="right"><?php _e('Align Right', 'quizmaker'); ?></option>
							</select>
						</div>
					</div>
					<div class="qm-group-input-label">
						<label><?php _e('Color', 'quizmaker'); ?></label>
						<div class="g-input">
							<input type="text" id="qm_certificate_color" data-name="color" value="#000000"/>
						</div>
					</div>
					<div class="qm-group-input-label">
						<label><?php _e('Content', 'quizmaker'); ?></label>
						<div class="g-input">
							<input type="text" id="qm_certificate_text" data-name="content" value="Sample Text"/>
						</div>
					</div>
					
				</div>
			</div>
		</div>
		
		<div class="qm_certificate_compose_container" id="qm_certificate_compose_container">
			<div id="qm_certificate_compose_scrollpanel">
				<div class="background" style="<?php echo $background_attr; ?>">
				<?php if($background_src && is_array($background_src)): ?>
				<img src="<?php echo $background_src[0]; ?>"/>
				<?php endif; ?>
				</div>
			</div>
		</div>
		
		<div id="qm_certificate_input">
			<input type="hidden" name="qm_certificate_background" value="" id="qm_certificate_background"/>
			<input type="hidden" name="qm_certificate_data" value="" id="qm_certificate_data"/>
		</div>
<?php 
	}
	
	public static function save( $post_id, $post ) {
			
			$background_id		=	$_POST['qm_certificate_background'];
			
			$background_id		=	$background_id ? absint($background_id) : 0;
			
			if($background_id){

				qm_update_post_meta( $post_id, array( 'certificate_background' => $background_id ) );
			}

			$certid = get_post_meta( $post_id, 'certid', true );

			if( !$certid ) {
				
				$certid = substr(uniqid(), 0, 10);

				update_post_meta( $post_id, 'certid', $certid );
			}
	}
}