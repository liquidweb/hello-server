<?php

/**
 * @package Hello_Server
 * @version 1.2.0
 */
/*
Plugin Name: Hello Server by Liquid Web
Plugin URI: https://wordpress.org/plugins/hello-server
License:           GPL-2.0
License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
Author: Dan Pock (Liquid Web)
Author URI: https://www.liquidweb.com
Description: This is just a plugin that tells you what server your WordPress is on. It's super useful for when you run WordPress in a cluster.
Version: 1.2.0
Text Domain: hello-server
Domain Path: /languages/
*/

/*  Copyright (c) 2017 Dan Pock (Liquid Web) <dpock@liquidweb.com>

	All rights reserved.
	Hello Server by Liquid Web is distributed under the GNU General Public License, Version 2,
	June 1991. Copyright (C) 1989, 1991 Free Software Foundation, Inc., 51 Franklin
	St, Fifth Floor, Boston, MA 02110, USA
	THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
	ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
	WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
	DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR
	ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
	(INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
	LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON
	ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
	(INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
	SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
*/

/**
 * Hello Server by Liquid Web
 *
 * Say hello to your server and maybe it will say it back.
 * With this WordPress plugin you can easily see what server your current
 * web request was processed on. If you run your WordPress in a cluster style
 * setup then this will simplify tracking server requests.
 *
 * PHP version 5.6-7.1
 *
 *
 * @package Hello_Server
 * @author     Dan Pock <dpock@liquidweb.com>
 * @copyright  2017 Liquid Web
 * @license    http://www.gnu.org/licenses/gpl-2.0.txt  GPL-2.0
 * @link       https://github.com/liquidweb/hello-server
 */

function hello_server_get_info() {
	$info = get_transient('hello_server_info_cache_'.$_SERVER["SERVER_ADDR"]);
	if (false === $info) {
		// It wasn't there, so regenerate the data and save the transient
		$info = [
			'ip' => get_server_ip(),
			'hostname' => gethostname()
		];
		set_transient('hello_server_info_cache_'.$_SERVER["SERVER_ADDR"], $info, 24 * HOUR_IN_SECONDS);
	}
	return $info;
}

function get_server_ip() {
	$serverIp =  $_SERVER["SERVER_ADDR"];
	if ($serverIp === '127.0.0.1') {
	  $resIp = gethostbyname(gethostname());
	  if (filter_var($resIp, FILTER_VALIDATE_IP) === true) {
		  return $resIp;
	  }
	}
	return $serverIp;
}

// This just echoes the chosen line, we'll position it later
function hello_server( $wp_admin_bar ) {
	$serverInfo = hello_server_get_info();
	$mainNode = [
	  'id' => 'lw_hello_server',
	  'title' => __("Hello, Server", "hello-server"),
	  'meta' => ['title' => __('View the current server info.', "hello-server"), 'class' => 'menupop']

	];
	$hostNode = [
	  'id' => 'lw_host_server',
      'title' => "Hello, I'm: <span itemprop='server-name'>".$serverInfo['hostname'] . "</span>!",
	  'meta' => ['title' => 'View the current server hostname.'],
	  'parent' => 'lw_hello_server'
	];
	$ipNode = [
	  'id' => 'lw_server_ip',
	  'title' => 'My IP is: '.$serverInfo['ip'],
	  'meta' => ['title' => 'The current servers IP Address.'],
	  'parent' => 'lw_hello_server'
	];
	$wp_admin_bar->add_node( $mainNode );
	$wp_admin_bar->add_node( $hostNode );
	$wp_admin_bar->add_node( $ipNode );
}

// Now we set that function up to execute when the admin_notices action is called
add_action( 'admin_bar_menu', 'hello_server', 999 );

// Internationalization Support
function load_plugins_textdomain()
{
	load_plugin_textdomain(
		'hello-server',
		false,
		dirname(dirname(plugin_basename(__FILE__))) . '/languages/'
	);
}
add_action('plugins_loaded', 'load_plugins_textdomain');
