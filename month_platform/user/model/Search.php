<?php
namespace app\user\model;

use think\Model;
use winleung\exception\WinException;

class Search extends Common
{
	private $XS;
	private $XSindex;
	private $search;
	public function initialize()
	{
		parent::initialize();
		require_once ('../vendor/XSsdk/php/lib/XS.php');
		$this->XS  		= new \XS('question_article');
		$this->XSindex	= $this->XS->index;
		$this->search   = $this->XS->search;
	}

	public function hot_words()
	{
		$words = $this->search->getHotQuery(10);

		foreach ($words as $k => $v) {
			$arr[$k] = $v;
		}

		arsort($arr);

		$words = array_flip($arr);

		$words = array_merge($words);

		if ($words) {
			return return_true($words);
		}
		return return_false(__LINE__);
	}

	public function history_words()
	{
		$words = $this->redis->get('history_words_by_'.$this->user_info['id']);

		if ($words) {
			return return_true(json_decode($words, true));
		}
		return return_false(__LINE__);
	}

	public function go_article_search()
	{
		try {
			$word = input('get.word');

			$docs = $this->search
						 ->setQuery("article_name:". $word)
						 ->addQueryTerm("status",'1')
						 ->search();

			if (!$docs) {
				model('UserQuestionRecord')->save([
					'user_id' => $this->user_info['id'],
					'question_title' => $word
				]);
				win_exception('', __LINE__);
			}

			if(!self::save_history_word($word)) win_exception('记录历史数据失败', __LINE__);

			foreach ($docs as $k => $doc) {
				$arr[$k]['id'] 				= $doc->id;
				$arr[$k]['article_name'] 	= $doc->article_name;
				$arr[$k]['status'] 			= $doc->status;
				$arr[$k]['article_content'] = $doc->article_content;
			}

			return return_true($arr);
		} catch (WinException $e) {
			return $e->false();
		}		
	}

	public function go_doctor_search()
	{
		try {
			$this->XS  = new \XS('doctor');
			$this->search   = $this->XS->search;

			$word = input('get.word');

			$docs = $this->search
						 ->setQuery("skilled:". $word)
						 ->addQueryTerm("status",'1')
						 ->search();

			if (!$docs) {
				win_exception('', __LINE__);
			}

			foreach ($docs as $k => $doc) {
				$arr[$k]['id'] 			= $doc->id;
				$arr[$k]['skilled'] 	= $doc->skilled;
				$arr[$k]['doctor_name'] = $doc->doctor_name;
				$arr[$k]['create_time'] = $doc->create_time;
				$arr[$k]['doctor_logo'] = $doc->doctor_logo;
			}

			return return_true($arr);
		} catch (WinException $e) {
			return $e->false();
		}
	}

	public function save_history_word($word)
	{
		$words = $this->redis->get('history_words_by_'.$this->user_info['id']);

		if (!$words) {
			$words = [$word];
		}else{
			$words = json_decode($words, true);

			if (!in_array($word, $words)) {
				array_unshift($words, $word);

				$words = array_merge($words);

				if (count($words) > 20) {
					array_pop($words);
				}
			}
		}

		return $this->redis->set('history_words_by_'.$this->user_info['id'], json_encode($words));
	}
}