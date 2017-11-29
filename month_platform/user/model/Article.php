<?php
namespace app\user\model;

use think\Model;
use winleung\exception\WinException;
use app\user\model\Behavior;

class Article extends Common
{
	public function scopeClassify($sql)
	{
		$sql->where('classify_id', input('get.classify_id'));
	}

	public function scopeRecommend($sql)
	{
		$id_list = model('ArticleBehavior')->getUserArticleNewTrack();
		if (!isset($id_list['err_msg'])) {
			$sql->where('id', 'in', implode(',', $id_list))->order('field(`id`, '. implode(',', $id_list) .')');
		}else{
			$sql->where('status', '1')->where('doctor_id', '0')->order('create_time desc');
		}
	}

    public function getArticleLogoAttr($value)
    {
        return json_decode($value, true);
    }

    public function ArticleBehavior()
    {
        return $this->hasMany('ArticleBehavior');
    }

    public function getArticleContentAttr($value)
    {
        return htmlspecialchars_decode(html_entity_decode($value));
    }

    // 根据分类id获取文章数据
	public function get_article_by_classify()
	{
		try {
			$list = self::classify()
						->status()
						->paginate(10)
                        ->hidden(['status'])
                        ->toArray();

			if (!$list) win_exception('', __LINE__);

			foreach ($list as $v) {
				$v['tag'] = model('Tag')
							->field('id, tag_name')
							->where('id', 'in', implode(',', $v['tag_list']))
							->select();
				$visit_total = model('ArticleStatis')->where('relevance_id', $v['id'])->sum('click_total');
				$v['visit_total'] = $visit_total?$visit_total:0;
				$result[] = $v;
			}

			return return_true($result);
		} catch (WinException $e) {
			return $e->false();
		}
	}

	public function article_info()
    {
        try{
            $info = self::id()
                        ->status()
                        ->withCount(['ArticleBehavior' => function ($sql){
                            $sql->where('article_id', input('get.id'))->where('visit_total', '<>', '0');
                        }])
                        ->find();

            if (!$info) win_exception('',__LINE__);

            $list = self::field('id,article_name')
                        ->where('status', '1')
                        ->where('id', '<>', $info->id)
                        ->where('tag_list', json_encode($info->tag_list))
                        ->order('create_time desc')
                        ->limit(4)
                        ->select()
                        ->toArray();

            if (!$list) {
                $list = self::field('id,article_name')
                        ->where('status', '1')
                        ->where('id', '<>', $info->id)
                        ->where('classify_id', $info->classify_id)
                        ->order('create_time desc')
                        ->limit(4)
                        ->select()
                        ->toArray();
            }

            $info['relevance_list'] = $list;

            return return_true($info);
        } catch (WinException $e){
            return $e->false();
        }
    }

    public function recommend_list()
    {
    	try {
    		$list = self::recommend()
    					->paginate(10);

    		if (!$list) {
    			win_exception('', __LINE__);
    		}
    		return return_true($list->toArray()['data']);
    	} catch (WinException $e) {
    		return $e->false();
    	}
    }

    public function article_list_by_doctor($id = '')
    {
    	try {
    		if (input('get.doctor_id')) $id = input('get.doctor_id');

    		$list = self::status()
    					->where('doctor_id', $id)
                        ->withCount(['ArticleBehavior' => function ($sql){
                            $sql->where('article_id', input('get.id'))->where('visit_total', '<>', '0');
                        }])
    					->paginate(10)
                        ->hidden(['status'])
                        ->toArray();

    		if (!$list) win_exception('', __LINE__);

    		foreach ($list as $v) {
                $v['tag'] = model('Tag')
                            ->field('id, tag_name')
                            ->where('id', 'in', implode(',', $v['tag_list']))
                            ->select();
                $visit_total = model('ArticleStatis')->where('relevance_id', $v['id'])->sum('click_total');
                $v['visit_total'] = $visit_total?$visit_total:0;
                $result[] = $v;
            }

            return return_true($result);
    	} catch (WinException $e) {
    		
    	}
    }
}