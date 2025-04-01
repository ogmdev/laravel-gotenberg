<?php

namespace SaferMobility\LaravelGotenberg\Facades;

use Illuminate\Support\Facades\Facade;
use SaferMobility\LaravelGotenberg\FakePdfBuilder;
use SaferMobility\LaravelGotenberg\GotenbergFactory;

/**
 * @mixin \SaferMobility\LaravelGotenberg\PdfBuilder
 * @mixin \SaferMobility\LaravelGotenberg\FakePdfBuilder
 */
class Gotenberg extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return GotenbergFactory::class;
    }

    public static function fake()
    {
        $fake = new FakePdfBuilder;

        static::swap($fake);
    }
}
