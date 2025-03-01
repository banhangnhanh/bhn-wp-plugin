<?php

namespace Banhangnhanh\BhnWpPlugin\Installer;

use Banhangnhanh\BhnWpPlugin\DB;

class DatabaseMigrationManager
{
  protected string $command;

  public array $commands = [
    'up' => 'handleUp',
    'refresh' => 'handleRefresh',
    'install' => 'handleInstall',
  ];

  public function __construct(
    string $command = 'up'
  ) {
    $this->command = $command;
  }

  public static function run($command = 'up')
  {
    return (new static($command))->handle();
  }

  public static function install()
  {
    return (new static('install'))->handle();
  }

  public function handle()
  {
    if (isset($this->commands[$this->command])) {
      $this->{$this->commands[$this->command]}();
    }
  }

  protected function handleInstall()
  {
    $dbVersion = get_option('BHN_version', null);
    $firstRunMigrate = is_null($dbVersion);

    if ($dbVersion != BHN_VERSION) {
      $this->handleUp($firstRunMigrate);
    }

    if ($firstRunMigrate) {
      do_action('bhn_first_run_install');
    }

    update_option('BHN_version', BHN_VERSION);
  }

  protected function handleUp($firstRunMigrate = false)
  {
    $migrationFiles = $this->getMigrationFiles();
    $willMigrateFiles = $migrationFiles;

    if ($firstRunMigrate == false) {
      $migratedFiles = DB::table('bhn_migrations')->pluck('migration')->toArray();
      $willMigrateFiles = array_diff($migrationFiles, $migratedFiles);
    }

    foreach ($willMigrateFiles as $migration) {
      $class = DatabaseMigrations::class . "\\$migration";
      $class::up();
      $this->markAsMigrated($migration);
    }
  }

  protected function handleRefresh()
  {
    $migratedFiles = DB::table('bhn_migrations')
      ->latest()
      ->pluck('migration')
      ->toArray();

    foreach ($migratedFiles as $migration) {
      $class = DatabaseMigrations::class . "\\$migration";
      $class::down();
    }

    $this->handleUp();
  }

  protected function markAsMigrated($migration)
  {
    DB::table('bhn_migrations')->insert([
      'migration' => $migration,
      'since_version' => BHN_VERSION,
      'created_at' => bhn_now()->format('Y-m-d H:i:s'),
      'updated_at' => bhn_now()->format('Y-m-d H:i:s'),
    ]);
  }

  protected function getMigrationFiles()
  {
    $allFiles = glob(__DIR__ . '/DatabaseMigrations/*.php');

    $files = [];

    foreach ($allFiles as $file) {
      if (basename($file) != 'Migration.php') {
        $files[] = basename($file, '.php');
      }
    }

    return $files;
  }
}
