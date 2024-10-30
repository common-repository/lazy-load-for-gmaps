<?php
/*
 * Plugin Name:			Lazy Load for GMaps
 * Plugin URI:			https://www.helper-wp.com/plugins/lazy-load-google-maps/
 * Description:			This plugin make your Google Maps in publications and pages more faster via Lazy Load.
 * Version:				1.0.3
 * Requires at least:	4.8.3
 * Requires PHP:		5.6
 * Author:				Webamator
 * Author URI:			https://www.helper-wp.com/wordpress-freelancer/
 * License:				GPL v2 or later
 * License URI:			http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:			lazy-load-for-gmaps
 * Domain Path:			/languages/
*/

/*
    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

	load_plugin_textdomain( 'lazy-load-for-gmaps', false, basename( dirname( __FILE__ ) ) . '/languages/' );

	require_once ( plugin_dir_path ( __FILE__ ) . 'includes/webamator-check-requirement.php' );
	$waRequirements = new WebamatorCheckRequirement();

	$requirements = array (
		'file'	=> plugin_basename( __FILE__ ),
		'name'	=> 'Lazy Load for Google Maps',
		'slug'	=> 'lazy-load-for-gmaps',
		'woo'	=> false
	);
	
	$waRequirements->set_requirements( $requirements );
	$waRequirements->check_requirement( $requirements );



	require_once( 'includes/webamator-check-plugins.php' );
	$waPlugins = new WebamatorCheckPlugins();

	$plugin_data = array (
		'text_domain'	=> 'lazy-load-for-gmaps',
	);
	$waPlugins->set_plugin_data( $plugin_data );
	$waPlugins->add_wa_plugins_menu( $plugin_data );

	//let`s go :)

	register_activation_hook(__FILE__, 'lazy_load_google_maps_set_options');
	register_deactivation_hook(__FILE__, 'lazy_load_google_maps_unset_options');

	add_action('admin_init', 'll_gmaps_plugin_settings');
	add_action('admin_enqueue_scripts', 'lazy_load_google_maps_admin_style' );
	add_action('admin_menu', 'register_lazy_load_google_maps_submenu_page');
	add_action('admin_menu', 'options_lazy_load_google_maps_submenu_page');
	add_action('wp_head', 'lazy_load_google_maps_style', 100);
	add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'lazy_load_google_maps_action_links' );

	function lazy_load_google_maps_set_options() {
	
		add_option('ll_gmaps_version', '1.0.3');
		add_option('ll_gmaps_date_install', time());

	}


	function lazy_load_google_maps_unset_options() {

		delete_option('ll_gmaps_version');
		delete_option('ll_gmaps_date_install');

	}

	
	function register_lazy_load_google_maps_submenu_page() {

		add_submenu_page( 'wa-plugins', __( 'Lazy Load GMaps', 'lazy-load-for-gmaps' ), __( 'Lazy Load GMaps', 'lazy-load-for-gmaps' ), 'manage_options', 'lazy_load_google_maps', 'lazy_load_google_maps_options_page' ); 

	}	

	function options_lazy_load_google_maps_submenu_page() {

		add_submenu_page( 'options-general.php', __( 'Lazy Load GMaps', 'lazy-load-for-gmaps' ), __( 'Lazy Load GMaps', 'lazy-load-for-gmaps' ), 'manage_options', 'lazy_load_google_maps', 'lazy_load_google_maps_options_page' ); 

	}	

	function lazy_load_google_maps_action_links( $links ) {

		$links[] = '<a href="'. esc_url( get_admin_url(null, 'options-general.php?page=lazy_load_google_maps') ) .'">'.__( 'Settings', 'lazy-load-for-gmaps' ).'</a>';
		return $links;

	}	
	
	function lazy_load_google_maps_admin_style() {
	
		wp_enqueue_style( 'll_gmaps_admin_style', plugins_url('assets/css/admin.css', __FILE__));
	
	}


	function lazy_load_google_maps_style() {

		echo '<style>';
		echo '.lazy-load-for-gmaps-wrap{width:100%;height:100%;}';
		echo '.lazy-load-for-gmaps-wrap:before{display:block;content:"";padding: 30%;}';
		echo '</style>';

	}


	function wa_shortcode_lazy_load_google_map($atts=false) {
	
		$data_zoom		= 15;
		$data_latitude	=
		$data_longitude	=
		$wrapper_width	= 
		$wrapper_height	=
		$wrapper_style	= 
		$map_lang		= false;


		if (isset($atts['latitude']) && preg_match('/^(|-)[\d]{1,3}.[\d]+$/', $atts['latitude'])){
			$data_latitude = $atts['latitude'];
		}
		if (isset($atts['longitude']) && preg_match('/^(|-)[\d]{1,3}.[\d]+$/', $atts['longitude'])){
			$data_longitude = $atts['longitude'];
		}
		if (isset($atts['zoom']) && preg_match('/^[\d]{1,2}+$/', $atts['zoom'])){
			$data_zoom = $atts['zoom'];
		}		
		if (isset($atts['width']) && preg_match('/^[\d]+$/', $atts['width'])){
			$wrapper_width = 'width:'.$atts['width'].'px;';
		}
		if (isset($atts['height']) && preg_match('/^[\d]+$/', $atts['height'])){
			$wrapper_height = 'height:'.$atts['height'].'px;';
		}
		if (isset($atts['language']) && preg_match('/^[a-z]{2,3}+$/', $atts['language'])){
			$map_lang = '&language='.$atts['language'];
		}
		if ($wrapper_width || $wrapper_height){
			$wrapper_style = ' style="'.$wrapper_width.$wrapper_height.'"';
		}

		
		static $i=1;

	
		if ($data_latitude && $data_longitude){
		
			$uluru = '	mapsarr['.$i.'] = ['.$data_latitude.','.$data_longitude.','.$data_zoom.'];';

			if ($i === 1){

				$settings = get_option('ll_gmaps_settings');
				$api_key = isset( $settings['ll_gmaps_api_key'] ) ? $settings['ll_gmaps_api_key'] : null;

				$lazy_google_dynamic_map_script = "
				function webamatorDynamicLoadGoogleMap() {
				var url = 'https://maps.googleapis.com/maps/api/js?key=".$api_key."&callback=initMap".$map_lang."';
				var jsgmap = document.createElement('script');
				jsgmap.src = url;
				jsgmap.async = true;
				jsgmap.defer = true;
				jsgmap.id = 'dynamic_gmap';
				document.body.appendChild(jsgmap);
				}
				let mapsarr = [];";

				wp_enqueue_script(
					'lazy_google_map_script', 
					plugin_dir_url( __FILE__ ).'assets/js/ll-map.js', 
					false, 
					get_option('ll_gmaps_version'), 
					true
				);

				wp_add_inline_script( 
					'lazy_google_map_script', 
					$lazy_google_dynamic_map_script, 
					'before' 
				);



			}
			

			wp_add_inline_script( "lazy_google_map_script", $uluru, "before" );

			$return = '<div id="ll_gmap_canvas_id_'.$i.'" class="lazy-load-for-gmaps-wrap"'.$wrapper_style.'></div>';

			
		} else {
			$return = false;
		}
		
		$i++;
		return $return;

	}
	add_shortcode ('lazy_google_map', 'wa_shortcode_lazy_load_google_map');



	function lazy_load_google_maps_options_page(){
	?>
		<div class="wrap">
			
			<h2 id="title"><?php _e( 'Settings for Google Maps', 'lazy-load-for-gmaps' ) ?></h2>

			<form action="options.php" method="POST">
			<?php
				settings_fields( 'option_group' );
				do_settings_sections( 'll_gmaps_settings_page' ); 
				submit_button();
			?>
			</form>

			<h2><?php _e('Shortcode (example):', 'lazy-load-for-gmaps') ?></h2>
		
			<p>[lazy_google_map latitude="50.4505584" longitude="30.5210754"]</p>
			
			<h2><?php _e('Attributes for shortcode:', 'lazy-load-for-gmaps') ?></h2>
			
			<table class="form-table" role="presentation">
				<tr>
					<th scope="row">latitude</th>
					<td><?php _e('Latitude, should be in format', 'lazy-load-for-gmaps') ?> <b>latitude="50.4505584"</b>. <b style="color:red"><?php _e('Required attribute', 'lazy-load-for-gmaps') ?></b></td>
				</tr>
				<tr>
					<th scope="row">longitude</th>
					<td><?php _e('Longitude, should be in format', 'lazy-load-for-gmaps') ?> <b>latitude="30.5210754"</b>. <b style="color:red"><?php _e('Required attribute', 'lazy-load-for-gmaps') ?></b></td>
				</tr>
				<tr>
					<th scope="row">zoom</th>
					<td><?php _e('Zoom, can be in format', 'lazy-load-for-gmaps') ?> <b>zoom="15"</b>. <?php _e('Zoom levels: 1 - World, 5 - Landmass/Continent, 10 - City, 15 - Streets, 20 - Buildings.', 'lazy-load-for-gmaps') ?></td>
				</tr>
				<tr>
					<th scope="row">width</th>
					<td><?php _e('Width of block of map, can be in format', 'lazy-load-for-gmaps') ?> <b>width="800"</b>.</td>
				</tr>
				<tr>
					<th scope="row">height</th>
					<td><?php _e('Height of block of map, can be in format', 'lazy-load-for-gmaps') ?> <b>height="600"</b>.</td>
				</tr>
				<tr>
					<th scope="row">language</th>
					<td><?php _e('Language for Google Maps, can be in format', 'lazy-load-for-gmaps') ?> <b>language="en"</b>.</td>
				</tr>

			</table>

		</div>
	<?php
	}




	function ll_gmaps_plugin_settings(){
		
		//$option_group, $option_name, $sanitize_callback
		register_setting( 
			'option_group',
			'll_gmaps_settings',
			'll_gmaps_sanitize_callback'
			);

		//$id, $title, $callback, $page
		add_settings_section(
			'll_gmaps_settings_section', 
			__('Options:', 'lazy-load-for-gmaps'), 
			'', 
			'll_gmaps_settings_page' 
			); 

		// $id, $title, $callback, $page, $section, $args
		add_settings_field(
			'gmaps_api_key_field', 
			__('Google Maps API key', 'lazy-load-for-gmaps'),
			'get_ll_gmaps_api_key_value', 
			'll_gmaps_settings_page', 
			'll_gmaps_settings_section' 
			);

	}


	function get_ll_gmaps_api_key_value(){
		$val = get_option('ll_gmaps_settings');
		$val = isset( $val['ll_gmaps_api_key'] ) ? $val['ll_gmaps_api_key'] : null;
		?>
		<label><input type="text" name="ll_gmaps_settings[ll_gmaps_api_key]" value="<?php echo esc_attr( $val ) ?>" /> <?php _e('Enter your API key. If you don\'t have an API key, you can get it', 'lazy-load-for-gmaps') ?> <a href="https://developers.google.com/maps/documentation/embed/get-api-key" target="_blank"><?php _e('here', 'lazy-load-for-gmaps') ?></a>.</label>
		<?php
	}


	function ll_gmaps_sanitize_callback( $options ){

		foreach( $options as $name => $val ){
			if( $name == 'll_gmaps_api_key' )
				$val = strip_tags( $val );
			}

		return $options;
	}


?>