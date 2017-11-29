<?php
namespace app\user\controller;

class DoctorInfo extends Common
{
	public function _initialize()
	{
		parent::_initialize();
		$this->obj = model('DoctorInfo');
	}

	public function getInfo()
	{
		return $this->obj->doctor_info();
	}

	public function getList_by_departments()
	{
		return $this->obj->doctor_list_by_departments();
	}

	public function getCheck_open()
	{
		return $this->obj->check_open();
	}
}