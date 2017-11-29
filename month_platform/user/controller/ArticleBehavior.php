<?php
namespace app\user\controller;

use think\Controller;

class ArticleBehavior extends Common
{
	public function _initialize()
	{
		parent::_initialize();
		$this->obj = model('ArticleBehavior');
	}

	public function getBehavior()
	{
		return $this->obj->note_down_user_article_behavior();
	}

	public function getMy_save_list()
	{
		return $this->obj->get_my_save();
	}

	public function getNo_save()
	{
		return $this->obj->change_no_save();
	}

	public function getMy_like_list()
	{
		return $this->obj->get_my_like();
	}

	public function getNo_like()
	{
		return $this->obj->change_no_like();
	}

	public function getPraise_count()
	{
		return $this->obj->where('article_id', input('get.id'))
						 ->where('is_like', '1')
						 ->count();
	}
}