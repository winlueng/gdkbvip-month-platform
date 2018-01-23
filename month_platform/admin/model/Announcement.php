<?php
namespace app\admin\model;

use think\Model;
use winleung\exception\WinException;
use think\Db;

class Announcement extends Common
{
	public function User()
	{
		return $this->hasMany('User', 'id', 'receiver_id')->field('id, nick_name');
	}

	public function DoctorInfo()
	{
		return $this->hasMany('DoctorInfo', 'id', 'receiver_id')->field('id, doctor_name');
	}

	public function announcement_add()
	{
		try {
			$data = input('post.');

			$validate = validate('Announcement');

			if (!$validate->scene('add')->check($data)) win_exception($validate->getError(), __LINE__);
			$save = $data;
			$max = self::max('announcement_id');
			$save['announcement_id'] = $max + 1;
			if (count($data['receiver_id']) > 1) {
				self::startTrans();
				for ($i=0; $i < count($data['receiver_id']); $i++) { 
					$save['receiver_id'] = $data['receiver_id'][$i];
					$res[] = $save;
				}
				if (!self::saveAll($res)) {
					trace('批量消息公告发送失败,错误时间: '. data('Y-m-d H:i:s'),'log');
					win_exception('新增失败', __LINE__);
				} 
			}else{
				$save['receiver_id'] = $data['receiver_id'][0];
				if (!self::allowField(true)->save($save)) {
					trace('消息公告发送失败, 用户id:'. $save['receiver_id'] .',错误时间: '. data('Y-m-d H:i:s'),'log');
					win_exception('发送失败', __LINE__); 
				}
			}
			self::commit();
			return return_no_data();
		} catch (WinException $e) {
			self::rollback();
			return $e->false();
		}
	}

	public function announcement_list()
	{
		try {
			$list = self::where('status', '<>', '-1')
                        ->where('order_id', '0')
						->order('create_time desc')
						->group('announcement_id')
						->paginate(10)
						->hidden(['user_status'])
						->toArray();

			if (!$list) win_exception('', __LINE__);

			$data_total = Db::query("select COUNT(*) total from (select announcement_id from month_announcement group by announcement_id) as a")[0]['total'];
			
			return return_true($list, '', $data_total);
		} catch (WinException $e) {
			return $e->false();
		}
	}

	public function change_status()
	{
		try {
			if (!self::save(['status' => input('get.status')], ['announcement_id' => input('get.id')])) win_exception('操作失败', __LINE__);
			
			return return_no_data();
		} catch (WinException $e) {
			return $e->false();
		}
	}

	// 创建订单默认系统消息
	public function create_system_news($data)
	{
		try {
			if (!$data) win_exception('announcement类,create_system_news方法遗留提交data数据', __LINE__);

			$default_data = [
				'title'		=> '消息提醒',
				'content'	=> '默认消息内容',
				'announcement_id'	=> self::max('announcement_id') + 1,
				'news_type' => 2,
			];

			$send_data = array_merge($default_data, $data);

			if (!self::save($send_data)) {
				win_exception('announcement类,create_system_news方法生成消息提醒失败', __LINE__);
			}

		} catch (WinException $e) {
			trace($e->getMassage(),'error');
		}
	}
}