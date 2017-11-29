<?php
namespace app\user\model;

require_once '../extend/GatewayClient/Gateway.php';

use think\Model;
use GatewayClient\Gateway;
use winleung\exception\WinException;

class Communication extends Common
{
	private $doctor_id;
	private $user_id;

	public function initialize()
	{
		parent::initialize();
		if (input('post.uid')) {
			$this->doctor_id = 'doctor_'. input('post.uid');
			$this->user_id   = 'user_'  . input('post.uid');
		}
	}

	public function bind_user_id()
	{
		try {
			Gateway::$registerAddress = '127.0.0.1:1238';
			if (!$client_id = $this->redis->get('user_client_id_'. $this->user_info['id'])) {// 没有绑定过才去绑定clientID
				if (!Gateway::bindUid(input('post.client_id'), 'user_'. $this->user_info['id'])) win_exception('绑定失败', __LINE__);
				$this->redis->set('user_client_id_'. $this->user_info['id'], input('post.client_id'));
				$client_id = input('post.client_id');
			}
			
			self::get_offline_msg_by_user();// 提取离线消息

			return return_true($client_id);
		} catch (WinException $e) {
			return $e->false();
		}
	}

	public function bind_doctor_id()
	{
		try {
			Gateway::$registerAddress = '127.0.0.1:1238';
			if (!$client_id = $this->redis->get('doctor_client_id_'. $this->user_info['id'])) {// 没有绑定过才去绑定clientID
				if (!Gateway::bindUid(input('post.client_id'), 'doctor_'. $this->user_info['id'])) win_exception('绑定失败', __LINE__);
				$this->redis->set('doctor_client_id_'. $this->user_info['id'], input('post.client_id'));
				$client_id = input('post.client_id');
			}
			self::get_offline_msg_by_doctor();// 提取离线消息

			return return_true($client_id);
		} catch (WinException $e) {
			return $e->false();
		}
	}

	public function send_message_by_user()
	{
		try {
			Gateway::$registerAddress = '127.0.0.1:1238';

			$order_info = model('DoctorQuestionOrder')->check_doctor_order_exist(input('post.uid'), '2');

			if (!$order_info) win_exception('提问订单不存在, 请生成提问订单再进行消息操作', __LINE__);

			$data = self::send_type(
				'用户', 
				$this->user_info['head_url'], 
				'1', 
				input('post.uid'), 
				$this->user_info['nick_name'],
				$order_info->id
			);

			if (!$data) win_exception('接收数据类型不接受', __LINE__);

			if (!self::check_doctor_is_online($this->doctor_id)) {// 不在线, 默认未离线信息
				win_exception('因专家未上线, 已发送为离线信息.', __LINE__);
			}else{
				if (!$client_arr = Gateway::getClientIdByUid($this->doctor_id)) win_exception('当前用户已离线', __LINE__);

				Gateway::sendToUid($this->doctor_id, json_encode($data));
			}
			
			return return_no_data();
		} catch (WinException $e) {
			return $e->false();
		}
	}

	public function send_message_by_doctor()
	{
		try {
			Gateway::$registerAddress = '127.0.0.1:1238';

			$order_info = model('DoctorQuestionOrder')->check_user_order_exist(input('post.uid'), '2');

			if (!$order_info) win_exception('提问订单已超时或不存在, 请确认用户是否有提交提问订单', __LINE__);

			$data = self::send_type(// 编制信息格式,并记录到消息记录(默认离线信息)
				'医生', 
				$this->user_info['doctor_logo'],
				'2', 
				input('post.uid'), 
				$this->user_info['doctor_name'],
				$order_info->id
			);

			if (!$data) win_exception('接收数据类型不接受', __LINE__);

			if (!self::check_user_is_online($this->user_id)) {// 不在线, 默认未离线信息
				win_exception('因用户未上线, 已发送为离线信息.', __LINE__);
			}else{
				if (!$client_arr = Gateway::getClientIdByUid($this->user_id)) win_exception(json_encode($client_arr), __LINE__);

				Gateway::sendToUid($this->user_id, json_encode($data));

				if ($order_info->is_control == '0') { // 修改订单状态为已操作
					$order_info->is_control = '1';
					$order_info->save();
				}
			}
			
			return return_no_data();
		} catch (WinException $e) {
			return $e->false();
		}
	}

	public function send_type($role, $head_url, $type, $receive_id, $name, $order_id)
	{
		$record = [
			'send_id' 		=> $this->user_info['id'],
			'send_type' 	=> $type,
			'receive_id' 	=> $receive_id,
			'receive_type'	=> ($type == '1'?'2':'1'),
		];
		if (input('post.msg')) {
			$data = [
				'type' 		=> 'talk',
				'name'		=> $name,
				'role'		=> $role,
				'news_content'		=> input('post.msg'),
				'send_id'	=> $this->user_info['id'],
				'head_url' 	=> $head_url,
			];
			$record['news_content'] = input('post.msg');
		}
		elseif(input('post.img')){
			$data = [
				'type' 		=> 'img',
				'name'		=> $name,
				'role'		=> $role,
				'news_content'		=> input('post.img'),
				'send_id'	=> $this->user_info['id'],
				'head_url' 	=> $head_url
			];

			$record['news_content'] = input('post.img');
			$record['news_type']	= 2;
		}
		else{
			return false;
		}
		$record['order_id'] = $order_id;
		// 记录聊天信息
		model('NewsRecord')->allowField(true)->save($record);
		return $data;
	}

	public function check_user_is_online($uid)
	{
		return Gateway::isUidOnline($this->user_id);
	}

	public function check_doctor_is_online($uid)
	{
		return Gateway::isUidOnline($this->doctor_id);
	}

	// 用户提取离线消息并发送
	public function get_offline_msg_by_user()
	{
		$list = model('NewsRecord')->all([
						'receive_id' => $this->user_info['id'],
						'receive_type' => '1',
						'is_read' => '1',
					])->toArray();

		if (!$list) {
			return 1;
		}

		foreach ($list as $v) {
			if (!isset($sender[$v['send_id']])) {
				$doctor_info = model('DoctorInfo')->find($v['send_id']);
				$sender[$v['send_id']]['doctor_name'] = $doctor_info->doctor_name;
				$sender[$v['send_id']]['doctor_logo'] = $doctor_info->doctor_logo;
			}
			$sender[$v['send_id']]['msg'][] = $v;
		}
		Gateway::$registerAddress = '127.0.0.1:1238';
		foreach ($sender as $k => $v) {
			$send_data = [
				'name'		=> $v['doctor_name'],
				'role'		=> '医生',
				'send_id'	=> $k,
				'head_url' 	=> $v['doctor_logo']
			];
			foreach ($v['msg'] as $value) {
				$send_data['news_content']  = $value['news_content'];
				if ($value['news_type'] == '2') {
					$send_data['type'] = 'img';
				}else{
					$send_data['type'] = 'talk';
				}
				Gateway::sendToUid('user_'. $this->user_info['id'], json_encode($send_data));
			}
		}

		return 2;

	}

	// 医生提取离线消息并发送
	public function get_offline_msg_by_doctor()
	{
		$list = model('NewsRecord')->all([
						'receive_id' => $this->user_info['id'],
						'receive_type' => '2',
						'is_read' => '1',
					])->toArray();

		if (!$list) {
			return 1;
		}

		foreach ($list as $v) {
			if (!isset($sender[$v['send_id']])) {
				$user_info = model('User')->get($v['send_id']);
				$sender[$v['send_id']]['nick_name'] = $user_info->nick_name?$user_info->nick_name:'';
				$sender[$v['send_id']]['head_url']  = $user_info->head_url?$user_info->head_url:'';
			}
			$sender[$v['send_id']]['msg'][] = $v;
		}
		Gateway::$registerAddress = '127.0.0.1:1238';
		foreach ($sender as $k => $v) {
			$send_data = [
				'name'		=> $v['nick_name'],
				'role'		=> '用户',
				'send_id'	=> $k,
				'head_url' 	=> $v['head_url']
			];
			foreach ($v['msg'] as $value) {
				$send_data['news_content']  = $value['news_content'];
				if ($value['news_type'] == '2') {
					$send_data['type'] = 'img';
				}else{
					$send_data['type'] = 'talk';
				}
				Gateway::sendToUid('doctor_'. $this->user_info['id'], json_encode($send_data));
			}
		}

		return 2;

	}

}