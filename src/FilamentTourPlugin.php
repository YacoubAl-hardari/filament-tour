<?php

namespace YacoubAlhaidari\FilamentTour;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Filament\Support\Facades\FilamentView;
use Filament\View\PanelsRenderHook;
use Illuminate\Support\Facades\Blade;

class FilamentTourPlugin implements Plugin
{
    protected bool $showTourButton = true;
    
    protected string $tourButtonIcon = 'heroicon-o-academic-cap';
    
    protected string $tourButtonColor = 'info';
    
    protected string $tourButtonTooltip = 'الجولة التعريفية';

    protected ?array $welcomeStep = null;

    protected ?array $finishStep = null;

    protected ?string $headerColor = null;

    protected ?string $primaryButtonColor = null;

    protected ?string $secondaryButtonColor = null;

    protected ?string $textColor = null;

    protected ?string $backgroundColor = null;

    protected ?string $contentBackgroundColor = null;

    protected ?string $primaryButtonHoverColor = null;

    protected ?string $secondaryButtonHoverColor = null;

    protected ?string $footerBackgroundColor = null;

    protected ?string $primaryButtonTextColor = null;

    protected ?string $secondaryButtonTextColor = null;

    protected ?string $footerBorderColor = null;

    public static function make(): static
    {
        return app(static::class);
    }

    public static function get(): static
    {
        return filament(app(static::class)->getId());
    }

    public function getId(): string
    {
        return 'filament-tour';
    }

    public function register(Panel $panel): void
    {
        // Register render hooks
        if ($this->showTourButton) {
            $panel->renderHook(
                PanelsRenderHook::USER_MENU_BEFORE,
                fn (): string => Blade::render(
                    '<x-filament::icon-button 
                        icon="' . $this->tourButtonIcon . '" 
                        color="' . $this->tourButtonColor . '" 
                        data-shepherd-tour-trigger 
                        tooltip="' . $this->tourButtonTooltip . '" 
                    />'
                )
            );
        }

        $panel->renderHook(
            PanelsRenderHook::BODY_START,
            fn (): string => view('filament-tour::tour-trigger', [
                'navigationMap' => \YacoubAlhaidari\FilamentTour\Services\TourStepCollector::getNavigationMap(),
                'tourSteps' => \YacoubAlhaidari\FilamentTour\Services\TourStepCollector::collectSteps(),
                'welcomeStep' => $this->getWelcomeStep(),
                'finishStep' => $this->getFinishStep(),
                'headerColor' => $this->getHeaderColor(),
                'primaryButtonColor' => $this->getPrimaryButtonColor(),
                'secondaryButtonColor' => $this->getSecondaryButtonColor(),
                'textColor' => $this->getTextColor(),
                'backgroundColor' => $this->getBackgroundColor(),
                'contentBackgroundColor' => $this->getContentBackgroundColor(),
                'primaryButtonHoverColor' => $this->getPrimaryButtonHoverColor(),
                'secondaryButtonHoverColor' => $this->getSecondaryButtonHoverColor(),
                'footerBackgroundColor' => $this->getFooterBackgroundColor(),
                'primaryButtonTextColor' => $this->getPrimaryButtonTextColor(),
                'secondaryButtonTextColor' => $this->getSecondaryButtonTextColor(),
                'footerBorderColor' => $this->getFooterBorderColor(),
            ])->render()
        );
    }

    public function boot(Panel $panel): void
    {
        //
    }

    public function showTourButton(bool $condition = true): static
    {
        $this->showTourButton = $condition;

        return $this;
    }

    public function tourButtonIcon(string $icon): static
    {
        $this->tourButtonIcon = $icon;

        return $this;
    }

    public function tourButtonColor(string $color): static
    {
        $this->tourButtonColor = $color;

        return $this;
    }

    public function tourButtonTooltip(string $tooltip): static
    {
        $this->tourButtonTooltip = $tooltip;

        return $this;
    }

    public function getTourButtonIcon(): string
    {
        return $this->tourButtonIcon;
    }

    public function getTourButtonColor(): string
    {
        return $this->tourButtonColor;
    }

    public function getTourButtonTooltip(): string
    {
        return $this->tourButtonTooltip;
    }

    public function shouldShowTourButton(): bool
    {
        return $this->showTourButton;
    }

    public function welcomeStep(array $step): static
    {
        $this->welcomeStep = $step;

        return $this;
    }

    public function finishStep(array $step): static
    {
        $this->finishStep = $step;

        return $this;
    }

    public function getWelcomeStep(): ?array
    {
        return $this->welcomeStep;
    }

    public function getFinishStep(): ?array
    {
        return $this->finishStep;
    }

    public function headerColor(string $color): static
    {
        $this->headerColor = $color;

        return $this;
    }

    public function primaryButtonColor(string $color): static
    {
        $this->primaryButtonColor = $color;

        return $this;
    }

    public function secondaryButtonColor(string $color): static
    {
        $this->secondaryButtonColor = $color;

        return $this;
    }

    public function textColor(string $color): static
    {
        $this->textColor = $color;

        return $this;
    }

    public function backgroundColor(string $color): static
    {
        $this->backgroundColor = $color;

        return $this;
    }

    public function contentBackgroundColor(string $color): static
    {
        $this->contentBackgroundColor = $color;

        return $this;
    }

    public function primaryButtonHoverColor(string $color): static
    {
        $this->primaryButtonHoverColor = $color;

        return $this;
    }

    public function secondaryButtonHoverColor(string $color): static
    {
        $this->secondaryButtonHoverColor = $color;

        return $this;
    }

    public function footerBackgroundColor(string $color): static
    {
        $this->footerBackgroundColor = $color;

        return $this;
    }

    public function primaryButtonTextColor(string $color): static
    {
        $this->primaryButtonTextColor = $color;

        return $this;
    }

    public function secondaryButtonTextColor(string $color): static
    {
        $this->secondaryButtonTextColor = $color;

        return $this;
    }

    public function footerBorderColor(string $color): static
    {
        $this->footerBorderColor = $color;

        return $this;
    }

    public function getHeaderColor(): ?string
    {
        return $this->headerColor;
    }

    public function getPrimaryButtonColor(): ?string
    {
        return $this->primaryButtonColor;
    }

    public function getSecondaryButtonColor(): ?string
    {
        return $this->secondaryButtonColor;
    }

    public function getTextColor(): ?string
    {
        return $this->textColor;
    }

    public function getBackgroundColor(): ?string
    {
        return $this->backgroundColor;
    }

    public function getContentBackgroundColor(): ?string
    {
        return $this->contentBackgroundColor;
    }

    public function getPrimaryButtonHoverColor(): ?string
    {
        return $this->primaryButtonHoverColor;
    }

    public function getSecondaryButtonHoverColor(): ?string
    {
        return $this->secondaryButtonHoverColor;
    }

    public function getFooterBackgroundColor(): ?string
    {
        return $this->footerBackgroundColor;
    }

    public function getPrimaryButtonTextColor(): ?string
    {
        return $this->primaryButtonTextColor;
    }

    public function getSecondaryButtonTextColor(): ?string
    {
        return $this->secondaryButtonTextColor;
    }

    public function getFooterBorderColor(): ?string
    {
        return $this->footerBorderColor;
    }
}

