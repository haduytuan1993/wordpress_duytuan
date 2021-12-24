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
 * QM_Post_Type_Quiz Class.
 */
class QM_Post_Type_Quiz {
	
	public static function init() {
		add_action( 'init', array( __CLASS__, 'register_taxonomies' ), 6 );
		add_action( 'init', array( __CLASS__, 'register_post_types' ), 6 );
		add_action( 'init', array( __CLASS__, 'support_jetpack_omnisearch' ) );
		add_filter( 'rest_api_allowed_post_types', array( __CLASS__, 'rest_api_allowed_post_types' ) );
	}
	
	public static function register_taxonomies() {
		if ( taxonomy_exists( 'quiz_type' ) ) {
			return;
		}

		do_action( 'quizmaker_register_taxonomy' );

		$permalinks = get_option( 'quizmaker_permalinks' );

		register_taxonomy( 'quiz_type',
			apply_filters( 'quizmaker_taxonomy_objects_quiz_type', array( 'quiz' ) ),
			apply_filters( 'quizmaker_taxonomy_args_quiz_type', array(
				'hierarchical'      => false,
				'show_ui'           => false,
				'show_in_nav_menus' => false,
				'query_var'         => is_admin(),
				'rewrite'           => false,
				'public'            => false
			) )
		);
		
		register_taxonomy( 'quiz_cat',
			apply_filters( 'quizmaker_taxonomy_objects_quiz_cat', array( 'quiz' ) ),
			apply_filters( 'quizmaker_taxonomy_args_quiz_cat', array(
				'hierarchical'          => true,
				'label'                 => __( 'Quiz Categories', 'quizmaker' ),
				'labels' => array(
						'name'              => __( 'Quiz Categories', 'quizmaker' ),
						'singular_name'     => __( 'Quiz Category', 'quizmaker' ),
						'menu_name'         => _x( 'Categories', 'Admin menu name', 'quizmaker' ),
						'search_items'      => __( 'Search Quiz Categories', 'quizmaker' ),
						'all_items'         => __( 'All Quiz Categories', 'quizmaker' ),
						'parent_item'       => __( 'Parent Quiz Category', 'quizmaker' ),
						'parent_item_colon' => __( 'Parent Quiz Category:', 'quizmaker' ),
						'edit_item'         => __( 'Edit Quiz Category', 'quizmaker' ),
						'update_item'       => __( 'Update Quiz Category', 'quizmaker' ),
						'add_new_item'      => __( 'Add New Quiz Category', 'quizmaker' ),
						'new_item_name'     => __( 'New Quiz Category Name', 'quizmaker' )
					),
				'show_ui'               => true,
				'query_var'             => true,
				'capabilities'          => array(
					'manage_terms' => 'manage_quiz_terms',
					'edit_terms'   => 'edit_quiz_terms',
					'delete_terms' => 'delete_quiz_terms',
					'assign_terms' => 'assign_quiz_terms'
				),
				'rewrite'               => array(
					'slug'         => empty( $permalinks['category_base'] ) ? _x( 'quiz-category', 'slug', 'quizmaker' ) : $permalinks['category_base'],
					'with_front'   => false,
					'hierarchical' => true,
				),
			) )
		);

		do_action( 'quizmaker_after_register_taxonomy' );
	}
	
	public static function register_post_types() {
		if ( post_type_exists('quiz') ) {
			return;
		}
		
		do_action( 'quizmaker_register_post_type' );
		
		register_post_type( 'quiz',
			apply_filters( 'quizmaker_register_post_type_quiz',
				array(
					'labels'              => array(
							'name'                  => __( 'Quiz', 'quizmaker' ),
							'singular_name'         => __( 'Quiz', 'quizmaker' ),
							'menu_name'             => _x( 'Quiz', 'Admin menu name', 'quizmaker' ),
							'add_new'               => __( 'Add Quiz', 'quizmaker' ),
							'add_new_item'          => __( 'Add New Quiz', 'quizmaker' ),
							'edit'                  => __( 'Edit', 'quizmaker' ),
							'edit_item'             => __( 'Edit Quiz', 'quizmaker' ),
							'new_item'              => __( 'New Quiz', 'quizmaker' ),
							'view'                  => __( 'View Quiz', 'quizmaker' ),
							'view_item'             => __( 'View Quiz', 'quizmaker' ),
							'search_items'          => __( 'Search Quiz', 'quizmaker' ),
							'not_found'             => __( 'No Quiz found', 'quizmaker' ),
							'not_found_in_trash'    => __( 'No Quiz found in trash', 'quizmaker' ),
							'parent'                => __( 'Parent Quiz', 'quizmaker' )
						),
					'description'         => __( 'This is where you can add new Quiz.', 'quizmaker' ),
					'menu_icon'			  => 'dashicons-quizmaker-tests',
					'public'              => true,
					'show_ui'             => true,
					'capability_type'     => 'quiz',
					'map_meta_cap'        => true,
					'publicly_queryable'  => true,
					'exclude_from_search' => false,
					'hierarchical'        => false,
					'rewrite'             => array( 'slug' => untrailingslashit( 'quiz' ), 'with_front' => false, 'feeds' => true ),
					'query_var'           => true,
					'supports'            => array( 'title', 'thumbnail', 'comments' ),
					'taxonomies'		  => array('quiz_cat'),
					'show_in_nav_menus'   => true
				)
			)
		);
	}
		
	public static function support_jetpack_omnisearch() {
		if ( class_exists( 'Jetpack_Omnisearch_Posts' ) ) {
			new Jetpack_Omnisearch_Posts( 'quiz' );
		}
	}
	
	public static function rest_api_allowed_post_types( $post_types ) {
		$post_types[] = 'quiz';

		return $post_types;
	}
}

QM_Post_Type_Quiz::init();
