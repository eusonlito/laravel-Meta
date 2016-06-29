<?php
namespace Eusonlito\LaravelMeta\Tags;

class TwitterCard extends TagAbstract
{
    protected static $available = [
        'card', 'site', 'site:id', 'creator', 'creator:id',
        'description', 'title', 'image', 'image:alt', 'player',
        'player:width', 'player:height', 'player:stream',
        'app:name:iphone', 'app:id:iphone', 'app:url:iphone',
        'app:name:ipad', 'app:id:ipad', 'app:url:ipad',
        'app:name:googleplay', 'app:id:googleplay', 'app:url:googleplay'
    ];

    public static function tagDefault($key, $value)
    {
        if (in_array($key, self::$available, true)) {
            return '<meta name="twitter:'.$key.'" content="'.$value.'" />';
        }
    }
}
