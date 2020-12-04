<?php

require 'b24.class.php';

/* Созадём лид, либо сделку */
$b24 = new B24();
$answer = $b24->getContacts($postData['user']['phone']); .// Ищем по номеру тел. контакт

if (!isset($answer['result'])) {
	//$oConfig->sentry->captureMessage('error empty($answer[result]) $b24->getContacts() ' .$postData['user']['phone']);
	Sentry\captureMessage('error empty($answer[result]) $b24->getContacts() ' .$postData['user']['phone']);
}
else {
	if ($answer['total'] > 1) {
		Sentry\captureMessage('$answer["total"] > 1; Total in B24: ' . $answer['total']);
		//$oConfig->sentry->captureMessage('$answer["total"] > 1; Total in B24: ' . $answer['total']); 
	//logg("total=" . $answer['total'] . ", first id found=" . $answer['result'][0]['ID']);
	//$result['CLIENT_ID'] = $answer['result'][0]['ID'];
		$queryData['fields']['TITLE'] = "Заказ № " . $order_id;
		$queryData['fields']['TYPE_ID'] = "GOODS";
		$queryData['fields']['STAGE_ID'] = "NEW";
		$queryData['fields']['CONTACT_ID'] = $answer['result'][0]['ID'];
    	$queryData['fields']['OPENED'] = "Y";
    	$queryData['fields']['CURRENCY_ID'] = 'BYR';
    	$queryData['fields']['OPPORTUNITY'] = $postData['user']['price']; //price total_price
		$queryData['params']['REGISTER_SONET_EVENT'] = "Y";
		$res = $b24->addDealtoContact($queryData);
	}
	else if($answer['total'] == 1) { // Create Deal and link with contact
		$queryData['fields']['TITLE'] = "Заказ № " . $order_id;
		$queryData['fields']['TYPE_ID'] = "GOODS";
		$queryData['fields']['STAGE_ID'] = "NEW";
		$queryData['fields']['CONTACT_ID'] = $answer['result'][0]['ID'];
    	$queryData['fields']['OPENED'] = "Y";
    	$queryData['fields']['CURRENCY_ID'] = 'BYR';
    	$queryData['fields']['OPPORTUNITY'] = $postData['user']['price'];
		$queryData['params']['REGISTER_SONET_EVENT'] = "Y";
		$queryData['fields']['ASSIGNED_BY_ID'] = "1";
		$ans = $b24->addDealtoContact($queryData);
	} 
	else { // Контакт не найден, 0 Create lead 
		$queryData['fields']['TITLE'] = "Заказ № " . $order_id;
    	$queryData['fields']['NAME'] = $name;
		$queryData['fields']['SECOND_NAME'] = $middlename; //$postData['user']['lastname'];
		$queryData['fields']['LAST_NAME'] = $lastname; //$postData['user']['lastname'];
		$queryData['fields']['ADDRESS'] = implode(', ', $address);
		$queryData['fields']['PHONE'][0]['VALUE'] = $postData['user']['phone'];
		$queryData['fields']['PHONE'][0]['VALUE_TYPE'] = 'HOME';
	 	$queryData['fields']['EMAIL'][0]['VALUE'] = $email;
		$queryData['fields']['EMAIL'][0]['VALUE_TYPE'] = 'HOME';
		$queryData['fields']['CURRENCY_ID'] = 'BYR';
		$queryData['fields']['OPPORTUNITY'] = $postData['user']['price'];
		$queryData['params']['REGISTER_SONET_EVENT'] = "Y";
		$queryData['fields']['ASSIGNED_BY_ID'] = "1";
		$ans = $b24->addLead($queryData);
	}			
}