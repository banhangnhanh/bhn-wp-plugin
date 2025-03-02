<?php

namespace Banhangnhanh\BhnWpPlugin\Entities;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Merchant extends Model
{
  use HasUuids;

  protected $table = 'bhn_merchants';

  protected $guarded = [
    //
  ];

  public function uniqueIds(): array
  {
    return ['uuid'];
  }
}
