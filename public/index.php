<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

// [ 应用入口文件 ]
// echo phpinfo(); exit;
// 定义应用目录
define('APP_PATH', __DIR__ . '/../month_platform/');
// define('EXTEND_PATH', __DIR__ . '/../exnted/');
if ($_SERVER['HTTP_HOST'] != 'img.month.gdkbvip.com') {
	require __DIR__ . '/../thinkphp/start.php';
}
// 加载框架引导文件
