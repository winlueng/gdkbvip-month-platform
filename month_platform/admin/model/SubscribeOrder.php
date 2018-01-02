<?php
namespace app\admin\model;

use think\Model;
use winleung\exception\WinException;


class SubscribeOrder extends Common
{
	public function scopeTime($sql)
	{
		if (input('get.start_time') && input('get.end_time')) {
			$sql->where('create_time', '>', input('get.start_time'))
				->where('create_time', '<', input('get.end_time'));
		}
	}

	public function scopeOrder_status($sql)
	{
		if (input('get.status') && input('get.status') >= 0) {
			$sql->where('status', input('get.status'));
		}else{
			$sql->where('status', '<>', '-1');
		}
	}

	public function order_list()
	{
		try {
			$list = self::time()
						->order_status()
						->with('Organization,User')
						->paginate(10)
						->hidden(['update_time'])
						->toArray();

			if (!$list) win_exception('', __LINE__);

			$data_total = self::time()
									  ->order_status()
									  ->where('user_status', '<>', '-1')
									  ->count();

			return return_true($list, '', $data_total);
		} catch (WinException $e) {
			return $e->false();
		}
	}

	public function getComeTimeAttr($value)
	{
		return date('Y-m-d H:i:s', $value);
	}

	public function Organization()
	{
		return $this->hasMany('Organization', 'id', 'organization_id');
	}

	public function User()
	{
		return $this->hasMany('User', 'id', 'user_id');
	}

	public function OrganizationDetail()
	{
		return $this->hasMany('OrganizationDetail', 'organization_id', 'organization_id');
	}

	public function OrganizationService()
	{
		return $this->hasMany('OrganizationService', 'id', 'service_id')->field('id,service_name,logo,price,discount_price,status');
	}

	public function order_detail()
	{
		try {
			$info = self::id()
						->where('status', '<>', '-1')
						->with('Organization,User,OrganizationDetail,OrganizationService')
						->find();
			if (!$info) win_exception('', __LINE__);

			return return_true($info);
		} catch (WinException $e) {
			return $e->false();
		}
	}

	public function change_status()
	{
		try {
			$info = self::id()
						->where('status', '<>', '-1')
						->find();

			if (!$info) {
				win_exception('', __LINE__);
			}

			$status = input('get.status');
			switch ($info->status) {
				case '0':
					if (!in_array($status, ['1','5'])) {
						win_exception('当前状态只可对订单进行接受或拒绝操作', __LINE__);
					}
					break;
				case '1':
					if (!in_array($status, ['2'])) {
						win_exception('当前状态只可对订单进行完成订单操作', __LINE__);
					}
					break;
				case '6':
					win_exception('订单已过期', __LINE__);
					break;
				default:
					win_exception('当前订单不适宜做任何操作', __LINE__);
					break;
			}

			$info->status = $status;

			if (!$info->save()) win_exception('操作失败', __LINE__);

			if ($status == '1' || $status == '2') {
				$organization = model('Organization')->get($info->organization_id);
				// $time = date('Y年m月d日H:i', $time);
				$news = [
					'title' => "您已经成功预约了{$organization->organization_name},我们将会有客服联系您,请留意您的电话",
					'order_id'	=> $info->id,
					'receiver_id' => $info->user_id
				];

				if ($status == '2') $news['title'] = "您已经完成了预约{$organization->organization_name}的服务,是否对店铺的服务满意呢";

				model('Announcement')->create_system_news($news);
			}
			
			return return_no_data();
		} catch (WinException $e) {
			return $e->false();
		}
	}

	public function reset_come_time()
	{
		try {
			$info = self::id()
						->where('status', '<>', '-1')
						->find();

			if (!$info) {
				win_exception('', __LINE__);
			}

			if ($info->is_reset_come_time == '1') {
				win_exception('此订单已重置过预约时间,不可再操作', __LINE__);
			} elseif($info->is_reset_come_time > time()){
				win_exception('此订单预约时间已超时, 不可再操作.', __LINE__);
			}

			$info->is_reset_come_time == '1';

			$time = strtotime(input('get.come_time'));

			if ($time < time()) win_exception('重置时间不可小于当前时间', __LINE__);

			$info->come_time = $time;

			if (!$info->save()) win_exception('重置失败',__LINE__);

			$organization = model('Organization')->get($info->organization_id);
			$time = date('Y年m月d日H:i', $time);
			$news = [
				'title' => "您预约{$organization->organization_name}的时间更改为{$time},请留意您的时间",
				'order_id'	=> $info->id,
				'receiver_id' => $info->user_id
			];
			model('Announcement')->create_system_news($news);

			return return_no_data();
		} catch (WinException $e) {
			return $e->false();
		}
	}
}