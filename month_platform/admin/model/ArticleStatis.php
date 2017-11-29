<?php
namespace app\admin\model;

use think\Model;

class ArticleStatis extends Common
{
	public function Article()
	{
		return $this->belongsTo('Article');
	}
}