<?php
use Laravel\Meta\Meta;

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
        $base = ' 0123456789 abcdefghi jklmnopqrs tuvwxyzA BCDEFGH IJKLMNOPQ RSTUVWXYZ ';
        $text = '';

        while (strlen($text) < $length) {
            $text .= str_shuffle($base);
        }

        return substr($text, 0, $length);
    }

    public function testMetaTitle()
    {
        $response = $this->Meta->meta('title', $text = self::text(50));

        $this->assertEqual($text, $response);

        $response = $this->Meta->meta('title', $text = self::text(80));

        $this->assertNotEqual($text, $response);
        $this->assertTrue(strlen($response) === 70);
    }

    public function testMetaDescription()
    {
        $response = $this->Meta->meta('description', $text = self::text(50));

        $this->assertEqual($title, $response);

        $response = $this->Meta->meta('description', $text = self::text(250));

        $this->assertNotEqual($text, $response);
        $this->assertTrue(strlen($response) === 200);
    }

    public function testMetaTitleWithTitle()
    {
        $response = $this->Meta->title(self::$title);

        $this->assertEqual(' - '.self::$title, $response);

        $response = $this->Meta->meta('title', $text = self::text(30));

        $this->assertEqual($text.' - '.self::$title, $response);

        $response = $this->Meta->meta('title', $text = self::text(80));

        $this->assertNotEqual($text.' - '.self::$title, $response);
        $this->asserTrue(strlen($response) === 70);
    }

    public function testMetaImage()
    {
        $response = $this->Meta->meta('image', $text = self::text(30));

        $this->assertTrue(is_array($response));
        $this->assertTrue(count($response) === 1);
        $this->assertEqual($text, $response[0]);

        for ($i = 0; $i < 10; $i++) {
            $response = $this->Meta->meta('image', self::text(80));

            if ($i > 3) {
                $this->assertTrue($response === null);
            }
        }

        $this->assertTrue(count($response) === 5);
    }
}
