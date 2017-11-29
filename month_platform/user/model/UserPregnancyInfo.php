<?php
namespace app\user\model;

use think\Model;
use winleung\exception\WinException;

class UserPregnancyInfo extends Common
{
	public function setPregnancyDateAttr($value)
	{
		return strtotime($value);
	}

	public function getPregnancyDateAttr($value)
	{
		return date('Y-m-d', $value);
	}

	public function setDueDateAttr($value)
	{
		return strtotime($value);
	}

	public function getDueDateAttr($value)
	{
		return date('Y-m-d', $value);
	}

	public function pregnancy_update()
	{
		try {
			$validate = validate('UserPregnancyInfo');

			if (!$validate->scene('UserPregnancyInfo')->check(input('post.'))) win_exception($validate->getError(), __LINE__);

			if (!self::allowField(['due_date', 'pregnancy_date', 'update_time'])->save(input('post.'), ['user_id' => $this->user_info['id']])) win_exception('更新妊娠状态资料失败', __LINE__);

			return return_no_data();
		} catch (WinException $e) {
			return $e->false();
		}
	}

	public function pregnancy_info()
	{
		try {
			$info = self::where('user_id', $this->user_info['id'])
						->find();

			if (!$info) {
				win_exception('', __LINE__);
			}

			return return_true($info);
		} catch (WinException $e) {
			return $e->false();
		}
	}
}