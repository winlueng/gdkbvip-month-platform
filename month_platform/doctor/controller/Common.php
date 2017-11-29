<?php
namespace app\doctor\controller;

use think\Controller;
use think\Request;

class Common extends Controller 
{
	protected $obj; // 默认对象继承类
	protected $user_info;
	protected $redis;
	protected $beforeActionList = [// 设置前置方法
		'check_user_info_exist' => ['except' => 'getsend_code,getverify_code_match,postupload_file,getart_work,postsign_in,postlogin,getreset_password'],
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

	// 返回原图的接口
	public function getArt_work()
	{
		return return_true(['path' => getArtwork(input('get.thumbnail'))]);
	}

	public function postUpload_file()
	{

		return $this->obj->async_upload_file();
	}

	public function getSend_code()
	{
		return $this->obj->send_code();
	}

	public function getVerify_code_match()
	{
		return $this->obj->verify_code_match();
	}
}