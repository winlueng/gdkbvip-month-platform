<?php
namespace app\admin\validate;

use think\Validate;

class TagClassify extends Validate
{
	protected $rule = [
		['tag_classify_name','require|chsAlpha|max:4','请输入标签分类名称|标签分类名称只能是汉字或字母组成|标签分类名称最大长度为4'],
		['tag_classify_type','require|number|in:1,2','请输入标签分类属性|分类属性只能为整数|取值只能为 1或2(1:文章,2:专家)'],
	];

	protected $scene = [
		'add' 		=> ['tag_classify_name', 'tag_classify_type'],
		'update' 	=> ['tag_classify_name'],
	];
}