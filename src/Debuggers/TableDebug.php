<?php

declare(strict_types = 1);

namespace LaradumpsFilament\Debuggers;

use Illuminate\Database\Eloquent\Model;

class TableDebug
{
    public function __construct(
        // Query Information
        public ?string $query = null,
        public ?array $bindings = null,
        public ?string $rawSql = null,

        // Model Information
        public ?Model $model = null,

        // Scopes and Relationships
        public ?array $globalScopes = null,
        public ?array $localScopes = null,
        public ?array $relations = null,
        public ?array $eagerLoaded = null,

        // Table Configuration
        public ?string $modelLabel = null,
        public ?array $columns = null,
        public ?array $filters = null,
        public ?array $actions = null,
        public ?array $bulkActions = null,

        // Pagination & Performance
        public ?int $recordsPerPage = null,
        public ?int $totalRecords = null,
        public ?bool $paginationEnabled = null,
        public ?array $searchableColumns = null,
        public ?array $sortableColumns = null,

        // Performance Metrics
        public ?float $queryTime = null,
        public ?int $queryCount = null,
        public ?array $appliedFilters = null,

        // Debugging Context
        public ?string $calledFrom = null,
    ) {
    }
}
