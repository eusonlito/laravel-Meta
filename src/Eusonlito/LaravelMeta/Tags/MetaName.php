<?php
namespace Eusonlito\LaravelMeta\Tags;

class MetaName extends TagAbstract
{
    protected static $specials = ['canonical', 'product'];

    public static function tagDefault($key, $value)
    {
        if (!in_array($key, self::$specials, true)) {
            return '<meta name="'.$key.'" content="'.$value.'" />';
        }
    }
}
