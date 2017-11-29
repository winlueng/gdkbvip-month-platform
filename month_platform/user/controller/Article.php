<?php
namespace app\user\controller;

use think\Controller;

class Article extends Common
{
	public function _initialize()
	{
		parent::_initialize();
		$this->obj = model('Article');
	}

	// 根据分类获取文章数据
	public function getArticle_by_classify()
	{
		return $this->obj->get_article_by_classify();
	}

	public function getRecommend_list()
	{

		return $this->obj->recommend_list();
	}

	public function getInfo()
	{
		return $this->obj->article_info();
	}

	public function getDoctor_article()
	{
		return $this->obj->article_list_by_doctor();
	}
}