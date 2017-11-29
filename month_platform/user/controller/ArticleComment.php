<?php
namespace app\user\controller;

use think\Controller;

class ArticleComment extends Common
{
	public function _initialize()
	{
		parent::_initialize();
		$this->obj = model('ArticleComment');
	}

	public function postInsert()
	{
		return $this->obj->comment_add();
	}

	public function getList()
	{
		return $this->obj->comment_list_by_article();
	}

	public function getStatus()
	{
		return $this->obj->change_comment_status();
	}

	public function getMy_comment()
	{
		return $this->obj->my_comment_list();
	}

	public function getPraise_control()
	{
		return $this->obj->praise_or_cancel();
	}
}