<?php

namespace Banhangnhanh\BhnWpPlugin\ServiceProviders;

use Banhangnhanh\BhnWpPlugin\Entities\Merchant;
use Banhangnhanh\BhnWpPlugin\Entities\MerchantUser;
use Banhangnhanh\BhnWpPlugin\Utilities\HasInstance;
use Illuminate\Support\Str;

class MerchantServiceProvider extends BaseServiceProvider
{
  use HasInstance;

  public function register()
  {
    add_action('init', [$this, 'register_bhn_user'], 10, 3);
    add_action('user_register', [$this, 'create_bhn_merchant'], 10, 3);
  }

  public function register_bhn_user()
  {
    /**
     * Role merchant có permissions nhu role shop manager
     */
    $shopManager = get_role('shop_manager');

    add_role('bhn_user', 'BHN user', $shopManager);
  }

  public function create_bhn_merchant($userId)
  {
    $user = get_userdata($userId);

    if (!in_array('bhn_user', $user->roles)) {
      return;
    }

    $userLogin = $user->user_login;

    $merchantData = [
      'user_id' => $userId,
    ];

    if (Str::isUuid($userLogin)) {
      $merchantData['uuid'] = $userLogin;
    }

    $merchant = Merchant::create($merchantData);

    MerchantUser::create([
      'merchant_id' => $merchant->id,

      /**
       * Init access token for merchant user is uuid of merchant
       */
      'access_token' => $merchant->uuid,
      'user_id' => $userId,
      'role' => 'owner',
    ]);
  }
}
