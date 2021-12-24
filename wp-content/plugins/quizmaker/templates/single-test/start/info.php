<?php if ( ! defined( 'ABSPATH' ) ) { exit; } ?>

<?php foreach( $test_info as $info ): ?>
	<?php if( $info['type'] == 'text' ): ?>

		<div class="gd">
			<div class="gd-label"><?php echo $info['label']; ?></div>
			<div class="gd-value"><?php echo $info['value']; ?></div>
		</div>
	
	<?php elseif( $info['type'] == 'recaptcha' ): ?>
	
		<div class="gd center">
			<div class="g-recaptcha" data-SiteKey="<?php echo $info['value']; ?>"> </div>
		</div>
	
	<?php elseif( $info['type'] == 'rating' ): ?>
		
		<div class="gd">
			<div class="gd-label"><?php echo $info['label']; ?></div>
			<div class="gd-value">
				
				<?php echo aws_rating_html( $info['value']['star'], $info['value']['users'], true ); ?>
				
			</div>
		</div>
		
	<?php endif; ?>
<?php endforeach; ?>