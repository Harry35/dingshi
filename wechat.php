<?php
namespace iDeliveryFood;

use LaneWeChat\Core\Wechat;
use LaneWeChat\Core\Menu;
use iDeliveryFood\services\WechatRequestService;
use iDeliveryFood\models\RestoModel;
use iDeliveryFood\models\SearchKeywordModel;


session_start();
//引入配置文件
include_once __DIR__.'/config.php';
//引入自动载入函数
include_once __DIR__.'/autoloader.php';
//调用自动载入函数
AutoLoader::register();

//初始化微信类
$wechat = new WeChat(WECHAT_TOKEN, TRUE);

//首次使用需要注视掉下面，并打开最后一行
$request = $wechat->getRequest();
echo WechatRequestService::switchType($request);

$wechat = new WeChat(WECHAT_TOKEN, TRUE);

//首次使用需要打开下面这一行（29行），并且注释掉上面1行（26行）。本行用来验证URL
// $wechat->checkSignature();
