<?php

namespace App\DTO\AI;

use App\Enum\Language;
use Symfony\AI\Platform\Contract\JsonSchema\Attribute\With;

class ReviewTranslationResponse
{
    public function __construct(
        #[With(enum: [
            Language::Croatian->value,
            Language::Czech->value,
            Language::Hungarian->value,
            Language::Romanian->value,
            Language::Slovak->value,
            Language::Slovenian->value,
        ])] public string $locale,
        public string $text,
    ) {
    }

    public function getLocaleEnum(): Language
    {
        return Language::from($this->locale);
    }
}
