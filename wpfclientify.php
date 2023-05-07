<?php
/**
* @link              https://efraim.cat
* @since             1.0.0
* @package           Wpfclientify
*
* @wordpress-plugin
* Plugin Name:       WpfClientify
* Plugin URI:        https://github.com/Efraimcat/wpfclientify/
* Description:       Funcionalidades para funos.es Clientify
* Version:           1.0.8
* Author:            Efraim Bayarri
* Author URI:        https://efraim.cat
* License:           GPL-2.0+
* License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
* Text Domain:       wpfclientify
* Domain Path:       /languages
* Requires PHP: 	   7.4
* Requires at least: 5.9
* Tested up to: 	   6.2
* GitHub Plugin URI: https://github.com/Efraimcat/wpfclientify/
*/
if ( ! defined( 'WPINC' ) ) {
	die;
}

define( 'WPFCLIENTIFY_VERSION', '1.0.8' );

function activate_wpfclientify() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wpfclientify-activator.php';
	Wpfclientify_Activator::activate();
}
function deactivate_wpfclientify() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wpfclientify-deactivator.php';
	Wpfclientify_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_wpfclientify' );
register_deactivation_hook( __FILE__, 'deactivate_wpfclientify' );

require plugin_dir_path( __FILE__ ) . 'includes/class-wpfclientify.php';

function run_wpfclientify() {
	$plugin = new Wpfclientify();
	$plugin->run();
}
run_wpfclientify();
