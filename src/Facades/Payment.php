<?php

namespace Devinweb\Payment\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Devinweb\Payment\Skeleton\SkeletonClass
 */
class Payment extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'payment';
    }
}
