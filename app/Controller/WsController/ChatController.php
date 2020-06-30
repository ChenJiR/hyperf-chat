<?php
declare(strict_types=1);

namespace App\Controller\WsController;

use App\Service\ChatMessageService;
use App\Service\ChatRoomsService;
use App\Service\UserService;
use Hyperf\Di\Annotation\Inject;
use Swoole\Http\Request;
use Swoole\Server;
use Swoole\Websocket\Frame;
use Swoole\WebSocket\Server as WebSocketServer;

class ChatController extends WsAbstractController
{

    /**
     * @Inject()
     * @var UserService
     */
    private $userService;

    /**
     * @Inject()
     * @var ChatRoomsService
     */
    private $chatRoomsService;

    /**
     * @Inject()
     * @var ChatMessageService
     */
    private $chatMessageService;

    /**
     * @param WebSocketServer $server
     * @param Frame $frame
     */
    public function onMessage($server, Frame $frame): void
    {
        $data = json_decode($frame->data, true);
        switch ($data['type']) {
            case 1://登录
                $user = $this->userService->login($frame->fd, $data['name'], $data['email'], $data['roomid']);
                $this->push(
                    $server, $frame->fd, 1,
                    $user->username . '加入了群聊',
                    [
                        'room_id' => $data['roomid'], 'fd' => $frame->fd,
                        'name' => $user->username, 'avatar' => '', 'time' => date("H:i", time())
                    ]
                );
                break;
            case 2: //新消息

                $this->push(
                    $server, $frame->fd, 2, '',
                    [
                        'room_id' => $data['roomid'], 'fd' => $frame->fd,
                        'name' => $user->username, 'avatar' => '', 'time' => date("H:i", time())
                    ]
                );
                break;
            case 3: // 改变房间
                $data = [
                    'task' => 'change',
                    'params' => ['name' => $data['name'], 'avatar' => $data['avatar']],
                    'fd' => $frame->fd,
                    'oldroomid' => $data['oldroomid'],
                    'roomid' => $data['roomid']
                ];
                $server->task(json_encode($data));
                break;
            default :
                $server->push($frame->fd, json_encode(array('code' => 0, 'msg' => 'type error')));
                break;
        }
    }


    /**
     * @param Server $server
     * @param int $fd
     * @param int $reactorId
     */
    public function onClose($server, int $fd, int $reactorId): void
    {
        echo "client {$fd} closed\n";
    }

    /**
     * @param WebSocketServer $server
     * @param Request $request
     * @return array|void
     */
    public function onOpen($server, Request $request): void
    {
        $this->push($server, $request, 4, 'success',
            [
                'mine' => 0,
                'rooms' => $this->chatRoomsService->getRoomList(),
                'users' => $this->userService->getOnlineUsers()
            ]
        );
    }


}