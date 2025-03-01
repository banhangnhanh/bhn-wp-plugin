<?php

namespace Banhangnhanh\BhnWpPlugin\ServiceProviders;

use Automattic\WooCommerce\Utilities\FeaturesUtil;
use Banhangnhanh\BhnWpPlugin\Utilities\HasInstance;

class BhnServiceProvider extends BaseServiceProvider
{
  use HasInstance;

  protected $providers = [
    DatabaseServiceProvider::class,
    InstallerServiceProvider::class,
    UuidServiceProvider::class,
  ];

  public function register()
  {
    $this->markSupportHPOS();

    /**
     * Let's start the game
     */
    $this->start();
  }

  protected function start()
  {
    add_action('plugins_loaded', function () {
      foreach ($this->providers as $provider) {
        $provider::instance()->register();
      }
    }, 11);
  }

  protected function markSupportHPOS()
  {
    add_action('before_woocommerce_init', function () {
      if (class_exists(FeaturesUtil::class)) {
        FeaturesUtil::declare_compatibility('custom_order_tables', __FILE__, true);
      }
    });
  }
}
