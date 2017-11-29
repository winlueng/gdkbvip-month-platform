<?php
namespace app\user\model;

use think\Model;
use winleung\exception\WinException;

class UserSymptomatography extends Common
{
	public function setUserIdAttr()
	{
		return $this->user_info['id'];
	}

	public function setSymptomImgAttr($value)
	{
		return json_encode($value);
	}

	public function getSymptomImgAttr($value)
	{
		return json_decode($value, true);
	}

	public function User()
	{
		return $this->hasMany('User', 'id', 'user_id')
					->field('id, nick_name, head_url');
	}

	public function question_record($title = '')
	{
		try {
			$validate = validate('UserSymptomatography');

			$data = input('post.');

			if (!$validate->scene('create')->check($data)) win_exception($validate->getError(), __LINE__);

			if (count($data['symptom_img']) > 5) win_exception('上传图片最多为5张', __LINE__);

			if (!self::allowField(true)->save(input('post.'))) win_exception('记录症状记录失败,请重试!', __LINE__);

			return return_no_data();
		} catch (WinException $e) {
			return $e->false();
		}
	}

	public function get_user_last_record()
	{
		try {
			$info = self::where('user_id', input('get.user_id'))
						->where('departments_id', $this->user_info['departments_id'])
						->with('User')
						->order('update_time desc')
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