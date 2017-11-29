<?php
namespace app\doctor\model;

use think\Model;
use think\Request;
use winleung\exception\WinException;
use WinleungWechat\WechatPhpSdk\SmsSingleSender;

class Common extends Model
{
	protected $resultSetType = 'collection';
	protected $redis;
	protected $user_info;
	protected $tx_sms;// 腾讯云短信api

	public function initialize()
	{
		parent::initialize();
		$this->redis = new \Redis;
		$this->redis->connect('127.0.0.1', 6379);
		$this->user_info = json_decode($this->redis->get(input('get.KB_CODE')), true);
		$this->tx_sms   = new SmsSingleSender(SMS_APPID, SMS_APPKEY);
	}

	public function scopeStatus($sql)
	{
		$sql->where('status', '<>', '-1');
	}

	public function scopeId($sql)
	{
		$sql->where('id', input('get.id'));
	}

	public function setTagListAttr($value)
	{
		return json_encode($value);
	}

	public function getTagListAttr($value)
	{
		return json_decode($value, true);
	}

	public function scopeClassify_id($sql)
	{
		if (input('get.classify_id')) {
			$sql->where('classify_id', input('get.classify_id'));
		}
	}

	public function async_upload_file()
	{
		$file_field = input('get.file_field');

		if (!empty(input('file.'.$file_field))) {
			switch (input('get.upload_type')) {
				case '1':
					$path = 'Article';// 文章
					break;
				case '2':
					$path = 'Banner';// 广告
					break;
				case '3':
					$path = 'Doctor';// 医生
					break;
				case '4':
					$path = 'User';// 用户
					break;
				case '5':
					$path = 'Organization';// 机构
					break;
				case '6':
					$path = 'QuestionArticle';// 知识
					break;
				default:
					return return_false(__LINE__, '未识别上传类型');
					break;
			}
			$res = imgUpload($file_field, $path, input('get.upload_total'));
			switch (input('get.upload_total')) {
				case '1':
					$data = ['path' => IMG_API.returnThumbnail($res)];
					break;
				case '2':
				foreach ($res as $v) {
					if (isset($v['error']) && $v['error'] == false) {
						return return_false(10, '上传失败');
					}
					$data['path'][]= IMG_API.returnThumbnail($v);
				}
					break;
			}
			return return_true($data);
		}
	}

	public function send_code()
	{
		$verify_code = mt_rand(1111,9999);

		$phone = input('get.phone');

		try {
			if(!$phone) win_exception('请输入手机号码', __LINE__);

			$status = $this->redis->get('verify_code_'.$phone.'_status');
			if ($status) {
				win_exception('60秒内发送过验证码,请稍后再试', __LINE__);
			}else{
				$this->redis->setex('verify_code_'.$phone, 300, $verify_code);
				$this->redis->setex('verify_code_'.$phone.'_status', 60, 1);
			}

			$params = [$verify_code, 300, 300];//模版参数

		    // 模版单发单发 
		    $result = $this->tx_sms->sendWithParam("86", $phone, TX_DX_TEMID, $params);

		    $rsp = json_decode($result);

		    if ($rsp->errmsg != 'OK') win_exception('短信错误代号:'.$rsp->result, __LINE__);

			return return_no_data();
		} catch (WinException $e) {
			return $e->false();
		}
	}

	public function verify_code_match()
	{
		try {
			$code = $this->redis->get('verify_code_'.input('get.phone'));

			if ($code != input('get.code')) win_exception('验证码有误', __LINE__);

			return return_no_data();
		} catch (WinException $e) {
			return $e->false();
		}
	}
}