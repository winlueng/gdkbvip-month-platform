<?php
namespace app\user\controller;

use think\Controller;

class Organization extends Common
{
	public function _initialize()
	{
		parent::_initialize();
		$this->obj = model('Organization');
	}

	public function getList()
	{
		return $this->obj->organization_list();
	}

	public function getList_by_location()
	{
		return $this->obj->list_by_location();
	}

	public function getDetail()
	{
		return $this->obj->organization_detail();
	}
}