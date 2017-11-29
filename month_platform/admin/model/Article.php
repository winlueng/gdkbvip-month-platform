<?php
namespace app\admin\model;

use think\Model;
use winleung\exception\WinException;

class Article extends Common
{
	public function Classify()
	{
		return $this->belongsTo('Classify');
	}

	public function classifybl()
	{
		return $this->hasMany('Classify', 'id', 'classify_id');
	}

	public function ArticleStatis()
	{
		return $this->hasMany('ArticleStatis', 'relevance_id');
	}

	public function ArticleBehavior()
	{
		return $this->hasMany('ArticleBehavior', 'article_id', 'id');
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
						->classify_id()
						->where('doctor_id', '0')
						->withCount(['ArticleBehavior' => function($sql) {
    						$sql->where('visit_total', '>', '0');
    					}])
    					->with('Classify')
						->order('create_time desc')
						->paginate(10)
						->visible(['id', 'article_name', 'tag_list', 'classify_id', 'article_behavior_count', 'status', 'classify'])
						->toArray();

			if(!$list) win_exception('', __LINE__);

			foreach ($list as $v) {
				$v['tag_info_list'] = model('Tag')->where('id', 'in', implode(',', $v['tag_list']))->select()->toArray();
				$result[] = $v;
			}

			$data_total = self::status()->classify_id()->count();

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
			$validate = validate('Article');

			if (!$validate->scene('add')->check(input('post.'))) win_exception($validate->getError(), __LINE__);

			if (!self::allowField(true)->save(input('post.'),['id' => input('get.id')])) win_exception('修改数据失败', __LINE__);

			return return_no_data();
		} catch (WinException $e) {
			return $e->false();
		}
	}

	public function article_status()
	{
		try {
			self::startTrans();
			if (!self::save(['status' => input('get.status')], ['id' => input('get.id')])) win_exception('操作失败', __LINE__);

				db('ArticleBehavior')->where('article_id', input('get.id'))->update(['status' => input('get.status')]);
			self::commit();
			return return_no_data();
		} catch (WinException $e) {
			self::rollback();
			return $e->false();
		}
	}

	public function article_list_by_doctor()
    {
    	try {
    		if (input('get.doctor_id')) $id = input('get.doctor_id');

    		$list = self::status()
    					->where('doctor_id', $id)
    					->withCount(['ArticleBehavior' => function($sql) {
    						$sql->where('visit_total', '>', '0');
    					}])
    					->with('Classify')
    					->paginate(10)
                        ->hidden(['status'])
                        ->toArray();

    		if (!$list) win_exception('', __LINE__);

    		foreach ($list as $v) {
                $v['tag'] = model('Tag')
                            ->field('id, tag_name')
                            ->where('id', 'in', implode(',', $v['tag_list']))
                            ->select();

                $result[] = $v;
            }

            $result['data_total'] = self::status()
    									->where('doctor_id', $id)
    									->count();

            return return_true($result);
    	} catch (WinException $e) {
    		return $e->false();
    	}
    }
}