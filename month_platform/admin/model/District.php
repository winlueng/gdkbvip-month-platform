<?php
namespace app\admin\model;

use think\Model;
use winleung\exception\WinException;

class District extends Common
{
	public function scopePid($sql)
	{
		$sql->where('parent_id', input('get.pid'));
	}

	public function district_list()
	{
		try {
			$list = self::pid()
						->select()
						->hidden(['initials'])
						->toArray();

			if (!$list) win_exception('', __LINE__);

			return return_true($list);
		} catch (WinException $e) {
			return $e->false();
		}
	}

	public function district_info()
	{
		try {
			$info = self::where('district_id', input('get.id'))
						->find();

			$result = self::return_info($info->parent_id, [$info->district_id]);
			sort($result);
			return return_true($result);
		} catch (WinException $e) {
			return $e->false();
		}
	}

	public function return_info($pid=0, $arr = [])
	{
		$info = self::where('district_id', $pid)
					->find();
		$arr[] = $info->district_id;
		if ($info->parent_id == 0) {
			return $arr;
		}else{
			return self::return_info($info->parent_id, $arr);
		}

	}
}