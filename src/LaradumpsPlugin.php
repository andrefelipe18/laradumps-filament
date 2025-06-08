<?php

declare(strict_types = 1);

namespace LaradumpsFilament;

use Filament\Contracts\Plugin;
use Filament\Forms\Components\Field;
use Filament\Panel;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Columns\Column;
use Filament\Tables\Filters\BaseFilter;
use Filament\Tables\Table;
use Filament\View\PanelsRenderHook;
use Illuminate\Database\Eloquent\Builder;
use LaradumpsFilament\Debuggers\FieldDebug;
use LaradumpsFilament\Debuggers\TableDebug;

class LaradumpsPlugin implements Plugin
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
        Field::macro('ds', function (bool $onBlur = true, ?int $debounce = null, string $color = 'orange'): Field {
            if (! app()->isLocal()) {
                \Log::debug('LaraDumps: ds() macro called in non-local environment, skipping.');

                return $this;
            }

            $label = $this->getLabel();

            $this
                ->live($onBlur, $debounce)
                ->afterStateUpdated(
                    fn (mixed $state, mixed $old): \LaraDumps\LaraDumpsCore\LaraDumps => ds(new FieldDebug(oldValue: $old, newValue: $state))
                        ->color($color)
                        ->label($label)
                );

            return $this;
        });
        Table::macro('ds', function (string $color = 'blue'): Table {
            if (! app()->isLocal()) {
                \Log::debug('LaraDumps: ds() macro called in non-local environment, skipping.');

                return $this;
            }

            $startTime = microtime(true);

            /** @var Builder $query */
            $query = $this->getQuery();
            $model = $query->getModel();

            $backtrace  = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 3);
            $caller     = $backtrace[1] ?? null;
            $calledFrom = $caller !== null && $caller !== [] ? "{$caller['file']}:{$caller['line']}" : null;

            $totalRecords = null;
            $queryTime    = null;

            try {
                $startQuery   = microtime(true);
                $totalRecords = $query->count();
                $queryTime    = round((microtime(true) - $startQuery) * 1000, 2);
            } catch (\Exception) {
            }

            $columns = collect($this->getColumns())->map(fn(Column $column): array => [
                'name'       => $column->getName(),
                'label'      => $column->getLabel(),
                'sortable'   => $column->isSortable(),
                'searchable' => $column->isSearchable(),
                'toggleable' => $column->isToggleable(),
            ])->toArray();

            $filters = collect($this->getFilters())->map(fn(BaseFilter $filter, string $name): array => [
                'name'  => $name,
                'label' => $filter->getLabel() ?? $name,
                'type'  => class_basename($filter),
            ])->toArray();

            $actions = collect($this->getActions())->map(fn(Action $action): array => [
                'name'    => $action->getName(),
                'label'   => $action->getLabel(),
                'type'    => class_basename($action),
                'visible' => $action->isVisible(),
            ])->toArray();

            $bulkActions = collect($this->getBulkActions())->map(fn(BulkAction $action): array => [
                'name'    => $action->getName(),
                'label'   => $action->getLabel(),
                'type'    => class_basename($action),
                'visible' => $action->isVisible(),
            ])->toArray();

            $tableDebug = new TableDebug(
                // Query Information
                query: $query->toSql(),
                bindings: $query->getBindings(),
                rawSql: $query->toRawSql(),

                // Scopes and Relationships
                model: $model,
                globalScopes: $model->getGlobalScopes(),
                relations: array_keys($model->getRelations()),
                eagerLoaded: $query->getEagerLoads(),

                // Table Configuration
                modelLabel: $this->getModelLabel(),
                columns: $columns,
                filters: $filters,
                actions: $actions,
                bulkActions: $bulkActions,

                // Pagination & Performance
                recordsPerPage: $this->getPaginationPageOptions()[0] ?? null,
                totalRecords: $totalRecords,
                paginationEnabled: $this->isPaginated(),
                searchableColumns: collect($columns)->where('searchable', true)->pluck('name')->toArray(),
                sortableColumns: collect($columns)->where('sortable', true)->pluck('name')->toArray(),

                // Performance Metrics
                queryTime: $queryTime,
                queryCount: $query->getQuery()->getCountForPagination(),
                appliedFilters: collect($filters)->where('active', true)->pluck('name')->toArray(),

                // Debugging Context
                calledFrom: $calledFrom,
            );

            $label          = $this->getHeading() ?: $this->getModelLabel();
            $processingTime = round((microtime(true) - $startTime) * 1000, 2);

            ds($tableDebug)
                ->color($color)
                ->label("Table: {$label} (Debug time: {$processingTime}ms)");

            return $this;
        });
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
                fn (): string => \Blade::render('@livewire(\'LaradumpsFilament\DsComponent\')'),
            );
    }
}
