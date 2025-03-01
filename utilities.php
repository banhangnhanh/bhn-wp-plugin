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
