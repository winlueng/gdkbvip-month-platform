<?php
namespace app\user\controller;

use think\Controller;

class UserDetailInfo extends Common
{
	public function _initialize()
	{
		parent::_initialize();
		$this->obj = model('UserDetailInfo');
	}

	public function postUpdate()
	{
		return $this->obj->user_update();
	}
}