<?php
namespace app\admin\controller;

use think\Controller;

class Admin extends Controller
{
	public function _initialize()
	{
		parent::_initialize();
		$this->obj = model('Admin');
	}

	public function postAdd()
	{
		controller('Common')->check_user_info_exist();
		return $this->obj->admin_add();
	}

	public function getList()
	{
		controller('Common')->check_user_info_exist();
		return $this->obj->admin_list();
	}

	public function postUpdate()
	{
		controller('Common')->check_user_info_exist();
		return $this->obj->admin_update();
	}

	public function getDel()
	{
		controller('Common')->check_user_info_exist();
		return $this->obj->admin_del();
	}

	public function postLogin()
	{
		return $this->obj->admin_login();
	}

	public function getStatus()
	{
		controller('Common')->check_user_info_exist();
		return $this->obj->change_status();
	}

	public function postReset_password()
	{
		controller('Common')->check_user_info_exist();
		return $this->obj->admin_reset_password();
	}
}