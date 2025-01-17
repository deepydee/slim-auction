<?php

declare(strict_types=1);

namespace App\ErrorHandler;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Log\LoggerInterface;
use Slim\Handlers\ErrorHandler;
use Slim\Interfaces\CallableResolverInterface;

/**
 * @psalm-suppress PropertyNotSetInConstructor
 */
final class LogErrorHandler extends ErrorHandler
{
    protected LoggerInterface $logger;

    public function __construct(
        CallableResolverInterface $callableResolver,
        ResponseFactoryInterface $responseFactory,
        LoggerInterface $logger,
    ) {
        parent::__construct($callableResolver, $responseFactory);
        $this->logger = $logger;
    }

    protected function writeToErrorLog(): void
    {
        //        \Sentry\captureException($this->exception);
        $this->logger->error($this->exception->getMessage(), [
            'exception' => $this->exception,
            'url' => (string) $this->request->getUri(),
        ]);
    }
}
