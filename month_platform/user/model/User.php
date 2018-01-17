<?php
namespace app\user\model;

use think\Model;
use winleung\exception\WinException;

class User extends Common
{
	protected $user_validate;

	public function UserDetailInfo()
	{
		return $this->hasMany('UserDetailInfo', 'user_id')
					->field('weight,height,user_id,last_login_time');
	}

	public function setBirthdayAttr($value)
	{
		return strtotime($value);
	}

	public function getBirthdayAttr($value)
	{
	    if ($value){
            return date('Y-m-d', $value);
        }
        else{
	        return null;
        }
	}

	public function initialize()
	{
		parent::initialize();
		$this->user_validate = validate('User');
	}

	public function setPasswordAttr($value)
	{
		return password_hash($value, PASSWORD_DEFAULT);
	}

	public function scopeOpen_id($sql)
	{
		$sql->where('open_id', input('get.open_id'));
	}

	// 其他浏览器登录
	public function user_login()
	{
		try {

			if (!$this->user_validate->scene('update_phone')->check(input('post.'))) win_exception($this->user_validate->getError(), __LINE__);

			$info = self::get(['phone' => input('post.phone'), 'status' => '1']);

			if (!$info) win_exception('用户不存在或被禁用登录', __LINE__);

			if (!password_verify(input('post.password'), $info->password)) win_exception('密码错误', __LINE__);

			$info = $info->toArray();

			$kb_code = substr(password_hash((string)time(), PASSWORD_DEFAULT), 0, 42);

			if (!$this->redis->setex($kb_code, 7200, json_encode($info))) win_exception('记录用户信息失败', __LINE__);

			model('UserDetailInfo')->save(['last_login_time' => time()], ['user_id' => $info['id']]);

			return return_true($info, $kb_code);
		} catch (WinException $e) {
			return $e->false();
		}
	}

	// 根据open_id登录
	public function wechat_login()
	{
		try {
			$info = self::open_id()
						->where('status', '1')
						->find();

			if (!$info) win_exception('用户不存在或被禁用登录', __LINE__);

			$kb_code = substr(password_hash((string)time(), PASSWORD_DEFAULT), 0, 42);

			$info = $info->toArray();

			if (!$this->redis->setex($kb_code, 7200, json_encode($info))) win_exception('记录用户信息失败', __LINE__);

			model('UserDetailInfo')->save(['last_login_time' => time()], ['user_id' => $info['id']]);

			return return_true($info, $kb_code);
		} catch (WinException $e) {
			return $e->false();
		}
	}

	public function sign_in()
	{
		try {
			self::startTrans();
			if (!$this->user_validate->scene('sign_in')->check(input('post.'))) win_exception($this->user_validate->getError(), __LINE__);

			if (self::get(['open_id' => input('post.open_id')])) win_exception('此open_id已注册', __LINE__);

			if (!self::allowField(['open_id', 'nick_name', 'head_url', 'sex', 'create_time', 'update_time'])->save(input('post.'))) win_exception('创建数据失败', __LINE__);

			$info = self::get($this->id)->toArray();

			$kb_code = substr(password_hash((string)time(), PASSWORD_DEFAULT), 0, 42);

			if (!$this->redis->setex($kb_code, 7200, json_encode($info))) win_exception('记录用户信息失败', __LINE__);

			if (!model('UserDetailInfo')->save(['last_login_time' => time(), 'user_id' => $info['id']])) win_exception('创建用户详细数据失败', __LINE__);
			if (!model('UserReadyPregnancyInfo')->save(['user_id' => $info['id']])) win_exception('创建用户妊娠数据失败', __LINE__);
			if (!model('UserPregnancyInfo')->save(['user_id' => $info['id']])) win_exception('创建用户妊娠数据失败', __LINE__);
			if (!model('UserAfterPregnancyInfo')->save(['user_id' => $info['id']])) win_exception('创建用户妊娠数据失败', __LINE__);
			self::commit();
			return return_true($info, $kb_code);
		} catch (WinException $e) {
			self::rollback();
			return $e->false();
		}
	}

	public function user_complete()
	{
		try {

			if (!$this->user_validate->scene('update_phone')->check(input('post.'))) win_exception($this->user_validate->getError(), __LINE__);

			if (self::get(['phone' => input('post.phone')])) win_exception('手机已注册', __LINE__);

			if (!self::allowField(['phone', 'password', 'update_time'])->save(input('post.'), ['id' => $this->user_info['id']])) win_exception('更新数据失败', __LINE__);

			return return_no_data();
		} catch (WinException $e) {
			return $e->false();
		}
	}

	// 手机注册
	public function phone_sign_in()
	{
		try {

			if (!$this->user_validate->scene('update_phone')->check(input('post.'))) win_exception($this->user_validate->getError(), __LINE__);
			// 1. 获取手机号资料, 主要判断是否已经注册
			$exist = self::get(['phone' => input('post.phone')]);

			if (input('post.phone') == $this->user_info['phone']) win_exception('不可与当前绑定手机号相同', __LINE__);
			
			// 2. 手机号注册过同时存在open_id
			if ($exist && $this->user_info['open_id']) { // 手机已注册, 捆绑手机和微信资料
				if (!$result = self::bind_phone_data(input('post.'), 'bind_phone')) {
					win_exception('捆绑手机号失败',__LINE__);
				}

				return return_true($result['data'], $result['kb_code']);
			}elseif((!$exist && $this->user_info['open_id']) || (!$exist && $this->user_info)){ // 手机没注册, 添加手机到微信资料
				if (!$result = self::bind_phone_data(input('post.'), 'bind_wechat')) {
					win_exception('捆绑手机号失败',__LINE__);
				}

				return return_true($result['data'], $result['kb_code']);
			}elseif ($exist && !$this->user_info){
                win_exception('手机号码已注册', __LINE__);
            }

			self::startTrans();

			if (!self::allowField(['phone', 'password', 'create_time', 'update_time'])->save(input('post.'))) win_exception('创建数据失败', __LINE__);

			$info = self::get($this->id)->toArray();

			$kb_code = substr(password_hash((string)time(), PASSWORD_DEFAULT), 0, 42);

			if (!$this->redis->setex($kb_code, 7200, json_encode($info))) win_exception('记录用户信息失败', __LINE__);

			if (!model('UserDetailInfo')->save(['last_login_time' => time(), 'user_id' => $info['id']])) win_exception('创建用户详细数据失败', __LINE__);
			if (!model('UserReadyPregnancyInfo')->save(['user_id' => $info['id']])) win_exception('创建用户妊娠数据失败', __LINE__);
			if (!model('UserPregnancyInfo')->save(['user_id' => $info['id']])) win_exception('创建用户妊娠数据失败', __LINE__);
			if (!model('UserAfterPregnancyInfo')->save(['user_id' => $info['id']])) win_exception('创建用户妊娠数据失败', __LINE__);

			self::commit();
			return return_true($info, $kb_code);
		} catch (WinException $e) {
			self::rollback();
			return $e->false();
		}
	}

	// 手机重置密码
	public function reset_password_by_phone()
	{
		try {

			if (!$this->user_validate->scene('update_phone')->check(input('post.'))) win_exception($this->user_validate->getError(), __LINE__);

			if (!self::get(['phone' => input('post.phone')])) win_exception('手机号未注册', __LINE__);

			if (!self::allowField(['password', 'update_time'])->save(['password' => input('post.password')], ['phone' => input('post.phone')])) win_exception('更新数据失败', __LINE__);

			return return_no_data();
		} catch (WinException $e) {
			return $e->false();
		}
	}

	public function info_update()
	{
		try {
			if (!$this->user_validate->scene('update')->check(input('post.'))) win_exception($this->user_validate->getError(), __LINE__);

			if (!self::allowField(['nick_name', 'head_url', 'sex', 'real_name', 'update_time', 'birthday', 'pregnancy_status'])->save(input('post.'), ['id' => $this->user_info['id']])) win_exception('更新数据失败', __LINE__);

			return return_no_data();
		} catch (WinException $e) {
			return $e->false();
		}
	}

	public function user_info()
	{
		try {
			$info = self::with('UserDetailInfo')
						->where('id', $this->user_info['id'])
						->find();
			if (!$info) win_exception('', __LINE__);

			return return_true($info);
		} catch (WinException $e) {
			return $e->false();
		}
	}

	public function bind_phone_data($data = '', $control = '')
	{
		if ($control == 'bind_phone') { // 注册过, 合并资料, 手机号优先
			$phone_info 			 = self::get(['phone' => $data['phone']]);
			$phone_info->nick_name   = $this->user_info['nick_name'];
			$phone_info->open_id	 = $this->user_info['open_id'];
			$phone_info->head_url  	 = $this->user_info['head_url'];
			$phone_info->city        = $this->user_info['city'];
			$phone_info->sex         = $this->user_info['sex'];
			$phone_info->password    = $data['password'];
			if (!$phone_info->save()) return false;
			if ($this->user_info['open_id']) {
				self::save(['id' => $this->user_info['id']], ['status' => '-1']);
			}

		}elseif($control == 'bind_wechat'){ // 没注册过, 捆绑手机号.
			$phone_info = self::get($this->user_info['id']);

			$phone_info->phone   	 = $data['phone'];
			$phone_info->password    = $data['password'];

			if (!$phone_info->save()) return false;
		}
		$phone_info = $phone_info->toArray();

		$kb_code = substr(password_hash((string)time(), PASSWORD_DEFAULT), 0, 42);

		if (!$this->redis->setex($kb_code, 7200, json_encode($phone_info))) return false;

		return ['data' => $phone_info, 'kb_code' => $kb_code];
	}
}
