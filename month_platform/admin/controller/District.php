<?php
namespace app\admin\controller;

use think\Controller;

class District extends Common
{
	public function _initialize()
	{
		parent::_initialize();
		$this->obj = model('District');
	}

	public function getAll()
	{
		return return_true($this->obj->select());
	}

	public function getList()
	{
		return $this->obj->district_list();
	}

	public function getInfo()
	{
		return $this->obj->district_info();
	}
}