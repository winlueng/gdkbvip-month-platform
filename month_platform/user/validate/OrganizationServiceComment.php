<?php 
namespace app\user\validate;

use think\Validate;

class OrganizationServiceComment extends Validate
{
	protected $rule = [
		['service_id','require|number','请输入机构id|机构id必需为整数'],
		['attitude_score','require|number','请输入态度评分(1-5分)|态度评分必需为整数'],
		['totality_score','require|number','请输入总体评分(1-5分)|总体评分必需为整数'],
		['comment_info','require|max:200','请输入评论内容|不可超出200有效字符'],
		// ['show_pic','chsDash','分享图片必需是数组'],
	];

	protected $scene = [
		'add'	=> ['service_id', 'attitude_score', 'totality_score', 'comment_info']
	];
}

 ?>