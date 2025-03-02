<?php

namespace Banhangnhanh\BhnWpPlugin\Installer\DatabaseMigrations;

use Banhangnhanh\BhnWpPlugin\DB;

class Migration_002_Create_Products_Table extends Migration
{
  public static function up()
  {
    if (!DB::schema()->hasTable('bhn_products')) {
      DB::schema()->create('bhn_products', function ($table) {
        $table->id();
        $table->uuid('uuid')->unique();
        $table->bigInteger('product_id')->index();
        $table->bigInteger('merchant_id')->index();
        $table->bigInteger('merchant_user_id')->index();
        $table->datetime('last_synced_at')->nullable();
        $table->timestamps();
      });
    }
  }

  public static function down()
  {
    if (!DB::schema()->hasTable('bhn_products')) {
      DB::schema()->dropIfExists('bhn_products');
    }
  }
}
