<?php
namespace app\user\controller;

use think\Controller;

class Search extends Common
{
	public function _initialize()
	{
		parent::_initialize();
		$this->obj = model('Search');
	}

	public function getHot_words()
	{
		return $this->obj->hot_words();
	}

	public function getHistory_words()
	{
		return $this->obj->history_words();
	}

	public function getGo_article_search()
	{
		return $this->obj->go_article_search();
	}
	
	public function getGo_doctor_search()
	{
		return $this->obj->go_doctor_search();
	}
}