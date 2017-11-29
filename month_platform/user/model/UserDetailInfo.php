<?php
namespace app\user\model;

use think\Model;
use winleung\exception\WinException;

class UserDetailInfo extends Common
{
	public function User()
	{
		return $this->belongsToMany('User');
	}

	public function getLastLoginTimeAttr($value)
	{
		return date('Y-m-d H:i:s', $value);
	}

	public function user_update()
	{
		try {
			$validate = validate('UserDetailInfo');

			if (!$validate->scene('update')->check(input('post.'))) win_exception($validate->getError(), __LINE__);

			if (!self::allowField(['weight','height'])->save(input('post.'), ['user_id' => $this->user_info['id']])) win_exception('修改数据失败', __LINE__);

			return return_no_data();
		} catch (WinException $e) {
			return $e->false();
		}
	}
}