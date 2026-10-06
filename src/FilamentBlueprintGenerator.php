<?php

namespace NigelR\FilamentBlueprintAddon;

use Blueprint\Contracts\Generator;
use Blueprint\Models\Model as BlueprintModel;
use Blueprint\Tree;
use Illuminate\Console\View\Components\Factory;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Artisan;
use Symfony\Component\Console\Output\ConsoleOutput;

class FilamentBlueprintGenerator implements Generator
{
    public function __construct(protected ?Filesystem $files)
    {
    }

    public function output(Tree $tree): array
    {
        $generator = new FilamentMake(app(Factory::class, ['output' => new ConsoleOutput()]));

        $filamentSettings = $tree->toArray()['filament'];

        ModelSchema::setConfig($tree);

        // Models defined as list in draft.yaml
        // e.g. models: AppUser,Address
        if (isset($filamentSettings['models'])) {
            foreach (explode(',', $filamentSettings['models']) as $model) {
                $filamentSettings[trim($model)] = '';
            }
        }
        // Get the global settings for all filament commands
        // e.g. options: panel:admin view:true
        $globalOptions = $filamentSettings['options'] ?? '';

        // Make sure all models are configured using internal settings
        foreach ($tree->models() as $model) {
            $generator->setModel($model);
            $generator->configureModel();
        }

        $this->clusterProcessing($filamentSettings, $globalOptions);

        /** @var BlueprintModel $model */
        foreach ($tree->models() as $model) {
            if (isset($filamentSettings[$model->name()])) {
                $generator->setModel($model);
                $generator->setOptions($globalOptions . ' ' . $filamentSettings[$model->name()]);
                $generator->handle();
            }
        }

        return [];
    }

    public function types(): array
    {
        return ['filament'];
    }

    protected function clusterProcessing(array $filamentSettings, string $globalOptions): void
    {
        $clusters = $filamentSettings['clusters'] ?? [];
        foreach (explode(',', $clusters) as $cluster) {
            Artisan::call('make:filament-cluster', [
                'name' => trim($cluster),
                '--force' => null,
            ]);
        }
    }
}
