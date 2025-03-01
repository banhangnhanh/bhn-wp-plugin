<?php

namespace Banhangnhanh\BhnWpPlugin\Installer\DatabaseMigrations;

use Banhangnhanh\BhnWpPlugin\DB;
use Illuminate\Database\Schema\Blueprint;

class Migration_002_Add_Uuid_Column extends Migration
{
  public static function up()
  {
    DB::schema()->table('posts', function (Blueprint $table) {
      $table->uuid('uuid')->unique()->nullable()->after('ID');
    });
  }

  public static function down()
  {
    DB::schema()->table('posts', function (Blueprint $table) {
      $table->dropColumn('uuid');
    });
  }
}
