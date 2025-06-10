<?php

declare(strict_types = 1);

namespace LaraDumpsFilament\Helpers;

class TraitChecker
{
    private array $traits;

    public function __construct(object $object)
    {
        $this->traits = class_uses($object);
    }

    public function uses(string $trait): bool
    {
        return isset($this->traits[$trait]);
    }
}
