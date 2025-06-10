<?php

declare(strict_types = 1);

namespace LaraDumpsFilament;

use Filament\Contracts\Plugin;
use Filament\Forms\Components\Field;
use Filament\Forms\Get;
use Filament\Panel;
use Filament\Tables\Table;
use Filament\View\PanelsRenderHook;
use LaraDumpsFilament\Debuggers\FieldDebug;
use LaraDumpsFilament\Debuggers\TableDebug;

class LaraDumpsPlugin implements Plugin
{
    public function getId(): string
    {
        return 'laradumps-filament';
    }

    public function register(Panel $panel): void
    {
        $this->registerMacros();

        if (! app()->isLocal()) {
            return;
        }

        $this->registerHooks($panel);
    }

    public function boot(Panel $panel): void
    {
    }

    public static function make(): static
    {
        return app(static::class);
    }

    private function registerMacros(): void
    {
        $this->registerFieldMacro();
        $this->registerTableMacro();
    }

    private function registerHooks(Panel $panel): void
    {
        $panel
            ->renderHook(
                PanelsRenderHook::HEAD_END,
                fn (): string => '<script src="https://cdn.jsdelivr.net/npm/laradumps-js/dist/laradumps.min.js"></script>',
            )
            ->renderHook(
                PanelsRenderHook::BODY_END,
                fn (): string => \Blade::render('@livewire(\'LaraDumpsFilament\DsComponent\')'),
            );
    }

    private function registerFieldMacro(): void
    {
        Field::macro('ds', function (bool $onBlur = true, ?int $debounce = null, string $color = 'orange'): Field {
            /** @var Field $field */
            $field = $this;

            if (! app()->isLocal()) {
                \Log::debug('LaraDumps: ds() macro called in non-local environment, skipping.');

                return $field;
            }

            $field
                ->live($onBlur, $debounce)
                ->afterStateUpdated(
                    function (mixed $state, mixed $old, Get $get) use ($color, $field): \LaraDumps\LaraDumpsCore\LaraDumps {
                        $fieldDebug = new FieldDebug(
                            oldValue: $old,
                            newValue: $state,
                            properties: FieldDebug::mountProperties($field),
                            attributes: FieldDebug::mountAttributes($field),
                            validation: FieldDebug::mountValidation($field),
                            livewire: FieldDebug::mountLivewireComponent($field, $get),
                            extras: FieldDebug::mountExtras($field),
                        );

                        return LaraDumpsFilament::dump(
                            $fieldDebug,
                            label: "Field: {$field->getLabel()} ({$field->getName()})",
                            color: $color,
                            type: class_basename($field),
                        );
                    }
                );

            return $field;
        });
    }

    private function registerTableMacro(): void
    {
        Table::macro('ds', function (string $color = 'blue'): Table {
            $table = $this;

            if (! app()->isLocal()) {
                \Log::debug('LaraDumps: ds() macro called in non-local environment, skipping.');

                return $table;
            }

            $startTime = microtime(true);

            $tableDebug = new TableDebug(
                query: TableDebug::mountQuery($table),
                model: TableDebug::mountModel($table),
                configuration: TableDebug::mountConfiguration($table),
                pagination: TableDebug::mountPagination($table),
                performance: TableDebug::mountPerformance($startTime, TableDebug::mountQuery($table)['Query Time (ms)']),
                actions: TableDebug::mountActions($table),
            );

            $label = $table->getHeading() ?: $table->getModelLabel();

            LaraDumpsFilament::dump(
                $tableDebug,
                label: "Table: {$label}",
                color: $color,
            );

            return $table;
        });
    }
}
