<?php
namespace app\index\controller;

use think\Config;
use think\Controller;
use think\Log;

class Index extends Controller
{
    public $domain;
    public $host;

    public function __construct()
    {
        parent::__construct();
        $chatConfig = Config::get('chat');
        $this->domain = $chatConfig['domain'];
        $this->host = $chatConfig['host'];

    }
    public function index()
    {
        header("Content-type: text/html; charset=utf-8");
        $fromId = input('from_id');
        $toId = input('to_id');
        $this->assign('fromId', $fromId);
        $this->assign('toId', $toId);
        $this->assign('domain', $this->domain);
        $this->assign('host', $this->host);
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
        $this->assign('host', $this->host);
        return $this->fetch();
    }
}
