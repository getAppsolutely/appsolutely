<?php

declare(strict_types=1);

namespace App\Http\Livewire;

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
     * Mount the component with data.
     *
     * @param  array  $data  The data to pass to the component
     */
    public function mount(array $data = []): void
    {
        $this->data = $data;
        $this->initializeComponent();
    }

    /**
     * Initialize the component after mounting.
     * Override this method in child classes to add custom initialization logic.
     */
    protected function initializeComponent(): void
    {
        // Override in child classes if needed
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
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        $viewName = 'livewire.' . $this->getViewName();

        return themed_view($viewName, [
            'data' => $this->data,
        ]);
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
}
