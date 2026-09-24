<?php

namespace NigelRel3\FilamentBlueprintAddon;

use Blueprint\Models\Column;
use Blueprint\Models\Model as BlueprintModel;
use Blueprint\Tree;
use Illuminate\Database\Schema\Builder as SchemaBuilder;
use Illuminate\Database\Connection;
use Illuminate\Support\Str;

class ModelSchema extends SchemaBuilder
{
    protected static BlueprintModel $model;
    protected static Tree $tree;

    public function __construct(?Connection $connection = null)
    {
    }

    public static function setModel(BlueprintModel $model): void
    {
        self::$model = $model;
    }

    public static function setConfig(Tree $tree): void
    {
        self::$tree = $tree;
    }

    public function getColumns($table): array
    {
        $columns = [];
        // TODO As the filament section only uses the name for matching, maybe allow alternative for database table name
        $table = Str::singular(Str::studly($table));
        /**
         * @var \Blueprint\Models\Column $column
         */
        // TODO Verify that the column modifiers and default values are correctly handled
        foreach (self::$tree->models()[$table]->columns() as $column){
            $type = $this->translateType($column);
            $definition = [
                'name' => $column->name(),
                'type_name' => $type,
                'type' => $type,
                'collation' => null,
                'nullable' => in_array('nullable', $column->modifiers()),
                'default' => when($column->modifiers()[0]['default'] ?? null, fn() => $column->modifiers()[0]['default'], null),
                'auto_increment' => $column->name() === 'id',
                'comment' => '',
                'generation' => null,
            ];

            $columns[] = $definition;
        }
        return $columns;
    }

    /**
     * Translate blueprint type names to MySQL types
     */
    protected function translateType(Column $column): string
    {
        $type = $column->dataType();
        $tx = match($type) {
            'string' => 'varchar',
            'boolean' => 'tinyint(1)',
            'dateTime' => 'datetime',
            'id' => 'bigint unsigned',
            default => $type,
        };

        if ($tx === 'varchar') {
            $length = $column->attributes()['length'] ?? 255;
            $tx .= "($length)";
        }

        if ($type === 'enum') {
            $tx .= '(' . implode(',', $column->attributes() ?? []) . ')';
        }
        return $tx;
    }

    public function getIndexes($table): array
    {
        // TODO Implement index retrieval based on the blueprint model
        return [];
    }
}
