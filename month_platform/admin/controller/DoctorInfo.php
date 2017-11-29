<?php
namespace app\admin\controller;

use think\Controller;

class DoctorInfo extends Common
{
	public function _initialize()
	{
		parent::_initialize();
		$this->obj = model('DoctorInfo');
	}

	// 审核记录
	public function getApproved()
	{
		return $this->obj->approved();
	}

	public function getList()
	{
		return $this->obj->doctor_list();
	}

	public function getDetail()
	{
		return $this->obj->doctor_detail();
	}

	public function getStatus()
	{
		return $this->obj->change_status();
	}
}