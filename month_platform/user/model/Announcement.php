<?php
namespace app\user\model;

use think\Model;
use winleung\exception\WinException;
use think\Db;

class Announcement extends Common
{
	public function User()
	{
		return $this->hasMany('User', 'id', 'receiver_id')->field('id, nick_name');
	}

	public function DoctorInfo()
	{
		return $this->hasMany('DoctorInfo', 'id', 'receiver_id')->field('id, doctor_name');
	}

	public function announcement_list()
	{
		try {
			$list = self::where('status', '<>', '-1')
						->where('user_status', '<>', '-1')
						->where('receiver_type', '1')
						->where('receiver_id', $this->user_info['id'])
						->order('create_time desc')
						->paginate(8)
						->hidden(['status'])
						->toArray();

			if (!$list) win_exception('', __LINE__);

			return return_true($list);
		} catch (WinException $e) {
			return $e->false();
		}
	}

	public function change_status()
	{
		try {
			if (!self::save(['user_status' => input('get.status')], ['id' => input('get.id')])) win_exception('操作失败', __LINE__);
			
			return return_no_data();
		} catch (WinException $e) {
			return $e->false();
		}
	}
}