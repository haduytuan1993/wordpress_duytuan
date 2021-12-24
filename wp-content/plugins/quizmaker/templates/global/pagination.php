<?php

if ( ! defined( 'ABSPATH' ) ) { exit; }
?>

<?php if(isset($pages) && is_array($pages) && count($pages) > 0): ?>
<div class="qm-pagination s1">
	<ul>
		<?php for($i = 1; $i <= $pages; $i++): ?>
		<li class="page <?php echo qm_active($i, $page); ?>"><a href="<?php echo $link . $i; ?>"><?php echo $i; ?></a></li>
		<?php endfor; ?>
	</ul>
</div>
<?php endif; ?>