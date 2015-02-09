<?php namespace Laravel\Meta;

use Config;
use Illuminate\Support\ServiceProvider;

class MetaServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../../config/config.php' => config_path('meta.php'),
        ], 'config');
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app['mutlucell'] = $this->app->share(function($app) {
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
        Config::get('meta::config');

        return Config::getItems()['meta::config'];
    }
}
