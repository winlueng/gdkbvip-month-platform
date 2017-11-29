<?php
namespace app\admin\controller;

class Rule extends Common
{
	public function _initialize()
	{
		parent::_initialize();
		$this->obj = model('Rule');
	}

	public function postRule_add()
	{
		return $this->obj->rule_add();
	}

	public function getRule_list()
	{
		return $this->obj->rule_list()->dispose_list();
	}

	public function postRule_update()
	{
		return $this->obj->rule_update();
	}

	public function getSort()
	{
		return $this->obj->sort_to_change();
	}

	public function getDel()
	{
		return $this->obj->rule_del();
	}
}