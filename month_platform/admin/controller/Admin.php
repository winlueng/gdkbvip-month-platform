<?php
namespace app\admin\controller;

use think\Controller;

class Admin extends Common
{
	public function _initialize()
	{
		parent::_initialize();
		$this->obj = model('Admin');
	}

	public function postAdd()
	{
		return $this->obj->admin_add();
	}

	public function getList()
	{
		return $this->obj->admin_list();
	}

	public function postUpdate()
	{
		return $this->obj->admin_update();
	}

	public function getDel()
	{
		return $this->obj->admin_del();
	}

	public function postLogin()
	{
		return $this->obj->admin_login();
	}

	public function getStatus()
	{
		return $this->obj->change_status();
	}

	public function postReset_password()
	{
		return $this->obj->admin_reset_password();
	}
}