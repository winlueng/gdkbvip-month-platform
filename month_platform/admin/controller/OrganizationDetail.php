<?php
namespace app\admin\controller;

use think\Controller;

class OrganizationDetail extends Common
{
	public function _initialize()
	{
		parent::_initialize();
		$this->obj = model('OrganizationDetail');
	}

	public function getDescription()
	{
		return $this->obj->description_info();
	}

	public function postDescription_update()
	{
		return $this->obj->description_update();
	}
}