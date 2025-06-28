<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * GeneralPage - A unified wrapper class for all page types
 *
 * Can handle:
 * 1. Root pages (regular Page models with no parent)
 * 2. Nested pages (nested content with a parent Page)
 *
 * This allows controllers to treat all page types uniformly
 */
class GeneralPage
{
    protected ?Page $parentPage;

    protected Model $content; // Can be Page or any nested content model

    protected ?string $childSlug;

    public function __construct(
        Model $content,
        ?Page $parentPage = null,
        ?string $childSlug = null
    ) {
        $this->content    = $content;
        $this->parentPage = $parentPage;
        $this->childSlug  = $childSlug;
    }

    /**
     * Check if this is a nested page (has parent page)
     */
    public function isNested(): bool
    {
        return $this->parentPage !== null;
    }

    /**
     * Check if this is a root page (no parent page)
     */
    public function isRoot(): bool
    {
        return $this->parentPage === null;
    }

    /**
     * Get the main content model (Page for root, nested content for nested)
     */
    public function getContent(): Model
    {
        return $this->content;
    }

    /**
     * Get the parent page (null for root pages)
     */
    public function getParentPage(): ?Page
    {
        return $this->parentPage;
    }

    /**
     * Get the child slug (null for root pages)
     */
    public function getChildSlug(): ?string
    {
        return $this->childSlug;
    }

    /**
     * Get the model class of the main content
     */
    public function getModelClass(): string
    {
        return get_class($this->content);
    }

    /**
     * Get the content type (derived from model class)
     */
    public function getContentType(): string
    {
        return strtolower(class_basename($this->content));
    }

    /**
     * Magic getter to provide page-like properties
     * Priority: main content > parent page (for nested) > defaults
     */
    public function __get(string $name)
    {
        return match ($name) {
            // Core page properties - prioritize main content
            'title'       => $this->getTitle(),
            'description' => $this->getDescription(),
            'keywords'    => $this->getKeywords(),
            'content'     => $this->getContentText(),
            'slug'        => $this->getSlug(),

            // Meta properties
            'meta_robots'     => $this->content->meta_robots ?? $this->parentPage?->meta_robots,
            'canonical_url'   => $this->getCanonicalUrl(),
            'og_title'        => $this->getOgTitle(),
            'og_description'  => $this->getOgDescription(),
            'og_image'        => $this->getOgImage(),
            'structured_data' => $this->getStructuredData(),
            'hreflang'        => $this->getHreflang(),
            'language'        => $this->getLanguage(),

            // Page structure - from parent page for nested, from content for root
            'blocks'    => $this->getBlocks(),
            'setting'   => $this->getSetting(),
            'parent_id' => $this->getParentId(),

            // Timestamps - from main content
            'published_at' => $this->content->published_at,
            'expired_at'   => $this->content->expired_at ?? null,
            'created_at'   => $this->content->created_at,
            'updated_at'   => $this->content->updated_at,

            // Status - from main content
            'status' => $this->content->status ?? 1,

            // IDs
            'id'             => $this->content->id,
            'parent_page_id' => $this->parentPage?->id,

            // Fallback to main content, then parent page
            default => $this->content->$name ?? $this->parentPage?->$name ?? null,
        };
    }

    /**
     * Magic isset to check if properties exist
     */
    public function __isset(string $name): bool
    {
        return isset($this->content->$name) || isset($this->parentPage?->$name);
    }

    /**
     * Get the title with optional parent page prefix for nested pages
     */
    protected function getTitle(): string
    {
        $title     = $this->content->title ?? '';
        $separator = config('appsolutely.seo.title_separator', ' | ');
        $siteName  = config('appsolutely.general.site_name');

        // For nested pages, optionally append parent title
        if ($this->isNested()) {
            $title = $title . $separator . $this->parentPage->title;
        }

        return $siteName ? $title . $separator . $siteName : $title;
    }

    /**
     * Get description - prioritize main content
     */
    protected function getDescription(): ?string
    {
        return $this->content->description ?? $this->parentPage?->description;
    }

    /**
     * Get keywords - merge content and parent page for nested
     */
    protected function getKeywords(): ?string
    {
        $contentKeywords = $this->content->keywords ?? '';
        $parentKeywords  = $this->parentPage?->keywords ?? '';

        if (empty($contentKeywords)) {
            return $parentKeywords;
        }

        if (empty($parentKeywords) || $this->isRoot()) {
            return $contentKeywords;
        }

        return $contentKeywords . ', ' . $parentKeywords;
    }

    /**
     * Get content text - from main content
     */
    protected function getContentText(): ?string
    {
        return $this->content->content ?? '';
    }

    /**
     * Get the full slug path
     */
    protected function getSlug(): string
    {
        if ($this->isRoot()) {
            return $this->content->slug ?? '/';
        }

        // For nested pages, combine parent slug with child slug
        $parentSlug = trim($this->parentPage->slug, '/');

        return $parentSlug . '/' . $this->childSlug;
    }

    /**
     * Get canonical URL
     */
    protected function getCanonicalUrl(): ?string
    {
        if (isset($this->content->canonical_url)) {
            return $this->content->canonical_url;
        }

        return app_url($this->getSlug());
    }

    /**
     * Get OG title
     */
    protected function getOgTitle(): ?string
    {
        return $this->content->og_title ?? $this->getTitle();
    }

    /**
     * Get OG description
     */
    protected function getOgDescription(): ?string
    {
        return $this->content->og_description ?? $this->getDescription();
    }

    /**
     * Get OG image
     */
    protected function getOgImage(): ?string
    {
        return $this->content->og_image ?? $this->parentPage?->og_image;
    }

    /**
     * Get structured data
     */
    protected function getStructuredData(): ?array
    {
        $contentData = $this->content->structured_data ?? [];
        $parentData  = $this->parentPage?->structured_data ?? [];

        if (empty($contentData)) {
            return $parentData ?: null;
        }

        return $contentData;
    }

    /**
     * Get hreflang
     */
    protected function getHreflang(): ?string
    {
        return $this->content->hreflang ?? $this->parentPage?->hreflang;
    }

    /**
     * Get language
     */
    protected function getLanguage(): ?string
    {
        return $this->content->language ?? $this->parentPage?->language;
    }

    /**
     * Get blocks - from parent page for nested, from content for root
     */
    protected function getBlocks()
    {
        if ($this->isNested()) {
            return $this->parentPage->blocks;
        }

        return $this->content->blocks ?? collect();
    }

    /**
     * Get setting - from parent page for nested, from content for root
     */
    protected function getSetting()
    {
        if ($this->isNested()) {
            return $this->parentPage->setting;
        }

        return $this->content->setting ?? null;
    }

    /**
     * Get parent ID
     */
    protected function getParentId(): ?int
    {
        if ($this->isNested()) {
            return $this->parentPage->parent_id;
        }

        return $this->content->parent_id ?? null;
    }

    /**
     * Blocks relationship - delegate to appropriate model
     */
    public function blocks(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        if ($this->isNested()) {
            return $this->parentPage->blocks();
        }

        return $this->content->blocks();
    }

    /**
     * Convert to array for debugging
     */
    public function toArray(): array
    {
        return [
            'type'           => $this->isNested() ? 'nested' : 'root',
            'content_type'   => $this->getContentType(),
            'content_id'     => $this->content->id,
            'parent_page_id' => $this->parentPage?->id,
            'child_slug'     => $this->childSlug,
            'title'          => $this->getTitle(),
            'slug'           => $this->getSlug(),
            'model'          => $this->getContent(),
        ];
    }
}
