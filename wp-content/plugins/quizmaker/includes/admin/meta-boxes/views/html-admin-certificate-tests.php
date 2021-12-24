

<?php if(isset($cert_tests) && $cert_tests): ?>
<?php foreach($cert_tests as $test): ?>
	<tr data-id="<?php echo $test['id']; ?>">
		<td class="qm-a-c"><input type="checkbox" value="<?php echo $test['id']; ?>" name="select_cert_tests[]" class="select_row select_assigned_users"/></td>
		<td>
			<a href="<?php echo $test['link']; ?>">
		<?php echo esc_attr( $test['title'] ); ?></a>
		</td>
		<td>
			<input type="text" value="<?php echo $test['pass']; ?>" data-id="<?php echo $test['id']; ?>" class="pass"/>
		</td>
	</tr>
<?php endforeach; ?>
<?php else: ?>
	<tr class="cert-tests_nodata"><td class="qm-a-c" colspan="2"><?php _e('No Data', 'quizmaker'); ?></td></tr>
<?php endif; ?>