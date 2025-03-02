<?php

namespace Banhangnhanh\BhnWpPlugin\ServiceProviders;

use Banhangnhanh\BhnWpPlugin\Entities\MerchantUser;
use Banhangnhanh\BhnWpPlugin\Utilities\CurrentMerchantUser;
use Banhangnhanh\BhnWpPlugin\Utilities\HasInstance;

class MerchantUserAuthenticateProvider extends BaseServiceProvider
{
  use HasInstance;

  public function register()
  {
    add_action('rest_request_before_callbacks', [$this, 'init_current_merchant_user_from_request'], 10, 1);
  }

  public function init_current_merchant_user_from_request()
  {
    $wpCurrentUser = wp_get_current_user();

    if (!$wpCurrentUser) {
      return;
    }

    $headers = getallheaders();

    $merchantUserToken = isset($headers['Merchant-User-Token']) ? $headers['Merchant-User-Token'] : null;

    if (!$merchantUserToken) {
      return;
    }

    $merchantUser = MerchantUser::where('access_token', $merchantUserToken)->first();

    if (!$merchantUser) {
      return;
    }

    $merchant = $merchantUser->merchant;

    $currentUserInMerchantUserList = $merchant->users()
      ->where('user_id', $wpCurrentUser->ID)
      ->exists();

    if (!$currentUserInMerchantUserList) {
      return;
    }

    CurrentMerchantUser::instance()->setUser($merchantUser);
  }
}
