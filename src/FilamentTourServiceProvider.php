<?php

namespace YacoubAlhaidari\FilamentTour;

use Filament\Support\Assets\Css;
use Filament\Support\Assets\Js;
use Filament\Support\Facades\FilamentAsset;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class FilamentTourServiceProvider extends PackageServiceProvider
{
    public static string $name = 'filament-tour';

    public function configurePackage(Package $package): void
    {
        $package
            ->name(static::$name)
            ->hasViews();
    }

    public function packageBooted(): void
    {
        // Register CSS and JS assets
        FilamentAsset::register([
            Css::make('filament-tour-styles', __DIR__ . '/../resources/css/shepherd-tour.css'),
            Js::make('filament-tour-scripts', __DIR__ . '/../resources/dist/filament-tour.js')
                ->module(), // Load as ES Module
        ], package: 'yacoubalhaidari/filament-tour');
    }
}

