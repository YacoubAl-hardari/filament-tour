<?php

namespace YacoubAlhaidari\FilamentTour;

use Filament\Support\Assets\Css;
use Filament\Support\Assets\Js;
use Filament\Support\Facades\FilamentAsset;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use YacoubAlhaidari\FilamentTour\Console\PublishAssetsCommand;

class FilamentTourServiceProvider extends PackageServiceProvider
{
    public static string $name = 'filament-tour';

    public function configurePackage(Package $package): void
    {
        $package
            ->name(static::$name)
            ->hasViews()
            ->hasCommand(PublishAssetsCommand::class);
    }

    public function packageBooted(): void
    {
        // Register CSS and JS assets
        $cssPath = __DIR__ . '/../resources/css/shepherd-tour.css';
        $distJsPath = __DIR__ . '/../resources/dist/filament-tour.js';
        $srcJsPath = __DIR__ . '/../resources/js/shepherd-tour.js';

        $assets = [
            Css::make('filament-tour-styles', $cssPath),
        ];

        if (file_exists($distJsPath)) {
            $assets[] = Js::make('filament-tour-scripts', $distJsPath)->module();
        } elseif (file_exists($srcJsPath)) {
            // Register source file (non-module) as a graceful fallback.
            $assets[] = Js::make('filament-tour-scripts', $srcJsPath);
        }

        if (! empty($assets)) {
            FilamentAsset::register($assets, package: 'yacoubalhaidari/filament-tour');
        }
    }
}

