<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * QM_Post_Type_QAnalytics Class.
 */
class QM_Post_Type_QAnalytics {
	
	public static function init() {
		add_action( 'init', array( __CLASS__, 'register_post_types' ), 6 );
	}
	
	public static function register_post_types() {
		if ( post_type_exists('qanalytics') ) {
			return;
		}
		
		do_action( 'quizmaker_register_post_type' );
		
		register_post_type( 'qanalytics',
			apply_filters( 'quizmaker_register_post_type_qanalytics',
				array(
					'labels'              => array(
							'name'                  => __( 'Analytics', 'quizmaker' ),
							'singular_name'         => __( 'Analytics', 'quizmaker' ),
							'menu_name'             => _x( 'Quiz Analytics', 'Admin menu name', 'quizmaker' )
						),
					'public'              => false,
					'show_ui'             => true,
					'capability_type'     => 'qanalytics',
					'capabilities' => array(
					    'create_posts' => false
					  ),
					'map_meta_cap'        => true,
					'publicly_queryable'  => true,
					'exclude_from_search' => false,
					'hierarchical'        => false,
					'rewrite'             => array( 'slug' => untrailingslashit( 'qanalytics' ), 'with_front' => false, 'feeds' => true ),
					'query_var'           => true,
					'supports'            => array( 'title' ),
					'show_in_nav_menus'   => true
				)
			)
		);
	}
	
	public static function rest_api_allowed_post_types( $post_types ) {
		$post_types[] = 'qanalytics';

		return $post_types;
	}
}

QM_Post_Type_QAnalytics::init();
