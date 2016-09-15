<?php

// My common functions
//require('');

use \Illuminate\Mail\Transport\SendgridTransport;

function get_user_time($remote_tz, $origin_tz = null, $time) {
	if ($origin_tz === null) {
		if (!is_string($origin_tz = date_default_timezone_get())) {
			return false; // A UTC timestamp was returned -- bail out!
		}
	}
	$origin_dtz = new DateTimeZone($origin_tz);
	$remote_dtz = new DateTimeZone($remote_tz);
	$origin_dt = new DateTime("now", $origin_dtz);
	$remote_dt = new DateTime("now", $remote_dtz);
	$offset = $origin_dtz->getOffset($origin_dt) - $remote_dtz->getOffset($remote_dt);

	$time_new = strtotime($time) + $offset;

	$new_time = date("Y-m-d H:i:s", $time_new);
	return $new_time;
}

function date_time_differ($datetime1, $week = NULL) {
	$datetime1 = new DateTime($datetime1);
	if ($week != NULL) {
		$datetime2 = new DateTime(date("Y-m-d H:i:s", strtotime("+2 weeks")));
	} else {
		$datetime2 = new DateTime(date("Y-m-d H:i:s"));
	}
	$interval = $datetime1->diff($datetime2);
	return $interval;
}

function unlink_image($image) {
	$base_asset_url = asset_url();

	$base = str_replace($base_asset_url, '../public', $image);
	try {
		unlink($base);
	} catch (Exception $e) {
		
	}
}

function get_location($lat, $long) {
	try {
		$curl_string = "https://roads.googleapis.com/v1/snapToRoads?path=$lat,$long&key=" . Config::get('app.gcm_browser_key') . "&interpolate=true";
		$session = curl_init($curl_string);
		curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($session, CURLOPT_SSL_VERIFYPEER, false);
		$msg_chk = curl_exec($session);
		$msg_chk = json_decode($msg_chk);
		$lat1 = $msg_chk->snappedPoints[0]->location->latitude;
		$long1 = $msg_chk->snappedPoints[0]->location->longitude;
		$location = array('lat' => $lat1, 'long' => $long1);
		return $location;
	} catch (Exception $ex) {
		$location = array('lat' => $lat, 'long' => $long);
		return $location;
	}
}

function get_dist($source_lat, $source_long, $dest_lat, $dest_long) {
	/* $curl_string = "https://maps.googleapis.com/maps/api/directions/json?origin=" . $source_lat . "," . $source_long . "&destination=" . $dest_lat . "," . $dest_long . "&key=AIzaSyD6ZVevefP2THEQrOaDGNANbrnbRLmzQdA"; */
	$curl_string = "https://maps.googleapis.com/maps/api/directions/json?origin=" . $source_lat . "," . $source_long . "&destination=" . $dest_lat . "," . $dest_long . "&key=" . Config::get('app.gcm_browser_key') . "";
//url_string='www.google.com';
	$session = curl_init($curl_string);
	curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($session, CURLOPT_SSL_VERIFYPEER, false);
	$msg_chk = curl_exec($session);
	$phpObj = json_decode($msg_chk);
	/* echo $msg_chk; */
	/* print_r($phpObj); */
	/* echo "Text :- " . $phpObj->routes[0]->legs[0]->distance->text; */
	$settings = Settings::where('key', 'default_distance_unit')->first();
	$unit = $settings->value;
	if (isset($phpObj->routes[0]->legs[0]->distance->value)) {
		if ($unit == 1) {
			$dist = ($phpObj->routes[0]->legs[0]->distance->value / 1000) * 0.621371;
		} else {
			$dist = ($phpObj->routes[0]->legs[0]->distance->value / 1000);
		}
	} else {
		$dist = 0;
	}
	return $dist;
}

function get_zipcode($source_lat, $source_long) {
	/* $curl_string = "https://maps.googleapis.com/maps/api/directions/json?origin=" . $source_lat . "," . $source_long . "&destination=" . $dest_lat . "," . $dest_long . "&key=AIzaSyD6ZVevefP2THEQrOaDGNANbrnbRLmzQdA"; */
	$curl_string = "http://maps.googleapis.com/maps/api/geocode/json?latlng=" . $source_lat . "," . $source_long . "&key=" . Config::get('app.gcm_browser_key') . "&sensor=false";
//url_string='www.google.com';
	$session = curl_init($curl_string);
	curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($session, CURLOPT_SSL_VERIFYPEER, false);
	$msg_chk = curl_exec($session);
	$phpObj = json_decode($msg_chk);

	if (isset($phpObj->results[0]->address_components)) {
		$count = sizeof($phpObj->results[0]->address_components);
		$count = $count - 1;
		$zip_code = preg_replace("/[^0-9,.]/", "", $phpObj->results[0]->address_components[$count]->long_name);
		if ($zip_code == "") {
			$zip_code = 0;
		}
	} else {
		$zip_code = 0;
	}
	return trim($zip_code);
}

function my_random6_number() {
	$min = 1;
	$max = 9;
	$random_number1 = rand($min, $max);


	//first capital  
	$length = 1;

	$chars = 'ABCDEFGHJKLMNOPQRSTUVWXYZ';
	$count = strlen($chars);

	for ($i = 0, $result = ''; $i < $length; $i++) {
		$index = rand(0, $count - 1);
		$result .= substr($chars, $index, 1);
	}


	//second  capital                  


	$chars1 = 'ABCDEFGHJKLMNOPQRSTUVWXYZ';
	$count = strlen($chars1);

	for ($i = 0, $result1 = ''; $i < $length; $i++) {
		$index = rand(0, $count - 1);
		$result1 .= substr($chars1, $index, 1);
	}


	//first small           


	$smallch = 'abcdefghijkmnopqrstuvwxyz';
	$counts = strlen($smallch);

	for ($i = 0, $smallchar = ''; $i < $length; $i++) {
		$index = rand(0, $counts - 1);
		$smallchar .= substr($smallch, $index, 1);
	}

	//second small    

	$smallch2 = 'abcdefghijkmnopqrstuvwxyz';
	$counts2 = strlen($smallch2);

	for ($i = 0, $smallchar2 = ''; $i < $length; $i++) {
		$index = rand(0, $counts - 1);
		$smallchar2 .= substr($smallch2, $index, 1);
	}


	$special = array("0", "7");
	$spe_random = rand(0, 1);
	$spe = $special[$spe_random];

	$rnd = $random_number1;

	$main_no = "";

	if ($random_number1 % 2 == 0) {
		if ($random_number1 == 2) {

			$main_no = $result . $smallchar . $rnd . $smallchar2 . $spe . $result1;
		}
		if ($random_number1 == 4) {
			$main_no = $smallchar . $rnd . $smallchar2 . $spe . $result1 . $result;
		}

		if ($random_number1 == 6) {
			$main_no = $rnd . $smallchar2 . $spe . $result1 . $result . $smallchar;
		}
		if ($random_number1 == 8) {
			$main_no = $smallchar2 . $spe . $result1 . $result . $smallchar . $rnd;
		}
	}

	if ($random_number1 % 2 != 0) {
		if ($random_number1 == 1) {
			$main_no = $spe . $result1 . $result . $smallchar . $rnd . $smallchar2;
		}

		if ($random_number1 == 3) {
			$main_no = $result1 . $result . $smallchar . $rnd . $smallchar2 . $spe;
		}

		if ($random_number1 == 5) {
			$main_no = $result . $smallchar . $rnd . $smallchar2 . $spe . $result1;
		}
		if ($random_number1 == 7) {
			$main_no = $smallchar . $rnd . $smallchar2 . $spe . $result1 . $result;
		}
		if ($random_number1 == 9) {
			$main_no = $rnd . $smallchar2 . $spe . $result1 . $result . $smallchar;
		}
	}
	return strtolower($main_no);
	//echo "<br><br><br>r1-".$random_number1;
}

function get_address($source_lat, $source_long) {
	/* $curl_string = "https://maps.googleapis.com/maps/api/directions/json?origin=" . $source_lat . "," . $source_long . "&destination=" . $dest_lat . "," . $dest_long . "&key=AIzaSyD6ZVevefP2THEQrOaDGNANbrnbRLmzQdA"; */
	$curl_string = "http://maps.googleapis.com/maps/api/geocode/json?latlng=" . $source_lat . "," . $source_long . "&key=" . Config::get('app.gcm_browser_key') . "&sensor=false";
//url_string='www.google.com';
	$session = curl_init($curl_string);
	curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($session, CURLOPT_SSL_VERIFYPEER, false);
	$msg_chk = curl_exec($session);
	$phpObj = json_decode($msg_chk);

	if (isset($phpObj->results[0]->address_components)) {
		$Address = "";
		foreach ($phpObj->results[0]->address_components as $get_add) {
			$Address .=$get_add->long_name . ", ";
		}
	} else {
		$Address = "Address Not Available.";
	}
	return trim(rtrim($Address, ", "));
}

function distanceGeoPoints($lat1, $lng1, $lat2, $lng2) {

	$earthRadius = 3958.75;

	$dLat = deg2rad($lat2 - $lat1);
	$dLng = deg2rad($lng2 - $lng1);


	$a = sin($dLat / 2) * sin($dLat / 2) +
			cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
			sin($dLng / 2) * sin($dLng / 2);
	$c = 2 * atan2(sqrt($a), sqrt(1 - $a));
	$dist = $earthRadius * $c;

	// from miles
	$meterConversion = 1609;
	$geopointDistance = $dist * $meterConversion;

	return $geopointDistance;
}

function get_angle($latitudeFrom, $longitudeFrom, $latitudeTo, $longitudeTo, $earthRadius = 3958.75) {
	// convert from degrees to radians
	$latFrom = deg2rad($latitudeFrom);
	$lonFrom = deg2rad($longitudeFrom);
	$latTo = deg2rad($latitudeTo);
	$lonTo = deg2rad($longitudeTo);

	$latDelta = $latTo - $latFrom;
	$lonDelta = $lonTo - $lonFrom;

	$angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) + cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));
	return $angle * $earthRadius;
}

function sprintf2($value, $decimal = null) {
	/* $decimal = 2; */
	return sprintf("%." . $decimal . "f", $value);
}

function weektotal($totalarray) {
	$weekpayout = 0;
	foreach ($totalarray as $total) {
		//$weekpayout = $weekpayout + $total->card_payment;
		$weekpayout = $weekpayout + $total->total;
	}
	return $weekpayout;
}

function payment_remaining_total($totalarray) {
	$weekpayout = 0;
	foreach ($totalarray as $total) {
		$weekpayout = $weekpayout + $total->payment_remaining;
	}
	return $weekpayout;
}

function refund_remaining_total($totalarray) {
	$weekpayout = 0;
	foreach ($totalarray as $total) {
		$weekpayout = $weekpayout + $total->refund_remaining;
	}
	return $weekpayout;
}

function formated_value($valu) {
	$value = str_replace('.', '.', number_format((float) $valu, 2));
	return $value;
}

function patyoutday($requests, $date, $id) {
	$daypayout = 0;
	foreach ($requests as $request) {
		$newdate = date('Y-m-d', strtotime($request->date));

		if ($newdate == $date) {
			if ($id == $request->confirmed_provider) {
				$daypayout += $request->provider_commission;
			}
		}
	}
	return $daypayout;
}

function driverweek($payoutarr, $id) {
	$weekpayout = 0;
	foreach ($payoutarr as $daytotal) {

		if ($id == $daytotal->confirmed_provider) {
			$weekpayout = $weekpayout + $daytotal->card_payment;
		}
	}
	return $weekpayout;
}

function check_cache($key) {

	$time = time();
	$cash = Cash::where('key', 'like', '%' . $key . '%')->where('expiry', '>', $time)->first();

	if (isset($cash)) {
		return true;
	} else {
		return false;
	}
}

function update_cache($key, $rate) {

	$cash = Cash::where('key', 'like', '%' . $key . '%')->first();

	if ($cash != NULL) {

		$cash->value = $rate;
		$time = time() + 86400;
		$cash->expiry = $time;
		$cash->save();
	} else {
		$cash = new Cash;
		$cash->key = $key;
		$cash->value = $rate;
		$time = time() + 86400;
		$cash->expiry = $time;
		$cash->save();
	}
}

function currency_converted($total) {

	/* $currency_selected = Keywords::find(5);
	  $currency_sel = $currency_selected->keyword; */
	$currency_sel = Config::get('app.generic_keywords.Currency');
	if ($currency_sel == 'R$') {
		$currency_sel = "BRL";
	} else {
		$currency_sel = Config::get('app.generic_keywords.Currency');
	}
	if ($currency_sel != 'BRL') {
		$check = check_cache($currency_sel);

		if (!$check) {
			$url = "http://currency-api.appspot.com/api/BRL/" . $currency_sel . ".json?key=65d69f1a909b37e41272574dcd20c30fb2fbb06e";

			$result = file_get_contents($url);
			$result = json_decode($result);
			$rate = $result->rate;
			update_cache($currency_sel, $rate);
			$total = $total * $rate;
		} else {
			$rate = Cash::where('key', 'like', '%' . $currency_sel . '%')->first();
			$total = $total * $rate->value;
		}
	} else {
		$total = $total;
	}
	return $total;
}

function clean($string) {
	$string = str_replace(' ', '-', $string); // Replaces all spaces with hyphens.

	return preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.
}

function generate_token() {
	return clean(Hash::make(rand() . time() . rand()));
}

function generate_expiry() {
	return time() + 3600000;
}

function convert($value, $type) {
	if ($value > 0) {
		if ($type == 1) {
			// Miles
			return $value / 1609;
		} else {
			// KM
			return $value / 1000;
		}
	} else {
		return 0;
	}
}

function is_token_active($ts) {
	if ($ts >= time()) {
		return true;
	} else {
		return false;
	}
}

function detectCardType($num){
	$re = array(
		"visa"       => "/^4[0-9]{12}(?:[0-9]{3})?$/",
		"mastercard" => "/^5[1-5][0-9]{14}$/",
		"amex"       => "/^3[47][0-9]{13}$/",
		"discover"   => "/^6(?:011|5[0-9]{2})[0-9]{12}$/",
		"dinersclub" =>	"/^3(?:0[0-5]|[68][0-9])[0-9]{11}$/",
		"jcb"		 =>	"/^(?:2131|1800|35\d{3})\d{11}$/",
	);

	if (preg_match($re['visa'],$num)){
		return 'VISA';
	}
	else if (preg_match($re['mastercard'],$num)){
		return 'MASTERCARD';
	}
	else if (preg_match($re['amex'],$num)){
		return 'AMERICAN EXPRESS';
	}
	else if (preg_match($re['discover'],$num)){
		return 'DISCOVER';
	}
	else if (preg_match($re['dinersclub'],$num)){
		return 'DINERS CLUB';
	}
	else if (preg_match($re['jcb'],$num)){
		return 'JCB';
	}
	else{
		return 'Unknown';
	}
}

function get_html_file($filename){
	$path = app_path().'/views/emails/'.$filename.'.html';
	$content = "Missing file";
	if (file_exists($path)) {
		$content = file_get_contents($path, "r");	
	}else{
		
	}
	return $content;
}

function email_notification($id, $type, $vars, $subject, $key = null, $is_imp = null) {
	$settings = Settings::where('key', 'email_notification')->first();
	$email_notification = $settings->value;
	// pega o from das configuracoes, mas cada e-mail deveria ser possivel configurar o from

	if ($type == 'provider') {
		$user = Provider::find($id);
		$emailTo = $user->email;
		// dd($email);
	} elseif ($type == 'admin') {
		$settings = Settings::where('key', 'admin_email_address')->first();
		$emailTo = $settings->value;
		//dd($email);
	} else {
		$user = User::find($id);
		$emailTo = $user->email;
		//  dd($email);
	}
	if ($email_notification == 1 || $is_imp == "imp") {

		if(!$key)
			$key = 'layout' ;
		
		EmailTemplate::SendByKey($key, $vars, $emailTo, $subject);	

	} else{

	}
}

function send_eta_email($email, $message_body, $subject) {

	$settings = Settings::where('key', 'email_notification')->first();
	$email_notification = $settings->value;

	if ($email_notification == 1) {

		try {
			//  dd($email);
			Mail::send('emails.layout', array('vars' => $message_body), function ($message) use ($email, $subject) {
				$message->to($email)->subject($subject);
			});

			// dd('yoyo');
		} catch (Exception $e) {
			//Log::error($e->getMessage());
		}
	}
}

function sms_notification($id, $type, $message) {
	$settings = Settings::where('key', 'sms_notification')->first();
	$sms_notification = $settings->value;


	if ($sms_notification == 1) {
		if ($type == 'provider') {
			$user = Provider::find($id);
			$phone = $user->phone;
		} elseif ($type == 'admin') {
			$settings = Settings::where('key', 'admin_phone_number')->first();
			$phone = $settings->value;
		} else {
			$user = User::find($id);
			$phone = $user->phone;
		}
		$AccountSid = Config::get('app.twillo_account_sid');
		$AuthToken = Config::get('app.twillo_auth_token');
		$twillo_number = Config::get('app.twillo_number');

		$client = new Services_Twilio($AccountSid, $AuthToken);

		try {
			$message = $client->account->messages->create(array(
				"From" => $twillo_number,
				"To" => $phone,
				"Body" => $message,
			));
		} catch (Exception $e) {
			//Log::error($e->getMessage());
		}
	}
}

function send_eta($phone, $message) {
	$settings = Settings::where('key', 'sms_notification')->first();
	$sms_notification = $settings->value;



	if ($sms_notification == 1) {

		$AccountSid = Config::get('app.twillo_account_sid');
		$AuthToken = Config::get('app.twillo_auth_token');
		$twillo_number = Config::get('app.twillo_number');

		$client = new Services_Twilio($AccountSid, $AuthToken);

		try {
			$message = $client->account->messages->create(array(
				"From" => $twillo_number,
				"To" => $phone,
				"Body" => $message,
			));
		} catch (Services_Twilio_RestException $e) {
			//Log::error($e->getMessage());
		}
	}
}

/* from HelloController it jumps to the test_ios_noti() */

function test_ios_noti($id, $type, $title, $message) {
	/* $deviceTokens = array("11F1530C543DA98EF4BC013D28FF91B4906BE0EA0523DD4B0A04732CC91B4570"); */ /* ckUberForXUser.pem token */
	$deviceTokens = array($id); /* ckUberForXProvider.pem token */
	send_ios_push2($deviceTokens, $title, $message, $type);
}

function test_and_noti($id, $type, $title, $message) {
	/* $deviceTokens = array("11F1530C543DA98EF4BC013D28FF91B4906BE0EA0523DD4B0A04732CC91B4570"); */ /* ckUberForXUser.pem token */
	$deviceTokens = $id; /* ckUberForXProvider.pem token */
	send_android_push($deviceTokens, $title, $message);
}

function send_notifications($id, $type, $title, $message, $is_imp = NULL) {
	//Log::info('push notification');
	$settings = Settings::where('key', 'push_notification')->first();
	$push_notification = $settings->value;

	if ($type == 'provider') {
		$user = Provider::find($id);
	} else {
		$user = User::find($id);
	}

	if ($push_notification == 1 || $is_imp == "imp") {
		if ($user->device_type == 'ios') {
			/* WARNING:- you can't pass devicetoken as string in GCM or IOS push
			 * you have to pass array of devicetoken even thow it's only one device's token. */
			/* send_ios_push("E146C7DCCA5EBD49803278B3EE0C1825EF0FA6D6F0B1632A19F783CB02B2617B",$title,$message,$type); */
			
			send_ios_push($user->device_token, $title, $message, $type);
		} else {

			$message = json_encode($message);

			return send_android_push($user->device_token, $title, $message);
		}
	}
}

function send_ios_push($user_id, $title, $message, $type) {
	if ($type == 'provider') {
		include_once 'ios_push/provider/apns.php';

		$msg = array("alert" => $title,
			"status" => "success",
			"title" => $title,
			"message" => $message,
			"badge" => 1,
			"sound" => "default");

		if (!isset($user_id) || empty($user_id)) {
			$deviceTokens = array();
		} else {
			$deviceTokens = array(trim($user_id));
		}

		$apns = new ProviderApns();
		$apns->send_notification($deviceTokens, $msg);
	} else {
		include_once 'ios_push/apns.php';

		$msg = array("alert" => $title,
			"status" => "success",
			"title" => $title,
			"message" => $message,
			"badge" => 1,
			"sound" => "default");

		if (!isset($user_id) || empty($user_id)) {
			$deviceTokens = array();
		} else {
			$deviceTokens = array(trim($user_id));
		}

		$apns = new ClientApns();
		$apns->send_notification($deviceTokens, $msg);
	}
	/* normally we have to send three perameters to ios device which are "alert","badge","sound", if it is not in aps{} object then push will not deliver.
	 * in this array just add that veriable which's text in to "alert" you want to display in device screen as a notification
	 * "status" is my strategy to display success or Filear or push data
	 * "title" is a string which is send as a push string and i hed put it in this perameter because if ios developer wants that message then ios developer can get it from here
	 * "messsage" is a bulk of data which is send from database
	 *
	 * don't concat title & message in alert if not required.
	 *
	 * if you want ot check the json will be proper or not then you can echo "$payload" variable which is generated in "apns.php"
	 * and if you git is as a perfect json then only push data is perfect and may be send to device.
	 *
	 * i use "may" word in my sentence because if you hed made any mistake like devicetoken will not array if dubble jsonencode or etc then also it will not work.
	 *
	 * if in push you will not send perfect json then also it will not deliver to device
	 * EXAMPLE of perfect json for ios push (formate taken from your "create_request" code. and also I put a comment in it. after formated array)
	 *
	  {
	  "aps":{
	  "alert":"message",
	  "title":"title",
	  "badge":1,
	  "sound":"default",
	  "message":{
	  "unique_id":1,
	  "request_id":2,
	  "time_left_to_respond":"12 minutes",
	  "request_data":{
	  "user":{
	  "name":"first name last name",
	  "picture":"picture",
	  "phone":"+919876543210",
	  "address":"address",
	  "latitude":"22",
	  "longitude":"77",
	  "rating":1,
	  "num_rating":1
	  },
	  "dog":{
	  "name":"dog_name",
	  "age":"dog_age",
	  "breed":"dog_breed",
	  "likes":"dog_likes",
	  "picture":"dog_image"
	  }
	  }
	  }
	  }
	  }
	 */
}

function send_ios_push2($user_id, $title, $message, $type) {
	if ($type == 'provider') {
		include_once 'ios_push/provider/apns.php';
		$apns = new ProviderApns();
	} else {
		include_once 'ios_push/apns.php';
		$apns = new ClientApns();
	}
	$msg = array("alert" => "" . $title,
		"status" => "success",
		"title" => $title,
		"message" => $message,
		"badge" => 1,
		"sound" => "default");

	if (!isset($user_id) || empty($user_id)) {
		$deviceTokens = array();
	} else {
		/* here not required to make it array, it's already an array. If we assign it as an array then it will be array in array and it will not work while it pass to apns file. */
		/* to check whether it is array or variable then you can uncomment all echo's from apns files
		  now from http://54.148.195.44/test we can get the push to our company's device as I had made changes.
		 */
		$deviceTokens = $user_id;
	}

	$apns->send_notification($deviceTokens, $msg);
}

function send_android_push2($user_id, $message, $title) {
	require_once 'gcm/GCM_1.php';
	/* require_once 'gcm/const.php'; */

	if (!isset($user_id) || empty($user_id)) {
		$registatoin_ids = "0";
	} else {
		$registatoin_ids = trim($user_id);
	}
	if (!isset($message) || empty($message)) {
		$msg = "Message not set";
	} else {
		$msg = trim($message);
	}
	if (!isset($title) || empty($title)) {
		$title1 = "Message not set";
	} else {
		$title1 = trim($title);
	}

	/* $message = array(TEAM => $title1, MESSAGE => $msg); */
	$message = array('team' => $title1, 'message' => $msg);

	$gcm = new GCM();
	$registatoin_ids = array($registatoin_ids);
	$gcm->send_notification($registatoin_ids, $message);
}

function send_android_push($user_id, $message, $title) {
	require_once 'gcm/GCM_1.php';
	/* require_once 'gcm/const.php'; */

	if (!isset($user_id) || empty($user_id)) {
		$registatoin_ids = "0";
	} else {
		$registatoin_ids = trim($user_id);
	}
	if (!isset($message) || empty($message)) {
		$msg = "Message not set";
	} else {
		$msg = trim($message);
	}
	if (!isset($title) || empty($title)) {
		$title1 = "Message not set";
	} else {
		$title1 = trim($title);
	}

	/* $message = array(TEAM => $title1, MESSAGE => $msg); */
	$message = array('team' => $title1, 'message' => $msg);

	$gcm = new GCM();
	$registatoin_ids = array($registatoin_ids);
	return $gcm->send_notification($registatoin_ids, $message);
}

function asset_url() {
	return URL::to('/');
}

function web_url() {
	return URL::to('/');
}

function generate_db_config($host, $username, $password, $database) {
	return "<?php

return array(

	/*
	|--------------------------------------------------------------------------
	| PDO Fetch Style
	|--------------------------------------------------------------------------
	|
	| By default, database results will be returned as instances of the PHP
	| stdClass object; however, you may desire to retrieve records in an
	| array format for simplicity. Here you can tweak the fetch style.
	|
	*/

	'fetch' => PDO::FETCH_CLASS,

	/*
	|--------------------------------------------------------------------------
	| Default Database Connection Name
	|--------------------------------------------------------------------------
	|
	| Here you may specify which of the database connections below you wish
	| to use as your default connection for all database work. Of course
	| you may use many connections at once using the Database library.
	|
	*/

	'default' => 'mysql',

	/*
	|--------------------------------------------------------------------------
	| Database Connections
	|--------------------------------------------------------------------------
	|
	| Here are each of the database connections setup for your application.
	| Of course, examp les of configuring each database platform that is
	| supported by Laravel is shown below to make development simple.
	|
	|
	| All database work in Laravel is done through the PHP PDO facilities
	| so make sure you have the driver for your particular database of
	| choice installed on your machine before you begin development.
	|
	*/

	'connections' => array(

		'sqlite' => array(
			'driver'   => 'sqlite',
			'database' => __DIR__.'/../database/production.sqlite',
			'prefix'   => '',
		),

		'mysql' => array(
			'driver'    => 'mysql',
			'host'      => '$host',
			'database'  => '$database',
			'username'  => '$username',
			'password'  => '$password',
			'charset'   => 'utf8',
			'collation' => 'utf8_unicode_ci',
			'prefix'    => '',
		),

		'pgsql' => array(
			'driver'   => 'pgsql',
			'host'     => 'localhost',
			'database' => 'forge',
			'username' => 'forge',
			'password' => '',
			'charset'  => 'utf8',
			'prefix'   => '',
			'schema'   => 'public',
		),

		'sqlsrv' => array(
			'driver'   => 'sqlsrv',
			'host'     => 'localhost',
			'database' => 'database',
			'username' => 'root',
			'password' => '',
			'prefix'   => '',
		),

	),

	/*
	|--------------------------------------------------------------------------
	| Migration Repository Table
	|--------------------------------------------------------------------------
	|
	| This table keeps track of all the migrations that have already run for
	| your application. Using this information, we can determine which of
	| the migrations on disk haven't actually been run in the database.
	|
	*/

	'migrations' => 'migrations',

	/*
	|--------------------------------------------------------------------------
	| Redis Databases
	|--------------------------------------------------------------------------
	|
	| Redis is an open source, fast, and advanced key-value store that also
	| provides a richer set of commands than a typical key-value systems
	| such as APC or Memcached. Laravel makes it easy to dig right in.
	|
	*/

	'redis' => array(

		'cluster' => false,

		'default' => array(
			'host'     => '127.0.0.1',
			'port'     => 6379,
			'database' => 0,
		),

	),

);
";
}

function generate_generic_page_layout($body) {

	return "@extends('website.layout')

	@section('content')
		$body
	@stop

";
}

function generate_app_config($pagarme_api_key, $pagarme_encryption_key, $braintree_cse, $stripe_publishable_key, $url, $timezone, $website_title, $s3_bucket, $twillo_account_sid, $twillo_auth_token, $twillo_number, $default_payment, $stripe_secret_key, $braintree_environment, $braintree_merchant_id, $braintree_public_key, $braintree_private_key, $customer_certy_url = null, $customer_certy_pass = null, $customer_certy_type = null, $provider_certy_url = null, $provider_certy_pass = null, $provider_certy_type = null, $gcm_browser_key = null, $key_provider = null, $key_user = null, $key_taxi = null, $key_trip = null, $key_currency = null, $total_trip = null, $cancelled_trip = null, $total_payment = null, $completed_trip = null, $card_payment = null, $credit_payment = null, $key_ref_pre = null, $android_client_app_url = null, $android_provider_app_url = null, $ios_client_app_url = null, $ios_provider_app_url = null, $cash_payment = null, $promotional_payment = null, $schedules_icon = null) {
	if ($key_ref_pre != null) {
		$key_ref_pre = $key_ref_pre;
	} else {
		$key_ref_pre = Config::get('app.referral_prefix');
	}
	if ($cash_payment != null) {
		$cash_payment = $cash_payment;
	} else {
		$cash_payment = Config::get('app.generic_keywords.cash_payment');
	}
	if ($promotional_payment != null) {
		$promotional_payment = $promotional_payment;
	} else {
		$promotional_payment = Config::get('app.generic_keywords.promotional_payment');
	}
	if ($schedules_icon != null) {
		$schedules_icon = $schedules_icon;
	} else {
		$schedules_icon = Config::get('app.generic_keywords.schedules_icon');
	}
	if ($key_provider != null) {
		$key_provider = $key_provider;
	} else {
		$key_provider = Config::get('app.generic_keywords.Provider');
	}
	if ($key_user != null) {
		$key_user = $key_user;
	} else {
		$key_user = Config::get('app.generic_keywords.User');
	}
	if ($key_taxi != null) {
		$key_taxi = $key_taxi;
	} else {
		$key_taxi = Config::get('app.generic_keywords.Services');
	}
	if ($key_trip != null) {
		$key_trip = $key_trip;
	} else {
		$key_trip = Config::get('app.generic_keywords.Trip');
	}
	if ($key_currency != null) {
		$key_currency = $key_currency;
	} else {
		$key_currency = Config::get('app.generic_keywords.Currency');
	}
	if ($total_trip != null) {
		$total_trip = $total_trip;
	} else {
		$total_trip = Config::get('app.generic_keywords.total_trip');
	}
	if ($cancelled_trip != null) {
		$cancelled_trip = $cancelled_trip;
	} else {
		$cancelled_trip = Config::get('app.generic_keywords.cancelled_trip');
	}
	if ($total_payment != null) {
		$total_payment = $total_payment;
	} else {
		$total_payment = Config::get('app.generic_keywords.total_payment');
	}
	if ($completed_trip != null) {
		$completed_trip = $completed_trip;
	} else {
		$completed_trip = Config::get('app.generic_keywords.completed_trip');
	}
	if ($card_payment != null) {
		$card_payment = $card_payment;
	} else {
		$card_payment = Config::get('app.generic_keywords.card_payment');
	}
	if ($credit_payment != null) {
		$credit_payment = $credit_payment;
	} else {
		$credit_payment = Config::get('app.generic_keywords.credit_payment');
	}
	if ($customer_certy_url != null) {
		$customer_certy_url = $customer_certy_url;
	} else {
		$customer_certy_url = Config::get('app.customer_certy_url');
	}
	if ($customer_certy_pass != null) {
		$customer_certy_pass = $customer_certy_pass;
	} else {
		$customer_certy_pass = Config::get('app.customer_certy_pass');
	}
	if ($customer_certy_type != null) {
		$customer_certy_type = $customer_certy_type;
	} else {
		$customer_certy_type = Config::get('app.customer_certy_type');
	}
	if ($provider_certy_url != null) {
		$provider_certy_url = $provider_certy_url;
	} else {
		$provider_certy_url = Config::get('app.provider_certy_url');
	}
	if ($provider_certy_pass != null) {
		$provider_certy_pass = $provider_certy_pass;
	} else {
		$provider_certy_pass = Config::get('app.provider_certy_pass');
	}
	if ($provider_certy_type != null) {
		$provider_certy_type = $provider_certy_type;
	} else {
		$provider_certy_type = Config::get('app.provider_certy_type');
	}
	if ($gcm_browser_key != null) {
		$gcm_browser_key = $gcm_browser_key;
	} else {
		$gcm_browser_key = Config::get('app.gcm_browser_key');
	}
	if ($android_client_app_url != null) {
		$android_client_app_url = $android_client_app_url;
	} else {
		$android_client_app_url = Config::get('app.android_client_app_url');
	}
	if ($android_provider_app_url != null) {
		$android_provider_app_url = $android_provider_app_url;
	} else {
		$android_provider_app_url = Config::get('app.android_provider_app_url');
	}
	if ($ios_client_app_url != null) {
		$ios_client_app_url = $ios_client_app_url;
	} else {
		$ios_client_app_url = Config::get('app.ios_client_app_url');
	}
	if ($ios_provider_app_url != null) {
		$ios_provider_app_url = $ios_provider_app_url;
	} else {
		$ios_provider_app_url = Config::get('app.ios_provider_app_url');
	}

	if($url == null)
		$url = Config::get('app.url');

	return "<?php

return array(

	/*
	|--------------------------------------------------------------------------
	| Application Debug Mode
	|--------------------------------------------------------------------------
	|
	| When your application is in debug mode, detailed error messages with
	| stack traces will be shown on every error that occurs within your
	| application. If disabled, a simple generic error page is shown.
	|
	*/

	'debug' => TRUE,

	/*
	|--------------------------------------------------------------------------
	| Application URL
	|--------------------------------------------------------------------------
	|
	| This URL is used by the console to properly generate URLs when using
	| the Artisan command line tool. You should set this to the root of
	| your application so that it is used when running Artisan tasks.
	|
	*/

	'url' => '" . $url . "',

	/*
	|--------------------------------------------------------------------------
	| Application Timezone
	|--------------------------------------------------------------------------
	|
	| Here you may specify the default timezone for your application, which
	| will be used by the PHP date and date-time functions. We have gone
	| ahead and set this to a sensible default for you out of the box.
	|
	*/

	'timezone' => '" . Config::get('app.timezone') . "',

	/*
	|--------------------------------------------------------------------------
	| Application Locale Configuration
	|--------------------------------------------------------------------------
	|
	| The application locale determines the default locale that will be used
	| by the translation service provider. You are free to set this value
	| to any of the locales which will be supported by the application.
	|
	*/

	'locale' => '" . Config::get('app.locale') . "',

	/*
	|--------------------------------------------------------------------------
	| Application Fallback Locale
	|--------------------------------------------------------------------------
	|
	| The fallback locale determines the locale to use when the current one
	| is not available. You may change the value to correspond to any of
	| the language folders that are provided through your application.
	|
	*/

	'fallback_locale' => '" . Config::get('app.fallback_locale') . "',

	/*
	|--------------------------------------------------------------------------
	| Encryption Key
	|--------------------------------------------------------------------------
	|
	| This key is used by the Illuminate encrypter service and should be set
	| to a random, 32 character string, otherwise these encrypted strings
	| will not be safe. Please do this before deploying an application!
	|
	*/

	'key' => '" . Config::get('app.key') . "',

	'cipher' => MCRYPT_RIJNDAEL_128,

	/*
	|--------------------------------------------------------------------------
	| Autoloaded Service Providers
	|--------------------------------------------------------------------------
	|
	| The service providers listed here will be automatically loaded on the
	| request to your application. Feel free to add your own services to
	| this array to grant expanded functionality to your applications.
	|
	*/

	'providers' => array(

		'Illuminate\Foundation\Providers\ArtisanServiceProvider',
		'Illuminate\Auth\AuthServiceProvider',
		'Illuminate\Cache\CacheServiceProvider',
		'Illuminate\Session\CommandsServiceProvider',
		'Illuminate\Foundation\Providers\ConsoleSupportServiceProvider',
		'Illuminate\Routing\ControllerServiceProvider',
		'Illuminate\Cookie\CookieServiceProvider',
		'Illuminate\Database\DatabaseServiceProvider',
		'Illuminate\Encryption\EncryptionServiceProvider',
		'Illuminate\Filesystem\FilesystemServiceProvider',
		'Illuminate\Hashing\HashServiceProvider',
		'Illuminate\Html\HtmlServiceProvider',
		'Illuminate\Log\LogServiceProvider',
		'Illuminate\Mail\MailServiceProvider',
		'Illuminate\Database\MigrationServiceProvider',
		'Illuminate\Pagination\PaginationServiceProvider',
		'Illuminate\Queue\QueueServiceProvider',
		'Illuminate\Redis\RedisServiceProvider',
		'Illuminate\Remote\RemoteServiceProvider',
		'Illuminate\Auth\Reminders\ReminderServiceProvider',
		'Illuminate\Database\SeedServiceProvider',
		'Illuminate\Session\SessionServiceProvider',
		'Illuminate\Translation\TranslationServiceProvider',
		'Illuminate\Validation\ValidationServiceProvider',
		'Illuminate\View\ViewServiceProvider',
		'Illuminate\Workbench\WorkbenchServiceProvider',
		'Aws\Laravel\AwsServiceProvider',
		'Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider',
		'Way\Generators\GeneratorsServiceProvider',
		'Raahul\LarryFour\LarryFourServiceProvider',
		'Davibennun\LaravelPushNotification\LaravelPushNotificationServiceProvider',
		'Intervention\Image\ImageServiceProvider',
		'Barryvdh\DomPDF\ServiceProvider',
	),

	/*
	|--------------------------------------------------------------------------
	| Service Provider Manifest
	|--------------------------------------------------------------------------
	|
	| The service provider manifest is used by Laravel to lazy load service
	| providers which are not needed for each request, as well to keep a
	| list of all of the services. Here, you may set its storage spot.
	|
	*/

	'manifest' => storage_path().'/meta',

	/*
	|--------------------------------------------------------------------------
	| Class Aliases
	|--------------------------------------------------------------------------
	|
	| This array of class aliases will be registered when this application
	| is started. However, feel free to register as many as you wish as
	| the aliases are lazy loaded so they don't hinder performance.
	|
	*/

	'aliases' => array(

		'App'               => 'Illuminate\Support\Facades\App',
		'Artisan'           => 'Illuminate\Support\Facades\Artisan',
		'Auth'              => 'Illuminate\Support\Facades\Auth',
		'Blade'             => 'Illuminate\Support\Facades\Blade',
		'Cache'             => 'Illuminate\Support\Facades\Cache',
		'ClassLoader'       => 'Illuminate\Support\ClassLoader',
		'Config'            => 'Illuminate\Support\Facades\Config',
		'Controller'        => 'Illuminate\Routing\Controller',
		'Cookie'            => 'Illuminate\Support\Facades\Cookie',
		'Crypt'             => 'Illuminate\Support\Facades\Crypt',
		'DB'                => 'Illuminate\Support\Facades\DB',
		'Eloquent'          => 'Illuminate\Database\Eloquent\Model',
		'Event'             => 'Illuminate\Support\Facades\Event',
		'File'              => 'Illuminate\Support\Facades\File',
		'Form'              => 'Illuminate\Support\Facades\Form',
		'Hash'              => 'Illuminate\Support\Facades\Hash',
		'HTML'              => 'Illuminate\Support\Facades\HTML',
		'Input'             => 'Illuminate\Support\Facades\Input',
		'Lang'              => 'Illuminate\Support\Facades\Lang',
		'Log'               => 'Illuminate\Support\Facades\Log',
		'Mail'              => 'Illuminate\Support\Facades\Mail',
		'Paginator'         => 'Illuminate\Support\Facades\Paginator',
		'Password'          => 'Illuminate\Support\Facades\Password',
		'Queue'             => 'Illuminate\Support\Facades\Queue',
		'Redirect'          => 'Illuminate\Support\Facades\Redirect',
		'Redis'             => 'Illuminate\Support\Facades\Redis',
		'Request'           => 'Illuminate\Support\Facades\Request',
		'Response'          => 'Illuminate\Support\Facades\Response',
		'Route'             => 'Illuminate\Support\Facades\Route',
		'Schema'            => 'Illuminate\Support\Facades\Schema',
		'Seeder'            => 'Illuminate\Database\Seeder',
		'Session'           => 'Illuminate\Support\Facades\Session',
		'SoftDeletingTrait' => 'Illuminate\Database\Eloquent\SoftDeletingTrait',
		'SSH'               => 'Illuminate\Support\Facades\SSH',
		'Str'               => 'Illuminate\Support\Str',
		'URL'               => 'Illuminate\Support\Facades\URL',
		'Validator'         => 'Illuminate\Support\Facades\Validator',
		'View'              => 'Illuminate\Support\Facades\View',
		'AWS' => 'Aws\Laravel\AwsFacade',
		'PushNotification' => 'Davibennun\LaravelPushNotification\Facades\PushNotification',
		'Image' => 'Intervention\Image\Facades\Image',
		'PDF' => 'Barryvdh\DomPDF\Facade',
	),
	'menu_titles' => array(
		'admin_control' => '" . Config::get('app.menu_titles.admin_control') . "',
		'income_history' => '" . Config::get('app.menu_titles.income_history') . "',
		'log_out' => '" . Config::get('app.menu_titles.log_out') . "',
		'dashboard' => '" . Config::get('app.menu_titles.dashboard') . "',
		'map_view' => '" . Config::get('app.menu_titles.map_view') . "',
		'providers' => '" . Config::get('app.menu_titles.providers') . "',
		'requests' => '" . Config::get('app.menu_titles.requests') . "',
		'customers' => '" . Config::get('app.menu_titles.customers') . "',
		'reviews' => '" . Config::get('app.menu_titles.reviews') . "',
		'information' => '" . Config::get('app.menu_titles.information') . "',
		'types' => '" . Config::get('app.menu_titles.types') . "',
		'documents' => '" . Config::get('app.menu_titles.documents') . "',
		'settings' => '" . Config::get('app.menu_titles.settings') . "',
		'balance' => '" . Config::get('app.menu_titles.balance') . "',
		'create_request' => '" . Config::get('app.menu_titles.create_request') . "',
		'promotional_codes' => '" . Config::get('app.menu_titles.promotional_codes') . "',
	),
	'generic_keywords'=> array(
		'Provider' => '$key_provider',
		'User' => '$key_user',
		'Services' => '$key_taxi',
		'Trip' => '$key_trip',
		'Currency' => '$key_currency',
		'total_trip' => '$total_trip',
		'cancelled_trip' => '$cancelled_trip',
		'total_payment' => '$total_payment',
		'completed_trip' => '$completed_trip',
		'card_payment' => '$card_payment',
		'credit_payment' => '$credit_payment',
		'cash_payment' => '$cash_payment',
		'promotional_payment' => '$promotional_payment',
		'schedules_icon' => '$schedules_icon',
	),
	/* DEVICE PUSH NOTIFICATION DETAILS */
	'customer_certy_url' => '" . $customer_certy_url . "',
	'customer_certy_pass' => '" . $customer_certy_pass . "',
	'customer_certy_type' => '" . $customer_certy_type . "',
	'provider_certy_url' => '" . $provider_certy_url . "',
	'provider_certy_pass' => '" . $provider_certy_pass . "',
	'provider_certy_type' => '" . $provider_certy_type . "',
	'gcm_browser_key' => '" . $gcm_browser_key . "',
	/* DEVICE PUSH NOTIFICATION DETAILS END */
	'currency_symb' => '$key_currency', 
	
	/* Developer Company Details */
	'developer_company_name' => '" . Config::get('app.developer_company_name') . "',
	'developer_company_web_link' => '" . Config::get('app.developer_company_web_link') . "', 
	'developer_company_email' => '" . Config::get('app.developer_company_email') . "', 
	'developer_company_fb_link' => '" . Config::get('app.developer_company_fb_link') . "', 
	'developer_company_twitter_link' => '" . Config::get('app.developer_company_twitter_link') . "',
	/* Developer Company Details END */
	
	/* APP LINK DATA */
	'android_client_app_url'=>'" . $android_client_app_url . "',
	'android_provider_app_url'=>'" . $android_provider_app_url . "',
	'ios_client_app_url'=>'" . $ios_client_app_url . "',
	'ios_provider_app_url'=>'" . $ios_provider_app_url . "',
	/* APP LINK DATA END */
	
	'no_data_available' => '" . Config::get('app.no_data_available') . "', 
	'data_not_available' => '" . Config::get('app.data_not_available') . "', 
	'blank_fiend_val' => '" . Config::get('app.blank_fiend_val') . "',

	'website_title' => '$website_title',
	'referral_prefix' => '$key_ref_pre',
	'datenow'=>'" . Config::get('app.datenow') . "',
	'appdate'=>'" . Config::get('app.appdate') . "',
	'referral_zero_len' => " . Config::get('app.referral_zero_len') . ",
	'website_meta_description' => '" . Config::get('app.website_meta_description') . "',
	'website_meta_keywords' => '" . Config::get('app.website_meta_keywords') . "',

	's3_bucket' => '$s3_bucket',

	'twillo_account_sid' => '$twillo_account_sid',
	'twillo_auth_token' => '$twillo_auth_token',
	'twillo_number' => '$twillo_number',

	'production' => false,

	'default_payment' => '$default_payment',

    'pagarme_api_key' => '$pagarme_api_key',
    'pagarme_encryption_key' => '$pagarme_encryption_key',
	'stripe_secret_key' => '$stripe_secret_key',
	'stripe_publishable_key' => '$stripe_publishable_key',
	'braintree_environment' => '$braintree_environment',
	'braintree_merchant_id' => '$braintree_merchant_id',
	'braintree_public_key' => '$braintree_public_key',
	'braintree_private_key' => '$braintree_private_key',
	'braintree_cse' => '$braintree_cse',
		
	'coinbaseAPIKey' => '" . Config::get('app.coinbaseAPIKey') . "',
	'coinbaseAPISecret' => '" . Config::get('app.coinbaseAPISecret') . "',

	'paypal_sdk_mode' => '" . Config::get('app.paypal_sdk_mode') . "',
	'paypal_sdk_UserName' => '" . Config::get('app.paypal_sdk_UserName') . "',
	'paypal_sdk_Password' => '" . Config::get('app.paypal_sdk_Password') . "',
	'paypal_sdk_Signature' => '" . Config::get('app.paypal_sdk_Signature') . "',
	'paypal_sdk_AppId' => '" . Config::get('app.paypal_sdk_AppId') . "',

);
";
}

function generate_custome_key($dashboard, $map_view, $provider, $user, $taxi, $trip, $walk, $request, $reviews, $information, $types, $documents, $promo_codes, $customize, $payment_details, $settings, $val_admin, $admin_control, $log_out, $schedule, $weekstatement) {
	return "<?php
return array(

	'Dashboard' => '$dashboard',
	'map_view' => '$map_view',
	'Reviews' => '$reviews',
	'Information' => '$information',
	'Types' => '$types',
	'Documents' => '$documents',
	'promo_codes' => '$promo_codes',
	'Customize' => '$customize',
	'payment_details' => '$payment_details',
	'Settings' => '$settings',
	'Admin' => '$val_admin',
	'admin_control' => '$admin_control',
	'log_out' => '$log_out',
	'Provider' => '$provider',
	'User' => '$user',
	'Taxi' => '$taxi',
	'Trip' => '$trip',
	'Walk' => '$walk',
	'Request' => '$request',
	'Schedules' => '$schedule',
	'WeekStatement' => '$weekstatement',
);
";
}

function import_db($mysql_username, $mysql_password, $mysql_host, $mysql_database) {
	// Name of the file
	$filename = public_path() . '/uberx.sql';


	// Connect to MySQL server
	$db_conn = mysqli_connect($mysql_host, $mysql_username, $mysql_password, $mysql_database) or die('Error connecting to MySQL server: ' . mysql_error());
	// Select database
	//mysql_select_db($mysql_database) or die('Error selecting MySQL database: ' . mysql_error());
	// Temporary variable, used to store current query
	$templine = '';
	// Read in entire file
	$lines = file($filename);
	// Loop through each line
	foreach ($lines as $line) {
		// Skip it if it's a comment
		if (substr($line, 0, 2) == '--' || $line == '')
			continue;

		// Add this line to the current segment
		$templine .= $line;
		// If it has a semicolon at the end, it's the end of the query
		if (substr(trim($line), -1, 1) == ';') {
			// Perform the query
			mysqli_query($db_conn, $templine) or print('Error performing query \'<strong>' . $templine . '\': ' . mysql_error() . '<br /><br />');
			// Reset temp variable to empty
			$templine = '';
		}
	}
	//echo "Tables imported successfully";
}

function generate_mail_config($host, $mail_driver, $email_name, $email_address) {

	return "<?php

return array(

	/*
	|--------------------------------------------------------------------------
	| Mail Driver
	|--------------------------------------------------------------------------
	|
	| Laravel supports both SMTP and PHP's 'mail' function as drivers for the
	| sending of e-mail. You may specify which one you're using throughout
	| your application here. By default, Laravel is setup for SMTP mail.
	|
	| Supported: 'smtp', 'mail', 'sendmail', 'mailgun', 'mandrill', 'log', 'sendgrid'
	|
	*/

	'driver' => '$mail_driver',

	/*
	|--------------------------------------------------------------------------
	| SMTP Host Address
	|--------------------------------------------------------------------------
	|
	| Here you may provide the host address of the SMTP server used by your
	| applications. A default option is provided that is compatible with
	| the Mailgun mail service which will provide reliable deliveries.
	|
	*/

	'host' => '$host',

	/*
	|--------------------------------------------------------------------------
	| SMTP Host Port
	|--------------------------------------------------------------------------
	|
	| This is the SMTP port used by your application to deliver e-mails to
	| users of the application. Like the host we have set this value to
	| stay compatible with the Mailgun e-mail application by default.
	|
	*/

	'port' => 587,

	/*
	|--------------------------------------------------------------------------
	| Global 'From' Address
	|--------------------------------------------------------------------------
	|
	| You may wish for all e-mails sent by your application to be sent from
	| the same address. Here, you may specify a name and address that is
	| used globally for all e-mails that are sent by your application.
	|
	*/

	'from' => array('address' => '$email_address', 'name' => '$email_name'),

	/*
	|--------------------------------------------------------------------------
	| E-Mail Encryption Protocol
	|--------------------------------------------------------------------------
	|
	| Here you may specify the encryption protocol that should be used when
	| the application send e-mail messages. A sensible default using the
	| transport layer security protocol should provide great security.
	|
	*/

	'encryption' => 'tls',

	/*
	|--------------------------------------------------------------------------
	| SMTP Server Username
	|--------------------------------------------------------------------------
	|
	| If your SMTP server requires a username for authentication, you should
	| set it here. This will get used to authenticate with your server on
	| connection. You may also set the 'password' value below this one.
	|
	*/

	'username' => null,

	/*
	|--------------------------------------------------------------------------
	| SMTP Server Password
	|--------------------------------------------------------------------------
	|
	| Here you may set the password required by your SMTP server to send out
	| messages from your application. This will be given to the server on
	| connection so that the application will be able to send messages.
	|
	*/

	'password' => null,

	/*
	|--------------------------------------------------------------------------
	| Sendmail System Path
	|--------------------------------------------------------------------------
	|
	| When using the 'sendmail' driver to send e-mails, we will need to know
	| the path to where Sendmail lives on this server. A default path has
	| been provided here, which will work well on most of your systems.
	|
	*/

	'sendmail' => '/usr/sbin/sendmail -bs',

	/*
	|--------------------------------------------------------------------------
	| Mail 'Pretend'
	|--------------------------------------------------------------------------
	|
	| When this option is enabled, e-mail will not actually be sent over the
	| web and will instead be written to your application's logs files so
	| you may inspect the message. This is great for local development.
	|
	*/

	'pretend' => false,

);
";
}

function generate_services_config($mandrill_secret, $mandrill_username, $sendgrid_secret, $sendgrid_username) {

	return "<?php

return array(

	/*
	|--------------------------------------------------------------------------
	| Third Party Services
	|--------------------------------------------------------------------------
	|
	| This file is for storing the credentials for third party services such
	| as Stripe, Mailgun, Mandrill, and others. This file provides a sane
	| default location for this type of information, allowing packages
	| to have a conventional place to find your various credentials.
	|
	*/

	'mailgun' => array(
		'domain' => '',
		'secret' => '',
	),

	'mandrill' => array(
		'secret' => '$mandrill_secret',
		'username' => '$mandrill_username',
	),

	'sendgrid' => array(
		'secret' => '$sendgrid_secret',
		'username' => '$sendgrid_username',
	),

	'stripe' => array(
		'model'  => 'User',
		'secret' => '',
	),

);
";
}

// formating functions

/**
 * Sanitizes a title, replacing whitespace and a few other characters with dashes.
 *
 * Limits the output to alphanumeric characters, underscore (_) and dash (-).
 * Whitespace becomes a dash.
 *
 * @since 1.2.0
 *
 * @param string $title     The title to be sanitized.
 * @param string $raw_title Optional. Not used.
 * @param string $context   Optional. The operation for which the string is sanitized.
 * @return string The sanitized title.
 */
function sanitize_title_with_dashes( $title, $raw_title = '', $context = 'save' ) {
	$title = remove_accents($title);

	$title = strip_tags($title);
	// Preserve escaped octets.
	$title = preg_replace('|%([a-fA-F0-9][a-fA-F0-9])|', '---$1---', $title);
	// Remove percent signs that are not part of an octet.
	$title = str_replace('%', '', $title);
	// Restore octets.
	$title = preg_replace('|---([a-fA-F0-9][a-fA-F0-9])---|', '%$1', $title);

	if (seems_utf8($title)) {
		if (function_exists('mb_strtolower')) {
			$title = mb_strtolower($title, 'UTF-8');
		}
		$title = utf8_uri_encode($title, 200);
	}

	$title = strtolower($title);

	if ( 'save' == $context ) {

		// Convert nbsp, ndash and mdash to hyphens
		$title = str_replace( array( '%c2%a0', '%e2%80%93', '%e2%80%94' ), '-', $title );
		// Convert nbsp, ndash and mdash HTML entities to hyphens
		$title = str_replace( array( '&nbsp;', '&#160;', '&ndash;', '&#8211;', '&mdash;', '&#8212;' ), '-', $title );

		// Strip these characters entirely
		$title = str_replace( array(
			// iexcl and iquest
			'%c2%a1', '%c2%bf',
			// angle quotes
			'%c2%ab', '%c2%bb', '%e2%80%b9', '%e2%80%ba',
			// curly quotes
			'%e2%80%98', '%e2%80%99', '%e2%80%9c', '%e2%80%9d',
			'%e2%80%9a', '%e2%80%9b', '%e2%80%9e', '%e2%80%9f',
			// copy, reg, deg, hellip and trade
			'%c2%a9', '%c2%ae', '%c2%b0', '%e2%80%a6', '%e2%84%a2',
			// acute accents
			'%c2%b4', '%cb%8a', '%cc%81', '%cd%81',
			// grave accent, macron, caron
			'%cc%80', '%cc%84', '%cc%8c',
		), '', $title );

		// Convert times to x
		$title = str_replace( '%c3%97', 'x', $title );
	}

	$title = preg_replace('/&.+?;/', '', $title); // kill entities
	$title = str_replace('.', '-', $title);

	$title = preg_replace('/[^%a-z0-9 _-]/', '', $title);
	$title = preg_replace('/\s+/', '-', $title);
	$title = preg_replace('|-+|', '-', $title);
	$title = trim($title, '-');

	return $title;
}

/**
 * Encode the Unicode values to be used in the URI.
 *
 * @since 1.5.0
 *
 * @param string $utf8_string
 * @param int    $length Max  length of the string
 * @return string String with Unicode encoded for URI.
 */
function utf8_uri_encode( $utf8_string, $length = 0 ) {
	$unicode = '';
	$values = array();
	$num_octets = 1;
	$unicode_length = 0;

	//mbstring_binary_safe_encoding();
	$string_length = strlen( $utf8_string );
	//reset_mbstring_encoding();

	for ($i = 0; $i < $string_length; $i++ ) {

		$value = ord( $utf8_string[ $i ] );

		if ( $value < 128 ) {
			if ( $length && ( $unicode_length >= $length ) )
				break;
			$unicode .= chr($value);
			$unicode_length++;
		} else {
			if ( count( $values ) == 0 ) {
				if ( $value < 224 ) {
					$num_octets = 2;
				} elseif ( $value < 240 ) {
					$num_octets = 3;
				} else {
					$num_octets = 4;
				}
			}

			$values[] = $value;

			if ( $length && ( $unicode_length + ($num_octets * 3) ) > $length )
				break;
			if ( count( $values ) == $num_octets ) {
				for ( $j = 0; $j < $num_octets; $j++ ) {
					$unicode .= '%' . dechex( $values[ $j ] );
				}

				$unicode_length += $num_octets * 3;

				$values = array();
				$num_octets = 1;
			}
		}
	}

	return $unicode;
}

/**
 * Checks to see if a string is utf8 encoded.
 *
 * NOTE: This function checks for 5-Byte sequences, UTF8
 *       has Bytes Sequences with a maximum length of 4.
 *
 * @author bmorel at ssi dot fr (modified)
 * @since 1.2.1
 *
 * @param string $str The string to be checked
 * @return bool True if $str fits a UTF-8 model, false otherwise.
 */
function seems_utf8( $str ) {
	//mbstring_binary_safe_encoding();
	$length = strlen($str);
	//reset_mbstring_encoding();
	for ($i=0; $i < $length; $i++) {
		$c = ord($str[$i]);
		if ($c < 0x80) $n = 0; // 0bbbbbbb
		elseif (($c & 0xE0) == 0xC0) $n=1; // 110bbbbb
		elseif (($c & 0xF0) == 0xE0) $n=2; // 1110bbbb
		elseif (($c & 0xF8) == 0xF0) $n=3; // 11110bbb
		elseif (($c & 0xFC) == 0xF8) $n=4; // 111110bb
		elseif (($c & 0xFE) == 0xFC) $n=5; // 1111110b
		else return false; // Does not match any model
		for ($j=0; $j<$n; $j++) { // n bytes matching 10bbbbbb follow ?
			if ((++$i == $length) || ((ord($str[$i]) & 0xC0) != 0x80))
				return false;
		}
	}
	return true;
}

/**
 * Converts all accent characters to ASCII characters.
 *
 * If there are no accent characters, then the string given is just returned.
 *
 * @since 1.2.1
 *
 * @param string $string Text that might have accent characters
 * @return string Filtered string with replaced "nice" characters.
 */
function remove_accents( $string ) {
	if ( !preg_match('/[\x80-\xff]/', $string) )
		return $string;

	if (seems_utf8($string)) {
		$chars = array(
		// Decompositions for Latin-1 Supplement
		chr(194).chr(170) => 'a', chr(194).chr(186) => 'o',
		chr(195).chr(128) => 'A', chr(195).chr(129) => 'A',
		chr(195).chr(130) => 'A', chr(195).chr(131) => 'A',
		chr(195).chr(132) => 'A', chr(195).chr(133) => 'A',
		chr(195).chr(134) => 'AE',chr(195).chr(135) => 'C',
		chr(195).chr(136) => 'E', chr(195).chr(137) => 'E',
		chr(195).chr(138) => 'E', chr(195).chr(139) => 'E',
		chr(195).chr(140) => 'I', chr(195).chr(141) => 'I',
		chr(195).chr(142) => 'I', chr(195).chr(143) => 'I',
		chr(195).chr(144) => 'D', chr(195).chr(145) => 'N',
		chr(195).chr(146) => 'O', chr(195).chr(147) => 'O',
		chr(195).chr(148) => 'O', chr(195).chr(149) => 'O',
		chr(195).chr(150) => 'O', chr(195).chr(153) => 'U',
		chr(195).chr(154) => 'U', chr(195).chr(155) => 'U',
		chr(195).chr(156) => 'U', chr(195).chr(157) => 'Y',
		chr(195).chr(158) => 'TH',chr(195).chr(159) => 's',
		chr(195).chr(160) => 'a', chr(195).chr(161) => 'a',
		chr(195).chr(162) => 'a', chr(195).chr(163) => 'a',
		chr(195).chr(164) => 'a', chr(195).chr(165) => 'a',
		chr(195).chr(166) => 'ae',chr(195).chr(167) => 'c',
		chr(195).chr(168) => 'e', chr(195).chr(169) => 'e',
		chr(195).chr(170) => 'e', chr(195).chr(171) => 'e',
		chr(195).chr(172) => 'i', chr(195).chr(173) => 'i',
		chr(195).chr(174) => 'i', chr(195).chr(175) => 'i',
		chr(195).chr(176) => 'd', chr(195).chr(177) => 'n',
		chr(195).chr(178) => 'o', chr(195).chr(179) => 'o',
		chr(195).chr(180) => 'o', chr(195).chr(181) => 'o',
		chr(195).chr(182) => 'o', chr(195).chr(184) => 'o',
		chr(195).chr(185) => 'u', chr(195).chr(186) => 'u',
		chr(195).chr(187) => 'u', chr(195).chr(188) => 'u',
		chr(195).chr(189) => 'y', chr(195).chr(190) => 'th',
		chr(195).chr(191) => 'y', chr(195).chr(152) => 'O',
		// Decompositions for Latin Extended-A
		chr(196).chr(128) => 'A', chr(196).chr(129) => 'a',
		chr(196).chr(130) => 'A', chr(196).chr(131) => 'a',
		chr(196).chr(132) => 'A', chr(196).chr(133) => 'a',
		chr(196).chr(134) => 'C', chr(196).chr(135) => 'c',
		chr(196).chr(136) => 'C', chr(196).chr(137) => 'c',
		chr(196).chr(138) => 'C', chr(196).chr(139) => 'c',
		chr(196).chr(140) => 'C', chr(196).chr(141) => 'c',
		chr(196).chr(142) => 'D', chr(196).chr(143) => 'd',
		chr(196).chr(144) => 'D', chr(196).chr(145) => 'd',
		chr(196).chr(146) => 'E', chr(196).chr(147) => 'e',
		chr(196).chr(148) => 'E', chr(196).chr(149) => 'e',
		chr(196).chr(150) => 'E', chr(196).chr(151) => 'e',
		chr(196).chr(152) => 'E', chr(196).chr(153) => 'e',
		chr(196).chr(154) => 'E', chr(196).chr(155) => 'e',
		chr(196).chr(156) => 'G', chr(196).chr(157) => 'g',
		chr(196).chr(158) => 'G', chr(196).chr(159) => 'g',
		chr(196).chr(160) => 'G', chr(196).chr(161) => 'g',
		chr(196).chr(162) => 'G', chr(196).chr(163) => 'g',
		chr(196).chr(164) => 'H', chr(196).chr(165) => 'h',
		chr(196).chr(166) => 'H', chr(196).chr(167) => 'h',
		chr(196).chr(168) => 'I', chr(196).chr(169) => 'i',
		chr(196).chr(170) => 'I', chr(196).chr(171) => 'i',
		chr(196).chr(172) => 'I', chr(196).chr(173) => 'i',
		chr(196).chr(174) => 'I', chr(196).chr(175) => 'i',
		chr(196).chr(176) => 'I', chr(196).chr(177) => 'i',
		chr(196).chr(178) => 'IJ',chr(196).chr(179) => 'ij',
		chr(196).chr(180) => 'J', chr(196).chr(181) => 'j',
		chr(196).chr(182) => 'K', chr(196).chr(183) => 'k',
		chr(196).chr(184) => 'k', chr(196).chr(185) => 'L',
		chr(196).chr(186) => 'l', chr(196).chr(187) => 'L',
		chr(196).chr(188) => 'l', chr(196).chr(189) => 'L',
		chr(196).chr(190) => 'l', chr(196).chr(191) => 'L',
		chr(197).chr(128) => 'l', chr(197).chr(129) => 'L',
		chr(197).chr(130) => 'l', chr(197).chr(131) => 'N',
		chr(197).chr(132) => 'n', chr(197).chr(133) => 'N',
		chr(197).chr(134) => 'n', chr(197).chr(135) => 'N',
		chr(197).chr(136) => 'n', chr(197).chr(137) => 'N',
		chr(197).chr(138) => 'n', chr(197).chr(139) => 'N',
		chr(197).chr(140) => 'O', chr(197).chr(141) => 'o',
		chr(197).chr(142) => 'O', chr(197).chr(143) => 'o',
		chr(197).chr(144) => 'O', chr(197).chr(145) => 'o',
		chr(197).chr(146) => 'OE',chr(197).chr(147) => 'oe',
		chr(197).chr(148) => 'R',chr(197).chr(149) => 'r',
		chr(197).chr(150) => 'R',chr(197).chr(151) => 'r',
		chr(197).chr(152) => 'R',chr(197).chr(153) => 'r',
		chr(197).chr(154) => 'S',chr(197).chr(155) => 's',
		chr(197).chr(156) => 'S',chr(197).chr(157) => 's',
		chr(197).chr(158) => 'S',chr(197).chr(159) => 's',
		chr(197).chr(160) => 'S', chr(197).chr(161) => 's',
		chr(197).chr(162) => 'T', chr(197).chr(163) => 't',
		chr(197).chr(164) => 'T', chr(197).chr(165) => 't',
		chr(197).chr(166) => 'T', chr(197).chr(167) => 't',
		chr(197).chr(168) => 'U', chr(197).chr(169) => 'u',
		chr(197).chr(170) => 'U', chr(197).chr(171) => 'u',
		chr(197).chr(172) => 'U', chr(197).chr(173) => 'u',
		chr(197).chr(174) => 'U', chr(197).chr(175) => 'u',
		chr(197).chr(176) => 'U', chr(197).chr(177) => 'u',
		chr(197).chr(178) => 'U', chr(197).chr(179) => 'u',
		chr(197).chr(180) => 'W', chr(197).chr(181) => 'w',
		chr(197).chr(182) => 'Y', chr(197).chr(183) => 'y',
		chr(197).chr(184) => 'Y', chr(197).chr(185) => 'Z',
		chr(197).chr(186) => 'z', chr(197).chr(187) => 'Z',
		chr(197).chr(188) => 'z', chr(197).chr(189) => 'Z',
		chr(197).chr(190) => 'z', chr(197).chr(191) => 's',
		// Decompositions for Latin Extended-B
		chr(200).chr(152) => 'S', chr(200).chr(153) => 's',
		chr(200).chr(154) => 'T', chr(200).chr(155) => 't',
		// Euro Sign
		chr(226).chr(130).chr(172) => 'E',
		// GBP (Pound) Sign
		chr(194).chr(163) => '',
		// Vowels with diacritic (Vietnamese)
		// unmarked
		chr(198).chr(160) => 'O', chr(198).chr(161) => 'o',
		chr(198).chr(175) => 'U', chr(198).chr(176) => 'u',
		// grave accent
		chr(225).chr(186).chr(166) => 'A', chr(225).chr(186).chr(167) => 'a',
		chr(225).chr(186).chr(176) => 'A', chr(225).chr(186).chr(177) => 'a',
		chr(225).chr(187).chr(128) => 'E', chr(225).chr(187).chr(129) => 'e',
		chr(225).chr(187).chr(146) => 'O', chr(225).chr(187).chr(147) => 'o',
		chr(225).chr(187).chr(156) => 'O', chr(225).chr(187).chr(157) => 'o',
		chr(225).chr(187).chr(170) => 'U', chr(225).chr(187).chr(171) => 'u',
		chr(225).chr(187).chr(178) => 'Y', chr(225).chr(187).chr(179) => 'y',
		// hook
		chr(225).chr(186).chr(162) => 'A', chr(225).chr(186).chr(163) => 'a',
		chr(225).chr(186).chr(168) => 'A', chr(225).chr(186).chr(169) => 'a',
		chr(225).chr(186).chr(178) => 'A', chr(225).chr(186).chr(179) => 'a',
		chr(225).chr(186).chr(186) => 'E', chr(225).chr(186).chr(187) => 'e',
		chr(225).chr(187).chr(130) => 'E', chr(225).chr(187).chr(131) => 'e',
		chr(225).chr(187).chr(136) => 'I', chr(225).chr(187).chr(137) => 'i',
		chr(225).chr(187).chr(142) => 'O', chr(225).chr(187).chr(143) => 'o',
		chr(225).chr(187).chr(148) => 'O', chr(225).chr(187).chr(149) => 'o',
		chr(225).chr(187).chr(158) => 'O', chr(225).chr(187).chr(159) => 'o',
		chr(225).chr(187).chr(166) => 'U', chr(225).chr(187).chr(167) => 'u',
		chr(225).chr(187).chr(172) => 'U', chr(225).chr(187).chr(173) => 'u',
		chr(225).chr(187).chr(182) => 'Y', chr(225).chr(187).chr(183) => 'y',
		// tilde
		chr(225).chr(186).chr(170) => 'A', chr(225).chr(186).chr(171) => 'a',
		chr(225).chr(186).chr(180) => 'A', chr(225).chr(186).chr(181) => 'a',
		chr(225).chr(186).chr(188) => 'E', chr(225).chr(186).chr(189) => 'e',
		chr(225).chr(187).chr(132) => 'E', chr(225).chr(187).chr(133) => 'e',
		chr(225).chr(187).chr(150) => 'O', chr(225).chr(187).chr(151) => 'o',
		chr(225).chr(187).chr(160) => 'O', chr(225).chr(187).chr(161) => 'o',
		chr(225).chr(187).chr(174) => 'U', chr(225).chr(187).chr(175) => 'u',
		chr(225).chr(187).chr(184) => 'Y', chr(225).chr(187).chr(185) => 'y',
		// acute accent
		chr(225).chr(186).chr(164) => 'A', chr(225).chr(186).chr(165) => 'a',
		chr(225).chr(186).chr(174) => 'A', chr(225).chr(186).chr(175) => 'a',
		chr(225).chr(186).chr(190) => 'E', chr(225).chr(186).chr(191) => 'e',
		chr(225).chr(187).chr(144) => 'O', chr(225).chr(187).chr(145) => 'o',
		chr(225).chr(187).chr(154) => 'O', chr(225).chr(187).chr(155) => 'o',
		chr(225).chr(187).chr(168) => 'U', chr(225).chr(187).chr(169) => 'u',
		// dot below
		chr(225).chr(186).chr(160) => 'A', chr(225).chr(186).chr(161) => 'a',
		chr(225).chr(186).chr(172) => 'A', chr(225).chr(186).chr(173) => 'a',
		chr(225).chr(186).chr(182) => 'A', chr(225).chr(186).chr(183) => 'a',
		chr(225).chr(186).chr(184) => 'E', chr(225).chr(186).chr(185) => 'e',
		chr(225).chr(187).chr(134) => 'E', chr(225).chr(187).chr(135) => 'e',
		chr(225).chr(187).chr(138) => 'I', chr(225).chr(187).chr(139) => 'i',
		chr(225).chr(187).chr(140) => 'O', chr(225).chr(187).chr(141) => 'o',
		chr(225).chr(187).chr(152) => 'O', chr(225).chr(187).chr(153) => 'o',
		chr(225).chr(187).chr(162) => 'O', chr(225).chr(187).chr(163) => 'o',
		chr(225).chr(187).chr(164) => 'U', chr(225).chr(187).chr(165) => 'u',
		chr(225).chr(187).chr(176) => 'U', chr(225).chr(187).chr(177) => 'u',
		chr(225).chr(187).chr(180) => 'Y', chr(225).chr(187).chr(181) => 'y',
		// Vowels with diacritic (Chinese, Hanyu Pinyin)
		chr(201).chr(145) => 'a',
		// macron
		chr(199).chr(149) => 'U', chr(199).chr(150) => 'u',
		// acute accent
		chr(199).chr(151) => 'U', chr(199).chr(152) => 'u',
		// caron
		chr(199).chr(141) => 'A', chr(199).chr(142) => 'a',
		chr(199).chr(143) => 'I', chr(199).chr(144) => 'i',
		chr(199).chr(145) => 'O', chr(199).chr(146) => 'o',
		chr(199).chr(147) => 'U', chr(199).chr(148) => 'u',
		chr(199).chr(153) => 'U', chr(199).chr(154) => 'u',
		// grave accent
		chr(199).chr(155) => 'U', chr(199).chr(156) => 'u',
		);

		$string = strtr($string, $chars);
	} else {
		$chars = array();
		// Assume ISO-8859-1 if not UTF-8
		$chars['in'] = chr(128).chr(131).chr(138).chr(142).chr(154).chr(158)
			.chr(159).chr(162).chr(165).chr(181).chr(192).chr(193).chr(194)
			.chr(195).chr(196).chr(197).chr(199).chr(200).chr(201).chr(202)
			.chr(203).chr(204).chr(205).chr(206).chr(207).chr(209).chr(210)
			.chr(211).chr(212).chr(213).chr(214).chr(216).chr(217).chr(218)
			.chr(219).chr(220).chr(221).chr(224).chr(225).chr(226).chr(227)
			.chr(228).chr(229).chr(231).chr(232).chr(233).chr(234).chr(235)
			.chr(236).chr(237).chr(238).chr(239).chr(241).chr(242).chr(243)
			.chr(244).chr(245).chr(246).chr(248).chr(249).chr(250).chr(251)
			.chr(252).chr(253).chr(255);

		$chars['out'] = "EfSZszYcYuAAAAAACEEEEIIIINOOOOOOUUUUYaaaaaaceeeeiiiinoooooouuuuyy";

		$string = strtr($string, $chars['in'], $chars['out']);
		$double_chars = array();
		$double_chars['in'] = array(chr(140), chr(156), chr(198), chr(208), chr(222), chr(223), chr(230), chr(240), chr(254));
		$double_chars['out'] = array('OE', 'oe', 'AE', 'DH', 'TH', 'ss', 'ae', 'dh', 'th');
		$string = str_replace($double_chars['in'], $double_chars['out'], $string);
	}

	return $string;
}

?>