<?php
/**
 * @package Hello_Server
 * @version 1.0a
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
	$mainNode = [
	  'id' => 'lw_hello_server',
	  'title' => "Hello, Server",
	  'meta' => ['title' => 'View the current server info.', 'class' => 'menupop']

	];
	$hostNode = [
		'id' => 'lw_hello_server',
		'title' => "Server: ".$serverInfo['hostname'],
		'meta' => ['title' => 'View the current server hostname.'],
		'parent' => 'lw_hello_server'
	];
	$ipNode = [
	  'id' => 'lw_server_ip',
	  'title' => 'Server IP: '.$serverInfo['ip'],
		'meta' => ['title' => 'The current servers IP Address.'],
	  'parent' => 'lw_hello_server'
	];
	$wp_admin_bar->add_node( $mainNode );
	$wp_admin_bar->add_node( $hostNode );
	$wp_admin_bar->add_node( $ipNode );
}

// Now we set that function up to execute when the admin_notices action is called
add_action( 'admin_bar_menu', 'hello_server', 999 );

?>
