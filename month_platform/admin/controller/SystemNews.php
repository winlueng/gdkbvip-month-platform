<?php
namespace app\admin\controller;

use think\Controller;
use winleung\exception\WinException;

class SystemNews extends Common
{
	public function _initialize()
	{
		parent::_initialize();
		$this->obj = model('SystemNews');
	}

	public function getList()
	{
		return $this->obj->news_list();
	}

	public function getDetail()
	{
		return $this->obj->news_detail();
	}

	public function getDel()
	{
		$del_list = $this->redis->get('admin_system_news_del_list_'. $this->user_info['id']);
		if ($del_list) {
			$del_list = json_decode($del_list, true);
			$list = explode(',', input('get.id_list'));
			$del_list += $list;
			$del_list = array_unique($del_list);
			$this->redis->set('admin_system_news_del_list_'. $this->user_info['id'], json_encode($del_list));
		}else{
			$this->redis->set('admin_system_news_del_list_'. $this->user_info['id'], json_encode(explode(',', input('get.id_list'))));
		}
		return return_no_data();
	}

	public function getRead()
	{
		$read_list = $this->redis->get('admin_system_news_read_list_'. $this->user_info['id']);
		if ($read_list) {
			$read_list = json_decode($read_list, true);
			$list = explode(',', input('get.id_list'));
			$read_list += $list;
			$read_list = array_unique($read_list);
			$this->redis->set('admin_system_news_read_list_'. $this->user_info['id'], json_encode($read_list));
		}else{
			$this->redis->set('admin_system_news_read_list_'. $this->user_info['id'], json_encode(explode(',', input('get.id_list'))));
		}
		return return_no_data();
	}
}