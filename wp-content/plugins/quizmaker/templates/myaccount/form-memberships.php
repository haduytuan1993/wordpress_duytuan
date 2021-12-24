<?php 

if ( ! defined( 'ABSPATH' ) ) { exit; }

?>

<div class="container">
	
<?php qm_print_messages(); ?>

<?php if($step == 2): ?>
	
	<h1 class="qm-memberships-title"><?php _e('MEMBERSHIPS', 'quizmaker'); ?></h1>

	<div class="qm-memberships-steps">
		<div class="steps">
			<div class="step step-1 in-active">
				<span class="num">1</span>
				<a class="lbl" href="<?php echo qm_get_page_permalink('memberships'); ?>"><?php _e('Choose Package', 'quizmaker'); ?></a>
			</div>
			<div class="step step-2 active">
				<span class="num">2</span>
				<span class="lbl"><?php _e('Register', 'quizmaker'); ?></span>
			</div>
			<div class="step step-3">
				<span class="num">3</span>
				<span class="lbl"><?php _e('Checkout', 'quizmaker'); ?></span>
			</div>
		</div>
	</div>
	
	<form class="qm-memberships-register" action="" method="post">
		<?php do_action( 'quizmaker_register_form_start' ); ?>
		
		<h2 class="package-name"><?php echo $package->post_title; ?></h2>
		
		<div class="form-row">
			<label for="username"><?php _e( 'Username', 'quizmaker' ); ?> <span class="required">*</span></label>
			<input type="text" class="input-text" name="username" id="username" />
		</div>
		
		<div class="form-row">
			<label for="email"><?php _e( 'Email address', 'quizmaker' ); ?> <span class="required">*</span></label>
			<input type="email" class="input-text" name="email" id="email" />
		</div>
		
		<div class="form-row">
			<label for="password"><?php _e( 'Password', 'quizmaker' ); ?> <span class="required">*</span></label>
			<input type="password" class="input-text" name="password" id="password" />
		</div>
	
		<?php do_action( 'quizmaker_register_form' ); ?>

		<div class="form-row-action">
			<?php wp_nonce_field( 'qm_save_register_membership' ); ?>
			<input type="submit" class="button" name="qm_save_register_membership" value="<?php esc_attr_e( 'Proceed Checkout', 'quizmaker' ); ?>" />
			<input type="hidden" name="action" value="qm_save_register_membership" />
		</div>
		
		<input type="hidden" name="membership" value="<?php echo $package->ID; ?>"/>
		<?php do_action( 'quizmaker_register_form_end' ); ?>
	</form>
		
<?php elseif($step == 3): ?>
	
	<h1 class="qm-memberships-checkout-title"><?php _e('CHECKOUT SUCCESS!', 'quizmaker'); ?></h1>

	<div class="qm-memberships-steps">
		<div class="steps">
			<div class="step step-1 in-active">
				<span class="num">1</span>
				<span class="lbl"><?php _e('Choose Package', 'quizmaker'); ?></span>
			</div>
			<div class="step step-2 in-active">
				<span class="num">2</span>
				<span class="lbl"><?php _e('Register', 'quizmaker'); ?></span>
			</div>
			<div class="step step-3 active">
				<span class="num">3</span>
				<span class="lbl"><?php _e('Checkout', 'quizmaker'); ?></span>
			</div>
		</div>
	</div>
	
	<?php if($is_success): ?>
		
	<div class="qm-memberships-checkout">
		
		<a href="<?php echo qm_get_page_permalink('myaccount'); ?>" class="qm-memberships-checkout-login">LOGIN</a>
	</div>
	
	<?php else: ?>
		
		<h1>PAY FAIL</h1>
		
	<?php endif; ?>
	
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
			<span class="lbl"><?php _e('Register', 'quizmaker'); ?></span>
		</div>
		<div class="step step-3">
			<span class="num">3</span>
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
					<div class="price">
						<span class="dollar">$</span>
						<span class="figure"><?php echo $pack->price; ?></span>
						<span class="month">
							<span class="currency"></span>/month
						</span>
					</div>
					<ul class="package_features">
						<li>The flexbox mixins</li>
						<li>The flexbox mixins</li>
						<li>The flexbox mixins</li>
						<li>The flexbox mixins</li>
						<li>The flexbox mixins</li>
						<li>The flexbox mixins</li>
					</ul>
					<a class="qm-btn-package" href="<?php echo $pack->step_2_url; ?>">Select</a>
				</div>
			</div>
		<?php endforeach; ?>
	<?php endif; ?>
	
</div>
	
<?php endif; ?>

</div>