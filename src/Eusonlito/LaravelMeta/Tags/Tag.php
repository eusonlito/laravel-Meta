<?php
namespace Eusonlito\LaravelMeta\Tags;

class Tag extends TagAbstract
{
    protected static $custom = ['image', 'canonical'];
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

    public static function tagCanonical($key, $value)
    {
        return '<link rel="canonical" href="'.$value.'" />';
    }
}
