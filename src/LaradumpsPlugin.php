<?php

namespace LaradumpsFilament;

use Filament\Contracts\Plugin;
use Filament\Forms\Components\Field;
use Filament\Panel;
use Filament\View\PanelsRenderHook;

class LaradumpsPlugin implements Plugin
{
    public function getId(): string
    {
        return 'laradumps-filament';
    }

    public function register(Panel $panel): void
    {
        Field::macro('ds', function (bool $onBlur = true, ?int $debounce = null, string $color = 'orange'): Field {
            if (! app()->isLocal()) {
                \Log::debug('LaraDumps: ds() macro called in non-local environment, skipping.');

                return $this;
            }

            $label = $this->getLabel();

            $this
                ->live($onBlur, $debounce)
                ->afterStateUpdated(fn (mixed $state) => ds($state)
                    ->color($color)
                    ->label($label)
                );

            return $this;
        });

        if (! app()->isLocal()) {
            return;
        }

        $panel
            ->renderHook(
                PanelsRenderHook::HEAD_END,
                fn (): string => '<script src="https://cdn.jsdelivr.net/npm/laradumps-js/dist/laradumps.min.js"></script>',
            )
            ->renderHook(
                PanelsRenderHook::BODY_END,
                fn (): string => \Blade::render('@livewire(\'LaradumpsFilament\DsComponent\')'),
            );
    }

    public function boot(Panel $panel): void {}

    public static function make(): static
    {
        return app(static::class);
    }
}
