<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/**
 * My Plugins only works in WordPress 4.8.3 or later. 
 * If Woocommerce required, use argument 'woo' as true in wa_check_requirement method.
 * 
 */
if ( !class_exists('WebamatorCheckRequirement') ) {

	class WebamatorCheckRequirement {

		var $requirements;

		function set_requirements($requirements) {
			$this->requirements = $requirements;
		}


		function get_requirements() {
			return (object) $this->requirements;
		}


		function check_requirement() {

			$this->wa_check_wp_version();
		
			if ( $this->get_requirements()->woo === true ) {
				add_action( 'admin_init', array($this,'wa_check_woo_enabled' ) );
			}

		}




		function wa_check_wp_version() {

			if ( version_compare( $GLOBALS['wp_version'], '4.8.3', '<' ) ) {
				add_action( 'admin_notices', array($this,'wordpress_upgrade_notice' ) ) ;
			}

		}

		function wordpress_upgrade_notice() {

			$message = sprintf( $this->get_requirements()->name . __( ' requires at least WordPress version 4.8.3. You are running version %s. Please upgrade and try again.', $this->get_requirements()->slug ), $GLOBALS['wp_version'] );
			echo '<style>div#message.updated{ display: none; }</style>';
			printf( '<div class="error"><p>%s</p></div>', $message );
			deactivate_plugins( $this->get_requirements()->file );

		}
		
		
		
		
		function wa_check_woo_enabled() {

			if ( ! is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
				add_action( 'admin_notices', array($this, 'woocommerce_required_notice'), 10, 1) ;
			}

		}
		
		function woocommerce_required_notice() {

			$message = sprintf( $this->get_requirements()->name . __( ' requires WooCommerce. Please install/enable it and try again.', $this->get_requirements()->slug ), $GLOBALS['wp_version'] );
			echo '<style>div#message.updated{ display: none; }</style>';
			printf( '<div class="error"><p>%s</p></div>', $message );
			deactivate_plugins( $this->get_requirements()->file );

		}	


	}

}
?>