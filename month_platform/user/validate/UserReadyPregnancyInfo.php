<?php 
namespace app\user\validate;

use think\Validate;

class UserReadyPregnancyInfo extends Validate
{
	protected $rule = [
		['last_menstruation_time','dateFormat:Y-m-d','日期格式请输入:年-月-日'],
		['menstruation_time','number|max:30','经期请输入整数(单位:天)|我想你来月经不会超过30天这么变态吧?有的话请赶往医院就诊'],
		['period','number','经期周期请输入整数'],
	];

	protected $scene = [
		'update'	=> ['last_menstruation_time', 'menstruation_time','period']
	];
}

 ?>