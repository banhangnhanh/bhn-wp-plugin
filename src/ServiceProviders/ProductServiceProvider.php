<?php

namespace Banhangnhanh\BhnWpPlugin\ServiceProviders;

use Banhangnhanh\BhnWpPlugin\Entities\Product;
use Banhangnhanh\BhnWpPlugin\Utilities\CurrentMerchantUser;
use Banhangnhanh\BhnWpPlugin\Utilities\HasInstance;
use Illuminate\Support\Str;

class ProductServiceProvider extends BaseServiceProvider
{
  use HasInstance;

  public function register()
  {
    add_action('woocommerce_new_product', [$this, 'create_btn_product'], 10, 3);
    // add_filter('wc_product_has_unique_sku', [$this, 'check_unique_sku'], 10, 2);
  }

  public function create_btn_product($productId)
  {
    $currentMerchantUser = CurrentMerchantUser::instance()->getUser();

    if (!$currentMerchantUser) {
      return;
    }

    $productData = [
      'product_id' => $productId,
      'merchant_id' => $currentMerchantUser->merchant_id,
      'merchant_user_id' => $currentMerchantUser->id,
    ];

    $requestData = bhn_get_request_data();

    $uuid = $requestData['uuid'] ?? Str::uuid();

    if (!empty($uuid)) {
      $productData['uuid'] = $uuid;
    }

    Product::create($productData);
  }

  // public function check_unique_sku($productId, $sku)
  // {
  //   return true;
  // }
}
