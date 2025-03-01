<?php

namespace Banhangnhanh\BhnWpPlugin\ServiceProviders;

abstract class BaseServiceProvider
{
    abstract public function register();

    abstract public static function instance();
}
