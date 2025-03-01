<?php

namespace Banhangnhanh\BhnWpPlugin\Entities;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class MerchantUser extends Model
{
  use HasUuids;

  protected $table = 'bhn_merchant_users';

  protected $guarded = [
    //
  ];

  public function uniqueIds(): array
  {
    return ['uuid'];
  }

  public function merchant()
  {
    return $this->belongsTo(Merchant::class);
  }

  public function realUser()
  {
    return $this->belongsTo(Wordpress\User::class, 'user_id', 'ID');
  }
}
