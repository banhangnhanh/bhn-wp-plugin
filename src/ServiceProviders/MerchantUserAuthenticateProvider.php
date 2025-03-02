<?php

namespace Banhangnhanh\BhnWpPlugin\ServiceProviders;

use Banhangnhanh\BhnWpPlugin\Entities\MerchantUser;
use Banhangnhanh\BhnWpPlugin\Utilities\CurrentMerchantUser;
use Banhangnhanh\BhnWpPlugin\Utilities\HasInstance;
use Illuminate\Support\Str;

class MerchantUserAuthenticateProvider extends BaseServiceProvider
{
  use HasInstance;

  private $basePath = '/wp-json/bhn-merchant/v1';

  private $ignoreUrls = [
    '/wp-json/bhn-merchant/v1/auth/register',
    '/wp-json/bhn-merchant/v1/auth/login',
  ];

  public function register()
  {
    add_action('rest_request_before_callbacks', [$this, 'init_current_merchant_user_from_request'], 10, 1);
  }

  public function init_current_merchant_user_from_request()
  {
    $currentUrl = $_SERVER['REQUEST_URI'];

    /**
     * Only apply to our own API
     */
    if (!str_starts_with($currentUrl, $this->basePath)) {
      return;
    }

    if (in_array($currentUrl, $this->ignoreUrls)) {
      return;
    }

    $headers = getallheaders();

    $merchantUserToken = isset($headers['Authorization']) ? $headers['Authorization'] : null;

    /**
     * Remove Bearer prefix
     */
    if (Str::startsWith($merchantUserToken, 'Bearer ')) {
      $merchantUserToken = Str::after($merchantUserToken, 'Bearer ');
    }

    if (!$merchantUserToken) {
      wp_send_json([
        'message' => 'Unauthorized (1)',
      ], 401);
    }

    $merchantUser = MerchantUser::where('access_token', $merchantUserToken)->first();

    if (!$merchantUser) {
      wp_send_json([
        'message' => 'Unauthorized (2)',
      ], 401);
      return;
    }

    CurrentMerchantUser::instance()->setUser($merchantUser);

    wp_set_current_user($merchantUser->user_id);
    wp_set_auth_cookie($merchantUser->user_id);
  }
}
