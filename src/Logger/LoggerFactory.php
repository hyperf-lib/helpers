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

use Hyperf\Logger\Logger;
use Hyperf\Logger\LoggerFactory as HyperfLoggerFactory;
use Hyperf\Utils\Context;
use Psr\Log\LoggerInterface;

/**
 * Class LoggerFactory.
 */
class LoggerFactory extends HyperfLoggerFactory
{
    public function get($name = 'hyperf', $group = 'default'): LoggerInterface
    {
        if (isset($this->loggers[$name]) && $this->loggers[$name] instanceof Logger) {
            return $this->loggers[$name];
        }

        $logger = $this->make($name, $group);
        $logger->pushProcessor(function ($record) {
            $files = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS)[4];

            $record['extra']['file'] = sprintf('%s:%s(%d)', $files['class'], $files['function'], $files['line']);
            $record['extra']['host'] = gethostname();
            $record['extra']['traceid'] = Context::get('traceid');
            return $record;
        });
        return $this->loggers[$name] = $logger;
    }
}
