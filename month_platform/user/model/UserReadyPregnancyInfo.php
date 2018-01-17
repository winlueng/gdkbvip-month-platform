<?php
namespace app\user\model;

use think\Model;
use winleung\exception\WinException;

class UserReadyPregnancyInfo extends Common
{
	public function setLastMenstruationTimeAttr($value)
	{
		return strtotime($value);
	}

	public function getLastMenstruationTimeAttr($value)
	{
        if ($value){
            return date('Y-m-d', $value);
        }
        else{
            return null;
        }
	}

	public function scopeUid($sql)
	{
		$sql->where('user_id', $this->user_info['id']);
	}

	public function pregnancy_update()
	{
		try {
			$validate = validate('UserReadyPregnancyInfo');

			if (!$validate->scene('update')->check(input('post.'))) {
				win_exception($validate->getError(), __LINE__);
			}

			if (!self::allowField(['last_menstruation_time', 'menstruation_time','period', 'update_time'])->save(input('post.'), ['user_id' => $this->user_info['id']])) {
				win_exception('更新数据失败', __LINE__);
			}

			return return_no_data();
		} catch (WinException $e) {
			return $e->false();
		}
	}

	public function pregnancy_info()
	{
		try {
			$info = self::uid()
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