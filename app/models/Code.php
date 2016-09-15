<?php

use Illuminate\Database\Eloquent\Relations\Model;

class Code extends Eloquent
{
	/**
	 * The table associated with the model.
	 *
	 * @var string
	 */
	protected $table = 'code';
	
	/**
	 * Indicates if the model should be timestamped.
	 *
	 * @var bool
	 */
	public $timestamps = true;
	
	protected $primaryKey = 'id';

	protected $fillable = array('id', 'code');
	
	public function ngoMyCode(){
		return $this->belongsTo("Ngo", "id", "my_code");
	}
	
	public function ngoNetworkingCode(){
		return $this->belongsTo("Ngo", "id", "networking_code");
	}
	
	public function userMyCode(){
		return $this->belongsTo("User", "id", "my_code");
	}
	
	public function userNetworkingCode(){
		return $this->belongsTo("User", "id", "networking_code");
	}
	
	public function providerMyCode(){
		return $this->belongsTo("Provider", "id", "my_code");
	}
	
	public function providerNetworkingCode(){
		return $this->belongsTo("Provider", "id", "networking_code");
	}
	
}