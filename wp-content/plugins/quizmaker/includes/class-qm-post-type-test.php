<?php
/**
 * Test Post Type
 *
 * Registers post types and taxonomies.
 *
 * @class     QM_Post_Type_Test
 * @version   1.1.0
 * @package   QuizMaker/Classes/Tests
 * @category  Class
 * @author    QuizMaker
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * QM_Post_Type_Test Class.
 */
class QM_Post_Type_Test {
	
	public static function init() {
		add_action( 'init', array( __CLASS__, 'register_taxonomies' ), 6 );
		add_action( 'init', array( __CLASS__, 'register_post_types' ), 6 );
		add_action( 'init', array( __CLASS__, 'support_jetpack_omnisearch' ) );
		add_filter( 'rest_api_allowed_post_types', array( __CLASS__, 'rest_api_allowed_post_types' ) );
	}
	
	public static function register_taxonomies() {
		if ( taxonomy_exists( 'test_type' ) ) {
			return;
		}

		do_action( 'quizmaker_register_taxonomy' );

		$permalinks = get_option( 'quizmaker_permalinks' );

		register_taxonomy( 'test_type',
			apply_filters( 'quizmaker_taxonomy_objects_test_type', array( 'test' ) ),
			apply_filters( 'quizmaker_taxonomy_args_test_type', array(
				'hierarchical'      => false,
				'show_ui'           => false,
				'show_in_nav_menus' => false,
				'query_var'         => is_admin(),
				'rewrite'           => false,
				'public'            => false
			) )
		);
		
		register_taxonomy( 'test_cat',
			apply_filters( 'quizmaker_taxonomy_objects_test_cat', array( 'test' ) ),
			apply_filters( 'quizmaker_taxonomy_args_test_cat', array(
				'hierarchical'          => true,
				'label'                 => __( 'Test Categories', 'quizmaker' ),
				'labels' => array(
						'name'              => __( 'Test Categories', 'quizmaker' ),
						'singular_name'     => __( 'Test Category', 'quizmaker' ),
						'menu_name'         => _x( 'Categories', 'Admin menu name', 'quizmaker' ),
						'search_items'      => __( 'Search Test Categories', 'quizmaker' ),
						'all_items'         => __( 'All Test Categories', 'quizmaker' ),
						'parent_item'       => __( 'Parent Test Category', 'quizmaker' ),
						'parent_item_colon' => __( 'Parent Test Category:', 'quizmaker' ),
						'edit_item'         => __( 'Edit Test Category', 'quizmaker' ),
						'update_item'       => __( 'Update Test Category', 'quizmaker' ),
						'add_new_item'      => __( 'Add New Test Category', 'quizmaker' ),
						'new_item_name'     => __( 'New Test Category Name', 'quizmaker' )
					),
				'show_ui'               => true,
				'query_var'             => true,
				'capabilities'          => array(
					'manage_terms' => 'manage_test_terms',
					'edit_terms'   => 'edit_test_terms',
					'delete_terms' => 'delete_test_terms',
					'assign_terms' => 'assign_test_terms'
				),
				'rewrite'               => array(
					'slug'         => empty( $permalinks['category_base'] ) ? _x( 'test-category', 'slug', 'quizmaker' ) : $permalinks['category_base'],
					'with_front'   => false,
					'hierarchical' => true,
				),
			) )
		);

		register_taxonomy( 'test_tag',
			apply_filters( 'quizmaker_taxonomy_objects_test_tag', array( 'test' ) ),
			apply_filters( 'quizmaker_taxonomy_args_test_tag', array(
				'hierarchical'          => false,
				'update_count_callback' => '_wc_term_recount',
				'label'                 => __( 'Test Tags', 'quizmaker' ),
				'labels'                => array(
						'name'                       => __( 'Test Tags', 'quizmaker' ),
						'singular_name'              => __( 'Test Tag', 'quizmaker' ),
						'menu_name'                  => _x( 'Tags', 'Admin menu name', 'quizmaker' ),
						'search_items'               => __( 'Search Test Tags', 'quizmaker' ),
						'all_items'                  => __( 'All Test Tags', 'quizmaker' ),
						'edit_item'                  => __( 'Edit Test Tag', 'quizmaker' ),
						'update_item'                => __( 'Update Test Tag', 'quizmaker' ),
						'add_new_item'               => __( 'Add New Test Tag', 'quizmaker' ),
						'new_item_name'              => __( 'New Test Tag Name', 'quizmaker' ),
						'popular_items'              => __( 'Popular Test Tags', 'quizmaker' ),
						'separate_items_with_commas' => __( 'Separate Test Tags with commas', 'quizmaker'  ),
						'add_or_remove_items'        => __( 'Add or remove Test Tags', 'quizmaker' ),
						'choose_from_most_used'      => __( 'Choose from the most used Test tags', 'quizmaker' ),
						'not_found'                  => __( 'No Test Tags found', 'quizmaker' ),
					),
				'show_ui'               => true,
				'query_var'             => true,
				'capabilities'          => array(
					'manage_terms' => 'manage_test_terms',
					'edit_terms'   => 'edit_test_terms',
					'delete_terms' => 'delete_test_terms',
					'assign_terms' => 'assign_test_terms',
				),
				'rewrite'               => array(
					'slug'       => empty( $permalinks['tag_base'] ) ? _x( 'test-tag', 'slug', 'quizmaker' ) : $permalinks['tag_base'],
					'with_front' => false
				),
			) )
		);

		do_action( 'quizmaker_after_register_taxonomy' );
	}
	
	public static function register_post_types() {
		if ( post_type_exists('test') ) {
			return;
		}
		
		do_action( 'quizmaker_register_post_type' );

		$archive_test_page_id	=	qm_get_page_id( 'archive_test' );
		
		register_post_type( 'test',
			apply_filters( 'quizmaker_register_post_type_test',
				array(
					'labels'              => array(
							'name'                  => __( 'Tests', 'quizmaker' ),
							'singular_name'         => __( 'Test', 'quizmaker' ),
							'menu_name'             => _x( 'Tests', 'Admin menu name', 'quizmaker' ),
							'add_new'               => __( 'Add Test', 'quizmaker' ),
							'add_new_item'          => __( 'Add New Test', 'quizmaker' ),
							'edit'                  => __( 'Edit', 'quizmaker' ),
							'edit_item'             => __( 'Edit Test', 'quizmaker' ),
							'new_item'              => __( 'New Test', 'quizmaker' ),
							'view'                  => __( 'View Test', 'quizmaker' ),
							'view_item'             => __( 'View Test', 'quizmaker' ),
							'search_items'          => __( 'Search Tests', 'quizmaker' ),
							'not_found'             => __( 'No Tests found', 'quizmaker' ),
							'not_found_in_trash'    => __( 'No Tests found in trash', 'quizmaker' ),
							'parent'                => __( 'Parent Test', 'quizmaker' ),
							'featured_image'        => __( 'Test Image', 'quizmaker' ),
							'set_featured_image'    => __( 'Set test image', 'quizmaker' ),
							'remove_featured_image' => __( 'Remove test image', 'quizmaker' ),
							'use_featured_image'    => __( 'Use as test image', 'quizmaker' ),
						),
					'description'         => __( 'This is where you can add new tests.', 'quizmaker' ),
					'menu_icon'			  => 'dashicons-quizmaker-tests',
					'public'              => true,
					'show_ui'             => true,
					'capability_type'     => 'test',
					'map_meta_cap'        => true,
					'publicly_queryable'  => true,
					'exclude_from_search' => false,
					'hierarchical'        => false,
					'rewrite'             => array( 'slug' => untrailingslashit( 'test' ), 'with_front' => false, 'feeds' => true ),
					'query_var'           => true,
					'supports'            => array( 'title', 'editor', 'excerpt', 'thumbnail', 'comments', 'page-attributes', 'publicize' ),
					'has_archive'         => ( $test_page_id = qm_get_page_id( 'archive_test' ) ) && get_post( $test_page_id ) ? get_page_uri( $test_page_id ) : 'quiz',
					'taxonomies'		  => array('test_cat'),
					'show_in_nav_menus'   => true
				)
			)
		);
	}
		
	public static function support_jetpack_omnisearch() {
		if ( class_exists( 'Jetpack_Omnisearch_Posts' ) ) {
			new Jetpack_Omnisearch_Posts( 'test' );
		}
	}
	
	public static function rest_api_allowed_post_types( $post_types ) {
		$post_types[] = 'test';

		return $post_types;
	}
}

QM_Post_Type_Test::init();
