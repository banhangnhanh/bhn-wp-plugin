<?php

namespace Banhangnhanh\BhnWpPlugin\MerchantRest;

use Banhangnhanh\BhnWpPlugin\Utilities\CurrentMerchantUser;
use WC_REST_Products_Controller;

class AuthController extends WC_REST_Products_Controller
{
  public static function routes()
  {
    $controller = new AuthController();

    add_action('rest_api_init', function () use ($controller) {
      register_rest_route('bhn-merchant/v1', '/auth/me', [
        'methods' => 'GET',
        'callback' => [$controller, 'me'],
        'permission_callback' => '__return_true',
      ]);
    });
  }

  public function me()
  {
    $currentMerchantUser = CurrentMerchantUser::instance()->getUser();

    return rest_ensure_response([
      'id' => $currentMerchantUser->id,
      'uuid' => $currentMerchantUser->uuid,
      'access_token' => $currentMerchantUser->access_token,
      'role' => $currentMerchantUser->role,

      'real_user' => [
        'id' => $currentMerchantUser->realUser->ID,
        'username' => $currentMerchantUser->realUser->user_login,
      ],

      'merchant' => [
        'id' => $currentMerchantUser->merchant->id,
        'uuid' => $currentMerchantUser->merchant->uuid,
      ]
    ]);
  }
}
