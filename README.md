# Laravel 4.2 Meta

[![Build Status](https://travis-ci.org/eusonlito/laravel-Meta.svg?branch=master)](https://travis-ci.org/eusonlito/laravel-Meta)
[![Latest Stable Version](https://poser.pugx.org/laravel/meta/v/stable.png)](https://packagist.org/packages/laravel/meta)
[![Total Downloads](https://poser.pugx.org/laravel/meta/downloads.png)](https://packagist.org/packages/laravel/meta)
[![License](https://poser.pugx.org/laravel/meta/license.png)](https://packagist.org/packages/laravel/meta)

With this package you can manage header Meta Tags from Laravel controllers.

If you want a Laravel >= 4.3 compatible version, please use `master` branch.

## Installation

Begin by installing this package through Composer.

```js
{
    "require": {
        "eusonlito/laravel-meta": "4.2.x-dev"
    }
}
```

### Laravel installation

```php

// app/config/app.php

'providers' => [
    '...',
    'Laravel\Meta\MetaServiceProvider',
];
```

When you've added the ```MetaServiceProvider``` an extra ```Meta``` facade is available.
You can use this Facade anywhere in your application

Publish the config file:

```
php artisan config:publish eusonlito/laravel-meta
```

#### app/controllers/homeController.php

```php
class Home extends Controller {
    public function __construct()
    {
        # Default title
        Meta::title('This is default page title to complete section title');

        # Default robots
        Meta::meta('robots', 'index,follow');
    }

    public function index()
    {
        # Section description
        Meta::meta('title', 'You are at home');
        Meta::meta('description', 'This is my home. Enjoy!');
        Meta::meta('image', asset('images/home-logo.png'));

        return View::make('html')->nest('body', 'index');
    }

    public function detail()
    {
        # Section description
        Meta::meta('title', 'This is a detail page');
        Meta::meta('description', 'All about this detail page');
        Meta::meta('image', asset('images/detail-logo.png'));

        return View::make('html')->nest('body', 'detail');
    }

    public function private()
    {
        # Section description
        Meta::meta('title', 'Private Area');
        Meta::meta('description', 'You shall not pass!');
        Meta::meta('image', asset('images/locked-logo.png'));

        # Custom robots for this section
        Meta::meta('robots', 'noindex,nofollow');

        return View::make('html')->nest('body', 'private');
    }
}
```

#### app/views/html.php

```php
<html>
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="content-type" content="text/html; charset=utf-8" />

        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <meta name="author" content="Lito - lito@eordes.com" />

        <title><?= Meta::meta('title'); ?></title>

        <?= Meta::tagMetaName('robots'); ?>

        <?= Meta::tagMetaProperty('site_name', 'My site'); ?>
        <?= Meta::tagMetaProperty('url', Request::url()); ?>
        <?= Meta::tagMetaProperty('locale', 'en_EN'); ?>

        <?= Meta::tag('title'); ?>
        <?= Meta::tag('description'); ?>
        <?= Meta::tag('image'); ?>

        # Set default share picture after custom section pictures
        <?= Meta::tag('image', asset('images/default-logo.png')); ?>
    </head>

    <body>
        ...
    </body>
</html>
```

### Config

```php
return array(

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

    'image_limit' => 5
);
```

### Using Meta outside Laravel

#### Controller

```php
require (__DIR__.'/vendor/autoload.php');

// Check default settings
$config = require (__DIR__.'/src/config/config.php');

$Meta = new Laravel\Meta\Meta($config);

# Default title
$Meta->title('This is default page title to complete section title');

# Default robots
$Meta->meta('robots', 'index,follow');

# Section description
$Meta->meta('title', 'This is a detail page');
$Meta->meta('description', 'All about this detail page');
$Meta->meta('image', '/images/detail-logo.png');
```

#### Template

```php
<title><?= $Meta->meta('title'); ?></title>

<?= $Meta->tagMetaName('robots'); ?>

<?= $Meta->tagMetaProperty('site_name', 'My site'); ?>
<?= $Meta->tagMetaProperty('url', getenv('REQUEST_URI')); ?>
<?= $Meta->tagMetaProperty('locale', 'en_EN'); ?>

<?= $Meta->tag('title'); ?>
<?= $Meta->tag('description'); ?>
<?= $Meta->tag('image'); ?>

# Set default share picture after custom section pictures
<?= $Meta->tag('image', '/images/default-logo.png'); ?>
```
