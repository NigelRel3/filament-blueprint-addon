<?php

namespace NigelR\FilamentBlueprintAddon;

use Blueprint\Blueprint;
use Illuminate\Database\Connection;
use Illuminate\Support\ServiceProvider;
use NigelR\FilamentBlueprintAddon\FilamentBlueprintGenerator;
use NigelR\FilamentBlueprintAddon\FilamentLexer;

class FilamentBlueprintAddonServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot(): void
    {

        if ($this->app->runningInConsole()) {
            $this->publishes([
                dirname(__DIR__) . '/config/filamentBlueprint.php' => config_path('filamentBlueprint.php'),
            ], 'filamentBlueprint');
        }
    }

    /**
     * Register the application services.
     */
    public function register(): void
    {
        Connection::resolverFor('dummy', function ($connection, $config, $name) {
            return new DummyConnection();
        });

        config([
            'database.connections.dummy.connection' => [
                'driver' => 'dummy',
                'database' => 'none',
            ],
        ]);

        $this->mergeConfigFrom(
            dirname(__DIR__) . '/config/filamentBlueprint.php',
            'filamentBlueprint'
        );

        $this->app->singleton(FilamentBlueprintGenerator::class, function ($app) {
            $generator = new FilamentBlueprintGenerator($app['files']);

            return $generator;
        });

        $this->app->extend(Blueprint::class, function ($blueprint, $app) {
            $blueprint->registerGenerator($app[FilamentBlueprintGenerator::class]);
            $blueprint->registerLexer(new FilamentLexer());

            return $blueprint;
        });

    }

    /**
     * Get the services provided by the provider.
     */
    public function provides(): array
    {
        return [
            'command.blueprint.build',
            FilamentBlueprintGenerator::class,
            Blueprint::class,
        ];
    }
}
