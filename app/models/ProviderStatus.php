<?php

use Illuminate\Database\Eloquent\Relations\Model;

class ProviderStatus extends Eloquent
{
	/**
	 * The table associated with the model.
	 *
	 * @var string
	 */
	protected $table = 'provider_status';
	
	/**
	 * Indicates if the model should be timestamped.
	 *
	 * @var bool
	 */
	public $timestamps = true;
	
	/**
	 * MASS ASSIGNMENT
	 * define which attributes are mass assignable (for security)
	 * we only want this attribute able to be filled
	 */
	protected $fillable = array('name');
	
	public function providers()
	{
		return $this->hasMany('Provider');
	}
}