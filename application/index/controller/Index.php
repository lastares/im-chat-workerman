<?php
namespace app\index\controller;

use think\Controller;
use think\Log;

class Index extends Controller
{
    public function index()
    {
        $fromId = input('from_id');
        $toId = input('to_id');
        $this->assign('fromId', $fromId);
        $this->assign('toId', $toId);
        return $this->fetch();
    }

    public function testPage()
    {
        return json(['code' => 1, 'msg' => input('id')]);
    }


    public function lists()
    {
        $fromId = input('from_id');
        $this->assign('fromId', $fromId);
        return $this->fetch();
    }
}
