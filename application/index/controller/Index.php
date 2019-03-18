<?php
namespace app\index\controller;

use think\Config;
use think\Controller;
use think\Log;

class Index extends Controller
{
    public $domain;

    public function __construct()
    {
        parent::__construct();
        $this->domain = Config::get('chat')['domain'];

    }
    public function index()
    {
        $fromId = input('from_id');
        $toId = input('to_id');
        $this->assign('fromId', $fromId);
        $this->assign('toId', $toId);
        $this->assign('domain', $this->domain);
        return $this->fetch();
    }

    public function testPage()
    {
        return json(['code' => 1, 'msg' => input('id')]);
    }


    public function lists()
    {
        $this->assign('domain', $this->domain);
        $fromId = input('from_id');
        $this->assign('fromId', $fromId);
        return $this->fetch();
    }
}
