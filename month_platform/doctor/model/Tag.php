<?php
namespace app\doctor\model;

use think\Model;
use winleung\exception\WinException;

class Tag extends Common
{
	public function TagClassify()
	{
		return $this->belongsTo('TagClassify');
	}

	public function scopeStatus($sql)
	{
		$sql->where('status', 1);
	}

	public function tag_list()
	{
		try {
			$list = self::status()
						->where('id', 'in', input('get.id_list'))
						->select()
						->hidden(['status', 'create_time'])
						->toArray();
			if(!$list) win_exception('', __LINE__);

			return return_true($list);
		} catch (WinException $e) {
			return $e->false();
		}
	}
}