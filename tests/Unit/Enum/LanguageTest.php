<?php

namespace App\Tests\Unit\Enum;

use App\Enum\Language;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class LanguageTest extends TestCase
{
    #[DataProvider('provideNeedTranslationData')]
    public function testNeedTranslationFor(Language $source, Language $target, bool $expected): void
    {
        $this->assertSame($expected, $source->needTranslationFor($target));
    }

    /** @return array<string, array{Language, Language, bool}> */
    public static function provideNeedTranslationData(): array
    {
        return [
            'Same language: Slovak' => [Language::Slovak, Language::Slovak, false],
            'Same language: Hungarian' => [Language::Hungarian, Language::Hungarian, false],
            'Slovak to Czech (no translation needed according to logic)' => [Language::Slovak, Language::Czech, false],
            'Czech to Slovak (symmetry check, if intended)' => [Language::Czech, Language::Slovak, false],
            'Slovak to Hungarian (translation needed)' => [Language::Slovak, Language::Hungarian, true],
            'Hungarian to Slovak (translation needed)' => [Language::Hungarian, Language::Slovak, true],
            'Romanian to Croatian (translation needed)' => [Language::Romanian, Language::Croatian, true],
        ];
    }

    public function testGetSupportedLanguages(): void
    {
        $supported = Language::getSupportedLanguages();
        $this->assertContains('sk', $supported);
        $this->assertContains('hu', $supported);
        $this->assertContains('cz', $supported);
    }
}
