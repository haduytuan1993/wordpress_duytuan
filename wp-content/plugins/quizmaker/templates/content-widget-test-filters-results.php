		
<div class="qm-card">
  <a href="<?php echo get_permalink( $data->ID ); ?>">
  	<?php echo get_the_post_thumbnail( $data->ID, 'post-medium', array('class' => 'card-img-top', 'alt' => $data->post_title )) ?>
  </a>
 	<div class="card-body">

		<h5 class="card-title"><a href="<?php echo get_permalink( $data->ID ); ?>"><?php echo $data->post_title; ?></a></h5>

  </div>

  	<?php if($categories): ?>
 	<div class="card-footer">
  		
  		<ul class="list-inline">
 		<?php foreach ($categories as $cat): ?>

 			<li class="list-inline-item"><small><?php echo $cat->name; ?></small></li>
 			
 		<?php endforeach ?>
 		</ul>

   	</div>
   <?php endif; ?>

</div>


