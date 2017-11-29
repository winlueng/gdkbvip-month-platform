<?php
namespace app\admin\controller;

use think\Controller;

class ArticleComment extends Common
{
	public function _initialize()
	{
		parent::_initialize();
		$this->obj = model('ArticleComment');
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