<?php
namespace app\admin\validate;

use think\Validate;

class Business extends Validate
{
	protected $rule = [
		['name','require|chsDash|max:4','请输入行业名称|行业名称只能是汉字、字母、数字和下划线_及破折号-|行业名称最大长度为4个有效字符'],
		['description','require|chsDash|max:20','请输入行业描述|行业描述最大长度为20个有效字符'],
	];

	protected $scene = [
		'add' => ['name', 'description'],
	];
}