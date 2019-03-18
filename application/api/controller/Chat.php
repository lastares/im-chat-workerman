<?php
/**
 * Created by PhpStorm.
 * User: songyaofeng
 * Date: 2019/3/11
 * Time: 20:14
 */

namespace app\api\controller;

use think\Controller;
use think\Db;
use think\Log;
use think\Request;

class Chat extends Controller
{

    public function saveMessage()
    {
        if (Request::instance()->isAjax()) {
            $params = input('post.');
            Log::write($params);
            $chatInfo = [
                'fromid' => $params['from_id'],
                'fromname' => $this->getName($params['from_id']),
                'toid' => $params['to_id'],
                'toname' => $this->getName($params['to_id']),
                'content' => $params['data'],
                'time' => time(),
                'shopid' => 0,
                'isread' => 0,
//                'isread' => $params['is_read'],
                'type' => 1
            ];

            Db::name("communication")->insert($chatInfo);
            return json(['code' => 0, 'msg' => 'ok'], JSON_UNESCAPED_UNICODE);
        }
    }

    /**
     * 根据用户id返回用户姓名
     */
    public function getName($uid)
    {

        $userinfo = Db::name("user")->where('id', $uid)->field('nickname')->find();

        return $userinfo['nickname'];
    }

    public function getHeadImg()
    {
        if (Request::instance()->isAjax()) {
            $fromId = input('from_id');
            $toId = input('to_id');
            $sender = Db::name('user')->where('id', $fromId)->field('headimgurl')->find();
            $receiver = Db::name('user')->where('id', $toId)->field('headimgurl')->find();

            return json([
                'sender_head_img' => $sender['headimgurl'],
                'receiver_head_img' => $receiver['headimgurl'],
            ]);
        }
    }

    public function getUserName()
    {
        if (Request::instance()->isAjax()) {
            $userId = input('post.user_id');
            $user = Db::name('user')->where('id', $userId)->field('nickname')->find();

            return json([
                'user_nickname' => $user['nickname']
            ]);
        }
    }

    public function loadChatList()
    {
        if (Request::instance()->isAjax()) {

            $fromid = input('post.from_id');
            $toid = input('post.to_id');


            $count = Db::name('communication')->where('(fromid=:fromid and toid=:toid) || (fromid=:toid1 and toid=:fromid1)', ['fromid' => $fromid, 'toid' => $toid, 'toid1' => $toid, 'fromid1' => $fromid])->count('id');

            if ($count >= 10) {

                $message = Db::name('communication')->where('(fromid=:fromid and toid=:toid) || (fromid=:toid1 and toid=:fromid1)', ['fromid' => $fromid, 'toid' => $toid, 'toid1' => $toid, 'fromid1' => $fromid])->limit($count - 10, 10)->order('id')->select();

            } else {
                $message = Db::name('communication')->where('(fromid=:fromid and toid=:toid) || (fromid=:toid1 and toid=:fromid1)', ['fromid' => $fromid, 'toid' => $toid, 'toid1' => $toid, 'fromid1' => $fromid])->order('id')->select();

            }

            foreach($message as &$value) {
                $value['from_img'] = $this->getHeadImg($value['fromid']);
                $value['to_img'] = $this->getName($value['toid']);
            }

            return json(['message' => $message]);

        }
    }

    public function uploadImg()
    {
        $file = $_FILES['file'];
        $fromid = input('fromid');
        $toid = input('toid');
        $online = input('online');

        $suffix = strtolower(strrchr($file['name'], '.'));
        $type = ['.jpg', '.jpeg', '.gif', '.png'];
        if (!in_array($suffix, $type)) {
            return ['status' => 'img type error'];
        }

        if ($file['size'] / 1024 > 5120) {
            return ['status' => 'img is too large'];
        }

        $filename = uniqid("chat_img_", false);
        $uploadpath = ROOT_PATH . 'public/uploads/';
        Log::write('上传路径======' . $uploadpath);
        if (!is_dir($uploadpath)) {
            mkdir($uploadpath, 0777);
        }
        $file_up = $uploadpath . $filename . $suffix;
        $re = move_uploaded_file($file['tmp_name'], $file_up);

        if ($re) {
            $name = $filename . $suffix;
            $data['content'] = $name;
            $data['fromid'] = $fromid;
            $data['toid'] = $toid;
            $data['fromname'] = $this->getName($data['fromid']);
            $data['toname'] = $this->getName($data['toid']);
            $data['time'] = time();
            $data['isread'] = 0;
//            $data['isread'] = $online;
            $data['type'] = 2;
            $data['shopid'] = 0;
            $message_id = Db::name('communication')->insertGetId($data);
            if ($message_id) {
                return ['status' => 'ok', 'img_name' => $name];
            } else {
                return ['status' => 'false'];
            }


        }
    }

    /**
     * 获取消息提醒列表
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getChatList()
    {
        $fromId = input('from_id');
        $info = Db::name('communication')->field(['fromid','toid','fromname'])->where('toid',$fromId)->group('fromid')->select();


        $rows = array_map(function($res){
            return [
                'head_url'=>$this->get_head_one($res['fromid']),
                'username'=>$res['fromname'],
                'countNoread'=>$this->getCountNoread($res['fromid'], $res['toid']),
                'last_message'=>$this->getLastMessage($res['fromid'], $res['toid']),
                'chat_page'=>"http://www.chat.com/index.php/index/index/index?from_id={$res['toid']}&to_id={$res['fromid']}"
            ];

        },$info);

        return $rows;
;
    }

    /**
     * @param $fromid
     * @param $toid
     * 根据fromid和toid来获取他们聊天的最后一条数据
     */
    public function getLastMessage($fromid,$toid){

        $info = Db::name('communication')->where('(fromid=:fromid&&toid=:toid)||(fromid=:fromid2&&toid=:toid2)',['fromid'=>$fromid,'toid'=>$toid,'fromid2'=>$toid,'toid2'=>$fromid])->order('id DESC')->limit(1)->find();

        return $info;
    }


    /**
     * @param $uid
     * 根据uid来获取它的头像
     */
    public function get_head_one($uid){

        $fromhead = Db::name('user')->where('id',$uid)->field('headimgurl')->find();

        return $fromhead['headimgurl'];
    }

    /**
     * @param $fromid
     * @param $toid
     * 根据fromid来获取fromid同toid发送的未读消息。
     */
    public function getCountNoread($fromid,$toid){

        return Db::name('communication')->where(['fromid'=>$fromid,'toid'=>$toid,'isread'=>0])->count('id');

    }

    /**
     * 改变信息纬度状态
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function changeNoRead()
    {
        $fromId = input('from_id');
        $toId = input('to_id');
        Db::name('communication')->where(['toid' => $fromId, 'fromid' => $toId])->update(['isread' => 1]);
    }
}