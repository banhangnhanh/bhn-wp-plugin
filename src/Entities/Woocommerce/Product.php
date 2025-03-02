<?php

namespace Banhangnhanh\BhnWpPlugin\Entities\Woocommerce;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
  protected $table = 'posts';

  protected $primaryKey = 'ID';

  protected $guarded = [
    //
  ];
}
