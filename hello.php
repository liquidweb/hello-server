<?php
/**
 * @package Hello_Dolly
 * @version 1.6
 */
/*
Plugin Name: Hello Server
Plugin URI: https://wordpress.org/plugins/
Description: This is just a plugin that tells you what server your WordPress is on. It's super useful for when you run WordPress in a cluster.
Author: Dan Pock (Liquid Web)
Version: 1.0a
Author URI: https://github.com/liquidweb/
*/

function hello_server_get_info() {
	$info = [
	  'ip' => $_SERVER["SERVER_ADDR"],
	  'hostname' => gethostname()
	];
	return $info;
}

// This just echoes the chosen line, we'll position it later
function hello_server( $wp_admin_bar ) {
	$serverInfo = hello_server_get_info();
	$args = [
	  'id' => 'lw_server_name',
	  'title' => "Server: ".$serverInfo['hostname'],
	  'meta' => ['title' => 'View the current server hostname.']

	];
	$wp_admin_bar->add_node( $args );
}

// Now we set that function up to execute when the admin_notices action is called
add_action( 'admin_bar_menu', 'hello_server', 999 );

?>
