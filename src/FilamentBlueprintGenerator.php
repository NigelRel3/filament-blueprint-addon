<?php

namespace NigelR\FilamentBlueprintAddon;

use Blueprint\Contracts\Generator;
use Blueprint\Models\Model as BlueprintModel;
use Blueprint\Tree;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Artisan;

class FilamentBlueprintGenerator implements Generator
{
    public function __construct(
        protected ?Filesystem $files,
        protected ?FilamentMake $generator = null,
    )
    {
    }

    public function output(Tree $tree): array
    {
        // TODO Pass this in
        //$generator = new FilamentMake(app(Factory::class, ['output' => new ConsoleOutput()]));

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
            $this->generator->setModel($model);
            $this->generator->configureModel();
        }

        $clusters = $filamentSettings['clusters'] ?? [];
        $clusterNames = [];

        /**
         * For format:
         * clusters:
         *   clusterName: model1,model2
         */
        if (is_array($clusters)) {
            foreach ($clusters as $key => $value) {
                $key = trim($key);
                $clusterNames[] = $key;
                foreach (explode(',', $value) as $item) {
                    $item = trim($item);
                    // Add cluster information to the model's filament settings
                    $filamentSettings[$item] = ($filamentSettings[$item] ?? '') . ' cluster:' . $key;
                }
            }
        }
        else {
            /**
             * For format:
             * clusters: cluster1,cluster2
             */
            $clusterNames = explode(',', $clusters);
        }
        // Create filament clusters for all cluster names
        foreach ($clusterNames as $cluster) {
            Artisan::call('make:filament-cluster', [
                'name' => trim($cluster),
                '--force' => null,
            ]);
        }

        /** @var BlueprintModel $model */
        foreach ($tree->models() as $model) {
            if (isset($filamentSettings[$model->name()])) {
                $this->generator->setModel($model);
                $this->generator->setOptions($globalOptions . ' ' . $filamentSettings[$model->name()]);
                $this->generator->handle();
            }
        }

        return [];
    }

    public function types(): array
    {
        return ['filament'];
    }

}
