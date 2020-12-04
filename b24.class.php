<?php

class B24 {

	public function query($queryUrl, $queryData) {
		//global $config;
	    $curl = curl_init();
	    curl_setopt_array($curl, array(
	        CURLOPT_TIMEOUT => 20,
	        CURLOPT_CONNECTTIMEOUT => 10,
	        CURLOPT_SSL_VERIFYPEER => 0,
	        CURLOPT_POST => 1,
	        CURLOPT_HEADER => 0,
	        CURLOPT_RETURNTRANSFER => 1,
	        CURLOPT_URL => B24_WEBHOOK . $queryUrl . '/',
	        CURLOPT_POSTFIELDS => http_build_query($queryData),
	    ));
	    $exec = curl_exec($curl);
	    if(curl_errno($curl))
	  		return 'query_to_bitrix24::: Curl error: ' . curl_error($curl) . ', queryData:' . print_r($queryData, true). "ERROR";
	    $data = json_decode($exec, 1);
	    curl_close($curl);
	    if(empty($data) || is_null($data))
	    	return 'query_to_bitrix24::: DATA is null or empty: ' . print_r($data, true) . ', queryData:' . print_r($queryData, true) . "ERROR";
	      //logg('query_to_bitrix24::: DATA is null or empty: ' . print_r($data, true) . ', queryData:' . print_r($queryData, true), "ERROR");
	    elseif (array_key_exists('error', $data))
	    	return 'query_to_bitrix24::: rest api answer contains ERROR: ' . print_r($data, true) . ', queryData:' . print_r($queryData, true) . "ERROR";
	     
	    //logg("query_to_bitrix24::: {$queryUrl}::: " . print_r($queryData, true) . " ::: data:" . print_r($data, true));
	    return $data;
	}
	public function bitrix24_notify_all_users($message){
	    $users = array(1, 14, 18, 20, 22);
	    foreach ($users as $userID) {
	        $queryData['to']=$userID;
	        $queryData['message']=$message;
	        $queryData['type']="SYSTEM";
	        $this->query("im.notify", $queryData);
	        unset($queryData);
	        sleep(1);
	    }
	    #add blogpost (feed)
	    $queryData['POST_MESSAGE']=$message;
	    $this->query("log.blogpost.add", $queryData);
	    unset($queryData);
	}

	public function getContacts($phone) {
		$queryData['filter']['PHONE'] = $phone;
		$queryData['select'] = ["ID"];
		$answer = $this->query("crm.contact.list", $queryData); 
		//logg("CONTACT SEARCH result: " . print_r($answer, true));
		return $answer;
		
	}

	public function addLead($queryData = array()) {
		$answer = $this->query("crm.lead.add", $queryData); 
		return $answer;
	}

	public function addDealtoContact($queryData = array()) {
		$answer = $this->query("crm.deal.add", $queryData); 
		return $answer;	
	}
}