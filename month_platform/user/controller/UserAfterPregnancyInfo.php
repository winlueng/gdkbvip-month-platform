<?php
namespace app\user\controller;

use think\Controller;

class UserAfterPregnancyInfo extends Common
{
	public function _initialize()
	{
		parent::_initialize();
		$this->obj = model('UserAfterPregnancyInfo');
	}

	public function postUpdate()
	{
		return $this->obj->pregnancy_update();
	}

	public function getInfo()
	{
		return $this->obj->pregnancy_info();
	}
}