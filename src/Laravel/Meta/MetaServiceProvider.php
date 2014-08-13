<?php
namespace Laravel\Meta;

use Config;

use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\ServiceProvider;

class MetaServiceProvider extends ServiceProvider
{
    /**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
    protected $defer = false;

    /**
	 * Bootstrap the application events.
	 *
	 * @return void
	 */
    public function boot()
    {
        $this->package('laravel/meta');
        AliasLoader::getInstance()->alias('Meta', 'Laravel\Meta\Facades\Meta');
    }

    /**
	 * Register the service provider.
	 *
	 * @return void
	 */
    public function register()
    {
        $this->app['Meta'] = $this->app->share(function () {
            return new Meta($this->config());
        });
    }

    /**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
    public function provides()
    {
        return ['meta'];
    }

    /**
     * Get the base settings from config file
     *
     * @return array
     */
    public function config()
    {
        return Config::get('meta::config');
    }
}
