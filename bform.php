<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://google.com
 * @since             1.0.0
 * @package           Bform
 *
 * @wordpress-plugin
 * Plugin Name:       BuilderForm
 * Plugin URI:        https://google.com
 * Description:       Constructor de Formularios para ULEAM-EP
 * Version:           1.0.0
 * Author:            Sr.Ralo
 * Author URI:        https://google.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       bform
 * Domain Path:       /languages
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
define( 'BFORM_VERSION', '1.0.0' );

$bform_autoload_path = plugin_dir_path( __FILE__ ) . 'vendor/autoload.php';
if ( file_exists( $bform_autoload_path ) ) {
	require_once $bform_autoload_path;
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-bform-activator.php
 */
function activate_bform() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-bform-activator.php';
	Bform_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-bform-deactivator.php
 */
function deactivate_bform() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-bform-deactivator.php';
	Bform_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_bform' );
register_deactivation_hook( __FILE__, 'deactivate_bform' );

/**
 * Try dependency bootstrap on admin requests when Dompdf is missing.
 *
 * @since    1.0.0
 */
function bform_maybe_bootstrap_dependencies() {
	if ( ! is_admin() || class_exists( '\\Dompdf\\Dompdf' ) ) {
		return;
	}

	$last_attempt = (int) get_option( 'bform_dependency_last_attempt', 0 );
	$cooldown = 12 * HOUR_IN_SECONDS;
	if ( $last_attempt > 0 && ( time() - $last_attempt ) < $cooldown ) {
		return;
	}

	update_option( 'bform_dependency_last_attempt', time(), false );

	require_once plugin_dir_path( __FILE__ ) . 'includes/class-bform-activator.php';
	Bform_Activator::ensure_dompdf_dependency();
}
add_action( 'admin_init', 'bform_maybe_bootstrap_dependencies' );

/**
 * Display dependency installation notice.
 *
 * @since    1.0.0
 */
function bform_render_dependency_notice() {
	if ( ! is_admin() || ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$message = get_option( 'bform_dependency_notice', '' );
	if ( ! is_string( $message ) || '' === trim( $message ) ) {
		return;
	}

	echo '<div class="notice notice-error"><p><strong>' . esc_html__( 'ULEAM Formularios:', 'bform' ) . '</strong> ' . esc_html( $message ) . '</p></div>';
}
add_action( 'admin_notices', 'bform_render_dependency_notice' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-bform.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_bform() {

	$plugin = new Bform();
	$plugin->run();

}
run_bform();
