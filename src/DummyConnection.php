<?php

namespace NigelRel3\FilamentBlueprintAddon;

use Illuminate\Database\Connection;

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
