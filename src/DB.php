<?php

namespace Banhangnhanh\BhnWpPlugin;

use Illuminate\Database\Capsule\Manager;

class DB
{
  public static function __callStatic($name, $arguments)
  {
    return Manager::$name(...$arguments);
  }
}
