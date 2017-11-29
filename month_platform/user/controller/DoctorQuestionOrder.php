<?php
namespace app\user\controller;

use think\Controller;

class DoctorQuestionOrder extends Common
{
	public function _initialize()
	{
		parent::_initialize();
		$this->obj = model('DoctorQuestionOrder');
	}

	public function getCreate()
	{
		return $this->obj->create_order();
	}

	public function getCheck_doctor()
	{
		return $this->obj->check_doctor_order_exist();
	}

	public function getCheck_user()
	{
		return $this->obj->check_user_order_exist();
	}

	public function getCut()
	{
		return $this->obj->cut_order();
	}

	public function postWechat_order_callback()
	{
		$this->obj->order_callback();
	}

	public function getDoctor_income()
	{
		return $this->obj->doctor_income_account();
	}
}