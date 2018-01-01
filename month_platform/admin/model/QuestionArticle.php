<?php
namespace app\admin\model;

use think\Model;
use winleung\exception\WinException;

class QuestionArticle extends Common
{
	private $XS;
	private $doc;
	private $XSindex;
	public function initialize()
	{
		parent::initialize();
		require_once ('../vendor/XSsdk/php/lib/XS.php');
		$this->XS  		= new \XS('question_article');
		$this->doc 		= new \XSDocument();
		$this->XSindex	= $this->XS->index;
	}

	public function Classify()
	{
		return $this->belongsTo('Classify');
	}

	public function classifyBl()
	{
		return $this->hasMany('Classify', 'id', 'classify_id');
	}

	public function article_add()
	{
		try {
			$validate = validate('QuestionArticle');
			if (!$validate->scene('add')->check(input('post.'))) win_exception($validate->getError(), __LINE__);

			if (!self::allowField(true)->save(input('post.'))) win_exception('新增数据失败', __LINE__);

			$data = [
				'id' 		   => $this->id,
				'article_name' => input('post.article_name'),
				'status'	   => 1,
				'article_content' => input('post.article_content'),
				'create_time' => $info->create_time,
			];

			$this->doc->setFields($data);
			$this->XSindex->add($this->doc);

			return return_no_data();
		} catch (WinException $e) {
			return $e->false();
		}
	}

	public function article_list()
	{
		try {
			$list = self::status()
						->classify_id()
						->with('classifyBl')
						->order('create_time desc')
						->paginate(10)
						->visible(['id', 'article_name', 'tag_list', 'classify_id', 'article_statis_count', 'status', 'classify_bl'])
						->toArray();

			if(!$list) win_exception('', __LINE__);

			foreach ($list as $v) {
				$sum = model('QuestionArticleStatis')->where('relevance_id', $v['id'])->sum('click_total');
				$v['statis_sum'] = $sum?$sum:0;
				$v['tag_info_list'] = model('Tag')->where('id', 'in', implode(',', $v['tag_list']))->select()->toArray();
				$result[] = $v;
			}

			$data_total = self::status()
										->classify_id()
										->count();

			return return_true($result, '', $data_total);
		} catch (WinException $e) {
			return $e->false();
		}
	}

	public function article_info()
	{
		try {
			$info = self::status()
						->id()
						->find();

			if (!$info) win_exception('', __LINE__);

			return return_true($info);
		} catch (WinException $e) {
			return $e->false();
		}
	}

	public function article_update()
	{
		try {
			$validate = validate('QuestionArticle');

			if (!$validate->scene('add')->check(input('post.'))) win_exception($validate->getError(), __LINE__);

			if (!self::allowField(true)->save(input('post.'),['id' => input('get.id')])) win_exception('修改数据失败', __LINE__);

			$info = self::get(input('get.id'));

			$data = [
				'id' 		   => $info->id,
				'article_name' => $info->article_name,
				'status'	   => $info->status,
				'article_content' => $info->article_content,
				'create_time' => $info->create_time,
			];

			$this->doc->setFields($data);
			$this->XSindex->update($this->doc);

			return return_no_data();
		} catch (WinException $e) {
			return $e->false();
		}
	}

	public function article_status()
	{
		try {
			if (!self::save(['status' => input('get.status')], ['id' => input('get.id')])) win_exception('操作失败', __LINE__);

			if (input('get.status') == '-1') {
				$this->XSindex->del(input('get.id'));
			}

			$info = self::get(input('get.id'));

			$data = [
				'id' 		   => $info->id,
				'article_name' => $info->article_name,
				'status'	   => $info->status,
				'article_content' => $info->article_content,
				'create_time' => $info->create_time,
			];

			$this->doc->setFields($data);
			$this->XSindex->update($this->doc);

			return return_no_data();
		} catch (WinException $e) {
			return $e->false();
		}
	}
}