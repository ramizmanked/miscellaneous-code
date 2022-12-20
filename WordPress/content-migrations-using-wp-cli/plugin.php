<?php
/*
Plugin Name: WP CLI Custom Migrations
Description: Custom WP-CLI script to migrate NWMLS content.
Author: Ramiz Manked
Author URI: https://ramizmanked.com/
*/

if ( ! defined( 'NWMLS_CUSTOM_MIGRATIONS_PATH' ) ) {
	define( 'NWMLS_CUSTOM_MIGRATIONS_PATH', plugin_dir_path( __FILE__ ) );
}

if ( ! defined( 'NWMLS_CUSTOM_MIGRATIONS_CONTENT_PATH' ) ) {
	define( 'NWMLS_CUSTOM_MIGRATIONS_CONTENT_PATH', plugin_dir_path( __FILE__ ) . 'content/' );
}

define( 'NWMLS_CUSTOM_MIGRATIONS_URL', plugin_dir_url( __FILE__ ) );

if ( defined( 'WP_CLI' ) && WP_CLI ) {

	require_once NWMLS_CUSTOM_MIGRATIONS_PATH . 'inc/import-posts.php';

	WP_CLI::add_command( 'icm-import-posts', 'NWMLS\Import_Posts' );
}
