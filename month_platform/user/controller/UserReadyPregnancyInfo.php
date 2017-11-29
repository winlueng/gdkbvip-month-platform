<?php
namespace app\user\controller;

use think\Controller;

class UserReadyPregnancyInfo extends Common
{
	public function _initialize()
	{
		parent::_initialize();
		$this->obj = model('UserReadyPregnancyInfo');
	}

	public function getInfo()
	{
		return $this->obj->pregnancy_info();
	}

	public function postUpdate()
	{
		return $this->obj->pregnancy_update();
	}
}