<?php
namespace app\user\model;

use think\Model;
use winleung\exception\WinException;

class OrganizationService extends Common
{
	public function scopeOid($sql)
	{
		$sql->where('organization_id', input('get.organization_id'));
	}

	public function getDetailAttr($value)
	{
		return htmlspecialchars_decode(html_entity_decode($value));
	}

	public function Organization()
	{
		return $this->hasMany('Organization', 'id', 'organization_id')
					->field('id,address_info,organization_name');
	}

	public function OrganizationDetail()
	{
		return $this->hasMany('OrganizationDetail', 'organization_id', 'organization_id')
					->field('organization_id, principal, principal_phone');
	}

	public function service_list()
	{
		try {
			$list = self::status()
						->oid()
						->order('update_time desc')
						->limit(4)
						->select()
						->hidden(['service_pic', 'status', 'organization_id'])
						->toArray();

			if (!$list) win_exception('', __LINE__);
			
			return return_true($list);
		} catch (WinException $e) {
			return $e->false();
		}
	}

	public function getServicePicAttr($value)
	{
		return json_decode($value, true);
	}

	public function service_detail()
	{
		try {
			$info = self::id()
						->status()
						->with('Organization,OrganizationDetail')
						->find();

			if (!$info) win_exception('', __LINE__);
			
			return return_true($info->hidden(['status', 'logo'])->toArray());
		} catch (WinException $e) {
			return $e->false();
		}
	}
}