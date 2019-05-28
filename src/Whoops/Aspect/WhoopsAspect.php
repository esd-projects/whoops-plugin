<?php
/**
 * Created by PhpStorm.
 * User: 白猫
 * Date: 2019/4/24
 * Time: 14:54
 */

namespace ESD\Plugins\Whoops\Aspect;

use ESD\Core\Server\Server;
use ESD\Plugins\Aop\OrderAspect;
use ESD\Plugins\Whoops\WhoopsConfig;
use Go\Aop\Intercept\MethodInvocation;
use Go\Lang\Annotation\Around;
use Whoops\Run;

class WhoopsAspect extends OrderAspect
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
     * @Around("within(ESD\Core\Server\Port\IServerPort+) && execution(public **->onHttpRequest(*))")
     * @return mixed|null
     * @throws \Throwable
     */
    protected function aroundRequest(MethodInvocation $invocation)
    {
        list($request, $response) = $invocation->getArguments();
        try {
            $invocation->proceed();
        } catch (\Throwable $e) {
            if ($this->whoopsConfig->isEnable() && Server::$instance->getServerConfig()->isDebug()) {
                $response->clear();
                $response->end($this->run->handleException($e));
            } else {
                $response->end(null);
            }
            throw $e;
        }
        return null;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return "WhoopsAspect";
    }
}