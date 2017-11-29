<?php
namespace app\user\model;

use think\Model;
use winleung\exception\WinException;

class OrganizationServiceComment extends Common
{
	// 根据机构id获取
	public function scopeOid($sql) 
	{
		$sql->where('service_id', input('get.service_id'));
	}

	public function User()
	{
		return $this->hasMany('User', 'id', 'user_id')
					->field('id, nick_name, head_url');
	}

	public function comment_list_by_organization()
	{
		try {
			$list = self::status()
						->oid()
						->with('User')
						->paginate(5);
			if(!$list) win_exception('', __LINE__);

			return return_true($list->hidden(['status'])->toArray());
		} catch (WinException $e) {
			return $e->false();
		}
	}

	public function setShowPicAttr($value)
	{
		return json_encode($value);
	}

	public function getShowPicAttr($value)
	{
		return json_decode($value, true);
	}

	public function comment_add()
	{
		try {
			$validate = validate('OrganizationServiceComment');
			$data = input('post.');
			if (!$validate->scene('add')->check($data)) {
				win_exception($validate->getError(), __LINE__);
			}

			if (count($data['show_pic']) > 6) win_exception('上传图片最大值为6张', __LINE__);

			$order_info = model('SubscribeOrder')->where('status', '2')
											   ->where('user_id', $this->user_info['id'])
											   ->order('update_time desc')
											   ->find();

			if (!$order_info) {
				win_exception('请进行服务后再评价.', __LINE__);
			}
			// halt($order_info);

			self::startTrans();

			if (self::get(['order_no' => $order_info->order_no])) {
				win_exception('您此前服务后已经评论过了.感谢您的支持', __LINE__);
			}

			$data['order_no'] = $order_info->order_no;

			if (!self::allowField(true)->save($data)) {
				win_exception('评论失败', __LINE__);
			}

			$order_info->status = '3';

			if (!$order_info->save()) {
				win_exception('评论失败', __LINE__);
			}

			self::commit();
			return return_no_data();
		} catch (WinException $e) {
			self::rollback();
			return $e->false();
		}
	}
}