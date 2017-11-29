<?php
namespace app\user\controller;

use think\Controller;

class DoctorBehavior extends Common
{
	public function _initialize()
	{
		parent::_initialize();
		$this->obj = model('DoctorBehavior');
	}

	public function getBehavior()
	{
		return $this->obj->note_down_user_doctor_behavior();
	}

	public function getMy_save_list()
	{
		return $this->obj->get_my_save();
	}

	public function getNo_save()
	{
		return $this->obj->change_no_save();
	}
}