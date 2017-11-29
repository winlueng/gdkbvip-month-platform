<?php
namespace app\doctor\validate;

use think\Validate;

class DoctorInfo extends Validate
{
	protected $rule = [
		['password','require|alphaDash','请输入管理员登录密码|登录密码只能是字母、数字和下划线_及破折号-'],
		['phone', 'require|length:11', '请输入手机号码|请输入11位有效手机号'],
		'phone' => ['regex' => '^((13[0-9])|(14[57])|(15[0-6])|(17[1567])|(18[1-9]))\d{8}|(170[4,7-9])\d{7}$'],
		['doctor_name','require|chsAlpha|max:10','请输入姓名|姓名只能是中文、字母、数字和下划线_及破折号-|姓名最大长度为10个有效字符'],
		['doctor_logo','require','请提交本人头像'],
		['sex','require|number|in:1,2','请输入性别|性别必需是整数|性别取值(1:男,2:女)'],
		['tag_classify_id','require|number|notIn:0','请输入标签分类id|标签分类id必需是整数|标签分类id不可为0'],
		['departments_id','require|number|notIn:0','请输入科室id|科室id必需是整数|科室id不可为0'],
		['organization_name','require|chsAlpha','请输入所属机构名称|登所属机构名称只能是中文、字母、数字和下划线_及破折号-'],
		['organization_tel','require','请输入所属机构联系电话'],
		['job_title','require','请输入当前职称'],
		['tag_list','require|array','请输入标签列|标签列必需是数组形式'],
		['skilled','require|max:60','请输入擅长技能|擅长技能不能超过60个有效字符'],
		['introduce','require|max:300','请输入自我描述|自我描述不能超过300个有效字符'],
	];

	protected $message = [
		'phone.regex' => '手机号码格式不正确',
	];

	protected $scene = [
		'update' => ['doctor_name', 'doctor_logo', 'sex', 'tag_classify_id', 'organization_name', 'organization_tel', 'job_title', 'tag_list'],
		'sign_in' => ['phone', 'password'],
	];
}