<?php
namespace app\doctor\controller;

use think\Controller;

class DoctorInfo extends Common
{
	public function _initialize()
	{
		parent::_initialize();
		$this->obj = model('DoctorInfo');
	}

	public function postLogin()
	{
		return $this->obj->doctor_login();
	}

	public function postSign_in()
	{
		return $this->obj->doctor_sign_in();
	}

	// 资料更新
	public function postUpdate()
	{
		return $this->obj->info_update();
	}

	public function getReset_password()
	{
		return $this->obj->reset_password();
	}

	public function getInfo()
	{
		return $this->obj->doctor_info();
	}

	public function postOther_update()
	{
		return $this->obj->other_update();
	}

	public function getOpen_control()
	{
		return $this->obj->open_or_close();
	}
}