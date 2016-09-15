<?php

class Settings extends Eloquent {

    protected $table = 'settings';
    protected $fillable = array('id', 'tool_tip', 'page', 'key', 'value');

    public static function getDefaultSearchRadius (){
    	$settings = self::where('key', 'default_search_radius')->first();

    	if($settings)
			return $settings->value;
		else 
			return 10 ;
    }

 	public static function getDefaultDistanceUnit (){
 		$settings = self::where('key', 'default_distance_unit')->first();

 		if($settings)
			return $settings->value;
		else 
			return 1 ;
 	}

 	public static function getDefaultMultiplyUnit (){
 		$distanceUnit = self::getDefaultDistanceUnit();

 		if ($distanceUnit == 0) {
			return 1.609344;
		} else  {
			return 1;
		}
 	}

 	public static function getProviderTimeout (){
 		$settings = self::where('key', 'provider_timeout')->first();

    	if($settings)
			return $settings->value;
		else 
			return 60 ;
 	}

 	public static function getAdminEmail (){
 		$settings = self::where('key', 'admin_email_address')->first();

    	if($settings)
			return $settings->value;
		else 
			return 'admin@localhost' ;
 	}

 	public static function getDefaultDateFormat (){
 		$settings = self::where('key', 'date_format')->first();

 		if($settings)
			return $settings->value;
		else 
			return "d/m/Y H:i" ;
 	}

 	public static function getPayment($key){

 		$setting = Settings::where('key', $key)->first();

 		if($setting){
 			return [ 
 				"name" 		=> trans('payment.'.$key) ,
 				"active" 	=> $setting->value 
			] ;
 		}
 		else return null ;

 	}

 	public static function getPaymentsArray(){
 		$paymentArray = [];

 		// money
 		if($payment = self::getPayment('payment_money')){
			$paymentArray[] = $payment ; 			
 		}

 		// voucher
 		if($payment = self::getPayment('payment_voucher')){
			$paymentArray[] = $payment ; 			
 		}

 		// card
 		if($payment = self::getPayment('payment_card')){
			$paymentArray[] = $payment ; 			
 		}

 		return $paymentArray;
 	}

 	public static function getPaymentMoney (){
 		$settings = self::where('key', 'payment_money')->first();

 		if($settings)
			return $settings->value;
		else 
			return 0;
 	}

 	public static function getPaymentCard (){
 		$settings = self::where('key', 'payment_card')->first();

 		if($settings)
			return $settings->value;
		else 
			return 0;
 	}

 	public static function getPaymentVoucher (){
 		$settings = self::where('key', 'payment_voucher')->first();

 		if($settings)
			return $settings->value;
		else 
			return 0;
 	}


 	public static function countPaymentMethods (){
 		$count = 0;

 		if(self::getPaymentMoney() == 1){
 			$count ++;
 		}

 		if(self::getPaymentCard() == 1){
 			$count ++;
 		}

 		if(self::getPaymentVoucher() == 1){
 			$count ++;
 		}

 		return $count;
 	}

 	public static function getDistanceCountOnProviderStart (){
 		$settings = self::where('key', 'distance_count_on_provider_start')->first();

 		if($settings)
			return $settings->value;
		else 
			return 1;
 	}

 	//0 - valor total da corrida pago pelo cliente / 1 - apenas valor recebido pelo prestador
 	public static function getVisibleValueToProvider (){
 		$settings = self::where('key', 'visible_value_to_provider')->first();

 		if($settings)
			return $settings->value;
		else 
			return 0;
 	}

 	public static function getProviderDirectory (){
 		$settings = self::where('key', 'provider_directory')->first();

 		if($settings)
			return $settings->value;
		else 
			return null;
 	}

 	public static function getWebsiteDirectory (){
 		$settings = self::where('key', 'website_directory')->first();

 		if($settings)
			return $settings->value;
		else 
			return null;
 	}

 	public static function getProviderUrl (){
 		$settings = self::where('key', 'provider_url')->first();

 		if($settings)
			return $settings->value;
		else 
			return null;
 	}

 	public static function getWebsiteUrl (){
 		$settings = self::where('key', 'website_url')->first();

 		if($settings)
			return $settings->value;
		else 
			return null;
 	}

 	public static function getMarkerMaximumArrivalTimeVisible (){
 		$settings = self::where('key', 'marker_maximum_arrival_time_visible')->first();

 		if($settings)
			return $settings->value;
		else 
			return 7;
 	}

 	public static function getDefaultBusinessModel (){
 		$settings = Settings::where('key', 'default_business_model')->first();

 		if($settings)
			return $settings->value;
		else 
			return RequestCharging::BUSINESS_MODEL_PERCENTAGE;
 	}

 	public static function getCarNumberFormat (){
 		$settings = Settings::where('key', 'car_number_format')->first();

 		if($settings)
			return $settings->value;
		else 
			return "AAA-1234";
 	}

 	public static function getGoogleMapsApiKey (){
 		$settings = Settings::where('key', 'google_maps_api_key')->first();

 		if($settings)
			return $settings->value;
		else 
			return "";
 	}

 	public static function getShowUserRegister (){
 		$settings = Settings::where('key', 'show_user_register')->first();

 		if($settings)
			return $settings->value;
		else 
			return "1";
 	}

}