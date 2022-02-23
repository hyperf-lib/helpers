<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */
namespace Helpers\Middleware;

use Helpers\Trace;
use Hyperf\Contract\ConfigInterface;
use Hyperf\Utils\ApplicationContext;
use OpenTracing\Tracer;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class TraceMiddleware implements MiddlewareInterface
{
    /**
     * @var Tracer
     */
    protected $tracer;

    /**
     * Process an incoming server request.
     * Processes an incoming server request in order to produce a response.
     * If unable to produce the response itself, it may delegate to the provided
     * request handler to do so.
     * @param ServerRequestInterface  $request
     * @param RequestHandlerInterface $handler
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if ($this->isIgnorePath($request)) {
            return $handler->handle($request);
        }

        return parent::process($request, $handler);
    }

    /**
     * 判断是否忽略Path
     * @param ServerRequestInterface $request
     * @return bool true为是需要忽略false为不需要忽略
     */
    protected function isIgnorePath(ServerRequestInterface $request): bool
    {
        $path      = (string)$request->getUri()->getPath();
        $container = ApplicationContext::getContainer();
        $config    = $container->get(ConfigInterface::class);

        return in_array($path, (array)$config->get('opentracing.ignore_path'));
    }
}
