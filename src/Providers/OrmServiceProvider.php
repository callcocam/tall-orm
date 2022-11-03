<?php
/**
* Created by Claudio Campos.
* User: callcocam@gmail.com, contato@sigasmart.com.br
* https://www.sigasmart.com.br
*/
namespace Tall\Orm\Providers;

use Illuminate\Support\ServiceProvider;
use Tall\Orm\Console\Commands\TallModelCommand;
use Tall\Orm\Core\Generators\MigrationsGeneratorServiceProvider;

class OrmServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        if(class_exists('\App\Models\Make')){
            $make = '\App\Models\Make';
        }
        else
        {
            $make = Make::class;
        }

        $this->app->bind('make', function() use($make){
              return app($make);
        });

        $this->app->register(MigrationsGeneratorServiceProvider::class);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../../config/tall-orm.php','tall-orm'
        );

        $this->registerCommands();
       
       
    }

    protected function registerCommands()
    {
        if (! $this->app->runningInConsole()) return;

        $this->commands([
            TallModelCommand::class, // make:livewire
        ]);
    }
}
