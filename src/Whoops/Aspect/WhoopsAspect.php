<?php
/**
 * Created by PhpStorm.
 * User: 白猫
 * Date: 2019/4/24
 * Time: 14:54
 */

namespace ESD\Plugins\Whoops\Aspect;

use Go\Aop\Aspect;
use Go\Aop\Intercept\MethodInvocation;
use Go\Lang\Annotation\Around;
use ESD\Plugins\Whoops\WhoopsConfig;
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
     * @Around("within(ESD\BaseServer\Server\IServerPort+) && execution(public **->onHttpRequest(*))")
     * @return mixed|null
     * @throws \Throwable
     */
    protected function aroundRequest(MethodInvocation $invocation)
    {
        list($request, $response) = $invocation->getArguments();
        try {
            $invocation->proceed();
        } catch (\Throwable $e) {
            if ($this->whoopsConfig->isEnable()) {
                $response->clear();
                $response->end($this->run->handleException($e));
            } else {
                $response->end(null);
            }
            throw $e;
        }
        return null;
    }
}