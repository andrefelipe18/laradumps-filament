<?php

declare(strict_types = 1);

namespace LaradumpsFilament\Debuggers;

class FieldDebug
{
    public function __construct(
        public mixed $oldValue = null,
        public mixed $newValue = null,
    ) {
    }
}
