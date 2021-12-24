<?php

if ( ! defined( 'ABSPATH' ) ) { exit; }

class QM_Meta_Box_Member_Data {
	
	public static function output( $post ) {
		global $post, $thepostid;
		
		$member_data 	=	get_post_meta($post->ID, '_member_data', true);
		
		$user_id		=	qm_get_post_meta( $post->ID, 'user_id' );
		$membership		=	0;
		$tests			=	array();
		
		if( $user_id )
		{			
			$user		=	new WP_User($user_id);
			
			$user_meta	=	qm_get_user_meta( $user_id );
			
			$membership	=	$user_meta['membership'];
			$is_payment	=	$user_meta['is_payment'];
			$date_paid	=	$user_meta['date_paid'];
			
			if(is_array($user_meta) && isset($user_meta['tests']) && is_array($user_meta['tests']) && $user_meta['tests']) {
				
				if( $user_meta['tests'] ) {
					
					$tests	=	qm_get_tests( $user_meta['tests'] );
				}
				
			}
		}
?>
	<div class="member-options">
		
		<div class="groups-field">
			<div class="group-field">
				<label><?php _e('Memberships', 'quizmaker'); ?></label>
				<?php qm_dropbox_posts(array( 
					'post_type' =>	'membership', 
					'name' 		=>	'qm_member_data[membership]',
					'selected'	=>	$membership
				)); ?>
			</div>
			<div class="group-field">
				<label><?php _e('Payment', 'quizmaker'); ?></label>
				<div class="group-input">
					<select name="qm_member_data[is_payment]">
						<option value="no" <?php selected($is_payment, 'no'); ?>>No</option>
						<option value="yes" <?php selected($is_payment, 'yes'); ?>>Yes</option>
						<option value="expired" <?php selected($is_payment, 'expired'); ?>>Expired</option>
					</select>
				</div>
			</div>
			
			<?php if($date_paid): ?>
			<div class="group-field">
				<label><?php _e('Date Paid', 'quizmaker'); ?></label>
				<div class="group-input">
					<?php echo $date_paid; ?>
				</div>
			</div>
			<?php endif; ?>
			
		</div>
		<div class="groups-field">
			<div class="group-field">
				<label><?php _e('User', 'quizmaker'); ?></label>
				<div class="group-input">
					<a href="<?php echo admin_url('user-edit.php?user_id=' . $user_id); ?>"><?php echo $user->nickname . ' - ' . $user->user_email; ?></a>
				</div>
			</div>
			<div class="group-field">
				<label><?php _e('Date Registered', 'quizmaker'); ?></label>
				<div class="group-input">
					<?php echo $user->user_registered; ?>
				</div>
			</div>
		</div>
		<?php if($tests): ?>
		<div class="groups-field">
			<div class="group-field">
				<label><?php _e('Assigned Tests', 'quizmaker'); ?></label>
				<div class="group-input">
					<div class="qm-list-tabs">
						
						<?php foreach($tests as $test): ?>
						<a class="item" href="<?php echo admin_url( 'post.php?post=' . $test->ID . '&action=edit' ); ?>">
							<?php echo $test->post_title; ?></a>
						<?php endforeach; ?>
						
					</div>
				</div>
			</div>
		</div>
		<?php endif; ?>
	</div>
<?php 
	}
	
	public static function save( $post_id, $post ) {
		
		if(!isset($_POST['qm_member_data'])) return false;
		
		$member_data	=	wp_parse_args( get_post_meta( $post_id, '_member_data', true ), array() );
		$user_id		=	absInt($member_data['user_id']);
		
		if(isset($_POST['qm_member_data']['membership']) && is_numeric($_POST['qm_member_data']['membership'])){
			$member_data['membership']	=	absInt($_POST['qm_member_data']['membership']);
			
			qm_update_user_meta( $user_id, array( 'membership' => $member_data['membership'] ) );
			
			qm_add_member_to_membership( $user_id, $member_data['membership'] );
		}
		
		if(isset($_POST['qm_member_data']['is_payment']) && in_array( $_POST['qm_member_data']['is_payment'], array('yes', 'no', 'expired'))){
			
			qm_update_user_meta( $user_id, array( 'is_payment' => $_POST['qm_member_data']['is_payment'] ) );
		}
		
		update_post_meta( $post_id, '_member_data', $member_data );
		
		
	}
}