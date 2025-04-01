<?php

namespace SaferMobility\LaravelGotenberg\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \SaferMobility\LaravelGotenberg\LaravelGotenberg
 */
class Gotenberg extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \SaferMobility\LaravelGotenberg\LaravelGotenberg::class;
    }
}
