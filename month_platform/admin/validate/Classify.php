<?php
namespace app\admin\validate;

use think\Validate;

class Classify extends Validate
{
	protected $rule = [
		['classify_name','require|chsAlpha|max:4','请输入标签分类名称|标签分类名称只能是汉字或字母组成|标签分类名称最大长度为4'],
		['classify_type','require|number|in:1,2','请输入标签分类属性|分类属性只能为整数|取值只能为 1或2(1:文章,2:专家)'],
		['pid','require|number','请输入父级分类id|父级分类只能为整数(0:为顶级分类)'],
	];

	protected $scene = [
		'add' 		=> ['classify_name', 'classify_type', 'pid'],
		'update' 	=> ['classify_name', 'pid'],
	];
}