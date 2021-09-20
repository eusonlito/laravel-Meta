<?php
namespace Eusonlito\LaravelMeta\Tags;

class MetaProduct extends TagAbstract
{
    protected static $available = [
        'price', 'currency',
    ];

    public static function tagDefault($key, $values)
    {
        if (!is_array($values)) {
            return;
        }

        $html = '';

        foreach($values as $key => $value) {
            if (in_array($key, self::$available, true)) {
                $html .= '<meta property="'.self::propertyTag($key).'" content="'.$value.'" />';
            }    
        }

        return $html;
    }

    public static function propertyTag ($key)
    {
        $tag = 'product:';

        switch ($key) {
            case 'amount':
            case 'price':
                $tag .= 'price:amount';
                break;
            case 'currency':
                $tag .= 'price:currency';
                break; 
        } 

        return $tag;
    }
}
