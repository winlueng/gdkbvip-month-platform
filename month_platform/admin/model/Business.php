<?php
namespace app\admin\model;

use think\Model;
use winleung\exception\WinException;

class Business extends Common
{
	public function scopeLike($sql)
	{
		if (input('get.name')) {
			$sql->where('name', 'like', '%'.input('get.name').'%');
		}
	}

	public function Organization()
	{
		return $this->hasMany('Organization', 'business_id', 'id');
	}

	public function business_add()
	{
		try {
			$validate = validate('Business');

			if (!$validate->scene('add')->check(input('post.'))) win_exception($validate->getError(), __LINE__);

			if (!self::allowField(true)->save(input('post.'))) win_exception('新增数据失败', __LINE__);

			return return_no_data();
		} catch (WinException $e) {
			return $e->false();
		}
	}

	public function business_list()
	{
		try {
			
			$list = self::status()
						->like()
						->withCount('Organization')
						->order('create_time desc')
						->paginate(10)
						->visible(['name', 'description', 'id', 'organization_count', 'status'])
						->toArray();

			if(!$list) win_exception('', __LINE__);

			$data_total = self::status()
									  ->like()
									  ->count();
			return return_true($list, '', $data_total);
		} catch (WinException $e) {
			return $e->false();
		}
	}

	public function business_info()
	{
		try {
			$info = self::status()
						->id()
						->find();
			if(!$info) win_exception('', __LINE__);

			return return_true($info);
		} catch (WinException $e) {
			return $e->false();
		}
	}

	public function business_update()
	{
		try {
			$validate = validate('Business');

			if (!$validate->scene('add')->check(input('post.'))) win_exception($validate->getError(), __LINE__);

			if(!self::allowField(true)->save(input('post.'), ['id' => input('get.id')])) win_exception('修改数据失败', __LINE__);

			return return_no_data();
		} catch (WinException $e) {
			return $e->false();
		}
	}

	public function business_status()
	{
		try {
			if(!self::save(['status' => input('get.status')], ['id' => input('get.id')])) win_exception('操作失败', __LINE__);

			return return_no_data();
		} catch (WinException $e) {
			return $e->false();
		}
	}
}