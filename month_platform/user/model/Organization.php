<?php
namespace app\user\model;

use think\Model;
use winleung\exception\WinException;

class Organization extends Common
{
	public function OrganizationDetail()
	{
		return $this->hasMany('OrganizationDetail', 'organization_id')
					->field('organization_id, description, principal, principal_phone, principal_email, organization_logo, organization_pic, home_link');
	}

	public function OrganizationComment()
	{
		return $this->hasMany('OrganizationComment', 'organization_id');
	}

	public function OrganizationService()
	{
		return $this->hasMany('OrganizationService', 'organization_id');
	}

	public function scopeLocation_where($sql)
	{
		if (input('get.y_point') && input('get.x_point')) {
			$point_arr = return_square_point(input('get.y_point'), input('get.x_point'), 5);// 进行5公里范围检测
			$sql->where('y_coordinate', '<', $point_arr['left-top']['lng'])
				->where('y_coordinate', '>', $point_arr['right-top']['lng'])
				->where('x_coordinate', '>', $point_arr['left-bottom']['lat'])
				->where('x_coordinate', '<', $point_arr['left-top']['lat']);
		}
	}

	public function scopeLocation($sql)
	{
		if(input('get.province_id')){
			$sql->where('province_id', input('get.province_id'));
		}
		elseif(input('get.city_id')){
			$sql->where('city_id', input('get.city_id'));
		}
		elseif(input('get.district_id')){
			$sql->where('district_id', input('get.district_id'));

		}
	}

	public function scopeTime($sql)
	{
		$sql->where('start_time', '<', time())
			->where('end_time', '>', time());
	}

	public function list_by_location()
	{
		try {
			$list = self::status()
						->location()
						->time()
						->select()
						->toArray();

			if (!$list) win_exception('', __LINE__);

			foreach ($list as $v) {
				$v['distance'] = return_two_point_distance(input('get.y_point'), input('get.x_point'), $v['y_point'], $v['x_point']);
				$v['comment_total'] = model('OrganizationComment')->where('organization_id', $v['id'])->count();
				$v['popularity']	= model('OrganizationBehavior')->where('organization_id', $v['id'])
																   ->where('visit_total', '>', '0')
																   ->sum('visit_total');
				$v['distance'] = round($v['distance'], 2);
				$data  = model('OrganizationDetail')->field('organization_logo,principal,principal_phone,principal_email')
													->where('organization_id', $v['id'])
													->find()
													->toArray();
				$c_list[] = array_merge($v, $data);
			}

			switch (input('get.sort')) {
				case 'comment':// 评论
					$res = bubble_sort_top($c_list, 'comment_total');
					break;
				case 'popularity': // 人气
					$res = bubble_sort_top($c_list, 'popularity');
					break;
				default:
					$res = bubble_sort_down($c_list, 'distance');
					break;
			}

			return return_true($res);
		} catch (WinException $e) {
			return $e->false();
		}
	}

	public function organization_list()
	{
		try {
			$list = self::location()
						->status()
						->time()
						->select()
						->toArray();

			if (!$list) {
				// 2. 不存在检索范围内店铺则进行本区->市->省进行范围递增检索
				$info = self::getAddressComponent(BAIDU_API_KEY, input('get.y_point'), input('get.x_point'));

				if ($info['status'] == 0) {
					$district_id = db('District')// 获得区的id
									->where('name', $info['result']['addressComponent']['district'])
									->column('district_id');

					$list = self::where('district_id', $district_id)
								 ->status()
								 ->time()
								 ->paginate(8);

					if (!$list) {
						$city_id = db('District')// 获得市的id
								->where('name', $info['result']['addressComponent']['city'])
								->column('district_id');

						$list = self::where('city_id', $city_id)
									 ->status()
									 ->time()
									 ->paginate(8);

						if (!$list) {
							$province_id = db('District')// 获取省的id
											->where('name', $info['result']['addressComponent']['province'])
											->column('district_id');

							$list = self::where('province_id', $province_id)
										 ->status()
										 ->time()
										 ->paginate(8);
							if (!$list) {
								win_exception('本省未有机构进驻', __LINE__);
							}
						}
					}
 
				}else{
					$list = self::status()
								->select()
								->toArray();
					if (!$list) {
						win_exception('', __LINE__);
					}
				}
			}

			foreach ($list as $v) {
				$v['distance'] = return_two_point_distance(input('get.y_point'), input('get.x_point'), $v['y_point'], $v['x_point']);
				$v['comment_total'] = model('OrganizationComment')->where('organization_id', $v['id'])->count();
				$v['popularity']	= model('OrganizationBehavior')->where('organization_id', $v['id'])
																   ->where('visit_total', '>', '0')
																   ->sum('visit_total');
				$v['distance'] = round($v['distance'], 2);
				$data  = model('OrganizationDetail')->field('organization_logo,principal,principal_phone,principal_email')
													->where('organization_id', $v['id'])
													->find()
													->toArray();
				$c_list[] = array_merge($v, $data);
			}

			switch (input('get.sort')) {
				case 'comment':// 评论
					$res = bubble_sort_top($c_list, 'comment_total');
					break;
				case 'popularity': // 人气
					$res = bubble_sort_top($c_list, 'popularity');
					break;
				default:
					$res = bubble_sort_down($c_list, 'distance');
					break;
			}
			
			return return_true($res);
		} catch (WinException $e) {
			return $e->false();
		}
	}

	public function OrganizationBehavior()
	{
		return $this->hasMany('OrganizationBehavior', 'organization_id', 'id')->where('visit_total', '<>', '0');
	}

	public function organization_detail()
	{
		try {
			$info = self::id()
						->status()
						->time()
						->with('organizationDetail')
						->withCount(['OrganizationBehavior' => function($sql){
							$sql->where('is_save', '1');
						}])
						->find();

			if (!$info) win_exception('', __LINE__);

			return return_true($info);
		} catch (WinException $e) {
			return $e->false();
		}
	}
}