<?php

use Illuminate\Database\Eloquent\Relations\Model;

class ProviderBankAccount extends Eloquent
{

	const INDIVIDUAL = 'individual';
	const COMPANY = 'company';

	/**
	 * The table associated with the model.
	 *
	 * @var string
	 */
	protected $table = 'provider_bank_account';
	
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
	
	public function bankAccountProvider()
	{
		return $this->hasOne('Provider');
	}
}