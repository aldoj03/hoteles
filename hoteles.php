<?php

/**
 * 
 *
 * @wordpress-plugin
 * Plugin Name:       Busqueda de hoteles
 * Description:       Plugin de busqueda de ofertas para habitaciones de hoteles
 * Version:           1.0.0
 * Author:            Seo Contenidos
 * Author URI:        http://seocontenidos.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'HOTELES_VERSION', '1.0.0' );
define('API_KEY', "625f4c71c0828f829bd1c878b4f6c3d6");
define('SECRET', "e805df16c7");
define('XML_API', 'http://xml.hotelresb2b.com/xml/listen_xml.jsp');

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-hoteles-activator.php
 */
function activate_hoteles() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-hoteles-activator.php';
	Hoteles_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-hoteles-deactivator.php
 */
function deactivate_plugin_name() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-hoteles-deactivator.php';
	Hoteles_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_hoteles' );
register_deactivation_hook( __FILE__, 'deactivate_hoteles' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-hoteles.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_plugin_name() {

	$plugin = new Hoteles();
	$plugin->run();

}
run_plugin_name();
