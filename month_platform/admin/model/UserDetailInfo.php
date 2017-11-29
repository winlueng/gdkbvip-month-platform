<?php
namespace app\admin\model;

use think\Model;

class UserDetailInfo extends Common
{
	public function getLastLoginTimeAttr($value)
	{
		return date('Y-m-d H:i:s', $value);
	}
}