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

use Hyperf\Utils\Context;

trait Trace
{
    /**
     * 设置traceId.
     *
     * @param $traceId
     * @param mixed $coverContext
     */
    protected function putTraceId($traceId = false, $coverContext = true)
    {
        if ($coverContext || ! Context::get('traceid')) {
            $traceId || $traceId = $this->getTraceId();
            Context::set('traceid', $traceId);
        }
    }

    protected function clearTraceId()
    {
        Context::destroy('traceid');
    }

    /**
     * 获取TraceId.
     *
     * @return string
     */
    private function getTraceId()
    {
        return sha1(uniqid(
            '',
            true
        ) . str_shuffle(str_repeat(
            '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ',
            16
        )));
    }
}
