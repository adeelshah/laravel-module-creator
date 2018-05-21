<?php

namespace Adeel\ModuleCreator;

use Illuminate\Support\ServiceProvider;

/**
 * Class ModuleCreatorServiceProvider
 *
 * @package Adeel\ModuleCreator
 */
class ModuleServiceProvider extends ModuleServiceProvider
{
    /** @var \Laravel\Lumen\Application */
    protected $app;

    /**
     * Bootstrap the application services
     *
     * @return void
     */
    public function boot()
    {
        $this->hookModules();

        // Publish the package configurations file
        $this->publishes([
            __DIR__ . '/../config/config.php' => base_path('config/config.php'),
        ], 'config');
    }

    /**
     * Register the application services
     *
     * @return void
     */
    public function register()
    {
        $this->loadConfigurations();

        // Registering module creator command
        $this->commands([
            MakeModule::class,
        ]);
    }

    /**
     * Loads the configurations from the config directory
     */
    protected function loadConfigurations()
    {
        // Make lumen pick the config/nitro.php file
        $this->app->configure('config');

        // Merge to put any defaults
        $this->mergeConfigFrom(__DIR__ . '/../config/config.php', 'config');

        // Load configurations
        $configFiles = array_filter(glob(base_path('config/*')), 'is_file');
        foreach ($configFiles as $configFile) {
            $configFile = array_last(explode('/', $configFile));
            $configFile = str_replace('.php', '', $configFile);

            $this->app->configure($configFile);
        }
    }

    /**
     * Loads the modules available in Modules directory
     */
    protected function hookModules()
    {
        // NOTE: Do not remove! It is being used in the routes files for modules
        $app    = $this->app;
        $router = $app->router;

        /** @var array $modulePaths */
        $modulePaths = config('config.modules_dir', []);

        foreach ($modulePaths as $modulePath) {
            $modules = glob($modulePath . '/*', GLOB_ONLYDIR);

            // For each of the services, include the route files
            foreach ($modules as $module) {
                // Load the routes for each of the modules
                if (!file_exists($module . '/routes.php')) {
                    continue;
                }

                require $module . '/routes.php';
            }
        }
    }
}
