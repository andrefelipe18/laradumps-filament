<?php

declare(strict_types = 1);

namespace LaraDumpsFilament\Debuggers;

use Filament\Forms\Components\Concerns\CanAllowHtml;
use Filament\Forms\Components\Concerns\CanBeNative;
use Filament\Forms\Components\Concerns\CanBePreloaded;
use Filament\Forms\Components\Concerns\CanBeReadOnly;
use Filament\Forms\Components\Concerns\CanDisableOptions;
use Filament\Forms\Components\Concerns\CanLimitItemsLength;
use Filament\Forms\Components\Concerns\HasAffixes;
use Filament\Forms\Components\Concerns\HasExtraInputAttributes;
use Filament\Forms\Components\Concerns\HasInputMode;
use Filament\Forms\Components\Concerns\HasLoadingMessage;
use Filament\Forms\Components\Concerns\HasOptions;
use Filament\Forms\Components\Concerns\HasPlaceholder;
use Filament\Forms\Components\Concerns\HasStep;
use Filament\Forms\Components\Field;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Get;
use Filament\Support\Concerns\HasExtraAlpineAttributes;
use Filament\Tables\Columns\Concerns\CanBeSearchable;
use LaraDumpsFilament\Helpers\TraitChecker;

class FieldDebug extends BaseDebug
{
    public function __construct(
        // State
        public mixed $oldValue = null,
        public mixed $newValue = null,
        public ?array $properties = null,
        public ?array $attributes = null,
        public ?array $validation = null,
        public ?array $livewire = null,
        public ?array $extras = null,
    ) {
    }

    public static function mountValidation(Field $field): array
    {
        return [
            'Rules'    => $field->getValidationRules(),
            'Messages' => $field->getValidationMessages(),
        ];
    }

    public static function mountProperties(Field $field): array
    {
        $properties = collect([
            'Field Name' => $field->getName(),
            'Field ID'   => $field->getId(),
        ]);

        $traitChecker = new TraitChecker($field);

        /** @var Select $field */
        if ($traitChecker->uses(HasOptions::class)) {
            $properties->put('Options', $field->getOptions());
        }

        /** @var Select $field */
        if ($traitChecker->uses(CanDisableOptions::class)) {
            $properties->put('Enabled Options', $field->getEnabledOptions());
        }

        /** @var Select $field */
        if ($traitChecker->uses(CanLimitItemsLength::class)) {
            $properties->put('Items Counts', $field->getItemsCount());
        }

        return $properties->filter()->toArray();
    }

    public static function mountAttributes(Field $field): array
    {
        $attributes = collect([
            'Label'       => $field->getLabel(),
            'Helper Text' => $field->getHelperText(),
            'Hint'        => $field->getHint(),
        ]);

        $traitChecker = new TraitChecker($field);

        /** @var TextInput $field */
        if ($traitChecker->uses(HasPlaceholder::class)) {
            $attributes->put('Placeholder', $field->getPlaceholder());
        }

        /** @var TextInput $field */
        if ($traitChecker->uses(HasAffixes::class)) {
            $attributes->put('Prefix Icon', $field->getPrefixIcon());
            $attributes->put('Suffix Icon', $field->getSuffixIcon());
            $attributes->put('Prefix Label', $field->getPrefixLabel());
            $attributes->put('Suffix Label', $field->getSuffixLabel());
            $attributes->put('Prefix Actions', $field->getPrefixActions());
            $attributes->put('Suffix Actions', $field->getSuffixActions());
        }

        /** @var TextInput $field */
        if ($traitChecker->uses(CanBeReadOnly::class)) {
            $attributes->put('Is Read Only', $field->isReadOnly());
        }

        if ($traitChecker->uses(HasStep::class)) {
            $attributes->put('Step', $field->getStep());
        }

        if ($traitChecker->uses(HasInputMode::class)) {
            $attributes->put('Input Mode', $field->getInputMode());
        }

        /** @var Select $field */
        if ($traitChecker->uses(CanAllowHtml::class)) {
            $attributes->put('Allows HTML', $field->isHtmlAllowed());
        }

        /** @var Select $field */
        if ($traitChecker->uses(CanBeNative::class)) {
            $attributes->put('Native', $field->isNative());
        }

        /** @var Select $field */
        if ($traitChecker->uses(CanBePreloaded::class)) {
            $attributes->put('Preloaded', $field->isPreloaded());
        }

        /** @var Select $field */
        if ($traitChecker->uses(CanBeSearchable::class)) {
            $attributes->put('Searchable', $field->isSearchable());
        }

        /** @var Select $field */
        if ($traitChecker->uses(CanDisableOptions::class)) {
            $attributes->put('Disabled Options', $field->hasDynamicDisabledOptions());
        }

        /** @var Select $field */
        if ($traitChecker->uses(CanLimitItemsLength::class)) {
            $attributes->put('Max Items', $field->getMaxItems());
            $attributes->put('Min Items', $field->getMinItems());
        }

        /** @var Select $field */
        if ($traitChecker->uses(HasLoadingMessage::class)) {
            $attributes->put('Loading Message', $field->getLoadingMessage());
        }

        return $attributes->filter()
            ->sortKeys()
            ->toArray();
    }

    public static function mountLivewireComponent(Field $field, Get $get): array
    {
        $livewireComponent = app('livewire')->current();

        if (! $livewireComponent) {
            return [];
        }

        return [
            'Livewire Component' => $livewireComponent->getName(),
            'Livewire Class'     => $livewireComponent::class,
            'Form Data'          => $get('') ?: [],
        ];
    }

    public static function mountExtras(Field $field): array
    {
        $extras = collect([]);

        $traitChecker = new TraitChecker($field);

        $extras->put('Wrapper Attributes', $field->getExtraFieldWrapperAttributes());

        /** @var Select $field */
        if ($traitChecker->uses(HasExtraInputAttributes::class)) {
            $extras->put('Input Attributes', $field->getExtraInputAttributes());
        }

        /** @var Select $field */
        if ($traitChecker->uses(HasExtraAlpineAttributes::class)) {
            $extras->put('Alpine Attributes', $field->getExtraAlpineAttributes());
        }

        return $extras->filter()->toArray();
    }
}
