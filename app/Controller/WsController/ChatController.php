<?php
declare(strict_types=1);

namespace App\Controller\WsController;

use Swoole\Http\Request;
use Swoole\Server;
use Swoole\Websocket\Frame;
use Swoole\WebSocket\Server as WebSocketServer;

class ChatController extends WsAbstractController
{
    /**
     * @param WebSocketServer $server
     * @param Frame $frame
     */
    public function onMessage($server, Frame $frame): void
    {
        $server->push($frame->fd, 'Recv: ' . $frame->data);
    }

    /**
     * @param Server $server
     * @param int $fd
     * @param int $reactorId
     */
    public function onClose($server, int $fd, int $reactorId): void
    {
        var_dump('closed');
    }

    /**
     * @param WebSocketServer $server
     * @param Request $request
     */
    public function onOpen($server, Request $request): void
    {
        $server->push($request->fd, 'Opened');
    }
}