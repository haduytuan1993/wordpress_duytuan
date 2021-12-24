<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


class QM_Meta_Box_Result_Data {
	
	public static function output( $post ) {
		global $post;
?>
		
		<table cellpadding="0" cellspacing="0" class="widefat wp-list-table results-box" 
		data-ajax-url="<?php echo admin_url('admin-ajax.php'); ?>" 
		data-ajax-action="quizmaker_load_html_lastest_general_results" 
		data-ajax-security="<?php echo wp_create_nonce( "quizmaker_load_html_lastest_general_results" ); ?>" 
		data-ajax-params="<?php echo htmlspecialchars(json_encode( array('test_id' => $post->ID ), JSON_FORCE_OBJECT )); ?>">
			<thead>
				<tr>
					<th class="qm-a-c qm-s-3 percent"><?php _e('Percent', 'quizmaker'); ?></th>
					<th class="qm-a-c qm-s-1 score"><?php _e('Score', 'quizmaker'); ?></th>
					<!-- <th class="qm-a-c qm-s-1 attend_question"><?php _e('Attend', 'quizmaker'); ?></th> -->
					<!-- <th class="qm-a-c qm-s-4 duration"><?php _e('Duration', 'quizmaker'); ?></th> -->
					<!-- <th class="qm-a-c qm-s-4 ranking"><?php _e('Ranking', 'quizmaker'); ?></th> -->
					<th class="qm-a-l qm-s-5 date"><?php _e('Date', 'quizmaker'); ?></th>
					<th class="name"><?php _e('User', 'quizmaker'); ?></th>
					<th class="qm-a-c qm-s-1"></th>
					<th class="qm-a-c qm-s-1"></th>
				</tr>
			</thead>
			<tbody></tbody>
			<tfoot>
				<tr>
					<th colspan="9">
						<div class="pull-right">
							<div class="aws-ui-pagination">
								<div class="ion-ios-arrow-left page-prev "></div>
								<div class="pages"></div>
								<div class="ion-ios-arrow-right page-next"></div>
							</div>
						</div>
					</th>
				</tr>
			</tfoot>
		</table>
		<div class="clear results-action">
			<div class="qm-pull-left">
				
				<button class="button button-primary" type="submit" id="qm-export-csv-test-results" name="download_csv_test_result" value="<?php echo $post->ID; ?>"><?php _e('Export CSV', 'quizmaker'); ?></button>
								
			</div>
			<div class="qm-pull-right">
				<button class="button" id="qm-remove-test-results"><?php _e('Remove All', 'quizmaker'); ?></button>
			</div>
			<span class="spinner spinner-results-action"></span>
		</div>
<?php 
	}
	
	public static function save( $post_id, $post ) {
		
	}
}