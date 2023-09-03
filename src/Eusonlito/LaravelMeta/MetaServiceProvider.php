<?php
namespace Eusonlito\LaravelMeta;

use Illuminate\Support\ServiceProvider;
use Illuminate\View\Compilers\BladeCompiler;

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
            __DIR__.'/../../config/config.php' => config_path('meta.php')
        ]);

        $this->addBladeDirectives();
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(Meta::class, function () {
            return new Meta(config('meta'));
        });

        $this->app->alias(Meta::class, 'meta');
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [Meta::class, 'meta'];
    }

    /**
     * Register blade directives
     *
     * @return void
     */
    protected function addBladeDirectives()
    {
        $this->app->afterResolving('blade.compiler', function (BladeCompiler $bladeCompiler) {
            $bladeCompiler->directive('meta', function ($arguments) {
                return "<?php echo Meta::tag($arguments); ?>";
            });

            $bladeCompiler->directive('metas', function ($arguments) {
                return "<?php echo Meta::tags($arguments); ?>";
            });
        });
    }
}
