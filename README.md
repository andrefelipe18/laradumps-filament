# LaraDumps Filament

This package provides a simple way to integrate LaraDumps with Filament.

## Installation

You can install the package via composer:

```bash
composer require --dev andrefelipe18/laradumps-filament
```

Once installed, you can use the `LaradumpsPlugin` in your PanelProvider:

```php
use LaradumpsFilament\LaradumpsPlugin;

class MyPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->plugins([
                LaradumpsPlugin::make(),
            ]);
    }
}
```

## Usage

### Form Fields

Use the `ds()` method on any Filament form field to automatically send its state to LaraDumps when updated:

```php
use Filament\Forms\Components\TextInput;

TextInput::make('name')
    ->label('Name')
    ->ds(),
```

When the field's value is updated, the current state will be dumped with the field's label using LaraDumps.
You can customize the behavior of the `ds()` method by passing parameters:

> Important: Always call the `->ds()` method at the end of the field definition chain. This ensures it captures the final state and doesn’t override any subsequent configuration.

```php
->ds(
    bool $onBlur = true,         // Trigger on blur (default: true)
    ?int $debounce = null,       // Optional debounce delay in ms
    string $color = 'orange'     // LaraDumps color label (default: 'orange')
)
```

### Table

You can also use the `ds()` method on Filament tables to dump the current state of the table:

```php
use Filament\Tables\Table;

public function table(Table $table): Table
{
    return $table
        ->columns([
            // ...
        ])
        ->ds(); // Dumps table
}
```

> Important: Always call the `->ds()` method at the end of the field definition chain. This ensures it captures the final state and doesn’t override any subsequent configuration.

### JavaScript
Now you can use `$ds` magic in your Filament pages

```php
<div 
  x-init"$ds('Hello World')"
>
  <h1>Hello World</h1>
</div>
```
