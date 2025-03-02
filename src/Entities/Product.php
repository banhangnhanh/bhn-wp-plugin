<?php

namespace Banhangnhanh\BhnWpPlugin\Entities;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
  use HasUuids;

  protected $table = 'bhn_products';

  protected $guarded = [
    //
  ];

  public function uniqueIds(): array
  {
    return ['uuid'];
  }

  public function realProduct()
  {
    return $this->belongsTo(Woocommerce\Product::class, 'product_id', 'ID');
  }
}
