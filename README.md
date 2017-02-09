# 云支付消息推送 v1.0

## 版本历史

v1.0 2017年02月08日

* 添加退汇成功消息推送

## 说明

云支付对用户的请求数据处理完成后，会将结果以服务器主动通知的方式通知给商户网站。
这些处理结果数据就是服务器异步通知参数。

示例：

curl -X __POST__ -H "Content-Type: __application/json__" -d '{"notify_id":"107719160414339072","partner":"testdealerid","trade_status":"REEXCHANGE_SUCCESS","sign":"c7bbc4b61be7afad07763ba9a0442dbfa5549fa1ca1ab7059ba7e80f234e87db","data":"{\"order_id\":\"201611110068650213602-realtime-test\",\"dealer_id\":\"testdealerid\",\"ref\":\"75411107795173382\",\"amount\":\"100.02\",\"sys_amount\":\"100.00\",\"broker_amount\":\"0.02\",\"broker_fee\":\"0.00\",\"sys_fee\":\"0.00\",\"name\":\"张三\",\"anchor_id\":\"56244623\"}","create_time":"2017-02-08 15:38:35","notify_time":"2017-02-08 15:38:35"}' "https://foo.bar/getnotice/stuff"

> 注意：data 的内容经过 json encode，请解析后使用。


## 通知触发条件

|触发条件（交易状态）|描述|
|-----------------|------------|
|REEXCHANGE_SUCCESS |退汇成功     |

## 通知参数

### 通知参数的数据格式

```js
{
    notify_id: '107719160414339072',
    partner: 'testdealerid',
    trade_status: 'REEXCHANGE_SUCCESS',
    sign: 'c7bbc4b61be7afad07763ba9a0442dbfa5549fa1ca1ab7059ba7e80f234e87db',
    data: '{"order_id":"201611110068650213602-realtime-test","dealer_id":"testdealerid","ref":"75411107795173382","amount":"100.02","sys_amount":"100.00","broker_amount":"0.02","broker_fee":"0.00","sys_fee":"0.00","name":"张三","anchor_id":"56244623"}',
    create_time: '2017-02-08 15:38:35',
    notify_time: '2017-02-08 15:38:35'
}
```
### 具体的业务参数

#### 1 退汇成功 REEXCHANGE_SUCCESS

```js
data: {
    order_id: '201611110068650213602-realtime-test',
    dealer_id: 'testdealerid',
    ref: '75411107795173382',
    amount: '100.02',
    sys_amount: '100.00',
    broker_amount: '0.02',
    broker_fee: '0.00',
    sys_fee: '0.00',
    name: '张三',
    anchor_id: '56244623'
},
```

## 商户通知参数合法性验证

当云支付处理完成后会把数据结果反馈给商户，商户获得这些数据时，必须进行如下处理。

### 验证签名

1. 待签名参数集合
    
    除了 sign 及 sign_type 以外，获得的参数集合都是要参与签名的。
    一般情况下，通知里的参数都是有值的，空值的参数不会出现。

1. 组装
    1. 排序

        除 sign、sign_type 字段外，所有参数按照字段名（KEY）的ASCII码从小到大排序
        
    2. 拼接

        排序后所有字段以 key1=value1&key2=value2 格式进行拼接
        
1. 调用签名验证函数 HMAC

    ```php
    # appkey 为云支付提供的通讯密钥
    $message = $message + "&key=" + $appkey
    $hash = hmac('sha256', $message, $appkey);
    ```
    
1. 签名示例

    * [demo](./sign_demo.php)

### 数据处理注意事项

1. 校验通知中的 partner 是否为商户方本身；
1. 商户方可以依据 notify_id 过滤重复的通知。

## 通知频率

程序执行完后必须打印输出 __success__。如果商户反馈给云支付的字符不是 __success__ 这7个字符，云支付服务器会不断重发通知，直到超过24小时22分钟。
一般情况下，25小时以内完成8次通知（通知的间隔频率一般是：4m,10m,10m,1h,2h,6h,15h）


