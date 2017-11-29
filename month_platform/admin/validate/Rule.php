<?php
namespace app\admin\validate;

use think\Validate;

class Rule extends Validate
{
	protected $rule = [
		['title','require|chsDash','请输入权限中文名称|权限中文名称只能是汉字、字母、数字和下划线_及破折号-'],
		['module', 'require|alpha', '请输入模块名称|模块名称只能输入字母'],
		['controller', 'require|alpha', '请输入控制器名称|控制器名称只能输入字母'],
		['method', 'require|alphaDash', '请输入方法名称|方法名称只能是字母和数字，下划线_及破折号-'],
		['parent_id', 'require|number', '请输入父级权限parent_id(0为顶级权限)|只能输入数字'],
		['is_display', 'require|in:0,1', '请输入是否显示字段(is_display)|值只能在(0:默认不显示,1:显示)'],
	];

	protected $scene = [
        'add'  =>  ['title', 'module', 'controller', 'method', 'parent_id', 'is_display'],
    ];
}