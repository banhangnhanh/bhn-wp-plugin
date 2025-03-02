<?php

namespace Banhangnhanh\BhnWpPlugin\MerchantRest;

use Banhangnhanh\BhnWpPlugin\Entities\MerchantUser;
use Banhangnhanh\BhnWpPlugin\Utilities\CurrentMerchantUser;
use Illuminate\Support\Str;
use WP_Error;

class AuthController
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

      register_rest_route('bhn-merchant/v1', '/auth/register', [
        'methods' => 'POST',
        'callback' => [$controller, 'register'],
        'permission_callback' => '__return_true',
      ]);
    });
  }

  public function register()
  {
    $requestData = bhn_get_request_data();
    $initAccessToken = $requestData['access_token'] ?? null;

    if (!$initAccessToken) {
      $error = new WP_Error(
        'invalid_access_token',
        'Access token is required',
        array('status' => 400)
      );

      return rest_ensure_response($error);
    }

    if (!Str::isUuid($initAccessToken)) {
      $error = new WP_Error(
        'invalid_access_token',
        'Access token must be a valid UUID',
        array('status' => 400)
      );

      return rest_ensure_response($error);
    }

    /**
     * Những thông tin này chỉ cần điền tạm vào cho phù hợp với rule của wordpress
     * Chứ thực chất thì không cần, nên cứ để random
     */
    wp_insert_user([
      'user_login' => $initAccessToken,
      'user_pass' => Str::random(10),
      'user_email' => Str::random(10) . '@banhangnhanh.vn',
      'first_name' => '',
      'last_name' => '',
      'role' => 'bhn_user',
    ]);

    $merchantUser = MerchantUser::where('access_token', $initAccessToken)->first();

    if (!$merchantUser) {
      $error = new WP_Error(
        'invalid_access_token',
        'Access token is invalid',
        array('status' => 400)
      );

      return rest_ensure_response($error);
    }

    return rest_ensure_response($this->getResource($merchantUser));
  }

  public function me()
  {
    $currentMerchantUser = CurrentMerchantUser::instance()->getUser();

    return rest_ensure_response($this->getResource($currentMerchantUser));
  }

  private function getResource(MerchantUser $merchantUser)
  {
    return [
      'id' => $merchantUser->id,
      'uuid' => $merchantUser->uuid,
      'access_token' => $merchantUser->access_token,
      'role' => $merchantUser->role,

      'real_user' => [
        'id' => $merchantUser->realUser->ID,
        'username' => $merchantUser->realUser->user_login,
        'access_token' => '',
      ],

      'merchant' => [
        'id' => $merchantUser->merchant->id,
        'uuid' => $merchantUser->merchant->uuid,
      ]
    ];
  }
}
