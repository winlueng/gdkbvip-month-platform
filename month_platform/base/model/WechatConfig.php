<?php
namespace app\base\model;

use think\Model;
use WinleungWechat\WechatPhpSdk\Api;
use think\Loader;

class WechatConfig extends Model
{
	public    $redis;
	private   $config;

	public function initialize()
	{
		parent::initialize();
		$this->redis = new \Redis;
		$this->redis->connect('127.0.0.1', 6379);
		Loader::import('WinleungWechat.WechatPhpSdk.Api', EXTEND_PATH);
	}

	public function setConfig()
	{
		$redis = $this->redis;
		$this->config = [
			'appId' 			=> config('wechat.AppID'),
			'appSecret' 		=> config('wechat.AppSecret'),
			'mchId'				=> config('wechat.MchId'),
			'key'				=> config('wechat.AppKey'),
			'get_access_token'  => function ($appId) use ($redis){
				return $redis->get('B_AccessToken_'.$appId);
			},
			'save_access_token' => function ($appId, $token) use ($redis){
				return $redis->setex('B_AccessToken_'.$appId, 7150, $token);
			} ,
			'get_jsapi_ticket'  => function ($appId) use ($redis){
				return $redis->get('B_JsApiTicket_'.$appId);
			},
			'save_jsapi_ticket' => function ($appId, $ticket) use ($redis) {
				return $redis->setex('B_JsApiTicket_'.$appId, 7150, $ticket);
			},
			'get_api_ticket'  => function ($appId) use ($redis){
				return $redis->get('B_ApiTicket_'.$appId);
			},
			'save_api_ticket' => function ($appId, $ticket) use ($redis) {
				return $redis->setex('B_ApiTicket_'.$appId, 7150, $ticket);
			},
		];

		return $this;
	}

	public function newApi()
	{
		self::setConfig();

		return new /*\WinleungWechat\WechatPhpSdk\*/Api($this->config);
	}
}