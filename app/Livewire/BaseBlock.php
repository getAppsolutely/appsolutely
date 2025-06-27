<?php

declare(strict_types=1);

namespace App\Livewire;

use Carbon\Carbon;
use Livewire\Component;

abstract class BaseBlock extends Component
{
    /**
     * The data passed to the component.
     */
    public array $data = [];

    /**
     * The view name to render (without the 'livewire.' prefix).
     */
    protected string $viewName = '';

    /**
     * Published date for the block.
     */
    protected ?Carbon $publishedAt = null;

    /**
     * Expired date for the block.
     */
    protected ?Carbon $expiredAt = null;

    /**
     * Mount the component with data.
     *
     * @param  array  $data  The data to pass to the component
     */
    public function mount(array $data = []): void
    {
        $this->data = $data;
        $this->initializeComponent();
        $this->initializePublishDates();
    }

    /**
     * Initialize the component after mounting.
     * Override this method in child classes to add custom initialization logic.
     */
    protected function initializeComponent(): void
    {
        // Override in child classes if needed
        $this->data = array_merge($this->defaultConfig(), $this->data);
    }

    /**
     * Initialize publish dates from data.
     */
    protected function initializePublishDates(): void
    {
        $this->publishedAt = $this->getData('published_at')
            ? Carbon::parse($this->getData('published_at'))
            : null;

        $this->expiredAt = $this->getData('expired_at')
            ? Carbon::parse($this->getData('expired_at'))
            : null;
    }

    /**
     * Check if the block should be visible based on publish dates.
     */
    public function isVisible(): bool
    {
        $now = now();

        // If no published_at is set, block is always visible
        if (! $this->publishedAt) {
            return true;
        }

        // Check if current time is after or equal to published_at
        if ($now->lt($this->publishedAt)) {
            return false;
        }

        // If expired_at is null, block is visible after published_at
        if (! $this->expiredAt) {
            return true;
        }

        // Check if current time is before or equal to expired_at
        return $now->lte($this->expiredAt);
    }

    protected function defaultConfig(): array
    {
        return [];
    }

    /**
     * Get the view name for this component.
     * Override this method in child classes to specify a custom view.
     */
    protected function getViewName(): string
    {
        if (empty($this->viewName)) {
            // Auto-generate view name from class name
            $className      = class_basename($this);
            $this->viewName = \Str::kebab($className);
        }

        return $this->viewName;
    }

    /**
     * Render the component.
     */
    public function render()
    {
        // Check if block should be visible
        if (! $this->isVisible()) {
            return '<span></span>';
        }

        $viewName = 'livewire.' . $this->getViewName();

        return themed_view($viewName, array_merge($this->getExtraData()),
            ['data' => $this->data]);
    }

    protected function getExtraData(): array
    {
        return [];
    }

    /**
     * Get a value from the data array.
     *
     * @param  mixed  $default
     * @return mixed
     */
    protected function getData(string $key, $default = null)
    {
        return $this->data[$key] ?? $default;
    }

    /**
     * Set a value in the data array.
     *
     * @param  mixed  $value
     */
    protected function setData(string $key, $value): void
    {
        $this->data[$key] = $value;
    }

    /**
     * Check if a key exists in the data array.
     */
    protected function hasData(string $key): bool
    {
        return isset($this->data[$key]);
    }

    public function paginationView()
    {
        return 'pagination.bootstrap';
    }
}
