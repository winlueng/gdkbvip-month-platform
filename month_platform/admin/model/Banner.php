<?php
namespace app\admin\model;

use think\Model;
use winleung\exception\WinException;

class Banner extends Common
{
	public function Classify()
	{
		return $this->belongsTo('Classify');
	}

	public function BannerStatis()
	{
		return $this->hasMany('BannerStatis', 'relevance_id');
	}

	public function banner_add()
	{
		try {
			$validate = validate('Banner');

			if(!$validate->scene('add')->check(input('post.'))) win_exception($validate->getError(), __LINE__);

			if(!self::allowField(true)->save(input('post.'))) win_exception('新增数据失败', __LINE__);

			return return_no_data();
		} catch (WinException $e) {
			return $e->false();
		}
	}

	public function banner_list()
	{
		try {
			$list = self::status()
						->classify_id()
						->order('create_time desc')
						->paginate(10)
						->visible(['id', 'title', 'classify_id', 'tag_list', 'status', 'banner_statis_count'])
						->toArray();

			if(!$list) win_exception('', __LINE__);

			foreach ($list as $v) {
				$sum = model('BannerStatis')->where('relevance_id', $v['id'])->sum('click_total');
				$v['statis_sum'] = $sum?$sum:0;
				$v['tag_info_list'] = model('Tag')->where('id', 'in', implode(',', $v['tag_list']))->select()->toArray();
				$result[] = $v;
			}

			$data_total = self::status()
									  ->classify_id()
									  ->count();

			return return_true($result, '', $data_total);
		} catch (WinException $e) {
			return $e->false();
		}
	}

	public function banner_info()
	{
		try {
			$list = self::status()
						->id()
						->find();

			if(!$list) win_exception('', __LINE__);

			return return_true($list);
		} catch (WinException $e) {
			return $e->false();
		}
	}

	public function banner_update()
	{
		try {
			$validate = validate('Banner');

			if(!$validate->scene('add')->check(input('post.'))) win_exception($validate->getError(), __LINE__);

			if(!self::allowField(true)->save(input('post.'), ['id' => input('get.id')])) win_exception('新增数据失败', __LINE__);

			return return_no_data();
		} catch (WinException $e) {
			return $e->false();
		}
	}

	public function banner_status()
	{
		try {
			if (!self::save(['status' => input('get.status')], ['id' => input('get.id')])) win_exception('操作失败', __LINE__);

			return return_no_data();
		} catch (WinException $e) {
			return $e->false();
		}
	}
}