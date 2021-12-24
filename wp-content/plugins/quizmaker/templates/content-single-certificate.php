
<?php if ( ! defined( 'ABSPATH' ) ) { exit; } ?>

<?php do_action( 'quizmaker_left_sidebar' ); ?>


<div id="certificate-<?php the_ID(); ?>" <?php quizmaker_container_class('single-certificate'); ?>>
	<div class="qm-container-inner">

		<h1 class="title"><?php the_title(); ?></h1>

		<?php the_content(); ?>
		
		<div id="certificate-register-box">
			<form action="" method="POST" enctype="multipart/form-data">
				<div class="table-data">
					<div class="gd">
						<div class="gd-label">Tests</div>
						<div class="gd-value">4</div>
					</div>
				</div>
				<button type="submit" name="quizmaker_register_certificate" value="<?php the_ID(); ?>" class="qm-btn-single-start"><?php _e('REGISTER', 'quizmaker'); ?></button>
			</form>
		</div>

	</div>
</div>

 <?php do_action( 'quizmaker_right_sidebar' ); ?>