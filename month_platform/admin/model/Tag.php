<?php
namespace app\admin\model;

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

	public function tag_del()
	{
		try {
			self::startTrans();

			if (!self::save(['status' => '-1'], ['id' => input('get.id')])) win_exception('删除标签失败', __LINE__);

			self::commit();
			return return_no_data();
		} catch (WinException $e) {
			self::rollback();
			return $e->false();
		}
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