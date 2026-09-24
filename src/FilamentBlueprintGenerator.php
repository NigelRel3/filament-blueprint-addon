<?php

namespace NigelRel3\FilamentBlueprintAddon;

use Blueprint\Contracts\Generator;
use Blueprint\Models\Model;
use Blueprint\Tree;
use Illuminate\Console\View\Components\Factory;
use Illuminate\Filesystem\Filesystem;
use Symfony\Component\Console\Output\ConsoleOutput;


// TODO Relationships? - https://filamentphp.com/docs/5.x/resources/managing-relationships#creating-a-relation-manager

// TODO erase to remove folders

// TODO create own library for handling blueprint filament generation

class FilamentBlueprintGenerator implements Generator
{
    public function __construct(protected ?Filesystem $files)
    {
    }

    public function output(Tree $tree): array
    {
        $output = [];

        $generator = new FilamentMake(app(Factory::class, ['output' => new ConsoleOutput()]));

        $filamentSettings = $tree->toArray()['filament'];

        ModelSchema::setConfig($tree);

        // Get the global settings for all filament commands
        $globalOptions = $filamentSettings['options'] ?? '';

        /** @var Model $model */
        foreach ($tree->models() as $model) {
            if (isset($filamentSettings[$model->name()])) {
                $generator->setModel($model);
                $generator->setOptions($globalOptions . ' ' . $filamentSettings[$model->name()]);
                $generator->handle();
            }
        }

        return $output;
    }

    public function types(): array
    {
        return ['filament'];
    }
}
