<?php
namespace app\user\controller;

use think\Controller;

class OrganizationComment extends Common
{
	public function _initialize()
	{
		parent::_initialize();
		$this->obj = model('OrganizationComment');
	}

	public function postInsert()
	{
		return $this->obj->comment_add();
	}

	public function getList()
	{
		return $this->obj->comment_list_by_organization();
	}
}