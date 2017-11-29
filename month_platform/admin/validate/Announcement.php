<?php
namespace app\admin\validate;

use think\Validate;

class Announcement extends Validate
{
	protected $rule = [
		['title','require|max:250','请输入公告标题|标题不可超过250个字'],
		['content','require','请输入公告内容'],
		['link','url','请输入有效的URL跳转路径(必需以http://或https://开头)'],
		['receiver_type','require|in:1,2','请输入接收者类型|接收者类型只能为(1:用户,2:专家)'],
		['receiver_id','require|array','请输入接收者类型|接收者id只能为有效数组组成'],
	];

	protected $scene = [
        'add'  		=> ['title', 'content', 'link', 'receiver_type', 'receiver_id'],
    ];
}