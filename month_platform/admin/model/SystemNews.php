<?php
namespace app\admin\model;

use think\Model;
use winleung\exception\WinException;

class SystemNews extends Common
{
	private $del_list;

	public function scopeType($sql)
	{
		if (input('get.type')) {
			$sql->where('news_type', input('get.type'));
		}
	}

	public function initialize()
	{
		parent::initialize();
		$this->del_list = $this->redis->get('admin_system_news_del_list_'. $this->user_info['id']);
		if ($this->del_list) {
			$this->del_list = json_decode($this->del_list, true);
		}
	}

	public function scopeNo_del($sql)
	{
		if ($this->del_list) {
			$sql->where('id', 'not in', implode(',', $this->del_list));
		}
	}

	public function news_list()
	{
		try {
			$list = self::type()
						->no_del()
						->order('create_time desc')
						->paginate(10)
						->hidden(['update_time'])
						->toArray();

			if (!$list) win_exception('', __LINE__);
			
			$is_read_list = $this->redis->get('admin_system_news_read_list_11'/*. $this->user_info['id']*/);
            if ($is_read_list) $is_read_list = json_decode($is_read_list, true);

            halt($is_read_list);
            if ($is_read_list){
				foreach ($list as $v) {
					if (in_array($v['id'], $is_read_list)) {
						$v['is_read'] = '1';
						$result[] = $v;
					}else{
						$v['is_read'] = '0';
						$result[] = $v;
					}
				}
			}else{
				foreach ($list as $v) {
					$v['is_read'] = '0';
					$result[] = $v;
				}
			}

			$data_total = self::type()
									  ->no_del()
									  ->count();
									  
			return return_true($result, '', $data_total);
		} catch (WinException $e) {
			return $e->false();
		}
	}

	public function news_detail()
	{
		try {
			$info = self::id()
						->no_del()
						->find();

			if (!$info) win_exception('', __LINE__);

			return return_true($info);
		} catch (WinException $e) {
			return $e->false();
		}
	}
}