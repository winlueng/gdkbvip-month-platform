<?php
namespace app\admin\validate;

use think\Validate;

class Admin extends Validate
{
	protected $rule = [
		['admin_name','require|alphaDash|max:10','请输入管理员登录帐号|管理员名称只能是字母、数字和下划线_及破折号-|用户名最大长度为10个有效字符'],
		['password','require|alphaDash','请输入管理员登录密码|登录密码只能是字母、数字和下划线_及破折号-'],
		['repass', 'require|confirm:password', '请输入重复密码|重复密码不一致'],
		['phone', 'require|length:11', '请输入手机号码|请输入11位有效手机号'],
		'phone' => ['regex' => '^((13[0-9])|(14[57])|(15[0-6])|(17[1567])|(18[1-9]))\d{8}|(170[4,7-9])\d{7}$'],
		['email', 'require|regex:/^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/', '请输入邮箱|邮箱格式不正确'],
		['status', 'in:-1,1,2', '状态取值范围(-1,1,2)']
	];

	protected $message = [
		'phone.regex' => '手机号码格式不正确',
	];

	protected $scene = [
        'add'  		=> ['admin_name', 'password', 'phone', 'email', 'repass'],
        'update' 	=> ['admin_name', 'phone', 'email'],
        'login' 	=> ['admin_name' => 'require|alphaDash', 'password'],
        'status' 	=> ['status'],
        'reset_pass'=> [ 'password', 'repass'],
    ];
}