<?php

declare(strict_types = 1);

namespace LaradumpsFilament;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class LaradumpsFilamentServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('laradumps-filament')
            ->hasViews();
    }
}
