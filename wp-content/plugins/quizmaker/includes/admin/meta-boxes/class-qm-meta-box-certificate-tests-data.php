<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


class QM_Meta_Box_Certificate_Tests_Data {
	
	public static function output( $post ) {
		
		
		
?>
		<div class="certificate-tests-data-panel">
			
			<div class="aws-ui-table-data qm-list-certificate-tests" 
			data-ajax-url="<?php echo admin_url('admin-ajax.php'); ?>" 
		data-ajax-action="load_html_certificate_tests" 
		data-ajax-security="<?php echo wp_create_nonce( "quizmaker_load_html_certificate_tests" ); ?>" 
		data-ajax-params="<?php echo htmlspecialchars(json_encode( array('id' => $post->ID ), JSON_FORCE_OBJECT )); ?>" data-ajax-page="1">
					
					<table cellpadding="0" cellspacing="0" class="qm-table-meta-box">

						<thead>
							<tr>
								<th class="qm-a-c qm-s-2 checkall"><input type="checkbox" class="aws-ui-table-data-checkall"/></th>
								<th class="qm-a-l name"><?php _e('Name', 'quizmaker'); ?></th>
								<th class="qm-a-l pass"><?php _e('Pass', 'quizmaker'); ?></th>
							</tr>
						</thead>

						<tbody></tbody>
						
					</table>
				
			</div>

			<div class="clear" style="padding: 10px 10px;">
					<select class="qm-select2" id="input-search-tests" style="width: 100%;" data-action="quizmaker_json_search_tests" data-security="<?php echo wp_create_nonce( "search_tests_nonce" ); ?>" data-others="<?php echo htmlspecialchars(json_encode(array('publish_for' => 0)), ENT_QUOTES, 'UTF-8'); ?>" multiple="multiple" data-placeholder="<?php _e('Search Tests', 'quizmaker'); ?>" data-limit="10"></select>
			</div>

			
			<div class="clear actions assign-users-action">
				<div class="qm-pull-left">
					<button class="button button-primary" id="add-test-to-certificate"><?php _e('Add', 'quizmaker'); ?></button>
					<span class="spinner spinner-assign-users"></span>
				</div>
				<div class="qm-pull-right">
					<button class="button" id="remove-test-from-certificate"><?php _e('Remove', 'quizmaker'); ?></button>
				</div>
				<span class="spinner spinner-remove-assign-users"></span>
			</div>

		</div>
<?php 
	}
	
	public static function save( $post_id, $post ) {
			
			
	}
}