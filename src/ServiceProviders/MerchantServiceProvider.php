<?php

namespace Banhangnhanh\BhnWpPlugin\ServiceProviders;

use Banhangnhanh\BhnWpPlugin\Entities\Merchant;
use Banhangnhanh\BhnWpPlugin\Entities\MerchantUser;
use Banhangnhanh\BhnWpPlugin\Utilities\HasInstance;
use Banhangnhanh\BhnWpPlugin\Utilities\MerchantUserAccessTokenManager;

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
    error_log('init create_bhn_merchant');

    $user = get_userdata($userId);

    if (!in_array('bhn_user', $user->roles)) {
      error_log('stop because user is not bhn_user');
      return;
    }

    error_log('start create bhn_merchant');

    $merchant = Merchant::create([
      'user_id' => $userId,
    ]);

    MerchantUser::create([
      'merchant_id' => $merchant->id,
      'access_token' => MerchantUserAccessTokenManager::generate(),
      'user_id' => $userId,
      'role' => 'owner',
    ]);
  }
}
