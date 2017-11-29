<?php
namespace app\admin\controller;

use think\Controller;

class User extends Common
{
	public function _initialize()
	{
		parent::_initialize();
		$this->obj = model('User');
	}

	public function getList()
	{
		return $this->obj->user_list();
	}

	public function getDetail()
	{
		return $this->obj->user_detail();
	}

	public function getStatus()
	{
		return $this->obj->change_status();
	}
}