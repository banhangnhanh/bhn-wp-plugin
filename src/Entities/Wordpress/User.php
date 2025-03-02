<?php

namespace Banhangnhanh\BhnWpPlugin\Entities\Wordpress;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
  protected $table = 'users';

  protected $primaryKey = 'ID';

  protected $guarded = [
    //
  ];
}
