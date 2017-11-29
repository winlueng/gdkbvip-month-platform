<?php
namespace app\doctor\controller;

use think\Controller;

class Announcement extends Common
{
	public function _initialize()
	{
		parent::_initialize();
		$this->obj = model('Announcement');
	}

	public function getList()
	{
		return $this->obj->announcement_list();
	}

	public function getStatus()
	{
		return $this->obj->change_status();
	}
}
