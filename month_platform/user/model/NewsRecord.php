<?php
namespace app\user\model;

use think\Model;
use winleung\exception\WinException;
use think\Db;

class NewsRecord extends Common
{
	// 获取聊天记录
	public function get_history_news_by_user()
	{
		try {
			$list = self::where('status', '0')
					    ->where(function($sql) {
					    	$sql->where('send_id', $this->user_info['id'])
						    	->where('send_type', '1')
						    	->where('receive_id', input('get.uid'))
					    		->where('receive_type', '2');
					    })
					    ->whereOr(function($sql) {
					    	$sql->where('receive_id', $this->user_info['id'])
					    		->where('receive_type', '1')
						    	->where('send_id', input('get.uid'))
						    	->where('send_type', '2');
					    })
					    ->order('create_time desc')
					    ->paginate(8)
					    ->hidden(['update_time'])
					    ->toArray();

			if (!$list) win_exception('', __LINE__);

			$doctor_info = model('DoctorInfo')->get(input('get.uid'));

			foreach ($list as $v) {
				if ($v['send_type'] == '2') {
					$v['head_url'] 	= $doctor_info->doctor_logo;
					$v['name']		= $doctor_info->doctor_name;
				}else{
					$v['head_url'] 	= $this->user_info['head_url'];
					$v['name']		= $this->user_info['nick_name'];
				}

				$res[]	= $v;
			}

			return return_true($res);
		} catch (WinException $e) {
			return $e->false();
		}
	}

	public function get_history_news_by_doctor()
	{
		try {
			$list = self::where('status', '0')
					     ->where(function($sql) {
					    	$sql->where('send_id', $this->user_info['id'])
						    	->where('send_type', '2')
						    	->where('receive_id', input('get.uid'))
					    		->where('receive_type', '1');
					    })
					    ->whereOr(function($sql) {
					    	$sql->where('receive_id', $this->user_info['id'])
					    		->where('receive_type', '2')
					    		->where('send_id', input('get.uid'))
						    	->where('send_type', '1');
					    })
					    ->order('create_time desc')
					    ->paginate(8)
					    ->hidden(['update_time'])
					    ->toArray();
			if (!$list) win_exception('', __LINE__);

			$user_info = model('User')->get(input('get.uid'));
			// halt($this->user_info);
			foreach ($list as $v) {
				if ($v['send_type'] == '2') {
					$v['head_url'] 	= $this->user_info['doctor_logo'];
					$v['name']		= $this->user_info['doctor_name'];
				}else{
					$v['head_url'] 	= $user_info->head_url;
					$v['name']		= $user_info->nick_name;
				}

				$res[]	= $v;
			}

			return return_true($res);
		} catch (WinException $e) {
			return $e->false();
		}
	}

	public function set_news_is_read_by_user()
	{
		try {
			$info = self::where('status', '0')
					    ->where(function($sql) {
					    	$sql->where('receive_id', $this->user_info['id'])->where('receive_type', '1');
					    })
					    ->where(function($sql) {
					    	$sql->where('send_id', input('get.uid'))->where('send_type', '2');
					    })
					    ->update(['is_read' => '2']);

			if (!$info) {
				win_exception('操作失败', __LINE__);
			}

			return return_no_data();
		} catch (WinException $e) {
			return $e->false();
		}
	}

	public function set_news_is_read_by_doctor()
	{
		try {
			$info = self::where('status', '0')
					    ->where(function($sql) {
					    	$sql->where('receive_id', $this->user_info['id'])->where('receive_type', '2');
					    })
					    ->where(function($sql) {
					    	$sql->where('send_id', input('get.uid'))->where('send_type', '1');
					    })
					    ->update(['is_read' => '2']);

			if (!$info) {
				win_exception('操作失败', __LINE__);
			}

			return return_no_data();
		} catch (WinException $e) {
			return $e->false();
		}
	}

	public function DoctorInfo()
	{
		return $this->hasMany('DoctorInfo', 'id', 'send_id');

	}

	public function DoctorQuestionOrder()
	{
		return $this->hasMany('DoctorQuestionOrder', 'id', 'order_id')
					->field('create_time,is_time_over,id');
	}

	public function user_question_history()
	{
		try {
			/*$list = self::field(['max(id)' => 'id_list'])
						->where('status', 'in', '0,1')
					    ->where('receive_id', $this->user_info['id'])
				    	->where('receive_type', '1')
					    ->where('send_type', '2')
					    ->group('send_id')
					    ->order('create_time desc')
					    ->select()
					    ->toArray();
			
			if(!$list) win_exception('', __LINE__);

			foreach ($list as $v) {
				$id_list[] = $v['id_list'];
			}

			$id_list = implode(',', $id_list);*/

			/*$result = self::where('id', 'in', $id_list)
						  ->with('DoctorInfo,DoctorQuestionOrder')
						  ->order('field(`id`,'. $id_list .')')
						  ->paginate(10)
						  ->hidden(['status'])
						  ->toArray();*/

			$page = input('get.page')?input('get.page'):1;

			if ($page == 1) {
				$num = 0;
			}else{
				$num = ($page-1) * 10;
			}

			$sql = "SELECT
					o.create_time AS order_create_time,
					o.is_time_over,
					d.doctor_name,
					d.doctor_logo,
					d.id as doctor_id,
					n.id as news_id, 
					n.send_id as news_send_id, 
					n.send_type as news_send_type, 
					n.receive_id as news_receive_id, 
					n.receive_type as news_receive_type, 
					n.create_time as news_create_time, 
					n.status as news_status, 
					n.news_content,
					n.news_type,
					n.is_read,
					u.nick_name as user_nick_name,
					u.head_url as user_head_url,
					u.id as user_id
					FROM
						month_doctor_question_order o
					LEFT JOIN month_doctor_info d ON o.doctor_id = d.id
					LEFT JOIN (select * from month_news_record order by create_time desc) n on n.order_id=o.id
					LEFT JOIN month_user u ON u.id = o.user_id 
					WHERE
						o.user_id = {$this->user_info['id']}
					AND o.pay_status = 1
					GROUP BY
						o.id
					ORDER BY
						o.create_time DESC
					LIMIT {$num},10";



			$result = Db::query($sql);

			return return_true($result);
		} catch (WinException $e) {
			return $e->false();
		}
	}

	public function User()
	{
		return $this->hasMany('User', 'id', 'send_id');
	}

	public function doctor_question_history()
	{
		try {
			/*$list = self::field(['max(id)' => 'id_list'])
						->where('status', '0')
					    ->where('receive_id', $this->user_info['id'])
				    	->where('receive_type', '2')
					    ->where('send_type', '1')
					    ->group('send_id')
					    ->order('create_time desc')
					    ->select()
					    ->toArray();
			
			if(!$list) win_exception('', __LINE__);

			foreach ($list as $v) {
				$id_list[] = $v['id_list'];
			}

			$id_list = implode(',', $id_list);

			$result = self::where('id', 'in', $id_list)
						  ->with('User,DoctorQuestionOrder')
						  ->order('field(`id`,'. $id_list .')')
						  ->paginate(10)
						  ->hidden(['status'])
						  ->toArray();*/
			$page = input('get.page')?input('get.page'):1;

			if ($page == 1) {
				$num = 0;
			}else{
				$num = ($page-1) * 10;
			}

			$sql = "SELECT
					o.create_time AS order_create_time,
					o.is_time_over,
					d.doctor_name,
					d.doctor_logo,
					d.id as doctor_id,
					n.id as news_id, 
					n.send_id as news_send_id, 
					n.send_type as news_send_type, 
					n.receive_id as news_receive_id, 
					n.receive_type as news_receive_type, 
					n.create_time as news_create_time, 
					n.status as news_status, 
					n.news_content,
					n.news_type,
					n.is_read,
					u.nick_name as user_nick_name,
					u.head_url as user_head_url,
					u.id as user_id
					FROM
						month_doctor_question_order o
					LEFT JOIN month_doctor_info d ON o.doctor_id = d.id
					LEFT JOIN (select * from month_news_record order by create_time desc) n on n.order_id=o.id
					LEFT JOIN month_user u ON u.id = o.user_id 
					WHERE
						o.doctor_id = {$this->user_info['id']}
					AND o.pay_status = 1
					GROUP BY
						o.id
					ORDER BY
						o.create_time DESC
					LIMIT {$num},10";

			$result = Db::query($sql);

			return return_true($result);
		} catch (WinException $e) {
			return $e->false();
		}
	}
}