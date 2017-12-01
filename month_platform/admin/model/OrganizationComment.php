<?php
namespace app\admin\model;

use think\Model;
use winleung\exception\WinException;
use think\Db;

class OrganizationComment extends Common
{
	public function User()
	{
		return $this->hasMany('User', 'id', 'user_id')
					->field('id,nick_name,head_url');
	}

	public function comment_list()
	{
		try {
			/*$list = self::where('organization_id', input('get.id'))
						->where('status', '<>', '-1')
						->with('User')
						->order('create_time desc')
						->paginate(10)
						->hidden(['update_time'])
						->toArray();*/
			$oid = input('get.id');
			$page = input('get.page')?input('get.page'):1;

			if ($page == 1) {
				$num = 0;
			}else{
				$num = ($page-1) * 10;
			}

			$sql  = "SELECT c.id as comment_id,
						c.attitude_score,
						c.totality_score,
						c.show_pic,
						c.status as comment_status,
						c.comment_info,
						c.service_id,
						c.organization_id,
						u.id as user_id,
						u.nick_name as user_nick_name,
						u.head_url as user_head_url,
						COUNT(c.id) as comment_total,
						s.service_name,
						s.logo as service_logo
					FROM month_organization o 
					LEFT JOIN month_organization_service s ON o.id=s.organization_id AND s.status<>'-1'
					LEFT JOIN month_organization_service_comment c ON c.service_id=s.id
					LEFT JOIN month_user u ON u.id=c.user_id 
					WHERE c.status<>'-1' AND o.id={$oid}
					ORDER BY c.create_time desc
					LIMIT {$num},10";

			$list = Db::query($sql);

			if ($list[0]['comment_id'] == null) win_exception('', __LINE__);

			foreach ($list as $v) {
				$v['show_pic'] = json_decode($v['show_pic'], true);
				$v['service_info']['service_name'] = $v['service_name'];
				$v['service_info']['service_logo'] = $v['service_logo'];
				$v['service_info']['service_id'] = $v['service_id'];
				$v['user_info']['user_id'] = $v['user_id'];
				$v['user_info']['user_nick_name'] = $v['user_nick_name'];
				$v['user_info']['user_head_url'] = $v['user_head_url'];
				unset($v['service_name']);
				unset($v['service_logo']);
				unset($v['service_id']);
				unset($v['user_id']);
				unset($v['user_nick_name']);
				unset($v['user_head_url']);
				$res[] = $v;
			}
			
			return return_true($res, '', (int)$list[0]['comment_total']);
		} catch (WinException $e) {
			return $e->false();
		}
	}

	public function change_status()
	{
		try {
			if(!self::save(['status' => input('get.status')], ['id' => input('get.id')])) win_exception('操作失败', __LINE__);
			
			return return_no_data();
		} catch (WinException $e) {
			return $e->false();	
		}
	}
}