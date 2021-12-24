<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'QM_Admin_Post_Types' ) ) :

class QM_Admin_Post_Types {
	public function __construct() {
		
		add_filter( 'views_edit-question', array( $this, 'question_views' ) );

		add_filter( 'manage_test_posts_columns', array( $this, 'test_columns' ) );
		add_filter( 'manage_question_posts_columns', array( $this, 'question_columns' ) );
		add_filter( 'manage_membership_posts_columns', array( $this, 'membership_columns' ) );
		add_filter( 'manage_member_posts_columns', array( $this, 'member_columns' ) );

		add_filter( 'manage_qanalytics_posts_columns', array( $this, 'qanalytics_columns' ) );
		
		add_action( 'manage_test_posts_custom_column', array( $this, 'render_test_columns' ), 2 );
		add_action( 'manage_question_posts_custom_column', array( $this, 'render_question_columns' ), 2 );
		add_action( 'manage_membership_posts_custom_column', array( $this, 'render_membership_columns' ), 2 );
		add_action( 'manage_member_posts_custom_column', array( $this, 'render_member_columns' ), 2 );

		add_action( 'manage_qanalytics_posts_custom_column', array( $this, 'render_qanalytics_columns' ), 2 );

		add_filter('post_row_actions', array( $this, 'remove_bulk_actions' ), 10, 1 );
		
		include_once( 'class-qm-admin-meta-boxes.php' );
	}

	public function remove_bulk_actions( $actions ) {

		if( get_post_type() == 'qanalytics' ){

			unset( $actions['edit'] );
			unset( $actions['trash'] );
			unset( $actions['view'] );
			unset( $actions['inline hide-if-no-js'] );
		}

        return $actions;
	}

	public function question_views( $views ) {
		global $wp_query;

		// Products do not have authors.
		unset( $views['mine'] );

		// Add sorting link.
		if ( current_user_can( 'edit_others_pages' ) ) {
			$class            = ( isset( $wp_query->query['orderby'] ) && 'menu_order title' === $wp_query->query['orderby'] ) ? 'current' : '';
			$query_string     = remove_query_arg( array( 'orderby', 'order' ) );
			$query_string     = add_query_arg( 'orderby', urlencode( 'menu_order title' ), $query_string );
			$query_string     = add_query_arg( 'order', urlencode( 'ASC' ), $query_string );
			$views['byorder'] = '<a href="' . esc_url( $query_string ) . '" class="' . esc_attr( $class ) . '">' . __( 'Sorting', 'quizmaker' ) . '</a>';
		}

		return $views;
	}
	
	public function test_columns( $existing_columns ) {
		if ( empty( $existing_columns ) && ! is_array( $existing_columns ) ) {
			$existing_columns = array();
		}
		
		$columns          		= array();
		$columns['cb']    		= '<input type="checkbox" />';
		$columns['title']		= __( 'Title', 'quizmaker' );
		$columns['duration']	= __( 'Duration', 'quizmaker' );
		$columns['attempt']		= __( 'Attempt', 'quizmaker' );
		$columns['publish_for'] = __( 'Publish For', 'quizmaker' );
		$columns['test_cat'] 	= __( 'Categories', 'quizmaker' );
		
		return array_merge( $columns, $existing_columns );
	}
	
	public function question_columns( $existing_columns ) {
		if ( empty( $existing_columns ) && ! is_array( $existing_columns ) ) {
			$existing_columns = array();
		}
		
		$columns          			= array();
		$columns['cb']    			= '<input type="checkbox" />';
		$columns['title']			= __( 'Title', 'quizmaker' );
		$columns['score'] 			= __( 'Score', 'quizmaker' );
		$columns['answer_type'] 	= __( 'Answer Type', 'quizmaker' );
		$columns['question_cat'] 	= __( 'Categories', 'quizmaker' );
		
		return array_merge( $columns, $existing_columns );
	}
	
	public function membership_columns( $existing_columns ) {
		if ( empty( $existing_columns ) && ! is_array( $existing_columns ) ) {
			$existing_columns = array();
		}
		
		$columns          					= array();
		$columns['cb']    					= '<input type="checkbox" />';
		$columns['title']					= __( 'Title', 'quizmaker' );
		$columns['price'] 					= __( 'Price', 'quizmaker' );
		$columns['members'] 				= __( 'Members', 'quizmaker' );
		$columns['tests'] 					= __( 'Tests', 'quizmaker' );
		$columns['default_membership'] 		= __( 'Default', 'quizmaker' );
		
		return array_merge( $columns, $existing_columns );
	}
	
	public function member_columns( $existing_columns ) {
		if ( empty( $existing_columns ) && ! is_array( $existing_columns ) ) {
			$existing_columns = array();
		}
		
		$columns          		= array();
		$columns['cb']    		= '<input type="checkbox" />';
		$columns['title']		= __( 'Title', 'quizmaker' );
		$columns['membership'] 	= __( 'Membership', 'quizmaker' );
		$columns['payment'] 	= __( 'Payment', 'quizmaker' );
		$columns['expired'] 	= __( 'Expired', 'quizmaker' );
		
		return array_merge( $columns, $existing_columns );
	}

	public function qanalytics_columns( $existing_columns ) {

		if ( empty( $existing_columns ) && ! is_array( $existing_columns ) ) {
			$existing_columns = array();
		}
		
		$columns          		= array();
		$columns['cb']    		= '<input type="checkbox" />';
		$columns['title']		= __( 'Title', 'quizmaker' );
		$columns['track_view']	= __( 'View', 'quizmaker' );
		$columns['track_player']		= __( 'Player', 'quizmaker' );
		$columns['track_total_share'] = __( 'Total Share', 'quizmaker' );
		$columns['track_rating'] = __( 'Rating', 'quizmaker' );
		
		return array_merge( $columns, $existing_columns );
	}
	
	public function render_test_columns( $column ) {
		global $post, $the_test;
		
		if ( empty( $the_test ) || $the_test->id != $post->ID ) {
			$the_test = qm_get_test( $post );
		}
		
		$settings	=	$the_test->get_settings();
		
		switch ( $column ) {
			case 'test_cat' :
			case 'test_tag' :
				if ( ! $terms = get_the_terms( $post->ID, $column ) ) {
					echo '<span class="na">&ndash;</span>';
				} else {
					$termlist = array();
					foreach ( $terms as $term ) {
						$termlist[] = '<a href="' . admin_url( 'edit.php?' . $column . '=' . $term->slug . '&post_type=test' ) . ' ">' . $term->name . '</a>';
					}

					echo implode( ', ', $termlist );
				}
				break;
			case 'duration' :
			
				echo $the_test->get_duration();
			
				break;
			case 'attempt' :
		
				echo $the_test->get_attempt();
		
				break;
			case 'publish_for' :
				
				echo qm_test_get_publish_for($settings['publish_for']);
				
				break;
			default:
				break;
		}
	}
	
	public function render_question_columns( $column ) {
		global $post, $the_question;
		
		if ( empty( $the_question ) || $the_question->id != $post->ID ) {
			$the_question = new QM_Question( $post );
		}
		
		switch ( $column ) {
			case 'question_cat' :
			case 'question_tag' :
				if ( ! $terms = get_the_terms( $post->ID, $column ) ) {
					echo '<span class="na">&ndash;</span>';
				} else {
					$termlist = array();
					foreach ( $terms as $term ) {
						$termlist[] = '<a href="' . admin_url( 'edit.php?' . $column . '=' . $term->slug . '&post_type=question' ) . ' ">' . $term->name . '</a>';
					}

					echo implode( ', ', $termlist );
				}
				break;
			case 'score' :
	
				echo $the_question->get_score();
	
				break;
			case 'answer_type' :

				echo $the_question->get_answer_type();

				break;
			default:
				break;
		}
	}
	
	public function render_membership_columns( $column ) {
		global $post, $the_membership;
		
		if ( empty( $the_membership ) || $the_membership->id != $post->ID ) {
			$the_membership = new QM_Membership( $post );
		}
		
		$meta_data	=	$the_membership->membership_data;
		
		switch ( $column ) {
			case 'price' :
	
				echo '$' . qm_get_post_meta( $the_membership->id, 'price' );
	
				break;
			case 'default_membership' :
				
				$is_default	=	'<span class="ion-ios-star-outline"></span>';;
				
				if( $the_membership->is_default() ){
					
					$is_default	=	'<span class="ion-ios-star"></span>';
					
				}
				
				echo sprintf('<a href="%s" class="qm_is_default">%s</a>', wp_nonce_url(admin_url('admin-ajax.php?action=quizmaker_default_membership&membership_id=' . $the_membership->id), 'quizmaker_default_membership'), $is_default);
				break;
			case 'members' :
				
				echo count( $the_membership->get_members() );
				
				break;
			case 'tests' :
			
				echo count( $the_membership->get_tests() );
			
				break;
			default:
				break;
		}
	}
	
	public function render_member_columns( $column ) {
		global $post, $the_member;
		
		if ( empty( $the_member ) || $the_member->id != $post->ID ) {
			$the_member = new QM_Member( $post );
		}
		
		switch ( $column ) {
			case 'membership' :
	
				echo $the_member->get_admin_membership_link();
	
				break;
			case 'payment' :

				echo $the_member->get_payment();

				break;
			case 'expired' :

				echo $the_member->get_expired();

				break;
			default:
				break;
		}
	}

	public function render_qanalytics_columns( $column ) {
		global $post;
		
		switch ( $column ) {
			
			case 'track_view' :

				echo get_post_meta( $post->ID, '_view', true );
				
				break;
			case 'track_player' :
			
				echo get_post_meta( $post->ID, '_player', true );
			
				break;
			case 'track_total_share' :
		
				echo get_post_meta( $post->ID, '_total_share', true );

				break;
			case 'track_rating' :
				
				$quiz = get_post_meta( $post->ID, '_quiz', true );

				$rating = get_post_meta( $quiz, '_rating', true );

				$stars = isset($rating['value']) ? absint($rating['value']) : 0;

				$total = isset($rating['total']) ? absint($rating['total']) : 0;

				echo '<star-rating :value="' . $stars . '" :total="' . $total . '"></star-rating>';

				break;
			default:
				break;
		}
	}
}

endif;

new QM_Admin_Post_Types();