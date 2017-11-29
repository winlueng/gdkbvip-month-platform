<?php
namespace app\admin\controller;

use think\Controller;

class Banner extends Common
{
	public function _initialize()
	{
		parent::_initialize();
		$this->obj = model('Banner');
	}

	public function postAdd()
	{
		return $this->obj->banner_add();
	}

	public function getList()
	{
		return $this->obj->banner_list();
	}

	public function getInfo()
	{
		return $this->obj->banner_info();
	}

	public function postUpdate()
	{
		return $this->obj->banner_update();
	}

	public function getStatus()
	{
		return $this->obj->banner_status();
	}
}