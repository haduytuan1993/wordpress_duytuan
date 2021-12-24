<?php
/**
 * Admin View: Settings
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

?>

<div class="wrap quizmaker">
	<form method="<?php echo esc_attr( apply_filters( 'quizmaker_settings_form_method_tab_' . $current_tab, 'post' ) ); ?>" id="mainform" action="" enctype="multipart/form-data">
		<div class="icon32 icon32-woocommerce-settings" id="icon-quizmaker"><br /></div>
		<h2 class="nav-tab-wrapper woo-nav-tab-wrapper">
			<?php
				foreach ( $tabs as $name => $label ) {
					echo '<a href="' . admin_url( 'admin.php?page=qm-settings&tab=' . $name ) . '" class="nav-tab ' . ( $current_tab == $name ? 'nav-tab-active' : '' ) . '">' . $label . '</a>';
				}

				do_action( 'quizmaker_settings_tabs' );
			?>
		</h2>

		<?php
			self::show_messages();

			do_action( 'quizmaker_sections_' . $current_tab );
			do_action( 'quizmaker_settings_' . $current_tab );
			do_action( 'quizmaker_settings_tabs_' . $current_tab ); // @deprecated hook
		?>

		<p class="submit">
			<?php if ( ! isset( $GLOBALS['hide_save_button'] ) || !$GLOBALS['hide_save_button'] ) : ?>
				<input name="save" class="button-primary" id="<?php echo 'btn-save-' . $current_tab; ?>" type="submit" value="<?php esc_attr_e( 'Save changes', 'quizmaker' ); ?>" />
			<?php endif; ?>
			<input type="hidden" name="subtab" id="last_tab" />
			<?php wp_nonce_field( 'quizmaker-settings' ); ?>
		</p>
	</form>
</div>
