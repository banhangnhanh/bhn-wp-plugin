<?php

namespace Banhangnhanh\BhnWpPlugin\ServiceProviders;

use Banhangnhanh\BhnWpPlugin\Utilities\HasInstance;
use WC_REST_Products_Controller;
use WP_Error;

class UuidServiceProvider extends BaseServiceProvider
{
  use HasInstance;

  public function register()
  {
    add_action('save_post', [$this, 'add_uuid_when_create_post'], 10, 3);
    add_filter('woocommerce_rest_prepare_product_object', [$this, 'add_uuid_to_product_object'], 10, 3);
    add_filter('woocommerce_rest_prepare_product_variation_object', [$this, 'add_uuid_to_product_object'], 10, 3);
    add_action('rest_api_init', [$this, 'register_custom_woocommerce_product_route'], 10, 3);
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

    if (!$uuid) {
      global $wpdb;
      $uuid = $wpdb->get_var($wpdb->prepare("SELECT uuid FROM {$wpdb->prefix}posts WHERE ID = %d", $product->get_id()));

      update_post_meta($product->get_id(), '_uuid', $uuid);
    }

    if (!empty($uuid)) {
      $response->data['uuid'] = $uuid;
    }

    return $response;
  }

  public function get_product_by_id_or_uuid($request)
  {
    global $wpdb;

    $idOrUuid = sanitize_text_field($request['id']);

    if (is_numeric($idOrUuid)) {
      $productId = (int) $idOrUuid;
    } else {
      $productId = $wpdb->get_var($wpdb->prepare(
        "SELECT ID FROM {$wpdb->prefix}posts WHERE uuid = %s",
        $idOrUuid
      ));
    }

    if (!$productId) {
      return new WP_Error('not_found', 'Product not found', array('status' => 404));
    }

    $controller = new WC_REST_Products_Controller();

    return $controller->get_item(['id' => $productId]);
  }

  public function register_custom_woocommerce_product_route()
  {
    $controller = new WC_REST_Products_Controller();

    register_rest_route('wc/v3', '/products/(?P<id>[a-zA-Z0-9-]+)', array(
      'methods'  => 'GET',
      'callback' => [$this, 'get_product_by_id_or_uuid'],
      'permission_callback' => function ($request) use ($controller) {
        return $controller->get_item_permissions_check($request);
      },
    ));
  }
}
