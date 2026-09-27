<?php

namespace NigelR\FilamentBlueprintAddon\Tests;

use NigelR\FilamentBlueprintAddon\FilamentLexer;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(FilamentLexer::class)]
class FilamentLexerTest extends TestCase
{
    #[Test]
    public function analyzeWithNoTokens()
    {
        $lexer = new FilamentLexer();
        $this->assertInstanceOf(FilamentLexer::class, $lexer);

        $registry = $lexer->analyze([]);
        $this->assertSame([
            'filament' => []
        ], $registry);
    }

    #[Test]
    public function analyzeWithTokens()
    {
        $lexer = new FilamentLexer();
        $this->assertInstanceOf(FilamentLexer::class, $lexer);

        $registry = $lexer->analyze(['filament' => ['token1', 'token2']]);
        $this->assertSame([
            'filament' => ['token1', 'token2']
        ], $registry);
    }
}