<?php

namespace App\Logging;

use Monolog\Formatter\LineFormatter;

class Formatter
{
    /**
     * Customize the given logger instance.
     *
     * @param  \Illuminate\Log\Logger  $logger
     * @return void
     */
    public function __invoke($logger)
    {
        $requestId = app('request_id');
        $SIMPLE_FORMAT = "[%datetime%][$requestId] %channel%.%level_name%: %message% %context% %extra%\n";

        foreach ($logger->getHandlers() as $handler) {
            $handler->setFormatter(new LineFormatter($SIMPLE_FORMAT, null, true, true));
        }
    }
}
