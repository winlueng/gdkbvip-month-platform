<?php
namespace app\admin\model;

use think\Model;
use think\Exception;
use winleung\exception\WinException;
use think\Request;

class Rule extends Common
{
	public $res;

	public function initialize($value='')
	{
		parent::initialize();
		$add_sort = self::max('sort');
		$this->insert = ['sort' => $add_sort+1];
	}

	public function scopeStatus($sql)
	{
		$sql->where('status', '1');
	}

	public function rule_list()
	{
		$this->res = self::status()
				->order('sort')
				->select()
				->hidden(['sort'])
				->toArray();

		return $this;
	}

	public function rule_in_list($id_list)
	{
		if (!$id_list) return false;
		$this->res = self::status()
						->where('id', 'in', $id_list)
						// ->order('sort')
						->select()
						->hidden(['sort'])
						->toArray();
		return $this;
	}

	public function dispose_list()
	{

		if (!$this->res) return return_false(48, '未有任何数据可处理');

		foreach ($this->res as $v) {
			if ($v['parent_id'] == 0) {
				$parent[] = $v;
			}else{
				$kid[$v['parent_id']][]    = $v;
			}
		}

		if (!isset($parent)) {
			return return_false(__LINE__, '未获取到任何数据');
		}
		if (isset($kid)) {
			foreach ($parent as $v) {
				if (isset($kid[$v['id']])) {
					$v['kid'] = $kid[$v['id']];
				}
				$result[] = $v;
			}
		}else{
			$result = $parent;
		}
		// halt($result);
		return return_true($result);
	}

	public function rule_add()
	{
		try {
			$this->startTrans();

			$validate = validate('Rule');

			if (!$validate->scene("add")->check(input('post.'))) throw new WinException($validate->getError(), 53);
			;

			$where = [
				'module' => input('post.module'),
				'controller' => input('post.controller'),
				'method' => input('post.method'),
				'status' => '1',
			];

			if ($parent = self::get(input('post.parent_id'))){
				if ($parent->status == '-1') win_exception('此父级id已删除,请确认', __LINE__);
			}

			if (self::get($where)) throw new WinException("同等模块和控制器下,方法名称已存在", 60);

			if (!self::save(input('post.'))) throw new WinException('添加失败', 63);

			$this->commit();
			return return_no_data();
		} catch (WinException $e) {
			$this->rollback();
			return $e->false();
		}
	}

	public function rule_update()
	{
		try {
			$this->startTrans();

			$validate = validate('Rule');

			if (!$validate->scene('add')->check(input('post.'))) throw new WinException($validate->getError(), 89);

			$where = [
				'title'	 => input('post.title'),
				'module' => input('post.module'),
				'controller' => input('post.controller'),
				'method' => input('post.method'),
				'status' => '1',
			];

			if (self::get($where)) throw new WinException("同等模块和控制器下,方法名称已存在", 97);

			if (!self::save(input('post.'), ['id' => input('get.id')])) throw new WinException('修改失败', 78);

			$this->commit();
			return return_no_data();
		} catch (WinException $e) {
			$this->rollback();
			return $e->false();
		}
	}

	public function sort_to_change()
	{
		try {
			$this->startTrans();

			$info = self::get(input('get.id'));

			if (!$info) exception('为查询到任何数据', 116);

			switch (input('get.control')) {
				case 'up':
					$sort = self::status()
								->where('parent_id', $info->parent_id)
								->where('sort', '<', $info->sort)
								->max('sort');
					break;
				case 'down':
					$sort = self::status()
								->where('parent_id', $info->parent_id)
								->where('sort', '>', $info->sort)
								->min('sort');
					break;
			}

			if (!$sort) exception('未查询到任何可以排序交换的数据', 133);

			if (!self::save(['sort' => $info->sort], ['sort' => $sort])) exception('排序修改失败', 135);

			$info->sort = $sort;

			if (!$info->save()) exception('排序修改失败', 139);

			$this->commit();
			return return_no_data();
		} catch (Exception $e) {
			$this->rollback();
			return return_false($e->getCode(), $e->getMessage());
		}
	}

	public function rule_del()
	{
		try {
			$info = self::get(input('get.id'));

			if (!$info) exception('为查询到任何数据', 154);

			$this->startTrans();

			if ($info->parent_id == '0' && self::all(['parent_id' => $info->id])->toArray()) {

				if(!self::save(['status' => '-1'],['parent_id' => $info->id])) win_exception('删除失败', 160);
			}

			$info->status = '-1';

			if (!$info->save()) win_exception('删除失败', 165);

			$this->commit();
			return return_no_data();
		} catch (WinException $e) {
			$this->rollback();
			return $e->false();
		}
	}

	public function check_admin_rule()
	{
		try {
			$group_id = model('GroupAccess')->get(['admin_id' => $this->user_info['id']])->group_id;

			if (!$group_id) win_exception('此用户未分配任何权限组,请联系超管', 218);
			$rule_list = model('RuleGroup')->get($group_id)->rule_list;

			if (!$rule_list) win_exception('权限组未添加任何权限,请联系超管', 221);

			$request = Request::instance();

			$rule = self::get([
					'module' => strtolower($request->module()),
					'controller' => strtolower($request->controller()),
					'method' => strtolower($request->action()),
					'status' => '1',
				]);

			if (!$rule) win_exception('未了解当前是什么操作,请联系超管添加操作权限', 231);

			if (!in_array($rule->id, $rule_list)) win_exception('你无权限操作,如想操作请联系超管添加权限', 233);

			return true;
		} catch (WinException $e) {
			return $e->false();
		}
	}
}