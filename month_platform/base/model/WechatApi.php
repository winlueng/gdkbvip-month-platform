<?php
namespace app\base\model;

use think\Model;
use WinleungWechat\WechatPhpSdk\Api;

class WechatApi extends Model
{
	private $api_obj;

	public function initialize()
	{
		parent::initialize();
		$this->api_obj = model('WechatConfig')->newApi();
	}

	public function wxPayCallBackUrl(){

        // 获取返回的post数据包
        $post_str = $GLOBALS["HTTP_RAW_POST_DATA"] ;
        if(!empty($post_str)){
            libxml_disable_entity_loader(true);
            $post_obj = (array)simplexml_load_string($post_str, 'SimpleXMLElement', LIBXML_NOCDATA);
            if($post_obj['result_code']=='SUCCESS') {

                $order_no = $post_obj['out_trade_no'];
                $open_id=$post_obj['openid'];
                $order_wx = Loader::model('base/OrdersWxpay')->where(['order_no' => $order_no,'payer_openid'=>$open_id,])->find();
                if($order_wx){
               //     Db::table('my_errs')->insert(['content'=>json_encode($order_wx)]);
                    //更新订单数据
                    $this->updateOrderIsPay($order_no);
                    $order_amount=(int)$post_obj['total_fee']/100;

                    //发送客服消息
                    $this->sendPayOkMessage($order_no,$order_amount,$open_id);
                    //保存支付记录
                    $save_data=[
                        'order_no'=> $order_no,
                        'pay_type'=> 'WxPay',
                        'openid'=>$open_id,
                        'total_fee' => $post_obj['total_fee'],
                        'trade_type'=>   $post_obj['trade_type'] ,       //JSAPI、NATIVE、APP
                        'bank_type'=>$post_obj['bank_type'] ,
                        'result_code'=>$post_obj['result_code'],
                        'transaction_id'=>$post_obj['transaction_id'],
                        'time_end'=>$post_obj['time_end'],
                    ];
                  //  Db::table('my_errs')->insert(['content'=>json_encode($save_data)]);
                   Loader::model('base/OrdersStatus')->editData($save_data);
                    $this->echoCallBack(true);

                }else{
                    $this->echoCallBack();
                }
            }else{
                $this->echoCallBack();
            }
        }else{
            $this->echoCallBack();
        }

    }

    protected function updateOrderIsPay($order_no,$pay_type='WxPay'){

        $update_data=[
           'is_pay'=>1,
           'order_state'=>1,
           'pay_time'=>time(),
          'pay_type'=>$pay_type,
       ];
        Loader::model('base/Orders')->save($update_data,['order_no'=>$order_no]);

        $update_data=[
            'is_pay'=>1,
        ];
        Loader::model('base/OrdersAccess')->save($update_data,['order_no'=>$order_no]);

    }

    protected function sendPayOkMessage($order_no,$order_amount,$open_id){

        $add_message = [
            'title' => '订单支付成功通知',
            'template_id' => 'NlBWOshEjRxoRtpodi-_Tt2XVfe5TNh-uAbDXkkJ4vw',
            'url' =>$this->request->domain().'/index/WC_html_1/mainContainer.html#order/order_orderInfo.html||orderId='.$order_no,
            'data' => [
                'first' => ['value' => '您好,您的订单已支付成功，我们会尽快为您发货(callBack)',],
                'keyword1' => ['value' => $order_no,],
                'keyword2' => ['value' =>$order_amount.'元',],
                'remark' => ['value' => '我们已经收到你的货款请耐心等待收货。',],
            ],
        ];
        $reg = controller('base/WxApi')->sendTemplateMessage($add_message,$open_id);
        return $reg ? true : false ;

    }

    protected function echoCallBack($status = false){
        if (!$status){
            $result = "<xml>
				<return_code><![CDATA[FAIL]]></return_code>
				<return_msg><![CDATA[未接收到post数据]]></return_msg>
				</xml>";
        }else{
            $result = "<xml>
				<return_code><![CDATA[SUCCESS]]></return_code>
				<return_msg><![CDATA[OK]]></return_msg>
				</xml>";
        }
        echo $result;
    }
}