<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://doc.hyperf.io
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf-cloud/hyperf/blob/master/LICENSE
 */

namespace App\Controller\WsController;

use Hyperf\Contract\OnCloseInterface;
use Hyperf\Contract\OnMessageInterface;
use Hyperf\Contract\OnOpenInterface;
use Hyperf\Contract\StdoutLoggerInterface;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Contract\ResponseInterface;
use Hyperf\Utils\Parallel;
use Psr\Container\ContainerInterface;
use Swoole\Http\Request;
use Swoole\Server;
use Swoole\Websocket\Frame;
use Swoole\WebSocket\Server as WebSocketServer;

abstract class WsAbstractController implements OnMessageInterface, OnCloseInterface, OnOpenInterface
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
     * @param WebSocketServer $server
     * @param Request $request
     */
    public function onOpen($server, Request $request): void
    {
//        $server->task(json_encode(['task' => 'open', 'fd' => $request->fd]));
        echo "open\n";
    }

    /**
     * @param WebSocketServer $server
     * @param $fd
     * @param $code
     * @param $msg
     * @param $data
     * @return void
     */
    public function push($server, $fd, $code, $msg, $data = []): void
    {
        $server->push($fd, json_encode(['code' => $code, 'msg' => $msg, 'data' => $data]));
    }

    /**
     * @param WebSocketServer $server
     * @param $code
     * @param $msg
     * @param array $data
     * @param $my_fd
     */
    public function sendMsg($server, $code, $msg, $data, $my_fd)
    {
        $co_parallel = new Parallel();
        $push_data = ['code' => $code, 'msg' => $msg];
        $data = $data ?: [];
        foreach ($server->connections as $fd) {
            $data['mine'] = $fd === $my_fd ? 1 : 0;
            $push_data['data'] = $data;
            $co_parallel->add(function () use ($server, $push_data, $fd) {
                $server->push($fd, json_encode($push_data));
            });
        }
        $co_parallel->wait();
    }
}
