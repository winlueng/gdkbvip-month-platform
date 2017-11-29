<?php
namespace app\admin\validate;

use think\Validate;

class Article extends Validate
{
	protected $rule = [
		['article_name','require|max:40','请输入文章名称|名称最大长度为40个有效字符'],
		['article_logo','require|array','请输入文章LOGO|图片上传类型必需是数组'],
		['article_content','require','请输入文章内容'],
		['doctor_id','require|number','请输入医生id(0为官方发布)|id只能为整数'],
		['tag_classify_id','require|number','请输入标签分类tag_classify_id|tag_classify_id只能为整数'],
		['classify_id','require|number','请输入分类classify_id|classify_id只能为整数'],
		['tag_list','require|array','请输入标签列|标签列必需是数组'],
	];

	protected $scene = [
		'add' 		=> ['article_name', 'article_logo', 'article_content', 'doctor_id', 'tag_classify_id', 'classify_id', 'tag_list'],
	];
}