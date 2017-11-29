<?php
namespace app\user\controller;

use think\Controller;

class SubscribeOrder extends Common
{
	public function _initialize()
	{
		parent::_initialize();
		$this->obj = model('SubscribeOrder');
	}

	public function postInsert()
	{
		return $this->obj->order_add();
	}

	public function getList()
	{
		return $this->obj->order_list();
	}

	public function getInfo()
	{
		return $this->obj->order_info();
	}

	public function getCancel()
	{
		return $this->obj->order_cancel();
	}

	public function getDel()
	{
		return $this->obj->order_del();
	}
}
