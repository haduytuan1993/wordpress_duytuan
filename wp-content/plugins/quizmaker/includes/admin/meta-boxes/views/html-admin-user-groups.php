

<?php if(isset($users) && $users): ?>
<?php foreach($users as $user): ?>
	<tr data-id="<?php echo $user->ID; ?>">
		<td class="qm-a-c"><input type="checkbox" value="<?php echo $user->ID; ?>" name="select_assigned_users[]" class="select_row select_assigned_users"/></td>
		<td>
			<a href="<?php echo get_edit_user_link($user->ID); ?>">
		<?php echo esc_attr( $user->user_nicename . ' - ' . $user->user_email ); ?></a>
		</td>
	</tr>
<?php endforeach; ?>
<?php else: ?>
	<tr class="assigned-users_nodata"><td class="qm-a-c" colspan="2"><?php _e('No Data', 'quizmaker'); ?></td></tr>
<?php endif; ?>