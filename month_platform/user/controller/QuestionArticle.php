<?php
namespace app\user\controller;

use think\Controller;

class QuestionArticle extends Common
{
	public function _initialize()
	{
		parent::_initialize();
		$this->obj = model('QuestionArticle');
	}

	public function getCorrelation_article()
	{
		return $this->obj->correlation_article();
	}

	public function getInfo()
	{
		return $this->obj->article_info();
	}
}