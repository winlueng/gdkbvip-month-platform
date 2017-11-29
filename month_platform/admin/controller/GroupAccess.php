<?php
namespace app\admin\controller;

class GroupAccess extends Common
{
	public function _initialize()
	{
		parent::_initialize();
		$this->obj = model('GroupAccess');
	}

	public function getUpdate()
	{
		return $this->obj->access_update();
	}
}