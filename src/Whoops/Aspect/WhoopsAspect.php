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
use GoSwoole\BaseServer\Server\Server;
use Whoops\Run;

class WhoopsAspect implements Aspect
{
    /**
     * @var Run
     */
    private $run;

    public function __construct(Run $run)
    {
        $this->run = $run;
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
            $log = Server::$instance->getLog();
            $log->error($e);
            $response->end($this->run->handleException($e));
        }
        return null;
    }
}