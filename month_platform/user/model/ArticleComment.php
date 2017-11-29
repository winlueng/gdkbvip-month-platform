<?php
namespace app\user\model;

use think\Model;
use winleung\exception\WinException;

class ArticleComment extends Common
{
	// 根据机构id获取
	public function scopeOid($sql) 
	{
		$sql->where('article_id', input('get.article_id'));
	}

	public function Article()
	{
		return $this->hasMany('Article', 'id', 'article_id')
					->field('id, article_name, article_logo, update_time');
	}

	public function ArticleStatis()
	{
		return $this->hasMany('ArticleStatis', 'relevance_id', 'article_id')
					->field(['relevance_id', 'SUM(click_total)' => 'statis']);
	}

	public function User()
	{
		return $this->hasMany('User', 'id', 'user_id')
					->field('id, nick_name, head_url');
	}

	public function getPraiseListAttr($value)
	{
		return json_decode($value, true);
	}

	public function comment_list_by_article()
	{
		try {
			$list = self::status()
						->oid()
						->with('User')
						->paginate(10)
						->hidden(['status'])
						->toArray();

			if(!$list) win_exception('', __LINE__);

			foreach ($list as $v) {
				if ($v['praise_list'])
				{
					$v['praise_total'] = count($v['praise_list']);
				}else{
					$v['praise_total'] = 0;
				}
				$res[] = $v;
			}

			return return_true($res);
		} catch (WinException $e) {
			return $e->false();
		}
	}

	public function comment_add()
	{
		try {
			$validate = validate('ArticleComment');

			if (!$validate->scene('create')->check(input('post.'))) {
				win_exception($validate->getError(), __LINE__);
			}

			if (!self::allowField(true)->save(input('post.'))) {
				win_exception('评论失败', __LINE__);
			}

			model('ArticleBehavior')->note_down_user_article_behavior('comment', input('post.article_id'));

			return return_no_data();
		} catch (WinException $e) {
			return $e->false();
		}
	}

	public function change_comment_status()
	{
		try {
			$info = self::id()
						->status()
						->find();

			if(!$info) win_exception('', __LINE__);

			$info->status = input('get.status');

			if(!$info->save()) win_exception('操作失败', __LINE__);

			return return_no_data();
		} catch (WinException $e) {
			return $e->false();
		}
	}

	public function my_comment_list()
	{
		try {
			$list = self::status()
						->user_id()
						->with('Article,ArticleStatis')
						->paginate(10)
						->hidden(['status'])
						->toArray();

			if(!$list) win_exception('', __LINE__);

			return return_true($list);
		} catch (WinException $e) {
			return $e->false();
		}
	}

	public function praise_or_cancel()
	{
		try {
			$info = self::where('id', input('get.id'))
						->where('status', '1')
						->find();

			if (!$info) win_exception('', __LINE__);

			$praise_list = $info->praise_list;

			switch (input('get.control')) {
				case 'praise':
					if (!$praise_list) {
						$info->praise_list = json_encode([$this->user_info['id']]); 
					}else{
						$linshi = array_merge($praise_list, [$this->user_info['id']]);
						$info->praise_list = json_encode(array_unique($linshi));
					}
					break;
				case 'cancel':
					if (!$praise_list) {
						win_exception('无任何点赞数据', __LINE__);
					}else{
						$key = array_search($this->user_info['id'], $praise_list);
						if ($key === false) {
							win_exception('你没点过赞', __LINE__);
						}else{
							unset($praise_list[$key]);
							$info->praise_list = json_encode($praise_list);
						}
					}
					break;
			}
			
			if (!$info->save()) win_exception('操作失败', __LINE__);

			return return_no_data();
		} catch (WinException $e) {
			return $e->false();	
		}
	}
}