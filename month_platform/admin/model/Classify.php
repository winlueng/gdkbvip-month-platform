<?php
namespace app\admin\model;

use think\Model;
use winleung\exception\WinException;

class Classify extends Common
{
	public function Article()
	{
		return $this->hasMany('Article', 'classify_id')
					->where('status', '1');
	}

	public function QuestionArticle()
	{
		return $this->hasMany('QuestionArticle', 'classify_id')
					->where('status', '1');
	}

	public function Banner()
	{
		return $this->hasMany('Banner', 'classify_id')
					->where('status', '1');
	}

	public function scopeStatus($sql)
	{
		$sql->where('status', '1');
	}

	public function scopeType($sql)
	{
		$sql->where('classify_type', '1');
	}

	public function scopeId($sql)
	{
		$sql->where('id', input('get.id'));
	}

	public function classify_add()
	{
		try {
			self::startTrans();
			$validate = validate('Classify');

			if (!$validate->scene('add')->check(input('post.'))) win_exception($validate->getError(), __LINE__);

			if (!self::allowField(true)->save(input('post.'))) win_exception('创建失败', __LINE__);

			self::commit();
			return return_no_data();
		} catch (WinException $e) {
			self::rollback();
			return $e->false();
		}
	}

	public function classify_list()
	{
		try {
			self::startTrans();

			$list = self::status()
						->where('pid', 0)
						->where('classify_type', input('get.classify_type'))
						->order('create_time desc')
						->paginate(10)
						->hidden(['status', 'create_time'])
						->toArray();

			if (!$list) win_exception('未查询到任何数据', __LINE__);
			// halt($list);
			foreach ($list as $v) {
				$arr = self::status()
							->withCount('Article,QuestionArticle,Banner')
							->where('pid', $v['id'])
							->order('create_time desc')
							->select()
							->hidden(['status', 'create_time'])
							->toArray();
				$v['article_count'] = 0;
				$v['question_article_count'] = 0;
				$v['banner_count'] = 0;
				$v['doctor_count'] = 0;
				if ($arr) {
					foreach ($arr as $vo) {
						$v['article_count'] += $vo['article_count'];
						$v['question_article_count'] += $vo['question_article_count'];
						$v['doctor_count'] += $vo['doctor_count'];
						$v['banner_count'] += $vo['banner_count'];
					}
					$v['child_list'] = $arr;
				}else{
					$v['child_list'] = [];
				}
				$result[] = $v;
			}

			$data_total = self::status()
										->where('pid', 0)
										->where('classify_type', input('get.classify_type'))
										->count();

			self::commit();
			return return_true($result, '', $data_total);
		} catch (WinException $e) {
			self::rollback();
			return $e->false();
		}
	}

	public function classify_info()
	{
		try {
			$info = self::status()
						->id()
						->find()
						->hidden(['status'])
						->toArray();

			if(!$info) win_exception('', __LINE__);

			return return_true($info);
		} catch (WinException $e) {
			return $e->false();
		}
	}

	public function top_classify()
	{
		try {
			$list = self::status()
						->where('pid', 0)
						->where('classify_type', input('get.classify_type'))
						->select()
						->hidden(['status'])
						->toArray();

			if(!$list) win_exception('', __LINE__);

			return return_true($list);
		} catch (WinException $e) {
			return $e->false();
		}
	}

	public function child_classify()
	{
		try {
			$list = self::status()
						->where('pid', input('get.pid'))
						->select()
						->hidden(['status'])
						->toArray();

			if(!$list) win_exception('', __LINE__);

			return return_true($list);
		} catch (WinException $e) {
			return $e->false();
		}
	}

	public function child_list()
	{
		try {
			$info = self::get(input('get.id'));

			if(!$info) win_exception('', __LINE__);

			$list = self::status()
						->where('pid', $info->pid)
						->select()
						->visible(['classify_name', 'classify_type', 'pid', 'id'])
						->toArray();

			return return_true($list);
		} catch (WinException $e) {
			return $e->false();
		}
	}

	public function classify_update()
	{
		try {
			self::startTrans();
			$validate = validate('Classify');

			if (!$validate->scene('update')->check(input('post.'))) win_exception($validate->getError(), __LINE__);

			if (!self::allowField(['classify_name', 'pid', 'update_time'])->save(input('post.'), ['id' => input('get.id')])) win_exception('修改失败', __LINE__);

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
			$info = self::get(input('get.id'));
			self::startTrans();
			if ($info->pid == 0) {

				$list = self::where('pid', $info->id)->column('id');
				if ($list) {
					
					if (!self::save(['status' => input('get.status')],['pid' => $info->id])) win_exception('删除分类失败', __LINE__);
					
					if (model('DoctorInfo')->all(function($sql) use ($list){
						$sql->where('departments_id', 'in', implode(',', $list));
					})->toArray()) {
						if (!model('DoctorInfo')->save(['status' => input('get.status')], function($sql) use ($list){
							$sql->where('departments_id', 'in', implode(',', $list));
						})) win_exception('删除所属医生失败', __LINE__);					
					}

					if ($test = model('Article')->all(function($sql) use ($list){
						$sql->where('classify_id', 'in', implode(',', $list));
					})->toArray()) {
						if (!model('Article')->save(['status' => input('get.status')], function($sql) use ($list){
							$sql->where('classify_id', 'in', implode(',', $list));
						})) win_exception('删除所属文章失败', __LINE__);					
					}

					if (model('Banner')->all(function($sql) use ($list){
						$sql->where('classify_id', 'in', implode(',', $list));
					})->toArray()) {
						if (!model('Banner')->save(['status' => input('get.status')], function($sql) use ($list){
							$sql->where('classify_id', 'in', implode(',', $list));
						})) win_exception('删除所属广告失败', __LINE__);					
					}

					if (model('QuestionArticle')->all(function($sql) use ($list){
						$sql->where('classify_id', 'in', implode(',', $list));
					})->toArray()) {
						if (!model('QuestionArticle')->save(['status' => input('get.status')], function($sql) use ($list){
							$sql->where('classify_id', 'in', implode(',', $list));
						})) win_exception('删除所属知识文章失败', __LINE__);	
					}
				}
			}else{
				if (model('DoctorInfo')->get(['departments_id' => $info->id])) {
					if(!model('DoctorInfo')->save(['status' => input('get.status')], ['departments_id' => $info->id])) win_exception('删除所属医生失败', __LINE__);
				}

				if (model('Article')->get(['classify_id' => $info->id])) {
					if(!model('Article')->save(['status' => input('get.status')], ['classify_id' => $info->id])) win_exception('删除所属文章失败', __LINE__);
				}

				if (model('Banner')->get(['classify_id' => $info->id])) {
					if(!model('Banner')->save(['status' => input('get.status')], ['classify_id' => $info->id])) win_exception('删除所属广告失败', __LINE__);
				}

				if (model('QuestionArticle')->get(['classify_id' => $info->id])) {
					if(!model('QuestionArticle')->save(['status' => input('get.status')], ['classify_id' => $info->id])) win_exception('删除所属知识文章失败', __LINE__);
				}
			}
			if (!$info->save(['status'=>input('get.status')])) win_exception('删除分类失败', __LINE__);

			self::commit();
			return return_no_data();
		} catch (WinException $e) {
			self::rollback();
			return $e->false();
		}
	}
}