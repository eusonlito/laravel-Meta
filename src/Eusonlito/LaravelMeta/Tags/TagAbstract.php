<?php
namespace Eusonlito\LaravelMeta\Tags;

abstract class TagAbstract implements TagInterface
{
    protected static $custom = [];

    public static function tag($key, $value)
    {
        if (in_array($key, static::$custom, true)) {
            $method = 'tag'.self::studly($key);
        } else {
            $method = 'tagDefault';
        }

        return static::$method($key, $value);
    }

    /**
     * @param string $string
     *
     * @return string
     */
    private static function studly($string)
    {
        return str_replace(' ', '', ucwords(str_replace(array('-', '_'), ' ', $string)));
    }
}
