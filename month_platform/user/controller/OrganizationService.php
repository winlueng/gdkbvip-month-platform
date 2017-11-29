<?php
namespace app\user\controller;

use think\Controller;

class OrganizationService extends Common
{
	public function _initialize()
	{
		parent::_initialize();
		$this->obj = model('OrganizationService');
	}

	public function getLimit_list()
	{
		return $this->obj->service_list();
	}

	public function getDetail()
	{
		return $this->obj->service_detail();
	}
}