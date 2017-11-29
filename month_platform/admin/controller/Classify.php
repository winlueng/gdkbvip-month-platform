<?php
namespace app\admin\controller;

use think\Controller;

class Classify extends Common
{
	public function _initialize()
	{
		parent::_initialize();
		$this->obj = model('Classify');
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

	public function getTop()
	{
		return $this->obj->top_classify();
	}

	public function getChild()
	{
		return $this->obj->child_classify();
	}

	public function getChild_list()
	{
		return $this->obj->child_list();
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
