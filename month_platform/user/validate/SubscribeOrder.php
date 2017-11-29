<?php 
namespace app\user\validate;

use think\Validate;

class SubscribeOrder extends Validate
{
	protected $rule = [
		['organization_id','require|number','请输入机构id|机构id必需为整数'],
		['phone', 'require|length:11', '请输入手机号码|请输入11位有效手机号'],
		'phone' => ['regex' => '^((13[0-9])|(14[57])|(15[0-6])|(17[1567])|(18[1-9]))\d{8}|(170[4,7-9])\d{7}$'],
		['come_man','require|chsDash','请填写预约人姓名|预约人姓名只能是汉字、字母、数字和下划线_及破折号-'],
	];

	public function __construct()
	{
		parent::__construct();
		$this->rule[] = ['come_time','require|dateFormat:Y-m-d H:i:s|after:'.date('Y-m-d H:i:s'),'请输入到店时间|时间格式未: 年-月-日 时:分:秒|预约到店时间必需大于当前时间'];
	}

	protected $message = [
		'phone.regex' => '预约人手机号码格式不正确',
	];

	protected $scene = [
		'add'	=> ['organization_id', 'phone', 'come_time', 'come_man'],
	];
}

 ?>