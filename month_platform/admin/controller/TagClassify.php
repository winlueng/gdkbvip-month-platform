<?php
namespace app\admin\controller;

use think\Controller;

class TagClassify extends Common
{
	public function _initialize()
	{
		parent::_initialize();
		$this->obj = model('TagClassify');
	}

	public function postAdd()
	{
		return $this->obj->classify_add();
	}

	public function getList()
	{
		return $this->obj->classify_list();
	}

	public function getInfo()
	{
		return $this->obj->classify_info();
	}

	public function postUpdate()
	{
		return $this->obj->classify_update();
	}

	public function getDel()
	{
		return $this->obj->classify_del();
	}
}