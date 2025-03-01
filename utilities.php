<?php

if (!defined('ABSPATH')) exit;

use Banhangnhanh\BhnWpPlugin\ServiceProviders\BhnServiceProvider;
use Carbon\Carbon;

if (!function_exists('bhn_now')) {
  function bhn_now()
  {
    return Carbon::createFromFormat('Y-m-d H:i:s', current_time('mysql'));
  }
}

if (!function_exists('bhn_get_request_data')) {
  function bhn_get_request_data()
  {
    if (isset($_POST) && !empty($_POST)) {
      return $_POST;
    }

    return json_decode(file_get_contents('php://input'), true);
  }
}

/**
 * -----------------------------------------------
 * Core use case functions
 * -----------------------------------------------
 */

if (!function_exists('BHN_INIT')) {
  function BHN_INIT()
  {
    return BhnServiceProvider::instance()->register();
  }
}
