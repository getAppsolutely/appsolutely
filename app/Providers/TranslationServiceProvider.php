<?php

namespace App\Providers;

use App\Services\Translation\DeepSeekTranslator;
use App\Services\Translation\OpenAITranslator;
use App\Services\Translation\TranslatorInterface;
use Illuminate\Support\ServiceProvider;

class TranslationServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Default binding for TranslatorInterface
        $this->app->bind(TranslatorInterface::class, function ($app) {
            // Get provider from config
            $provider = $app['config']->get('services.translation.provider', 'deepseek');

            // Create the appropriate translator instance
            return match ($provider) {
                'openai' => new OpenAITranslator(),
                default => new DeepSeekTranslator(),
            };
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
