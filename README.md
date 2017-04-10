# HTML Meta Tags management package available for Laravel 5/5.1/5.2/5.3/5.4

[![Build Status](https://travis-ci.org/eusonlito/laravel-Meta.svg?branch=master)](https://travis-ci.org/eusonlito/laravel-Meta)
[![Latest Stable Version](https://poser.pugx.org/laravel/meta/v/stable.png)](https://packagist.org/packages/laravel/meta)
[![Total Downloads](https://poser.pugx.org/laravel/meta/downloads.png)](https://packagist.org/packages/laravel/meta)
[![License](https://poser.pugx.org/laravel/meta/license.png)](https://packagist.org/packages/laravel/meta)

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
        Meta::set('image', asset('images/detail-logo.png'));

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

        {{-- Print custom section images and a default image after that --}}
        {!! Meta::tag('image', asset('images/default-logo.png')) !!}
    </head>

    <body>
        ...
    </body>
</html>
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

    'tags' => ['Tag', 'MetaName', 'MetaProperty', 'TwitterCard'],
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

# Print custom section image and a default image after that
<?= $Meta->tag('image', '/images/default-logo.png'); ?>
```

#### Updates from 2.*

* ``Meta::meta('title', 'Section Title')`` > ``Meta::set('title', 'Section Title')``
* ``Meta::meta('title')`` > ``Meta::get('title')``
* ``Meta::tagMetaName('title')`` > ``Meta::tag('title')``
* ``Meta::tagMetaProperty('title')`` > ``Meta::tag('title')``
