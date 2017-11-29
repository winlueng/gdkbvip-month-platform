<?php
namespace app\user\model;

use think\Model;

class ArticleStatis extends Model
{
	public function statis_save()
	{
		try {
			if ($info = self::get(['relevance_id' => input('get.article_id'), 'timezone' => strtotime(date('Y-m-d H:00:00'))])) {
				$info->click_total += 1;
			}else{
				if (!self::save(['relevance_id' => input('get.article_id'), 'timezone' => strtotime(date('Y-m-d H:00:00'))])) {
					win_exception('', __LINE__);
				}
				return true;
			}

			if (!$info->save()) {
				win_exception('', __LINE__);
			}

		} catch (WinException $e) {
			return false;
		}
	}
}