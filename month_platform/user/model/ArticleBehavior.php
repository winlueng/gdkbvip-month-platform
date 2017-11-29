<?php
namespace app\user\model;

use think\Model;
use winleung\exception\WinException;

/**
 * 文章行为分析
 */
class ArticleBehavior extends Common
{
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

	/**
	 * 获取用户文章并进行协同处理
	 */
	public function getUserArticleNewTrack()
	{
		try {
			// 1. 查询当前用户最近浏览的5篇文章
			$user_article_id_list = self::status()
									    ->where('user_id', $this->user_info['id'])
									    ->where('visit_second', '>', '5')// 浏览大于5秒的行为
									    ->order('update_time desc')
									    ->limit(5)
									    ->select()
									    ->toArray();
			// 判断最少数据不能少于2篇
			if (count($user_article_id_list) < 2) win_exception('数据不足以支撑执行协同过滤', __LINE__);

			// 2. 计算对每篇文章的兴趣分数(总:5分)
			foreach ($user_article_id_list as $v) { 
				$article_id_list[] = $v['article_id'];
				$score = ($v['visit_second'] > 10)?10:$v['visit_second'];
				$score += $v['is_save']?10:0;
				$score += ($v['visit_total'] > 10)?10:$v['visit_total'];
				$score += ($v['share_total'] > 10)?10:$v['share_total'];
				$score += ($v['comment_total'] > 10)?10:$v['comment_total'];
				$user_article_score[] = $score/10;
			}

			if (!isset($article_id_list)) win_exception('变量article_id_list数据丢失', __LINE__);

			// 3. 查找对这些篇文章也浏览过的用户数据
			$the_same_article_by_user = self::where('article_id', 'in', implode(',', $article_id_list))
											->where('user_id', '<>', $this->user_info['id'])
											->where('visit_second', '>', '5')
											->order('field(id,"'. implode(',', $article_id_list) .'")')
											->select()
											->toArray();
		    // 判断是否有足够的数据去处理
			if (!($the_same_article_by_user)) win_exception('数据不足以支撑执行协同过滤', __LINE__);

			// 4. 计算同样浏览过的用户对每篇文章的兴趣分数并进行分组(总:5分)
			foreach ($the_same_article_by_user as $v) { 
				$score =  ($v['visit_second'] > 10)?10:$v['visit_second'];
				$score += $v['is_save']?10:0;
				$score += ($v['visit_total'] > 10)?10:$v['visit_total'];
				$score += ($v['share_total'] > 10)?10:$v['share_total'];
				$score += ($v['comment_total'] > 10)?10:$v['comment_total'];
				$other_user_article_score_test[$v['user_id']][] = $score/10;
			}

			if (!isset($other_user_article_score_test)) win_exception('变量$other_user_article_score_test数据丢失', __LINE__);

			// 5. 进一步过滤得出本用户浏览过同等商品数量的用户
			foreach ($other_user_article_score_test as $k => $v) {
				if (count($v) == count($user_article_id_list)) {
					$other_user_article_score[$k] = $v;
				}
			}

			// 判断是否真的有这些用户存在
			if (!isset($other_user_article_score)) win_exception('其他用户数据不足以支撑执行协同过滤', __LINE__);
			
			// 6. 计算相关用户与当前用户的相关系数
			foreach ($other_user_article_score as $k => $v) {
				$corrcoef = corrcoef($user_article_score, $v);
				if ($corrcoef > 0.4) { // 系数大于0.4(中等相关以上)才进行合群推荐
					$correlation_user[$k] = $corrcoef;
				}
			}

            if (!isset($correlation_user)) win_exception('变量$correlation_user数据丢失', __LINE__);

			// 7. 获取群体浏览过的所有文章数据
			$correlation_article_id_list = self::status()
												->where('id', 'not in', implode(',', $article_id_list))
												->where('user_id', 'in', array_keys($correlation_user))
												->where('doctor_id', '0')
												->select()
												->toArray();

			if (!$correlation_article_id_list) win_exception('没相关的文章推荐', __LINE__);

			// 8. 计算相似度总计
			$corrcoef_sum = array_sum($correlation_user);

			// 9. 对每篇文章算分并加权
			foreach ($correlation_article_id_list as $v) {
				$score =  ($v['visit_second'] > 10)?10:$v['visit_second'];
				$score += $v['is_save']?10:0;
				$score += ($v['visit_total'] > 10)?10:$v['visit_total'];
				$score += ($v['share_total'] > 10)?10:$v['share_total'];
				$score += ($v['comment_total'] > 10)?10:$v['comment_total'];

				if (!isset($correlation_article_score[$v['article_id']]['score'])) {// 初始化分数
					$correlation_article_score[$v['article_id']]['score'] = 0;
				}

				$correlation_article_score[$v['article_id']]['score'] += $score/10 * $correlation_user[$v['user_id']];// 加权并叠加总分

			}

			// 10. 对所有文章进行最终计分
			foreach ($correlation_article_score as $k => $v) {
				$correlation_article_score_by_id[$k] = $v['score']/$corrcoef_sum;
			}

			// 11. 按分数排序保留关联性
			arsort($correlation_article_score_by_id);

			// 12. 最后提取文章id
			$final_article_id_list = array_keys($correlation_article_score_by_id);

			return $final_article_id_list;
		} catch (WinException $e) {
			return $e->false();
		}
	}

	// 记录用户对文章的行为数据
	public function note_down_user_article_behavior($behavior = '', $oid = 0)
	{
		try {
			if (input('get.behavior')) $behavior = input('get.behavior');

			if (input('get.article_id')) $oid = input('get.article_id');

			$behavior_info = self::get(['user_id' => $this->user_info['id'], 'article_id' => $oid]);

			if (!$behavior_info) {
				self::save(['user_id' => $this->user_info['id'], 'article_id' => $oid]);
				$behavior_info = self::get(['user_id' => $this->user_info['id'], 'article_id' => $oid]);
			}
			switch ($behavior) {
				case 'save':
					$behavior_info->is_save = 1;
					break;
				case 'share':
					$behavior_info->share_total += 1;
					break;
				case 'visit':
					$behavior_info->visit_total += 1;
					$behavior_info->visit_second += input('get.visit_second');
					if(!model('ArticleStatis')->statis_save()) win_exception('记录浏览量失败', __LINE__);
					break;
				case 'comment':
					$behavior_info->comment_total += 1;
					break;
				case 'praise':
					$behavior_info->is_like = '1';
					break;
			}
			if (!$behavior_info->save()) win_exception('更新行为失败', __LINE__);

			return return_no_data();
		} catch (WinException $e) {
			return $e->false();
		}
	}

	public function get_my_save()
	{
		try {
			$list = self::status()
						->user_id()
						->with('Article,ArticleStatis')
						->where('is_save', '1')
						->paginate(10)
						->hidden(['status'])
						->toArray();

			if(!$list) win_exception('', __LINE__);

			return return_true($list);
		} catch (WinException $e) {
			return $e->false();
		}
	}

	public function change_no_save()
	{
		try {
			if (!self::save(['is_save' => '0'], function($sql){
				$sql->where('article_id', 'in', input('get.article_id'));
			})) {
				win_exception('操作失败', __LINE__);
			}

			return return_no_data();
		} catch (WinException $e) {
			return $e->false();
		}
	}

	public function get_my_like()
	{
		try {
			$list = self::status()
						->user_id()
						->with('Article,ArticleStatis')
						->where('is_like', '1')
						->paginate(10)
						->hidden(['status'])
						->toArray();

			if(!$list) win_exception('', __LINE__);

			return return_true($list);
		} catch (WinException $e) {
			return $e->false();
		}
	}

	public function change_no_like()
	{
		try {
			if (!self::save(['is_like' => '0'], function($sql){
				$sql->where('article_id', 'in', input('get.article_id'));
			})) {
				win_exception('操作失败', __LINE__);
			}

			return return_no_data();
		} catch (WinException $e) {
			return $e->false();
		}
	}
}