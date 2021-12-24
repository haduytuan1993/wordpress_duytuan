<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

add_filter( 'mycred_setup_hooks', 'quizmaker_mycred_add_hooks', 10, 2 );

add_action( 'mycred_load_hooks', 'quizmaker_mycred_load_hook' );

// In admin after save ranking
add_action( 'quizmaker_test_data_form_edit_ranking', 'quizmaker_mycred_test_data_form_edit_ranking' );
add_action( 'quizmaker_test_data_form_add_ranking', 'quizmaker_mycred_test_data_form_add_ranking' );

add_filter( 'quizmaker_test_data_save_ranking', 'quizmaker_mycred_test_data_save_ranking', 10, 2 );


function quizmaker_mycred_add_hooks( $installed, $point_type ) {
	
	unset( $installed['quizmaker'] );

	$installed['quizmaker'] = array(
		'title'        => __('Points for passing test', 'quizmaker'),
		'description'  => __('Award points for pass test in Quizmaker plugin', 'quizmaker'),
		'callback'     => array( 'Quizmaker_myCRED_Hook' )
	);

	return $installed;
}

function quizmaker_mycred_load_hook() {

	class Quizmaker_myCRED_Hook extends myCRED_Hook {

		function __construct( $hook_prefs, $type ) {

			parent::__construct( array(
				'id'       => 'quizmaker',
				'defaults' => array(
					'new_member' => array(
						'creds'    => 0,
						'log'      => '%singular% for new member'
					),
					'pass_test' => array(
						'creds'	=>	0,
						'log'	=>	'Pass test: '
					)
				)
			), $hook_prefs, $type );

		}

		/**
		 * Run
		 * Fires by myCRED when the hook is loaded.
		 * Used to hook into any instance needed for this hook
		 * to work.
		 */
		public function run() {

			add_action( 'quizmaker_after_submit_result', array( $this, 'after_submit_result' ), 10, 2 );
			
			add_action( 'quizmaker_after_register', array( $this, 'after_user_register' ) );
		}

		public function after_submit_result( $result_id, $test_id ) {

			$result = QM_Test::get_result( $result_id );

			if(!$result || !isset($result['ranking_data'])){

				return false;
			}

			$ranking	=	$result['ranking_data'];
			
			if(!isset($ranking['mycred'])){

				return false;
			}

			$test 		=	new QM_Test($result['test_id']);

			$point 		=	absInt($ranking['mycred']);
			$user_id	=	absInt($result['user_id']);
			$test_id	=	absInt($result['test_id']);

			$log 		=	$this->prefs['pass_test']['log'] . $test->get_html_permalink();

			$this->core->add_creds(
				'pass_test',
				$user_id,
				$point,
				$log,
				$test_id,
				array( 'ref_type' => 'test' ),
				$this->mycred_type
			);
		}

		function after_user_register( $user_id ) {

			$this->core->add_creds(
				'new_member',
				$user_id,
				$this->prefs['new_member']['creds'],
				$this->prefs['new_member']['log'],
				$user_id,
				array( 'ref_type' => 'user' ),
				$this->mycred_type
			);
		}

		/**
		 * Hook Settings
		 * Needs to be set if the hook has settings.
		 */
		 public function preferences() {

			// Our settings are available under $this->prefs
			$prefs = $this->prefs;
?>
			
<label for="<?php echo $this->field_id( array( 'new_member', 'creds' ) ); ?>" class="subheader"><?php echo $this->core->template_tags_general( __( '%plural% for New Member', 'quizmaker' ) ); ?></label>
	<ol>
		<li>
			<div class="h2"><input type="text" name="<?php echo $this->field_name( array( 'new_member', 'creds' ) ); ?>" id="<?php echo $this->field_id( array( 'new_member', 'creds' ) ); ?>" value="<?php echo $this->core->number( $prefs['new_member']['creds'] ); ?>" size="8" /></div>
		</li>
	</ol>
<?php
		}

		/**
		 * Sanitize Preferences
		 * If the hook has settings, this method must be used
		 * to sanitize / parsing of settings.
		 */
		public function sanitise_preferences( $data ) {

			

			return $data;
		}
	}
}

function quizmaker_mycred_test_data_form_edit_ranking( $rank ){
	$value = 0;

	if(isset($rank['mycred'])){
		$value = absInt($rank['mycred']);
	}
?>
<div class="qm-group-input-label">
	<label><?php _e('myCRED', 'quizmaker'); ?></label>

	<div class="g-input">
		<input type="text" name="qm_ranking_<?php echo $rank['id']; ?>_mycred" value="<?php echo $value; ?>"/>
	</div>
</div>
<?php
}

function quizmaker_mycred_test_data_form_add_ranking(){
?>

<div class="qm-group-input-label">
	<label><?php _e('myCRED', 'quizmaker'); ?></label>

	<div class="g-input">
		<input type="text" name="qm_ranking_mycred" value="0"/>
	</div>
</div>

<?php
}

function quizmaker_mycred_test_data_save_ranking( $data, $certificate_id ){

	if(isset($data['mycred'])){

		$data['mycred'] = absInt($data['mycred']);

	}else{

		$data['mycred'] = 0;
	}

	return $data;
}