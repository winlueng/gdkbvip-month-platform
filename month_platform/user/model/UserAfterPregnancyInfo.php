<?php
namespace app\user\model;

use think\Model;
use winleung\exception\WinException;

class UserAfterPregnancyInfo extends Common
{
	public function setBabyBirthdayAttr($value)
	{
		return strtotime($value);
	}

	public function getBabyBirthdayAttr($value)
	{
	    if ($value){
            return date('Y-m-d H:i:s', $value);
        }
        else{
	        return null;
        }
	}

	public function pregnancy_update()
	{
		try {
			$validate = validate('UserAfterPregnancyInfo');

			if (!$validate->scene('UserAfterPregnancyInfo')->check(input('post.'))) win_exception($validate->getError(), __LINE__);

			if (!self::allowField(['baby_sex', 'baby_birthday', 'menstruation_time', 'period', 'update_time'])->save(input('post.'), ['user_id' => $this->user_info['id']])) win_exception('更新妊娠状态资料失败', __LINE__);

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