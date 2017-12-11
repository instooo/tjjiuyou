$(function(){
	
	$("#header2016 .main-nav-wrapper li").each(function() {
		$(this).hover(function(){
			$(this).find("a").addClass("hover");
			$(this).find(".nav-content").stop().slideDown();
		},function(){
	    	$(this).find("a").removeClass("hover");
			$(this).find(".nav-content").stop().slideUp();
		});
	})

	$("#J_searchHistoryContainer").hover(function() {
		$(".m-dropdown-container .common-dropdown").stop().slideDown();
	}, function() {
		$(".m-dropdown-container .common-dropdown").stop().slideUp();
	});

	$(".wide1190 .close,.manager-msg a").click(function(){
		$("#J_browserNotice,.manager-msg").slideUp();
	});

	// 单选反选
	$(".check-all").click(function(){
			//所有checkbox跟着全选的checkbox走。
			$("#jsondata td input[type=checkbox]").attr("checked", this.checked );
	 });

})


$(function () {
	$(".domainzk").hover(function () {
	    $(this).addClass('hover');
	    var menuTop = $(this).offset().top - 102;

	    var minTop = 102 + 50;
	    var itemsList = $(this).children(".sec_cd");
	    var itemsCount = itemsList.find('.sec_icon').length;
	    // 默认情况下 箭头居中
	    var minusTop = Math.floor((itemsCount*40+10 - 36)/2);
	    var subMenuTop = menuTop  - minusTop;
	    if(subMenuTop<50){
	        subMenuTop = 50;
	    }
	    itemsList.find('.blank').css('top',menuTop-subMenuTop+(96-21)/2);
	    itemsList.css("top", subMenuTop).fadeIn("fast");
	}, function () {
	    $(".sec_cd").hide();
	    $(".ms_right").hide();
	    $(".ms_right").eq(0).show();
	    $(this).removeClass('hover');
	});

	var currentLeftMenuList = null;
	$(".leftmenulist h1").click(function () {
	    var me = this;
	    var _this = $(this);
	    if (currentLeftMenuList && currentLeftMenuList != this) {
	        $(currentLeftMenuList).siblings("ul").slideToggle(200);

	        $(currentLeftMenuList).removeClass('current');
	    }
	    _this.siblings("ul").slideToggle(200, function () {
	        if (!$(this).is(":hidden")) {
	            _this.addClass("current");
	            currentLeftMenuList = me;
	        } else {
	            _this.removeClass("current");
	            currentLeftMenuList = null;
	        }
	    });
	})


    var header2016 = document.getElementById("header2016");
    var footer2016 = document.getElementById("footer2016");
    if(!footer2016){
        return;
    }
    // 设置MainContentDIV内容区最小高度
    var contentMinHeight = document.documentElement.clientHeight - header2016.offsetHeight - footer2016.offsetHeight;
    // -1px 为容器顶部加了1px 边框
    document.getElementById("MainContentDIV").style.minHeight=(contentMinHeight-50 -1) + 'px';
    // 中间内容区 高度设置最小高度 -1px 是因为加了1px top边框
    var managerRightShowDom =  $(".ManagerRightShow")[0];
    managerRightShowDom.style.minHeight=(contentMinHeight-50-20-20 -1) + 'px';
});

// 域名表格 结束
$(".tmp_wjf-ui-select").on('click',function(event) {
    $(this).siblings('.tmp_wjf-ui-select-container').toggle();
    event.stopPropagation();
    return false;
});
$('.tmp_wjf-ui-select-container').on('click','li',function() {
  var value = $(this).text();
   $(this).parents().find(".tmp_wjf-ui-select").text(value);
   $(this).parents('.tmp_wjf-ui-select-container').toggle();
   event.stopPropagation();
   return false;
});
$(document).click(function() {
   $(".tmp_wjf-ui-select-container").hide();
});