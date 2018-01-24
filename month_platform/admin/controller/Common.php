<?php
namespace app\admin\controller;

use think\Controller;

class Common extends Controller
{
	protected $obj;
	protected $user_info;
	protected $redis;
	/*protected $beforeActionList = [// 设置前置方法
		'check_user_info_exist' => [
			'except' => 'getall,postupload_file,getart_work,postlogin' 
		],
	];*/

	public function _initialize()
	{
		parent::_initialize();
        $this->obj = model('Common');
        $this->redis = new \Redis;
        $this->redis->connect('127.0.0.1', 6379);
		$this->user_info = json_decode($this->redis->get(input('get.KB_CODE')), true);
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

	public function check_user_info_exist()
	{
		if (!input('get.KB_CODE')) {
			echo json_encode(return_false(26, '请提交KB_CODE'));exit;
		}

		if (!$this->user_info) {
			echo json_encode(return_false(30, 'KB_CODE过期或用户不存在'));exit;
		}

		$check = model('Rule')->check_admin_rule();
		
		if(is_array($check)) {
			echo json_encode($check);exit;
		}
	}
}