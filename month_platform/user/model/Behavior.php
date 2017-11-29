<?php
namespace app\user\model;

use think\Model;
use winleung\exception\WinException;

/**
 * 行为分析
 */
class Behavior extends Common
{
	public function scopeClassify($sql)
	{
		$sql->where('classify_id', input('get.classify_id'));
	}

	public function get_tag()
	{
		try {
			$list = self::classify()
						->status()
						->paginate(10)
						->hidden(['status'])
						->toArray();
			if (!$list) win_exception('', __LINE__);

			return return_true($list);
		} catch (WinException $e) {
			return $e->false();
		}
	}

	public function get_pearson($x, $y)
	{
		try {
			return corrcoef($x,$y);

			return $info;
		} catch (WinException $e) {
			return $e->false();
		}
	}
}