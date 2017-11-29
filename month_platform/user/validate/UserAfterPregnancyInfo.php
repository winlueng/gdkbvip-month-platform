<?php 
namespace app\user\validate;

use think\Validate;

class UserAfterPregnancyInfo extends Validate
{
	protected $rule = [
		['baby_sex','number|in:0,1,2','bb性别请输入整数|性别范围(0,1,2)'],
		['baby_birthday','dateFormat:Y-m-d H:i:s','bb出生日期日期格式请输入:年-月-日 时:分:秒'],
		['menstruation_time','number|max:30','经期请输入整数(单位:天)|我想你来月经不会超过30天这么变态吧?有的话请赶往医院就诊'],
		['period','number','经期周期请输入整数'],
	];

	protected $scene = [
		'update'	=> ['baby_sex', 'baby_birthday', 'menstruation_time', 'period']
	];
}

 ?>