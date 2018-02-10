<?php
namespace app\user\model;

use think\Model;
use winleung\exception\WinException;
use \app\base\model\WechatConfig;
use think\Log;
use think\Db;

class DoctorQuestionOrder extends Common
{
	private $wechatObj;

	public function initialize()
	{
		parent::initialize();
		$obj = new WechatConfig;
		$this->wechatObj = $obj->newApi();
	}

	public function create_order()
	{
		try {
            trace('--用户生成提问订单开始--','info');
			$info = self::where('user_id', $this->user_info['id'])
						->where('doctor_id', input('get.doctor_id'))
						->order('update_time desc')
						->find();
			// 生成订单号
			$order_no = 'q'. $this->user_info['id'] .'d'. input('get.doctor_id') .'_'. date('YmdHis'); 
			if ($info) {
				// 生成过, 再次判断订单是否已经完成或过期
				if (($info->create_time < (time() - 86400 * 2))) { // 判断最后一次订单是否过期了
					if ($info->is_control == '0') { // 医生无操作过而过期了
						if ($info->is_time_over == '0') {
							$info->is_time_over = '1';// 将上次的未修改订单状态修改为结束状态
							if (!$info->save()) win_exception('修改前次订单状态失败');
						}

					}

				}else{
					if ($info->is_time_over == '0') {
						win_exception('当前存在着进行中的问答订单, 如需问答无须再新建订单', __LINE__);
					}
				}
			}
			self::startTrans();

			$sym_info = model('UserSymptomatography')->where('user_id', $this->user_info['id'])
													 ->where('order_id', '0')
													 ->order('create_time desc')
													 ->find();

			if (!$sym_info) {
                trace('未提交用户症状信息, 不可生成提问订单','error');
			    win_exception('未提交用户症状信息, 不可生成提问订单', __LINE__);
            }

			$return_data = self::create_advance($order_no);

			if (!$return_data) { // 生成预订单
                trace('生成订单失败','error');
				win_exception('生成订单失败', __LINE__);
			}else{

				$config = [
				    'out_trade_no' 	=> $return_data->order_no,
				    'body' 			=> '专家id'. $return_data->doctor_id .'-提问订单:'.$return_data->order_no,
				    'total_fee' 	=> $return_data->pay_total * 100,
				    'notify_url' 	=> config('wechat.WxReturnApi'),
				];
			}

			$sym_info->order_id = $return_data->id;

			if (!$sym_info->save()) win_exception('问题记录订单id失败', __LINE__);

			if ($return_data->pay_total == '0') {
				self::commit();
				return return_no_data();
			}else{
				if (!$config) win_exception('配置数组丢失', __LINE__);
				// halt($this->user_info['open_id']);
				$wxOrder = $this->wechatObj->wxPayUnifiedOrder($this->user_info['open_id'], $config);
				// 判断预订单是否生成成功
                trace('--提问订单微信支付接口返回结果--: '. json_encode($wxOrder),'info');
				if ($wxOrder['return_code'] != 'SUCCESS') {
				    win_exception('生成预订单失败,返回码: '. $wxOrder['return_code'], __LINE__);
				}

				// 生成微信支付JSAPI参数
				if (! array_key_exists('prepay_id', $wxOrder)) {
                    trace('--提问订单微信支付调起失败--: '. json_encode($wxOrder),'error');
				     win_exception('生成预订单失败,返回码: '. $wxOrder['err_code_des'], __LINE__);
				}
				$jsApiParams = $this->wechatObj->getWxPayJsApiParameters($wxOrder['prepay_id']);

				self::commit();
                trace('--提问订单微信支付处理后返回前端结果--: '. json_encode($jsApiParams),'info');
                return return_true(json_decode($jsApiParams));
			}

			self::commit();
            trace('--用户生成提问订单--: '. json_encode($return_data),'info');
			return return_true($return_data);
		} catch (WinException $e) { 
			self::rollback();
			return $e->false();
		}
	}

	// 生成预订单
	public function create_advance($order_no)
	{
		$doctor_info = model('DoctorInfo')->get(input('get.doctor_id'));

		$data = [
			'order_no'  => $order_no,
			'doctor_id' => input('get.doctor_id'),
			'user_id'	=> $this->user_info['id'],
			'pay_total' => $doctor_info->question_price,
		];

		if ($doctor_info->question_price == '0') {
			$data['pay_status'] = 1;
		}

		if (!self::allowField(true)->save($data)) return false;

		return self::get(['order_no' => $order_no]);
	}

	public function check_doctor_order_exist($doctor_id = '', $control = '1')
	{
		if (input('get.doctor_id')) $doctor_id = input('get.doctor_id');

		$time = time() - 86400 * 2; // 设置过期时间

		$info = self::where('user_id', $this->user_info['id'])
					->where('doctor_id', $doctor_id)
					->where('create_time', '>', $time)
					->where('is_time_over', '0')
					->where('pay_status', '1')
					->order('update_time desc')
					->find();
		if ($control == '2') {
			return $info;
		}else{
			if ($info) return true;

			return false;
		}
	}

	public function check_user_order_exist($user_id = '', $control = '1')
	{
		if (input('get.user_id')) $user_id = input('get.user_id');

		$time = time() - 86400 * 2; // 设置过期时间

		$info = self::where('doctor_id', $this->user_info['id'])
					->where('user_id', $user_id)
					->where('create_time', '>', $time)
					->where('is_time_over', '0')
					->where('pay_status', '1')
					->order('update_time desc')
					->find();
		if ($control == '2') {
			return $info;
		}else{
			if ($info) return true;

			return false;
		}
	}

	public function cut_order()
	{
		try {
			$time = time() - 86400 * 2; // 设置过期时间
			
			$info = self::where('doctor_id', $this->user_info['id'])
						->where('user_id', input('get.user_id'))
						->where('create_time', '>', $time)
						->where('is_time_over', '0')
						->order('update_time desc')
						->find();

			if (!$info) win_exception('', __LINE__);
			$info->is_time_over = '2';
			if (!$info->save()) win_exception('操作失败',__LINE__);

			return return_no_data();
		} catch (WinException $e) {
			return $e->false();
		}
	}

	public function order_callback()
	{
		try {
			list($res, $notifyData, $replyData) = $this->wechatObj->progressWxPayNotify();

			if (!$res) {
				$log = [
					'msg'	=> '微信支付回调失败',
					'callback-data' => json_encode($res),
					'time'	=> date('Y-m-d H:i:s'),
				];

				Log::write(json_encode($log));
				win_exception('', __LINE__);
			}

			$save = [
				'thrid_order_no' 	=> $notifyData['transaction_id'],
				'pay_status'		=> 1,
				'pay_total'			=> $notifyData['cash_fee']/100,
			];
			
			if (!self::save($save, ['order_no' => $notifyData['out_trade_no']])) {
				$log = [
					'msg'	=> '修改订单信息失败',
					'order_no' => $notifyData['out_trade_no'],
					'time'	=> date('Y-m-d H:i:s'),
				];
			
				Log::write(json_encode($log));
				win_exception('', __LINE__);
			}else{
				$this->wechatObj->replyWxPayNotify($replyData);
			}
		} catch (WinException $e) {
			return $e->false();
		}
	}

	// 获取医生的收入流水
	public function doctor_income_account()
	{
		try {
			$doctor_id = $this->user_info['id'];
			$income_data = Db::query("SELECT
							SUM(pay_total) as income,
							FROM_UNIXTIME(create_time, '%Y-%m') AS date
						FROM
							month_doctor_question_order
						WHERE
							doctor_id={$doctor_id}
						GROUP BY
							FROM_UNIXTIME(create_time, '%Y-%m')
						ORDER BY create_time DESC");
			if (!$income_data) win_exception('', __LINE__);
			return return_true($income_data);
		} catch (WinException $e) {
			return $e->false();
		}
	}
}