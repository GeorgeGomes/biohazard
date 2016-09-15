<?php

use Illuminate\Database\Eloquent\Relations\Model;

class Country extends Eloquent
{
	/**
	 * The table associated with the model.
	 *
	 * @var string
	 */
	protected $table = 'country';
	
	/**
	 * Indicates if the model should be timestamped.
	 *
	 * @var bool
	 */
	public $timestamps = true;

	protected $fillable = array('id', 'name','iso');

	public function getNamePhoneCodeAttribute()
	{
		return $this->name .' +'. $this->phone_code;
	}

	public function getPlusPhoneCodeAttribute()
	{
		return '+'. $this->phone_code;
	}
	
}