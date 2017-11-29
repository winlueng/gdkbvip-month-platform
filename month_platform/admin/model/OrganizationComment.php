<?php
namespace app\admin\model;

use think\Model;
use winleung\exception\WinException;

class OrganizationComment extends Common
{
	public function User()
	{
		return $this->hasMany('User', 'id', 'user_id')
					->field('id,nick_name,head_url');
	}

	public function comment_list()
	{
		try {
			$list = self::where('organization_id', input('get.id'))
						->where('status', '<>', '-1')
						->with('User')
						->order('create_time desc')
						->paginate(10)
						->hidden(['update_time'])
						->toArray();

			if (!$list) win_exception('', __LINE__);

			$data_total = self::where('organization_id', input('get.id'))
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
			if(!self::save(['status' => input('get.status')], ['id' => input('get.id')])) win_exception('操作失败', __LINE__);
			
			return return_no_data();
		} catch (WinException $e) {
			return $e->false();	
		}
	}
}