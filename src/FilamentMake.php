<?php

namespace NigelR\FilamentBlueprintAddon;

use Blueprint\Models\Model as BlueprintModel;
use Filament\Commands\MakeResourceCommand;
use Filament\Facades\Filament;
use Illuminate\Support\Stringable;
use Override;
use Illuminate\Container\Container;

class FilamentMake extends MakeResourceCommand
{
    protected BlueprintModel $model;

    protected array $options = [];

    public function setModel(BlueprintModel $model): void
    {
        $this->model = $model;
        ModelSchema::setModel($model);
    }

    public function setOptions(string $options): void
    {
        parse_str(str_replace([':', ' '], ['=', '&'], $options), $this->options);
    }

    public function __construct(protected $components) {}

    protected function configureIsSoftDeletable(): void
    {
        $this->isSoftDeletable = $this->model->usesSoftDeletes();
    }

    protected function configureIsGenerated(): void
    {
        $this->isGenerated = true;
    }

    protected function configureHasViewOperation(): void
    {
        $this->hasViewOperation = ($this->options['view'] ?? '') === 'true';
    }

    protected function configureParentResource(): void
    {
        $this->parentResourceFqn = null;
    }

    protected function configureCluster(): void
    {
        // TODO Determine if the resource is clusterFqn

        // https://filamentphp.com/docs/5.x/navigation/clusters#clusters

        $this->clusterFqn = null;
    }

    protected function configureIsSimple(): void
    {
        $this->isSimple = ($this->options['simple'] ?? 'false') === 'true';;
    }

    protected function configureIsNested(): void
    {
        $this->isNested = false;
    }

    protected function configurePanel(string $question, ?string $initialQuestion = null): void
    {
        $this->panel = Filament::getPanel($this->options['panel'] ?? 'admin', isStrict: false);
    }

    protected function configureModel(): void
    {
        $this->modelFqnEnd = (string) str($this->model->name())
            ->trim('/')
            ->trim('\\')
            ->trim(' ')
            ->when(
                fn (Stringable $model): bool => str($model)->endsWith('Resource'),
                fn (Stringable $model): Stringable => str($model)->beforeLast('Resource'),
            )
            ->studly()
            ->replace('/', '\\');

        $modelNamespace = app()->getNamespace() . 'Models';

        $this->modelFqn = "{$modelNamespace}\\{$this->modelFqnEnd}";

        Container::getInstance()->resolving($this->modelFqn, function ($object, $app) {
            $object->setConnection('dummy.connection');
            return $object;
        });
    }

    protected function configureRecordTitleAttribute(): void
    {
        if ($this->options['title'] ?? false) {
            $this->recordTitleAttribute = $this->options['title'];
        }
        elseif ($this->model->hasColumn('name')) {
            $this->recordTitleAttribute = 'name';
        }
    }

    /**
     * Ignore collisions for the given paths.
     */
    protected function checkForCollision(string | array $paths): bool
    {
        return false;
    }

    /**
     * Provides dummy values for the given option key.
     */
    #[Override]
    public function option($key = null)
    {
        return match ($key) {
            'force' => true,
            'not-embedded' => true,
            'embed-schemas' => false,
            'embed-table' => false,
            default => false,
        };
    }
}
