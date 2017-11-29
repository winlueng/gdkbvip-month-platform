<?php
namespace app\admin\model;

use think\Model;
use winleung\exception\WinException;

class User extends Common
{
	public function UserDetailInfo()
	{
		return $this->hasMany('UserDetailInfo', 'user_id', 'id');
	}

	public function UserAfterPregnancyInfo()
	{
		return $this->hasMany('UserAfterPregnancyInfo', 'user_id', 'id');
	}

	public function UserPregnancyInfo()
	{
		return $this->hasMany('UserPregnancyInfo', 'user_id', 'id');
	}

	public function UserReadyPregnancyInfo()
	{
		return $this->hasMany('UserReadyPregnancyInfo', 'user_id', 'id');
	}

	public function scopeLike()
	{
		if (input('get.nick_name')) {
			$sql->where('nick_name', 'like', '%'. input('get.nick_name') .'%');
		}
	}

	public function user_list()
	{
		try {
			$list = self::status()
						->like()
						->order('create_time desc')
						->paginate(10)
						->hidden(['update_time', 'password'])
						->toArray();

			if (!$list) {
				win_exception('', __LINE__);
			}

			$data_total = self::status()
									  ->like()
									  ->count();

			return return_true($list, '', $data_total);
		} catch (WinException $e) {
			return $e->false();
		}
	}

	public function user_detail()
	{
		try {
			$info = self::status()
						->id()
						->with('UserDetailInfo,UserAfterPregnancyInfo,UserPregnancyInfo,UserReadyPregnancyInfo')
						->find();

			if (!$info) win_exception('', __LINE__);

			return return_true($info->hidden(['password'])->toArray());
		} catch (WinException $e) {
			return $e->false();
		}
	}

	public function change_status()
	{
		try {
			if (!self::save(['status' => input('get.status')], ['id' => input('get.id')])) win_exception('操作失败', __LINE__);
			
			return return_no_data();
		} catch (WinException $e) {
			return $e->false();
		}
	}
}