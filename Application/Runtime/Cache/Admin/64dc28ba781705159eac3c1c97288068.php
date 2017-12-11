<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>7477网页游戏平台-系统后台管理</title>
    <link href="/Public/Admin/css/global.css" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" type="text/css" href="/Public/Admin/css/manager-common.css">
    <link href="/Public/Admin/css/Manager.css" rel="stylesheet" type="text/css" />
    <script type="text/javascript" src="/Public/Admin/js/jQuery.1.8.2.min.js"></script>
    <script src="/Public/Admin/js/jquery.form.js"></script> 
    <script type="text/javascript" src="/Public/Admin/js/layer/layer.js"></script>
    <script type="text/javascript" src="/Public/Admin/js/date/dateRange.js"></script>
    <script type="text/javascript" src="/Public/Admin/js/typeahead/bootstrap3-typeahead.min.js"></script>
    <link rel="stylesheet" type="text/css" href="/Public/Admin/js/date/dateRange.css" />


</head>
<body id="thrColEls">
<div class="Style2009">
    <!--[if lt ie 9 ]>
    <script type="text/javascript" src="/Public/Admin/js/jresponsed.js"></script>
    <![endif]-->
    <!--[if lte IE 7]>
    <div class="browser-notice hide" id="J_browserNotice">
        <div class="wide1190 pos-r">
            <p class="notice-content">
                您的浏览器版本太低，为推动浏览器W3C标准及更好的用户体验，本站强烈建议您升级到支持HTML5的浏览器，获取更好的用户体验。
            </p> <i class="close"></i>
        </div>
    </div>
    <![endif]-->

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


<link href="/Public/Admin/css/node.css" rel="stylesheet" type="text/css" />
<div id="SiteMapPath">
    <ul>
        <li>
            <a href="/">首页</a>
        </li>
        <li>
            <a href="/System/channel_list">渠道管理</a>
        </li>
        <li>渠道列表</li>
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

    <div id="ManagerRight" class="ManagerRightShow">
       <form id="myform" name="countform" method="get" action="/System/channel_list">
		<div id="count_search">
			<ul class="search_box">				
				<li class="line">				
					<label class="title">一级渠道：</label>
					<label class="msg">
						<select name="channelid" id="channelid" style="width:200px; height:30px;">
							<option value="">所有</option>							
							<?php if(is_array($topchannel)): $i = 0; $__LIST__ = $topchannel;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><option value="<?php echo ($vo["id"]); ?>" <?php if($channelid == $vo['id']): ?>selected<?php endif; ?>><?php echo ($vo["name"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
						</select>
					</label>	
                    <label class="title">渠道名：</label>
                    <label class="msg"><input type="text" style="width:200px" id="s_username" name="s_username" value="<?php echo ($s_username); ?>" class="manager-input s-input"></label>		
					<input type="submit" name="searchbtn" value="搜索" class="manager-btn s-btn search-btn" style="margin-top:2px;">									
				</li>							
			</ul>
			<a href="<?php echo U('System/channel_add');?>" class="manager-btn orange-btn" style=" margin-top:0px; height:32px!important; line-height:27px!important;">增加渠道</a>
			<a href="<?php echo U('System/channel_add_pl');?>" class="manager-btn orange-btn" style=" margin-top:0px; height:32px!important; line-height:27px!important;">批量增加渠道</a>
            <a href="<?php echo U('System/channel_pl_dao');?>" class="manager-btn orange-btn" style=" margin-top:0px; height:32px!important; line-height:27px!important;">批量导入渠道</a>				
		</div>
		</form>
		<div id="count_body" style="margin-top: 20px;">
            <table class="huitable node_table" id="list-table">
                <thead>
                <tr><th>ID</th><th>渠道名</th><th>简写渠道名</th><th>描述</th><th>启用</th><th>添加时间</th><th>操作</th></tr>
                </thead>
                <tbody>
                <?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr data-id="<?php echo ($vo["id"]); ?>">
                        <td class="id" data="<?php echo ($vo["id"]); ?>"><?php echo ($vo["id"]); ?></td>                       
                        <td class="name" data="<?php echo ($vo["name"]); ?>"><?php echo ($vo["name"]); ?></td>
                        <td class="short_name" data="<?php echo ($vo["short_name"]); ?>"><?php echo ($vo["short_name"]); ?></td>
                        <td class="description" data="<?php echo ($vo["description"]); ?>"><?php echo ($vo["description"]); ?></td>
                        <td class="status" data="<?php echo ($vo["status"]); ?>"><?php switch($vo["status"]): case "1": ?>是<?php break; case "0": ?>否<?php break; endswitch;?></td>
                        <td class="addtime" data="<?php echo ($vo["addtime"]); ?>"><?php echo (date("Y-m-d H:i:s",$vo["addtime"])); ?></td>
                        <td>    
							<a class="btn_edit" href="<?php echo U('System/channel_edit',array('id'=>$vo['id']));?>">编辑</a>
							&nbsp;|&nbsp;
							<a class="btn_dele" href="javascript:void(0);">删除</a>                       
                        </td>
                    </tr><?php endforeach; endif; else: echo "" ;endif; ?>
                </tbody>
            </table>
        </div>
		 <div id="jsonpage" class="pagebox"><div class="pageinfo"><?php echo ($page); ?></div></div>
        <div class="product-detail-desc mt-15">
            <div class="title mb-5">
                <span>渠道管理说明：</span>
            </div>
            <p class="pt-5"><span  style="font-weight:bold;">渠道管理说明：</span>当天账号显示渠道的详细信息，包括渠道名，简写渠道名、描述、是否启动、添加时间、操作、增加渠道按钮、批量增加渠道按钮，并可以通过一级渠道框搜索渠道。</p>
            <p class="pt-5"><span style="font-weight:bold;">使用者说明：</span>通过增加渠道和批量增加渠道按钮，可以增加渠道名；并可以通过编辑按钮进行已有的渠道名进行编辑，但不能进行删除。</p>
        </div>
    </div>
</div>

<script>
    $(function () {
        //删除角色
        $('.btn_dele').click(function () {
            var id = $(this).parents('tr').attr('data-id');
            if (id == '') return false;
            layer.confirm('确定要删除该渠道？', {
                btn: ['确定','取消']
            }, function(){
                doDeleRole(id);
            });
        });

        //删除角色
        function doDeleRole(id) {
            $.ajax({
                type:'post',
                dataType:'json',
                data:{id:id},
                url:'/System/channel_dele',
                error:function () {
                    layer.msg('未知错误', {icon:5,time:1000});
                },
                success:function (data) {
                    if (data.code == 1) {
                        layer.msg('删除成功', {icon:6,time:1000}, function () {location.reload()});
                    }else {
                        layer.msg(data.msg, {icon:5,time:1000});
                    }
                }
            });
        }
    })
</script>


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