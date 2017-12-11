#!/bin/sh

#########################################################
####执行访问某个时间段的url							#####
#加参数和不加参数
#参数加时间段，例如：/data/conf/shell/auto_fangwen_link_dingshi_wap.sh 2017-01-01 2017-02-07
#不加参数则执行前一天：
#########################################################

#vi /data/conf/shell/auto_fangwen_link_dingshi_wap.sh
#chmod +x /data/conf/shell/auto_fangwen_link_dingshi_wap.sh
#每天凌晨3点执行一次
#echo "0 3 * * * root /data/conf/shell/auto_fangwen_link_dingshi_wap.sh >/dev/null" >> /etc/crontab
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


link1="http://dingshi.7477.com/interval_wap/cron_date_install_bygid.php?date="
link2="http://dingshi.7477.com/interval_wap/cron_date_liucun.php?d=2&date="
link3="http://dingshi.7477.com/interval_wap/cron_date_liucun.php?d=3&date="
link4="http://dingshi.7477.com/interval_wap/cron_date_liucun.php?d=7&date="
link5="http://dingshi.7477.com/interval_wap/cron_date_pay_bygid.php?date="
link6="http://dingshi.7477.com/interval_wap/cron_date_pay_bysub.php?date="
link7="http://dingshi.7477.com/interval_wap/cron_date_register.php?date="
#link8="http://dingshi.7477.com/interval_wap/cron_delete_click.php"
link9="http://dingshi.7477.com/interval_wap/cron_copy_member_area.php?date="
link10="http://dingshi.7477.com/interval_wap/cron_date_member_byarea.php?date="

#20170228增加
link11="http://dingshi.7477.com/interval_wap/cron_copy_pay_log_area.php?date="
link12="http://dingshi.7477.com/interval_wap/cron_date_pay_bygid_area.php?date="
link13="http://dingshi.7477.com/interval_wap/cron_date_pay_bygid_hour.php?date="
link14="http://dingshi.7477.com/interval_wap/cron_date_pay_bysub_area.php?date="
link15="http://dingshi.7477.com/interval_wap/cron_date_pay_bysub_hour.php?date="

while [[ $date < $enddate ]]
do       
	echo "$link1""$date" >> /data/wwwroot/otherlog/cps_links_wap.log
        /usr/bin/curl "$link1""$date" >/dev/null
        sleep 3		
	echo "$link2""$date" >> /data/wwwroot/otherlog/cps_links_wap.log
        /usr/bin/curl "$link2""$date" >/dev/null
        sleep 3		
	echo "$link3""$date" >> /data/wwwroot/otherlog/cps_links_wap.log
        /usr/bin/curl "$link3""$date" >/dev/null
        sleep 3		
	echo "$link4""$date" >> /data/wwwroot/otherlog/cps_links_wap.log
        /usr/bin/curl "$link4""$date" >/dev/null
        sleep 3		
	echo "$link5""$date" >> /data/wwwroot/otherlog/cps_links_wap.log
        /usr/bin/curl "$link5""$date" >/dev/null
        sleep 3		
	echo "$link6""$date" >> /data/wwwroot/otherlog/cps_links_wap.log
        /usr/bin/curl "$link6""$date" >/dev/null
        sleep 3		
	echo "$link7""$date" >> /data/wwwroot/otherlog/cps_links_wap.log
        /usr/bin/curl "$link7""$date" >/dev/null
        sleep 3
	
	#echo "$link8" >> /data/wwwroot/otherlog/cps_links_wap.log
        #/usr/bin/curl "$link8" >/dev/null
        #sleep 3

	echo "$link9""$date" >> /data/wwwroot/otherlog/cps_links_wap.log
        /usr/bin/curl "$link9""$date" >/dev/null
        sleep 3

	echo "$link10""$date" >> /data/wwwroot/otherlog/cps_links_wap.log
        /usr/bin/curl "$link10""$date" >/dev/null
        sleep 3
		
		echo "$link11""$date" >> /data/wwwroot/otherlog/cps_links_wap.log
        /usr/bin/curl "$link11""$date" >/dev/null
        sleep 3
		
		echo "$link12""$date" >> /data/wwwroot/otherlog/cps_links_wap.log
        /usr/bin/curl "$link12""$date" >/dev/null
        sleep 3
		
		echo "$link13""$date" >> /data/wwwroot/otherlog/cps_links_wap.log
        /usr/bin/curl "$link13""$date" >/dev/null
        sleep 3
		
		echo "$link14""$date" >> /data/wwwroot/otherlog/cps_links_wap.log
        /usr/bin/curl "$link14""$date" >/dev/null
        sleep 3
		
		echo "$link15""$date" >> /data/wwwroot/otherlog/cps_links_wap.log
        /usr/bin/curl "$link15""$date" >/dev/null
        sleep 3
		
	#增加一天
        date=`date -d "+1 day $date" +%Y%m%d`
done




