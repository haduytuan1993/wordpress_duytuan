<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * QM_Post_Type_Usergroup Class.
 */
class QM_Post_Type_Usergroup {
	
	public static function init() {
		add_action( 'init', array( __CLASS__, 'register_post_types' ), 6 );
	}
	
	public static function register_post_types() {
		if ( post_type_exists('usergroup') ) {
			return;
		}
		
		do_action( 'quizmaker_register_post_type' );
		
		register_post_type( 'usergroup',
			apply_filters( 'quizmaker_register_post_type_usergroup',
				array(
					'labels'              => array(
							'name'                  => __( 'User Groups', 'quizmaker' ),
							'singular_name'         => __( 'User Group', 'quizmaker' ),
							'menu_name'             => _x( 'User Groups', 'Admin menu name', 'quizmaker' ),
						),
					'public'              => false,
					'show_ui'             => true,
					'capability_type'     => 'certificate',
					'show_in_menu'        => current_user_can( 'manage_quizmaker' ) ? 'quizmaker' : true,
					'map_meta_cap'        => true,
					'publicly_queryable'  => true,
					'exclude_from_search' => false,
					'hierarchical'        => false,
					'query_var'           => true,
					'supports'            => array( 'title', 'publicize' ),
					'has_archive'         => true,
					'show_in_nav_menus'   => true
				)
			)
		);
	}
		
}

QM_Post_Type_Usergroup::init();
