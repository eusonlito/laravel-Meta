<?php
namespace Eusonlito\LaravelMeta\Tags;

class Tag extends TagAbstract
{
    protected static $custom = ['image'];
    protected static $available = ['title'];

    public static function tagDefault($key, $value)
    {
        if (in_array($key, self::$available, true)) {
            return '<'.$key.'>'.$value.'</'.$key.'>';
        }
    }

    public static function tagImage($key, $value)
    {
        return '<link rel="image_src" href="'.$value.'" />';
    }
}
