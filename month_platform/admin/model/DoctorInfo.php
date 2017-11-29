<?php
namespace app\admin\model;

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

	public function Classify()
	{
		return $this->belongsTo('Classify');
	}

	public function scopeDepartments($sql)
	{
		if (input('get.departments_id')) {
			$sql->where('departments_id', input('get.departments_id'));
		}
	}

	public function scopeName_like($sql)
	{
		if(input('get.doctor_name')) {
			$sql->where('doctor_name', 'like', '%'. input('get.doctor_name') .'%');
		}
	}

	public function approved()
	{
		try {
			$list = self::departments()
						->name_like()
						->where('status', 'in', '1,2')
						->order('update_time')
						->paginate(10)
						->hidden(['create_time', 'password'])
						->toArray();

			if (!$list) win_exception('', __LINE__);

			$data_total = self::departments()
						->name_like()
						->where('status', 'in', '1,2')
						->count();

			return return_true($list,'', $data_total);
		} catch (WinException $e) {
			return $e->false();
		}
	}

	public function classifybl()
	{
		return $this->hasOne('Classify', 'id', 'departments_id');
	}

	public function doctor_list()
	{
		try {
			$list = self::departments()
						->name_like()
						->where('status', 'in', '0,1')
						->with('classifybl')
						->order('update_time')
						->paginate(15)
						->hidden(['create_time', 'password'])
						->toArray();

			if (!$list) win_exception('', __LINE__);

			$data_total = self::departments()
						->name_like()
						->where('status', 'in', '0,1')
						->count();

			return return_true($list, '', $data_total);
		} catch (WinException $e) {
			return $e->false();
		}
	}

	public function change_status()
	{
		try {
			$info = self::id()
						->where('status', '<>', '-1')
						->find();

			if (!$info) {
				win_exception('', __LINE__);
			}

			$info->status = input('get.status');

			if (input('get.status') != 0) {
				model('SystemNews')->where('relevance_id', $info->id)->where('relevance_type', '2')->update(['is_pass' => input('get.status')]);
			}

			if (!$info->save()) win_exception('操作失败', __LINE__);

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

	public function doctor_detail()
	{
		try {
			$info = self::id()
						->where('status', '<>', '-1')
						->find();

			if (!$info) win_exception('', __LINE__);

			return return_true($info);
		} catch (WinException $e) {
			return $e->false();
		}
	}
}