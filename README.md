# Laradumps Filament

This package provides a simple way to integrate Laradumps with Filament.

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

Now you can use `$ds` magic in your Filament pages

```php
<div 
  x-init"$ds('Hello World')"
>
  <h1>Hello World</h1>
</div>
```