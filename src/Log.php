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

use Helpers\Logger\LoggerFactory;
use Hyperf\Logger\Logger;

/**
 * Class Log.
 */
/**
 * @method static Logger get($name)
 * @method static void log($level, $message, array $context = [])
 * @method static void emergency($message, array $context = [])
 * @method static void alert($message, array $context = [])
 * @method static void critical($message, array $context = [])
 * @method static void error($message, array $context = [])
 * @method static void warning($message, array $context = [])
 * @method static void notice($message, array $context = [])
 * @method static void info($message, array $context = [])
 * @method static void debug($message, array $context = [])
 */
class Log
{
    public static function __callStatic($name, $arguments)
    {
        $factory = di(LoggerFactory::class);
        if ($name === 'get') {
            return $factory->get(...$arguments);
        }
        $log = $factory->get('default');

        $log->{$name}(...$arguments);
    }
}
