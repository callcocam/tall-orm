<?php
/**
* Created by Claudio Campos.
* User: callcocam@gmail.com, contato@sigasmart.com.br
* https://www.sigasmart.com.br
*/
namespace Tall\Cms\Providers;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Livewire\Commands\ComponentParser as CommandsComponentParser;
use Livewire\LivewireComponentsFinder ;
use Symfony\Component\Finder\Finder;
use Tall\Cms\Console\ComponentParser;
use Tall\Cms\Console\Commands\TallMakeCommand;
use Tall\Cms\LivewireComponentsFinder as LivewireLivewireComponentsFinder;

class CmsServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->register(RouteServiceProvider::class);
        $this->registerComponentAutoDiscovery();
       
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../../config/tall-cms.php','tall-cms'
        );

        $this->registerCommands();
        $this->publishViews();
        $this->configureDynamicComponent(cms_resources_path("views/components"));
        if(is_dir(resource_path("views/vendor/tall/cms/components"))){
            $this->configureDynamicComponent(resource_path("views/vendor/tall/cms/components"));
        }
        
    }
    protected function registerComponentAutoDiscovery()
    {
        // Rather than forcing users to register each individual component,
        // we will auto-detect the component's class based on its kebab-cased
        // alias. For instance: 'examples.foo' => App\Http\Livewire\Examples\Foo

        // We will generate a manifest file so we don't have to do the lookup every time.
        $defaultManifestPath = $this->app['livewire']->isRunningServerless()
            ? '/tmp/storage/bootstrap/cache/livewire-components.php'
            : app()->bootstrapPath('cache/livewire-components.php');


        $this->app->extend(LivewireComponentsFinder::class, function () use ($defaultManifestPath) {
        
            $namespaces[]=[
                'path'=>CommandsComponentParser::generatePathFromNamespace(config('livewire.class_namespace')),
                'namespace'=>'\\App',
                'search'=>app_path()
            ];
            $namespaces[]=[
                'path'=> ComponentParser::generatePathFromNamespace(cms_core_path("\\Tall\\Cms\\Http\\Livewire")),
                'namespace'=>'\\Tall\\Cms\\',
                'search'=>cms_core_path()
            ];
            return new LivewireLivewireComponentsFinder(
                new Filesystem,$defaultManifestPath,
                $namespaces
            );
        });
    }

    protected function registerCommands()
    {
        if (! $this->app->runningInConsole()) return;

        $this->commands([
            TallMakeCommand::class, // make:livewire
        ]);
    }

    private function publishViews(): void
    {
        $pathViews = __DIR__ . '/../../resources/views';
        $this->loadViewsFrom($pathViews, 'tall');
        if(is_dir(resource_path('views/vendor/tall')))
        {
            $pathViews = resource_path('views/vendor/tall');
            $this->loadViewsFrom($pathViews, 'tall');
        }
    }

     /**
     * Configure the component for the application.
     *
     * @return void
     */
    public function configureDynamicComponent($path,$search=".blade.php")
    {
       foreach ((new Finder)->in($path)->files()->name('*.blade.php') as $component) {                   
            $componentPath = $component->getRealPath();     
            $namespace = Str::beforeLast($componentPath, $search);
            $namespace = Str::afterLast($namespace, 'components/');
            $name = Str::replace(DIRECTORY_SEPARATOR,'.',$namespace);
            $this->loadComponent($name, $name);
        }
    }
    
    public function loadComponent($component, $alias=null){
        if ($alias == null){
            $alias=$component;
        }
        Blade::component("tall::components.{$component}",'tall-'.$alias);
    }
}
