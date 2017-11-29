<?php 
namespace app\user\validate;

use think\Validate;

class UserPregnancyInfo extends Validate
{
	protected $rule = [
		['due_date','dateFormat:Y-m-d','预产期日期格式请输入:年-月-日'],
		['pregnancy_date','dateFormat:Y-m-d','怀孕日期日期格式请输入:年-月-日'],
	];

	protected $scene = [
		'update'	=> ['due_date', 'pregnancy_date']
	];
}

 ?>