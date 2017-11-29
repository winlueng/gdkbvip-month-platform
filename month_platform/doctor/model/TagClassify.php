<?php
namespace app\doctor\model;

use think\Model;
use winleung\exception\WinException;

class TagClassify extends Common
{
	public function Tag()
	{
		return $this->hasMany('Tag', 'classify_id', 'id')
					->where('status', 1)
					->field('id, tag_name, classify_id');
	}

	public function scopeId($sql)
	{
		$sql->where('id', input('get.id'));
	}

	public function scopeStatus($sql)
	{
		$sql->where('status', '1');
	}

	public function classify_list()
	{
		$list = self::status()
					->with('Tag')
					->select()
					->hidden(['status'])
					->toArray();

		if($list) return return_true($list);

		return return_false(__LINE__);
	}
}