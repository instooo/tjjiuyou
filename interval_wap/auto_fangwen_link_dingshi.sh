#!/bin/sh

#########################################################
####执行访问某个时间段的url							#####
#加参数和不加参数
#参数加时间段，例如：/data/conf/shell/auto_fangwen_link_dingshi.sh 2014-06-01 2014-06-30
#不加参数则执行前一天：
#########################################################

#vi /data/conf/shell/auto_fangwen_link_dingshi.sh
#chmod +x /data/conf/shell/auto_fangwen_link_dingshi.sh
#每天凌晨3点执行一次
#echo "0 3 * * * root /data/conf/shell/auto_fangwen_link_dingshi.sh >/dev/null" >> /etc/crontab
#service crond restart

startday="$1"
endday="$2"

date=`date -d "+0 day $startday" +%Y%m%d`
enddate=`date -d "+1 day $endday" +%Y%m%d`

if  [ ! -n "$startday" ] && [ ! -n "$startday" ]  ;then
	#echo "参数1,2为空时，执行前一天"
	date=`date -d "-1 day $1" +%Y%m%d`
	enddate=`date -d "+0 day $2" +%Y%m%d`	
fi

link1="http://dingshi.7477.com/interval/cron_spend_bysub.php?date="
link2="http://dingshi.7477.com/interval/cron_spend_bygid.php?date="
link3="http://dingshi.7477.com/interval/cron_date_click.php?date="
#统计注册人数的定时任务地址
link4="http://dingshi.7477.com/interval/cron_date_member.php?date="
#2日留存率
link5="http://dingshi.7477.com/interval/cron_date_liucun.php?d=2&date="
#3日留存率
link6="http://dingshi.7477.com/interval/cron_date_liucun.php?d=3&date="
#7日留存率
link7="http://dingshi.7477.com/interval/cron_date_liucun.php?d=7&date="
#消费日志
link8="http://dingshi.7477.com/interval/cron_data_spend_move.php?date="
#用户转移
link9="http://dingshi.7477.com/interval/cron_data_member_move.php?date="
link10="http://dingshi.7477.com/interval/cron_date_member_hour.php?date="
link11="http://dingshi.7477.com/interval/cron_spend_bygid_hour.php?date="
link12="http://dingshi.7477.com/interval/cron_spend_bysub_hour.php?date="
link13="http://dingshi.7477.com/interval/cron_del_click_log.php?date="
link14="http://dingshi.7477.com/interval/cron_date_click_tc.php?date="
link15="http://dingshi.7477.com/interval/cron_del_click_log_tc.php"
#####20170515-add
link16="http://dingshi.7477.com/interval/cron_date_pt_spends_range.php?date="
link17="http://dingshi.7477.com/interval/cron_date_pt_spends.php?date="
link18="http://dingshi.7477.com/interval/cron_date_pt_member_day.php?date="
link19="http://dingshi.7477.com/interval/cron_date_pt_gamelogin_day.php?date="

while [[ $date < $enddate ]]
do       
		echo "$link1""$date" >> /data/wwwroot/otherlog/cps_links.log
        /usr/bin/curl "$link1""$date" >/dev/null
        sleep 3		
		echo "$link2""$date" >> /data/wwwroot/otherlog/cps_links.log
        /usr/bin/curl "$link2""$date" >/dev/null
        sleep 3		
		echo "$link3""$date" >> /data/wwwroot/otherlog/cps_links.log
        /usr/bin/curl "$link3""$date" >/dev/null
        sleep 3		
		echo "$link4""$date" >> /data/wwwroot/otherlog/cps_links.log
        /usr/bin/curl "$link4""$date" >/dev/null
        sleep 3		
		echo "$link5""$date" >> /data/wwwroot/otherlog/cps_links.log
        /usr/bin/curl "$link5""$date" >/dev/null
        sleep 3		
		echo "$link6""$date" >> /data/wwwroot/otherlog/cps_links.log
        /usr/bin/curl "$link6""$date" >/dev/null
        sleep 3		
		echo "$link7""$date" >> /data/wwwroot/otherlog/cps_links.log
        /usr/bin/curl "$link7""$date" >/dev/null
        sleep 3	
		echo "$link8""$date" >> /data/wwwroot/otherlog/cps_links.log
        /usr/bin/curl "$link8""$date" >/dev/null
        sleep 3
		echo "$link9""$date" >> /data/wwwroot/otherlog/cps_links.log
        /usr/bin/curl "$link9""$date" >/dev/null
        sleep 3			
		echo "$link10""$date" >> /data/wwwroot/otherlog/cps_links.log
        /usr/bin/curl "$link10""$date" >/dev/null
        sleep 3		
		echo "$link11""$date" >> /data/wwwroot/otherlog/cps_links.log
        /usr/bin/curl "$link11""$date" >/dev/null
        sleep 3		
	echo "$link12""$date" >> /data/wwwroot/otherlog/cps_links.log
        /usr/bin/curl "$link12""$date" >/dev/null
        sleep 3
	
	echo "$link13""$date" >> /data/wwwroot/otherlog/cps_links.log
        /usr/bin/curl "$link13""$date" >/dev/null
        sleep 3
	
	echo "$link14""$date" >> /data/wwwroot/otherlog/cps_links.log
        /usr/bin/curl "$link14""$date" >/dev/null
        sleep 3
	
	echo "$link15" >> /data/wwwroot/otherlog/cps_links.log
        /usr/bin/curl "$link15" >/dev/null
        sleep 3
	
	echo "$link16""$date" >> /data/wwwroot/otherlog/cps_links.log
        /usr/bin/curl "$link16""$date" >/dev/null
        sleep 2	
		echo "$link17""$date" >> /data/wwwroot/otherlog/cps_links.log
        /usr/bin/curl "$link17""$date" >/dev/null
        sleep 2		
		echo "$link18""$date" >> /data/wwwroot/otherlog/cps_links.log
        /usr/bin/curl "$link18""$date" >/dev/null
		sleep 2
		
	echo "$link19""$date" >> /data/wwwroot/otherlog/cps_links.log
        /usr/bin/curl "$link19""$date" >/dev/null
	sleep 2
		
	#增加一天
        date=`date -d "+1 day $date" +%Y%m%d`
done


