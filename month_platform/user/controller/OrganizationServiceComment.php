<?php
namespace app\user\controller;

use think\Controller;

class OrganizationServiceComment extends Common
{
	public function _initialize()
	{
		parent::_initialize();
		$this->obj = model('OrganizationServiceComment');
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