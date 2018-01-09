<?php
namespace app\user\model;

use think\Model;
use winleung\exception\WinException;

class Classify extends Common
{
	private $sort;

	public function initialize()
	{
		parent::initialize();
		$this->sort = $this->redis->get("user_classify_sort_". $this->user_info['id']);
	}

	public function scopeSort_where($sql)
	{
		if ($this->sort) {
			$sql->where('id', 'in', $this->sort)->order('field(`id`, '. $this->sort .')');
		}else{
			$sql->order('create_time');
		}
	}

	public function scopeChild($sql)
	{
		$sql->where('pid', '<>', '0');
	}

	public function header_classify_list()
	{
		try {
			$list = self::sort_where()
						->child()
						->where('status', '0')
						->limit(8)
						->select();

			if (!$list) {
				win_exception('', __LINE__);
			}
			
			return return_true($list);
		} catch (WinException $e) {
			return $e->false();
		}
	}

	public function classify_list()
	{
		try {
			$list = self::where('status', '0')
						->where('pid', 0)
						->order('create_time desc')
						->select()
						->hidden(['status', 'create_time'])
						->toArray();

			if (!$list) win_exception('未查询到任何数据', __LINE__);
			// halt($list);
			foreach ($list as $v) {
				$arr = self::where('status','0')
							->where('pid', $v['id'])
							->order('create_time desc')
							->select()
							->hidden(['status', 'create_time'])
							->toArray();
				if ($arr) {
					$v['child_list'] = $arr;
				}else{
					$v['child_list'] = [];
				}
				$result[] = $v;
			}

			return return_true($result);
		} catch (WinException $e) {
			return $e->false();
		}
	}

	public function classify_set_sort()
	{
		try {
			$data = input('post.');

			if (!is_array($data['id_list'])){
				win_exception('分类排序id列只能为数组形式', __LINE__);
			}
			elseif (count($data['id_list']) > 7){
				win_exception('排序分类个数最大数量为7', __LINE__);
			}

			if (!$this->redis->set("user_classify_sort_". $this->user_info['id'], implode(',', $data['id_list']))) {
				win_exception('记录排序失败', __LINE__);
			}

			return return_no_data();
		} catch (WinException $e) {
			return $e->false();
		}
	}
}
