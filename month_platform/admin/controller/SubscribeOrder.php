<?php
namespace app\admin\controller;

use think\Controller;

class SubscribeOrder extends Common
{
	public function _initialize()
	{
		parent::_initialize();
		$this->obj = model('SubscribeOrder');
	}

	public function getList()
	{
		return $this->obj->order_list();
	}

	public function getDetail()
	{
		return $this->obj->order_detail();
	}

	public function getStatus()
	{
		return $this->obj->change_status();
	}

	public function getReset_come_time()
	{
		return $this->obj->reset_come_time();
	}
}