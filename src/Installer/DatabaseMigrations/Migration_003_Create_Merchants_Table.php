<?php

namespace Banhangnhanh\BhnWpPlugin\Installer\DatabaseMigrations;

use Banhangnhanh\BhnWpPlugin\DB;

class Migration_003_Create_Merchants_Table extends Migration
{
  public static function up()
  {
    if (!DB::schema()->hasTable('bhn_merchants')) {
      DB::schema()->create('bhn_merchants', function ($table) {
        $table->id();
        $table->uuid('uuid')->unique();
        $table->bigInteger('user_id')->index();
        $table->string('phone_number')->nullable();
        $table->string('phone_verified_at')->nullable();
        $table->timestamps();
      });
    }

    if (!DB::schema()->hasTable('bhn_merchant_users')) {
      DB::schema()->create('bhn_merchant_users', function ($table) {
        $table->id();
        $table->uuid('uuid')->unique();
        $table->string('access_token')->nullable();
        $table->bigInteger('merchant_id')->index();
        $table->bigInteger('user_id')->index();
        $table->string('role')->index();
        $table->string('status')->nullable();
        $table->timestamps();
      });
    }
  }

  public static function down()
  {
    if (!DB::schema()->hasTable('bhn_merchants')) {
      DB::schema()->dropIfExists('bhn_merchants');
    }

    if (!DB::schema()->hasTable('bhn_merchant_users')) {
      DB::schema()->dropIfExists('bhn_merchant_users');
    }
  }
}
