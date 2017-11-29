<?php
namespace app\user\controller;

use think\Controller;

class User extends Common
{
	public function _initialize()
	{
		parent::_initialize();
		$this->obj = model('User');
	}

	public function postSign_in()
	{
		return $this->obj->sign_in();
	}

	public function postUser_login()
	{
		return $this->obj->user_login();
	}

	public function getWechat_login()
	{
		return $this->obj->wechat_login();
	}

	public function postPhone_sign_in()
	{
		return $this->obj->phone_sign_in();
	}

	public function postReset_password_by_phone()
	{
		return $this->obj->reset_password_by_phone();
	}

	public function postUpdate()
	{
		return $this->obj->info_update();
	}

	public function getInfo()
	{
		return $this->obj->user_info();
	}
}