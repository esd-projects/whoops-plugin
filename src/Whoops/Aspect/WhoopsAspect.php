<?php
/**
 * Created by PhpStorm.
 * User: 白猫
 * Date: 2019/4/24
 * Time: 14:54
 */

namespace GoSwoole\Plugins\Whoops\Aspect;

use Go\Aop\Aspect;
use Go\Aop\Intercept\MethodInvocation;
use Go\Lang\Annotation\Around;
use GoSwoole\Plugins\Whoops\WhoopsConfig;
use Whoops\Run;

class WhoopsAspect implements Aspect
{
    /**
     * @var Run
     */
    private $run;

    /**
     * @var WhoopsConfig
     */
    protected $whoopsConfig;

    public function __construct(Run $run, WhoopsConfig $whoopsConfig)
    {
        $this->run = $run;
        $this->whoopsConfig = $whoopsConfig;
    }

    /**
     * around onHttpRequest
     *
     * @param MethodInvocation $invocation Invocation
     * @Around("within(GoSwoole\BaseServer\Server\IServerPort+) && execution(public **->onHttpRequest(*))")
     * @return mixed|null
     */
    protected function aroundRequest(MethodInvocation $invocation)
    {
        list($request, $response) = $invocation->getArguments();
        try {
            $invocation->proceed();
        } catch (\Throwable $e) {
            if ($this->whoopsConfig->isEnable()) {
                $response->end($this->run->handleException($e));
            }
        }
        return null;
    }
}