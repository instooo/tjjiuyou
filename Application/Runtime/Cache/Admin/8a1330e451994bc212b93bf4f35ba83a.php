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
            <a href="/channel/real_reg">权限管理</a>
        </li>
        <li>角色列表</li>
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
        <input type="submit" name="searchbtn" value="增加用户" class="manager-btn s-btn search-btn member_add">
        <div id="count_body" style="margin-top: 20px;">
            <table class="huitable node_table" id="list-table">
                <tr><th>ID</th><th>帐号</th><th>昵称</th><th>角色</th><th>状态</th><th>上次登录时间</th><th>操作</th></tr>
                <?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr data-id="<?php echo ($vo["id"]); ?>">
                        <td><?php echo ($vo["id"]); ?></td>
                        <td class="username"><?php echo ($vo["username"]); ?></td>
                        <td class="nickname" data="<?php echo ($vo["nickname"]); ?>"><?php echo ($vo["nickname"]); ?></td>
                        <td class="roleid" data="<?php echo ($vo["roleid"]); ?>"><?php echo ($vo["rolename"]); ?></td>
                        <td class="status" data="<?php echo ($vo["status"]); ?>">
                            <?php switch($vo["status"]): case "1": ?>启用<?php break;?>
                                <?php case "0": ?>关闭<?php break; endswitch;?>
                        </td>
                        <td><?php echo (date("Y-m-d H:i:s",$vo["last_login_time"])); ?></td>
                        <td>
                            <a class="btn_edit" href="javascript:void(0);">编辑</a>
                            &nbsp;|&nbsp;
                            <a class="btn_dele" href="javascript:void(0);">删除</a>
                        </td>
                    </tr><?php endforeach; endif; else: echo "" ;endif; ?>
                <tr><td colspan="7"><?php echo ($pagebar); ?></td></tr>
            </table>
        </div>
        <div class="product-detail-desc mt-15">
            <div class="title mb-5">
                <span>用户列表说明：</span>
            </div>
            <p class="pt-5"><span  style="font-weight:bold;">用户列表说明：</span>用户列表，对于当前账号显示用户的详细信息，用户列表会展示账号，昵称，角色，状态，上次登录时间等信息。</p>
            <p class="pt-5"><span style="font-weight:bold;">使用者说明：</span>使用者可以根据增加用户按钮进行用户的添加或点击编辑字样对用户进行修改。</p>
        </div>
    </div>
</div>

<div id="add_alert_tmp" style="display: none">
    <div class="form_content add_member_content" style="padding: 20px;">
        <table width="100%">
            <tr><td>帐号</td><td><input type="text" class="alert_input add_input_username" /></td></tr>
            <tr><td>密码</td><td><input type="password" class="alert_input add_input_password" /></td></tr>
            <tr><td>昵称</td><td><input type="text" class="alert_input add_input_nickname" /></td></tr>
            <tr>
                <td>角色</td>
                <td>
                    <select class="alert_input add_input_role" style="width: 100px">
                        <?php if(is_array($rolelist)): $i = 0; $__LIST__ = $rolelist;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><option value="<?php echo ($vo["id"]); ?>"><?php echo ($vo["name"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td>状态</td>
                <td>
                    <select class="alert_input add_input_status"><option value="1">启用</option><option value="0">关闭</option></select>
                </td>
            </tr>
            <tr><td></td><td><a class="btn_sub">提交</a></td></tr>
        </table>
    </div>
</div>

<div id="edit_alert_tmp" style="display: none">
    <div class="form_content edit_member_content" style="padding: 20px;">
        <input type="hidden" class="edit_id" />
        <table width="100%">
            <tr><td>帐号</td><td class="edit_username"></td></tr>
            <tr><td>密码</td><td><input type="password" class="alert_input edit_input_password" /></td></tr>
            <tr><td>昵称</td><td><input type="text" class="alert_input edit_input_nickname" /></td></tr>
            <tr>
                <td>角色</td>
                <td>
                    <select class="alert_input edit_input_role" style="width: 100px">
                        <?php if(is_array($rolelist)): $i = 0; $__LIST__ = $rolelist;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><option value="<?php echo ($vo["id"]); ?>"><?php echo ($vo["name"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td>状态</td>
                <td>
                    <select class="alert_input edit_input_status"><option value="1">启用</option><option value="0">关闭</option></select>
                </td>
            </tr>
            <tr><td></td><td><a class="btn_sub">提交</a></td></tr>
        </table>
    </div>
</div>


<script>
    $(function () {
        //新增用户
        $('.member_add').click(function () {
            layer.open({
                type: 1,
                title: '添加用户',
                skin: 'layui-layer', //加上边框
                area: ['350px', '400px'], //宽高
                content: $('#add_alert_tmp').html()
            });
            $('.add_member_content .btn_sub').click(function () {
                doAddMember(this);
            });
        });
        //删除用户
        $('.btn_dele').click(function () {
            var id = $(this).parents('tr').attr('data-id');
            if (id == '') return false;
            layer.confirm('确定要删除该角色？', {
                btn: ['确定','取消']
            }, function(){
                doDeleMember(id);
            });
        });

        //编辑用户
        $('.btn_edit').click(function () {
            var _tr = $(this).parents('tr');
            var id = _tr.attr('data-id');
            var username = _tr.find('.username').html();
            var nickname = _tr.find('.nickname').attr('data');
            var role = _tr.find('.roleid').attr('data');
            var status = _tr.find('.status').attr('data');

            $('#edit_alert_tmp .edit_id').attr('value', id);
            $('#edit_alert_tmp .edit_username').html(username);
            $('#edit_alert_tmp .edit_input_nickname').attr('value', nickname);
            $('#edit_alert_tmp .edit_input_role option[value="'+role+'"]').attr('selected', true).siblings('option').removeAttr('selected');
            $('#edit_alert_tmp .edit_input_status option[value="'+status+'"]').attr('selected', true).siblings('option').removeAttr('selected');
            var _form_id = 'edit_form_' + Math.ceil(Math.random()*1000);
            var _html = '<div id="'+_form_id+'" class="edit_member_div">' + $('#edit_alert_tmp').html() + '</div>';
            layer.open({
                type: 1,
                title: '添加用户',
                skin: 'layui-layer', //加上边框
                area: ['350px', '400px'], //宽高
                content: _html
            });
            $('#'+_form_id+' .btn_sub').click(function () {
                var data = {
                    id:$('#'+_form_id+' .edit_id').val(),
                    password:$('#'+_form_id+' .edit_input_password').val(),
                    nickname:$('#'+_form_id+' .edit_input_nickname').val(),
                    role:$('#'+_form_id+' .edit_input_role').val(),
                    status:$('#'+_form_id+' .edit_input_status').val()
                };
                doEditMember(data);
            });
        });

    });
    //增加用户
    function doAddMember(_this) {
        var form_content = $(_this).parents('.form_content');
        var username = form_content.find('.add_input_username').val();
        var password = form_content.find('.add_input_password').val();
        var nickname = form_content.find('.add_input_nickname').val();
        var role = form_content.find('.add_input_role').val();
        var status = form_content.find('.add_input_status').val();
        if (username == '' || password == '' || nickname == '') {
            layer.msg('数据不能为空', {icon:5,time:1000});
            return false;
        }
        $.ajax({
            type:'post',
            dataType:'json',
            data:{username:username,password:password,nickname:nickname,role:role,status:status},
            url:'/Permission/memberAdd',
            error:function () {
                layer.msg('未知错误', {icon:5,time:1000});
            },
            success:function (data) {
                if (data.code == 1) {
                    layer.msg('添加成功', {icon:6,time:1000}, function () {location.reload()});
                }else {
                    layer.msg(data.msg, {icon:5,time:1000});
                }
            }
        });
    }
    //删除用户
    function doDeleMember(id) {
        $.ajax({
            type:'post',
            dataType:'json',
            data:{id:id},
            url:'/Permission/memberDelete',
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
    //编辑用户
    function doEditMember(data) {
        if (data.id == '' || data.nickname == '') {
            layer.msg('参数不全', {icon:5,time:1000});
            return false;
        }
        $.ajax({
            type:'post',
            dataType:'json',
            data:data,
            url:'/Permission/memberEdit',
            error:function () {
                layer.msg('未知错误', {icon:5,time:1000});
            },
            success:function (data) {
                if (data.code == 1) {
                    layer.msg('修改成功', {icon:6,time:1000}, function () {location.reload()});
                }else {
                    layer.msg(data.msg, {icon:5,time:1000});
                }
            }
        });
    }
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