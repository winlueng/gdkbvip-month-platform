<?php
namespace app\admin\validate;

use think\Validate;

class Organization extends Validate
{
	protected $rule = [
		['organization_ip','require|alphaDash','请输入店铺编号|店铺编号只能是字母、数字和下划线_及破折号-'],
		['organization_name','require|max:40','请输入店铺名称|店铺名称最多40个字'],
		['synopsis','require','请输入店铺简介|店铺名称最多40个字'],
		['postfix','require|alphaDash|max:10','请输入店铺域名后缀|域名后缀只能是字母、数字和下划线_及破折号-|域名后缀最长不能超过10个有效字符'],
		['business_id','require|number|notIn:0','请输入店铺行业id|行业id只能为整数|行业id不为0'],
		['address_info','require','请输入店铺详细地址'],
		['province_id','require|number|notIn:0','请输入店铺省级id|省级id只能是整数|省级id不为0'],
		['city_id','require|number|notIn:0','请输入店铺市级id|市级id只能是整数|市级id不为0'],
		['district_id','require|number|notIn:0','请输入店铺区级id|区级id只能是整数|区级id不为0'],
		['x_point','require|between:0,180','请输入纬度坐标|坐标值只能在0-180区间'],
		['y_point','require|between:0,90','请输入经度坐标|坐标值只能在0-90区间'],
		['start_time','require|<:end_time|notIn:0','请输入店铺有效开始时间|有效开始时间必需小于有效结束时间|开始时间不可为0'],
		['end_time','require|>:start_time|notIn:0','请输入店铺有效结束时间|有效结束时间必需大于有效开始时间|结束时间不可为0'],
		['make_a_contract_time','require|<=:start_time|notIn:0','请输入店铺有效签约时间|有效签约时间必需小于或等于有效开始时间|签约时间不可为0'],
	];

	protected $scene = [
		'add' => ['organization_ip', 'organization_name', 'postfix', 'business_id', 'address_info', 'address_id', 'x_point', 'y_point', 'start_time', 'end_time', 'make_a_contract_time'],
		'update' => ['postfix', 'end_time']
	];
}