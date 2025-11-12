<?php

namespace YacoubAlhaidari\FilamentTour\Console;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class PublishAssetsCommand extends Command
{
    protected $signature = 'filament-tour:assets';

    protected $description = 'Publish Filament Tour assets to the application public directory';

    public function handle(Filesystem $files)
    {
        $base = __DIR__ . '/../../resources';
        $cssSrc = $base . '/css/shepherd-tour.css';
        $distJs = $base . '/dist/filament-tour.js';
        $srcJs = $base . '/js/shepherd-tour.js';

        $public = public_path();
        $vendor = 'yacoubalhaidari';
        $package = 'filament-tour';

        $cssDestDir = $public . "/css/{$vendor}/{$package}";
        $jsDestDir = $public . "/js/{$vendor}/{$package}";

        if (! $files->exists($cssSrc) && ! $files->exists($distJs) && ! $files->exists($srcJs)) {
            $this->error('No assets found to publish.');
            return 1;
        }

        if (! $files->exists($cssDestDir)) {
            $files->makeDirectory($cssDestDir, 0755, true, true);
        }

        if (! $files->exists($jsDestDir)) {
            $files->makeDirectory($jsDestDir, 0755, true, true);
        }

        if ($files->exists($cssSrc)) {
            $cssDest = $cssDestDir . '/filament-tour-styles.css';
            $files->copy($cssSrc, $cssDest);
            $this->info("Published CSS: {$cssDest}");
        }

        $jsSrcToUse = $files->exists($distJs) ? $distJs : ($files->exists($srcJs) ? $srcJs : null);
        if ($jsSrcToUse) {
            $jsDest = $jsDestDir . '/filament-tour-scripts.js';
            $files->copy($jsSrcToUse, $jsDest);
            $this->info("Published JS: {$jsDest}");
        }

        $this->info('filament-tour assets published successfully.');

        return 0;
    }
}
