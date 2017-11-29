<?php
namespace app\admin\validate;

use think\Validate;

class RuleGroup extends Validate
{
	protected $rule = [
		['title','require|chsDash','请输入权限中文名称|只能是汉字、字母、数字和下划线_及破折号-'],
		['rule_list', 'require|array', '请提交权限列数据|权限列只能为数组'],
		['is_super', 'require|in:0,1', '请输入是否超级管理员分组(is_super,0:默认, 1:超级管理员)|is_super只能为0或1']
	];

	protected $scene = [
        'add'  =>  ['title', 'rule_list', 'is_super'],
    ];
}