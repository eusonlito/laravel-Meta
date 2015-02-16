<?php namespace Laravel\Meta;

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
        return 'meta';
    }
}
