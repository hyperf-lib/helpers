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
namespace Helpers\Logger;

use Helpers\Trace;
use Hyperf\Logger\Logger;
use Hyperf\Logger\LoggerFactory as HyperfLoggerFactory;
use Hyperf\Utils\Context;
use Psr\Log\LoggerInterface;

/**
 * Class LoggerFactory.
 */
class LoggerFactory extends HyperfLoggerFactory
{
    use Trace;

    public function get($name = 'hyperf', $group = 'default'): LoggerInterface
    {
        if (isset($this->loggers[$name]) && $this->loggers[$name] instanceof Logger) {
            return $this->loggers[$name];
        }

        $logger = $this->make($name, $group);
        $logger->pushProcessor(function ($record) {
            $record['extra']['host'] = gethostname();
            $record['extra']['app_name'] = config('app_name', '');
            $record['extra']['app_env'] = config('app_env', '');

            $files = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS)[4];
            $record['extra']['file'] = sprintf('%s:%s(%d)', $files['class'], $files['function'], $files['line']);
            if (! Context::get('traceid')) {
                $this->putTraceId();
            }
            $record['extra']['traceid'] = Context::get('traceid');


            if (interface_exists(\OpenTracing\Span::class)) {
                $span = Context::get('tracer.root');
                if ($span instanceof \OpenTracing\Span) {
                    $record['trace_id'] = $span->getContext()->traceIdToString();
                    $record['trace_flags'] = dechex($span->getContext()->getFlags());
                    $record['span_id'] = dechex($span->getContext()->getSpanId());
                    return $record;
                }
            }

            return $record;
        });
        return $this->loggers[$name] = $logger;
    }
}
