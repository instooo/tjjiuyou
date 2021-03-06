<?php
$rbac = include('rbac.php');
//'配置项'=>'配置值'
$config = array(
	/* 模板相关配置 */
	'TMPL_PARSE_STRING' => array(
			'__APP__' => __ROOT__.'/index.php/'.MODULE_NAME,					// 更改默认的__APP__ 替换规则				
			'__IMAGE__'    =>  '/Public/'. MODULE_NAME . '/images',
			'__CSS__'      =>  '/Public/'.MODULE_NAME . '/css',
			'__JS__'       =>  '/Public/'. MODULE_NAME . '/js',
			'__DATE__'       =>  '/Public/'. MODULE_NAME . '/My97DatePicker',		
	),	
	'USER_AUTH_KEY'      =>'authId',	       // 用户认证SESSION标记
	/* 语言设置 */
	'LANG_SWITCH_ON'=>true,
	'DEFAULT_LANG'=>'zh-cn',
	'LANG_AUTO_DETECT'=>false,
	'LANG_LIST'=>'en-us,zh-cn,zh-tw',
	/***CronSign***/
	'TIMESIGN'=>'9120b3e216ff71f1e9c3cbb588c3ca80',	
	
	'DB_TYPE'               =>  'mysql',     // 数据库类型
	'DB_HOST'               =>  '192.168.1.241', // 服务器地址
	'DB_NAME'               =>  '7477_me_sycps',          // 数据库名
	'DB_USER'               =>  'sycps',      // 用户名
	'DB_PWD'                =>  '123456',          // 密码
	'DB_PORT'               =>  '3306',        // 端口
	'DB_PREFIX'             =>  'mygame_',    // 数据库表前缀

	'DB_CONFIG_ZHU' => array(
		'db_type'  => 'mysql',
		'db_user'  => 'sycps',
		'db_pwd'   => '123456',
		'db_host'  => '192.168.1.241',
		'db_port'  => '3306',
		'db_name'  => '7477_me_sycps'
	),		
	
);


return array_merge($config, $rbac);