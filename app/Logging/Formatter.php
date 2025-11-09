<?php

declare(strict_types=1);

namespace App\Logging;

class Formatter
{
    public function __invoke($logger)
    {
        foreach ($logger->getHandlers() as $handler) {
            $formatter = new LineFormatter(null, null, true, true);
            $handler->setFormatter($formatter);
        }
    }
}
