<?php
namespace app\doctor\model;

use think\Model;
use winleung\exception\WinException;

class Article extends Common
{ 
	public function initialize()
	{
		parent::initialize();
		$this->insert = ['doctor_id' => $this->user_info['id']];
	}

	public function getArticleContentAttr($value)
	{
		return htmlspecialchars_decode(html_entity_decode($value));
	}

	public function setArticleLogoAttr($value)
	{
		return json_encode($value);
	}

	public function getArticleLogoAttr($value)
	{
		return json_decode($value, true);
	}

	public function scopeDoctor_id($sql)
	{
		$sql->where('doctor_id', $this->user_info['id']);
	}

	public function scopeOther_id($sql)
	{
		$sql->where('doctor_id', input('get.doctor_id'));
	}

	public function article_add()
	{
		try {
			$validate = validate('Article');
			if (!$validate->scene('add')->check(input('post.'))) win_exception($validate->getError(), __LINE__);

			if (!self::allowField(true)->save(input('post.'))) win_exception('新增数据失败', __LINE__);

			return return_no_data();
		} catch (WinException $e) {
			return $e->false();
		}
	}

	public function article_list()
	{
		try {
			$list = self::status()
						->doctor_id()
						->order('create_time desc')
						->paginate(8)
						->hidden(['status', 'classify_id','tag_classify_id'])
						->toArray();

			if(!$list) win_exception('', __LINE__);

			foreach ($list as $v) {
				$sum = model('ArticleStatis')->where('relevance_id', $v['id'])->sum('click_total');
				$v['statis_sum'] = $sum?$sum:0;
				$result[] = $v;
			}

			return return_true($result);
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

	public function other_article_list()
	{
		try {
			$list = self::status()
						->other_id()
						->order('create_time desc')
						->paginate(8)
						->hidden(['status', 'classify_id','tag_classify_id'])
						->toArray();

			if(!$list) win_exception('', __LINE__);

			foreach ($list as $v) {
				$sum = model('ArticleStatis')->where('relevance_id', $v['id'])->sum('click_total');
				$v['statis_sum'] = $sum?$sum:0;
				$result[] = $v;
			}

			return return_true($result);
		} catch (WinException $e) {
			return $e->false();
		}
	}

	public function article_status()
	{
		try {
			if (!self::save(['status' => '-1'], ['id' => input('get.id'), 'doctor_id' => $this->user_info['id']])) win_exception('操作失败', __LINE__);

			return return_no_data();
		} catch (WinException $e) {
			return $e->false();
		}
	}
}