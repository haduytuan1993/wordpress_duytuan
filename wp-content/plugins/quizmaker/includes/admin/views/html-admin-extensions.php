<?php
/**
 * Admin View: Extensions
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

$section = 'premium';

if(isset($_GET['section']) && $_GET['section'] == 'free') {

	$section = 'free';
}

?>

<div class="wrap" id="qm-extensions-wrap">

	<h1><?php _e('Quizmaker - Extensions', 'quizmaker'); ?></h1>

	<div class="wp-filter">
		<ul class="filter-links">
			
			<li><a href="<?php echo admin_url('admin.php?page=qm-extensions&section=premium'); ?>" class="link <?php echo $section == 'premium' ? 'current' : ''; ?>"><?php _e('Premium', 'quizmaker'); ?></a></li>
			<li><a href="<?php echo admin_url('admin.php?page=qm-extensions&section=free'); ?>" class="link <?php echo $section == 'free' ? 'current' : ''; ?>"><?php _e('Free', 'quizmaker'); ?></a></li>
			
		</ul>
	</div>

	<br class="clear"/>

	<div class="wp-list-table widefat extensions-install">
		
	<div id="the-list">

		<?php if( $extensions ): ?>
		
		<?php foreach( $extensions as $extension ): ?>

			<?php if( $section == 'premium' && $extension['price'] > 0 ): ?>
		<div class="plugin-card plugin-card-theme-check">
			
			<div class="plugin-card-top">
				
				<div class="name column-name">
					<h3>
						<a href="<?php echo $extension['detail']; ?>" class="thickbox open-plugin-details-modal">
						<?php echo $extension['name']; ?>				
						<img src="<?php echo $extension['thumbnail']; ?>" class="plugin-icon" alt="">
						</a>
					</h3>
				</div>

				<div class="desc column-description">
					
					<?php echo $extension['description']; ?>

					<div class="amount"><span class="price"><?php echo _e('From', 'quizmaker'); ?> $<?php echo $extension['price']; ?></span></div>

				</div>

			</div>

			<div class="plugin-card-bottom">
				<?php if($extension['status'] == 1): ?>

				<a class="button" href="<?php echo admin_url('plugins.php'); ?>"><?php _e('Active Plugin', 'quizmaker'); ?></a>
				
				<?php elseif($extension['status'] == 2): ?>

				<a class="button" href="<?php echo admin_url('plugins.php'); ?>"><?php _e('Deactivate Plugin', 'quizmaker'); ?></a>

				<?php else: ?>

				<a class="button" href="<?php echo $extension['detail']; ?>" target="blank"><?php _e('Download Plugin', 'quizmaker'); ?></a>

				<?php endif; ?>
			</div>

		</div>
			<?php endif; ?>

			<?php if( $section == 'free' && $extension['price'] == 0 ): ?>
		<div class="plugin-card plugin-card-theme-check">
			
			<div class="plugin-card-top">
				
				<div class="name column-name">
					<h3>
						<a href="<?php echo $extension['detail']; ?>" class="thickbox open-plugin-details-modal">
						<?php echo $extension['name']; ?>				
						<img src="<?php echo $extension['thumbnail']; ?>" class="plugin-icon" alt="">
						</a>
					</h3>
				</div>

				<div class="desc column-description">
					
					<?php echo $extension['description']; ?>

				</div>

			</div>

			<div class="plugin-card-bottom">
				<?php if($extension['status'] == 1): ?>

				<a class="button" href="<?php echo admin_url('plugins.php'); ?>"><?php _e('Active Plugin', 'quizmaker'); ?></a>
				
				<?php elseif($extension['status'] == 2): ?>

				<a class="button" href="<?php echo admin_url('plugins.php'); ?>"><?php _e('Deactivate Plugin', 'quizmaker'); ?></a>

				<?php else: ?>

				<a class="button" href="<?php echo $extension['detail']; ?>" target="blank"><?php _e('Download Plugin', 'quizmaker'); ?></a>

				<?php endif; ?>
			</div>

		</div>
			<?php endif; ?>

		<?php endforeach; ?>

		<?php endif; ?>

	</div>

	</div>
</div>