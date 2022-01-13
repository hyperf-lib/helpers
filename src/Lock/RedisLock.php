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
namespace Helpers\Lock;

use Helpers\Redis;

/**
 * Class RedisLock.
 */
class RedisLock extends AbstractLock
{
    /**
     * The Redis factory implementation.
     *
     * @var Redis
     */
    protected $redis;

    /**
     * Create a new lock instance.
     *
     * @param \Redis $redis
     * @param string $name
     * @param int $seconds
     * @param null|string $owner
     */
    public function __construct(Redis $redis, $name, $seconds, $owner = null)
    {
        parent::__construct($name, $seconds, $owner);

        $this->redis = $redis;
    }

    /**
     * Attempt to acquire the lock.
     *
     * @return bool
     */
    public function acquire()
    {
        if ($this->seconds > 0) {
            return $this->redis->set($this->name, $this->owner, ['nx', 'ex' => $this->seconds]);
        }
        return $this->redis->setnx($this->name, $this->owner);
    }

    /**
     * Release the lock.
     *
     * @return bool
     */
    public function release()
    {
        $luaScript = <<<'LUA'
if redis.call("get",KEYS[1]) == ARGV[1] then
    return redis.call("del",KEYS[1])
else
    return 0
end
LUA;
        $this->redis->eval($luaScript, [config('redis.prefix', '') . $this->name, $this->owner], 1);
        echo $this->getCurrentOwner(), '  ',$this->name,'  ',$this->owner,PHP_EOL;
        return $this->redis->eval($luaScript, [config('redis.prefix', '') . $this->name, $this->owner], 1);
    }

    /**
     * Releases this lock in disregard of ownership.
     */
    public function forceRelease()
    {
        $this->redis->del($this->name);
    }

    /**
     * Returns the owner value written into the driver for this lock.
     *
     * @return string
     */
    protected function getCurrentOwner()
    {
        return $this->redis->get($this->name);
    }
}
