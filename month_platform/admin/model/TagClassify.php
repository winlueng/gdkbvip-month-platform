<?php
namespace app\admin\model;

use think\Model;
use winleung\exception\WinException;

class TagClassify extends Common
{
	public function initialize()
	{
		parent::initialize();
		$this->insert = ['create_time' => time(), 'update_time' => time()];
		$this->update = ['update_time' => time()];
	}

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

	public function classify_add()
	{
		try {
			$validate = validate('TagClassify');

			if (!$validate->scene('add')->check(input('post.'))) win_exception($validate->getError(), __LINE__);

			self::startTrans();

			if (!self::allowField(true)->save(input('post.'))) win_exception('创建标签分类失败', __LINE__);

			if (!input('post.tag_list_ins')) win_exception('请提交标签列', __LINE__);

			$list = explode(',', input('post.tag_list_ins'));

			foreach ($list as $v) {
				$arr[] = ['tag_name' => $v, 'classify_id' => $this->id, 'classify_type' => input('post.tag_classify_type')];
			}

			if(!model('Tag')->saveAll($arr)) win_exception('创建标签失败', __LINE__);

			self::commit();
			return return_true(['id' => $this->id]);
		} catch (WinException $e) {
			self::rollback();
			return $e->false();
		}
	}

	public function classify_list()
	{
		$list = self::status()
					->with('Tag')
					->paginate(10)
					->hidden(['status'])
					->toArray();
		$data_total = self::status()->count();

		if($list) return return_true($list, '', $data_total);


		return return_false(__LINE__);
	}

	public function classify_update()
	{
		try {
			self::startTrans();

			$validate = validate('TagClassify');

			if (!$validate->scene('update')->check(input('post.'))) win_exception($validate->getError(), __LINE__);

			$info = self::get(input('get.id'));

			if (!$info->allowField(['update_time', 'tag_classify_name'])->save(input('post.'))) win_exception('修改失败', __LINE__);

			if (input('post.tag_list_ins')) {

				$list = explode(',', input('post.tag_list_ins'));

				foreach ($list as $v) {
					$arr[] = ['tag_name' => $v, 'classify_id' => $info->id, 'classify_type' => $info->tag_classify_type];
				}

				if(!model('Tag')->saveAll($arr)) win_exception('创建标签失败', __LINE__);
			}

			self::commit();
			return return_no_data();
		} catch (WinException $e) {
			self::rollback();
			return $e->false();
		}
	}

	public function classify_del()
	{
		try {
			self::startTrans();

			$info = self::get(input('get.id'));

			if(!$info->save(['status' => '-1'])) win_exception('删除失败', __LINE__);

			if(!model('Tag')->save(['status' => '-1'], ['classify_id' => $info->id])) win_exception('删除隶属标签失败', __LINE__);
			
			self::commit();
			return return_no_data();
		} catch (WinException $e) {
			self::rollback();
			return $e->false();
		}
	}
}