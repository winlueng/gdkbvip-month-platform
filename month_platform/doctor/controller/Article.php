<?php
namespace app\doctor\controller;

use think\Controller;

class Article extends Common
{
	public function _initialize()
	{
		parent::_initialize();
		$this->obj = model('Article');
	}

	public function postAdd()
	{
		return $this->obj->article_add();
	}

	public function getList()
	{
		return $this->obj->article_list();
	}

	public function getInfo()
	{
		return $this->obj->article_info();
	}

	public function getStatus()
	{
		return $this->obj->article_status();
	}

	public function getOther_list()
	{
		return $this->obj->other_article_list();
	}
}