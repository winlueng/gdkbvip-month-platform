<?php
namespace app\user\controller;

use think\Controller;

class DoctorComment extends Common
{
	public function _initialize()
	{
		parent::_initialize();
		$this->obj = model('DoctorComment');
	}

	public function postInsert()
	{
		return $this->obj->comment_add();
	}

	public function getList()
	{
		return $this->obj->comment_list_by_doctor();
	}
}