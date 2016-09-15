<?php

use Illuminate\Database\Eloquent\Relations\Model;

class Bank extends Eloquent
{
	/**
	 * The table associated with the model.
	 *
	 * @var string
	 */
	protected $table = 'bank';
	
	/**
	 * Indicates if the model should be timestamped.
	 *
	 * @var bool
	 */
	public $timestamps = true;

	protected $fillable = array('id', 'name', 'code');
	
}