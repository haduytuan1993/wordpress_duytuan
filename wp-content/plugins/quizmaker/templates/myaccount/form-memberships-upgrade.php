<?php 

if ( ! defined( 'ABSPATH' ) ) { exit; }

?>

<div class="container">
	
<?php qm_print_messages(); ?>



<?php if($step == 2): ?>

<div class="qm-inform-success">
	<h1 class="title"><?php _e('CHECKOUT SUCCESS!', 'quizmaker'); ?></h1>
	
	<div class="action">
		<a href="<?php echo $myaccount_link; ?>" class="qm-btn"><?php _e('My Account', 'quizmaker'); ?>
	</div>
</div>
	
<?php else: ?>
	
<h1 class="qm-memberships-title">MEMBERSHIPS</h1>

<div class="qm-memberships-steps">
	<div class="steps">
		<div class="step step-1 active">
			<span class="num">1</span>
			<a class="lbl" href="#"><?php _e('Choose Package', 'quizmaker'); ?></a>
		</div>
		<div class="step step-2">
			<span class="num">2</span>
			<span class="lbl"><?php _e('Checkout', 'quizmaker'); ?></span>
		</div>
	</div>
</div>
	
<div class="qm-memberships packages">
	
	<?php if($packages): ?>
		<?php foreach($packages as $pack): ?>
			<div class="package <?php echo $pack->post_name; ?>">
				<div class="inner">
					<h2 class="package_title"><?php echo $pack->post_title; ?></h2>
					<?php echo $pack->post_content; ?>
					<a class="qm-btn-package" href="<?php echo $pack->step_2_url; ?>"><?php _e('Select', 'quizmaker'); ?></a>
				</div>
			</div>
		<?php endforeach; ?>
	<?php endif; ?>
	
</div>
<?php endif; ?>

</div>