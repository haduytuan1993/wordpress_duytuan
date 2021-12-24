<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

function qm_get_endpoint_url( $endpoint, $value = '', $permalink = '' ) {
	if ( ! $permalink )
		$permalink = get_permalink();

	// Map endpoint to options
	$endpoint = ! empty( QM()->query->query_vars[ $endpoint ] ) ? QM()->query->query_vars[ $endpoint ] : $endpoint;

	if ( get_option( 'permalink_structure' ) ) {
		if ( strstr( $permalink, '?' ) ) {
			$query_string = '?' . parse_url( $permalink, PHP_URL_QUERY );
			$permalink    = current( explode( '?', $permalink ) );
		} else {
			$query_string = '';
		}
		$url = trailingslashit( $permalink ) . $endpoint . '/' . $value . $query_string;
	} else {
		$url = add_query_arg( $endpoint, $value, $permalink );
	}
	
	return apply_filters( 'quizmaker_get_endpoint_url', $url, $endpoint, $value, $permalink );
}

function qm_get_page_id( $page ) {
	
	$page = apply_filters( 'quizmaker_get_' . $page . '_page_id', get_option('quizmaker_' . $page . '_page_id' ) );
	
	return $page ? absint( $page ) : -1;
}

function qm_get_page_permalink( $page ) {
	
	$page_id   = qm_get_page_id( $page );
	
	$permalink = $page_id ? get_permalink( $page_id ) : get_home_url();
	
	return apply_filters( 'quizmaker_get_' . $page . '_page_permalink', $permalink );
}


function qm_nav_menu_item_classes( $menu_items ) {

	if ( ! is_quizmaker() ) {
		return $menu_items;
	}

	$test_page 		= (int) qm_get_page_id('archive_test');
	$page_for_posts = (int) get_option( 'page_for_posts' );

	foreach ( (array) $menu_items as $key => $menu_item ) {

		$classes = (array) $menu_item->classes;

		// Unset active class for blog page
		if ( $page_for_posts == $menu_item->object_id ) {
			$menu_items[$key]->current = false;

			if ( in_array( 'current_page_parent', $classes ) ) {
				unset( $classes[ array_search('current_page_parent', $classes) ] );
			}

			if ( in_array( 'current-menu-item', $classes ) ) {
				unset( $classes[ array_search('current-menu-item', $classes) ] );
			}

		// Set active state if this is the shop page link
		} elseif ( is_quiz() && $test_page == $menu_item->object_id && 'page' === $menu_item->object ) {
			$menu_items[ $key ]->current = true;
			$classes[] = 'current-menu-item';
			$classes[] = 'current_page_item';

		// Set parent state if this is a product page
		} elseif ( is_singular( 'test' ) && $test_page == $menu_item->object_id ) {
			$classes[] = 'current_page_parent';
		}

		$menu_items[ $key ]->classes = array_unique( $classes );

	}

	return $menu_items;
}
add_filter( 'wp_nav_menu_objects', 'qm_nav_menu_item_classes', 2 );

function qm_list_pages( $pages ) {
    if (is_quizmaker()) {
        $pages = str_replace( 'current_page_parent', '', $pages); // remove current_page_parent class from any item
        $test_page = 'page-item-' . qm_get_page_id('archive_test'); // find shop_page_id through woocommerce options

        if (is_quiz()) :
        	$pages = str_replace($test_page, $test_page . ' current_page_item', $pages); // add current_page_item class to shop page
    	else :
    		$pages = str_replace($test_page, $test_page . ' current_page_parent', $pages); // add current_page_parent class to shop page
    	endif;
    }
    return $pages;
}
add_filter( 'wp_list_pages', 'qm_list_pages' );