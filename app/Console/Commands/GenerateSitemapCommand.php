<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Services\SitemapService;
use Illuminate\Console\Command;

/**
 * Command to generate sitemap.xml
 *
 * Can be run manually or scheduled to regenerate periodically
 */
final class GenerateSitemapCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sitemap:generate
                           {--force : Force regeneration by clearing cache first}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate XML sitemap for SEO (regenerates cached sitemap)';

    /**
     * Execute the console command.
     */
    public function handle(SitemapService $sitemapService): int
    {
        $force = $this->option('force');

        if ($force) {
            $this->info('🗑️  Clearing sitemap cache...');
            $sitemapService->clearCache();
        }

        $this->info('🗺️  Generating sitemap.xml...');

        $startTime = microtime(true);
        $xml       = $sitemapService->generateXml();
        $endTime   = microtime(true);

        $duration = round(($endTime - $startTime) * 1000, 2);
        $urlCount = substr_count($xml, '<url>');

        $this->info('✅ Sitemap generated successfully!');
        $this->line("   URLs included: {$urlCount}");
        $this->line("   Generation time: {$duration}ms");
        $this->line('   Size: ' . number_format(strlen($xml)) . ' bytes');

        return Command::SUCCESS;
    }
}
