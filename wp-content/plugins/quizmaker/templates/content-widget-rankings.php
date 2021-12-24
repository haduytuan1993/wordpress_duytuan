<?php 

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<li class="item">
	<span class="order"><?php echo $order; ?></span>
	<span class="user"><?php echo $data['user']; ?></span>
	
	<?php if($show_points): ?>
	<span class="score"><?php echo $data['score']; ?></span>
	<?php endif; ?>
</li>
