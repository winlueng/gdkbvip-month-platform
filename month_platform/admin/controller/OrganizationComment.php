<?php
namespace app\admin\controller;

use think\Controller;

class OrganizationComment extends Common
{
	public function _initialize()
	{
		parent::_initialize();
		$this->obj = model('OrganizationComment');
	}

	public function getList()
	{
		return $this->obj->comment_list();
	}

	public function getStatus()
	{
		return $this->obj->change_status();
	}
}