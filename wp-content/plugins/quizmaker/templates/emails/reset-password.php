<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

?>

<h1 style="margin-top:0;margin-right:0;margin-left:0;padding-top:0;padding-bottom:0;padding-right:0;padding-left:0;font-family:'HelveticaNeue-Light', 'Helvetica Neue Light', 'Helvetica Neue', Helvetica, Arial, 'Lucida Grande', sans-serif;line-height:1.1;margin-bottom:24px;color:#5a5a5a;font-weight:bold;font-size:27px;text-align:center;" >RESET PASSWORD</h1>

<p style="margin-top:0;margin-right:0;margin-left:0;padding-top:0;padding-bottom:0;padding-right:0;padding-left:0;font-family:'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;margin-bottom:10px;font-weight:normal;font-size:14px;line-height:1.6;">
	<?php _e( 'Someone requested that the password be reset for the following account:', 'quizmaker' ); ?>
</p>
<p style="margin-top:0;margin-right:0;margin-left:0;padding-top:0;padding-bottom:0;padding-right:0;padding-left:0;font-family:'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;margin-bottom:10px;font-weight:normal;font-size:14px;line-height:1.6;">
	<?php printf( __( 'Username: %s', 'quizmaker' ), $user_login ); ?>
</p>
<p style="margin-top:0;margin-right:0;margin-left:0;padding-top:0;padding-bottom:0;padding-right:0;padding-left:0;font-family:'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;margin-bottom:10px;font-weight:normal;font-size:14px;line-height:1.6;">
	<?php _e( 'If this was a mistake, just ignore this email and nothing will happen.', 'quizmaker' ); ?>
</p>
<p style="margin-top:0;margin-right:0;margin-left:0;padding-top:0;padding-bottom:0;padding-right:0;padding-left:0;font-family:'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;margin-bottom:10px;font-weight:normal;font-size:14px;line-height:1.6;">
	<?php _e( 'To reset your password, visit the following address:', 'quizmaker' ); ?>
</p>
<p style="text-align:center;margin-top: 24px;">
<a class="btn" href="<?php echo $link_reset; ?>" style="margin-top:0;margin-bottom:0;margin-left:0;font-family:'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;text-decoration:none;color:#FFF;background-color:#666;padding-top:10px;padding-bottom:10px;padding-right:16px;padding-left:16px;font-weight:bold;margin-right:10px;text-align:center;cursor:pointer;display:inline-block;" ><?php _e( 'Click here to reset your password', 'quizmaker' ); ?></a>
</p>