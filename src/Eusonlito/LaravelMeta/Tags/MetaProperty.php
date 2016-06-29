<?php
namespace Eusonlito\LaravelMeta\Tags;

class MetaProperty extends TagAbstract
{
    protected static $available = [
        'title', 'type', 'image', 'url', 'audio', 'description',
        'determiner', 'locale', 'site_name', 'video'
    ];

    public static function tagDefault($key, $value)
    {
        if (in_array($key, self::$available, true)) {
            return '<meta property="og:'.$key.'" content="'.$value.'" />';
        }
    }
}
