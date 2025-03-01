<?php

namespace Banhangnhanh\BhnWpPlugin\ServiceProviders;

use Banhangnhanh\BhnWpPlugin\Utilities\HasInstance;

class UuidServiceProvider extends BaseServiceProvider
{
  use HasInstance;

  public function register()
  {
    add_action('save_post', [$this, 'add_uuid_when_create_post'], 10, 3);
    add_filter('woocommerce_rest_prepare_product_object', [$this, 'add_uuid_to_product_object'], 10, 3);
  }

  public function add_uuid_when_create_post($postId, $post, $isUpdate)
  {
    if ($isUpdate) {
      return;
    }

    global $wpdb;

    $table = $wpdb->prefix . 'posts';

    $uuid = sanitize_text_field($_POST['uuid'] ?? wp_generate_uuid4());

    $wpdb->update(
      $table,
      ['uuid' => $uuid],
      ['ID' => $postId]
    );

    update_post_meta($postId, '_uuid', $uuid);
  }

  public function add_uuid_to_product_object($response, $product)
  {
    $uuid = get_post_meta($product->get_id(), '_uuid', true);

    if (!empty($uuid)) {
      $response->data['uuid'] = $uuid;
    }

    return $response;
  }
}
