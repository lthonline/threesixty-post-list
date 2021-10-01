<?php
/**
 * Plugin Name:       Threesixty Post List
 * Description:       Custom plugin created for private use only.
 * Version:           1.0.0
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            The 360 Virtual Tours
 * Author URI:        https://www.the360virtualtours.in/
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       threesixty-post-list
 * Domain Path:       /languages
 */

// Make sure we don't expose any info if called directly
if ( !function_exists( 'add_action' ) ) {
	echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
	exit;
}

define( 'THREESIXTY_POST_LIST_VERSION', '1.0.0' );
define( 'THREESIXTY_POST_LIST_DB_VERSION', '1.0.0' );
define( 'THREESIXTY_POST_LIST___MINIMUM_WP_VERSION', '4.0' );
define( 'THREESIXTY_POST_LIST___PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

register_activation_hook( __FILE__, array( 'Threesixty_Post_List', 'plugin_activation' ) );
register_deactivation_hook( __FILE__, array( 'Threesixty_Post_List', 'plugin_deactivation' ) );
register_uninstall_hook(__FILE__, array( 'Threesixty_Post_List', 'plugin_delete' ));

add_action( 'init', array( 'Threesixty_Post_List', 'init' ) );


if ( is_admin() || ( defined( 'WP_CLI' ) && WP_CLI ) ) {
    require_once( THREESIXTY_POST_LIST___PLUGIN_DIR . 'class.threesixty-post-list-admin.php' );
    add_action( 'init', array( 'Threesixty_Post_List_Admin', 'init' ) );
}


require_once( THREESIXTY_POST_LIST___PLUGIN_DIR . 'class.threesixty-post-list.php' );
require_once( THREESIXTY_POST_LIST___PLUGIN_DIR . 'class.threesixty-utilities.php' );
require_once( THREESIXTY_POST_LIST___PLUGIN_DIR . 'class.threesixty-post-list-renderer.php' );