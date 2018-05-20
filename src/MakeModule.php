<?php

namespace Adeel\ModuleCreator;

use Illuminate\Console\Command;

/**
 * Class MakeModule
 *
 * @package Adeel\ModuleCreator\Console\Commands
 */
class MakeModule extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'module:make';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command to create basic module structure';

    /**
     * The default base namespace for the new module.
     *
     * @var string
     */
    protected $namespace;

    /** @var string */
    protected $moduleName;

    /** @var string */
    protected $modulesDir;

    /** @var string */
    protected $newModulePath;

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        $this->moduleName = $this->askModuleName();
        $this->modulesDir = $this->askModuleDirectory();
        $this->namespace  = $this->askModuleNamespace();

        $this->createDirectories();

        $this->createController();
        $this->createRoutes();
        $this->createHelpers();
        $this->createModels();

        $this->info(sprintf('Module %s successfully added.', $this->moduleName));
    }

    /**
     * Asks the namespace for module
     *
     * @return string
     */
    protected function askModuleNamespace()
    {
        return $this->ask('What should be the base namespace for module? e.g. App\\Modules\\AccessControl', 'App\\Modules\\' . $this->moduleName);
    }

    /**
     * Gets the directory for the modules
     */
    protected function askModuleDirectory()
    {
        return $this->ask('Where should I put this module?', 'app/Modules');
    }

    /**
     * Gets the module name from the user
     */
    protected function askModuleName()
    {
        do {
            if (!empty($this->moduleName)) {
                $this->error('Invalid module name. It must not have any spaces or special characters');
            }

            $this->moduleName = $this->ask('What should be the name of module? e.g. AccessControl or System etc', 'NewModule');
        } while (!preg_match('/^(?=_*[A-z]+)[A-z0-9_]+$/', $this->moduleName));

        return $this->moduleName;
    }

    /**
     * Create the directories for the files.
     *
     * @return void
     */
    protected function createDirectories()
    {
        if (!is_dir(base_path($this->modulesDir))) {
            mkdir(base_path($this->modulesDir), 0755, true);
        }

        $this->newModulePath = $this->modulesDir . '/' . $this->moduleName;

        if (!is_dir(base_path($this->newModulePath))) {
            mkdir(base_path($this->newModulePath), 0755, true);
        }

        if (!is_dir(base_path($this->newModulePath . '/Controllers'))) {
            mkdir(base_path($this->newModulePath . '/Controllers'), 0755, true);
        }

        if (!is_dir(base_path($this->newModulePath . '/Models'))) {
            mkdir(base_path($this->newModulePath . '/Models'), 0755, true);
        }

        if (!is_dir(base_path($this->newModulePath . '/Helpers'))) {
            mkdir(base_path($this->newModulePath . '/Helpers'), 0755, true);
        }
    }

    /**
     * Compiles the IndexController stub.
     *
     * @return void
     */
    protected function createController()
    {
        $this->info('Creating controller');

        $controllerStub = file_get_contents(__DIR__ . '/stubs/make/controllers/IndexController.stub');

        $controllerStub = str_replace('{{module_name}}', $this->moduleName, $controllerStub);
        $controllerStub = str_replace('{{namespace}}', $this->namespace, $controllerStub);

        file_put_contents($this->newModulePath . '/Controllers/IndexController.php', $controllerStub);
    }

    /**
     * Creates the routes file in the newly created module
     */
    private function createRoutes()
    {
        $this->info('Adding routes');

        $routeStub = file_get_contents(__DIR__ . '/stubs/make/routes.stub');

        $routeStub = str_replace('{{module_name}}', $this->moduleName, $routeStub);
        $routeStub = str_replace('{{namespace}}', $this->namespace, $routeStub);

        file_put_contents($this->newModulePath . '/routes.php', $routeStub);
    }

    /**
     * Creates the helpers file in the newly created module
     */
    private function createHelpers()
    {
        $this->info('Adding the helper class');

        $helperStub = file_get_contents(__DIR__ . '/stubs/make/helpers/Helper.stub');

        $helperStub = str_replace('{{module_name}}', $this->moduleName, $helperStub);
        $helperStub = str_replace('{{namespace}}', $this->namespace, $helperStub);

        file_put_contents($this->newModulePath . '/Helpers/Helper.php', $helperStub);
    }

    /**
     * Creates the models file in the newly created module
     */
    private function createModels()
    {
        $this->info('Creating a model');

        $modelStub = file_get_contents(__DIR__ . '/stubs/make/models/model.stub');

        $modelStub = str_replace('{{module_name}}', $this->moduleName, $modelStub);
        $modelStub = str_replace('{{namespace}}', $this->namespace, $modelStub);

        file_put_contents($this->newModulePath . '/Models/' . $this->moduleName . '.php', $modelStub);
    }
}
