## 介紹

微付聚合支付 PHP 版本封裝。

## 安裝

使用 Composer 安裝。

```
composer require jetfueltw/wefupay-php
```

## 使用方法

### 掃碼支付下單

使用微信支付、支付寶、財付通、京東支付掃碼支付，下單後返回支付網址，請自行轉為 QR Code。

```
$merchantId = '1XXXXXXXX6'; // 商家號
$merchantPrivateKey = '-----BEGIN PRIVATE KEY-----XXX-----END PRIVATE KEY-----'; // 商家私鑰
$tradeNo = '20170101235959XXX'; // 商家產生的唯一訂單號
$channel = Channel::WECHAT; // 第三方支付，支援微信支付、支付寶、財付通、京東支付
$amount = 1.00; // 消費金額 (元)
$clientIp = 'XXX.XXX.XXX.XXX'; // 消費者端 IP 位址
$notifyUrl = 'https://XXX.XXX.XXX'; // 交易完成後異步通知接口
```
```
$payment = new DigitalPayment($merchantId, $merchantPrivateKey);
$result = $payment->order($tradeNo, $channel, $amount, $clientIp, $notifyUrl);
```
```
Result:
[
    'interface_version' => 'V3.3', // 接口版本
    'merchant_code' => '1XXXXXXXX6', // 商家號
    'order_amount'=> '1.00'; // 消費金額 (元)
    'order_no'=>'20170101235959XXX', // 商家產生的唯一訂單號
    'order_time'=>'2017-01-01 12:23:59', // 下單時間
    'qrcode'=> 'weixin://wxpay/bizpayurl?pr=XXXXXXX', // 支付網址
    'resp_code'=>'SUCCESS', // SUCCESS 成功、FAIL 失敗
    'resp_desc'=>'通讯成功', // 描述訊息
    'result_code'=> '0', // 0 成功、1 失敗
    'trade_no'=> 'CXXXXXXXXXX', // 聚合支付平台訂單號
    'trade_time'=>'2017-01-01 13:00:00', // 交易時間
    'sign_type'=> 'RSA-S', // 簽名方式
    'sign'=> 'XXXXXXXXXXX', // 簽名
];
```

### 掃碼支付交易成功通知

消費者支付成功後，平台會發出 HTTP POST 請求到你下單時填的 $notifyUrl，商家在收到通知並處理完後必須回應 "SUCCESS" 這 7 個字元，否則平台會認為通知失敗，並重發最多 5 次通知。

* 商家必需正確處理重複通知的情況。
* 能使用 `NotifyWebhook@successNotifyResponse` 返回成功回應。  
* 務必使用 `NotifyWebhook@verifyNotifyPayload` 驗證簽證是否正確。

```
Post Data:
[
    'interface_version' => 'V3.3', // 接口版本
    'merchant_code' => '1XXXXXXXX6', // 商家號
    'orginal_money' => '1.00', // 消費金額 (元)
    'order_amount' => '1.00', // 實際支付金額 (元)
    'order_no' => '20170101235959XXX', // 商家產生的唯一訂單號
    'order_time' => '2017-01-01 12:23:59', // 下單時間
    'trade_status' => 'SUCCESS', // SUCCESS 成功、FAILED 失敗
    'trade_no' => 'CXXXXXXXXXX', // 聚合支付平台訂單號
    'trade_time' => '2017-01-01 13:00:00', // 交易時間
    'bank_seq_no' => 'WXXXXXXXXXXXXXXXXXXXXXXX', // 網銀交易流水號
    'notify_type' => 'offline_notify', // 通知類型
    'notify_id' => 'XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX', // 通知校驗 ID
    'sign_type' => 'RSA-S', // 簽名方式
    'sign' => 'XXXXXXXXXX', // 簽名
]
```

### 掃碼支付訂單查詢

使用商家訂單號查詢單筆訂單狀態。

```
$merchantId = '1XXXXXXXX6'; // 商家號
$merchantPrivateKey = '-----BEGIN PRIVATE KEY-----XXX-----END PRIVATE KEY-----'; // 商家私鑰
$tradeNo = '20170101235959XXX'; // 商家產生的唯一訂單號
```
```
$tradeQuery = new TradeQuery($merchantId, $merchantPrivateKey);
$result = $tradeQuery->find($tradeNo);
```
```
Result:
[
    'is_success' => 'T', // T 成功、F 失敗
    'sign_type' => 'RSA-S', // 簽名方式
    'sign' => 'XXXXXXXXXX', // 簽名
    'trade' => [
        'merchant_code' => '1XXXXXXXX6', // 商家號
        'orginal_money' => '1.00', // 消費金額 (元)
        'order_amount' => '1.00', // 實際支付金額 (元)
        'order_no' => '20170101235959XXX', // 商家產生的唯一訂單號
        'order_time' => '2017-01-01 12:23:59', // 下單時間  
        'trade_status' => 'UNPAY', // SUCCESS 成功、FAILED 失敗、UNPAY 未支付
        'trade_no' => 'CXXXXXXXXXX', // 聚合支付平台訂單號
        'trade_time' => '2017-01-01 13:00:00', // 交易時間
    ]
]
```

### 掃碼支付訂單支付成功查詢

使用商家訂單號查詢單筆訂單是否支付成功。

```
$merchantId = '1XXXXXXXX6'; // 商家號
$merchantPrivateKey = '-----BEGIN PRIVATE KEY-----XXX-----END PRIVATE KEY-----'; // 商家私鑰
$tradeNo = '20170101235959XXX'; // 商家產生的唯一訂單號
```
```
$tradeQuery = new TradeQuery($merchantId, $merchantPrivateKey);
$result = $tradeQuery->isPaid($tradeNo);
```
```
Result:
bool(true|false)
```

### 網銀支付下單

使用網路銀行支付，下單後返回跳轉頁面，請 render 到客戶端。

```
$merchantId = '1XXXXXXXX6'; // 商家號
$merchantPrivateKey = '-----BEGIN PRIVATE KEY-----XXX-----END PRIVATE KEY-----'; // 商家私鑰
$httpReferer = 'https://xxx.xxx.xxx'; // 在平台設定的允許接入域名
$tradeNo = '20170101235959XXX'; // 商家產生的唯一訂單號
$bank = Bank::PSBC; // 銀行編號
$amount = 1.00; // 消費金額 (元)
$clientIp = 'XXX.XXX.XXX.XXX'; // 消費者端 IP 位址
$notifyUrl = 'https://XXX.XXX.XXX'; // 交易完成後異步通知接口
```
```
$payment = new BankPayment($merchantId, $merchantPrivateKey, $httpReferer);
$result = $payment->order($tradeNo, $bank, $amount, $clientIp, $notifyUrl);
```
```
Result:
跳轉用的 HTML，請 render 到客戶端
```

### 網銀支付交易成功通知

同掃碼支付交易成功通知

### 網銀支付訂單查詢

同掃碼支付訂單查詢

### 網銀支付訂單支付成功查詢

同掃碼支付訂單支付成功查詢
