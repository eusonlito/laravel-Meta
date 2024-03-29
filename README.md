# HTML Meta Tags management package available for Laravel >= 5 (Including 10)

[![Build Status](https://travis-ci.org/eusonlito/laravel-Meta.svg?branch=master)](https://travis-ci.org/eusonlito/laravel-Meta)
[![Latest Stable Version](https://poser.pugx.org/eusonlito/laravel-meta/v/stable.png)](https://packagist.org/packages/eusonlito/laravel-meta)
[![Total Downloads](https://poser.pugx.org/eusonlito/laravel-meta/downloads.png)](https://packagist.org/packages/eusonlito/laravel-meta)
[![License](https://poser.pugx.org/eusonlito/laravel-meta/license.png)](https://packagist.org/packages/eusonlito/laravel-meta)

With this package you can manage header Meta Tags from Laravel controllers.

If you want a Laravel <= 4.2 compatible version, please use `v4.2` branch.

## Installation

Begin by installing this package through Composer.

```js
{
    "require": {
        "eusonlito/laravel-meta": "3.1.*"
    }
}
```

### Laravel installation

```php

// config/app.php

'providers' => [
    '...',
    Eusonlito\LaravelMeta\MetaServiceProvider::class
];

'aliases' => [
    '...',
    'Meta'    => Eusonlito\LaravelMeta\Facade::class,
];
```

Now you have a ```Meta``` facade available.

Publish the config file:

```
php artisan vendor:publish --provider="Eusonlito\LaravelMeta\MetaServiceProvider"
```

#### app/Http/Controllers/Controller.php

```php
<?php namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesCommands;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;

use Meta;

abstract class Controller extends BaseController
{
    use DispatchesCommands, ValidatesRequests;

    public function __construct()
    {
        # Default title
        Meta::title('This is default page title to complete section title');

        # Default robots
        Meta::set('robots', 'index,follow');

        # Default image
        Meta::set('image', asset('images/logo.png'));
    }
}
```

#### app/Http/Controllers/HomeController.php

```php
<?php namespace App\Http\Controllers;

use Meta;

class HomeController extends Controller
{
    public function index()
    {
        # Section description
        Meta::set('title', 'You are at home');
        Meta::set('description', 'This is my home. Enjoy!');
        Meta::set('image', asset('images/home-logo.png'));

        return view('index');
    }

    public function detail()
    {
        # Section description
        Meta::set('title', 'This is a detail page');
        Meta::set('description', 'All about this detail page');

        # Remove previous images
        Meta::remove('image');

        # Add only this last image
        Meta::set('image', asset('images/detail-logo.png'));

        # Canonical URL
        Meta::set('canonical', 'http://example.com');

        return view('detail');
    }

    public function private()
    {
        # Section description
        Meta::set('title', 'Private Area');
        Meta::set('description', 'You shall not pass!');
        Meta::set('image', asset('images/locked-logo.png'));

        # Custom robots for this section
        Meta::set('robots', 'noindex,nofollow');

        return view('private');
    }
}
```

#### resources/views/html.php

```php
<html>
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="content-type" content="text/html; charset=utf-8" />

        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <meta name="author" content="Lito - lito@eordes.com" />

        <title>{!! Meta::get('title') !!}</title>

        {!! Meta::tag('robots') !!}

        {!! Meta::tag('site_name', 'My site') !!}
        {!! Meta::tag('url', Request::url()); !!}
        {!! Meta::tag('locale', 'en_EN') !!}

        {!! Meta::tag('title') !!}
        {!! Meta::tag('description') !!}

        {!! Meta::tag('canonical') !!}

        {{-- Print custom section images and a default image after that --}}
        {!! Meta::tag('image', asset('images/default-logo.png')) !!}
    </head>

    <body>
        ...
    </body>
</html>
```

Or you can use Blade directives:

```php
<html>
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="content-type" content="text/html; charset=utf-8" />

        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <meta name="author" content="Lito - lito@eordes.com" />

        <title>{!! Meta::get('title') !!}</title>

        @meta('robots')

        @meta('site_name', 'My site')
        @meta('url', Request::url())
        @meta('locale', 'en_EN')

        @meta('title')
        @meta('description')

        @meta('canonical')

        {{-- Print custom section images and a default image after that --}}
        @meta('image', asset('images/default-logo.png'))

        {{-- Or use @metas to get all tags at once --}}
        @metas
        
    </head>

    <body>
        ...
    </body>
</html>
```

### MetaProduct / og:product
This will allow you to add product data to your meta data. See [Open Graph product object](https://developers.facebook.com/docs/payments/product/)
```php
// resources/views/html.php

<head>
    ...
    {!! Meta::tag('type') !!} // this is needed for Meta Product to change the og:type to og:product
    {!! Meta::tag('product') !!}
</head>

```

Add your product data from your controller

```php
<?php namespace App\Http\Controllers;

use Meta;

class ProductController extends Controller
{
    public function show()
    {
        # Add product meta
        Meta::set('product', [
            'price' => 100,
            'currency' => 'EUR',
        ]);
        
        # if multiple currencies just add more product metas
        Meta::set('product', [
            'price' => 100,
            'currency' => 'USD',
        ]);

        return view('index');
    }
}
```

### Config

```php
return [
    /*
    |--------------------------------------------------------------------------
    | Limit title meta tag length
    |--------------------------------------------------------------------------
    |
    | To best SEO implementation, limit tags.
    |
    */

    'title_limit' => 70,

    /*
    |--------------------------------------------------------------------------
    | Limit description meta tag length
    |--------------------------------------------------------------------------
    |
    | To best SEO implementation, limit tags.
    |
    */

    'description_limit' => 200,

    /*
    |--------------------------------------------------------------------------
    | Limit image meta tag quantity
    |--------------------------------------------------------------------------
    |
    | To best SEO implementation, limit tags.
    |
    */

    'image_limit' => 5,

    /*
    |--------------------------------------------------------------------------
    | Available Tag formats
    |--------------------------------------------------------------------------
    |
    | A list of tags formats to print with each definition
    |
    */

    'tags' => ['Tag', 'MetaName', 'MetaProperty', 'MetaProduct', 'TwitterCard'],
];
```

### Using Meta outside Laravel

#### Controller

```php
require __DIR__.'/vendor/autoload.php';

// Check default settings
$config = require __DIR__.'/src/config/config.php';

$Meta = new Eusonlito\LaravelMeta\Meta($config);

# Default title
$Meta->title('This is default page title to complete section title');

# Default robots
$Meta->set('robots', 'index,follow');

# Section description
$Meta->set('title', 'This is a detail page');
$Meta->set('description', 'All about this detail page');
$Meta->set('image', '/images/detail-logo.png');

# Canonical URL
$Meta->set('canonical', 'http://example.com');
```

#### Template

```php
<title><?= $Meta->get('title'); ?></title>

<?= $Meta->tag('robots'); ?>

<?= $Meta->tag('site_name', 'My site'); ?>
<?= $Meta->tag('url', getenv('REQUEST_URI')); ?>
<?= $Meta->tag('locale', 'en_EN'); ?>

<?= $Meta->tag('title'); ?>
<?= $Meta->tag('description'); ?>

<?= $Meta->tag('canonical'); ?>

# Print custom section image and a default image after that
<?= $Meta->tag('image', '/images/default-logo.png'); ?>
```

#### Updates from 2.*

* ``Meta::meta('title', 'Section Title')`` > ``Meta::set('title', 'Section Title')``
* ``Meta::meta('title')`` > ``Meta::get('title')``
* ``Meta::tagMetaName('title')`` > ``Meta::tag('title')``
* ``Meta::tagMetaProperty('title')`` > ``Meta::tag('title')``
