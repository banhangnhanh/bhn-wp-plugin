<?php

namespace Banhangnhanh\BhnWpPlugin\ServiceProviders;

use Banhangnhanh\BhnWpPlugin\Cli\CrawlMuaDayProductCommand;
use Banhangnhanh\BhnWpPlugin\Utilities\HasInstance;
use WP_CLI;

class CliServiceProvider extends BaseServiceProvider
{
  use HasInstance;

  public function register()
  {
    if (!class_exists(WP_CLI::class)) {
      return;
    }

    WP_CLI::add_command('muaday_product', CrawlMuaDayProductCommand::class);
  }
}
