<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$session    = 	new QM_Test_Session();

$test_id    =	$session->get('test_id');

$result     =	$session->get('result');

$show_form_data = true;

if( is_user_ranking_view_result_setting() ) {

	$show_form_data = qm_is_ranking( $test_id, $result['score'], $result['total_score'], false);
}

?>

<div class="col-sm-12">
	<div id="quizmaker-fillform-container" class="fillform-container">
		
		<?php if( $show_form_data ): 

			$current_user = wp_get_current_user();

			$form_settings = get_option('quizmaker_forms_after_play_test_settings');

			$form_data = array();
			
			if( isset($form_settings['form_data']) && $form_settings['form_data'] ){
				$form_data = $form_settings['form_data'];
			}

		?>
		<form method="post" class="fillform_input">

			<div class="fillform_input_value">
				
				<?php if ( !($current_user instanceof WP_User) || $current_user->ID == 0 ): ?>

				<div class="form-group">
					<label for="inputName"><?php _e('Name', 'quizmaker'); ?></label>
					<input type="text" name="name" class="form-control" id="inputName" required/>
				</div>

				<div class="form-group">
					<label for="inputEmail"><?php _e('Email', 'quizmaker'); ?></label>
					<input type="email" name="email" class="form-control" id="inputEmail" required/>
				</div>

				<?php else: ?>

				<div class="form-group">
					<label for="inputName"><?php _e('Name', 'quizmaker'); ?></label>
					<input type="text" readonly class="form-control" id="inputName" value="<?php echo qm_get_formated_user_name($current_user); ?>"/>
				</div>

				<div class="form-group">
					<label for="inputEmail"><?php _e('Email', 'quizmaker'); ?></label>
					<input type="email" readonly class="form-control" id="inputName" value="<?php echo $current_user->user_email; ?>"/>
				</div>

				<?php endif; ?>
				
				<?php if($form_data): ?>
					<?php echo qm_generate_formbuilder( $form_data, array('wrap_name' => 'user_meta') ); ?>
				<?php endif; ?>
				
			</div>

			<div class="fillform_input_submit">
				
				<button class="btn btn-primary btn-lg" type="submit"><?php _e('View Result', 'quizmaker'); ?></button>

				<a class="btn btn-lg" href="<?php echo get_permalink( $test_id ); ?>"><?php _e('Try Again', 'quizmaker'); ?></a>

			</div>

			<input type="hidden" name="quizmaker_doing_fillform" value="1"/>
		</form>
		<?php endif; ?>

	</div>
</div>