<?php
namespace app\admin\validate;

use think\Validate;

class QuestionArticle extends Validate
{
	protected $rule = [
		['article_name','require|max:40','请输入文章名称|名称最大长度为40个有效字符'],
		['article_content','require','请输入文章内容'],
		['tag_classify_id','require|number','请输入标签分类tag_classify_id|tag_classify_id只能为整数'],
		['tag_list','require|array','请输入标签列|标签列必需是数组'],
		['classify_id','require|number','请输入分类classify_id|classify_id只能为整数'],
	];

	protected $scene = [
		'add' 		=> ['article_name', 'classify_id', 'article_content', 'tag_classify_id', 'tag_list'],
	];
}