<?php
/**
 * Plugin Name: Communibase
 * Description: Communibase API Support
 * Version: 0.0.1
 * Author: Kingsquare BV
 * Author URI: https://kingsquare.nl
 * Plugin URI: https://github.com/AubreyHewes/communibase-wordpress-plugin
 */
defined('ABSPATH') or die;
define('COMMUNIBASE_PLUGIN_FILE', __FILE__);
define('COMMUNIBASE_PLUGIN_DIR', trailingslashit(plugin_dir_path(__FILE__)));
define('COMMUNIBASE_PLUGIN_URL', trailingslashit(plugin_dir_url(__FILE__)));
define('COMMUNIBASE_LANG', 'communibase');
define('COMMUNIBASE_VERSION', '0.0.1');

require_once __DIR__ . '/vendor/autoload.php';

// Add Connector class to Wordpress
require_once __DIR__ . '/lib/WP_Communibase_Connector.php';

// Add Admin Settings page
if (is_admin()) {

  // Init Settings Page
  require_once __DIR__ . '/lib/WP_Communibase_SettingsPage.php';
  new WP_Communibase_SettingsPage();
}

// Init API endpoints
require_once __DIR__ . '/lib/api/WP_Communibase_API.php';
WP_Communibase_API::init();

// non-admin enqueues, actions, and filters
