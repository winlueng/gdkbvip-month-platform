<?php
namespace app\admin\model;

use think\Model;
use winleung\exception\WinException;

class DoctorComment extends Common
{
	// 根据机构id获取
	public function scopeOid($sql) 
	{
		$sql->where('doctor_id', input('get.doctor_id'));
	}

	public function User()
	{
		return $this->hasMany('User', 'id', 'user_id')
					->field('id, nick_name, head_url');
	}

	public function comment_list_by_doctor()
	{
		try {
			$list = self::oid()
						->where('status', '<>', '-1')
						->with('User')
						->order('create_time desc')
						->paginate(10)
						->hidden(['status'])
						->toArray();

			if(!$list) win_exception('', __LINE__);

			$data_total = self::oid()
									  ->where('status', '<>', '-1')
									  ->count();
 
			return return_true($list, '', $data_total);
		} catch (WinException $e) {
			return $e->false();
		}
	}

	public function change_status()
	{
		try {
			$info = self::id()
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