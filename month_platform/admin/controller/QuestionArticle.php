<?php
namespace app\admin\controller;

use think\Controller;

class QuestionArticle extends Common
{
	public function _initialize()
	{
		parent::_initialize();
		$this->obj = model('QuestionArticle');
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

	public function postUpdate()
	{
		return $this->obj->article_update();
	}
}