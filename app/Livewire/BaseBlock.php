<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Models\Model;
use Carbon\Carbon;
use Livewire\Component;

abstract class BaseBlock extends Component
{
    public array $page = [];

    public ?Model $model = null;

    public array $displayOptions = [];

    public string $style = 'default';

    public array $queryOptions = [];

    protected array $defaultDisplayOptions = [];

    protected array $defaultQueryOptions = [];

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
     * @param  array  $page  page data
     * @param  array  $data  The data to pass to the component
     */
    public function mount(array $page, array $data = []): void
    {
        $this->page           = $page;
        $this->model          = $page['model'] ?? null;
        $queryOptions         = $this->queryOptions ?? ($data['query_options'] ?? []);
        $displayOptions       = $this->displayOptions ?? ($data['display_options'] ?? []);
        $this->queryOptions   =  ! empty($this->defaultQueryOptions) ? $this->mergeByKey($this->defaultQueryOptions, $queryOptions) : $queryOptions;
        $this->displayOptions =  ! empty($this->defaultDisplayOptions) ? $this->mergeByKey($this->defaultDisplayOptions, $displayOptions) : $displayOptions;
        $this->style          = $displayOptions['style'] ?? $this->style;
        $this->initializeComponent(app());
        $this->initializePublishDates();
    }

    /**
     * Initialize the component after mounting.
     * Override this method in child classes to add custom initialization logic.
     *
     * @param  \Illuminate\Contracts\Container\Container  $container  The service container for dependency resolution
     */
    protected function initializeComponent(\Illuminate\Contracts\Container\Container $container): void
    {
        // Override in child classes if needed
    }

    protected function mergeByKey(array $default, array $data): array
    {
        return array_replace($default, array_intersect_key($data, $default));
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
        return array_merge($this->queryOptions, $this->displayOptions);
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
            $baseViewName   = \Str::kebab($className);

            if ($this->style && $this->style !== 'default') {
                $styleViewName = $baseViewName . '_' . $this->style;
                // Check if the style-specific view exists in the current theme
                $themedStyleView = themed_path() . '/views/livewire/' . $styleViewName . '.blade.php';
                if (file_exists(base_path($themedStyleView))) {
                    $this->viewName = $styleViewName;
                } else {
                    $this->viewName = $baseViewName;
                }
            } else {
                $this->viewName = $baseViewName;
            }
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

        return themed_view($viewName, $this->getExtraData());
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
        return $this->displayOptions[$key] ?? $default;
    }

    /**
     * Set a value in the data array.
     *
     * @param  mixed  $value
     */
    protected function setData(string $key, $value): void
    {
        $this->displayOptions[$key] = $value;
    }

    /**
     * Check if a key exists in the data array.
     */
    protected function hasData(string $key): bool
    {
        return isset($this->displayOptions[$key]);
    }

    public function paginationView(): string
    {
        return 'pagination.bootstrap';
    }
}
