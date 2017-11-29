<?php
namespace app\user\controller;

use think\Controller;

class UserSymptomatography extends Common
{
	public function _initialize()
	{
		parent::_initialize();
		$this->obj = model('UserSymptomatography');
	}

	// 记录症状问题
	public function postRecord()
	{
		return $this->obj->question_record();
	}

	public function getUser_symptomatography()
	{
		return $this->obj->get_user_last_record();
	}
}