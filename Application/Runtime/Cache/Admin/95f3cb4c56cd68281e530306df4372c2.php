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


<script src="/Public/Admin/js/highcharts/highcharts.js"></script>

<style>.url_td{display:block;white-space: nowrap;text-overflow:ellipsis;width:350px;overflow: hidden}</style>
<div id="SiteMapPath">
    <ul>
        <li>
            <a href="/">首页</a>
        </li>
        <li>
            <a href="/Realtime/recharge">数据分析</a>
        </li>
        <li>注册走势对比</li>
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
        <form id="myform" name="countform" method="get" action="/Charts/register_compare">
            <div id="count_search">
                <ul class="search_box">
                    <li class="line">
                        <label class="title" style="width:80px">日期选择：</label>
                        <input type="hidden" class="start_input" name="start1">
                        <input type="hidden" class="end_input" name="end1">
                        <div class="ta_date" id="div_date_demo3">
                            <span class="date_title" id="date_demo3"></span>
                            <a class="opt_sel" id="input_trigger_demo3" href="#">
                                <i class="i_orderd"></i>
                            </a>
                        </div>
                    </li>
					<li class="line">
                        <label class="title" style="width:80px">日期选择：</label>
                        <input type="hidden" class="start_input" name="start2">
                        <input type="hidden" class="end_input" name="end2">
                        <div class="ta_date" id="div_date_demo4">
                            <span class="date_title" id="date_demo4"></span>
                            <a class="opt_sel" id="input_trigger_demo4" href="#">
                                <i class="i_orderd"></i>
                            </a>
                        </div>
                    </li>
                    <li class="line">
                        <label class="title">游戏：</label>
                        <label class="msg">
                            <select name="gid" id="gid">
                                <option value="">所有</option>
                                <?php if(is_array($gamelist)): $i = 0; $__LIST__ = $gamelist;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><option value="<?php echo ($vo["gid"]); ?>" <?php if($gid == $vo['gid']): ?>selected<?php endif; ?>><?php echo ($vo["game"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
                            </select>
                        </label>
                    </li>
                    <li class="line">
                        <label class="title">渠道类型：</label>
                        <label class="msg">
                            <select name="channeltype" id="channeltype">
                                <option value="">请选择渠道类型</option>
                                <?php if(is_array($typename)): $i = 0; $__LIST__ = $typename;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><option value="<?php echo ($vo["tid"]); ?>" <?php if($type_name == $vo['tid']): ?>selected<?php endif; ?>><?php echo ($vo["name"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
                            </select>
                        </label>
                    </li>
                    <?php if($sub_channel): ?><li class="line">
                            <label class="title">一级栏目：</label>
                            <label class="msg">
                                <select name="sub_channel" id="sub_channel" onchange="getSecChannel(this)">
                                    <option value="">请选择一级栏目</option>
                                    <?php if(is_array($sub_channel)): $i = 0; $__LIST__ = $sub_channel;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><option value="<?php echo ($vo["id"]); ?>" <?php if($sub_channel_val == $vo['id']): ?>selected<?php endif; ?>><?php echo ($vo["name"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
                                </select>
                            </label>
                        </li><?php endif; ?>
					
					<style>
						.msg>ul{position:absolute;width:150px;border:1px solid #eee;background-color:#fff;z-index:999;}
					</style>
					<li class="line">
                        <label class="title">二级栏目搜索：</label>
                        <label class="msg" style="position:relative;">
                           <input id="product_search" type="text" name="secsearch" value="<?php echo ($sec_search); ?>" data-provide="typeahead" autocomplete="off"/>
                        </label>
                    </li>

                    <li class="bottom">
                        <input type="hidden" name="order" id="order" value="">
                        <input type="submit" name="searchbtn" value="搜索" class="manager-btn s-btn search-btn">
                        <input type="button" name="excelbtn" value="导出EXCEL" class="manager-btn s-btn orange-btn ml-10">
                    </li>
                </ul>
            </div>
            <div id="count_body">
				 <div id="container" style="min-width:800px;height:400px;"></div>
            </div>
           
        </form>
        <div class="product-detail-desc mt-15">
            <div class="title mb-5">
                <span>注册走势对比说明：</span>
            </div>
            <p class="pt-5"><span  style="font-weight:bold;">注册走势对比说明：</span>注册走势对比，对于当前账号显示注册走势的对比图，注册走势对比会展示某段时间的注册人数的对比图。</p>
            <p class="pt-5"><span style="font-weight:bold;">使用者说明：</span>
                使用者可以通过对两个日期选择，游戏，一级栏目，二级栏目进行自定义，选择自己想查询的数据并点击搜索按钮实现两种注册数据的对比图并显示日期内的总人数。
            </p>
        </div>
    </div>
</div>

<script>
	 $(function () {
        var klist = '<?php echo ($klist); ?>';
        var register_v = '<?php echo ($register_v); ?>';
        var subtitle_register = '<?php echo ($subtitle_register); ?>';
        var subtitle_num = '<?php echo ($subtitle_num); ?>';
        var charts;
        var data_source = {
            chart: {
                renderTo: 'container',
                type: 'line'
            },
            title: {
                text: '每日注册人数对比',
                x: -20 //center
            },
            subtitle: {
                text: subtitle_register,
                x: -20
            },
            xAxis: {
                categories: eval("("+klist+")")
            },
            yAxis: {
                title: {
                    text: '注册人数'
                },
                plotLines: [{
                    value: 0,
                    width: 1,
                    color: '#808080'
                }]
            },
            tooltip: {
                valueSuffix: ''
            },
            legend: {
                layout: 'vertical',
                align: 'right',
                verticalAlign: 'middle',
                borderWidth: 0
            },
            plotOptions: {
                line: {
                    dataLabels: {
                        enabled: true,
                        formatter:function () {
                            return (this.y)
                        }
                    }
                }
            },
            series: eval("("+register_v+")"),
            credits: {enabled:false}
        };
        charts = new Highcharts.Chart(data_source);

        $('#switch li').click(function () {
            var colorArr = ['#7cb5ec','#808080'];
            if ($(this).find('a').attr('data') == 'money') {
                charts.options.plotOptions.line.dataLabels.formatter = function () {return (this.y)};
                charts.setTitle({text:'每日注册人数趋势'}, {text:subtitle_register});
                while (charts.series.length>0) {
                    charts.series[0].remove();
                }
                var series = eval("("+register_v+")");
                for (var j in series) {
                    series[j].tooltip = {valueSuffix: '￥'};
                    series[j].color = colorArr[j];
                    charts.addSeries(series[j]);
                }
                charts.yAxis[0].setTitle({text:'注册人数'});
            }else if($(this).find('a').attr('data') == 'num') {
                charts.options.plotOptions.line.dataLabels.formatter = function () {return (this.y)+"人"};
                charts.setTitle({text:'每日注册人数趋势'}, {text:subtitle_num});
                while (charts.series.length>0) {
                    charts.series[0].remove();
                }
                var series = eval("("+num_v+")");
                for (var j in series) {
                    series[j].tooltip = {valueSuffix: '人'};
                    series[j].color = colorArr[j];
                    charts.addSeries(series[j]);
                }
                charts.yAxis[0].setTitle({text:'充值人数 (人)'});
            }else {
                return false;
            }
            charts.redraw();
        });


    });
    
   

    $(function () {
        var start = "<?php echo ($start1); ?>";
        var end = "<?php echo ($end1); ?>";
        $('.start_input').attr('value',start);
        $('.end_input').attr('value',end);
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
    });
	
	
	$(function () {
        var start = "<?php echo ($start2); ?>";
        var end = "<?php echo ($end2); ?>";
        $('.start_input').attr('value',start);
        $('.end_input').attr('value',end);
        var dateRange = new pickerDateRange('date_demo4', {
            aRecent7Days: 'aRecent7DaysDemo4', //最近7天
            isTodayValid: true,
            startDate: start,
            endDate: end,
            //needCompare : true,
            //isSingleDay : true,
            //shortOpr : true,
            defaultText: ' 至 ',
            inputTrigger: 'input_trigger_demo4',
            theme: 'ta',
            success: function (obj) {
                $("#dCon_demo3").html('开始时间 : ' + obj.startDate + '<br/>结束时间 : ' + obj.endDate);
                $("#input_trigger_demo4").parents('li.line').find('.start_input').attr('value', obj.startDate);
                $("#input_trigger_demo4").parents('li.line').find('.end_input').attr('value', obj.endDate);
            }
        });
    });

    //排序
    function order(order) {
        $('#order').attr('value', order);
        $('#myform').submit();
    }
</script>
<script>
	function QuickSelect(){
        var select = $('#quick_select').val();
        var options = $('#sec_channel').find("option"); // select下所有的option
        for (var i=0;i<options.length;i++){
            if (options.eq(i).text()==select){
                options.eq(i).attr("selected",true);
            }
        }
    }
	$(document).ready(function(){
		var sec_data = null;
		get_typeahead(sec_data);
		getSecChannel();
	})
	function get_typeahead(sec_data){
		$.fn.typeahead.Constructor.prototype.blur = function() {
		  var that = this;
		  setTimeout(function () { that.hide() }, 250);
		};
		$('#product_search').typeahead({
			source:sec_data,
			items: 10,//最多显示个数
			highlighter: function (item) {
				return item;
			},
			updater: function (item) {
				return item;
			}
		});	
	}
  //获取二级渠道列表
    function getSecChannel(_this) {
        var sub_channel = $(_this).val();
		var sec_channel_val = $("#sec_channel").val();
        $.ajax({
            type:'post',
            dataType:'json',
            data:{sub_channel:sub_channel},
            url:'/Realtime/getSecChannel',
            success:function (data) {
				var sec_data = data.sec_data;
				$('#product_search').data('typeahead').setSource(sec_data);
                if (data.code == 1) {
                    var seclist = data.seclist;
                    var _ht = '<option value="">请选择二级栏目</option>';
                    for (var i in seclist) {
                       if(sec_channel_val ==seclist[i]['id']){							
							_ht += '<option value="'+seclist[i]['id']+'" selected>'+seclist[i]['name']+'</option>';
						}else{
							_ht += '<option value="'+seclist[i]['id']+'">'+seclist[i]['name']+'</option>';
						} 
                    }
                    $('#sec_channel').html(_ht);
                }else{
                    var _ht = '<option value="">请选择二级栏目</option>';
                    $('#sec_channel').html(_ht);                    
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