<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>7477网页游戏平台-系统后台管理</title>
    <link href="/Public/Admin/css/global.css" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" type="text/css" href="/Public/Admin/css/manager-common.css">
    <link href="/Public/Admin/css/home.css" rel="stylesheet" type="text/css" />
    <script type="text/javascript" src="/Public/Admin/js/jQuery.1.8.2.min.js"></script>


</head>
<body id="thrColEls">
<div class="Style2009">
    
    <!-- 顶部 开始 -->
<div id="header2016">
  <style>
    .nav-user {
      cursor: default;
      margin-left: 20px;
      width: 310px;
      height: 72px;
      line-height: 72px;
      float: left;
      display: inline-block;
    }
  </style>
<h1 class="hide">7477游禧科技</h1>
<!-- 导航菜单栏-->
<div class="header-nav">
  <div class="wide1190 cl header-nav-content">
    <a class="logo" href="/" style="background-image:url(/Public/Admin/images/bg/logo.jpg);"></a>
    <?php if($_SESSION['user']): ?><a class="nav-user">你好(<?php echo ($_SESSION["user"]); ?>)</a><?php endif; ?>
    <ul class="main-nav-wrapper cl" id="J_mainNavWrapper">
      <li>
        <a href="http://www.7477.com" target="_blank" class="nav-menu current" id="J_mainMenu_sy">平台首页</a>
      </li>
      <li>
        <a href="http://bbs.7477.com/" target="_blank" class="nav-menu" id="J_mainMenu_ymzc">论坛</a>       
      </li>
      <?php if($_SESSION['user']): ?><li><a href="/public/loginout" class="nav-menu">退出</a></li><?php endif; ?>
  
</ul>
</div>
</div>
</div>
<!-- 顶部 结束 -->
<div id="SiteMapPath">
  <ul>
  <li>
  <a href="/">西部数码首页</a>
  </li>
  <li>
  <a href="/Manager/">用户管理中心</a>
  </li>
  <li>域名管理中心</li>
  </ul>
</div>

    <div id="MainContentDIV">
        <link rel="stylesheet" type="text/css" href="/Public/Admin/css/managerMenu.css">
        <div class="manage_left_menu">
    <a class="leftmenu_top" href="/index.php">
        <i class="icon_manage iconposition"></i>
        管理中心
    </a>
    <div class="leftmenu_main">
        <?php if(is_array($tree)): $i = 0; $__LIST__ = $tree;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><div class="leftmenulist ">
                <h1 data-id="ymjy">
                    <i class="icon_manage lmenu-ymjy"></i>
                    <?php echo ($vo["title"]); ?>
                    <i class="expand-icon "></i>
                </h1>
                <?php if(is_array($vo['child'])): ?><ul>
                        <?php if(is_array($vo["child"])): $i = 0; $__LIST__ = $vo["child"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v1): $mod = ($i % 2 );++$i; if($v1['ismenu'] == 1): ?><li class="domainzk"><a href="<?php echo ($v1["name"]); ?>"><?php echo ($v1["title"]); ?></a></li><?php endif; endforeach; endif; else: echo "" ;endif; ?>
                    </ul><?php endif; ?>
            </div><?php endforeach; endif; else: echo "" ;endif; ?>
    </div>
</div>

        <div id="ManagerRight" style="margin: 0 20px 20px; padding-top:20px;">
           
            <!--第一层账户信息 开始-->
            <div class="manager-top cl">
                <!--账号信息 开始-->
                <div class="mt-left">
                    <div class="mt-info">
                        <div class="mt-info-title">
                            <a href="javascript:void(0);" style="color:#ff8041"><?php echo ($_SESSION["user"]); ?></a>
                            ，欢迎您！
                            <label>
                                您的级别是：
                                <span>推广会员</span>
                                &nbsp;&nbsp;
                                <span id="zhmsgother"></span>

                            </label>
                            <label>
                                <a href="javascript:void(0);">
                                    您的用户ID是：
                                    <span class="font18"><?php echo ($userinfo["id"]); ?></span>
                                </a>
                                <a class="id-help" href="javascript:void(0);">
                                    <div class="id-help-tip">
                                        <i></i>
                                        <p>用户id是您的账号id</p>
                                    </div>
                                </a>
                            </label>
                        </div>
                       
                        <div class="pt-20">

                            <a class="mt-info-login" href="javascript:void(0);">
                                最近登录：
                                <span class="j_logtime"><?php echo (date('Y-m-d H:i:s','$userinfo["last_login_time"]')); ?></span>
                            </a>
                            <span class="mt-info-login">
							  登录ip:
							  <span class="j_logip"><?php echo ($userinfo["last_login_ip"]); ?></span>
							  <span class="j_ipmsg"><?php echo ($userinfo["location"]); ?></span>
							</span>

                        </div>
                    </div>
                    
                </div>
                <!--账号信息 结束-->
            </div>
            <!--第一层账户信息 结束-->
			<div class="manager-domain">
				<div class="md-title" style="padding-top:12px;">
					<span>快捷入口</span>
				</div>
			</div>
            <!--第二层常用链接 开始-->
            <div class="manager-link" id="J_managerLink">
                <ul class="ml-list cl">
                    <li>
                        <a class="item" href="/realtime/register">
                            <i class="item-icon tel"></i>
                            <span>实时注册</span>
                        </a>
                    </li>
                    <li>
                        <a class="item" href="/realtime/recharge">
                            <i class="item-icon record"></i>
                            <span>实时充值</span>
                        </a>
                    </li>
                    <li class="last-li">
                        <a class="item" href="/realtime/pay_list">
                            <i class="item-icon tousu"></i>
                            <span>充值查询</span>
                        </a>
                    </li>
                </ul>
            </div>
			
			<div class="product-detail-desc mt-15">
				<div class="title mb-5">
				<span>提示：</span>
				</div>
				<p class="pt-5">快捷入口是常用功能入口，后期可根据实际情况进行添加。</p>
			</div>
            

    </div>
</div>
<!-- 管理中心页面  使用的简单版本页脚 -->
<!-- 页脚部分 开始-->

</div>
<!-- 管理中心页面  使用的简单版本页脚 -->
<!-- 页脚部分 开始-->
<div id="footer2016">
    <!-- 页脚底部部分 -->
    <div class="footer-bottom">
        <p>

            <a href="http://www.miitbeian.gov.cn/" rel="nofollow" target="_blank"> Copyright © 2015  深圳游禧科技有限公司旗下7477.com游戏平台 版权所有</a>
            &nbsp;&nbsp;粤ICP备14083534号
            <br />

        </p>
        <p>媒体及商务合作：43173784（QQ）&emsp;&emsp;400电话：400-886-7477</p>
        
    </div>
</div>
<!-- 页脚部分 结束 -->
<script type="text/javascript" src="/Public/Admin/js/common.js"></script>
<script>
    $(function () {
        var url_tag = "<?php echo strtolower(CONTROLLER_NAME.'/'.ACTION_NAME); ?>";
        $('.leftmenu_main a').each(function () {
            var _href = $(this).attr('href').toLowerCase();
            if (_href.indexOf(url_tag) > 0) {
                $(this).css('color','#4597EA');
                $(this).parents('.leftmenulist').find('h1').trigger('click');return false;
            }
        });
		$(".manager-tab li").each(function(){		
            var _href = $(this).find('a').attr('href').toLowerCase();
            if (_href.indexOf(url_tag) > 0) {
                $(this).addClass("liactive");
                $(this).siblings("li").removeClass('liactive');return false;
            }
		});
    });
</script>
</body>
</html>
<!-- 页脚部分 结束 -->

<script type="text/javascript" src="/Public/Admin/js/common.js"></script>

</body>
</html>