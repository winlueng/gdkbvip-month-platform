<?php
namespace app\user\controller;

use think\Controller;
use think\Request;

class Common extends Controller 
{
	protected $obj; // 默认对象继承类
	protected $user_info;
	protected $redis;
	protected $beforeActionList = [// 设置前置方法
		'check_user_info_exist' => [
			'except' => 'postsign_in,postuser_login,getWechat_login,postreset_password_by_phone,postphone_sign_in,postwechat_order_callback'
		],
	];

	public function _initialize()
	{
		parent::_initialize();
		$this->obj = model('Common');
		$this->redis = new \Redis;
		$this->redis->connect('127.0.0.1', 6379);
		$this->user_info = json_decode($this->redis->get(input('get.KB_CODE')), true);
	}

	public function check_user_info_exist()
	{
		if (!input('get.KB_CODE')) {
			echo json_encode(return_false(26, '请提交KB_CODE'));exit;
		}

		if (!$this->user_info) {
			echo json_encode(return_false(30, 'KB_CODE过期或用户不存在'));exit;
		}
	}

	// 前端要求用到的存储接口,存储在redis里
	public function postDl_data()
	{
		return $this->obj->set_and_get_dl_data();
	}
}