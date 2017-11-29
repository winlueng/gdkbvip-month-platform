<?php
namespace app\admin\model;

use think\Model;
use winleung\exception\WinException;

class NewsRecord extends Common
{
	// 获取聊天记录
	public function news_history()
	{
		try {
			$list = self::where('status', '0')
					    ->where('order_id', input('get.id'))
					    ->order('create_time desc')
					    ->paginate(10)
					    ->hidden(['update_time'])
					    ->toArray();

			if (!$list) win_exception('', __LINE__);
			if ($list[0]['send_type'] == '1') {
				$doctor_info = model('DoctorInfo')->get($list[0]['receive_id']);
				$user_info   = model('User')->get($list[0]['send_id']);
			}else{
				$doctor_info = model('DoctorInfo')->get($list[0]['send_id']);
				$user_info   = model('User')->get($list[0]['receive_id']);
			}

			foreach ($list as $v) {
				if ($v['send_type'] == '2') {
					$v['head_url'] 	= $doctor_info->doctor_logo;
					$v['name']		= $doctor_info->doctor_name;
				}else{
					$v['head_url'] 	= $user_info['head_url'];
					$v['name']		= $user_info['nick_name'];
				}

				$res[]	= $v;
			}

			$data_total = self::where('status', '0')
					    ->where('order_id', input('get.id'))
					    ->count();


			return return_true($res, '', $data_total);
		} catch (WinException $e) {
			return $e->false();
		}
	}
}