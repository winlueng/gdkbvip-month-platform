<?php
namespace app\admin\model;

use think\Model;
use winleung\exception\WinException;

class Organization extends Common
{
	public function OrganizationDetail()
	{
		return $this->hasMany('OrganizationDetail', 'organization_id');
	}

	public function getStartTimeAttr($value)
	{
		return date('Y-m-d H:i:s', $value);
	}

	public function Business()
	{
		return $this->hasMany('Business', 'id', 'business_id');
	}

	public function getEndTimeAttr($value)
	{
		return date('Y-m-d H:i:s', $value);
	}

	/*public function setEndTimeAttr($value)
	{
		return strtotime($value);
	}

	public function setStartTimeAttr($value)
	{
		return strtotime($value);
	}

	public function setMakeAContractTimeAttr($value)
	{
		return strtotime($value);
	}*/

	public function scopeSearch_business($sql)
	{
		if (input('get.business_id')) {
			$sql->where('business_id', input('get.business_id'));
		}
	}

	public function scopeSearch_name($sql)
	{
		if (input('get.name')) {
			$sql->where('organization_name', 'like', '%'.input('get.name').'%');
		}
	}

	public function scopeStatus($sql)
	{
		$sql->where('status', '<>', '-1');
	}

	public function organization_add()
	{
		try {
			$basic_validate = validate('Organization');
			$detail_validate = validate('OrganizationDetail');
			$data = input('post.');

			if (!$basic_validate->scene('add')->check($data)) win_exception($basic_validate->getError(), __LINE__);
			if (!$detail_validate->scene('add')->check($data)) win_exception($detail_validate->getError(), __LINE__);
			if ($data['organization_ip']) {
				if (self::get($data['organization_ip'])) win_exception('ip号已存在', __LINE__);
			}
			self::startTrans();
			if (!self::allowField(true)->save($data)) win_exception('新增数据失败', __LINE__);

			$data['organization_id'] = $this->id;

			if (!model('OrganizationDetail')->allowField(true)->save($data)) win_exception('新增数据失败', __LINE__);

			self::commit();
			return return_no_data();
		} catch (WinException $e) {
			self::rollback();
			return $e->false();
		}
	}

	public function visitStatis()
    {
        return $this->hasMany('OrganizationBehavior', 'organization_id', 'id')
                    ->where('visit_total', '>','0');
    }

	public function organization_list()
	{
		try {
			$list = self::status()
						->search_name()
						->search_business()
						->with('OrganizationDetail,Business')
                        ->withCount('visitStatis')
						->order('create_time')
						->paginate(10)
						->hidden(['create_time'])
						->toArray();
						// halt($list);
			if (!$list) win_exception('', __LINE__);

			$data_total = self::status()
						->search_business()
						->search_name()
						->count();

			return return_true($list, '', $data_total);
		} catch (WinException $e) {
			return $e->false();
		}
	}

	public function organization_update()
	{
		try {
			$basic_validate = validate('Organization');
			$detail_validate = validate('OrganizationDetail');
			$data = input('post.');

			if (!$basic_validate->scene('update')->check($data)) win_exception($basic_validate->getError(), __LINE__);
			if (!$detail_validate->scene('update')->check($data)) win_exception($detail_validate->getError(), __LINE__);

			self::startTrans();
			if (!self::allowField(/*['postfix', 'update_time', 'end_time']*/true)->save($data, ['id' => input('get.id')])) win_exception('修改数据失败', __LINE__);

			if (!model('OrganizationDetail')->allowField(['update_time', 'organization_logo', 'organization_pic'])->save($data, ['organization_id' => input('get.id')])) win_exception('修改数据失败', __LINE__);

			self::commit();
			return return_no_data();
		} catch (WinException $e) {
			self::rollback();
			return $e->false();
		}
	}

	public function organization_info()
	{
		try {
			$info = self::status()
						->with('OrganizationDetail')
						->where('id', input('get.id'))
						->find();

			if (!$info) win_exception('', __LINE__);

			return return_true($info);
		} catch (WinException $e) {
			return $e->false();
		}
	}

	public function organization_status()
	{
		try {
			if (!self::save(['status' => input('get.status')], ['id' => input('get.id')])) win_exception('操作失败', __LINE__);

			return return_no_data();
		} catch (WinException $e) {
			return $e->false();
		}
	}
}