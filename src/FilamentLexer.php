<?php

namespace NigelR\FilamentBlueprintAddon;

use Blueprint\Contracts\Lexer;

class FilamentLexer implements Lexer
{
    public function analyze(array $tokens): array
    {
        $registry = ['filament' => []];

        if (!empty($tokens['filament'])) {
            $registry['filament'] = $tokens['filament'] ?? '';
        }

        return $registry;
    }
}
