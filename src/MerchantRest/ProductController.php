<?php

namespace Banhangnhanh\BhnWpPlugin\MerchantRest;

class ProductController
{
  public static function routes()
  {
    $controller = new ProductController();

    add_action('rest_api_init', function () use ($controller) {
      register_rest_route('bhn/v1', '/hello', [
        'methods' => 'GET',
        'callback' => [$controller, 'hello_world'],
        'permission_callback' => '__return_true',
      ]);
    });
  }

  public function hello_world()
  {
    return rest_ensure_response([
      'message' => 'welcome',
    ]);
  }
}
