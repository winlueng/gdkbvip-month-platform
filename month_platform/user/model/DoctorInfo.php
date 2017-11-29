<?php
namespace app\user\model;

use think\Model;
use winleung\exception\WinException;

class DoctorInfo extends Common
{
	public function Article()
	{
		return $this->hasMany('Article', 'doctor_id');
	}

	public function DoctorQuestionOrder()
	{
		return $this->hasMany('DoctorQuestionOrder', 'doctor_id', 'id')->where('is_time_over', '<>', '0');
	}

	public function doctor_info()
	{
		try {
			$info = self::status()
						->id()
						->withCount(['Article'=>function($sql){
						    $sql->where('status','1');
						}, 'DoctorQuestionOrder'])->find();

			if (!$info) win_exception('', __LINE__);

			return return_true($info->hidden(['password'])->toArray());
		} catch (WinException $e) {
			return $e->false();
		}
	}

	public function doctor_list_by_departments()
	{
		try {
			$list = self::status()
						->where('departments_id', input('get.departments_id'))
						->withCount('DoctorQuestionOrder')
						->paginate(10)
						->hidden(['status'])
						->toArray();
						
			if (!$list) win_exception('', __LINE__);

			return return_true($list);
		} catch (WinException $e) {
			return $e->false();
		}
	}
}