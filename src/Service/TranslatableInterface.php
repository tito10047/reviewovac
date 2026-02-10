<?php

namespace App\Service;

interface TranslatableInterface
{
    public function getTranslatableType(): string;

    public function getTranslatableId(): string;
}
