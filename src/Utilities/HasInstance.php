<?php

namespace Banhangnhanh\BhnWpPlugin\Utilities;

trait HasInstance
{
  protected static $instance;

  public static function instance()
  {
    if (is_null(static::$instance)) {
      static::$instance = new static;
    }

    return static::$instance;
  }
}
