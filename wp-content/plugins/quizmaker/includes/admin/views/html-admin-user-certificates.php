
<div class="wrap">

	<h1 class="wp-heading-inline mb-1">User Certificates</h1>

	<hr class="wp-header-end">

	<form class="posts-filter" method="get">

		<div class="search-box-cert-id">
			<label class="screen-reader-text" for="post-search-input-cert-id">Search:</label>
			<input type="search" id="post-search-input-cert-id" name="s" value="<?php echo $search; ?>" placeholder="Certificate ID">
			<input type="submit" id="search-submit" class="button" value="Search">
		</div>

		<table class="wp-list-table widefat fixed striped posts">
			<thead>
				<tr>
					
					<th scope="col" id="cbs" class="manage-column column-cb check-column"></th>

					<th scope="col" id="id" class="manage-column column-title column-primary">ID</th>

					<th scope="col" id="user" class="manage-column column-title">Users</th>

					<th scope="col" id="tests" class="manage-column column-title">Tests</th>

					<th scope="col" id="action" class="manage-column column-action"></th>

				</tr>
			</thead>

			
			<tbody id="the-list">
				<?php if( $results ): ?>
				<?php foreach($results as $index => $result): 

					$user_link  = __('Guest', 'quizmaker');

					if( $result['user_id'] ) {

						$user_link  = '<a href="' . admin_url('user-edit.php?user_id=' . $result['user_id']) . '">' . $result['user_nicename'] . '</a>';
					}

				?>
				<tr class="iedit author-self level-0 status-publish hentry">

					<td><?php echo $index+1; ?></td>
					<td><a href="<?php echo qm_get_result_url($result['id']) ?>" target="blank"><?php echo $result['cert_id']; ?></a></td>
					<td><?php echo $user_link; ?></td>
					<td><a href="<?php echo admin_url('post.php?post=' . $result['test_id'] . '&action=edit'); ?>"><?php echo $result['test_name']; ?></a></td>
					<td><a href="<?php echo qm_view_certificate_test_url($result['test_id'], $result['id']); ?>"><?php _e('Download Certificate', 'quizmaker'); ?></a></td>
					
				</tr>
				<?php endforeach; ?>

				<?php else: ?>

				<tr>
					<td colspan="5" align="center"><?php _e('Find Certificates', 'quizmaker'); ?></td>
				</tr>

				<?php endif; ?>
			</tbody>
			

		</table>

		<input type="hidden" name="page" value="qm-user-certificates">
	</form>

</div>