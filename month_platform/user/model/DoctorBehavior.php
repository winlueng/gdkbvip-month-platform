<?php
namespace app\user\model;

use think\Model;
use winleung\exception\WinException;

/**
 * 用户对医生行为分析
 */
class DoctorBehavior extends Common
{
	// 记录用户对医生的行为数据
	public function note_down_user_doctor_behavior($behavior = '', $oid = 0)
	{
		try {
			if (input('get.behavior')) $behavior = input('get.behavior');

			if (input('get.doctor_id')) $oid = input('get.doctor_id');

			$behavior_info = self::get(['user_id' => $this->user_info['id'], 'doctor_id' => $oid]);

			if (!$behavior_info) {
				self::save(['user_id' => $this->user_info['id'], 'doctor_id' => input('get.doctor_id')]);
				$behavior_info = self::get(['user_id' => $this->user_info['id'], 'doctor_id' => input('get.doctor_id')]);
			}

			switch ($behavior) {
				case 'save':
					$behavior_info->is_save = 1;
					break;
				case 'share':
					$behavior_info->share_total += 1;
					break;
				case 'visit':
					$behavior_info->visit_total += 1;
					$behavior_info->visit_second += input('get.visit_second');
					if(!model('ArticleStatis')->statis_save()) win_exception('记录浏览量失败', __LINE__);
					break;
				case 'comment':
					$behavior_info->comment_total += 1;
					break;
				case 'quiz':
					$behavior_info->quiz_total += 1;
					break;
			}

			if (!$behavior_info->save()) win_exception('更新行为失败', __LINE__);

			return return_no_data();
		} catch (WinException $e) {
			return $e->false();
		}
	}

	public function DoctorInfo()
	{
		return $this->hasMany('DoctorInfo', 'id', 'doctor_id')
					->field('id, doctor_name, departments_id, organization_name, doctor_logo');
	}

	public function get_my_save()
	{
		try {
			$list = self::status()
						->user_id()
						->with('DoctorInfo')
						->where('is_save', '1')
						->paginate(10)
						->hidden(['status'])
						->toArray();

			if(!$list) win_exception('', __LINE__);

			return return_true($list);
		} catch (WinException $e) {
			return $e->false();
		}
	}

	public function change_no_save()
	{
		try {
			if (!self::save(['is_save' => '0'], function($sql){
				$sql->where('doctor_id', 'in', input('get.doctor_id'));
			})) {
				win_exception('操作失败', __LINE__);
			}

			return return_no_data();
		} catch (WinException $e) {
			return $e->false();
		}
	}
}