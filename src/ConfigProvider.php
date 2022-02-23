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
namespace Helpers;

use Helpers\Aspect\LogAspect;
use Helpers\Middleware\SetLoggerTraceidMiddleware;
use Helpers\Middleware\TraceMiddleware;
use Helpers\Request\HttpClient;

class ConfigProvider
{
    public function __invoke(): array
    {
        return [
            'dependencies' => [
                HttpClient::class => HttpClient::class
            ],
            'commands' => [
            ],
            'middlewares' => [
                'http' => [
                    TraceMiddleware::class,
                    SetLoggerTraceidMiddleware::class
                ],
            ],
            'aspect' => [
                LogAspect::class,
            ],
            'annotations' => [
                'scan' => [
                    'paths' => [
                        __DIR__,
                    ],
                ],
            ],
            'publish' => [
                'id' => 'config',
            ],
        ];
    }
}
