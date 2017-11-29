<?php
namespace app\admin\controller;

use think\controller;

class RuleGroup extends Common
{
	public function _initialize()
	{
		parent::_initialize();
		$this->obj = model('RuleGroup');
	}

	public function postAdd()
	{
		return $this->obj->group_add();
	}

	// 获取分组列表
	public function getList()
	{
		return $this->obj->group_list();
	}

	public function postUpdate()
	{
		return $this->obj->group_update();
	}

	public function getDel()
	{
		return $this->obj->group_del();
	}

	public function getEee()
    {
        $string_to_number = [ '零', '壹', '贰', '叄', '肆', '伍', '陆', '柒', '捌', '玖', '拾'];
        
        $num_1 = mt_rand(0,10);
        $num_2 = mt_rand(0,10);

        $operator_total = ['加', '减', '乘以'];
        $operator = $operator_total[mt_rand(0, 2)];
        $result_arr = [ $string_to_number[$num_1], $operator, $string_to_number[$num_2], '等于'];
        // halt($result_arr);
        switch ($operator) {
            case '加':
                $final_total = $num_1 + $num_2;
                break;
            case '减':
                if ($num_1 > $num_2) {
                    $final_total = $num_1 - $num_2;
                }else{
                    $final_total = $num_2 - $num_1;
                    $result_arr = [ $string_to_number[$num_2], $operator, $string_to_number[$num_1], '等于'];
                }
                break;
            case '乘以':
                $final_total = $num_1 * $num_2;
                break;
        }
        return ['result_arr' => $result_arr,'final' => $final_total];
        
    }

    public function getCreate($formId = '123')
    {
        $code = self::getEee();// 获取算题和答案
        halt($code);
        if (!$formId) {
            $formId = hash('sha256', URL::previous());
        }
        Session::put('captchaOperator.' . $formId, $code['final']); // 保存答案到session
        
        $bg_image = $this->asset('backgrounds');

        $bg_image_info = getimagesize($bg_image);
        if ($bg_image_info['mime'] == 'image/jpg' || $bg_image_info['mime'] == 'image/jpeg') {
            $old_image = imagecreatefromjpeg($bg_image);
        } elseif ($bg_image_info['mime'] == 'image/gif') {
            $old_image = imagecreatefromgif($bg_image);
        } elseif ($bg_image_info['mime'] == 'image/png') {
            $old_image = imagecreatefrompng($bg_image);
        }

        $new_image = imagecreatetruecolor($this->config['width'], $this->config['height']);
        $bg = imagecolorallocate($new_image, 255, 255, 255);
        imagefill($new_image, 0, 0, $bg);

        imagecopyresampled($new_image, $old_image, 0, 0, 0, 0, $this->config['width'], $this->config['height'], $bg_image_info[0], $bg_image_info[1]);

        $bg = imagecolorallocate($new_image, 255, 255, 255);

        $codeLength = count($code['result_arr']);
        
        $spaces = (array)$this->config['space'];
        $space = $spaces[array_rand($spaces)];
        for ($i = 0; $i < $codeLength; ++$i) {
            $color_cols = explode(',', $this->asset('colors'));
            $fg = imagecolorallocate($new_image, trim($color_cols[0]), trim($color_cols[1]), trim($color_cols[2]));
            imagettftext($new_image, $this->asset('fontsizes'), mt_rand(-10, 10), 15 + ($i * $space), mt_rand($this->config['height'] - 10, $this->config['height'] - 5), $fg, $this->asset('fonts'), $code[$i]);
        }
        imagealphablending($new_image, false);

        ob_start();
        imagejpeg($new_image, null, $this->config['quality']);
        $content = ob_get_clean();
        imagedestroy($new_image);

        return Response::make($content, 200)
            ->header('cache-control', 'no-cache, no-store, max-age=0, must-revalidate')
            ->header('pragma', 'no-cache')
            ->header('content-type', 'image/jpeg')
            ->header('content-disposition', 'inline; filename=captcha.jpg');
    }
}