<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Test Search Widget.
 *
 * @author   AWSTheme
 * @category Widgets
 * @package  Quizmaker/Widgets
 * @version  1.0.0
 * @extends  QM_Widget
 */

class QM_Widget_Test_Filters extends QM_Widget {
	
	public function __construct() {
		$this->widget_cssclass    = 'quizmaker widget_test_filters';
		$this->widget_description = __( 'Display a test filters on your site.', 'quizmaker' );
		$this->widget_id          = 'quizmaker_test_filters';
		$this->widget_name        = __( 'Quizmaker Test Filters', 'quizmaker' );
		$this->settings           = array(
			'title'  => array(
				'type'  => 'text',
				'std'   => __( 'Tests', 'quizmaker' ),
				'label' => __( 'Title', 'quizmaker' )
			),
		);

		// add_action( 'wp_enqueue_scripts', array( $this, 'load_styles' ) );

		parent::__construct();
	}

	public function load_styles()
	{
		qm_load_styles();
	}
	
	function widget( $args, $instance ) {
		$this->widget_start( $args, $instance );

		$terms = get_terms( array(
			'taxonomy' => 'test_cat',
		    'hide_empty' => false,
		) );		
		
		$termsData = [];

		if( $terms ) {

			foreach($terms as $t) { $termsData[] = ['id' => $t->term_id, 'text' => $t->name]; }
		}

		?>

			<div class="widget_test_filter_container" id="widget_test_filters_<?php echo time(); ?>" data-ajax_url="<?php echo site_url() . '?qm-ajax=' ?>" data-ajax_security="<?php echo wp_create_nonce( "quizmaker_test_resource" ); ?>">

				<div class="test-filters">

					<multiselect
				      v-model="categoryData" 
				      id="qmtl-findtests" 
				      label="text" 
				      track-by="id" 
				      placeholder="<?php esc_html_e('Type to search', 'quizmaker') ?>" 
				      open-direction="bottom" 
				      :options="<?php echo htmlspecialchars(json_encode($termsData)); ?>" 
				      :multiple="true" 
				      :searchable="false" 
				      :loading="isLoading" 
				      :internal-search="false" 
				      :clear-on-select="true" 
				      :close-on-select="false" 
				      :options-limit="300" 
				      :limit="100" 
				      :max-height="600" 
				      :show-no-results="false" 
				      :hide-selected="false" 
				      >
				    </multiselect>
				
					

						  	
					<div class="mt-3">
						<div class="row test-filters-results">
				  			<div v-for="(rs, index) in resultHTML" class="col-sm-6 col-lg-4 mb-3" :key="index" v-html="rs">
				  				

							</div>
						</div>
					</div>
				 	
				 	<div class="text-xs-center">
				 		
				 		<button class="qm-loadmore" :loading="isLoading" v-if="!isLoading && isLast == 0" @click.prevent="nextPage"><?php _e('Next', 'quizmaker'); ?></button>

				 		<div v-if="isLoading" class="qm-loading"></div>

				 	</div>
						 
					</div>
				
			</div>

		<?php

		$this->widget_end( $args );
	}
}
