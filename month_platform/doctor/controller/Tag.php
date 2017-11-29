<?php
namespace app\doctor\controller;

use think\Controller;

class Tag extends Common
{
	public function _initialize()
	{
		parent::_initialize();
		$this->obj = model('Tag');
	}

	public function getTag()
	{
		return $this->obj->tag_list();
	}
}