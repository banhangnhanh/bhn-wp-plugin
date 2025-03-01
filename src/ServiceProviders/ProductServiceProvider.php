<?php

namespace Banhangnhanh\BhnWpPlugin\ServiceProviders;

use Banhangnhanh\BhnWpPlugin\DB;
use Banhangnhanh\BhnWpPlugin\Utilities\HasInstance;
use WC_REST_Products_Controller;
use WP_Error;
use WP_REST_Request;
use WP_REST_Server;

class ProductServiceProvider extends BaseServiceProvider
{
  use HasInstance;

  public function register()
  {
    add_action('save_post', [$this, 'add_uuid_when_create_post'], 10, 3);
    add_filter('woocommerce_rest_prepare_product_object', [$this, 'add_uuid_to_product_object'], 10, 3);
    add_filter('woocommerce_rest_prepare_product_variation_object', [$this, 'add_uuid_to_product_object'], 10, 3);
    add_action('rest_api_init', [$this, 'register_custom_woocommerce_product_route'], 10, 3);
    add_action('rest_api_init', [$this, 'register_sync_product_route'], 10, 3);
  }

  public function add_uuid_when_create_post($postId, $post, $isUpdate)
  {
    if ($isUpdate) {
      return;
    }

    global $wpdb;

    $table = $wpdb->prefix . 'posts';
    $requestData = bhn_get_request_data();

    $uuid = sanitize_text_field($requestData['uuid'] ?? wp_generate_uuid4());

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

    $image = get_post_meta($product->get_id(), '_thumbnail_id', true);

    if (!empty($image)) {
      $response->data['image'] = wp_get_attachment_url($image);
    }

    return $response;
  }

  public function get_product_by_id_or_uuid($request)
  {
    $idOrUuid = sanitize_text_field($request['id']);

    if (is_numeric($idOrUuid)) {
      $productId = (int) $idOrUuid;
    } else {
      $result = DB::table('posts')->where('uuid', $idOrUuid)->first();
      $productId = $result ? $result->ID : null;
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
      'permission_callback' => [$controller, 'get_item_permissions_check'],
      'args' => [
        'context' => $controller->get_context_param(
          [
            'default' => 'view',
          ]
        ),
      ],
    ));
  }

  public function sync_product_by_uuid(WP_REST_Request $request)
  {
    $data = $request->get_json_params();
    $uuid = sanitize_text_field($data['uuid'] ?? '');

    if (empty($uuid)) {
      return new WP_Error('missing_uuid', 'UUID is required', array('status' => 400));
    }

    $controller = new WC_REST_Products_Controller();

    $productId = DB::table('posts')->where('uuid', $uuid)->value('ID');

    if ($productId) {
      $request->set_param('id', $productId);
      $response = $controller->update_item($request);
    } else {
      $response = $controller->create_item($request);
    }

    return rest_ensure_response($response->get_data());
  }

  public function register_sync_product_route()
  {
    $controller = new WC_REST_Products_Controller();

    register_rest_route('wc/v3', 'products/synchronization', array(
      'methods'  => 'POST',
      'callback' => [$this, 'sync_product_by_uuid'],
      'permission_callback' => [$controller, 'create_item_permissions_check'],
      'args' => $controller->get_endpoint_args_for_item_schema(WP_REST_Server::EDITABLE),
    ));
  }
}
