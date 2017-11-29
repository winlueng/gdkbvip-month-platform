<?php
namespace app\base\controller;

use think\Controller;

class BaseApi extends Common
{
	public function _initialize()
	{
		parent::_initialize();
		$this->obj = model('BaseApi');
	}

	public function getSendSystemNews()
	{
		return $this->obj->sendSystemNews();
	}
}
