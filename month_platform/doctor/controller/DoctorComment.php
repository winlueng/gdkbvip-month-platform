<?php
namespace app\doctor\controller;

use think\Controller;

class DoctorComment extends Common
{
	public function _initialize()
	{
		parent::_initialize();
		$this->obj = model('DoctorComment');
	}

	public function getList()
	{
		return $this->obj->comment_list_by_doctor();
	}

	public function getStatus()
	{
		return $this->obj->change_status();
	}
}