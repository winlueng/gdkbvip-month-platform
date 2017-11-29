<?php
namespace app\admin\controller;

use think\Controller;

class DoctorQuestionOrder extends Common
{
	public function _initialize()
	{
		parent::_initialize();
		$this->obj = model('DoctorQuestionOrder');
	}

	public function getList()
	{
		return $this->obj->order_list();
	}
}