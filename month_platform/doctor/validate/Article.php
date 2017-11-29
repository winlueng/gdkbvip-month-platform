<?php
namespace app\doctor\validate;

use think\Validate;

class Article extends Validate
{
	protected $rule = [
		['article_name','require|max:40','请输入文章名称|名称最大长度为40个有效字符'],
		// ['article_logo','require','请输入文章LOGO'],
		['article_content','require','请输入文章内容'],
		['tag_list','require|array','请输入标签列|标签列必需是数组'],
	];

	protected $scene = [
		'add' 		=> ['article_name'/*, 'article_logo'*/, 'article_content', 'tag_classify_id', 'tag_list'],
	];
}