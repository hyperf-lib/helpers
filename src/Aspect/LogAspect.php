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
namespace Helpers\Aspect;

use Helpers\Trace;
use Hyperf\Di\Annotation\Aspect;
use Hyperf\Di\Aop\AbstractAspect;
use Hyperf\Di\Aop\ProceedingJoinPoint;

/**
 * @Aspect
 * Class LogAspect.
 */
class LogAspect extends AbstractAspect
{
    use Trace;

    public $classes = [
        'Helpers\\Logger\\LoggerFactory::get',
    ];

    public function process(ProceedingJoinPoint $proceedingJoinPoint)
    {
        $parentId = \Hyperf\Utils\Coroutine::parentId();
        ($parentId == -1) && $parentId = null;

        $this->putTraceId($parentId);
        return $proceedingJoinPoint->process();
    }
}
