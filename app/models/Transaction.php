<?php

use Illuminate\Database\Eloquent\Relations\Model;

class Transaction extends Eloquent
{
	//transaction type
	const BASE_TAX = 'base_tax';
	const CANCEL_TAX = 'cancel_tax';
	const REQUEST_PRICE = 'request_price';

	//transaction status
	const PROCESSING = 'processing';
	const AUTHORIZED = 'authorized';
 	const PAID = 'paid';
 	const WAITING_PAYMENT = 'waiting_payment';
 	const PENDING_REFUND ='pending_refund';
 	const REFUNDED ='refunded';
 	const REFUSED = 'refused';

 	//split status
 	const SPLIT_WAITING_FUNDS = 'waiting_funds';
 	const SPLIT_PAID = 'paid';

	/**
	 * The table associated with the model.
	 *
	 * @var string
	 */
	protected $table = 'transaction';
	
	/**
	 * Indicates if the model should be timestamped.
	 *
	 * @var bool
	 */
	public $timestamps = true;
	
}