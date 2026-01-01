<?php

namespace YacoubAlhaidari\FilamentTour\Concerns;

trait HasTourSteps
{
    /**
     * Get tour steps for this resource
     */
    public static function getTourSteps(): array
    {
        return [];
    }

    /**
     * Get the tour step ID for this resource
     */
    public static function getTourStepId(): ?string
    {
        return null;
    }

    /**
     * Get the tour step title
     */
    public static function getTourStepTitle(): ?string
    {
        return static::getModelLabel();
    }

    /**
     * Get the tour step description
     */
    public static function getTourStepDescription(): ?string
    {
        return null;
    }

    /**
     * Get the tour step features
     */
    public static function getTourStepFeatures(): array
    {
        return [];
    }

    /**
     * Get the tour step position
     */
    public static function getTourStepPosition(): string
    {
        return 'right';
    }

    /**
     * Get the tour step sort order
     * Lower numbers appear first in the tour
     */
    public static function getTourStepSort(): int
    {
        return 100; // Default sort order
    }

    /**
     * Get the data-tour selector for this resource
     */
    public static function getTourSelector(): ?string
    {
        return static::getTourStepId();
    }

    /**
     * Check if this resource should have a tour step
     */
    public static function hasTourStep(): bool
    {
        return !empty(static::getTourStepId());
    }
}

