<?php

use Illuminate\Database\Eloquent\Relations\Model;

class Requests extends Eloquent {

    protected $table = 'request';


    public static function UserHasPendings($userId){
    	return DB::table('request')->where('user_id', $userId)
							->where('is_completed', 0)
							->where('is_cancelled', 0)
							->where('current_provider', '!=', 0)
							->count();
    }

    /**
	 * Finds one row in the request_options table associated with 'request_id' 
	 *
	 * @return HasOne object
	 */
	public function option()
	{
		return $this->hasOne('RequestOptions', 'request_id', 'id');
	}

	/**
	 * Finds one row in the request_services table associated with 'request_id' 
	 *
	 * @return HasOne object
	 */
	public function service()
	{
		return $this->hasOne('RequestServices', 'request_id', 'id');
	}

	/**
	 * Finds one row in the provider table associated with 'confirmed_provider' 
	 *
	 * @return HasOne object
	 */
	public function confirmedProvider()
	{
		return $this->hasOne('Provider', 'id', 'confirmed_provider');
	}

	public function getTypeAttribute()
	{
		if($this->service)
			return $this->service->type;
		else 
			return null ;
	}

	public function getCategoryAttribute()
	{
		if($this->option && $this->option->service)
			return $this->option->service->category;
		else 
			return null ;
	}

	public function getProviderService()
	{
		if($this->option){
			return $this->option->service;
		}
		else{
			return $this->confirmedProvider->getProviderServiceByTypeId($this->service->type);
		}
	}

	public function getBasePrice()
	{
		return $this->getProviderService()->base_price_user;

	}
}
