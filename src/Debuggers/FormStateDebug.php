<?php

declare(strict_types = 1);

namespace LaraDumpsFilament\Debuggers;

class FormStateDebug extends BaseDebug
{
    public function __construct(
        // State
        public mixed $oldState = null,
        public mixed $newState = null,
    ) {
    }
}
