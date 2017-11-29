<?php
namespace app\admin\model;

use think\Model;
use winleung\exception\WinException;

class RuleGroup extends Common
{
	public function getRuleListAttr($value)
	{
		return json_decode($value, true);
	}

	public function setRuleListAttr($value)
	{
		return json_encode($value);
	}

	public function scopeStatus($sql)
	{
		$sql->where('status', 1);
	}

	public function group_add()
	{
		$validate = validate('RuleGroup');
		try {
			if (!$validate->scene('add')->check(input('post.'))) throw new WinException($validate->getError(), 28);
			
			if (!self::save(input('post.'))) throw new WinException("新增数据失败", 30);
			
			return return_no_data();
		} catch (WinException $e) {
			return $e->false();
		}
	}

	public function group_list()
	{
		try {
			$list = self::status()
						->order('update_time desc')
						->select()
						->toArray();

			if (!$list) throw new WinException("未查询到任何数据", 45);

			foreach ($list as $v) {
				if ($v['rule_list']) {
					$rule = model('Rule')->rule_in_list(implode(',', $v['rule_list']))->dispose_list();
					if ($rule['err_code'] == 0) {
						$v['rule'] = $rule['data'];
					}
				}
				$res[] = $v;
			}
			
			return return_true($res);
		} catch (WinException $e) {
			return $e->false();
		}
	}

	public function group_update()
	{
		$validate = validate('RuleGroup');
		try {
			if (!$validate->scene('add')->check(input('post.'))) throw new WinException($validate->getError(), 63);
			
			if (!self::save(input('post.'), ['id' => input('get.id')])) throw new WinException("修改数据失败", 65);
			
			return return_no_data();
		} catch (WinException $e) {
			return $e->false();
		}
	}

	public function group_del()
	{
		try {
			if (!self::save(['status' => '-1'], ['id' => input('get.id')])) throw new WinException("删除数据失败", 75);
			
			return return_no_data();
		} catch (WinException $e) {
			return $e->false();
		}
	}
}