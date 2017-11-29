<?php
namespace app\user\model;

use think\Model;
use winleung\exception\WinException;

class QuestionArticle extends Common
{
	public function getArticleContentAttr($value)
    {
        return htmlspecialchars_decode(html_entity_decode($value));
    }
	
	// 获取相关文章
	public function correlation_article()
	{
		try {
			$info = self::status()
						->id()
						->value('tag_list');

			$list = self::status()
						->where('tag_list', $info)
						->where('id', '<>', input('get.id'))
						->column('id');

			if (!$list) {
				win_exception('', __LINE__);
			}

			$list = implode(',', $list);

			$sort_list = model('QuestionArticleStatis')->where('relevance_id', 'in', $list)
													->order('SUM(click_total) desc')
													->limit(4)
													->select();
			if (!$sort_list) {
				$sort_list = $list;
			}

			$result = self::status()
						  ->where('id', 'in', $list)
						  ->order('create_time')
						  ->limit(4)
						  ->select()
						  ->visible(['id', 'article_name'])
						  ->toArray();

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

	public function article_praise_or_cancle()
	{
		try {
			$info = self::status()
						->id()
						->find();

			if (!$info) win_exception('', __LINE__);
			$praise_list = $info->praise_list;
			switch (input('get.praise')) {
				case '1':
					if (!$praise_list) {
						$praise_list = [$this->user_info['id'] => ['t' => time(), 'uid' => $this->user_info['id']]];
					}else{
						if ($praise_list[$this->user_info['id']]) {
							win_exception('已经点赞过', __LINE__);
						}
						$praise_list += [$this->user_info['id'] => ['t' => time(), 'uid' => $this->user_info['id']]];
					}
					break;
				case '2':
					
					break;
			}

			return return_no_data();
		} catch (WinException $e) {
			return $e->false();
		}
	}
}