<?php

namespace NigelR\FilamentBlueprintAddon\Tests;

use Blueprint\Models\Column;
use NigelR\FilamentBlueprintAddon\ModelSchema;
use Blueprint\Models\Model as BlueprintModel;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Blueprint\Tree;


#[CoversClass(ModelSchema::class)]
class ModelSchemaTest extends TestCase
{
    #[Test]
    public function getSchemaBuilderReturnsBasicModelColumns()
    {
        $model = new ModelSchema();
        $modelName = 'TestModel';
        $testModel = $this->generateBlueprintModel($modelName, [
            $this->generateColumn('id', 'id', ['primary']),
            $this->generateColumn('name', 'string', [], [19])
        ], false);
        $model->setModel($testModel);
        $model->setConfig($this->getTree([$modelName => $testModel], []));

        $columns = $model->getColumns($modelName);
        $this->assertCount(2, $columns);
        $this->assertEquals('id', $columns[0]['name']);
        $this->assertEquals('bigint unsigned', $columns[0]['type_name']);
        $this->assertEquals('bigint unsigned', $columns[0]['type']);
        $this->assertTrue($columns[0]['auto_increment']);
        $this->assertFalse($columns[0]['nullable']);

        $this->assertEquals('name', $columns[1]['name']);
        $this->assertEquals('varchar', $columns[1]['type_name']);
        $this->assertEquals('varchar(19)', $columns[1]['type']);
        $this->assertEquals('19', $columns[1]['length']);
        $this->assertFalse($columns[1]['nullable']);
    }

    #[Test]
    public function getSchemaBuilderReturnsModelWithtimestamps()
    {
        $model = new ModelSchema();
        $modelName = 'TestModel';
        $testModel = $this->generateBlueprintModel($modelName, [
            $this->generateColumn('id', 'id', ['primary']),
            $this->generateColumn('name', 'string', [], [19])
        ], true);
        $model->setModel($testModel);
        $model->setConfig($this->getTree([$modelName => $testModel], []));

        $columns = $model->getColumns($modelName);
        $this->assertCount(4, $columns);
        $this->assertEquals('id', $columns[0]['name']);

        $this->assertEquals('name', $columns[1]['name']);

        $this->assertEquals('created_at', $columns[2]['name']);
        $this->assertEquals('datetime', $columns[2]['type_name']);
        $this->assertEquals('datetime', $columns[2]['type']);
        $this->assertFalse($columns[2]['nullable']);

        $this->assertEquals('updated_at', $columns[3]['name']);
        $this->assertEquals('datetime', $columns[3]['type_name']);
        $this->assertEquals('datetime', $columns[3]['type']);
        $this->assertFalse($columns[3]['nullable']);
    }


    /**
     * Internal function to generate a column for testing purposes.
     */
    public function generateColumn(string $name, string $type, array $modifiers = [], array $attributes = []): Column
    {
        return new Column($name, $type, $modifiers, $attributes);
    }

    /**
     * Internal function to generate a blueprint model for testing purposes.
     */
    public function generateBlueprintModel(string $name, array $columns, bool $timestamps = true): BlueprintModel
    {
        $model = new BlueprintModel($name);
        if (!$timestamps) {
            $model->disableTimestamps();
        }

        foreach ($columns as $column) {
            $model->addColumn($column);
        }

        return $model;
    }

    /**
     * Internal function to generate a blueprint tree for testing purposes.
     */
    public function getTree(array $models, array $filament)
    {
        return new Tree([
            'tree' => [
                'models' => $models,
                'filament' => $filament
            ],
            'models' => $models
        ]);
    }
}