<?php

/*
运行方法：
php sign_demo.php
*/

# 商户配置
$partner = 'push_test_dealer_id';
$key = 'ajksaljasakae499u9ee9r9ew9r9a9e9';

# 通知的数据
$input = '{"notify_id":"107719160414339072","partner":"testdealerid","trade_status":"REEXCHANGE_SUCCESS","sign":"c7bbc4b61be7afad07763ba9a0442dbfa5549fa1ca1ab7059ba7e80f234e87db","data":"{\"order_id\":\"201611110068650213602-realtime-test\",\"dealer_id\":\"testdealerid\",\"ref\":\"75411107795173382\",\"amount\":\"100.02\",\"sys_amount\":\"100.00\",\"broker_amount\":\"0.02\",\"broker_fee\":\"0.00\",\"sys_fee\":\"0.00\",\"name\":\"张三\",\"anchor_id\":\"56244623\"}","create_time":"2017-02-08 15:38:35","notify_time":"2017-02-08 15:38:35"}';

# 参数列表
$params = json_decode($input, true);
echo sprintf("参数列表: %s \n", json_encode($params));

# 签名原始串
ksort($params);

$signStr = '';
foreach($params as $k => $val) {
	if ($k != 'sign' && $k != 'sign_type' && $val != '') {
		$signStr .= $k . '=' . $val . '&';
	}
}
$signStr = $signStr . "key=" . $key;
echo sprintf("签名原始串: %s \n", $signStr);

# 签名结果
$hash = hash_hmac('sha256', $signStr, $key);
echo sprintf("hmac 签名结果: %s \n", $hash);

# 比较签名
if ($hash == $params['sign']) {
	echo sprintf("签名匹配 \n");
} else {
	echo sprintf("签名不匹配 \n");
}
