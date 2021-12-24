<?php 

if ( ! defined( 'ABSPATH' ) ) { exit; }

function qm_clean( $var ) {
	return is_array( $var ) ? array_map( 'qm_clean', $var ) : sanitize_text_field( $var );
}

function qm_sanitize_term_text_based( $term ) {
	return trim( wp_unslash( strip_tags( $term ) ) );
}

function qm_date_format() {
	return apply_filters( 'quizmaker_date_format', get_option( 'date_format' ) );
}

function qm_object_to_array( $object ) {
	if( !is_object($object) ){
		return $object;
	}

	return get_object_vars($object);
}

function qm_date_nextmonth( $date = 'now', $format = 'Y-m-d H:i:s', $timezone = '' ) {
	
	if( !$timezone ) {
		$timezone = qm_get_timezone();
	}
	
	if($date == 'now') {
		$date	=	mktime();
	}else{
		$date	=	strtotime($date);
	}
	
	$datetime	=	qm_get_date( $date, $format = 'Y-m-d H:i:s', $timezone );
		
	$datetime->modify('+1 month');
	
	return $datetime->format($format);
}



function qm_timezone_list() {
    static $timezones = null;

    if ($timezones === null) {
        $timezones = array();
        $offsets = array();
        $now = new DateTime('now', new DateTimeZone('UTC'));

        foreach (DateTimeZone::listIdentifiers() as $timezone) {
            $now->setTimezone(new DateTimeZone($timezone));
			
            $offset = $now->getOffset();
			
			array_push($offsets, $offset);
				
            $timezones[$timezone] = '(' . format_GMT_offset($offset) . ') ' . format_timezone_name($timezone);
        }

        array_multisort($offsets, $timezones);
    }

    return $timezones;
}

function qm_format_GMT_offset($offset) {
    $hours = intval($offset / 3600);
    $minutes = abs(intval($offset % 3600 / 60));
    return 'GMT' . ($offset ? sprintf('%+03d:%02d', $hours, $minutes) : '');
}

function qm_format_timezone_name($name) {
    $name = str_replace('/', ', ', $name);
    $name = str_replace('_', ' ', $name);
    $name = str_replace('St ', 'St. ', $name);
    return $name;
}

function qm_score( $score ) {

	if( $score > 1 ) {

		$score .= ' points';
	}else {

		$score .= ' point';
	}

	return $score;
}

function qm_timezone_dropdown_options( $selected_tz = '' ) {
	
	$timezone_list	=	qm_timezone_list();
	
	foreach( $timezone_list as $tz_key => $tz_value ){
		
		echo '<option value="' . esc_attr( $tz_key ) . '" ' . selected( $tz_key, $selected_tz, false ) . '>' . $tz_value . '</option>';
	}
}

function qm_timezone_string() {

	// if site timezone string exists, return it
	if ( $timezone = get_option( 'timezone_string' ) ) {
		return $timezone;
	}

	// get UTC offset, if it isn't set then return UTC
	if ( 0 === ( $utc_offset = get_option( 'gmt_offset', 0 ) ) ) {
		return 'UTC';
	}

	// adjust UTC offset from hours to seconds
	$utc_offset *= 3600;

	// attempt to guess the timezone string from the UTC offset
	$timezone = timezone_name_from_abbr( '', $utc_offset, 0 );

	// last try, guess timezone string manually
	if ( false === $timezone ) {
		$is_dst = date( 'I' );

		foreach ( timezone_abbreviations_list() as $abbr ) {
			foreach ( $abbr as $city ) {
				if ( $city['dst'] == $is_dst && $city['offset'] == $utc_offset ) {
					return $city['timezone_id'];
				}
			}
		}

		// fallback to UTC
		return 'UTC';
	}

	return $timezone;
}

function qm_dropbox_posts( $args = array(), $echo = true ) {
	
	$args		=	wp_parse_args($args, array(
		'post_type'	=>	'member'
	));
	
	$posts		=	get_posts(array(
		'post_type'	=>	$args['post_type']
	));
	
	$options	=	'<option value="0"' . selected($args['selected'], 0, false) . '>' . __('None', 'quizmaker') . '</option>';
	
	foreach($posts as $index => $post) {
		
		$options	.=	sprintf('<option value="%s" %s>%s</option>', $post->ID, selected($args['selected'], $post->ID, false), $post->post_title);
	}
	
	$output	=	sprintf( '<select name="%s">%s</select>', $args['name'], $options );
	
	if($echo){
		
		echo $output;
		
	}else{
		
		return $output;
	}
		
}

function qm_active( $value, $active, $echo = true, $text = 'active' ) {
	
	if($value == $active){
		
		if( $echo ){
			
			echo $text;
			
		}else{
			
			return $text;
			
		}
	}
}

function qm_pagination_format( $results, $page = 1, $perpage = 20) {
	
	if(!isset($results) || !is_array($results) || !$results) {
		
		return false;
	}
	
	if($page == -1){
		
		return array(
			'data'			=>	$results,
			'pagination'	=>	array(
				'page' 		=>	$page,
				'perpage' 	=>	$perpage
			)
		);
	}
	
	$page		=	$page ? $page : 1;
	
	$data		=	array_chunk( $results, $perpage );
	
	$total		=	count( $results );
	$pages		=	count( $data );
	
	if($data) {
				
		$data	=	$data[$page - 1];
		
		foreach($data as $i => &$value){
			
			if(is_array($value)){
				$value['index']	=	((count($data) * ($page - 1)) + $i) + 1;
			}elseif(is_object($value)){
				$value->index	=	((count($data) * ($page - 1)) + $i) + 1;
			}
		}
	}
	
	return array(
		'data'			=>	$data,
		'pagination'	=>	array(
			'page' 		=>	$page,
			'pages'		=>	$pages,
			'perpage' 	=>	$perpage,
			'total'		=>	$total
		)
	);
}

function qm_romanic_number( $integer = 1, $upcase = true ) { 
		
    $table = array('M'=>1000, 'CM'=>900, 'D'=>500, 'CD'=>400, 'C'=>100, 'XC'=>90, 'L'=>50, 'XL'=>40, 'X'=>10, 'IX'=>9, 'V'=>5, 'IV'=>4, 'I'=>1);
	
    $return = '';
	
	$integer	=	$integer ? $integer : 1;
	
    while($integer > 0) 
    { 
        foreach($table as $rom=>$arb) 
        { 
            if($integer >= $arb) 
            { 
                $integer -= $arb; 
                $return .= $rom; 
                break; 
            } 
        } 
    } 

    return $return; 
}

function qm_alphabet_number( $integer, $upcase = true ) {
	
	if( !is_numeric( $integer ) ) return false;
	
	if( $upcase ){
		
		$table	=	range( 'A', 'Z' );
		
	}else{
		
		$table	=	range( 'a', 'z' );
	}

	return $table[$integer-1];
}

function qm_order_content( $integer = 0, $type = 0, $upcase = true ) {
	
	if( !$type ) return false;
	
	if( $type == 1 ){
		
		return qm_alphabet_number( $integer, 1, $upcase );
		
	} elseif ( $type == 2 ){
				
		return $integer;
		
	} elseif ( $type == 3) {
		
		return qm_romanic_number( $integer, 3, $upcase );
		
	}
}

function qm_sanitize_tooltip( $var ) {
	return htmlspecialchars( wp_kses( html_entity_decode( $var ), array(
		'br'     => array(),
		'em'     => array(),
		'strong' => array(),
		'small'  => array(),
		'span'   => array(),
		'ul'     => array(),
		'li'     => array(),
		'ol'     => array(),
		'p'      => array(),
    ) ) );
}

function qm_get_field_value_formbuilder( $name, $value, $form_data ) {

	foreach( $form_data as $fd ) {

		if( isset($fd['name']) && ($name == $fd['name']) ) {

			if( in_array($fd['type'], array('select', 'radio-group')) && $fd['values'] ) {

				foreach( $fd['values'] as $fd_v ) {

					if( $fd_v['value'] == $value ) {

						return $fd_v['label'];
					}
				}

			}elseif ( in_array($fd['type'], array('text', 'number')) ) {
				
				return $value;

			}elseif ( $fd['type'] == 'checkbox-group' ) {

				$lbm = array();

				foreach( $fd['values'] as $fd_v ) {
					
					if( is_array($value) && in_array($fd_v['value'], $value) ) {

						$lbm[] = $fd_v['label'];
					}
				}

				if( $lbm ) {

					return implode(', ', $lbm);
				}

				return '';
			}

			
		}
	}

	return '';
}


function qm_generate_formbuilder( $form_data, $params = array() ) {

	if( !$form_data ) { return false; }
	
	$params = wp_parse_args( $params, array(
		'wrap_name' => false
	) );

	foreach( $form_data as $field ) {

		switch ( $field['type'] ) {
			case 'select':
				
				$name = $field['name'];

				if( $params['wrap_name'] ) {

					$name = $params['wrap_name'] . '[' . $name . ']';
				}

				?>
				
				<div class="form-group <?php echo isset($field['className']) ? $field['className'] : ''; ?>">
					<?php if(isset($field['label']) && $field['label']): ?>
					<label for="<?php echo $name; ?>"><?php echo $field['label']; ?></label>
					<?php endif; ?>

					<div class="wrap-input">
						<select name="<?php echo $name; ?>" class="form-control <?php echo isset($field['className']) ? $field['className'] : ''; ?>" <?php echo isset($field['required']) ? 'required' : ''; ?>>
							<?php foreach( $field['values'] as $option ): ?>
							<option value="<?php echo $option['value']; ?>" <?php echo isset($option['selected']) ? 'selected': ''; ?>><?php echo $option['label']; ?></option>
							<?php endforeach; ?>
						</select>
					</div>
				</div>
<?php
				break;
			
			case 'text': 

				$name = $field['name'];

				if( $params['wrap_name'] ) {

					$name = $params['wrap_name'] . '[' . $name . ']';
				}

			?>	
				<div class="form-group <?php echo isset($field['className']) ? $field['className'] : ''; ?>">
					<?php if(isset($field['label']) && $field['label']): ?>
					<label for="<?php echo $name; ?>"><?php echo $field['label']; ?></label>
					<?php endif; ?>
					<input type="text" name="<?php echo $name; ?>" class="<?php echo $field['className']; ?>" id="<?php echo 'input-' . sanitize_title($field['label']); ?>" placeholder="<?php echo isset($field['placeholder']) ? $field['placeholder'] : ''; ?>" <?php echo isset($field['required']) ? 'required' : ''; ?>/>
				</div>

<?php
				break;

			case 'textarea': 

				$name = $field['name'];

				if( $params['wrap_name'] ) {

					$name = $params['wrap_name'] . '[' . $name . ']';
				}

			?>
				
				<div class="form-group <?php echo isset($field['className']) ? $field['className'] : ''; ?>">
					<?php if(isset($field['label']) && $field['label']): ?>
					<label for="<?php echo $name; ?>"><?php echo $field['label']; ?></label>
					<?php endif; ?>
					
					<textarea 
					id="<?php echo $name; ?>" 
					name="<?php echo $name; ?>" 
					class="form-control <?php echo $field['className']; ?>" 
					rows="<?php echo isset($field['rows']) ? absint($field['rows']) : 4; ?>" 
					placeholder="<?php echo isset($field['placeholder']) ? $field['placeholder'] : ''; ?>" <?php echo isset($field['required']) ? 'required' : ''; ?>><?php echo isset($field['value']) ? $field['value'] : ''; ?></textarea>
				</div>
				
			<?php break; ?>

			<?php case 'paragraph': ?>

				<div class="fillform_paragraph">
					<?php echo $field['label']; ?>
				</div>

			<?php break; ?>

			<?php case 'number': 

				$name = $field['name'];

				if( $params['wrap_name'] ) {

					$name = $params['wrap_name'] . '[' . $name . ']';
				}

			?>

				<div class="form-group <?php echo isset($field['className']) ? $field['className'] : ''; ?>">
				<?php if(isset($field['label']) && $field['label']): ?>
					<label for=""><?php echo $field['label']; ?></label>
					<?php endif; ?>
					
					<input type="number" 
						name="<?php echo $name; ?>" 
						class="form-control <?php echo $field['className']; ?>" 
						placeholder="<?php echo isset($field['placeholder']) ? $field['placeholder'] : ''; ?>" 
						value="<?php echo $field['value']; ?>" 
						step="<?php echo isset($field['step']) ? $field['step'] : 1; ?>" 
						min="<?php echo isset($field['min']) ? $field['min'] : 0; ?>" 
						<?php echo isset($field['max']) ? 'max="' . $field['max'] . '"' : ''; ?> <?php echo isset($field['required']) ? 'required' : ''; ?>
						/>
				</div>
			<?php break; ?>

			<?php case 'checkbox-group':

				$name = $field['name'];

				if( $params['wrap_name'] ) {

					$name = $params['wrap_name'] . '[' . $name . ']';
				}

				?>
				
				<div class="form-group <?php echo isset($field['className']) ? $field['className'] : ''; ?>">
					<?php if(isset($field['label']) && $field['label']): ?>
					<label for=""><?php echo $field['label']; ?></label>
					<?php endif; ?>

					<div class="wrap-input checkbox">
						<?php foreach( $field['values'] as $index => $option ): ?>
						<div class="form-check">

							<input class="form-check-input" type="checkbox" value="<?php echo $option['value']; ?>" name="<?php echo $name; ?>[]" id="<?php echo $name . '-' . $index; ?>" <?php echo isset($option['selected']) ? 'checked': ''; ?>/>
							<label class="form-check-label" for="<?php echo $name . '-' . $index; ?>"><?php echo $option['label']; ?></label>

						</div>
						<?php endforeach; ?>
					</div>
				</div>

			<?php break; ?>

			<?php case 'radio-group':

				$name = $field['name'];

				if( $params['wrap_name'] ) {

					$name = $params['wrap_name'] . '[' . $name . ']';
				}

				?>
				
				<div class="form-group <?php echo isset($field['className']) ? $field['className'] : ''; ?>">
					<?php if(isset($field['label']) && $field['label']): ?>
					<label for=""><?php echo $field['label']; ?></label>
					<?php endif; ?>

					<div class="wrap-input radio">
						<?php foreach( $field['values'] as $index => $option ): ?>

						<div class="form-check">

							<input class="form-check-input" type="radio" value="<?php echo $option['value']; ?>" name="<?php echo $name; ?>" id="<?php echo $name . '-' . $index; ?>" <?php echo isset($option['selected']) ? 'checked': ''; ?>/>
							<label class="form-check-label" for="<?php echo $name . '-' . $index; ?>"><?php echo $option['label']; ?></label>
							
						</div>

						<?php endforeach; ?>
					</div>
				</div>

			<?php break; ?>
<?php
			default:
				# code...
				break;
		}

	}
}

function qm_get_formated_user_name( $current_user ) {

	$name = '';

	if( !$current_user->user_firstname && !$current_user->user_lastname ) {

		$user_info = get_userdata($current_user->ID);
		
		$name  = $user_info->nickname;

	}else{

		$name  = $current_user->user_firstname . ' ' . $current_user->user_lastname;
	}

	return $name;
}

function qm_get_formated_user_meta( $user_meta ) {

	$um_str = '';

	if( is_array($user_meta) && $user_meta ) {	

		foreach( $user_meta as $um_label => $um_value ) {

			 $um_str .= $um_label . ': ' . $um_value . "\r\n";
		}
	}

	return trim($um_str);
}

function get_form_data_after_play_test() {

	$form_settings = get_option('quizmaker_forms_after_play_test_settings');

	$form_data = array();

	if( isset($form_settings['form_data']) ) {

		$form_data = $form_settings['form_data'];

	}

	return $form_data;
}

function get_facebook_image_result( $result_id ) {
	
	$result	=	QM_Test::get_result( $result_id );

	$test   = 	new QM_Test( $result['test_id'] );
	
	$image	=	new QM_Image( array('width' => 600, 'height' => 315, 'color' => '#007fca') );

	$image->addText( array(
		'text' 			=> $result['percent'] . '%', 
		'font_name' 	=> 'opensans_light',
		'font_size' 	=> 90,
		'text_align'	=> 'center',
		'color'			=> '#ffffff',
		'angle'			=>	0,
		'x'				=> 300,
		'y'				=> 100
	 ) );

	return $image->store( 'result_' . $result_id );
}

function qm_load_styles()
{

	if(get_option('quizmaker_is_default_stylesheet') == 'yes') {
		
		wp_enqueue_style( 'material-icons', 'https://fonts.googleapis.com/icon?family=Material+Icons' );
		
		wp_enqueue_style( 'quizmaker-frontend', QUIZMAKER_URI . 'assets/css/frontend.css', array(), '1.5.9' );
		wp_enqueue_style('quizmaker-bootstrap-library', QUIZMAKER_URI . 'assets/vendor/bootstrap/css/bootstrap.min.css', array(), '4.0.2');
	}

}