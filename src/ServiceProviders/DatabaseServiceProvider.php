<?php

namespace Banhangnhanh\BhnWpPlugin\ServiceProviders;

use Illuminate\Database\Capsule\Manager as Capsule;
use Banhangnhanh\BhnWpPlugin\Utilities\HasInstance;

class DatabaseServiceProvider extends BaseServiceProvider
{
  use HasInstance;

  public function register()
  {
    global $wpdb;

    if (!$wpdb) {
      return;
    }

    $DB_HOST = $wpdb->dbhost;
    $DB_NAME = $wpdb->dbname;
    $DB_USER = $wpdb->dbuser;
    $DB_PASSWORD = $wpdb->dbpassword;
    $DB_CHARSET = $wpdb->charset;
    $DB_PREFIX = $wpdb->prefix;

    $capsule = new Capsule;
    $capsule->addConnection([
      'driver' => 'mysql',
      'host' => $DB_HOST,
      'database' => $DB_NAME,
      'username' => $DB_USER,
      'password' => $DB_PASSWORD,
      'charset' => $DB_CHARSET,
      'prefix' => $DB_PREFIX,
    ]);

    $capsule->setAsGlobal();
    $capsule->bootEloquent();
  }
}
