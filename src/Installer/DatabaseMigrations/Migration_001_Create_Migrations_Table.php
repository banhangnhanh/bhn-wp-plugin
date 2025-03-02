<?php

namespace Banhangnhanh\BhnWpPlugin\Installer\DatabaseMigrations;

use Banhangnhanh\BhnWpPlugin\DB;

class Migration_001_Create_Migrations_Table extends Migration
{
  public static function up()
  {
    if (!DB::schema()->hasTable('bhn_migrations')) {
      DB::schema()->create('bhn_migrations', function ($table) {
        $table->id();
        $table->string('migration');
        $table->string('since_version');
        $table->timestamps();
      });
    }
  }

  public static function down()
  {
    if (!DB::schema()->hasTable('bhn_migrations')) {
      DB::schema()->dropIfExists('bhn_migrations');
    }
  }
}
