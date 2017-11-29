<?php
namespace app\user\model;

use think\Model;

class OrganizationDetail extends Common
{
	public function getDescriptionAttr($value)
	{
		return htmlspecialchars_decode(html_entity_decode($value));
	}

	public function getOrganizationPicAttr($value)
	{
		return json_decode($value, true);
	}
}