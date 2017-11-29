<?php 
namespace app\user\validate;

use think\Validate;

class ArticleComment extends Validate
{
	protected $rule = [
		['article_id','require|number|notIn:0','请输入文章id|文章id必需为整数|文章id不可为0'],
		['comment_info', 'require|max:200', '请输入评论内容|评论内容最大字数为200']
	];

	protected $scene = [
		'create'	=> ['article_id', 'comment_info']
	];
}

 ?>