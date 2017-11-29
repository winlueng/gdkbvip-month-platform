<?php
namespace app\admin\model;

use think\Model;
use winleung\exception\WinException;

class OrganizationDetail extends Common
{
	public function setOrganizationPicAttr($value)
	{
		return json_encode($value);
	}

	public function getOrganizationPicAttr($value)
	{
		return json_decode($value, true);
	}

	public function getDescriptionAttr($value)
	{
		return htmlspecialchars_decode(html_entity_decode($value));
	}

	public function description_info()
	{
		try {
			$info = self::where('organization_id', input('get.id'))
						->find();

			return return_true($info);
		} catch (WinException $e) {
			return $e->false();
		}
	}

	public function description_update()
	{
		try {
			if (!self::save(['description' => input('post.description'), 'synopsis' => input('post.synopsis')], ['organization_id' => input('get.id')])) win_exception('修改失败', __LINE__);

			return return_no_data();
		} catch (WinException $e) {
			return $e->false();
		}
	}
}