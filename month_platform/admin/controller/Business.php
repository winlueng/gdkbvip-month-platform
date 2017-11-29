<?php
namespace app\admin\controller;

use think\Controller;

class Business extends Common
{
	public function _initialize()
	{
		parent::_initialize();
		$this->obj = model('Business');
	}

	public function postAdd()
	{
		return $this->obj->business_add();
	}

	public function getList()
	{
		return $this->obj->business_list();
	}

	public function getInfo()
	{
		return $this->obj->business_info();
	}

	public function postUpdate()
	{
		return $this->obj->business_update();
	}

	public function getStatus()
	{
		return $this->obj->business_status();
	}
}