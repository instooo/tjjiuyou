<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>7477网页游戏平台-系统后台管理</title>
    <link href="/Public/Third/css/global.css" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" type="text/css" href="/Public/Third/css/manager-common.css">
    <link href="/Public/Third/css/home.css" rel="stylesheet" type="text/css" />
    <script type="text/javascript" src="/Public/Third/js/jQuery.1.8.2.min.js"></script>
	   <script src="/Public/Third/js/jquery.form.js"></script> 
    <script type="text/javascript" src="/Public/Third/js/layer/layer.js"></script>
    <script type="text/javascript" src="/Public/Third/js/date/dateRange.js"></script>
    <script type="text/javascript" src="/Public/Third/js/typeahead/bootstrap3-typeahead.min.js"></script>
    <link rel="stylesheet" type="text/css" href="/Public/Third/js/date/dateRange.css" />

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
    <a class="logo" href="/" style="background-image:url(/Public/Third/images/bg/logo.jpg);"></a>
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


    <div id="MainContentDIV">
        <link rel="stylesheet" type="text/css" href="/Public/Third/css/managerMenu.css">
        <div class="manage_left_menu">
    <a class="leftmenu_top" href="/index.php">
        <i class="icon_manage iconposition"></i>
        管理中心
    </a>
    <div class="leftmenu_main">    
		<a href="/thirddata/tongji">
		<div class="leftmenulist ">
			<h1>
				<i class="icon_manage lmenu-ymjy"></i>
				数据查询
				<i class="expand-icon "></i>
			</h1>
		</div> 
</a>		
    </div>
</div>

        <div id="ManagerRight" style="margin: 0 20px 20px; padding-top:20px;">
           <form id="myform" name="countform" method="get" action="/thirddata/tongji">
		   <div id="count_search">
                <ul class="search_box">
                    <li class="line">
                        <label class="title" style="width:80px">日期选择：</label>
                        <input type="hidden" class="start_input" name="start">
                        <input type="hidden" class="end_input" name="end">
                        <div class="ta_date" id="div_date_demo3">
                            <span class="date_title" id="date_demo3"></span>
                            <a class="opt_sel" id="input_trigger_demo3" href="#">
                                <i class="i_orderd"></i>
                            </a>
                        </div>
						<input type="hidden" name="order" id="order" value="">
                        <input type="submit" name="searchbtn" value="搜索" class="manager-btn s-btn search-btn"></li>
                </ul>
				
				</form>	 
				
            </div>
            <!--第一层账户信息 开始-->
            <div class="manager-top cl">
                <!--账号信息 开始-->
                <div class="mt-left">
                    <div class="mt-info" style="background:none; padding-left:5px;">
                        <div class="mt-info-title">
                            注册数：<?php echo ($total_register); ?>人 
                        </div>
                       <div class="mt-info-title">
                            安装数：<?php echo ($total_install); ?>人 
                        </div>
						<div class="mt-info-title">
                            充值金额：<?php echo ($total_pay); ?>元
                        </div>                       
                    </div>
                    
                </div>
                <!--账号信息 结束-->
            </div>  
			<div class="product-detail-desc mt-15">
            <div class="title mb-5">
                <span>数据查询说明：</span>
            </div>
            <p class="pt-5"><span  style="font-weight:bold;">数据查询说明：</span>当日充值数据、注册数据、安装数据,只能在次日进行查询</p>
        </div>
			
    </div>
</div>
<!-- 管理中心页面  使用的简单版本页脚 -->
<!-- 页脚部分 开始-->

<!-- 页脚部分 结束 -->

<script type="text/javascript">
$(function () {
        var start = "<?php echo ($start); ?>";
        var end = "<?php echo ($end); ?>";
		var reg_start = "<?php echo ($reg_start); ?>";
        var reg_end = "<?php echo ($reg_end); ?>";
        $('.start_input').attr('value',start);
        $('.end_input').attr('value',end);
		$('.reg_start_input').attr('value',reg_start);
        $('.reg_end_input').attr('value',reg_end);
        var dateRange = new pickerDateRange('date_demo3', {
            aRecent7Days: 'aRecent7DaysDemo3', //最近7天
            isTodayValid: true,
            startDate: start,
            endDate: end,
            //needCompare : true,
            //isSingleDay : true,
            //shortOpr : true,
            defaultText: ' 至 ',
            inputTrigger: 'input_trigger_demo3',
            theme: 'ta',
            success: function (obj) {
                $("#dCon_demo3").html('开始时间 : ' + obj.startDate + '<br/>结束时间 : ' + obj.endDate);
                $("#input_trigger_demo3").parents('li.line').find('.start_input').attr('value', obj.startDate);
                $("#input_trigger_demo3").parents('li.line').find('.end_input').attr('value', obj.endDate);
            }
        });
		$('#quick_select').blur(function(){
			QuickSelect();
		});
		var dateRange = new pickerDateRange('date_demo4', {
            aRecent7Days: 'aRecent7DaysDemo4', //最近7天
            isTodayValid: true,
            startDate: reg_start,
            endDate: reg_end,
            //needCompare : true,
            //isSingleDay : true,
            //shortOpr : true,
            defaultText: ' 至 ',
            inputTrigger: 'input_trigger_demo4',
            theme: 'ta',
            success: function (obj) {
                $("#dCon_demo4").html('开始时间 : ' + obj.startDate + '<br/>结束时间 : ' + obj.endDate);
                $("#input_trigger_demo4").parents('li.line').find('.reg_start_input').attr('value', obj.startDate);
                $("#input_trigger_demo4").parents('li.line').find('.reg_end_input').attr('value', obj.endDate);
            }
        });	
    });
    //排序
    function order(order) {
        $('#order').attr('value', order);
        $('#myform').submit();
    }
</script>
</body>
</html>