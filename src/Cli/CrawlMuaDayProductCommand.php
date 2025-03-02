<?php

namespace Banhangnhanh\BhnWpPlugin\Cli;

use Banhangnhanh\BhnWpPlugin\Entities\MerchantUser;
use Banhangnhanh\BhnWpPlugin\Utilities\CurrentMerchantUser;
use WC_Data_Exception;
use WC_Product_Simple;
use WP_CLI;

/**
 * wp muaday_product crawl --muaday_user=0399958555 --merchant_user_id=3 --allow-root
 */
class CrawlMuaDayProductCommand
{
  public function crawl($arg, $options)
  {
    $merchantUserId = $options['merchant_user_id'] ?? null;
    $muadayUser = $options['muaday_user'] ?? null;

    if (!$merchantUserId || !$muadayUser) {
      WP_CLI::error('Missing required arguments');
      return;
    }

    $merchantUser = MerchantUser::find($merchantUserId);

    if (!$merchantUser) {
      WP_CLI::error('Merchant user not found');
      return;
    }

    CurrentMerchantUser::instance()->setUser($merchantUser);

    $data = $this->getProductList($muadayUser);

    if (!isset($data['rows'])) {
      WP_CLI::error('Không có dữ liệu');
      return;
    }

    foreach ($data['rows'] as $productData) {
      try {
        $product = new WC_Product_Simple();
        $product->set_name($productData['name']);
        $product->set_sku($productData['code']);
        $product->set_price((string) $productData['unit_price']);
        $product->set_regular_price((string) $productData['unit_price']);
        $product->set_manage_stock($productData['qty'] > 0);
        $product->set_stock_quantity($productData['qty']);
        $product->set_status('publish');
        $product->save();

        do_action('woocommerce_new_product', $product->get_id());

        WP_CLI::success('Product created: ' . $product->get_name());
      } catch (WC_Data_Exception $exception) {
        WP_CLI::log(sprintf('%s: %s', $productData['name'], $exception->getMessage()));
      }
    }
  }

  private function getProductList($muadayUser)
  {
    $response = wp_remote_get("https://muaday.vn/{$muadayUser}/items");
    $body = wp_remote_retrieve_body($response);
    $data = json_decode($body, true);

    return $data;
  }
}
