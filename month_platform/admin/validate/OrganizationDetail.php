<?php
namespace app\admin\validate;

use think\Validate;

class OrganizationDetail extends Validate
{
	protected $rule = [
		['description', 'require', '请输入店铺介绍'],
		['business_license', 'require', '请提交店铺营业执照'],
		['person_code_front', 'require', '请提交负责人身份证正面照片'],
		['person_code_rear', 'require', '请提交负责人身份证背面照片'],
		['organization_logo', 'require', '请提交店铺logo'],
		['organization_pic', 'require|array', '请提交店铺图片|图片请以数组形式提交'],
		['home_link', 'require|url', '请提交店铺官网链接|链接格式不正确(请以http://或https://开头)'],
		['principal', 'require|chsAlphaNum', '请提交负责人姓名|负责人姓名只能是汉字、字母和数字'],
		['principal_phone', 'require|length:11', '请输入负责人手机号码|负责人请输入11位有效手机号'],
		'principal_phone' => ['regex' => '^((13[0-9])|(14[57])|(15[0-6])|(17[1567])|(18[1-9]))\d{8}|(170[4,7-9])\d{7}$'],
		['principal_email', 'require|regex:/^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/', '请输入负责人邮箱|负责人邮箱格式不正确'],
	];

	protected $message = [
		'principal_phone.regex' => '手机号码格式不正确',
	];

	protected $scene = [
		'add' => ['business_license', 'person_code_front', 'person_code_rear', 'principal', 'principal_phone', 'principal_email', 'organization_logo', 'organization_pic', 'home_link'],
		'update' => ['organization_logo', 'organization_pic'],
	];
}