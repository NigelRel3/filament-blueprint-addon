<?php

namespace NigelR\FilamentBlueprintAddon;

use Illuminate\Database\Connection;

/**
 * Class DummyConnection
 *
 * A dummy database connection that returns a schema builder which uses the blueprint draft.yaml
 * for the table columns.
 */
class DummyConnection extends Connection
{
    public function __construct()
    {
    }

    public function getSchemaBuilder(): ?\Illuminate\Database\Schema\Builder
    {
        return new ModelSchema();
    }
}
