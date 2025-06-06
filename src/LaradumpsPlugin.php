<?php

namespace LaradumpsFilament;

use Filament\Contracts\Plugin;
use Filament\Forms\Components\Field;
use Filament\Panel;
use Filament\Support\Facades\FilamentAsset;
use Filament\Support\Assets\Js;
use Filament\View\PanelsRenderHook;

class LaradumpsPlugin implements Plugin
{
    public function getId(): string
    {
        return 'laradumps-filament';
    }

    public function register(Panel $panel): void
    {
        if (!app()->isLocal()) {
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

        Field::macro('ds', function (...$args): Field {
            ds(...$args);

            return $this;
        });
    }

    public function boot(Panel $panel): void
    {

    }

    public static function make(): static
    {
        return app(static::class);
    }
}
