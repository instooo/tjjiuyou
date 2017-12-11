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
        <li>节点列表</li>
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
        <div id="count_body" style="margin-top: 20px;">
            <table class="huitable node_table" id="list-table">
                <thead>
                <tr><th>节点名称</th><th>访问链接</th><th>排序</th><th>是否显示</th><th>操作</th></tr>
                </thead>
                <tbody>
                <tr data-id="0">
                    <td>CPS系统</td>
                    <td>/</td>
                    <td>0</td>
                    <td>否</td>
                    <td class="handle">
                        <a class="btn_add" href="javascript:void(0);">添加子节点</a>
                    </td>
                </tr>
                <?php if(is_array($node_tree)): $i = 0; $__LIST__ = $node_tree;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr class="cnode_0 cnode_<?php echo ($vo["id"]); ?>" data-id="<?php echo ($vo["id"]); ?>" data-level="<?php echo ($vo["level"]); ?>">
                        <td>
                            <img class="fold_tag unfold" src="/Public/Admin/images/icon/menu_minus.gif" style="margin-left: 50px">
                            <a class="title" data="<?php echo ($vo["title"]); ?>"><?php echo ($vo["title"]); ?></a>
                        </td>
                        <td class="name" data="<?php echo ($vo["name"]); ?>"><?php echo ($vo["name"]); ?></td>
                        <td class="sort" data="<?php echo ($vo["sort"]); ?>"><?php echo ($vo["sort"]); ?></td>
                        <td class="ismenu" data="<?php echo ($vo["ismenu"]); ?>"><?php if($vo['ismenu'] == 1): ?>是<?php else: ?>否<?php endif; ?></td>
                        <td>
                            <a class="btn_edit" href="javascript:void(0);">编辑</a>
                            &nbsp;|&nbsp;
                            <a class="btn_add" href="javascript:void(0);">添加子节点</a>
                            &nbsp;|&nbsp;
                            <a class="btn_dele" href="javascript:void(0);">删除</a>
                        </td>
                    </tr>
                    <?php if(is_array($vo['child'])): if(is_array($vo["child"])): $i = 0; $__LIST__ = $vo["child"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v1): $mod = ($i % 2 );++$i;?><tr class="cnode_0 cnode_<?php echo ($vo["id"]); ?> cnode_<?php echo ($v1["id"]); ?>" data-id="<?php echo ($v1["id"]); ?>" data-level="<?php echo ($v1["level"]); ?>">
                                <td>
                                    <img class="fold_tag unfold" src="/Public/Admin/images/icon/menu_minus.gif" style="margin-left: 75px">
                                    <a class="title" data="<?php echo ($v1["title"]); ?>"><?php echo ($v1["title"]); ?></a>
                                </td>
                                <td class="name" data="<?php echo ($v1["name"]); ?>"><?php echo ($v1["name"]); ?></td>
                                <td class="sort" data="<?php echo ($v1["sort"]); ?>"><?php echo ($v1["sort"]); ?></td>
                                <td class="ismenu" data="<?php echo ($v1["ismenu"]); ?>"><?php if($v1['ismenu'] == 1): ?>是<?php else: ?>否<?php endif; ?></td>
                                <td>
                                    <a class="btn_edit" href="javascript:void(0);">编辑</a>
                                    &nbsp;|&nbsp;
                                    <a class="btn_add" href="javascript:void(0);">添加子节点</a>
                                    &nbsp;|&nbsp;
                                    <a class="btn_dele" href="javascript:void(0);">删除</a>
                                </td>
                            </tr>
                            <?php if(is_array($v1['child'])): if(is_array($v1["child"])): $i = 0; $__LIST__ = $v1["child"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v2): $mod = ($i % 2 );++$i;?><tr class="cnode_0 cnode_<?php echo ($vo["id"]); ?> cnode_<?php echo ($v1["id"]); ?>" data-id="<?php echo ($v2["id"]); ?>" data-level="<?php echo ($v2["level"]); ?>">
                                        <td>
                                            <img class="fold_tag unfold" src="/Public/Admin/images/icon/menu_minus.gif" style="margin-left: 100px">
                                            <a class="title" data="<?php echo ($v2["title"]); ?>"><?php echo ($v2["title"]); ?></a>
                                        </td>
                                        <td class="name" data="<?php echo ($v2["name"]); ?>"><?php echo ($v2["name"]); ?></td>
                                        <td class="sort" data="<?php echo ($v2["sort"]); ?>"><?php echo ($v2["sort"]); ?></td>
                                        <td class="ismenu" data="<?php echo ($v2["ismenu"]); ?>"><?php if($v2['ismenu'] == 1): ?>是<?php else: ?>否<?php endif; ?></td>
                                        <td>
                                            <a class="btn_edit" href="javascript:void(0);">编辑</a>
                                            &nbsp;|&nbsp;
                                            <a class="btn_add" href="javascript:void(0);">添加子节点</a>
                                            &nbsp;|&nbsp;
                                            <a class="btn_dele" href="javascript:void(0);">删除</a>
                                        </td>
                                    </tr><?php endforeach; endif; else: echo "" ;endif; endif; endforeach; endif; else: echo "" ;endif; endif; endforeach; endif; else: echo "" ;endif; ?>
                </tbody>
            </table>
        </div>
        <div class="product-detail-desc mt-15">
            <div class="title mb-5">
                <span>节点列表说明：</span>
            </div>
            <p class="pt-5"><span  style="font-weight:bold;">节点列表说明：</span>节点列表，对于当前账号显示节点的详细信息，节点列表会展示节点名称，访问链接，是否开启等信息。</p>
            <p class="pt-5"><span style="font-weight:bold;">使用者说明：</span>使用者可以根据通过点击编辑字样对节点进行修改或点击添加子节点字样添加子节点。</p>
        </div>
    </div>
</div>

<div id="add_alert_tmp" style="display: none">
    <div class="form_content" style="padding: 20px;">
        <input type="hidden" class="add_input_id" value="" />
        <table width="100%">
            <tr><td>节点名称</td><td><input type="text" class="alert_input add_input_title" /></td></tr>
            <tr><td>访问链接</td><td><input type="text" class="alert_input add_input_name" /></td></tr>
            <tr><td>排序</td><td><input type="text" class="alert_input add_input_sort" value="0" /></td></tr>
            <tr><td>是否显示</td><td><select class="alert_input add_input_ismenu"><option value="0">否</option><option value="1">是</option></select></td></tr>
            <tr><td></td><td><a class="subBtn">提交</a></td></tr>
        </table>
    </div>
</div>

<script>
    $(function () {
        $('.node_table tr').hover(function () {
            $(this).css('background-color', '#f5faff');
        },function () {
            $(this).css('background-color', '#FFFFFF');
        });
        //节点展开收起
        $('.node_table .fold_tag').click(function () {
            var _this_tr = $(this).parents('tr');
            var data_id = _this_tr.attr('data-id');
            if ($(this).hasClass('unfold')) {
                $(this).removeClass('unfold').addClass('fold').attr('src', '/Public/Admin/images/icon/menu_plus.gif');
                _this_tr.siblings('.cnode_'+data_id).hide();
            }else {
                $(this).removeClass('fold').addClass('unfold').attr('src', '/Public/Admin/images/icon/menu_minus.gif');
                _this_tr.siblings('.cnode_'+data_id).show();
            }
        });
        //编辑节点输入框
        $('.node_table .btn_edit').click(function () {
            editNode(this);
        });
        //添加子节点
        $('.node_table .btn_add').click(function () {
            var _this_tr = $(this).parents('tr');
            $('#add_alert_tmp .add_input_id').attr('value', _this_tr.attr('data-id'));
            var _form_id = 'add_form_' + Math.ceil(Math.random()*1000);
            var _html = '<div id="'+_form_id+'" class="add_node_div">' + $('#add_alert_tmp').html() + '</div>';
            layer.open({
                type: 1,
                title: '添加节点',
                skin: 'layui-layer', //加上边框
                area: ['420px', '300px'], //宽高
                content: _html
            });
            $('#'+_form_id+' .subBtn').click(function () {
                doAddNode(this);
            });
        });
        //删除节点
        $('.node_table .btn_dele').click(function () {
            var _this = this;
            layer.confirm('确定要删除该节点？', {
                btn: ['确定','取消']
            }, function(){
                doDeleNode(_this);
            });
        });
    });


    //编辑节点方法
    function editNode(_this) {
        if ($(_this).hasClass('do_edit_btn')) {
            doEditNode(_this);
        }else {
            var _this_tr = $(_this).parents('tr');
            var id = _this_tr.attr('data-id');
            var title = _this_tr.find('.title').attr('data');
            var name = _this_tr.find('.name').attr('data');
            var sort = _this_tr.find('.sort').attr('data');
            var ismenu = _this_tr.find('.ismenu').attr('data');

            _this_tr.find('.title').html('<input type="text" class="edit_input input_title" value="'+title+'" />');
            _this_tr.find('.name').html('<input type="text" class="edit_input input_name" value="'+name+'" />');
            _this_tr.find('.sort').html('<input type="text" class="edit_input input_sort" value="'+sort+'" />');
            _this_tr.find('.ismenu').html('<select class="edit_input input_ismenu"><option value="1">是</option><option value="0">否</option></select>');

            _this_tr.find('.input_ismenu option[value="'+ismenu+'"]').attr('selected', true);
            $(_this).addClass('do_edit_btn').html('提交');
        }
    }
    function doEditNode(_this) {
        var _this_tr = $(_this).parents('tr');
        var id = _this_tr.attr('data-id');
        if (id == '') return false;
        var title = _this_tr.find('.input_title').val();
        var name = _this_tr.find('.input_name').val();
        var sort = _this_tr.find('.input_sort').val();
        var ismenu = _this_tr.find('.input_ismenu').val();
        if (title == '' || name == '' || sort == '') {
            layer.msg('数据不能为空', {icon:5,time:1000});
            return false;
        }
        $.ajax({
            type:'post',
            dataType:'json',
            data:{id:id,title:title,name:name,sort:sort,ismenu:ismenu},
            url:'/Permission/updateNode',
            error:function () {
                layer.msg('未知错误', {icon:5,time:1000});
            },
            success:function (data) {
                if (data.code == 1) {
                    layer.msg('修改成功', {icon:6,time:1000}, function () {
                        $(_this).removeClass('do_edit_btn').html('编辑');
                        _this_tr.find('.title').html(title).attr('data', title);
                        _this_tr.find('.name').html(name).attr('data', name);
                        _this_tr.find('.sort').html(sort).attr('data', sort);
                        _this_tr.find('.ismenu').html((ismenu==1)?'是':'否').attr('data', ismenu);
                    });
                }else {
                    layer.msg(data.msg, {icon:5,time:1000});
                }
            }
        });
    }

    //增加节点
    function doAddNode(_this) {
        var form_content = $(_this).parents('.form_content');
        var pid = form_content.find('.add_input_id').val();
        if (pid == '') return false;
        var title = form_content.find('.add_input_title').val();
        var name = form_content.find('.add_input_name').val();
        var sort = form_content.find('.add_input_sort').val();
        var ismenu = form_content.find('.add_input_ismenu').val();
        if (title == '' || name == '' || sort == '' || ismenu == '') {
            layer.msg('数据不能为空', {icon:5,time:1000});
            return false;
        }
        $.ajax({
            type:'post',
            dataType:'json',
            data:{pid:pid,title:title,name:name,sort:sort,ismenu:ismenu},
            url:'/Permission/addNode',
            error:function () {
                layer.msg('未知错误', {icon:5,time:1000});
            },
            success:function (data) {
                if (data.code == 1) {
                    layer.msg('添加成功', {icon:6,time:1000}, function () {location.reload();});
                }else {
                    layer.msg(data.msg, {icon:5,time:1000});
                }
            }
        });
    }

    //删除节点
    function doDeleNode(_this) {
        var _this_tr = $(_this).parents('tr');
        var id = _this_tr.attr('data-id');
        if (id == '') return false;
        $.ajax({
            type:'post',
            dataType:'json',
            data:{id:id},
            url:'/Permission/deleteNode',
            error:function () {
                layer.msg('未知错误', {icon:5,time:1000});
            },
            success:function (data) {
                if (data.code == 1) {
                    layer.msg('删除成功', {icon:6,time:1000}, function () {
                        _this_tr.siblings('.cnode_'+id).remove();
                        _this_tr.remove();
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