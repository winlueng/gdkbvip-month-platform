<?php 
namespace app\user\validate;

use think\Validate;

class UserSymptomatography extends Validate
{
	protected $rule = [
		['symptom','require|max:200','请输入症状内容|症状内容不能超过200字'],
		['symptom_img','array','症状图片必需是数组'],
	];
	
	protected $scene = [
		'create' => ['symptom', 'symptom_img'],
	];
}

 ?>