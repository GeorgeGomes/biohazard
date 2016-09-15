<?php

use Illuminate\Database\Eloquent\Relations\Model;

class Ngo extends Eloquent
{
	/**
	 * The table associated with the model.
	 *
	 * @var string
	 */
	protected $table = 'ngo';
	
	/**
	 * Indicates if the model should be timestamped.
	 *
	 * @var bool
	 */
	public $timestamps = true;
	
	protected $primaryKey = 'id';

	protected $fillable = array('id', 'name');
	
	
	public function myCode(){
        return $this->hasOne('Code', 'id', 'my_code');
    }
	
	public function networkingCode(){
        return $this->hasOne('Code', 'id', 'networking_code');
    }
	
}