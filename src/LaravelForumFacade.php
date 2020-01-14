<?php

namespace Vientodigital\LaravelForum;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Vientodigital\LaravelForum\Skeleton\SkeletonClass
 */
class LaravelForumFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'laravel-forum';
    }
}
