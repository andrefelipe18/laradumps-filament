<?php

declare(strict_types = 1);

namespace LaraDumpsFilament;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class LaraDumpsFilamentServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('laradumps-filament')
            ->hasViews();
    }
}
