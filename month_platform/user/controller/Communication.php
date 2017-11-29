<?php
namespace app\user\controller;

use think\Controller;

class Communication extends Common
{
	public function _initialize()
	{
		parent::_initialize();
		$this->obj = model('Communication');
		// 星号表示所有的域都可以接受跨域访问，
		header("Access-Control-Allow-Origin:*");
		header("Access-Control-Allow-Methods:GET,POST");
	}

	public function postBind_user()
	{
		return $this->obj->bind_user_id();
	}

	public function postBind_doctor()
	{
		return $this->obj->bind_doctor_id();
	}

	public function postSend_to_doctor()
	{
		return $this->obj->send_message_by_user();
	}

	public function postSend_to_user()
	{
		return $this->obj->send_message_by_doctor();
	}

	public function getCheck_doctor_online()
	{
		return $this->obj->check_doctor_is_online();
	}

	public function getCheck_user_online()
	{
		return $this->obj->check_user_is_online();
	}
}