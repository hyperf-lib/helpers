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
if (! function_exists('formatThrowable')) {
    function formatThrowable(Throwable $throwable)
    {
        return [
            'className' => get_class($throwable),
            'file' => $throwable->getFile() . '(' . $throwable->getLine() . ')',
            'code' => $throwable->getCode(),
            'msg' => $throwable->getMessage(),
        ];
    }
}

if (function_exists('di') === false) {
    /**
     * Finds an entry of the container by its identifier and returns it.
     *
     * @param null|mixed $id
     *
     * @return mixed|\Psr\Container\ContainerInterface
     */
    function di($id = null)
    {
        $container = \Hyperf\Utils\ApplicationContext::getContainer();
        if ($id) {
            return $container->get($id);
        }

        return $container;
    }
}

if (! function_exists('getPkId')) {
    /**
     * 获取主键id.
     *
     * @return int
     */
    function getPkId()
    {
        $generator = di(\Hyperf\Snowflake\IdGeneratorInterface::class);

        return $generator->generate();
    }
}

if (! function_exists('runWithLock')) {
    /**
     * 加锁执行一个方法.
     *
     * @param string $lockName
     * @param null $lockValue
     * @param int $lockSec
     * @param int $waitSec
     *
     * @return mixed
     */
    function runWithLock(Closure $callback, $lockName = '', $lockSec = 60, $waitSec = 2)
    {
        $lockValue = getPkId();

        $lock = make(\Helpers\Lock\RedisLock::class, ['name' => md5($lockName), 'seconds' => $lockSec, 'owner' => $lockValue]);

        return $lock->block($waitSec, $callback);
    }
}

if (! function_exists('apiSucc')) {
    /**
     * 构建成功的接口返回数据.
     *
     * @param $data
     *
     * @return array
     */
    function apiSucc($data = [])
    {
        return [
            'code' => 0,
            'msg' => 'ok',
            'data' => $data,
        ];
    }
}

if (! function_exists('apiErr')) {
    /**
     * 构建成功的接口返回数据.
     *
     * @param int $code
     * @param string $message
     *
     * @return array
     */
    function apiErr($code, $message)
    {
        return [
            'code' => $code,
            'msg' => $message,
            'data' => [],
        ];
    }
}
if (! function_exists('removeEmoji')) {
    function removeEmoji($text)
    {
        // Match Emoticons
        $regexEmoticons = '/[\x{1F600}-\x{1F64F}]/u';
        $clean_text = preg_replace($regexEmoticons, '', $text);

        // Match Miscellaneous Symbols and Pictographs
        $regexSymbols = '/[\x{1F300}-\x{1F5FF}]/u';
        $clean_text = preg_replace($regexSymbols, '', $clean_text);

        // Match Transport And Map Symbols
        $regexTransport = '/[\x{1F680}-\x{1F6FF}]/u';
        $clean_text = preg_replace($regexTransport, '', $clean_text);

        // Match Miscellaneous Symbols
        $regexMisc = '/[\x{2600}-\x{26FF}]/u';
        $clean_text = preg_replace($regexMisc, '', $clean_text);

        // Match Dingbats
        $regexDingbats = '/[\x{2700}-\x{27BF}]/u';
        $clean_text = preg_replace($regexDingbats, '', $clean_text);

        return $clean_text;
    }
}

if (! function_exists('fen2yuan')) {
    /**
     * @param int $num
     * @return float
     */
    function fen2yuan(int $num) {
        return (float)bcdiv((string)$num, '100', 2);
    }
}

if (! function_exists('yuan2fen')) {
    /**
     * @param float $num
     * @return int
     */
    function yuan2fen(float $num):int {
        return (int)bcmul((string)$num, '100');
    }
}
