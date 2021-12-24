<?php

if ( ! defined( 'ABSPATH' ) ) { exit; }

class QM_Template_Loader {
	public static function init() {
		add_filter( 'template_include', array( __CLASS__, 'template_loader' ) );
	}
	
	public static function template_loader( $template ) {
		$find = array( 'quizmaker.php' );
		$file = '';
		
		if ( is_single() && get_post_type() == 'test' ) {

			$file 	= 'single-test.php';
			$find[] = $file;
			$find[] = QM()->template_path() . $file;

		} elseif ( is_test_taxonomy() ) {
			
			$term   = get_queried_object();

			if ( is_tax( 'test_cat' ) || is_tax( 'test_tag' ) ) {
				$file = 'taxonomy-' . $term->taxonomy . '.php';
			} else {
				$file = 'archive-test.php';
			}

			$find[] = 'taxonomy-' . $term->taxonomy . '-' . $term->slug . '.php';
			$find[] = QM()->template_path() . 'taxonomy-' . $term->taxonomy . '-' . $term->slug . '.php';
			$find[] = 'taxonomy-' . $term->taxonomy . '.php';
			$find[] = QM()->template_path() . 'taxonomy-' . $term->taxonomy . '.php';
			$find[] = $file;
			$find[] = QM()->template_path() . $file;
			
		} elseif ( is_post_type_archive( 'test' ) || is_page( qm_get_page_id( 'quiz' ) ) ) {

			$file 	= 'archive-test.php';
			$find[] = $file;
			$find[] = QM()->template_path() . $file;

		} elseif ( is_single() && get_post_type() == 'certificate' ) {

			$file 	= 'single-certificate.php';
			$find[] = $file;
			$find[] = QM()->template_path() . $file;

		} elseif ( is_single() && get_post_type() == 'question' ) {

			$file 	= 'single-question.php';
			$find[] = $file;
			$find[] = QM()->template_path() . $file;
		}
		
		if ( $file ) {
			$template       = locate_template( array_unique( $find ) );
			if ( ! $template ) {
				$template = QM()->plugin_path() . '/templates/' . $file;
			}
		}
		
		return $template;
	}
}

QM_Template_Loader::init();