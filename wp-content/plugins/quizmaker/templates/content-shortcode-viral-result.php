
<h3>Result</h3>

<div class="col-group">
	<div class="col-4">
		<div class="block-rs block-rs-percent">
			<span class="block-rs-icon ion-ios-speedometer"></span>
			<span class="block-rs-value"><?php echo $percent; ?>%</span>
			<span class="block-rs-label"><?php _e('Percent', 'quizmaker'); ?></span>
		</div>
	</div>
	
	<div class="col-4">
		<div class="block-rs block-rs-percent">
			<span class="block-rs-icon ion-checkmark-circled"></span>
			<span class="block-rs-value"><?php echo $num_corrects; ?></span>
			<span class="block-rs-label"><?php _e('Corrected Questions', 'quizmaker'); ?></span>
		</div>
	</div>

	<div class="col-4">
		<div class="block-rs block-rs-percent">
			<span class="block-rs-icon ion-android-bookmark"></span>
			<span class="block-rs-value"><?php echo $score . '/' . $total; ?></span>
			<span class="block-rs-label"><?php _e('Score', 'quizmaker'); ?></span>
		</div>
	</div>
</div>