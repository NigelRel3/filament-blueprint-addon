<?php

namespace NigelR\FilamentBlueprintAddon;

use Blueprint\Models\Column;
use Blueprint\Models\Model as BlueprintModel;
use Blueprint\Tree;
use Illuminate\Database\Schema\Builder as SchemaBuilder;
use Illuminate\Support\Str;

/**
 * Class ModelSchema
 *
 * A custom schema builder that uses the blueprint draft.yaml for the table columns.
 */
class ModelSchema extends SchemaBuilder
{
    protected static BlueprintModel $model;
    protected static Tree $tree;

    public function __construct()
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
        $table = Str::singular(Str::studly($table));
        $thisTable = self::$tree->models()[$table] ?? null;

        if ($thisTable === null) {
            return [];
        }
        /**
         * @var Column $column
         */
        foreach ($thisTable->columns() as $column){
            $typeName = $this->translateType($column);
            $type = $typeName;
            if ($type === 'enum') {
                $type .= ' (' . implode(',', $column->attributes() ?? []) . ')';
            }
            elseif ($type === 'varchar') {
                $length = (int)($column->attributes()[0] ?? 255);
                $type .= "($length)";
            }
            $definition = [
                'name' => $column->name(),
                'type_name' => $typeName,
                'type' => $type,
                'collation' => null,
                'nullable' => in_array('nullable', $column->modifiers() ?? []),
                'default' => when($column->modifiers()[0]['default'] ?? null, fn() => $column->modifiers()[0]['default'], null),
                'auto_increment' => $column->name() === 'id',
                'comment' => '',
                'generation' => null,

                'length' => when($typeName === 'varchar', fn() => $column->attributes()[0] ?? 255, null),

                'values' => when($typeName === 'enum', fn() => $column->attributes() ?? [], null),
            ];

            $columns[] = $definition;
        }

        $additionalColumns = [];
        if ($thisTable->usesSoftDeletes()) {
            $additionalColumns[] = 'deleted_at';
        }
        if ($thisTable->usesTimestamps()) {
            $additionalColumns[] = 'created_at';
            $additionalColumns[] = 'updated_at';
        }

        foreach ($additionalColumns as $timestampColumn) {
            $columns[] = [
                'name' => $timestampColumn,
                'type_name' => 'datetime',
                'type' => 'datetime',
                'collation' => null,
                'nullable' => $timestampColumn === 'deleted_at',
                'default' => null,
                'auto_increment' => false,
                'comment' => '',
                'generation' => null,
                'length' => null,
                'values' => null,
            ];
        }

        return $columns;
    }

    /**
     * Translate blueprint type names to MySQL types
     */
    protected function translateType(Column $column): string
    {
        $type = $column->dataType();
        return match($type) {
            'string' => 'varchar',
            'boolean' => 'tinyint(1)',
            'dateTime' => 'datetime',
            'id' => 'bigint unsigned',
            default => $type,
        };
    }

    public function getIndexes($table)
    {
        return [];
    }

    public function getForeignKeys($table)
    {
        return [];
    }
}
