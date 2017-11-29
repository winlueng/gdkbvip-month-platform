<?php 
namespace app\base\model;

use think\Model;
use \app\admin\model\Announcement;
use winleung\exception\WinException;
use \app\admin\model\SubscribeOrder;

class BaseApi extends Model
{
	private $sysObj;
	private $orderObj;

	public function initialize()
	{
		parent::initialize();
		$sysObj = new Announcement;
		$orderObj = new SubscribeOrder;
	}

	public function sendSystemNews()
	{
		try {
			// $list = $orderObj->where('');

			$news = [
				'title' => "您预约{$organization->organization_name}的时间更改为{$time},请留意您的时间",
				'order_id'	=> $info->id,
				'receiver_id' => $info->user_id
			];


		} catch (WinException $e) {
			trace($e->getMessage());
		}
	}
}

 ?>