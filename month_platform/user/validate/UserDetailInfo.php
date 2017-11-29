<?php 
namespace app\user\validate;

use think\Validate;

class UserDetailInfo extends Validate
{
	protected $rule = [
		['weight','float','体重请输入数字格式'],
		['height','float','身高请输入数字格式'],
	];

	protected $scene = [
		'update'	=> ['weight', 'height']
	];
}

 ?>