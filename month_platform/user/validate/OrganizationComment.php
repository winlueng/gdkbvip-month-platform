<?php 
namespace app\user\validate;

use think\Validate;

class OrganizationComment extends Validate
{
	protected $rule = [
		['organization_id','require|number','请输入机构id|机构id必需为整数'],
		['attitude_score','require|number','请输入态度评分(1-5分)|态度评分必需为整数'],
		['totality_score','require|number','请输入总体评分(1-5分)|总体评分必需为整数'],
		['comment_info','require|chsDash','请输入评论内容|评论内容只能是汉字、字母、数字和下划线_及破折号-'],
		['show_pic','chsDash','分享图片必需是数组'],
	];

	protected $scene = [
		'add'	=> ['organization_id', 'attitude_score', 'totality_score', 'comment_info']
	];
}

 ?>