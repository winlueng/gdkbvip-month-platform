<?php
namespace app\admin\validate;

use think\Validate;

class Banner extends Validate
{
	protected $rule = [
		['title','require|max:40','请输入广告标题|标题最大长度为40个有效字符'],
		['banner_logo','require','请输入广告logo路径'],
		['link','require|url','请输入广告URL跳转路径|请输入有效的URL跳转路径(必需以http://或https://开头)'],
		['tag_classify_id','require|number','请输入标签分类tag_classify_id|tag_classify_id只能为整数'],
		['classify_id','require|number','请输入分类classify_id|classify_id只能为整数'],
		['tag_list','require|array','请输入标签列|标签列必需是数组'],
	];

	protected $scene = [
		'add' => ['title', 'banner_logo', 'link', 'tag_classify_id', 'classify_id', 'tag_list'],
	];
}