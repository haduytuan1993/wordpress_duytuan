<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! $messages ){
	return;
}

?>
<ul class="quizmaker-error" role="alert">
	<?php foreach ( $messages as $message ) : ?>
		<li><?php echo wp_kses_post( $message ); ?></li>
	<?php endforeach; ?>
</ul>
