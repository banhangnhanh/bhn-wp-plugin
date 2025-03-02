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
    add_action('save_post', [$this, 'create_btn_product'], 10, 3);
  }

  public function create_btn_product($postId, $post, $isUpdate)
  {
    if ($isUpdate) {
      return;
    }

    if ($post->post_type !== 'product') {
      return;
    }

    $currentMerchantUser = CurrentMerchantUser::instance()->getUser();

    $productData = [
      'id' => $postId,
      'product_id' => $postId,
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
}
