<?php
/**
 * Certificate Post Type
 *
 * Registers post types.
 *
 * @class     QM_Post_Type_Certificate
 * @version   1.1.0
 * @package   QuizMaker/Classes/QM_Post_Type_Certificate
 * @category  Class
 * @author    QuizMaker
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * QM_Post_Type_Certificate Class.
 */
class QM_Post_Type_Certificate {
	
	public static function init() {
		add_action( 'init', array( __CLASS__, 'register_post_types' ), 6 );
		add_action( 'init', array( __CLASS__, 'support_jetpack_omnisearch' ) );
		add_filter( 'rest_api_allowed_post_types', array( __CLASS__, 'rest_api_allowed_post_types' ) );
	}
	
	public static function register_post_types() {
		if ( post_type_exists('certificate') ) {
			return;
		}
		
		do_action( 'quizmaker_register_post_type' );
		
		register_post_type( 'certificate',
			apply_filters( 'quizmaker_register_post_type_certificate',
				array(
					'labels'              => array(
							'name'                  => __( 'Certificates', 'quizmaker' ),
							'singular_name'         => __( 'Certificate', 'quizmaker' ),
							'menu_name'             => _x( 'Certificates', 'Admin menu name', 'quizmaker' ),
							'add_new'               => __( 'Add Certificate', 'quizmaker' ),
							'add_new_item'          => __( 'Add New Certificate', 'quizmaker' ),
							'edit'                  => __( 'Edit', 'quizmaker' ),
							'edit_item'             => __( 'Edit Certificate', 'quizmaker' ),
							'new_item'              => __( 'New Certificate', 'quizmaker' ),
							'view'                  => __( 'View Certificate', 'quizmaker' ),
							'view_item'             => __( 'View Certificate', 'quizmaker' ),
							'search_items'          => __( 'Search Certificates', 'quizmaker' ),
							'not_found'             => __( 'No Certificates found', 'quizmaker' ),
							'not_found_in_trash'    => __( 'No Certificates found in trash', 'quizmaker' ),
							'parent'                => __( 'Parent Certificate', 'quizmaker' ),
							'featured_image'        => __( 'Certificate Image', 'quizmaker' ),
							'set_featured_image'    => __( 'Set Thumbnail', 'quizmaker' ),
							'remove_featured_image' => __( 'Remove Thumbnail', 'quizmaker' ),
							'use_featured_image'    => __( 'Use as Thumbnail', 'quizmaker' ),
						),
					'description'         => __( 'This is where you can add new certificate.', 'quizmaker' ),
					'public'              => true,
					'show_ui'             => true,
					'capability_type'     => 'certificate',
					'show_in_menu'        => current_user_can( 'manage_quizmaker' ) ? 'quizmaker' : true,
					'map_meta_cap'        => true,
					'publicly_queryable'  => true,
					'exclude_from_search' => false,
					'hierarchical'        => false,
					'rewrite'             => array( 'slug' => untrailingslashit( 'certificate' ), 'with_front' => false, 'feeds' => true ),
					'query_var'           => true,
					'supports'            => array( 'title', 'editor', 'excerpt', 'thumbnail', 'publicize' ),
					'has_archive'         => true,
					'show_in_nav_menus'   => true
				)
			)
		);
	}
		
	public static function support_jetpack_omnisearch() {
		if ( class_exists( 'Jetpack_Omnisearch_Posts' ) ) {
			new Jetpack_Omnisearch_Posts( 'certificate' );
		}
	}
	
	public static function rest_api_allowed_post_types( $post_types ) {
		$post_types[] = 'certificate';

		return $post_types;
	}
}

QM_Post_Type_Certificate::init();
