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
//                $this->userService->login();
                $data = [
                    'task' => 'login',
                    'params' => ['name' => $data['name'], 'email' => $data['email']],
                    'fd' => $frame->fd,
                    'roomid' => $data['roomid']
                ];
                !$data['params']['name'] || !$data['params']['email'] && $data['task'] = "nologin";
                $server->task(json_encode($data));
                break;
            case 2: //新消息
                $data = [
                    'task' => 'new',
                    'params' => ['name' => $data['name'], 'avatar' => $data['avatar']],
                    'c' => $data['c'],
                    'message' => $data['message'],
                    'fd' => $frame->fd,
                    'roomid' => $data['roomid']
                ];
                $server->task(json_encode($data));
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
//        $pushMsg = ['code'=>0,'msg'=>'','data'=>[]];
//        //获取用户信息
//        $user = Chat::logout("",$fd);
//        if($user){
//            $data = array(
//                'task' => 'logout',
//                'params' => array(
//                    'name' => $user['name']
//                ),
//                'fd' => $fd
//            );
//            $this->serv->task( json_encode($data) );
//        }

        echo "client {$fd} closed\n";
    }




}