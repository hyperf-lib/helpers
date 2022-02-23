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

/**
 * Class Redis.
 *
 * @mixin \Redis
 */
class Redis
{
    public static function __callStatic($name, $arguments)
    {
        return self::call($name, $arguments);
    }

    public function __call($name, $arguments)
    {
        return self::call($name, $arguments);
    }

    private static function call($name, $arguments)
    {
        $redis = di(\Hyperf\Redis\Redis::class);

        if (! in_array($name, ['eval'])) {
            if (is_string($arguments[0])) {
                $arguments[0] = config('redis.prefix', '') . $arguments[0];
            } elseif (is_array($arguments[0])) {
                foreach ($arguments[0] as &$key) {
                    $key = config('redis.prefix', '') . $key;
                }
            }
        }

        Log::debug(__CLASS__.'::'.$name, $arguments);
        return $redis->{$name}(...$arguments);
    }
}
