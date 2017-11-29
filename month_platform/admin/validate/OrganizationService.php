<?php
namespace app\admin\validate;

use think\Validate;

class OrganizationService extends Validate
{
	protected $rule = [
		['service_name', 'require|chsDash|max:20', '请输入店铺介绍|服务名称只能是中文、字母、数字和下划线_及破折号-|名称最长为20个有效字符'],
		['organization_id', 'require|number|notIn:0', '请输入店铺id|店铺id只能为证书|店铺id不能为0'],
		['logo', 'require', '请提交服务logo'],
		['service_pic', 'require|array', '请提交服务服务详情图片|详情图片请输入为数组'],
		['detail', 'require', '请输入服务详情'],
		['price', 'require|float|>=:0', '请输入服务原价|原价只能为浮点数|原价格必需大于等于0'],
		['discount_price', 'require|float|>=:0|<:price', '请输入服务现价|现价只能为浮点数|现价格必需大于等于0|现价必须小于原价'],
	];

	protected $scene = [
		'add'    => ['service_name', 'organization_id', 'logo', 'service_pic', 'detail', 'price', 'discount_price'],
		'update' => ['service_name', 'logo', 'service_pic', 'detail', 'price', 'discount_price']
	];
}