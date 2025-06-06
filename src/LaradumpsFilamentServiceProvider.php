<?php

namespace LaradumpsFilament;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use LaradumpsFilament\LaradumpsFilament\Commands\LaradumpsFilamentCommand;

class LaradumpsFilamentServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('laradumps-filament')
            ->hasViews();
    }
}
