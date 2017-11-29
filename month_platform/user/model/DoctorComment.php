<?php
namespace app\user\model;

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
			$list = self::status()
						->oid()
						->with('User')
						->order('create_time desc')
						->paginate(10)
						->hidden(['update_time'])
						->toArray();

			if(!$list) win_exception('', __LINE__);

			$res['comment'] = $list;

			$res['count'] = self::status()
								->oid()
								->count();
 
			return return_true($res);
		} catch (WinException $e) {
			return $e->false();
		}
	}

	public function comment_add()
	{
		try {
			$validate = validate('DoctorComment');

			if (!$validate->scene('create')->check(input('post.'))) {
				win_exception($validate->getError(), __LINE__);
			}

			if (!self::allowField(true)->save(input('post.'))) {
				win_exception('评论失败', __LINE__);
			}

			model('DoctorBehavior')->note_down_user_doctor_behavior('comment', input('post.doctor_id'));

			return return_no_data();
		} catch (WinException $e) {
			return $e->false();
		}
	}
}