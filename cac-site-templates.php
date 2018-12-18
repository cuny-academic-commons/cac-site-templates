<?php
/**
 * Plugin Name: site-template-info — CGB Gutenberg Block Plugin
 * Plugin URI: https://github.com/ahmadawais/create-guten-block/
 * Description: site-template-info — is a Gutenberg plugin created via create-guten-block.
 * Author: mrahmadawais, maedahbatool
 * Author URI: https://AhmadAwais.com/
 * Version: 1.0.0
 * License: GPL2+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 *
 * @package CGB
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'CAC_SITE_TEMPLATES_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'CAC_SITE_TEMPLATES_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

require __DIR__ . '/autoload.php';

/**
 * Shorthand function to fetch our CAC Site Templates instance.
 *
 * @since 0.1.0
 */
function cac_site_templates() {
	return \CAC\SiteTemplates\App::get_instance();
}

add_action( 'plugins_loaded', function() {
	cac_site_templates()->init();
} );
