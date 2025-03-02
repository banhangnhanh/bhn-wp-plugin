<?php

namespace Banhangnhanh\BhnWpPlugin\MerchantRest;

use Banhangnhanh\BhnWpPlugin\Entities\Product;
use Banhangnhanh\BhnWpPlugin\Utilities\CurrentMerchantUser;
use WC_REST_Products_Controller;

class ProductController extends WC_REST_Products_Controller
{
  public static function routes()
  {
    $controller = new ProductController();

    add_action('rest_api_init', function () use ($controller) {
      register_rest_route('bhn-merchant/v1', '/products', [
        'methods' => 'GET',
        'callback' => [$controller, 'get_items'],
        'permission_callback' => '__return_true',
      ]);

      register_rest_route('bhn-merchant/v1', '/products', [
        'methods' => 'POST',
        'callback' => [$controller, 'create_item'],
        'permission_callback' => '__return_true',
      ]);
    });
  }

  public function get_items($request)
  {
    $currentMerchantUser = CurrentMerchantUser::instance()->getUser();

    $products = Product::query()
      ->with(['realProduct'])
      ->where('merchant_id', $currentMerchantUser->merchant_id)
      ->get()
      ->map(fn (Product $product) => $this->getResource($product))
      ->toArray();

    return rest_ensure_response($products);
  }

  private function getResource(Product $product)
  {
    $realProduct = wc_get_product($product->product_id);

    return [
      'id' => $product->id,
      'uuid' => $product->uuid,
      'name' => $realProduct->get_name(),
      'price' => $realProduct->get_price(),
    ];
  }
}
