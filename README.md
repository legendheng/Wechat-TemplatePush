# Wechat-TemplatePush
thinkphp3.2实现微信模板推送接口(该例子使用了测试号)
### 第一步、在你的控制器编写wenxin入口方法
```php
public function wechat(){
        //获得参数 signature nonce token timestamp echostr
        $nonce     = $_GET['nonce'];
        $token     = 'wechattoken';
        $timestamp = $_GET['timestamp'];
        $echostr   = $_GET['echostr'];
        $signature = $_GET['signature'];
        //形成数组，然后按字典序排序
        $array = array($nonce, $timestamp, $token);
        sort($array);
        //拼接成字符串,sha1加密 ，然后与signature进行校验
        $str = sha1( implode( $array ) );
        if( $str  == $signature && $echostr ){
            //第一次接入weixin api接口的时候
            echo  $echostr;
            exit;
        }else{
            $this->reponseMsg();
        }
    }
```
### 第二步、在测试号填写接口配置信息
* URL：你的的微信入口方法
```php
例如：http://www.test.com/test/index.php/Home/wechat/wechat //这是URL
```
* Token：你在入口方法填的token值
```php
例如：wechattoken
```
### 第三步、申请模板消息接口
* 例如：模板标题填测试
* 模板内容填如下
```php
	{{first.DATA}} 测试1：{{keyword1.DATA}} 测试2：{{keyword2.DATA}} 测试3：{{keyword4.DATA}} {{remark.DATA}}
```
### 第四步、在Application\common下新建一个function.php文件专门放自定义类函数
* 以下是使用curl函数模拟提交
```php
function template_muban($data,$ACCESS_TOKEN){
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "http://api.weixin.qq.com/cgi-bin/message/template/send?access_token=".$ACCESS_TOKEN);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $tmpInfo = curl_exec($ch);
    if (curl_errno($ch)) {
        return curl_error($ch);
    }
    curl_close($ch);
    return $tmpInfo;
}
```
### 第五步、在控制器编写消息推送方法
```php
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
```

