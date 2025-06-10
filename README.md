# LaraDumps Filament

This package provides a simple way to integrate LaraDumps with Filament.

## Installation

You can install the package via composer:

```bash
composer require --dev andrefelipe18/laradumps-filament
```

Once installed, you can use the `LaraDumpsPlugin` in your PanelProvider:

```php
use LaraDumpsFilament\LaraDumpsPlugin;

class MyPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->plugins([
                LaraDumpsPlugin::make(),
            ]);
    }
}
```

## Usage

The LaraDumps Filament package provides debugging capabilities for Filament components through the `ds()` method. This method is available on form fields, tables, and also provides JavaScript debugging capabilities.

> **Important:** The `ds()` method only works in local environment. In production, the method calls are safely ignored.

### 🔧 Form Fields Debugging

The `ds()` method can be applied to any Filament form field to automatically capture and send its state changes to LaraDumps.

#### Basic Usage

```php
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;

TextInput::make('name')
    ->label('Full Name')
    ->required()
    ->ds(), // Debug field changes

Select::make('status')
    ->label('User Status')
    ->options([
        'active' => 'Active',
        'inactive' => 'Inactive',
    ])
    ->ds(onBlur: false, debounce: 0, color: 'green'),
```

#### Advanced Configuration

The `ds()` method accepts several parameters to customize its behavior:

```php
->ds(
    bool $onBlur = true,         // Trigger on blur event (default: true)
    ?int $debounce = null,       // Debounce delay in milliseconds
    string $color = 'orange'     // LaraDumps color label (default: 'orange')
)
```

> **Important:** Always call `->ds()` at the end of the field definition chain to ensure it captures the final configuration.

### 📊 Table Debugging

The `ds()` method on Filament tables provides comprehensive debugging information about the table's configuration, query performance, and structure.

#### Basic Usage

```php
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;

public function table(Table $table): Table
{
    return $table
        ->columns([
            TextColumn::make('name')
                ->label('Name')
                ->sortable()
                ->searchable(),

            // ...
        ])
        ->filters([
            SelectFilter::make('status')
                ->options([
                    'active' => 'Active',
                    'inactive' => 'Inactive',
                ]),
        ])
        ->actions([
            // ... your actions
        ])
        ->ds(color: 'green'); // Debug table configuration and performance
}
```

### 🚀 JavaScript Debugging

The package automatically injects LaraDumps JavaScript integration, allowing you to use the `$ds` magic method in your Blade templates and Alpine.js components.

#### Basic Usage

```blade
<div x-data="{ message: 'Hello World' }">
    <button
        x-on:click="$ds(message)"
        type="button"
    >
        Debug Message
    </button>
</div>
```

## Troubleshooting

### Common Issues

1. **Missing JavaScript debugging**: Verify that the plugin is registered in your Panel Provider.

### Debug Information Not Showing

Make sure:

-   Your application is running in local environment (`APP_ENV=local`)
-   The plugin is registered in your Panel Provider

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
