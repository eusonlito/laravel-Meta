<?php
use Eusonlito\LaravelMeta\Meta;

class Tests extends PHPUnit_Framework_TestCase
{
    protected static $title;
    protected $Meta;

    public function setUp()
    {
        self::$title = self::text(20);

        $this->Meta = new Meta([
            'title_limit' => 70,
            'description_limit' => 200,
            'image_limit' => 5
        ]);
    }

    protected static function text($length)
    {
        $base = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $text = '';

        while (mb_strlen($text) < $length) {
            $text .= str_shuffle($base);
        }

        return mb_substr($text, 0, $length);
    }

    public function testMetaTitle()
    {
        $response = $this->Meta->meta('title', $text = self::text(50));

        $this->assertTrue($text === $response);

        $response = $this->Meta->meta('title', $text = self::text(80));

        $this->assertNotTrue($text, $response);
        $this->assertTrue(mb_strlen($response) === 70);
    }

    public function testMetaDescription()
    {
        $response = $this->Meta->meta('description', $text = self::text(50));

        $this->assertTrue($text === $response);

        $response = $this->Meta->meta('description', $text = self::text(250));

        $this->assertNotTrue($text === $response);
        $this->assertTrue(mb_strlen($response) === 200);
    }

    public function testMetaTitleWithTitle()
    {
        $response = $this->Meta->title(self::$title);

        $this->assertTrue(self::$title === $response);

        $response = $this->Meta->meta('title', $text = self::text(30));

        $this->assertTrue($text.' - '.self::$title === $response);

        $response = $this->Meta->meta('title', $text = self::text(80));

        $this->assertNotTrue($text.' - '.self::$title === $response);
        $this->assertTrue(mb_strlen($response) === 70);
    }

    public function testMetaImage()
    {
        $response = $this->Meta->meta('image', $text = self::text(30));

        $this->assertTrue($text === $response);

        $response = $this->Meta->meta('image', $text = self::text(150));

        $this->assertTrue($text === $response);

        for ($i = 0; $i < 5; $i++) {
            $response = $this->Meta->meta('image', $text =self::text(80));

            if ($i > 2) {
                $this->assertTrue($response === null);
            } else {
                $this->assertTrue($text === $response);
            }
        }

        $this->assertTrue(count($this->Meta->meta('image')) === 5);
    }

    public function testTagTitle()
    {
        $this->Meta->title(self::$title);
        $this->Meta->meta('title', $text = self::text(20));

        $tag = $this->Meta->tag('title');

        $this->assertTrue(mb_substr_count($tag, '<meta') === 2);
        $this->assertTrue(mb_substr_count($tag, 'title"') === 2);
        $this->assertTrue(mb_strstr($tag, self::$title) ? true : false);
        $this->assertTrue(mb_strstr($tag, $text) ? true : false);
    }

    public function testTagMetaNameTitle()
    {
        $this->Meta->title(self::$title);
        $this->Meta->meta('title', $text = self::text(20));

        $tag = $this->Meta->tagMetaName('title');

        $this->assertTrue(mb_substr_count($tag, '<meta') === 1);
        $this->assertTrue(mb_substr_count($tag, 'title"') === 1);
        $this->assertTrue(mb_strstr($tag, self::$title) ? true : false);
        $this->assertTrue(mb_strstr($tag, $text) ? true : false);
    }

    public function testTagMetaPropertyTitle()
    {
        $this->Meta->title(self::$title);
        $this->Meta->meta('title', $text = self::text(20));

        $tag = $this->Meta->tagMetaProperty('title');

        $this->assertTrue(mb_substr_count($tag, '<meta') === 1);
        $this->assertTrue(mb_substr_count($tag, 'title"') === 1);
        $this->assertTrue(mb_strstr($tag, self::$title) ? true : false);
        $this->assertTrue(mb_strstr($tag, $text) ? true : false);
    }

    public function testTagDescription()
    {
        $this->Meta->meta('description', $text = self::text(150));

        $tag = $this->Meta->tag('description');

        $this->assertTrue(mb_substr_count($tag, '<meta') === 2);
        $this->assertTrue(mb_substr_count($tag, 'description"') === 2);
        $this->assertTrue(mb_strstr($tag, $text) ? true : false);
    }

    public function testTagMetaNameDescription()
    {
        $this->Meta->meta('description', $text = self::text(150));

        $tag = $this->Meta->tagMetaName('description');

        $this->assertTrue(mb_substr_count($tag, '<meta') === 1);
        $this->assertTrue(mb_substr_count($tag, 'description"') === 1);
        $this->assertTrue(mb_strstr($tag, $text) ? true : false);
    }

    public function testTagMetaPropertyDescription()
    {
        $this->Meta->meta('description', $text = self::text(150));

        $tag = $this->Meta->tagMetaProperty('description');

        $this->assertTrue(mb_substr_count($tag, '<meta') === 1);
        $this->assertTrue(mb_substr_count($tag, 'description"') === 1);
        $this->assertTrue(mb_strstr($tag, $text) ? true : false);
    }

    public function testTagKeywords()
    {
        $this->Meta->meta('keywords', $text = self::text(150));

        $tag = $this->Meta->tag('keywords');

        $this->assertTrue(mb_substr_count($tag, '<meta') === 1);
        $this->assertTrue(mb_substr_count($tag, 'keywords"') === 1);
        $this->assertTrue(mb_strstr($tag, $text) ? true : false);
    }

    public function testTagMetaNameKeywords()
    {
        $this->Meta->meta('keywords', $text = self::text(150));

        $tag = $this->Meta->tagMetaName('keywords');

        $this->assertTrue(mb_substr_count($tag, '<meta') === 1);
        $this->assertTrue(mb_substr_count($tag, 'keywords"') === 1);
        $this->assertTrue(mb_strstr($tag, $text) ? true : false);
    }

    public function testTagMetaPropertyKeywords()
    {
        $this->Meta->meta('keywords', $text = self::text(150));

        $tag = $this->Meta->tagMetaProperty('keywords');

        $this->assertTrue(mb_substr_count($tag, '<meta') === 1);
        $this->assertTrue(mb_substr_count($tag, 'keywords"') === 1);
        $this->assertTrue(mb_strstr($tag, $text) ? true : false);
    }

    public function testTagImage()
    {
        for ($i = 0; $i < 10; $i++) {
            $this->Meta->meta('image', self::text(80));
        }

        $tag = $this->Meta->tag('image');

        $this->assertTrue(mb_substr_count($tag, '<meta') === 10);
        $this->assertTrue(mb_substr_count($tag, '<link') === 5);
        $this->assertTrue(mb_substr_count($tag, 'image') === 15);
    }
}
