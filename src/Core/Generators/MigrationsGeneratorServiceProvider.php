<?php 
/**
 * Created by Claudio Campos.
 * User: callcocam@gmail.com, contato@sigasmart.com.br
 * https://www.sigasmart.com.br
 */

namespace Tall\Orm\Core\Generators;

use Illuminate\Support\ServiceProvider;

class MigrationsGeneratorServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->app->singleton('migration.generate',
            function($app) {
                return new MigrateGenerateCommand(
                    $app->make('Tall\Orm\Core\Generators\Way\Generators\Generator'),
                    $app->make('Tall\Orm\Core\Generators\Way\Generators\Filesystem\Filesystem'),
                    $app->make('Tall\Orm\Core\Generators\Way\Generators\Compilers\TemplateCompiler'),
                    $app->make('migration.repository'),
                    $app->make('config')
                );
            });

		$this->commands('migration.generate');

		// Bind the Repository Interface to $app['migrations.repository']
		$this->app->bind('Illuminate\Database\Migrations\MigrationRepositoryInterface', function($app) {
			return $app['migration.repository'];
		});
	}

	/**
	 * Bootstrap the application events.
	 *
	 * @return void
	 */
	public function boot()
	{
		$this->mergeConfigFrom(__DIR__.'/config/generators.php','generators');
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array();
	}

}
