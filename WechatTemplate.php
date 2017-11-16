<?php
namespace Home\Controller;
use Think\Controller;

class WechatTemplate extends Controller{
    public function TemplatePush(){
        header("content-type:text/html;charset=utf-8");
        define("APPID","你的appid");//在微信公众号有
        define("APPSECRET","你的appsecret");//在微信公众号有
        $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".APPID."&secret=".APPSECRET;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);  // 设置你需要抓取的URL
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);  //这两句用于验证第三方服务器与微信服务器的安全性
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);  //这两句用于验证第三方服务器与微信服务器的安全性
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);  // 设置cURL 参数，要求结果保存到字符串中还是输出到屏幕上。1是不输出在页面上
        $output = curl_exec($ch);   //得到的access_token赋值给$output
        curl_close($ch);
        $result = json_decode($output, true);
        $access_token = $result['access_token'];
        $openid = "接收人的openid";//每个人关注了公众号就会有一个唯一的openid
        $data = array(
            'touser' => $openid, // openid是发送消息的基础
            'template_id' => '推送模板id', //这是申请模板是会生成的
            'url' => 'http://test.com', // 点击跳转地址
            'topcolor' => 'blue', // 顶部颜色
            'data' => array(
                'first' => array('value' => urlencode('这是标题'),
            'color'=>"#FE0000"//设置字体颜色
                ),
                'keyword1' => array('value' => urlencode('这是keyword1'),
                ),
                'keyword2' => array('value' => urlencode('这是keyword2'),
                ),
                'keyword4' => array('value' => urlencode('这是keyword3'),
                ),
                'remark' => array('value' => urlencode('我是remark'),
                ),
            )
        );
        $resultStr = muban(urldecode(json_encode($data)),$access_token);//用curl模拟触发执行
        var_dump($resultStr);
    }


}