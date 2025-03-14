<?php

declare(strict_types=1);

namespace App\ErrorHandler;

use Slim\Handlers\ErrorHandler;

/**
 * @psalm-suppress PropertyNotSetInConstructor
 */
final class LogErrorHandler extends ErrorHandler
{
    protected function writeToErrorLog(): void
    {
        //        \Sentry\captureException($this->exception);
        $this->logger->error($this->exception->getMessage(), [
            'exception' => $this->exception,
            'url' => (string) $this->request->getUri(),
        ]);
    }
}
