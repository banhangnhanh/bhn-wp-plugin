<?php

namespace Banhangnhanh\BhnWpPlugin\ServiceProviders;

use Banhangnhanh\BhnWpPlugin\Installer\DatabaseMigrationManager;
use Banhangnhanh\BhnWpPlugin\Utilities\HasInstance;

class InstallerServiceProvider extends BaseServiceProvider
{
  use HasInstance;

  public function register()
  {
    DatabaseMigrationManager::install();
  }
}
