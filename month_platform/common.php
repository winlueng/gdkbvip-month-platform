<?php
// +----------------------------------------------------------------------
// | Author: winleung <393857054@qq.com>
// +----------------------------------------------------------------------
// 应用公共文件

    /**
     * 邮箱接口
     * @param  $to 	    email 		  邮箱地址
     * @param  $title   string(chs)   邮件标题
     * @param  $content string(chs)	  邮件内容
     * @return bool
     */
    function sendMail($to, $title, $content) {
        //3213222
        // require_once './ThinkPHP/Library/Org/Email/class.phpmailer.php';
        Vendor('PHPMailer.class#phpmailer');
        // require_once './ThinkPHP/Library/Org/Email/class.smtp.php';
        $mail = new PHPMailer(); //实例化
        $mail->IsSMTP(); // 启用SMTP
        $mail->Host 		= MAIL_HOST; //smtp服务器的名称（这里以QQ邮箱为例）
        $mail->SMTPAuth 	= MAIL_SMTPAUTH; //启用smtp认证
        $mail->Port 		= 465;  //邮件发送端口
        $mail->SMTPSecure 	= 'ssl';// 链接方式如果使用QQ邮箱；需要把此项改为  ssl
        $mail->Username 	= MAIL_USERNAME; //你的邮箱名
        $mail->Password 	= MAIL_PASSWORD ; //邮箱密码
        $mail->From 		= MAIL_FROM; //发件人地址（也就是你的邮箱地址）
        $mail->FromName 	= MAIL_FROMNAME; //发件人姓名
        $mail->AddAddress($to,"旷邦");
        $mail->WordWrap 	= 50; //设置每行字符长度
        $mail->IsHTML(MAIL_ISHTML); // 是否HTML格式邮件
        $mail->CharSet 		= MAIL_CHARSET; //设置邮件编码
        $mail->Subject 		= $title; //邮件主题
        $mail->Body 		= $content; //邮件内容
        $mail->AltBody 		= "这是一个纯文本的身体在非营利的HTML电子邮件客户端"; //邮件正文不支持HTML的备用显示
        return($mail->Send());
    }

    // 用户密码加密
    function pwd_hash($pwd,$hash='')
    { 
        if($hash === ''){
            $options = [ 'cost' => 12, 'salt' => 'usesomesillystringforsalt' ];
            //return password_hash($key,PASSWORD_DEFAULT );
            return password_hash($pwd, PASSWORD_BCRYPT, $options);
        }else{  
            return password_verify ($pwd,$hash);
        }
    }

    // 返回html格式字符串
    function toHtmlString($string)
    {
       return  htmlspecialchars_decode(html_entity_decode($string));
    }

    // 获取原图的函数
    function getArtwork($kw,$mark1 = '_',$mark2 = '.')
    {
        $st = strrpos($kw,$mark1);
        $ed = strrpos($kw,$mark2);
        if(($st==false||$ed==false)||$st>=$ed) return 0;
        $res1=substr($kw,0,$st);
        $res2=substr($kw,$ed);
        return $res1.$res2;
    }

    /**
     * 图片上传
     * @param $fileField string 上传字段名
     * @param $path      string 上传路劲
     * @param $uploadType int   上传类型(1=>单文件上传,2=>多文件上传)
     * @param $return    string|array 
     */
    function imgUpload($fileField, $path, $uploadType=1, $uploadDir = '/uploads/')
    {
        $validate = [// 上传规则
            'size' => 5242880,// 最大值5M
            'ext'  => 'jpg,png,jpeg,bmp'
        ];

        $path = $uploadDir.$path;

        $file = request()->file($fileField);
        if ($uploadType == 1) {
            $info = $file->validate($validate)->move(UPLOAD_PATH.$path.'/');
            if($info){
                // 成功上传后 获取上传信息
                return $path.'/'.$info->getSaveName();
            }else{
                // 上传失败获取错误信息
                $result['error'] = false;
                $result['errMsg'] = $file->getError();
                return $result;
            }
        }else if($uploadType == 2){// 多文件上传
            foreach ($file as $file) {
                $info = $file->validate($validate)->move(UPLOAD_PATH.$path.'/');
                if($info){
                    // 成功上传后 获取上传信息
                    $result[] = $path.'/'.$info->getSaveName();
                }else{
                    // 上传失败获取错误信息
                    $result['error'] = false;
                    $result['errMsg'] = $file->getError();
                    return $result;
                }
            }
            return $result;
        }
    }

    // 保存并返回缩略图路径
    function returnThumbnail($path, $width = 400, $height = 400)
    {
        $openPath = UPLOAD_PATH.$path;
        $image = \think\Image::open($openPath);
        $ext = substr($path, strrpos($path,'.'));
        $newPath = substr($path, 0, strrpos($path,'.'));
        $savePath = UPLOAD_PATH.$newPath.'_thumbnail'.$ext;
        $image->thumb(300, 300)->save($savePath);
        return $newPath.'_thumbnail'.$ext;
    }

    // PHP“刚刚”、“几分钟前”、“昨天”、“前天”、"n年"等时间函数
    function tranTime($time) { 
        $rtime = date("m-d H:i", $time);
        $htime = date("H:i", $time);
        $ytime = date('Y', $time);
        $btime = date("Y-m-d H:i:s", $time);
        $time = time() - $time;
        if ($time < 60) { 
            $str = '刚刚'; 
        } elseif ($time < 60 * 60) { 
            $min = floor($time / 60); 
            $str = $min . '分钟前'; 
        } elseif ($time < 60 * 60 * 24) { 
            $h = floor($time / (60 * 60)); 
            $str = $h . '小时前 ' . $htime; 
        } elseif ($time < 60 * 60 * 24 * 3) { 
            $d = round($time / (60 * 60 * 24)); 
            if ($d == 1) 
                $str = '昨天 ' . $rtime; 
            else 
                $str = '前天 ' . $rtime; 
        }elseif ($ytime < date('Y', time())){ 
            $str = $btime;
        }else{
            $str = $rtime;
        }
        return $str; 
    }

    function return_true($data, $KB_CODE = '', $total = '')
    {
        if ($KB_CODE) {
            return ['err_code' => 0, 'err_msg' => 'ok', 'data' => $data, 'KB_CODE' => $KB_CODE];
        }
        if (is_int($total)) {
            
            return ['err_code' => 0, 'err_msg' => 'ok', 'data' => $data, 'data_total' => $total];
        }
        return ['err_code' => 0, 'err_msg' => 'ok', 'data' => $data];
    }

    function return_no_data()
    {
        return ['err_code' => 0, 'err_msg' => 'ok'];
    }

    function return_false($code=__LINE__, $msg='未查询到任何数据')
    {
        if(!$code) $code = __LINE__;
        return ['err_code' => $code, 'err_msg' => $msg];
    }

    // 星座判断
    function constellationJudge($timestamp) 
    {
        list($year, $month, $day) = explode('-', date('Y-m-d', $timestamp));
        // 检查参数有效性 
        if ($month < 1 || $month > 12 || $day < 1 || $day > 31) return false;

         // 星座名称以及开始日期
        $constellations = array(
            array( "20" => "宝瓶座"),
            array( "19" => "双鱼座"),
            array( "21" => "白羊座"),
            array( "20" => "金牛座"),
            array( "21" => "双子座"),
            array( "22" => "巨蟹座"),
            array( "23" => "狮子座"),
            array( "23" => "处女座"),
            array( "23" => "天秤座"),
            array( "24" => "天蝎座"),
            array( "22" => "射手座"),
            array( "22" => "摩羯座")
        );

         list($constellation_start, $constellation_name) = each($constellations[(int)$month-1]);

         if ($day < $constellation_start) list($constellation_start, $constellation_name) = each($constellations[($month -2 < 0) ? $month = 11: $month -= 2]);

         return $constellation_name;
    }

    function win_exception($msg = '', $code = __LINE__, $exception = '')
    {
        if(!$msg) $msg = '未查询到任何数据';
        $e = $exception ?: '\winleung\exception\WinException';
        throw new $e($msg, $code);
    }

        /**
     * 冒泡降序排序(1-0)
     * @param  array    $array    传入数组
     * @param  string   $field    根据字段排序
     * @return array    $result   排序后的数组
     */
    function bubble_sort_top($array, $field)
    {
        $count = count($array);

        if ($count < 2) return $array;

        for ($k=0; $k < $count; $k++) { 
            for($j=$count-1;$j>$k;$j--){
                if($array[$j][$field]>$array[$j-1][$field])
                {
                    $temp = $array[$j];
                    $array[$j] = $array[$j-1];
                    $array[$j-1] = $temp;
                }
            }
        }
        return $array;
    }

    /**
     * 冒泡升序排序(0-1)
     * @param  array    $array    传入数组
     * @param  string   $field    根据字段排序
     * @return array    $result   排序后的数组
     */
    function bubble_sort_down($array, $field)
    {
        $count = count($array);
        
        if ($count < 2) return $array;

        for ($k=0; $k < $count; $k++) { 
            for($j=1;$j<$count-$k;$j++){
                if($array[$j][$field]<$array[$j-1][$field]){
                    $temp =$array[$j-1];
                    $array[$j-1] =$array[$j] ;
                    $array[$j] = $temp;
                }
            }
        }

        return $array;
    }

    /**
     * 皮尔逊相关系数的算法-求乘积之和
     * @param  array $a 用户a商品浏览描述
     * @param  array $b 用户b商品浏览描述
     * @return int    乘积之和
     */
    function multipl($a, $b)
    {
        $sumfab = 0;

        foreach ($a as $k => $v) {
            $sumfab += $v * $b[$k];
        }

        return $sumfab;
    }

    /**
     * 求平方和
     * @param  array $a 用户a商品浏览评分数组
     * @return int    平方和
     */
    function sum_of_squares($a)
    {
        if (!$a) {
            return false;
        }

        $sum = 0;
        foreach ($a as $v) {
            $sum += pow($v, 2);
        }
        return $sum;
    }

    /**
     * 皮尔逊相关系数的算法-求出相关系数
     * @param  array $a 用户a商品浏览描述
     * @param  array $b 用户b商品浏览描述
     * @return float    相关系数
     */
    function corrcoef($a, $b)
    {
        if (count($a) != count($b)) return false;
        
        $n = count($a);

        // 求总和
        $sum_a = array_sum($a);
        $sum_b = array_sum($b);
        // 乘积和
        $sum_of_ab = multipl($a, $b);

        // 平方和
        $sum_of_a = sum_of_squares($a);
        $sum_of_b = sum_of_squares($b);

        $up = $sum_of_ab - (number_format($sum_a, 2, '.', '')*number_format($sum_b, 2, '.', '')/$n);

        $down = sqrt(($sum_of_a - number_format(pow($sum_a, 2), 2, '.', '')/$n) * ($sum_of_b - number_format(pow($sum_b, 2), 2, '.', '')/$n));

        return $up/$down;
    }

    /**
     *计算某个经纬度的周围某段距离的正方形的四个点
     *
     *@param lng float 经度
     *@param lat float 纬度
     *@param distance float 该点所在圆的半径，该圆与此正方形内切，默认值为0.5千米
     *@return array 正方形的四个点的经纬度坐标
     */
    function return_square_point($lng, $lat,$distance = 0.5)
    {
        
        $dlng =  2 * asin(sin($distance / (2 * EARTH_RADIUS)) / cos(deg2rad($lat)));
        $dlng = rad2deg($dlng);
         
        $dlat = $distance/EARTH_RADIUS;
        $dlat = rad2deg($dlat);
         
        return [
                'left-top'      =>['lat' => $lat + $dlat, 'lng' => $lng - $dlng],
                'right-top'     =>['lat' => $lat + $dlat, 'lng' => $lng + $dlng],
                'left-bottom'   =>['lat' => $lat - $dlat, 'lng' => $lng - $dlng],
                'right-bottom'  =>['lat' => $lat - $dlat, 'lng' => $lng + $dlng]
            ];
    }

    /**
     * 获取两坐标点距离
     * @param  [type] $a_x [description]
     * @param  [type] $a_y [description]
     * @param  [type] $b_x [description]
     * @param  [type] $b_y [description]
     * @return [type]      [description]
     */
    function return_two_point_distance($a_y,$a_x,$b_y,$b_x)
    {
        $pk = 180 / 3.14169;
        $a1 = $a_y / $pk;
        $a2 = $a_x / $pk;
        $b1 = $b_y / $pk;
        $b2 = $b_x / $pk;
        $t1 = cos($a1) * cos($a2) * cos($b1) * cos($b2);
        $t2 = cos($a1) * sin($a2) * cos($b1) * sin($b2);
        $t3 = sin($a1) * sin($b1);
        $tt = acos($t1 + $t2 + $t3);
        return 6371000*$tt;
    }