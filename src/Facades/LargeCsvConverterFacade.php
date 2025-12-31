<?php

namespace Noki\LargeCsvConverter\Facades;

use Illuminate\Support\Facades\Facade;

class LargeCsvConverterFacade extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'largecsvconverter';
    }
}
