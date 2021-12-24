<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class QM_Image {
	
	public $image, $font_dir;
	public $fonts = array(
		'alegreya_regular'			=>	'alegreya-regular.otf',
		'bebasneue_regular'			=>	'bebasneue-regular.ttf',
		'bebasneue_light'			=>	'bebasneue-light.ttf',
		'bebasneue_thin'			=>	'bebasneue-thin.ttf',
		'bebasneue_bold'			=>	'bebasneue-bold.ttf',
		'bebasneue_book'			=>	'bebasneue-book.ttf',
		'greatvibes_regular'		=>	'greatvibes-regular.otf',
		'notosans_regular'			=>	'notosans-regular.ttf',
		'notosans_bold'				=>	'notosans-bold.ttf',
		'notosans_italic'			=>	'notosans-italic.ttf',
		'notosans_bolditalic'		=>	'notosans-bolditalic.ttf',
		'opensans_regular'			=>	'opensans-regular.ttf',
		'opensans_bold'				=>	'opensans-bold.ttf',
		'opensans_italic'			=>	'opensans-italic.ttf',
		'opensans_extrabold'		=>	'opensans-extrabold.ttf',
		'opensans_extrabolditalic'	=>	'opensans-extrabolditalic.ttf',
		'opensans_thin'				=>	'opensans-thin.ttf',
		'opensans_bolditalic'		=>	'opensans-bolditalic.ttf',
		'opensans_light'			=>	'opensans-light.ttf',
		'opensans_lightitalic'		=>	'opensans-lightitalic.ttf',
	);
		
	function __construct( $options = array() ) {

		$this->font_dir = QUIZMAKER_DIR . '/assets/fonts/';
		
		if( isset($options['src']) ){
			$this->image_src	=	$options['src'];
			
			switch( $this->get_image_type($this->image_src) ){
				case 'image/png':
					$this->image		=	@imagecreatefrompng( $this->image_src );
					$this->imagetype	=	IMAGETYPE_PNG;
				break;
				case 'image/jpeg':
					$this->image		=	@imagecreatefromjpeg( $this->image_src );
					$this->imagetype	=	IMAGETYPE_JPEG;
					
				break;
			}

		}else{

			$this->image = imagecreatetruecolor( $options['width'], $options['height'] );
			$this->imagetype = IMAGETYPE_PNG;

			imagefill( $this->image, 0, 0, $this->getColor($options['color']) );
		}			
	}

	function get_image_type( $filename ) {
	    $img = getimagesize( $filename );
	    if ( !empty( $img[2] ) )
	        return image_type_to_mime_type( $img[2] );
		return false;
	}
	
	function getFont( $name ) {
		
		$font	=	$this->font_dir . $this->fonts[ $name ];
		
		return $font;
	}
	
	function getImageSize() {
		
		return getimagesize($this->image_src);
	}
	
	function getDimensionXCenter() {
		
		$container	=	$this->getImageSize();
		
		$x_axis		=	$container[0]/2;
		$y_axis		=	$container[1]/2;
		
		return array( $x_axis, $y_axis );
	}
	
	function addText( $params ) {
		
		$color	=	$this->getColor( $params['color'] );
		
		$font	=	$this->getFont( $params['font_name'] );
		
		$axis	=	$this->getDimensionText( $params );
		
		$params['x']	=	$axis[0];
		$params['y']	=	$axis[1];
		$width			=	absInt($axis[2]);
		$height			=	absInt($axis[3]);

		$axis_x	= absInt($axis[0]);	
		$axis_y = absInt($axis[1]);
		
		switch($params['text_align']){
			case 'center':
			
				$axis_x	=	$axis_x - ($axis[2]/2);
				
			break;
			case 'right':
				
				$axis_x	=	$axis_x -($axis[2]);
				
			break;
		}
		
		imagefttext( $this->image, $params['font_size'], $params['angle'], $axis_x, $axis_y, $color, $font, $params['text'] );
		
		return $this->image;
	}
	
	private function _convertBoundingBox ($bbox) {
	   
	    if ($bbox[0] >= -1)
	        $xOffset = -abs($bbox[0] + 1);
	    else
	        $xOffset = abs($bbox[0] + 2);
	    $width = abs($bbox[2] - $bbox[0]);
	    if ($bbox[0] < -1) $width = abs($bbox[2]) + abs($bbox[0]) - 1;
	    $yOffset = abs($bbox[5] + 1);
	    if ($bbox[5] >= -1) $yOffset = -$yOffset;
	    $height = abs($bbox[7]) - abs($bbox[1]);
	    if ($bbox[3] > 0) $height = abs($bbox[7] - $bbox[1]) - 1;
	    return array(
	        'width' => $width,
	        'height' => $height,
	        'xOffset' => $xOffset,
	        'yOffset' => $yOffset,
	        'belowBasepoint' => max(0, $bbox[1])
	    );
	}
	
	function getDimensionText( $params ) {
		$font	=	$this->getFont( $params['font_name'] );
		
		$type_space = imagettfbbox($params['font_size'], 0, $font, $params['text']);

		$axis = $this->_convertBoundingBox( $type_space );

		$x_axis = $axis['xOffset'] + $params['x'];
		$y_axis = $axis['yOffset'] + $params['y'];

		return array( $x_axis, $y_axis, $axis['width'], $axis['height'] );
	}

	function getDimensionTextCenter( $params = array() ) {
		
		$font	=	$this->getFont( $params['font_name'] );
		
		$type_space = imagettfbbox($params['font_size'], 0, $font, $params['text']);
		
		$width	=	abs($type_space[4] - $type_space[0]);
		$height	=	abs($type_space[5] - $type_space[1]);
		
		$x_axis	=	$params['x'] - ($width/2);
		$y_axis	=	$params['y'];
		
		return array( $x_axis, $y_axis );
	}
	
	function getDimensionTextRight( $params = array() ) {
		
		$font	=	$this->getFont( $params['font_name'] );
		
		$type_space = imagettfbbox($params['font_size'], 0, $font, $params['text']);
		
		$width	=	abs($type_space[4] - $type_space[0]);
		$height	=	abs($type_space[5] - $type_space[1]);
		
		$x_axis	=	$params['x'] - $width;
		$y_axis	=	$params['y'];
		
		return array( $x_axis, $y_axis );
	}
	
	function getColor( $colorName = '#000000' ) {
		
		list($r, $g, $b) = array_map('hexdec',str_split(ltrim($colorName, '#'),2));
		
		$color	=	imagecolorallocate($this->image, $r, $g, $b);
		
		return $color;
	}

	function getTextBoundingBox( $text, $params = array() ) {

		$params 			=	wp_parse_args( $params, array( 
			'font_name' => 'opensans_regular', 
			'font_size' => 20, 'x' => 0, 'y' => 0, 'angle' => 0, 'color' => '#000000' ) );

		$params['text-align'] = 'left';

		$font				=	$this->getFont( $params['font_name'] );
		
		$textBoundingBox 	=	imagettfbbox($params['font_size'], 0, $font, $text);

		if( !$textBoundingBox ){
			
			return false;
		}

		$result 	=	$this->_convertBoundingBox( $textBoundingBox );

		$this->image 		= 	@imagecreatetruecolor( $result['width'] + 6, $result['height'] );
		
		$background = imagecolorallocate( $this->image, 255, 255, 255 );

		imagefill($this->image, 0, 0, $background); 

		imagecolortransparent($this->image, $background);

		$params['text'] = $text;

		$this->addText( $params );

		ob_start (); 
			imagepng( $this->image );
			
			$image_data = ob_get_contents(); 

		ob_end_clean (); 

		$result['image_base64'] = base64_encode($image_data);

		imagedestroy($this->image);

		return $result;
	}
	
	function output( $name = '' ) {

		if( !$name ) {

			$name = uniqid();
		}
		
		header('Content-Type: ' . image_type_to_mime_type($this->imagetype));
		
		switch($this->imagetype){
			case IMAGETYPE_PNG:
				header('Content-Disposition: attachment; filename="' . $name . '.png"');
				imagepng( $this->image );
			break;
			case IMAGETYPE_JPEG:
				header('Content-Disposition: attachment; filename="' . $name . '.jpg"');
				imagejpeg( $this->image );
			break;
		}
		
		imagedestroy( $this->image );
	}

	function outputPDF() {

		$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

		$path_file_image = $this->store('pdf1');

		$image_size      = getimagesize( $path_file_image );

		$pdf->SetAutoPageBreak(false, 0);
		$pdf->AddPage();
		$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
		$pdf->Image($path_file_image, 0, 0, $image_size[0], $image_size[1], '', '', '', false, 300, '', false, false, 0);

		$pdf->setPageMark();

		$pdf->Output('example_009.pdf', 'D');
	}

	function store( $filename, $is_url = true ) {

		
		$wp_upload_dir	= wp_upload_dir();
		$path 			= $wp_upload_dir['path'];
		$url			= $wp_upload_dir['url'];

		switch($this->imagetype){
			case IMAGETYPE_PNG:

				$filename .= '.png';

				imagepng( $this->image, $path . '/' . $filename );

			break;
			case IMAGETYPE_JPEG:

				$filename .= '.jpg';

				imagejpeg( $this->image, $path . '/' . $filename );

			break;
		}
		
		imagedestroy( $this->image );

		if( $is_url ){
			
			return $url . '/' . $filename;
		}else{

			return $path . '/' . $filename;
		}
	}
}

