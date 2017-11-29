<?php
namespace app\admin\controller;

use think\Controller;

class NewsRecord extends Common
{
	public function _initialize()
	{
		parent::_initialize();
		$this->obj = model('NewsRecord');
	}

	public function getList()
	{
		return $this->obj->news_history();
	}
}