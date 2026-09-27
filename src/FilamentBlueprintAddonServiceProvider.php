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
    // TODO Test
    public function register(): void
    {
        Connection::resolverFor('filament_dummy', fn ($connection, $config, $name) => 
            new DummyConnection()
        );

        config([
            'database.connections.filament_dummy.connection' => [
                'driver' => 'filament_dummy',
                'database' => 'none',
            ],
        ]);

        $this->mergeConfigFrom(
            dirname(__DIR__) . '/config/filamentBlueprint.php',
            'filamentBlueprint'
        );

        $this->app->singleton(FilamentBlueprintGenerator::class, 
            fn ($app): FilamentBlueprintGenerator => new FilamentBlueprintGenerator($app['files']));

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
