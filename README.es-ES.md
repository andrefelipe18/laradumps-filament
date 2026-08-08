

# LaraDumps Filament

Este paquete ofrece una forma sencilla de integrar LaraDumps con Filament.

## Instalación

Puedes instalar el paquete a través de Composer:

```bash
composer require --dev andrefelipe18/laradumps-filament
```

Una vez instalado, puedes usar el `LaraDumpsPlugin` en tu PanelProvider:

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

## Uso

El paquete LaraDumps Filament proporciona capacidades de depuración para componentes de Filament a través del método `ds()`. Este método está disponible en campos de formularios, tablas y también ofrece capacidades de depuración para JavaScript.

> **Importante:** El método `ds()` solo funciona en el entorno local. En producción, las llamadas al método se ignoran de forma segura.

### 🔧 Depuración de Campos de Formulario

El método `ds()` puede aplicarse a cualquier campo de formulario de Filament para capturar y enviar automáticamente los cambios de su estado a LaraDumps.

#### Uso Básico

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

#### Configuración Avanzada

El método `ds()` acepta varios parámetros para personalizar su comportamiento:

```php
->ds(
    bool $onBlur = true,         // Trigger on blur event (default: true)
    ?int $debounce = null,       // Debounce delay in milliseconds
    string $color = 'orange'     // LaraDumps color label (default: 'orange')
)
```

> **Importante:** Llama siempre a `->ds()` al final de la cadena de definición del campo para garantizar que capture la configuración final.

### Atributo `DumpFormState`

También puedes usar el atributo `DumpFormState` para volcar automáticamente el estado del formulario cuando se envíe. 
Esto es útil para depurar el estado completo del formulario de una sola vez.

```php

// Dumping Create/Edit Resource Pages
#[\LaraDumpsFilament\Attributes\DumpFormState]
class CreateUser extends CreateRecord
{
   // ...
}


// Dumping Custom Pages
#[\LaraDumpsFilament\Attributes\DumpFormState('customStatePath')]
class CustomPage extends Page
{
    public array $customStatePath = []; // You can define a custom state path if needed

    public function form(Form $form): Form
    {
        return $form
            ->statePath('customStatePath')
            ->schema([
                // ... other fields
            ]);
    }
}
```

> **Importante:** El atributo `DumpFormState` solo captura campos reactivos (aquellos definidos con `->live()`). Los campos no reactivos no se incluirán en el volcado.

### 📊 Depuración de Tablas

El método `ds()` en las tablas de Filament proporciona información detallada de depuración sobre la configuración de la tabla, el rendimiento de las consultas y su estructura.

#### Uso Básico

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

### 🚀 Depuración en JavaScript

El paquete inyecta automáticamente la integración de JavaScript de LaraDumps, permitiéndote usar el método mágico `$ds` en tus plantillas Blade y componentes Alpine.js.

#### Uso Básico

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

## Solución de Problemas

### Problemas Comunes

1. **Depuración de JavaScript ausente**: Verifica que el plugin esté registrado en tu Panel Provider.

### La Información de Depuración No se Muestra

Asegúrate de que:

-   Tu aplicación se esté ejecutando en el entorno local (`APP_ENV=local`)
-   El plugin esté registrado en tu Panel Provider

## Licencia

Licencia MIT (MIT). Por favor, consulta el [Archivo de Licencia](LICENSE.md) para más información.
