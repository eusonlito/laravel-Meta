# Laravel Meta

[![Build Status](https://travis-ci.org/eusonlito/laravel-Meta.svg?branch=master)](https://travis-ci.org/eusonlito/laravel-Meta)
[![Latest Stable Version](https://poser.pugx.org/laravel/meta/v/stable.png)](https://packagist.org/packages/laravel/meta)
[![Total Downloads](https://poser.pugx.org/laravel/meta/downloads.png)](https://packagist.org/packages/laravel/meta)
[![License](https://poser.pugx.org/laravel/meta/license.png)](https://packagist.org/packages/laravel/meta)

With this package you can manage header Meta Tags from Laravel controllers.

## Installation

Begin by installing this package through Composer.

```js
{
    "require": {
        "laravel/meta": "master-dev"
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
php artisan config:publish laravel/packer
```

#### app/controllers/index.php

```php
class Home extends Controller {
    public function index()
    {
        Meta::title('This is default page title to complete section title');

        Meta::meta('title', 'You are at home');
        Meta::meta('description', 'This is my home. Enjoy!');
        Meta::meta('image', 'images/facebook.php');
        Meta::meta('image', 'images/twitter.php');
        Meta::meta('image', 'images/linkedin.php');

        return View::make('html')->nest('body', 'index');
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

        <meta property="og:site_name" content="My site" />
        <meta property="og:url" content="<?= Request::url(); ?>" />
        <meta property="og:locale" content="en_EN" />

        <?= Meta::tag('title'); ?>
        <?= Meta::tag('description'); ?>
        <?= Meta::tag('image'); ?>
    </head>

    <body>
        ...
    </body>
</html>
