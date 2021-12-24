<?php
/**
 * Question Post Type
 *
 * Registers post types and taxonomies.
 *
 * @class     QM_Post_Type_Question
 * @version   1.1.0
 * @package   QuizMaker/Classes/Questions
 * @category  Class
 * @author    QuizMaker
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WC_Post_types Class.
 */
class QM_Post_Type_Question {

	/**
	 * Hook in methods.
	 */
	public static function init() {
		add_action( 'init', array( __CLASS__, 'register_taxonomies' ), 6 );
		add_action( 'init', array( __CLASS__, 'register_post_types' ), 6 );
		add_action( 'init', array( __CLASS__, 'support_jetpack_omnisearch' ) );
		add_filter( 'rest_api_allowed_post_types', array( __CLASS__, 'rest_api_allowed_post_types' ) );
	}

	/**
	 * Register core taxonomies.
	 */
	public static function register_taxonomies() {
		if ( taxonomy_exists( 'question_type' ) ) {
			return;
		}

		do_action( 'quizmaker_register_taxonomy' );

		$permalinks = get_option( 'quizmaker_permalinks' );

		register_taxonomy( 'question_type',
			apply_filters( 'quizmaker_taxonomy_objects_question_type', array( 'question' ) ),
			apply_filters( 'quizmaker_taxonomy_args_question_type', array(
				'hierarchical'      => false,
				'show_ui'           => false,
				'show_in_nav_menus' => false,
				'query_var'         => is_admin(),
				'rewrite'           => false,
				'public'            => false
			) )
		);

		register_taxonomy( 'question_cat',
			apply_filters( 'quizmaker_taxonomy_objects_question_cat', array( 'question' ) ),
			apply_filters( 'quizmaker_taxonomy_args_question_cat', array(
				'hierarchical'          => true,
				'label'                 => __( 'Question Categories', 'quizmaker' ),
				'labels' => array(
						'name'              => __( 'Question Categories', 'quizmaker' ),
						'singular_name'     => __( 'Question Category', 'quizmaker' ),
						'menu_name'         => _x( 'Categories', 'Admin menu name', 'quizmaker' ),
						'search_items'      => __( 'Search Question Categories', 'quizmaker' ),
						'all_items'         => __( 'All Question Categories', 'quizmaker' ),
						'parent_item'       => __( 'Parent Question Category', 'quizmaker' ),
						'parent_item_colon' => __( 'Parent Question Category:', 'quizmaker' ),
						'edit_item'         => __( 'Edit Question Category', 'quizmaker' ),
						'update_item'       => __( 'Update Question Category', 'quizmaker' ),
						'add_new_item'      => __( 'Add New Question Category', 'quizmaker' ),
						'new_item_name'     => __( 'New Question Category Name', 'quizmaker' )
					),
				'show_ui'               => true,
				'query_var'             => true,
				'capabilities'          => array(
					'manage_terms' => 'manage_question_terms',
					'edit_terms'   => 'edit_question_terms',
					'delete_terms' => 'delete_question_terms',
					'assign_terms' => 'assign_question_terms',
				),
				'rewrite'               => array(
					'slug'         => empty( $permalinks['category_base'] ) ? _x( 'question-category', 'slug', 'quizmaker' ) : $permalinks['category_base'],
					'with_front'   => false,
					'hierarchical' => true,
				),
			) )
		);

		register_taxonomy( 'question_tag',
			apply_filters( 'quizmaker_taxonomy_objects_question_tag', array( 'question' ) ),
			apply_filters( 'quizmaker_taxonomy_args_question_tag', array(
				'hierarchical'          => false,
				'update_count_callback' => '_wc_term_recount',
				'label'                 => __( 'Question Tags', 'quizmaker' ),
				'labels'                => array(
						'name'                       => __( 'Question Tags', 'quizmaker' ),
						'singular_name'              => __( 'Question Tag', 'quizmaker' ),
						'menu_name'                  => _x( 'Tags', 'Admin menu name', 'quizmaker' ),
						'search_items'               => __( 'Search Question Tags', 'quizmaker' ),
						'all_items'                  => __( 'All Question Tags', 'quizmaker' ),
						'edit_item'                  => __( 'Edit Question Tag', 'quizmaker' ),
						'update_item'                => __( 'Update Question Tag', 'quizmaker' ),
						'add_new_item'               => __( 'Add New Question Tag', 'quizmaker' ),
						'new_item_name'              => __( 'New Question Tag Name', 'quizmaker' ),
						'popular_items'              => __( 'Popular Question Tags', 'quizmaker' ),
						'separate_items_with_commas' => __( 'Separate Question Tags with commas', 'quizmaker'  ),
						'add_or_remove_items'        => __( 'Add or remove Question Tags', 'quizmaker' ),
						'choose_from_most_used'      => __( 'Choose from the most used Question tags', 'quizmaker' ),
						'not_found'                  => __( 'No Question Tags found', 'quizmaker' ),
					),
				'show_ui'               => true,
				'query_var'             => true,
				'capabilities'          => array(
					'manage_terms' => 'manage_question_terms',
					'edit_terms'   => 'edit_question_terms',
					'delete_terms' => 'delete_question_terms',
					'assign_terms' => 'assign_question_terms',
				),
				'rewrite'               => array(
					'slug'       => empty( $permalinks['tag_base'] ) ? _x( 'question-tag', 'slug', 'quizmaker' ) : $permalinks['tag_base'],
					'with_front' => false
				),
			) )
		);

		do_action( 'quizmaker_after_register_taxonomy' );
	}

	/**
	 * Register core post types.
	 */
	public static function register_post_types() {
		if ( post_type_exists('question') ) {
			return;
		}
		
		do_action( 'quizmaker_register_post_type' );

		register_post_type( 'question',
			apply_filters( 'quizmaker_register_post_type_question',
				array(
					'labels'              => array(
							'name'                  => __( 'Questions', 'quizmaker' ),
							'singular_name'         => __( 'Question', 'quizmaker' ),
							'menu_name'             => _x( 'Questions', 'Admin menu name', 'quizmaker' ),
							'add_new'               => __( 'Add Question', 'quizmaker' ),
							'add_new_item'          => __( 'Add New Question', 'quizmaker' ),
							'edit'                  => __( 'Edit', 'quizmaker' ),
							'edit_item'             => __( 'Edit Question', 'quizmaker' ),
							'new_item'              => __( 'New Question', 'quizmaker' ),
							'view'                  => __( 'View Question', 'quizmaker' ),
							'view_item'             => __( 'View Question', 'quizmaker' ),
							'search_items'          => __( 'Search Questions', 'quizmaker' ),
							'not_found'             => __( 'No Questions found', 'quizmaker' ),
							'not_found_in_trash'    => __( 'No Questions found in trash', 'quizmaker' ),
							'parent'                => __( 'Parent Question', 'quizmaker' ),
							'featured_image'        => __( 'Question Image', 'quizmaker' ),
							'set_featured_image'    => __( 'Set question image', 'quizmaker' ),
							'remove_featured_image' => __( 'Remove question image', 'quizmaker' ),
							'use_featured_image'    => __( 'Use as question image', 'quizmaker' ),
						),
					'description'         => __( 'This is where you can add new questions.', 'quizmaker' ),
					'menu_icon'			  => 'dashicons-quizmaker-questions',
					'public'              => true,
					'show_ui'             => true,
					'capability_type'     => 'question',
					'map_meta_cap'        => true,
					'publicly_queryable'  => true,
					'exclude_from_search' => false,
					'hierarchical'        => false, // Hierarchical causes memory issues - WP loads all records!
					'rewrite'             => array( 'slug' => untrailingslashit( 'question' ), 'with_front' => false, 'feeds' => true ),
					'query_var'           => true,
					'supports'            => array( 'title', 'editor', 'thumbnail', 'comments'),
					'show_in_nav_menus'   => true
				)
			)
		);
	}
		
	/**
	 * Add Product Support to Jetpack Omnisearch.
	 */
	public static function support_jetpack_omnisearch() {
		if ( class_exists( 'Jetpack_Omnisearch_Posts' ) ) {
			new Jetpack_Omnisearch_Posts( 'question' );
		}
	}

	/**
	 * Added product for Jetpack related posts.
	 *
	 * @param  array $post_types
	 * @return array
	 */
	public static function rest_api_allowed_post_types( $post_types ) {
		$post_types[] = 'question';

		return $post_types;
	}
}

QM_Post_Type_Question::init();
