<?php
/**
 * Class SampleTest
 *
 * @package Hello_Server
 */
require_once(__DIR__ . '/../hello-server.php'); //path to the main plugin file

/**
 * Sample test case.
 */
class SampleTest extends WP_UnitTestCase {

    public $plugin_slug = 'hello-server';

    public function setUp() {
        global $wpdb;
        $wpdb->suppress_errors = false;
        $wpdb->show_errors = true;
        $wpdb->db_connect();
        ini_set('display_errors', 1 );
        $this->clean_up_global_scope();
        $this->start_transaction();
    }

    public function tearDown() {
        global $wpdb;
        $wpdb->query( 'ROLLBACK' );
    }

    function clean_up_global_scope() {
        $_GET = array();
        $_POST = array();
        $this::flush_cache();
    }

    public static function flush_cache() {
        global $wp_object_cache;
        $wp_object_cache->group_ops = array();
        $wp_object_cache->stats = array();
        $wp_object_cache->memcache_debug = array();
        $wp_object_cache->cache = array();
        if ( method_exists( $wp_object_cache, '__remoteset' ) ) {
            $wp_object_cache->__remoteset();
        }
        wp_cache_flush();
    }

    function start_transaction() {
        global $wpdb;
        $wpdb->query( 'SET autocommit = 0;' );
        $wpdb->query( 'START TRANSACTION;' );
    }

	/**
	 * A simple ip test.
	 */
	function test_get_ip_function() {
        global $_SERVER;

        // Test localhost IP
        $_SERVER['SERVER_ADDR'] = '127.0.0.1';
        $serverIpInfo = get_server_ip();
        $this->assertEquals( '127.0.0.1', $serverIpInfo  );

        // Test public IP
        $_SERVER['SERVER_ADDR'] = '8.8.8.8';
        $serverIpInfo = get_server_ip();
        $this->assertEquals( '8.8.8.8', $serverIpInfo  );

        // Test localhost IP
        $_SERVER['SERVER_ADDR'] = '64.128.56.42';
        $serverIpInfo = get_server_ip();
        $this->assertEquals( '64.128.56.42', $serverIpInfo  );
	}

    /**
	 * A simple hello function test.
	 */
	function test_hello_main_function() {
        global $_SERVER;

        // Test localhost IP
        $_SERVER['SERVER_ADDR'] = '127.0.0.1';
        $serverInfo = hello_server_get_info();
        $this->assertEquals( ['ip' => '127.0.0.1', 'hostname' => gethostname()], $serverInfo  );

        // Test public IP
        $_SERVER['SERVER_ADDR'] = '8.8.8.8';
        $serverInfo = hello_server_get_info();
        $this->assertEquals( ['ip' => '8.8.8.8', 'hostname' => gethostname()], $serverInfo  );

        // Test localhost IP
        $_SERVER['SERVER_ADDR'] = '64.128.56.42';
        $serverInfo = hello_server_get_info();
        $this->assertEquals( ['ip' => '64.128.56.42', 'hostname' => gethostname()], $serverInfo  );
	}

}
