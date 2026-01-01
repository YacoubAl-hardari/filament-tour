<?php

namespace YacoubAlhaidari\FilamentTour\Services;

use Filament\Facades\Filament;
use YacoubAlhaidari\FilamentTour\Concerns\HasTourSteps;

class TourStepCollector
{
    /**
     * Collect tour steps from all registered resources
     */
    public static function collectSteps(): array
    {
        $steps = [];
        $panel = Filament::getCurrentPanel();

        if (!$panel) {
            return $steps;
        }

        foreach ($panel->getResources() as $resource) {
            // Check if resource uses HasTourSteps trait
            if (!in_array(HasTourSteps::class, class_uses_recursive($resource))) {
                continue;
            }

            if (!$resource::hasTourStep()) {
                continue;
            }

            $stepId = $resource::getTourStepId();
            $title = $resource::getTourStepTitle();
            $description = $resource::getTourStepDescription();
            $features = $resource::getTourStepFeatures();
            $position = $resource::getTourStepPosition();
            $sort = $resource::getTourStepSort();

            // Get resource URL
            $url = null;
            try {
                $url = $resource::getUrl('index');
            } catch (\Exception $e) {
                // Resource might not have index page
            }

            // Build step text
            $text = static::buildStepText($description, $features);

            $steps[] = [
                'id' => $stepId,
                'title' => $title,
                'text' => $text,
                'attachTo' => '[data-tour="' . $stepId . '"]',
                'position' => $position,
                'sort' => $sort,
                'url' => $url,
                'buttons' => [
                    ['text' => 'السابق', 'action' => 'back', 'secondary' => true],
                    ['text' => 'التالي', 'action' => 'next', 'secondary' => false],
                ],
            ];
        }

        // Sort steps by sort order (ascending)
        usort($steps, function ($a, $b) {
            return $a['sort'] <=> $b['sort'];
        });

        return $steps;
    }

    /**
     * Build step text from description and features
     */
    protected static function buildStepText(?string $description, array $features): string
    {
        $text = '<br>';

        if ($description) {
            $text .= $description . '<br><br>';
        }

        if (!empty($features)) {
            $text .= 'يمكنك:<br>';
            foreach ($features as $feature) {
                $text .= '• ' . $feature . '<br>';
            }
        }

        return $text;
    }

    /**
     * Get navigation map for data-tour attributes
     */
    public static function getNavigationMap(): array
    {
        $map = [];
        $panel = Filament::getCurrentPanel();

        if (!$panel) {
            return $map;
        }

        foreach ($panel->getResources() as $resource) {
            if (!in_array(HasTourSteps::class, class_uses_recursive($resource))) {
                continue;
            }

            if (!$resource::hasTourStep()) {
                continue;
            }

            $stepId = $resource::getTourStepId();
            $label = $resource::getNavigationLabel();

            $map[$stepId] = $label;
        }

        return $map;
    }
}

