<?php

declare(strict_types=1);

namespace Helpers\Middleware;

use Helpers\Trace;
use Hyperf\Utils\Context;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class SetLoggerTraceidMiddleware implements MiddlewareInterface
{
    use Trace;

    /**
     * @var ContainerInterface
     */
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $logTraceid = false;

        if (interface_exists(\OpenTracing\Span::class)) {
            $span = Context::get('tracer.root');
            if ($span instanceof \OpenTracing\Span) {
                $logTraceid = $span->getContext()->traceIdToString();
            }
        }

        $this->putTraceId($logTraceid);
        return $handler->handle($request);
    }
    
}
