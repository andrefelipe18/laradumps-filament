<?php

declare(strict_types = 1);

namespace LaraDumpsFilament\Debuggers;

use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Columns\Column;
use Filament\Tables\Table;

class TableDebug extends BaseDebug
{
    public function __construct(
        public ?array $query = null,
        public ?array $model = null,
        public ?array $configuration = null,
        public ?array $pagination = null,
        public ?array $performance = null,
        public ?array $actions = null,
    ) {
    }

    public static function mountQuery(Table $table): array
    {
        $query = $table->getQuery();

        $startQuery   = microtime(true);
        $totalRecords = null;
        $queryTime    = null;

        try {
            $totalRecords = $query->count();
            $queryTime    = round((microtime(true) - $startQuery) * 1000, 2);
        } catch (\Exception) {
        }

        return [
            'SQL'             => $query->toSql(),
            'Bindings'        => $query->getBindings(),
            'Raw SQL'         => $query->toRawSql(),
            'Total Records'   => $totalRecords,
            'Query Time (ms)' => $queryTime,
            'Eager Loaded'    => $query->getEagerLoads(),
        ];
    }

    public static function mountModel(Table $table): array
    {
        $query = $table->getQuery();
        $model = $query->getModel();

        return [
            'Model Class'   => $model::class,
            'Table Name'    => $model->getTable(),
            'Primary Key'   => $model->getKeyName(),
            'Global Scopes' => array_keys($model->getGlobalScopes()),
            'Relations'     => array_keys($model->getRelations()),
        ];
    }

    public static function mountConfiguration(Table $table): array
    {
        $columns = collect($table->getColumns())->map(fn (Column $column): array => [
            'Name'       => $column->getName(),
            'Label'      => $column->getLabel(),
            'Sortable'   => $column->isSortable(),
            'Searchable' => $column->isSearchable(),
            'Toggleable' => $column->isToggleable(),
            'Type'       => class_basename($column),
        ])->toArray();

        $filters = collect($table->getFilters())->map(fn (\Filament\Tables\Filters\BaseFilter $filter, string $name): array => [
            'Name'  => $name,
            'Label' => $filter->getLabel(),
        ])->toArray();

        return [
            'Model Label'            => $table->getModelLabel(),
            'Heading'                => $table->getHeading(),
            'Columns'                => $columns,
            'Filters'                => $filters,
            'Searchable Columns'     => collect($columns)->where('searchable', true)->pluck('name')->toArray(),
            'Sortable Columns'       => collect($columns)->where('sortable', true)->pluck('name')->toArray(),
            'Default Sort Direction' => $table->getDefaultSortDirection(),
        ];
    }

    public static function mountPagination(Table $table): array
    {
        return [
            'Is Paginated'      => $table->isPaginated(),
            'Records Per Page'  => $table->getPaginationPageOptions()[0] ?? null,
            'Page Options'      => $table->getPaginationPageOptions(),
        ];
    }

    public static function mountPerformance(float $startTime, ?float $queryTime = null): array
    {
        $processingTime = round((microtime(true) - $startTime) * 1000, 2);

        return [
            'Query Time (ms)'      => $queryTime,
            'Processing Time (ms)' => $processingTime,
        ];
    }

    public static function mountActions(Table $table): array
    {
        $actions = collect($table->getActions())->map(fn (Action $action): array => [
            'name'    => $action->getName(),
            'label'   => $action->getLabel(),
            'type'    => class_basename($action),
            'visible' => $action->isVisible(),
            'color'   => $action->getColor(),
        ])->toArray();

        $bulkActions = collect($table->getBulkActions())->map(fn (BulkAction $action): array => [
            'name'    => $action->getName(),
            'label'   => $action->getLabel(),
            'type'    => class_basename($action),
            'visible' => $action->isVisible(),
            'color'   => $action->getColor(),
        ])->toArray();

        $headerActions = collect($table->getHeaderActions())->map(fn (Action $action): array => [
            'name'    => $action->getName(),
            'label'   => $action->getLabel(),
            'type'    => class_basename($action),
            'visible' => $action->isVisible(),
            'color'   => $action->getColor(),
        ])->toArray();

        return [
            'Row Actions'           => $actions,
            'Bulk Actions'          => $bulkActions,
            'Header Actions'        => $headerActions,
            'Actions Position'      => $table->getActionsPosition(),
        ];
    }
}
