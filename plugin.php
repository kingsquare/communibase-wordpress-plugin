<?php
/**
 * Plugin Name: Communibase
 * Description: Communibase API Support
 * Version: 0.0.1
 * Author: Kingsquare BV
 * Author URI: https://kingsquare.nl
 */
defined('ABSPATH') or die;
define('COMMUNIBASE_PLUGIN_FILE', __FILE__);
define('COMMUNIBASE_PLUGIN_DIR', trailingslashit(plugin_dir_path(__FILE__)));
define('COMMUNIBASE_PLUGIN_URL', trailingslashit(plugin_dir_url(__FILE__)));
define('COMMUNIBASE_LANG', 'communibase');
define('COMMUNIBASE_VERSION', '0.0.1');

require_once __DIR__ . '/vendor/autoload.php';

/**
 *
 */
require_once __DIR__ . '/lib/WP_Communibase_Connector.php';
if (is_admin()) {
  require_once __DIR__ . '/lib/WP_Communibase_SettingsPage.php';
  new WP_Communibase_SettingsPage();
} else {
  // non-admin enqueues, actions, and filters
}
