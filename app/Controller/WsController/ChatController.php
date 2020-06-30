<?php
declare(strict_types=1);

namespace App\Controller\WsController;

use App\Service\ChatMessageService;
use App\Service\ChatRoomsService;
use App\Service\UserService;
use App\Util\StringHelper;
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
                $code = 1;
                $msg = $user->username . '加入了群聊';
                $data = [
                    'room_id' => $data['roomid'], 'fd' => $frame->fd,
                    'name' => $user->username, 'avatar' => '', 'time' => date("H:i", time())
                ];
                break;
            case 2: //新消息
                list($msg, $remains) =
                    $this->chatMessageService->messageHandler($data['message'], $data['roomid'], $data['c'] == 'img');
                $code = 1;
                $msg = '';
                $data = [
                    'room_id' => $data['roomid'], 'fd' => $frame->fd,
                    'name' => $data['name'], 'avatar' => $data['avatar'],
                    'newmessage' => $msg, 'remains' => $remains,
                    'time' => date("H:i", time())
                ];
                break;
            case 3: // 改变房间
                $this->chatRoomsService->checkRooms($frame->fd, $data['roomid']);
                $code = 6;
                $msg = '换房成功';
                $data = [
                    'oldroomid' => $data['oldroomid'], 'room_id' => $data['roomid'], 'fd' => $frame->fd,
                    'mine' => 0, 'name' => $data['name'], 'avatar' => $data['avatar'],
                    'time' => date("H:i", time())
                ];
                break;
            default :
                $server->push($frame->fd, json_encode(array('code' => 0, 'msg' => 'type error')));
                return;
        }
        $this->sendMsg($server, $code, $msg, $data, $frame->fd);
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
        $rooms = [];
        foreach ($this->chatRoomsService->getRoomList() as $room) {
            $rooms[] = ['roomid' => $room->id, 'roomname' => $room->name];
        }
        $this->push($server, $request->fd, 4, 'success',
            [
                'mine' => 0,
                'rooms' => $rooms,
                'users' => $this->userService->getOnlineUsers()
            ]
        );
    }


}