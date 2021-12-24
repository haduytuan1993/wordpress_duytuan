<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class QM_Table_Result extends QM_Table
{
	protected	$_table	=	'results';
	protected	$_key	=	'id';
	
	protected $fields	=	array(
		'id'			=>	'd',
		'test_id'		=>	'd',
		'user_id'		=>	'd',
		'user_name'		=>	's',
		'user_email'	=>	's',
		'user_ip'		=>	's',
		'user_meta'		=>	's',
		'score'			=>	'd',
		'percent'		=>	'd',
		'total_score'	=>	'd',
		'answers'		=>	's',
		'duration'		=>	's',
		'date_start'	=>	's',
		'date_end'		=>	's',
		'others'		=>	's'
	);

}