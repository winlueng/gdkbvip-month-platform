<?php
namespace app\doctor\controller;

use think\Controller;

class TagClassify extends Common
{
	public function _initialize()
	{
		parent::_initialize();
		$this->obj = model('TagClassify');
	}
	
	public function getList()
	{
		return $this->obj->classify_list();
	}
}