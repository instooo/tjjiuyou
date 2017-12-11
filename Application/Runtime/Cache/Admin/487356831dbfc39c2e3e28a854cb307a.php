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
            <a href="/channel/real_reg">系统管理</a>
        </li>
        <li>用户渠道</li>
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
        <form id="myform" name="countform" method="get" action="/System/userChannel">
            <div id="count_search">
                <ul class="search_box">
                    <li class="line">
                        <label class="title">用户名：</label>
                        <label class="msg"><input type="text" style="width:200px" id="s_username" name="s_username" value="<?php echo ($s_username); ?>" class="manager-input s-input"></label>
                    </li>
                    <li class="line">
                        <input type="submit" name="searchbtn" value="搜索" class="manager-btn s-btn search-btn">
                    </li>
                </ul>
            </div>
            <div id="count_body" style="margin-top: 20px;">
                <table class="huitable node_table" id="list-table" style="table-layout: fixed;">
                    <tr><th>ID</th><th>帐号</th><th style="width: 40%">渠道</th><th>操作</th></tr>
                    <?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr data-id="<?php echo ($vo["id"]); ?>">
                            <td><?php echo ($vo["id"]); ?></td>
                            <td class="username"><?php echo ($vo["username"]); ?></td>
                            <td class="channel" style="text-overflow: ellipsis;white-space: nowrap;overflow: hidden;">
                                <?php if(is_array($vo["clist"])): $i = 0; $__LIST__ = $vo["clist"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vv): $mod = ($i % 2 );++$i; echo ($vv["short_name"]); ?>&nbsp;&nbsp;<?php endforeach; endif; else: echo "" ;endif; ?>
                            </td>
                            <td>
                                <a class="btn_edit" href="javascript:void(0);">修改渠道</a>
                            </td>
                            <input type="hidden" name="cid" value="<?php echo ($vo["channel_id"]); ?>" />
                        </tr><?php endforeach; endif; else: echo "" ;endif; ?>
                    <tr><td colspan="7"><?php echo ($pagebar); ?></td></tr>
                </table>
            </div>
        </form>
        <div class="product-detail-desc mt-15">
            <div class="title mb-5">
                <span>用户渠道说明：</span>
            </div>
            <p class="pt-5"><span  style="font-weight:bold;">用户渠道说明：</span>广告投放媒体渠道详情，会展示账号、渠道和操作。</p>
            <p class="pt-5"><span style="font-weight:bold;">使用者说明：</span>可以通过用户名框进行搜索查询；对用户渠道进行修改。</p>
        </div>
    </div>
</div>
<style>
    tr.list1{line-height: 21px !important;}
</style>
<div id="edit_alert_tmp" style="display: none">
    <div class="form_content edit_member_content" style="padding: 20px;">
        <input type="hidden" class="edit_id" />
        <table width="100%">
            <tr><td>帐号</td><td class="edit_username"></td></tr>
            <tr class="list1">
                <td>一级渠道列表</td>
                <td>
                    <div class="checkbox" style="width:310px;height:265px;overflow: scroll;border: 1px solid #eee;" >
                        <?php if(is_array($clist1)): $i = 0; $__LIST__ = $clist1;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><input type="checkbox" name="channel" value="<?php echo ($vo["id"]); ?>"><?php echo ($vo["short_name"]); ?><br /><?php endforeach; endif; else: echo "" ;endif; ?>
                    </div>

                </td>
            </tr>
            <tr><td></td>
                <td style="text-align: center;">
                    <div class="select" style="display:inline-block;float:left;margin-top:10px;">
                        <input type="radio" name="select" class="select_all" value="<?php echo ($vo["id"]); ?>">全选
                        <input type="radio" name="select" class="select_none" value="<?php echo ($vo["id"]); ?>">全不选
                    </div>
                    <a class="btn_sub" style="display:inline-block;float:right;margin-top:15px;">保存</a></td></tr>
        </table>
    </div>
</div>
<script>
    $('.btn_edit').click(function () {
        var _tr = $(this).parents('tr');
        var user_id = _tr.attr('data-id');
        var username = _tr.find('.username').html();
        if (user_id == '') return false;
        var c_id = _tr.find('input[name="cid"]').val();
        $('#edit_alert_tmp .edit_id').attr('value', user_id);
        $('#edit_alert_tmp .edit_username').html(username);
        var channel_list = c_id.split('-');
        //将channel_list中的id分别填入对应的checkbox
        $('.checkbox input[type="checkbox"]').each(function(i){
            $(this).attr('checked',false);
            if($.inArray($(this).val(),channel_list) > -1){
                $(this).attr('checked',true);
            }else{
                $(this).attr('checked',false);
            }
        });

        var _form_id = 'add_form_' + Math.ceil(Math.random()*1000);
        var _html = '<div id="'+_form_id+'" class="add_node_div">' + $('#edit_alert_tmp').html() + '</div>';
        layer.open({
            type: 1,
            title: '编辑渠道',
            skin: 'layui-layer', //加上边框
            area: ['420px', '450px'], //宽高
            content: _html
        });
        $('#'+_form_id+' .btn_sub').click(function () {
            doEditUserChannel(this);
        });
        $('#'+_form_id+' .edit_input_sub').change(function () {
            var flag = $('#'+_form_id+' input[type="radio"][name="channel"]:checked').val();
            if (flag == 1) {
                $('#'+_form_id+' .list1').show();
                $('#'+_form_id+' .list2').hide();
            }else {
                $('#'+_form_id+' .list2').show();
                $('#'+_form_id+' .list1').hide();
            }
        });
        $('.select .select_all').live('change',function(){
            $('.checkbox input[type="checkbox"]').prop('checked', $(this).prop("checked"));;
        });
        $('.select .select_none').live('change',function(){
            $('.checkbox input[type="checkbox"]').prop('checked', false);;
        });
    });

    //编辑渠道权限
    function doEditUserChannel(_this) {
        var form_content = $(_this).parents('.form_content');
        var user_id = form_content.find('.edit_id').val();
        if (user_id == '') return false;
        var channel = new Array();
        var length = $('.checkbox').children('input[type="checkbox"]').length;
        var sub_length = length/2;
        //循环判断
        $('.checkbox input[type="checkbox"]').each(function (i) {
            if(i >= sub_length){
                if($(this).is(':checked')){
                    channel.push($(this).val());
                }
            }
        });
        if (!channel) {
            layer.msg('请选择渠道', {icon:5,time:1000});
            return false;
        }
        var data = {user_id:user_id,channel:channel};
        $.ajax({
            type:'post',
            dataType:'json',
            data:data,
            url:'/System/doEditUserChannel',
            success:function (data) {
                if (data.code == 1) {
                    layer.msg('保存成功', {icon:6,time:1000}, function () {
                        location.reload();
                    });
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