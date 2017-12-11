<?php
/**
 * Created by PhpStorm.
 * User: DaiLinLin
 * Date: 2017/1/3
 * Time: 15:17
 * for:首页获取token的接口
 */

namespace Api\Controller;

use Org\Util\ApiHelper;
use Org\Util\Response;

class IndexController extends ApiController
{

    public function index(){
        Response::apiReturn(400,'非法请求');
    }
}