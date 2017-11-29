<?php
namespace app\admin\controller;

use think\Controller;

class OrganizationService extends Common
{
	public function _initialize()
	{
		parent::_initialize();
		$this->obj = model('OrganizationService');
	}

	public function postAdd()
	{
		return $this->obj->service_add();
	}

	public function getList()
	{
		return $this->obj->service_list();
	}

	public function getInfo()
	{
		return $this->obj->service_info();
	}

	public function postUpdate()
	{
		return $this->obj->service_update();
	}

	public function getStatus()
	{
		return $this->obj->service_status();
	}
}