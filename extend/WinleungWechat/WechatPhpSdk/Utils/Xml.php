<?php
/**
 * Xml class
 *
 * Xml处理
 *
 * @author 		WinleungWechat <393857054@qq.com>
 * @link 		https://github.com/Winlueng
 * @link 		http://me.diary8.com/
 */

namespace WinleungWechat\WechatPhpSdk\Utils;

class Xml
{
    /**
     * 生成xml字符串
     * @param array $params 数据数组
     * @return string
     */
    public static function toXml($params)
    {
        $xml = '<xml>';
        foreach ($params as $key => $val) {
            if (is_numeric($val)) {
                $xml .= '<'.$key.'>'.$val.'</'.$key.'>';
            } else {
                $xml .= '<'.$key.'><![CDATA['.$val.']]></'.$key.'>';
            }
        }
        $xml .= '</xml>';
        return $xml;
    }
}