<?php
/**
 * Plugin Name: Hello Server by Liquid Web
 * Plugin URI: https://wordpress.org/plugins/hello-server
 * Description: This is a simple plugin that tells you what server your WordPress is running on. It's super useful for when you run WordPress in a clustered, or mulit-server, environment.
 * Author: Dan Pock (Liquid Web)
 * Author URI: https://www.liquidweb.com
 * Version: 1.2.1
 * License: GPL-2.0
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain: hello-server
 * Requires WP: 4.4
 * Domain Path: languages
 * @package Hello_Server
 */

/*
	Copyright (c) 2017 Dan Pock (Liquid Web) <dpock@liquidweb.com>

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
 * @package    Hello_Server
 * @version    1.2.1
 * @author     Dan Pock <dpock@liquidweb.com>
 * @copyright  2017 Liquid Web
 * @license    http://www.gnu.org/licenses/gpl-2.0.txt  GPL-2.0
 * @link       https://github.com/liquidweb/hello-server
 */


/**
 * Load our textdomain for internationalization support.
 *
 * @return void
 */
function lw_load_plugins_textdomain() {
	load_plugin_textdomain( 'hello-server', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
}
add_action( 'plugins_loaded', 'lw_load_plugins_textdomain' );

/**
 * Pull our server info for display use.
 *
 * @return array $info  The IP and hostname being displayed.
 */
function lw_hello_server_get_info() {

	// Bail if we don't have a server address.
	if ( empty( $_SERVER['SERVER_ADDR'] ) ) {
		return false;
	}

	// Set our transient key for use later.
	$cache_key  = 'hello_server_info_cache_' . sanitize_text_field( wp_unslash( $_SERVER['SERVER_ADDR'] ) );

	// If we don't want the cache'd version, delete the transient first.
	if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
		delete_transient( $cache_key );
	}

	// It wasn't there, so regenerate the data and save the transient.
	if ( false === $data = get_transient( $cache_key )  ) {

		// Call the global WPDB class to get our version.
		global $wpdb;

		// Get our software name with a fallback.
		$svsoft = ! empty( $_SERVER['SERVER_SOFTWARE'] ) ? sanitize_text_field( wp_unslash( $_SERVER['SERVER_SOFTWARE'] ) ) : __( 'unknown', 'hello-server' );

		// Build my PHP version string.
		$phpver = absint( PHP_MAJOR_VERSION ) . '.' . absint( PHP_MINOR_VERSION ) . '.' . absint( PHP_RELEASE_VERSION );

		// Set up our array of info.
		$data   = array(
			'address'   => lw_get_server_ip(),
			'software'  => $svsoft,
			'hostname'  => gethostname(),
			'phpvers'   => $phpver,
			'dbvers'    => $wpdb->db_version(),
		);

		// Store our data in the transient.
		set_transient( $cache_key, $data, '', DAY_IN_SECONDS );
	}

	// Return our array of data.
	return $data;
}

/**
 * Get our server IP address by checking various PHP vars.
 *
 * @return string
 */
function lw_get_server_ip() {

	// Bail if we don't have a server address.
	if ( empty( $_SERVER['SERVER_ADDR'] ) ) {
		return false;
	}

	// Fetch our server address.
	$server_ip  = sanitize_text_field( wp_unslash( $_SERVER['SERVER_ADDR'] ) );

	// If we're on a local, do some additional checks.
	if ( '127.0.0.1' === $server_ip ) {

		// Get our hostname data.
		$res_ip   = gethostbyname( gethostname() );

		// Run a validation check on our IP to make sure it's legit.
		if ( filter_var( $res_ip, FILTER_VALIDATE_IP ) === true ) {
			return $res_ip;
		}
	}

	// Return our IP.
	return $server_ip;
}

/**
 * Include a small bit of CSS to display and position the icon.
 *
 * @return void
 */
function lw_add_admin_bar_css() {

	// Bail if current user doesnt have cap.
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	// Echo out the CSS.
	echo '
	<style>

		li#wp-admin-bar-lw-hello-server {
			display: block;
		}

		li#wp-admin-bar-lw-hello-server .ab-item.ab-empty-item .ab-lw-icon {}

		li#wp-admin-bar-lw-hello-server .ab-item.ab-empty-item .ab-lw-icon:before {
			content: "\f115";
			top: 2px;
		}

		li#wp-admin-bar-lw-hello-server .lw-admin-child-menu .ab-item.ab-empty-item span.ab-lw-inner-data {
			color: #fff;
		}

		@media screen and ( max-width: 782px ) {

			li#wp-admin-bar-lw-hello-server {
				display: block !important;
			}
		}

	</style>
	';
}
add_action( 'wp_head', 'lw_add_admin_bar_css' );
add_action( 'admin_head', 'lw_add_admin_bar_css' );

/**
 * Display our server info in the admin bar.
 *
 * @param  WP_Admin_Bar $wp_admin_bar  The global WP_Admin_Bar object.
 *
 * @return void
 */
function lw_load_hello_server( WP_Admin_Bar $wp_admin_bar ) {

	// Bail if current user doesnt have cap.
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	// Fetch our server info, and bail if we have none.
	if ( false === $info = lw_hello_server_get_info() ) {
		return;
	}

	// Add a parent item.
	$wp_admin_bar->add_node(
		array(
			'id'    => 'lw-hello-server',
			'title' => '<span class="ab-icon ab-lw-icon"></span><span class="ab-label ab-lw-label">' . esc_html__( 'Hello Server', 'hello-server' ) . '</span>',
			'meta'  => array(
				'title' => esc_html__( 'View the current server info.', 'hello-server' ),
				'class' => 'lw-admin-parent-menu',
			),
		)
	);

	// Add the host server info.
	$wp_admin_bar->add_node(
		array(
			'id'        => 'lw-host-server',
			'title'     => '<span class="ab-lw-inner-label">' . esc_html__( "Hello, I'm:", 'hello-server' ) . ' </span><span class="ab-lw-inner-data" itemprop="server-name">' . esc_html( $info['hostname'] ) . '</span>',
			'position'  => 0,
			'parent'    => 'lw-hello-server',
			'meta'      => array(
				'title' => esc_html__( 'View the current server hostname.', 'hello-server' ),
				'class' => 'lw-admin-child-menu',
			),
		)
	);

	// Add the server IP info.
	$wp_admin_bar->add_node(
		array(
			'id'        => 'lw-ip-address',
			'title'     => '<span class="ab-lw-inner-label">' . esc_html__( 'IP Address:', 'hello-server' ) . ' </span><span class="ab-lw-inner-data" itemprop="ip-address">' . esc_html( $info['address'] ) . '</span>',
			'position'  => 1,
			'parent'    => 'lw-hello-server',
			'meta'      => array(
				'title' => esc_html__( 'View the current server IP address.', 'hello-server' ),
				'class' => 'lw-admin-child-menu',
			),
		)
	);

	// Add the PHP software info.
	$wp_admin_bar->add_node(
		array(
			'id'        => 'lw-php-version',
			'title'     => '<span class="ab-lw-inner-label">' . esc_html__( 'PHP Version:', 'hello-server' ) . ' </span><span class="ab-lw-inner-data" itemprop="php-version">' . esc_html( $info['phpvers'] ) . '</span>',
			'position'  => 2,
			'parent'    => 'lw-hello-server',
			'meta'      => array(
				'title' => esc_html__( 'View the current PHP version.', 'hello-server' ),
				'class' => 'lw-admin-child-menu',
			),
		)
	);

	// Add the database version info.
	$wp_admin_bar->add_node(
		array(
			'id'        => 'lw-database-version',
			'title'     => '<span class="ab-lw-inner-label">' . esc_html__( 'DB Version:', 'hello-server' ) . ' </span><span class="ab-lw-inner-data" itemprop="database-version">' . esc_html( $info['dbvers'] ) . '</span>',
			'position'  => 3,
			'parent'    => 'lw-hello-server',
			'meta'      => array(
				'title' => esc_html__( 'View the current database version.', 'hello-server' ),
				'class' => 'lw-admin-child-menu',
			),
		)
	);

	// Add the server software info.
	$wp_admin_bar->add_node(
		array(
			'id'        => 'lw-server-software',
			'title'     => '<span class="ab-lw-inner-label">' . esc_html__( 'Software:', 'hello-server' ) . ' </span><span class="ab-lw-inner-data" itemprop="server-software">' . esc_html( $info['software'] ) . '</span>',
			'position'  => 4,
			'parent'    => 'lw-hello-server',
			'meta'      => array(
				'title' => esc_html__( 'View the current server software.', 'hello-server' ),
				'class' => 'lw-admin-child-menu',
			),
		)
	);
}

// Now we set that function up to execute when the admin_notices action is called.
add_action( 'admin_bar_menu', 'lw_load_hello_server', 999 );
