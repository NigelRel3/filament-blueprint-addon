<?php

namespace NigelR\FilamentBlueprintAddon\Tests;

use Blueprint\Blueprint;
use Illuminate\Foundation\Application;
use NigelR\FilamentBlueprintAddon\FilamentBlueprintAddonServiceProvider;
use NigelR\FilamentBlueprintAddon\FilamentBlueprintGenerator;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(FilamentBlueprintAddonServiceProvider::class)]
class FilamentBlueprintAddonServiceProviderTest extends TestCase
{
    #[Test]
    public function bootCheckPublished()
    {
        $app = new Application();
        $this->assertTrue($app->runningInConsole());
        $provider = new FilamentBlueprintAddonServiceProvider($app);
        $this->assertInstanceOf(FilamentBlueprintAddonServiceProvider::class, $provider);
        $provider->boot();
        $providers = FilamentBlueprintAddonServiceProvider::publishableProviders();
        $this->assertIsArray($providers);
        $this->assertContains(FilamentBlueprintAddonServiceProvider::class, $providers);

        $publishPaths = FilamentBlueprintAddonServiceProvider::pathsToPublish(null, 'filamentBlueprint');
        $this->assertIsArray($publishPaths);
        $this->assertStringEndsWith('filamentBlueprint.php', array_key_first($publishPaths));
    }

    #[Test]
    public function providesReturnsArray()
    {
        $app = new Application();
        $provider = new FilamentBlueprintAddonServiceProvider($app);

        $provides = $provider->provides();
        $this->assertIsArray($provides);
        $this->assertSame('command.blueprint.build', $provides[0]);
        $this->assertSame(FilamentBlueprintGenerator::class, $provides[1]);
        $this->assertSame(Blueprint::class, $provides[2]);
    }
}