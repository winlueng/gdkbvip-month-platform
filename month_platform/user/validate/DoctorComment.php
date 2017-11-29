<?php 
namespace app\user\validate;

use think\Validate;

class DoctorComment extends Validate
{
	protected $rule = [
		['doctor_id','require|number|notIn:0','请输入医生id|医生id必需为整数|医生id不可为0'],
		['comment_info', 'require|max:200', '请输入评论内容|评论内容最大字数为200']
	];

	protected $scene = [
		'create'	=> ['doctor_id', 'comment_info']
	];
}

 ?>