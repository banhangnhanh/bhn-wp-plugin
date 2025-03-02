<?php

namespace Banhangnhanh\BhnWpPlugin\ServiceProviders;

use Banhangnhanh\BhnWpPlugin\Utilities\HasInstance;

class ProductServiceProviderV2 extends BaseServiceProvider
{
  use HasInstance;

  public function register()
  {
    add_action('save_post', [$this, 'add_uuid_when_create_post'], 10, 3);
  }

  public function add_uuid_when_create_post($postId, $post, $isUpdate)
  {
    if ($isUpdate) {
      return;
    }
  }
}
