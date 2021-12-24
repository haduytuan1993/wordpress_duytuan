<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

function qm_message_count( $message_type = '' ) {
	if ( ! did_action( 'quizmaker_init' ) ) {
		return;
	}

	$message_count = 0;
	$all_messages  = QM()->session->get( 'qm_messages', array() );
	
	if ( isset( $all_messages[$message_type] ) ) {

		$message_count = absint( sizeof( $all_messages[$message_type] ) );

	} elseif ( empty( $message_type ) ) {

		foreach ( $all_messages as $messages ) {
			$message_count += absint( sizeof( $all_messages ) );
		}

	}

	return $message_count;
}

function qm_has_message( $message, $message_type = 'success' ) {
	if ( ! did_action( 'quizmaker_init' ) ) {
		return false;
	}

	$messages = QM()->session->get( 'qm_messages', array() );
	$messages = isset( $messages[ $message_type ] ) ? $messages[ $message_type ] : array();
	return array_search( $message, $messages ) !== false;
}

function qm_add_message( $message, $message_type = 'success' ) {
	if ( ! did_action( 'quizmaker_init' ) ) {
		return;
	}

	$messages = QM()->session->get( 'qm_messages', array() );

	$messages[$message_type][] = apply_filters( 'quizmaker_add_' . $message_type, $message );
	
	QM()->session->set( 'qm_messages', $messages );
}

function qm_clear_messages() {
	if ( ! did_action( 'quizmaker_init' ) ) {
		return;
	}
	
	QM()->session->set( 'qm_messages', null );
}

function qm_print_messages() {
	if ( ! did_action( 'quizmaker_init' ) ) {
		return;
	}

	$all_messages  = QM()->session->get( 'qm_messages', array() );
	$message_types = apply_filters( 'quizmaker_message_types', array( 'error', 'success', 'notice' ) );
	
	foreach ( $message_types as $message_type ) {
		if ( qm_message_count( $message_type ) > 0 ) {
			qm_get_template( "messages/{$message_type}.php", array(
				'messages' => $all_messages[$message_type]
			) );
		}
	}

	qm_clear_messages();
}
