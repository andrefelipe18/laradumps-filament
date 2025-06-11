<?php

declare(strict_types = 1);

namespace LaraDumpsFilament\Attributes;

use LaraDumpsFilament\Debuggers\FormStateDebug;
use LaraDumpsFilament\LaraDumpsFilament;
use Livewire\Attribute;
use Livewire\Component;
use Livewire\Mechanisms\HandleComponents\ComponentContext;

use function Livewire\on;

#[\Attribute(\Attribute::TARGET_CLASS)]
class DumpFormState extends Attribute
{
    public function __construct(
        public string $statePath = 'data',
    ) {
    }

    public function boot(): void
    {
        $component   = $this->getComponent();
        $componentId = $component->getId();

        $previousState = data_get($component->all(), $this->statePath, []);

        on('dehydrate', function (Component $component, ComponentContext $context) use ($componentId, &$previousState): void {
            if ($component->getId() !== $componentId) {
                return;
            }

            $properties   = $context->component->all();
            $currentState = data_get($properties, $this->statePath, []);

            if ($currentState === $previousState) {
                return;
            }

            LaraDumpsFilament::dump(
                new FormStateDebug(oldState: $previousState, newState: $currentState),
                label: "Form State",
            );

            $previousState = $currentState;
        });
    }
}
