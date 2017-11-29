<?php
namespace app\admin\model;

use think\Model;

class UserSymptomatography extends Common
{
	public function getSymptomImgAttr($value)
	{
		return json_decode($value, true);
	}
}