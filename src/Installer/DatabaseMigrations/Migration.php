<?php

namespace Banhangnhanh\BhnWpPlugin\Installer\DatabaseMigrations;

use Illuminate\Database\Schema\Blueprint;
use Banhangnhanh\BhnWpPlugin\DB;

abstract class Migration
{
  abstract public static function up();

  abstract public static function down();

  public static function createIndexIfNotExist($tableName, $column, $indexName = null)
  {
    if (is_null($indexName)) {
      $indexName = $tableName . '_' . $column . '_index';
    }

    DB::schema()->table($tableName, function (Blueprint $table) use ($tableName, $column, $indexName) {
      $conn = DB::schema()->getConnection();
      $sm = $conn->getDoctrineSchemaManager();
      $doctrineTable = $sm->listTableDetails(DB::getTablePrefix() . $tableName);

      if (!$doctrineTable->hasIndex($indexName)) {
        $table->index($column, $indexName);
      }
    });
  }

  public static function dropIndexIfExist($tableName, $column, $indexName = null)
  {
    if (is_null($indexName)) {
      $indexName = $tableName . '_' . $column . '_index';
    }

    DB::schema()->table($tableName, function (Blueprint $table) use ($tableName, $indexName) {
      $conn = DB::schema()->getConnection();
      $sm = $conn->getDoctrineSchemaManager();
      $doctrineTable = $sm->listTableDetails(DB::getTablePrefix() . $tableName);

      if ($doctrineTable->hasIndex($indexName)) {
        $table->dropIndex($indexName);
      }
    });
  }
}
