<?php
namespace app\doctor\model;

use think\Model;
use winleung\exception\WinException;

class DoctorInfo extends Common
{
	private $XS;
	private $doc;
	private $XSindex;
	public function initialize()
	{
		parent::initialize();
		require_once ('../vendor/XSsdk/php/lib/XS.php');
		$this->XS  		= new \XS('doctor');
		$this->doc 		= new \XSDocument();
		$this->XSindex	= $this->XS->index;
	}

	public function setPasswordAttr($value)
	{
		return password_hash($value, PASSWORD_DEFAULT);
	}

	public function scopeStatus($sql)
	{
		$sql->where('status', '<>', '-1');
	}

	public function doctor_sign_in()
	{
		try {
			$validate = validate('DoctorInfo');

			if (!$validate->scene('sign_in')->check(input('post.'))) win_exception($validate->getError(), __LINE__);

			if (self::get(['phone' => input('post.phone')])) {
				win_exception('手机已注册', __LINE__);
			}

			if (!self::allowField(true)->save(input('post.'))) win_exception('注册失败', __LINE__);

			$info = self::get(['phone' => input('post.phone')]);

			/*$news_data = [
					'title' 		 => '审核消息-'. $info->doctor_name .'专家的个人注册审核',
					'news_content'   => '是否同意'. $info->doctor_name .'专家的个人注册审核',
					'news_type' 	 => '1',
					'relevance_id'   => $info->id,
					'relevance_type' => '2',
			];

			if (!model('SystemNews')->save($news_data)) win_exception('系统消息写入失败', __LINE__);*/

			$data = [
				'id' 		   	=> $info->id,
				'skilled' 		=> $info->skilled,
				'doctor_name' 	=> $info->doctor_name,
				'status'	   	=> $info->status,
				'doctor_logo'	=> $info->doctor_logo,
			];

			$this->doc->setFields($data);
			$this->XSindex->add($this->doc);

			$kb_code = substr(password_hash((string)time(), PASSWORD_DEFAULT), 0, 41);

			if (!$this->redis->setex($kb_code, 7200, json_encode($info->toArray()))) win_exception('记录用户信息失败', 125);

			return return_true($info, $kb_code);
		} catch (WinException $e) {
			return $e->false();
		}
	}

	public function doctor_login()
	{
		try {
			$validate = validate('DoctorInfo');

			if (!$validate->scene('sign_in')->check(input('post.'))) win_exception($validate->getError(), __LINE__);

			if ($info = self::get(['phone' => input('post.phone')/*, 'status' => '1'*/]));

			else win_exception('医师帐号不存在或被禁用');

			if (!password_verify(input('post.password'), $info->password)) win_exception('密码错误', 122);

			$kb_code = substr(password_hash((string)time(), PASSWORD_DEFAULT), 0, 41);

			$info = $info->hidden(['password'])->toArray();

			if (!$this->redis->setex($kb_code, 7200, json_encode($info))) win_exception('记录用户信息失败', 125);

			return return_true($info,$kb_code);
		} catch (WinException $e) {
			return $e->false();	
		}
	}

	public function doctor_info()
	{
		try {
			$info = self::status()
						->where('id', $this->user_info['id'])
						->find();

			if (!$info) win_exception('', __LINE__);

			return return_true($info->hidden(['password', 'organization_id'])->toArray());
		} catch (WinException $e) {
			return $e->false();
		}
	}

	public function info_update()
	{
		try {
			$validate = validate('DoctorInfo');

			if (!$validate->scene('update')->check(input('post.'))) win_exception($validate->getError(), __LINE__);

			$data = input('post.');
			$data['status'] = 0;
			self::startTrans();
			if (!self::allowField(['doctor_name', 'doctor_logo', 'sex', 'tag_classify_id', 'organization_name', 'organization_tel', 'job_title', 'tag_list', 'update_time', 'status', 'departments_id', 'question_price'])->save($data, ['id' => $this->user_info['id']])) {
				win_exception('完善资料失败', __LINE__);
			}

			$info = self::get($this->user_info['id']);

			$news_data = [
					'title' 	     => '审核消息-'. $info->doctor_name .'专家发起个人资料审核',
					'news_content'   => '是否同意'. $info->doctor_name .'专家的个人资料审核',
					'news_type'      => '1',
					'relevance_id'   => $this->user_info['id'],
					'relevance_type' => '2',
			];

			if (!model('SystemNews')->save($news_data)) win_exception('系统消息写入失败', __LINE__);

			$data = [
				'id' 		   	=> $info->id,
				'skilled' 		=> $info->skilled,
				'doctor_name' 	=> $info->doctor_name,
				'status'	   	=> $info->status,
				'doctor_logo'	=> $info->doctor_logo,
			];

			$this->doc->setFields($data);
			$this->XSindex->update($this->doc);
			self::commit();
			return return_no_data();
		} catch (WinException $e) {
			self::rollback();
			return $e->false();
		}
	}

	public function reset_password()
	{
		try {
			if (!self::save(['password' => input('get.password')], ['phone' => input('get.phone')])) win_exception('重置密码失败', __LINE__);

			return return_no_data();
		} catch (WinException $e) {
			return $e->false();
		}
	}

	public function other_update()
	{
		try {
			if (!self::allowField(['skilled', 'introduce'])->save(input('post.'), ['id' => $this->user_info['id']])) win_exception('修改资料失败', __LINE__);

			$info = self::get($this->user_info['id']);
			
			$data = [
				'id' 		   	=> $info->id,
				'skilled' 		=> $info->skilled,
				'doctor_name' 	=> $info->doctor_name,
				'status'	   	=> $info->status,
				'doctor_logo'	=> $info->doctor_logo,
			];

			$this->doc->setFields($data);
			$this->XSindex->update($this->doc);

			return return_no_data();
		} catch (WinException $e) {
			return $e->false();
		}
	}

	public function open_or_close()
	{
		try {
			$info = self::status()
						->where('id', $this->user_info['id'])
						->find();

			if (!$info) win_exception('', __LINE__);

			$info->is_open = input('get.is_open');

			if (!$info->save()) win_exception('操作失败', __LINE__);

			return return_no_data();
		} catch (WinException $e) {
			return $e->false();
		}
	}
}