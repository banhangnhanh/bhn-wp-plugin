<?php

namespace Banhangnhanh\BhnWpPlugin\Utilities;

use Illuminate\Support\Str;

class MerchantUserAccessTokenManager
{
  /**
   * Chỉ đơn giản là một string đảm bảo sự unique và khó đoán
   */
  public static function generate()
  {
    return Str::uuid();
  }
}
