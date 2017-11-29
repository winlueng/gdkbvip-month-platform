<?php
namespace app\user\controller;

use think\Controller;

class Classify extends Common
{
	public function _initialize()
	{
		parent::_initialize();
		$this->obj = model('Classify');
	}

	public function getSort_list()
	{
		return $this->obj->header_classify_list();
	}

	public function getAll_list()
	{
		return $this->obj->classify_list();
	}

	public function postSort()
	{
		return $this->obj->classify_set_sort();
	}
}