<?php
namespace Laravel\Meta\Facades;

use Illuminate\Support\Facades\Facade;

class Meta extends Facade
{
    /**
     * Name of the binding in the IoC container
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'Meta';
    }
}
