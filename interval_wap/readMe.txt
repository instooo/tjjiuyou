################################################
#       线上定时任务项目名称为P：interval_wap       #
#       此项目为:手游CPS系统的数据统计转移           #
################################################

--------------------------------------------------------------------------------
注册人数定时任务（每天一点执行）
            cron_date_register.php
linux: # crontab -e
        0 1 * * * /usr/local/bin/php /home/Interval/cron_date_register.php?date=20171222
相关表格：mygame_tj_member
参数：date 格式日期(int)--统计指定日期
--------------------------------------------------------------------------------
充值按游戏和媒体统计定时任务（每天一点执行）
            cron_date_pay_bygid.php
linux: # crontab -e
       0 1 * * * /usr/local/bin/php /home/Interval/cron_date_pay_bygid.php?date=20171222
相关表格：mygame_tj_pay_bygid
参数：date 格式日期(int)--统计指定日期
--------------------------------------------------------------------------------
充值按媒体统计定时任务（每天一点执行）
            cron_date_pay_bysub.php
linux: # crontab -e
       0 1 * * * /usr/local/bin/php /home/Interval/cron_date_pay_bysub.php?date=20171222
相关表格：mygame_tj_pay_bysub
参数：date 格式日期(int)--统计指定日期
--------------------------------------------------------------------------------
广告点击统计定时任务(每小时执行一次)
            cron_date_click_by_hour.php
linux: # crontab -e
       00 * * * * /usr/local/bin/php /home/Interval/cron_date_click.php?date=20171222 12:00
相关表格：mygame_tj_click
参数：date 格式日期(int)--统计指定日期
--------------------------------------------------------------------------------
安装统计定时任务(每天一点执行)
            cron_date_install_bygid.php
linux: # crontab -e
       0 1 * * * /usr/local/bin/php /home/Interval/cron_date_install_bygid.php?date=20171222
相关表格：mygame_tj_install_bygid
参数：date 格式日期(int)--统计指定日期
--------------------------------------------------------------------------------
留存统计定时任务(每天一点执行)
            cron_date_liucun.php
linux: # crontab -e
       0 1 * * * /usr/local/bin/php /home/Interval/cron_date_liucun.php?d=3&date=20171222
相关表格：mygame_tj_liucun
参数：date 格式日期(int)--统计指定日期
     d 留存天数：正整数（int）
——————————————————————————————————————————————————
删除广告点击数据(每天一点执行)
    cron_date_liucun.php
linux: # crontab -e
     0 1 * * * /usr/local/bin/php /home/Interval/cron_delete_click.php
相关表格：mygame_tj_click
——————————————————————————————————————————————————
复制用户表(每天一点执行--比cron_date_member_byarea.php先执行)
    cron_copy_member_area.php
linux: # crontab -e
     0 1 * * * /usr/local/bin/php /home/Interval/cron_copy_member_area.php.php
相关表格：mygame_member_area
——————————————————————————————————————————————————
统计用户表地区表注册情况(每天一点执行)
    cron_date_member_byarea.php
linux: # crontab -e
     0 1 * * * /usr/local/bin/php /home/Interval/cron_date_member_byarea.php.php
相关表格：cron_date_member_byarea
——————————————————————————————————————————————————
统计用户表注册情况(每小时执行一次)
    cron_date_member_byhour.php
linux: # crontab -e
     0 1 * * * /usr/local/bin/php /home/Interval/cron_date_member_byhour.php.php
相关表格：cron_date_member_byhour

定时任务type:
cron_date_click_by_hour.php 20
cron_date_install_bygid.php 30
cron_date_liucun.php     42 43 47
cron_date_pay_bygid.php 50
cron_date_pay_bysub.php 60
cron_date_register.php 70
cron_copy_member_area.php 80
cron_date_member_byarea.php 90
cron_date_member_byhour.php 100

cron_copy_pay_log_area.php  110
cron_date_pay_bygid_area.php 120
cron_date_pay_bysub_area.php 121
cron_date_pay_bygid_hour.php 122
cron_date_pay_bysub_hour.php 123

