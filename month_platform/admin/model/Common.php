<?php
namespace app\admin\model;

use think\Model;

class Common extends Model
{
	protected $resultSetType = 'collection';
	protected $redis;
	protected $user_info;

	public function initialize()
	{
		parent::initialize();
        $this->redis = new \Redis;
        $this->redis->connect('127.0.0.1', 6379);
        $this->user_info = json_decode($this->redis->get(input('get.KB_CODE')), true);
	}

	public function scopeStatus($sql)
	{
		$sql->where('status', 'in', '1,2');
	}

	public function scopeId($sql)
	{
		$sql->where('id', input('get.id'));
	}

	public function setTagListAttr($value)
	{
		sort($value);
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
}