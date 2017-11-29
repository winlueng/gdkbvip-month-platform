<?php
namespace app\admin\model;

use think\Model;
use winleung\exception\WinException;

class GroupAccess extends Common
{
	public function access_update()
	{
		try {
			
			if (!input('get.group_id') || !input('get.admin_id')) throw new WinException("请提交权限组id(group_id)和管理员id(admin_id)", 13);
			
			if (!self::save(['group_id' => input('get.group_id')], ['admin_id' => input('get.admin_id')])) throw new WinException("修改失败", 15);

			return return_no_data();
		} catch (WinException $e) {
			return $e->false();
		}
		
	}
}