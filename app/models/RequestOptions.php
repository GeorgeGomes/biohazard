<?php

use Illuminate\Database\Eloquent\Relations\Model;

class RequestOptions extends Eloquent
{
	/**
	 * The table associated with the model.
	 *
	 * @var string
	 */
	protected $table = 'request_options';
	
	/**
	 * Indicates if the model should be timestamped.
	 *
	 * @var bool
	 */
	public $timestamps = false;
	
	/**
	 * MASS ASSIGNMENT
	 * define which attributes are mass assignable (for security)
	 * we only want these attributes able to be filled
	 */
	protected $fillable = array('provider_id', 'category', 'type', 'is_visible', 'price_per_unit_distance', 'price_per_unit_time', 'base_price', 'base_distance', 'base_time', 'distance_unit', 'time_unit', 'base_price_provider', 'base_price_user', 'commission_rate');
	
	/**
	 * Finds one row in the categories table associated with 'category' 
	 *
	 * @return HasOne object
	 */
	public function service()
	{
		return $this->hasOne('ProviderServices', 'id', 'provider_service_id');
	}
	
	/**
	 * Finds one row in the services table associated with 'type'
	 *
	 * @return HasOne object
	 */
	public function request()
	{
		return $this->hasOne('Requests', 'id', 'request_id');
	}
}