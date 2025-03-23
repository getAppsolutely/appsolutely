<?php

namespace App\Services;

use App\Models\Translation;
use App\Repositories\TranslationRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class TranslationService
{
    protected TranslationRepository $translationRepository;

    protected string $cacheKey;

    public function __construct(TranslationRepository $translationRepository)
    {
        $this->translationRepository = $translationRepository;
        $this->cacheKey              = appsolutely() . '.translations';
    }

    /**
     * Translate a string
     *
     * @param  string  $text  The original text to translate
     * @param  string|null  $locale  The target locale
     * @param  string  $type  The source type (php, blade, db)
     * @param  string|null  $callStack  The call stack information
     * @return string The translated text or original if not found
     */
    public function translate(string $text, ?string $locale = null, string $type = 'php', ?string $callStack = null): string
    {
        if (empty($text)) {
            return $text;
        }

        // Use current locale if not specified
        $locale = $locale ?? app()->getLocale();

        // Don't translate if using default locale
        if ($locale === config('app.locale')) {
            return $text;
        }

        // Check cache first
        $cacheKey       = $this->getCacheKey($text, $locale);
        $translatedText = Cache::get($cacheKey);

        if ($translatedText !== null) {
            return $translatedText;
        }

        // Look up in database
        $translation = $this->translationRepository->findByOriginalText($text, $locale);

        if ($translation) {
            // Update usage statistics
            $this->translationRepository->incrementUsage($translation);

            // Cache the result
            $this->cacheTranslation($text, $translation->translated_text ?? $text, $locale);

            return $translation->translated_text ?? $text;
        }

        // Create a new translation entry
        $translation = $this->translationRepository->create([
            'locale'          => $locale,
            'type'            => $type,
            'original_text'   => $text,
            'translated_text' => null, // Will be filled by translators
            'translator'      => null, // Store translator source
            'call_stack'      => $callStack,
            'used_count'      => 1,
            'last_used'       => Carbon::now(),
        ]);

        // Cache the original text as fallback
        $this->cacheTranslation($text, $text, $locale);

        return $text;
    }

    /**
     * Get the cache key for a translation
     */
    protected function getCacheKey(string $text, string $locale): string
    {
        return $this->cacheKey . '.' . $locale . '.' . md5($text);
    }

    /**
     * Cache a translation
     */
    public function cacheTranslation(string $original, string $translated, string $locale): void
    {
        $cacheKey = $this->getCacheKey($original, $locale);
        Cache::put($cacheKey, $translated, now()->addDay());
    }

    /**
     * Clear the translation cache
     */
    public function clearCache(): void
    {
        Cache::forget($this->cacheKey);
    }

    /**
     * Get all missing translations
     */
    public function getMissingTranslations(?string $locale = null): array
    {
        return $this->translationRepository->getMissingTranslations($locale);
    }

    /**
     * Update a translation with a new translated text and translator
     *
     * @param  int  $id  The translation ID
     * @param  string  $translatedText  The new translated text
     * @param  string  $translator  The translator source (Google, DeepSeek, OpenAI, Manual)
     * @return bool True if successful
     */
    public function updateTranslation(int $id, string $translatedText, string $translator): bool
    {
        $translation = $this->translationRepository->find($id);

        if (! $translation) {
            return false;
        }

        $result = $this->translationRepository->update($translation, [
            'translated_text' => $translatedText,
            'translator'      => $translator,
        ]);

        if ($result) {
            // Update cache
            $this->cacheTranslation($translation->original_text, $translatedText, $translation->locale);
        }

        return $result;
    }
}
