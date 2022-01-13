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

use Hyperf\Utils\Str;

/**
 * Class AbstractLock.
 */
abstract class AbstractLock
{
    /**
     * The name of the lock.
     *
     * @var string
     */
    protected $name;

    /**
     * The number of seconds the lock should be maintained.
     *
     * @var int
     */
    protected $seconds;

    /**
     * The scope identifier of this lock.
     *
     * @var string
     */
    protected $owner;

    /**
     * Create a new lock instance.
     *
     * @param string $name
     * @param int $seconds
     * @param null|string $owner
     */
    public function __construct($name, $seconds, $owner = null)
    {
        if (is_null($owner)) {
            $owner = Str::random();
        }

        $this->name = $name;
        $this->owner = $owner;
        $this->seconds = $seconds;
    }

    /**
     * Attempt to acquire the lock.
     *
     * @return bool
     */
    abstract public function acquire();

    /**
     * Release the lock.
     *
     * @return bool
     */
    abstract public function release();

    /**
     * Notes: Attempt to acquire the lock for the given number of seconds.
     *
     * @param $waitSec
     *
     * @throws LockTimeOutException
     * @return bool
     */
    public function block($waitSec, callable $callback)
    {
        $starting = microtime(true);

        while (! $this->acquire()) {
            usleep(250 * 1000);

            if ((microtime(true) - $waitSec) >= $starting) {
                throw new LockTimeOutException('Lock timeout!');
            }
        }

        if (is_callable($callback)) {
            try {
                return $callback();
            } finally {
                $this->release();
            }
        }

        return true;
    }

    /**
     * Returns the current owner of the lock.
     *
     * @return string
     */
    public function owner()
    {
        return $this->owner;
    }

    /**
     * Determines whether this lock is allowed to release the lock in the driver.
     *
     * @return bool
     */
    protected function isOwnedByCurrentProcess()
    {
        return $this->getCurrentOwner() === $this->owner;
    }

    /**
     * Returns the owner value written into the driver for this lock.
     *
     * @return string
     */
    abstract protected function getCurrentOwner();
}
