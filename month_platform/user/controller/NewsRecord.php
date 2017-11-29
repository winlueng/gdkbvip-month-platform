<?php
namespace app\user\controller;

use think\Controller;

class NewsRecord extends Common
{
	public function _initialize()
	{
		parent::_initialize();
		$this->obj = model('NewsRecord');
	}

	public function getUser_history()
	{
		return $this->obj->get_history_news_by_user();
	}

	public function getDoctor_history()
	{
		return $this->obj->get_history_news_by_doctor();
	}

	public function getTurn_read_status_by_user()
	{
		return $this->obj->set_news_is_read_by_user();
	}

	public function getTurn_read_status_by_doctor()
	{
		return $this->obj->set_news_is_read_by_doctor();
	}

	public function getUser_question_history()
	{
		return $this->obj->user_question_history();
	}

	public function getDoctor_question_history()
	{
		return $this->obj->doctor_question_history();
	}
} 