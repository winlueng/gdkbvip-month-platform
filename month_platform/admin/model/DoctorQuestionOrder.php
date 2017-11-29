<?php
namespace app\admin\model;

use think\Model;
use winleung\exception\WinException;

class DoctorQuestionOrder extends Common
{
	public function User()
	{
		return $this->hasMany('User', 'id', 'user_id')->field('id, nick_name, real_name, sex');
	}

	public function UserSymptomatography()
	{
		return $this->hasMany('UserSymptomatography', 'order_id', 'id');
	}

	public function order_list()
	{
		try {
			$list = self::where('doctor_id', input('get.id'))
						->where('is_time_over', 'in', '1,2')
						->with('User,UserSymptomatography')
						->order('create_time desc')
						->paginate(10)
						->hidden(['order_no'])
						->toArray();

			if (!$list) win_exception('', __LINE__);

			$data_total = self::id()
									  ->where('is_time_over', 'in', '1,2')
									  ->count();

			return return_true($list, '', $data_total);
		} catch (WinException $e) {
			return $e->false();
		}
	}
}
?>