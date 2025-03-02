<?php

namespace Banhangnhanh\BhnWpPlugin\ServiceProviders;

use Banhangnhanh\BhnWpPlugin\MerchantRest\ProductController;
use Banhangnhanh\BhnWpPlugin\Utilities\HasInstance;

class MerchantRestServiceProvider extends BaseServiceProvider
{
  use HasInstance;

  public function register()
  {
    ProductController::routes();
  }
}
