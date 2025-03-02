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

      register_rest_route('bhn-merchant/v1', '/products/synchronization', [
        'methods' => 'POST',
        'callback' => [$controller, 'sync_item'],
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
      ->map(fn(Product $product) => $this->getResource($product))
      ->toArray();

    return rest_ensure_response($products);
  }

  public function sync_item(WP_REST_Request $request)
  {
    $data = $request->get_json_params();
    $uuid = sanitize_text_field($data['uuid'] ?? '');

    if (empty($uuid)) {
      $error = new WP_Error('missing_uuid', 'UUID is required', array('status' => 400));

      return rest_ensure_response($error);
    }

    $product = Product::query()->where('uuid', $uuid)->first();

    if ($product) {
      $request->set_param('id', $product->product_id);
      $this->update_item($request);
    } else {
      $this->create_item($request);
    }

    return rest_ensure_response($this->getResource($product));
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
