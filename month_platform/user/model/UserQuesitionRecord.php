<?php
namespace app\user\model;

use think\Model;
use winleung\exception\WinException;

class UserQuestionRecord extends Common
{
	public function setUserIdAttr()
	{
		return $this->user_info['id'];
	}

	public function question_record($title = '')
	{
		if (!$title) return false;

		if (self::save(['question_title' => $title])) return true;

		return false;
	}
}