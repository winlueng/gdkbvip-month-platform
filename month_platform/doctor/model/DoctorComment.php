<?php
namespace app\doctor\model;

use think\Model;
use winleung\exception\WinException;

class DoctorComment extends Common
{

	public function User()
	{
		return $this->hasMany('User', 'id', 'user_id')
					->field('id, nick_name, head_url');
	}

	public function comment_list_by_doctor()
	{
		try {
			$list = self::where('status', '<>', '-1')
						->where('doctor_id', $this->user_info['id'])
						->with('User')
						->order('create_time desc')
						->paginate(10)
						->hidden(['update_time'])
						->toArray();

			if(!$list) win_exception('', __LINE__);
 
			return return_true($list);
		} catch (WinException $e) {
			return $e->false();
		}
	}

	public function change_status()
	{
		try {
			$info = self::id()
						->where('doctor_id', $this->user_info['id'])
						->find();
			if (!$info) win_exception('', __LINE__);

			$info->status = input('get.status');

			if (!$info->save()) win_exception('操作失败', __LINE__);
			
			return return_no_data();
		} catch (WinException $e) {
			return $e->false();
		}
	}
}