<?php
namespace app\user\controller;

use think\Controller;

class Business extends Common
{
	public function _initialize()
	{
		parent::_initialize();
		$this->obj = model('Business');
	}
}