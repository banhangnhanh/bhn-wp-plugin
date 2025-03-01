<?php
/*
Plugin Name: BHN WP Plugin
Plugin URI: https://banhangnhanh.vn
Description: Plugin tùy chỉnh cho WordPress - Thêm tính năng đặc biệt vào trang web của bạn.
Version: 1.0.0
Author: BHN
Author URI: https://banhangnhanh.vn
*/

if (!defined('ABSPATH')) {
  exit;
}

include_once __DIR__ . '/vendor/autoload.php';
include_once __DIR__ . '/utilities.php';

$pluginInfo = get_file_data(__FILE__, [
  'plugin_name' => 'Plugin Name',
  'version' => 'Version',
]);

define('BHN_PLUGIN_NAME', $pluginInfo['plugin_name']);
define('BHN_VERSION', $pluginInfo['version']);
