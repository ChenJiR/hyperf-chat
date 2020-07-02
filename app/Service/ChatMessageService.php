<?php


namespace App\Service;

use App\Util\StringHelper;
use Hyperf\Di\Annotation\Inject;

/**
 * Class UserService
 * @package App\Service
 */
class ChatMessageService
{

    /**
     * @Inject()
     * @var ChatRoomsService
     */
    private $chatRoomService;

    /**
     * 发送消息
     */
    public function send()
    {

    }

    public function messageHandler($message, $room_id, $isImg = false)
    {
        $message = StringHelper::escape(htmlspecialchars($message));
        $remains = [];
        if ($isImg) {
            $message = '<img class="chat-img" onclick="preview(this)" style="display: block; max-width: 120px; max-height: 120px; visibility: visible;" src=' . $message . '>';
        } else {
//            $emoji = require '../Util/emoji.php';
            $emoji = [];
            foreach ($emoji as $_k => $_v) {
                $message = str_replace($_k, $_v, $message);
            }
            $tmp = self::remind($room_id, $message);
            if ($tmp) {
                $message = $tmp['msg'];
                $remains = $tmp['remains'];
            }
        }
        return [$message, $remains];
    }

    /**
     * @
     * @param $roomid
     * @param $msg
     * @return array
     */
    private function remind($roomid, $msg)
    {
        $data = [];
        if ($msg != "") {
            $data['msg'] = $msg;
            //正则匹配出所有@的人来
            $s = preg_match_all('~@(.+?)　~', $msg, $matches);
            if ($s) {
                $m1 = array_unique($matches[0]);
                $m2 = array_unique($matches[1]);
                $users = $this->chatRoomService->getRoomUserList($roomid);
                $m3 = [];
                foreach ($users as $roomOnlineUser) {
                    $m3[$roomOnlineUser->user->username] = $roomOnlineUser->fd;
                }
                $i = 0;
                foreach ($m2 as $_k => $_v) {
                    if (array_key_exists($_v, $m3)) {
                        $data['msg'] = str_replace($m1[$_k], '<font color="blue">' . trim($m1[$_k]) . '</font>', $data['msg']);
                        $data['remains'][$i]['fd'] = $m3[$_v];
                        $data['remains'][$i]['name'] = $_v;
                        $i++;
                    }
                }
            }
        }
        return $data;
    }

}