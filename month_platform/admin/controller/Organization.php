<?php
namespace app\admin\controller;

use think\Controller;

class Organization extends Common
{
	public function _initialize()
	{
		parent::_initialize();
		$this->obj = model('Organization');
	}

	public function postAdd()
	{
		return $this->obj->organization_add();
	}

	public function getList()
	{
		return $this->obj->organization_list();
	}

	public function getInfo()
	{
		return $this->obj->organization_info();
	}

	public function postUpdate()
	{
		return $this->obj->organization_update();
	}

	public function getStatus()
	{
		return $this->obj->organization_status();
	}
}