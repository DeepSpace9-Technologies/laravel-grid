<?php namespace Nayjest\Grids;

use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class ServiceProvider extends BaseServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * This method required for backward compatibility with Laravel 4.
     * 
     * @deprecated
     * @return string
     */
    public function guessPackagePath()
    {
        return __DIR__;
    }

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        $pkg_path = dirname(__DIR__);
        $views_path = $pkg_path . '/resources/views';
        $configFileName = "grid_setting.php";
        $configFilePath = $pkg_path . '/config/grid_setting.php';

        # only for Laravel 4 & some of 5-dev
        if (version_compare(Application::VERSION, '5.0.0', '<')) {
            $this->package('nayjest/grids');
            $this->app['view']->addNamespace('grids', $views_path);
        } else {
            $this->loadViewsFrom($views_path, 'grids');
            $this->loadTranslationsFrom($pkg_path . '/resources/lang', 'grids');
            $this->publishes([
                $views_path => base_path('resources/views/nayjest/grids')
            ]);
            // Publish the config file
            $this->publishes([
                $configFilePath => config_path('grid_setting.php'),
            ], 'config');
        }

        // Check if config file is published in the project; otherwise, merge package config
        if (file_exists(config_path($configFileName))) {
            $this->app['config']->set('grid_setting', require config_path($configFileName));
        } else {
            $this->mergeConfigFrom($configFilePath, 'grid_setting');
        }

        if (!class_exists('Grids')) {
            class_alias('\\Nayjest\\Grids\\Grids', '\\Grids');
        }
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [];
    }
}
