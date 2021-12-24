<?php

define ('THEME_NAME',   'Sahifa' );
define ('THEME_FOLDER', 'sahifa' );
define ('THEME_VER',    '5.7.6'  );	//DB Theme Version

define( 'NOTIFIER_XML_FILE',      "http://themes.tielabs.com/xml/".THEME_FOLDER.".xml" );
define( 'NOTIFIER_CHANGELOG_URL', "http://tielabs.com/changelogs/?id=2819356" );
define( 'DOCUMENTATION_URL',      "http://themes.tielabs.com/docs/".THEME_FOLDER );

if ( ! isset( $content_width ) ) $content_width = 618;

// Main Functions
require_once ( get_template_directory() . '/framework/functions/theme-functions.php');
require_once ( get_template_directory() . '/framework/functions/common-scripts.php' );
require_once ( get_template_directory() . '/framework/functions/mega-menus.php'     );
require_once ( get_template_directory() . '/framework/functions/pagenavi.php'       );
require_once ( get_template_directory() . '/framework/functions/breadcrumbs.php'    );
require_once ( get_template_directory() . '/framework/functions/tie-views.php'      );
require_once ( get_template_directory() . '/framework/functions/translation.php'    );
require_once ( get_template_directory() . '/framework/widgets.php'                  );
require_once ( get_template_directory() . '/framework/admin/framework-admin.php'    );
require_once ( get_template_directory() . '/framework/shortcodes/shortcodes.php'    );

if( tie_get_option( 'live_search' ) )
	require_once ( get_template_directory() . '/framework/functions/search-live.php');

if( !tie_get_option( 'disable_arqam_lite' ) )
	require_once ( get_template_directory() . '/framework/functions/arqam-lite.php');



/* WPCOVID-19
if( ! class_exists( 'WPCOVID19_CLASS' ) ){
	add_action( 'admin_menu',    'tie_wpcv_add_admin' );
}


function tie_wpcv_add_admin() {
	add_submenu_page( 'panel', __( 'WPCOVID-19', 'tie' ), __( 'WPCOVID-19', 'tie' ) ,'switch_themes', 'tie_wpcv' , 'tie_get_wpcovid');
}
function tie_get_wpcovid(){
	echo "<script type='text/javascript'>window.location='https://wpcovid19.com/?utm_campaign=tielabs&utm_medium=themes&utm_source=sahifa';</script>";
}

*/
add_theme_support( 'post-formats', array( 'aside', 'gallery' ) );
add_theme_support( 'post-thumbnails', array( 'page' ) );          // Pages only

