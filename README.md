# Laravel Meta Tags

[![Latest Stable Version](https://poser.pugx.org/torann/laravel-meta-tags/v/stable.png)](https://packagist.org/packages/torann/laravel-meta-tags) [![Total Downloads](https://poser.pugx.org/torann/laravel-meta-tags/downloads.png)](https://packagist.org/packages/torann/laravel-meta-tags)

With this package you can manage header Meta Tags from Laravel controllers.

----------

## Installation

- [Laravel MetaTags on Packagist](https://packagist.org/packages/torann/laravel-meta-tags)
- [Laravel MetaTags on GitHub](https://github.com/Torann/laravel-meta-tags)

From the command line run

```
$ composer require torann/laravel-meta-tags
```

Once Meta Tags is installed you need to register the service provider with the application. Open up `config/app.php` and find the `providers` key.

```php
'providers' => array(

    Torann\LaravelMetaTags\MetaTagsServiceProvider::class,

)
```

Meta Tags also ships with a facade which provides the static syntax for creating collections. You can register the facade in the `aliases` key of your `config/app.php` file.

```php
'aliases' => array(

    'MetaTag'   => Torann\LaravelMetaTags\Facades\MetaTag::class,

)
```

### Publish the configurations

Run this on the command line from the root of your project:

```
$ php artisan vendor:publish --provider="Torann\LaravelMetaTags\MetaTagsServiceProvider"
```

A configuration file will be publish to `config/meta-tags.php`.

## Twitter Cards and OpenGraph

Various settings for these options can be found in the `config/meta-tags.php` file.

**Twitter Cards**

```php
{!! MetaTag::twitterCard() !!}
```

**OpenGraph**

```php
{!! MetaTag::openGraph() !!}
```


## Examples

#### app/Http/Controllers/Controller.php

```php
<?php 

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesCommands;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;

use MetaTag;

abstract class Controller extends BaseController 
{
    use DispatchesCommands, ValidatesRequests;

    public function __construct()
    {
        // Defaults
        MetaTag::set('description', 'Blog Wes Anderson bicycle rights, occupy Shoreditch gentrify keffiyeh.');
        MetaTag::set('image', asset('images/default-share-image.png'));
    }
}
```

#### app/Http/Controllers/HomeController.php

```php
<?php 

namespace App\Http\Controllers;

use MetaTag;

class HomeController extends Controller 
{
    public function index()
    {
        // Section description
        MetaTag::set('title', 'You are at home');
        MetaTag::set('description', 'This is my home. Enjoy!');

        return view('index');
    }

    public function detail()
    {
        // Section description
        MetaTag::set('title', 'This is a detail page');
        MetaTag::set('description', 'All about this detail page');
        MetaTag::set('image', asset('images/detail-logo.png'));

        return view('detail');
    }

    public function private()
    {
        // Section description
        MetaTag::set('title', 'Private Area');
        MetaTag::set('description', 'You shall not pass!');
        MetaTag::set('image', asset('images/locked-logo.png'));

        return view('private');
    }
}
```

#### resources/views/layouts/app.blade.php

```php
<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="content-type" content="text/html; charset=utf-8">

        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <title>{{ MetaTag::get('title') }}</title>

        {!! MetaTag::tag('description') !!}
        {!! MetaTag::tag('image') !!}
        
        {!! MetaTag::openGraph() !!}
        
        {!! MetaTag::twitterCard() !!}

        {{--Set default share picture after custom section pictures--}}
        {!! MetaTag::tag('image', asset('images/default-logo.png')) !!}
    </head>

    <body>
        @yield('content')
    </body>
</html>
```