<?php

namespace App\Middleware;

use App\Util\TimeHelper;
use Hyperf\Contract\StdoutLoggerInterface;
use Hyperf\Di\Annotation\Inject;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Hyperf\Utils\Context;

/**
 * RequestMiddleware
 * @package App\Middleware
 */
class RequestMiddleware implements MiddlewareInterface
{
    /**
     * @Inject
     * @var StdoutLoggerInterface
     */
    protected $logger;

    /**
     * @Inject
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @Inject
     * @var ServerRequestInterface
     */
    protected $request;


    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if (!$this->requestFilter()) {
            return Context::get(ResponseInterface::class)->withStatus(404);
        }
        $qid = $this->getRequestId();
        // 为每一个请求增加一个qid
        $request = Context::override(
            ServerRequestInterface::class,
            function (ServerRequestInterface $request) use ($qid) {
                $request = $request->withAddedHeader('qid', $qid);
                return $request;
            }
        );

        $this->logger->debug("Request [{$request->getMethod()}] {$request->fullUrl()}");
        $request_start_time = microtime(true);
        Context::set('request_time', $request_start_time);

        $response = $handler->handle($request);

        $executionTime = microtime(true) - $request_start_time;
        $this->logger->debug(
            "Response [{$request->getMethod()}] {$request->fullUrl()} status:{$response->getStatusCode()} time:{$executionTime}"
        );

        return $response;
    }

    /**
     * getRequestId
     * 唯一请求id
     * @return string
     */
    protected function getRequestId()
    {
        $tmp = $this->request->getServerParams();
        $name = strtoupper(substr(md5(gethostname()), 12, 8));
        $remote = isset($tmp['remote_addr'])
            ? strtoupper(substr(md5($tmp['remote_addr']), 12, 8)) : "test";
        return $remote . '-' . $name . uniqid();
    }

    /**
     * request 过滤器
     * @return bool
     */
    private function requestFilter()
    {
        $server_params = $this->request->getServerParams();
        if ($server_params['path_info'] === '/favicon.ico') {
            return false;
        }
        return true;
    }

}