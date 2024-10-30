<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( !class_exists('WebamatorCheckPlugins') ) {

	class WebamatorCheckPlugins {

		var $plugin_data;

		function set_plugin_data($plugin_data) {
			$this->plugin_data = $plugin_data;
		}


		function get_plugin_data() {
			return (object) $this->plugin_data;
		}

		function add_wa_plugins_menu() {

			add_action( 'admin_menu', array( $this, 'wa_plugins_menu' ), 10, 1 ) ;

		}
		
		function wa_plugins_menu(){

			if ( empty ( $GLOBALS['admin_page_hooks']['wa-plugins'] ) ) {
				add_menu_page( __( 'WA plugins', $this->get_plugin_data()->text_domain ), __( 'WA plugins', $this->get_plugin_data()->text_domain ), 'manage_options', 'wa-plugins', array( $this, 'wa_plugins_options_page' ), '', 50 );
			}
		}
		
		
		function wa_plugins_options_page($text_domain) {


			$text_domain = $this->get_plugin_data()->text_domain;		
		
			$webamator_total_plugins = array (
				'Woocommerce Exclude States' 			=> 'woocommerce-exclude-states',
				'Woocommerce Payment Method Rotator' 	=> 'woocommerce-payment-method-rotator',
				'Webamator CSV Exporter' 				=> 'wa-csv-exporter',
				'Youtube Embed Performance' 			=> 'yep-youtube-embed',
				'Lazy Load for GMaps' 					=> 'lazy-load-for-gmaps',
			);
			$webamator_active_plugins = array();

			foreach ($webamator_total_plugins as $key => $plugin) {
				if ( is_plugin_active( $plugin.'/'.$plugin.'.php' ) ) {
					$webamator_active_plugins[$key] = $plugin;
				}
			}


			$webamator_plugins_string = '';
			$i=0;
			foreach ($webamator_active_plugins as $key => $plugin) {

				$i++;
				if ( $i == count($webamator_active_plugins) ) {
					$line_comma = '.';
				} else if ( $i == (count($webamator_active_plugins) - 1) ) {
//					$line_comma = ' and ';
					$line_comma = __( ' and ', $this->get_plugin_data()->text_domain );
					
				} else {
					$line_comma = ', ';
				}
				$permalink = str_replace ('-','_',$plugin);
				$webamator_plugins_string .= '<a href="'. esc_url( get_admin_url(null, 'admin.php?page='.$permalink) ) .'">'.$key.'</a>'.$line_comma;

			}


		?>	
		<div class="wrap">	
			<h2 id="title"><?php _e( 'Plugins by Webamator', $text_domain ); ?></h2>
			<p><?php _e( 'Thank you for use my plugins:', $text_domain ); ?> <?php echo $webamator_plugins_string; ?></p>
		</div>
		<?php 	

		
		}

	}

}
?>