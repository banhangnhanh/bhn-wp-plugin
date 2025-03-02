<?php

namespace Banhangnhanh\BhnWpPlugin\Utilities;

use Banhangnhanh\BhnWpPlugin\Entities\MerchantUser;

class CurrentMerchantUser
{
  use HasInstance;

  private $user;

  public function getUser(): ?MerchantUser
  {
    return $this->user;
  }

  public function setUser(MerchantUser $user)
  {
    $this->user = $user;
  }
}
