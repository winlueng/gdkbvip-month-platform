<?php
namespace app\admin\model;

use think\Model;
use winleung\exception\WinException;

class OrganizationService extends Common
{
	public function getServicePicAttr($value)
	{
		return json_decode($value, true);
	}

	public function setServicePicAttr($value)
	{
		return json_encode($value);
	}

	public function scopeStatus($sql)
	{
		$sql->where('status', '<>', '-1');
	}

	public function getDetailAttr($value)
	{
		return htmlspecialchars_decode(html_entity_decode($value));
	}

	public function service_add()
	{
		try {
			$validate = validate('OrganizationService');

			if (!$validate->scene('add')->check(input('post.'))) win_exception($validate->getError(), __LINE__);

			if (!self::allowField(true)->save(input('post.'))) win_exception('添加数据失败', __LINE__);

			return return_no_data();
		} catch (WinException $e) {
			return $e->false();
		}
	}

	public function service_list()
	{
		try {
			$list = self::status()
						->where('organization_id', input('get.organization_id'))
						->paginate(10)
						->hidden(['create_time'])
						->toArray();

			if (!$list) win_exception('', __LINE__);

			foreach ($list as $v) {
				$sum = model('OrganizationServiceStatis')->where('relevance_id', $v['id'])->sum('click_total');
				$v['statis'] = $sum?$sum:0;
				$res[] = $v;				
			}

			$data_total = self::status()
									 ->where('organization_id', input('get.organization_id'))
									 ->count();

			return return_true($res, '', $data_total);
		} catch (WinException $e) {
			return $e->false();
		}
	}

	public function service_info()
	{
		try {
			$info = self::status()
						->where('id', input('get.id'))
						->find();
			if (!$info) win_exception('', __LINE__);			

			return return_true($info);
		} catch (WinException $e) {
			return $e->false();
		}
	}

	public function service_update()
	{
		try {
			 $validate = validate('OrganizationService');

			if (!$validate->scene('update')->check(input('post.'))) win_exception($validate->getError(), __LINE__);

			if (!self::allowField(['service_name', 'logo', 'service_pic', 'detail', 'price', 'discount_price'])->save(input('post.'), ['id' => input('get.id')])) win_exception('修改数据失败', __LINE__);

			return return_no_data();
		} catch (WinException $e) {
			return $e->false();
		}
	}

	public function service_status()
	{
		try {
			if(!self::save(['status' => input('get.status')], ['id' => input('get.id')])) win_exception('操作失败', __LINE__);

			return return_no_data();
		} catch (WinException $e) {
			return $e->false();
		}
	}
}