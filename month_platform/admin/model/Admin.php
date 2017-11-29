<?php
namespace app\admin\model;

use think\Model;
use winleung\exception\WinException;

class Admin extends Common
{
	public function setPasswordAttr($value)
	{
		return password_hash($value, PASSWORD_DEFAULT);
	}

	public function GroupAccess()
	{
		return $this->hasMany('GroupAccess', 'admin_id')
					->field('admin_id, group_id');
	}

	public function scopeStatus($sql)
	{
		$sql->where('status', '<>', '-1');
	}

	public function admin_add()
	{
		try {
			if(!input('get.group_id')) win_exception("请提交权限组id", 28);

			$this->startTrans();

			$validate = validate('Admin');

			if (!$validate->scene('add')->check(input('post.'))) win_exception($validate->getError(), 34);

			if(self::get(['admin_name' => input('post.admin_name')])) win_exception('用户名已存在', 36);

			if(self::get(['phone' => input('post.phone')])) win_exception('手机号已注册', 36);
			
			if (!self::allowField(true)->save(input('post.'))) win_exception("添加失败", 40);
			
			if (!model('GroupAccess')->save(['group_id' => input('get.group_id'), 'admin_id' => $this->id])) win_exception("添加失败", 42);
			$this->commit();
			return return_no_data();
		} catch (WinException $e) {
			$this->rollback();
			return $e->false();
		}
	}

	public function admin_list()
	{
		try {
			$list = self::status()
						->with('GroupAccess')
						->order('update_time desc')
						->paginate(10)
						->hidden(['sex', 'head_url', 'admin_id', 'password'])
						->toArray();

			if(!$list) win_exception('未查询到任何数据', 54);

			$data_total = self::status()->count();

			return return_true($list, '', $data_total);
		} catch (WinException $e) {
			return $e->false();
		}
	}

	public function admin_update()
	{
		try {
			$validate = validate('Admin');

			if (!$validate->scene('update')->check(input('post.'))) win_exception($validate->getError(), 74);
			
			if (!self::allowField(['admin_name','phone', 'email'])->save(input('post.'),['id' => input('get.id')])) win_exception("修改失败", 76);

			return return_no_data();
		} catch (WinException $e) {
			return $e->false();
		}
	}

	public function change_status()
	{
		try {
			$validate = validate('Admin');

			if (!$validate->scene('status')->check(input('get.'))) win_exception($validate->getError(), 89);

			if (!self::save(['status' => input('get.status')],['id' => input('get.id')])) win_exception('修改状态失败', 91);

			return return_no_data();
		} catch (WinException $e) {
			return $e->false();
		}
	}

	public function admin_del()
	{
		try {
			if (!self::save(['status' => input('get.status')],['id' => input('get.id')])) win_exception('删除失败', 102);

			return return_no_data();
		} catch (WinException $e) {
			return $e->false();
		}
	}

	public function admin_login()
	{
		try {
			$validate = validate('Admin');

			if (!$validate->scene('login')->check(input('post.'))) win_exception($validate->getError(), 115);

			if ($admin = self::get(['admin_name' => input('post.admin_name'), 'status' => '1']));
			elseif($admin = self::get(['phone' => input('post.admin_name'), 'status' => '1']));
			elseif($admin = self::get(['email' => input('post.admin_name'), 'status' => '1']));
			else win_exception('管理员不存在或被禁用');

			if (!password_verify(input('post.password'), $admin->password)) win_exception('密码错误', 122);

			$kb_code = substr(password_hash((string)time(), PASSWORD_DEFAULT), 0, 40);

			if (!$this->redis->setex($kb_code, 7200, json_encode($admin->toArray()))) win_exception('记录用户信息失败', 125);

			return return_true($admin->toArray(),$kb_code);
		} catch (WinException $e) {
			return $e->false();	
		}
	}

	public function admin_reset_password()
	{
		try {
			$info = self::get(input('get.id'));

			$validate = validate('Admin');

			if(!$validate->scene('reset_pass')->check(input('post.'))) win_exception($validate->getError(), 141);

			if($info->save(['password' => input('post.password')])) win_exception('重置密码失败', 143);

			return return_no_data();
		} catch (WinException $e) {
			return $e->false();
		}
	}
}