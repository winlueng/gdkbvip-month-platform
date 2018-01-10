<?php 
namespace app\user\validate;

use think\Validate;

class User extends Validate
{
	protected $rule = [
		['open_id','require','请输入微信登录open_id'],
		['nick_name','require','请输入昵称'],
		['head_url','require','请输入头像路径'],
		['sex','require|number|in:0,1,2','请输入性别|性别必需是整数|性别范围只在0,1,2'],
//		['real_name','chsAlpha','性别必需是整数'],
		['phone', 'require|length:11', '请输入手机号码|请输入11位有效手机号'],
		'phone' => ['regex' => '^((13[0-9])|(14[57])|(15[0-6])|(17[1567])|(18[1-9]))\d{8}|(170[4,7-9])\d{7}$'],
		['password','require','请输入密码'],
		['birthday', 'dateFormat:Y-m-d', '生日日期格式请用:年-月-日'],
		['pregnancy_status', 'number|in:1,2,3', '妊娠状态必需为整数|妊娠状态只在(1,2,3)取值'],
	];

	protected $message = [
		'phone.regex' => '手机号码格式不正确',
	];
	
	protected $scene = [
		'sign_in' => ['open_id', 'nick_name', 'head_url', 'sex'],
		'update_phone' => ['phone', 'password'],
		'update'	=> ['nick_name', 'head_url', 'sex', 'real_name','birthday']
	];
}

 ?>