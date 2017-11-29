<?php
namespace app\user\model;

use think\Model;
use winleung\exception\WinException;

class SubscribeOrder extends Common
{
	public function Organization()
	{
		return $this->hasMany('Organization', 'id', 'organization_id')
					->field('id, organization_name');
	}

	public function OrganizationDetail()
	{
		return $this->hasMany('OrganizationDetail', 'organization_id', 'organization_id')
					->field('organization_id, organization_logo');
	}

	public function setComeTimeAttr($value)
	{
		return strtotime($value);
	}

	public function getComeTimeAttr($value)
	{
		return date('Y-m-d H:i:s', $value);
	}

	public function order_add()
	{
		try {
			$validate = validate('SubscribeOrder');

			$data = input('post.');

			if (!$validate->scene('add')->check($data)) {
				win_exception($validate->getError(), __LINE__);
			}

			if ($info = self::where('phone', $data['phone'])->where('organization_id', $data['organization_id'])->where('status', 'not in','-1,3,4')->find()){
				switch ($info->status) {
					case '0':
						win_exception('您好,您有一张该店的订单正在等待店家操作,为避免不必要的重单问题期间不能再次预约!', __LINE__);
						break;
					case '1':
						win_exception('您好, 店家已安排了您的预约,为避免不必要的重单问题期间不能再次预约!', __LINE__);
						break;
					case '2':
						win_exception('您好, 您已经完成了服务, 为提高更好的服务质量, 请评价后再进行预约操作.谢谢您的支持', __LINE__);
						break;
				}
			}

			$data['order_no'] = 'USER_'. $this->user_info['id'] .'_OID_'. $data['organization_id'] . time(). mt_rand(111,999);
			
			$data['user_id'] = $this->user_info['id'];
			// halt($data);
			if (!self::allowField(true)->save($data)) {
				win_exception('预约失败', __LINE__);
			}

			$return_data = self::where('id', $this->id)
								->with('Organization,OrganizationDetail')
								->find();

			return return_true($return_data);
		} catch (WinException $e) {
			return $e->false();
		}
	}

	public function scopeStatus($sql)
	{
		if (input('get.status') != '0' && input('get.status')) {
			$sql->where('status', input('get.status'));
		}
	}

	public function scopeUid($sql)
	{
		$sql->where('user_id', $this->user_info['id']);
	}

	public function order_list()
	{
		try {
			$list = self::status()
						->uid()
						->where('user_status', '0')
						->with('Organization,OrganizationDetail')
						->paginate(10);

			if (!$list) win_exception('', __LINE__);

			return return_true($list->hidden(['update_time'])->toArray());
		} catch (WinException $e) {
			return $e->false();
		}
	}

	public function OrganizationService()
	{
		return $this->hasMany('OrganizationService', 'id', 'service_id')->field('id,service_name,logo,price,discount_price,status');
	}

	public function order_info()
	{
		try {
			$info = self::id()
						->uid()
						->where('user_status', '0')
						->with('Organization,OrganizationDetail,OrganizationService')
						->find();

			if(!$info) win_exception('', __LINE__);

			return return_true($info->toArray());
		} catch (WinException $e) {
			return $e->false();
		}
	}

	public function order_cancel()
	{
		try {
			if(!self::save(['status' => '4'], ['id' => input('get.id')])) win_exception('取消操作失败', __LINE__);

			return return_no_data();
		} catch (WinException $e) {
			return $e->false();
		}
	}

	public function order_del()
	{
		try {
			if(!self::save(['user_status' => '-1'], ['id' => input('get.id')])) win_exception('取消操作失败', __LINE__);

			return return_no_data();
		} catch (WinException $e) {
			return $e->false();
		}
	}
}