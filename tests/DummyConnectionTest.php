<?php

namespace NigelR\FilamentBlueprintAddon\Tests;

use NigelR\FilamentBlueprintAddon\DummyConnection;
use NigelR\FilamentBlueprintAddon\ModelSchema;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(DummyConnection::class)]
class DummyConnectionTest extends TestCase
{
    #[Test]
    public function getSchemaBuilderReturnsModelSchema()
    {
        $connection = new DummyConnection();
        $this->assertInstanceOf(DummyConnection::class, $connection);

        $this->assertInstanceOf(ModelSchema::class, $connection->getSchemaBuilder());
    }
}