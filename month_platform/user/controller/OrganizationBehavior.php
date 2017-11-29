<?php
namespace app\user\controller;

use think\Controller;

class OrganizationBehavior extends Common
{
	public function _initialize()
	{
		parent::_initialize();
		$this->obj = model('OrganizationBehavior');
	}

	public function getBehavior()
	{
		return $this->obj->note_down_user_organization_behavior();
	}

	public function getMy_save_list() 
	{
		return $this->obj->get_my_save();
	}

	public function getNo_save()
	{
		return $this->obj->change_no_save();
	}
}