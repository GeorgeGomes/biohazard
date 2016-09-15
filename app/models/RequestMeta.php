<?php

class RequestMeta extends Eloquent {

    protected $table = 'request_meta';
    
    const 	Schedule = 0 ;
    const 	Confirm = 1 ;
    const 	Archive = 3 ;
    
    public static function getProviderIdArray($requestId){
    	$providerIdArray = array();
    	$requestMetaArray = RequestMeta::where('request_id', '=', $requestId)->orderBy('created_at')->get();

    	foreach($requestMetaArray  as $requestMeta){
    		$providerIdArray[] = $requestMeta->provider_id ;
    	}

    	return $providerIdArray;
	}

}
