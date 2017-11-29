<?php
namespace app\admin\controller;

use think\Controller;
use winleung\exception\WinException;

class Tag extends Common
{
	public function _initialize()
	{
		parent::_initialize();
		$this->obj = model('Tag');
	}

	public function getDel()
	{
		return $this->obj->tag_del();
	}

	public function getTag()
	{
		return $this->obj->tag_list();
	}
}