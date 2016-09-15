<?php

use PayPal\Rest\ApiContext;
use PayPal\Auth\OAuthTokenCredential;
use PayPal\Api\Amount;
use PayPal\Api\Details;
use PayPal\Api\Item;
use PayPal\Api\ItemList;
use PayPal\Api\Payer;
use PayPal\Api\Payment as PaymentPaypal;
use PayPal\Api\RedirectUrls;
use PayPal\Api\ExecutePayment;
use PayPal\Api\PaymentExecution;
use PayPal\Api\Transaction;

class WebUserController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 *
	 * @return Response
	 */
	public $status = 0;
	private $_api_context;

	private function get_timezone_offset($remote_tz, $origin_tz = null) {
		if ($origin_tz === null) {
			if (!is_string($origin_tz = date_default_timezone_get())) {
				return false; // A UTC timestamp was returned -- bail out!
			}
		}
		$origin_dtz = new DateTimeZone($origin_tz);
		$remote_dtz = new DateTimeZone($remote_tz);
		$origin_dt = new DateTime("now", $origin_dtz);
		$remote_dt = new DateTime("now", $remote_dtz);
		$offset = $origin_dtz->getOffset($origin_dt) - $remote_dtz->getOffset($remote_dt);
		return $offset;
	}

	public function __construct() {
		if (Config::get('app.production')) {
			echo trans('user_provider_controller.something_cool');
			die();
		}

		$this->beforeFilter(function() {
			if (!Session::has('user_id')) {
				Session::put('pre_login_url', URL::current());
				return Redirect::to('/user/signin');
			} else {
				$user_id = Session::get('user_id');
				$user = User::where('id', $user_id)->first();
				Session::put('user_name', $user->first_name . " " . $user->last_name);
				Session::put('user_pic', $user->picture);
			}
		}, array('except' => array(
				'userLogin',
				'userVerify',
				'userForgotPassword',
				'userRegister',
				'userSave',
				'surroundingCars',
		)));


		$date = date("Y-m-d H:i:s");
		$time_limit = date("Y-m-d H:i:s", strtotime($date) - (3 * 60 * 60));
		$user_id = Session::get('user_id');

		$current_request = Requests::where('user_id', $user_id)
				->where('is_cancelled', 0)
				->where('created_at', '>', $time_limit)
				->orderBy('created_at', 'desc')
				->where(function($query) {
					$query->where('status', 0)->orWhere(function($query_inner) {
						$query_inner->where('status', 1)
						->where('is_provider_rated', 0);
					});
				})
				->first();
		$this->status = 0;
		if ($current_request) {
			if ($current_request->confirmed_provider) {
				$provider = Provider::find($current_request->confirmed_provider);
			}

			if ($current_request->is_completed) {
				$this->status = 5;
			} elseif ($current_request->is_started) {
				$this->status = 4;
			} elseif ($current_request->is_provider_arrived) {
				$this->status = 3;
			} elseif ($current_request->is_provider_started) {
				$this->status = 2;
			} elseif ($current_request->confirmed_provider) {
				$this->status = 1;
			} else {
				if ($current_request->status == 1) {
					$this->status = 6;
				}
			}
			Session::put('status', $this->status);
			Session::put('request_id', $current_request->id);
		}

		$paypal_conf = Config::get('paypal');
		$this->_api_context = new ApiContext(new OAuthTokenCredential($paypal_conf['client_id'], $paypal_conf['secret']));
		$this->_api_context->setConfig($paypal_conf['settings']);
	}

	public function saveUserPayment() {    
		$user_id = Session::get('user_id');
		$user_data = User::find($user_id);
		//try {
			if(Config::get('app.default_payment') == 'pagarme'){
				$payment_token = Input::get('card_hash');

				PagarMe::setApiKey(Config::get('app.pagarme_api_key'));


				$card = new PagarMe_Card(array(
					'card_hash' => "$payment_token"
				));
				$card->create();

				$card_type = detectCardType(Input::get('number'));
				$last_four = substr(Input::get('number'), -4);
				if ($card->id) {
					$payment = new Payment;
					$payment->user_id = $user_id;
					$payment->customer_id = $user_id;
					$payment->card_type = $card_type;
					$payment->last_four = $last_four;
					$payment->card_token = $card->id;
					$payment->save();
					$message = trans('user_provider_controller.card_add');
					$type = "success";
					return Redirect::to('/user/payments')->with('message', $message)->with('type', $type);
				} else {
					$message = trans('user_provider_controller.something_wrong');
					$type = "danger";
					return Redirect::to('/user/payments')->with('message', $message)->with('type', $type);
				}
			}
			else if (Config::get('app.default_payment') == 'stripe') {
				$payment_token = Input::get('stripeToken');
				Stripe::setApiKey(Config::get('app.stripe_secret_key'));

				$customer = Stripe_Customer::create(array(
							"card" => $payment_token,
							"description" => $user_data->email)
				);
				Log::info('key = ' . print_r($customer, true));

				$last_four = substr(Input::get('number'), -4);
				if ($customer) {
					$customer_id = $customer->id;
					$payment = new Payment;
					$payment->user_id = $user_id;
					$payment->customer_id = $customer_id;
					$payment->last_four = $last_four;
					$payment->card_token = $customer->sources->data[0]->id;
					$payment->save();
					$message = trans('user_provider_controller.card_add');
					$type = "success";
					return Redirect::to('/user/payments')->with('message', $message)->with('type', $type);
				} else {
					$message = trans('user_provider_controller.something_wrong');
					$type = "danger";
					return Redirect::to('/user/payments')->with('message', $message)->with('type', $type);
				}
			} else {
				Braintree_Configuration::environment(Config::get('app.braintree_environment'));
				Braintree_Configuration::merchantId(Config::get('app.braintree_merchant_id'));
				Braintree_Configuration::publicKey(Config::get('app.braintree_public_key'));
				Braintree_Configuration::privateKey(Config::get('app.braintree_private_key'));
				$result = Braintree_Customer::create(array(
							"firstName" => $user_data->first_name,
							"lastName" => $user_data->last_name,
							"creditCard" => array(
								"number" => Input::get('number'),
								"expirationMonth" => Input::get('month'),
								"expirationYear" => Input::get('year'),
								"cvv" => Input::get('cvv'),
							)
				));

				if ($result->success) {
					$num = $result->customer->creditCards[0]->maskedNumber;
					$last_four = substr($num, -4);
					$customer_id = $result->customer->id;
					$payment = new Payment;
					$payment->user_id = $user_id;
					$payment->customer_id = $customer_id;
					$payment->last_four = $last_four;
					$payment->card_token = $result->customer->creditCards[0]->token;
					$payment->save();

					$message = trans('user_provider_controller.card_add');
					$type = "success";
					return Redirect::to('/user/payments')->with('message', $message)->with('type', $type);
				} else {
					$message = trans('user_provider_controller.something_wrong');
					$type = "danger";
					return Redirect::to('/user/payments')->with('message', $message)->with('type', $type);
				}
			}
		/*} catch (Exception $e) {
			$message = trans('user_provider_controller.something_wrong');
			$type = "danger";
			return Redirect::to('/user/payments')->with('message', $message)->with('type', $type);
		}*/
	}

	public function saveUserReview() {
		$request_id = Input::get('request_id');
		$user_id = Session::get('user_id');
		$status = Session::get('status');
		$request = Requests::where('id', $request_id)->where('user_id', $user_id)->first();
		$rating = 0;
		if (Input::has('rating')) {
			$rating = Input::get('rating');
		}
		if ($request) {
			$review_provider = new ProviderReview;
			$review_provider->provider_id = $request->confirmed_provider;
			$review_provider->comment = Input::get('review');
			$review_provider->rating = $rating;
			$review_provider->user_id = $user_id;
			$review_provider->request_id = $request->id;
			$review_provider->save();

			if ($rating) {
				if ($provider = Provider::find($request->confirmed_provider)) {
					$old_rate = $provider->rate;
					$old_rate_count = $provider->rate_count;
					$new_rate_counter = ($provider->rate_count + 1);
					$new_rate = (($provider->rate * $provider->rate_count) + $rating) / $new_rate_counter;
					$provider->rate_count = $new_rate_counter;
					$provider->rate = $new_rate;
					$provider->save();
				}
			}

			$request->is_provider_rated = 1;
			$request->save();
			Session::forget('status');
			Session::put('status', 6);
		}

		$message = trans('user_provider_controller.rate_driver');
		$type = "success";
		return Redirect::to('/user/trips')->with('message', $message)->with('type', $type);
	}

	public function index() {
		//return Redirect::to('/user/signin');
	}

	public function userLogin() {
		return View::make('web.userLogin');
	}

	public function userRegister() {
		$fields = array(
			'first_name'			=> '',
			'last_name'				=> '',
			'email'					=> '',
			'phone'					=> '+55',
		);
		return View::make('web.userSignup')
						->with('fields', $fields);
	}

	public function userTripCancel() {
		$request_id = Request::segment(4);
		$user_id = Session::get('user_id');
		$request = Requests::find($request_id);
		if ($request->user_id == $user_id) {
			Requests::where('id', $request_id)->update(array('is_cancelled' => 1));
			RequestMeta::where('request_id', $request_id)->update(array('is_cancelled' => 1));
			if ($request->confirmed_provider) {
				$provider = Provider::find($request->confirmed_provider);
				$provider->is_available = 1;
				$provider->save();
			}
			if ($request->current_provider) {


				$msg_array = array();
				$msg_array['request_id'] = $request_id;
				$msg_array['unique_id'] = 2;

				$user = User::find($user_id);
				$request_data = array();
				$request_data['user'] = array();
				$request_data['user']['name'] = $user->first_name . " " . $user->last_name;
				$request_data['user']['picture'] = $user->picture;
				$request_data['user']['phone'] = $user->phone;
				$request_data['user']['address'] = $user->address;
				$request_data['user']['latitude'] = $request->latitude;
				$request_data['user']['longitude'] = $request->longitude;
				/* $request_data['user']['rating'] = DB::table('review_user')->where('user_id', '=', $user->id)->avg('rating') ? : 0; */
				$request_data['user']['rating'] = $user->rate;
				/* $request_data['user']['num_rating'] = DB::table('review_user')->where('user_id', '=', $user->id)->count(); */
				$request_data['user']['num_rating'] = $user->rate_count;

				$request_data['dog'] = array();
				if ($dog = Dog::find($user->dog_id)) {
					$request_data['dog']['name'] = $dog->name;
					$request_data['dog']['age'] = $dog->age;
					$request_data['dog']['breed'] = $dog->breed;
					$request_data['dog']['likes'] = $dog->likes;
					$request_data['dog']['picture'] = $dog->image_url;
				}
				$msg_array['request_data'] = $request_data;

				$title = trans('user_provider_controller.request_canceled');
				$message = $msg_array;
				send_notifications($request->current_provider, "provider", $title, $message);
			}
		}

		// Redirect
		$message = trans('user_provider_controller.request_canceled_success');
		$type = "success";
		return Redirect::to('/user/trips')->with('message', $message)->with('type', $type);
	}

	public function userTripStatus() {
		$id = Request::segment(4);
		$user_id = Session::get('user_id');
		$request = Requests::where('id', $id)->first();
		if ($request != NULL) {
			$status = 0;

			if ($request->is_provider_rated) {
				$status = 6;
			} elseif ($request->is_completed) {
				$status = 5;
			} elseif ($request->is_started) {
				$status = 4;
			} elseif ($request->is_provider_arrived) {
				$status = 3;
			} elseif ($request->is_provider_started) {
				$status = 2;
			} elseif ($request->confirmed_provider) {
				$status = 1;
			}
			echo $status;
		}
	}

	public function userRequestTrip() {
		$date = date("Y-m-d H:i:s");
		$time_limit = date("Y-m-d H:i:s", strtotime($date) - (3 * 60 * 60));
		$user_id = Session::get('user_id');

		$get_value = Settings::where('key', 'provider_selection')->first();
		$selection = $get_value->value;

		$get_dest = Settings::where('key', 'get_destination')->first();
		$destination = $get_dest->value;

		$current_request = Requests::where('user_id', $user_id)
				->where('is_cancelled', 0)
				->where('created_at', '>', $time_limit)
				->orderBy('created_at', 'desc')
				->where(function($query) {
					$query->where('status', 0)->orWhere(function($query_inner) {
						$query_inner->where('status', 1)
						->where('confirmed_provider', '>', 0);
					});
				})
				->first();

		if (!$current_request or Session::has('skipReview') or $current_request->is_provider_rated == 1) {
			// array to store all allowed payments 
			$payment_options = array();

			$payments = Payment::where('user_id', Session::get('user_id'))->count();

			if ($payments) {
				$payment_options['stored_cards'] = 1;
			} else {
				$payment_options['stored_cards'] = 0;
			}
			$codsett = Settings::where('key', 'payment_money')->first();
			if ($codsett->value == 1) {
				$payment_options['cod'] = 1;
			} else {
				$payment_options['cod'] = 0;
			}

			$paypalsett = Settings::where('key', 'paypal')->first();
			if ($paypalsett->value == 1) {
				$payment_options['paypal'] = 1;
			} else {
				$payment_options['paypal'] = 0;
			}

			//Log::info('payment_options = ' . print_r($payment_options, true));

			/* $var = Keywords::where('id', 4)->first(); */

			$types = ProviderType::where('is_visible', '=', 1)->get();
			return View::make('web.userRequestTrip')
							/* ->with('title', 'Request ' . $var->keyword . '') */
							->with('title', trans('user_provider_controller.request_trip'))
							->with('types', $types)
							->with('selection', $selection)
							->with('destination', $destination)
							->with('payment_option', $payment_options)
							->with('page', 'request-trip');
		} else {
			$user = User::find($user_id);
			$type = ProviderType::find($current_request->type);
			$status = 0;
			$payment_mode = $current_request->payment_mode;
			if ($current_request->is_provider_rated) {
				$status = 6;
			} elseif ($current_request->is_completed) {
				$status = 5;
			} elseif ($current_request->is_started) {
				$status = 4;
			} elseif ($current_request->is_provider_arrived) {
				$status = 3;
			} elseif ($current_request->is_provider_started) {
				$status = 2;
			} elseif ($current_request->confirmed_provider) {
				$status = 1;
			} else {
				if ($current_request->status == 1) {
					$status = 7;
				}
			}

			$provider_detail = RequestLocation::where('request_id', $current_request->id)->orderBy('created_at', 'desc')->first();

			$eta = Settings::where('key', '=', 'get_destination')->first();
			$eta_value = $eta->value;

			if ($provider_detail) {
				$provider_detail = $provider_detail;
			} else {
				$provider_detail = '';
			}

			/* $var = Keywords::where('id', 4)->first(); */

			if ($current_request->confirmed_provider) {
				$provider = Provider::find($current_request->confirmed_provider);

				/* $rating = DB::table('review_provider')->where('provider_id', '=', $current_request->confirmed_provider)->avg('rating') ? : 0; */
				$rating = $provider->rate;
				return View::make('web.userRequestTripStatus')
								/* ->with('title', '' . $var->keyword . ' Status')
								  ->with('page', '' . $var->keyword . '-status') */
								->with('title', trans('user_provider_controller.status_trip'))
								->with('page', '' . Config::get('app.generic_keywords.Trip') . '-status')
								->with('request', $current_request)
								->with('user', $user)
								->with('provider', $provider)
								->with('type', $type)
								->with('provider_detail', $provider_detail)
								->with('destination', $destination)
								->with('status', $status)
								->with('eta_value', $eta_value)
								->with('payment_mode', $payment_mode)
								->with('rating', $rating);
			} else {
				return View::make('web.userRequestTripStatus')
								/* ->with('title', '' . $var->keyword . ' Status')
								  ->with('page', '' . $var->keyword . '-status') */
								->with('title', trans('user_provider_controller.status_trip'))
								->with('page', '' . Config::get('app.generic_keywords.Trip') . '-status')
								->with('request', $current_request)
								->with('user', $user)
								->with('type', $type)
								->with('provider_detail', $provider_detail)
								->with('destination', $destination)
								->with('rating', 0)
								->with('eta_value', $eta_value)
								->with('payment_mode', $payment_mode)
								->with('status', $status);
			}
		}
	}

	public function paybypalwebSubmit() {

		// Normal Payment

		$request_id = Request::segment(3);
		$request = Requests::where('id', $request_id)->first();
		$reqserv = RequestServices::where('request_id', $request_id)->first();
		$typess = ProviderType::where('id', $reqserv->type)->first();

		$total_amount = $request->total;
		$service_name = $typess->name;

		$user = User::where('id', $request->user_id)->first();
		$provider = Provider::where('id', $request->confirmed_provider)->first();
		$admins = Admin::first();

		// Adaptive payments
		// check if transfer is allowed
		$transfersett = Settings::where('key', 'transfer')->first();
		$payRequest = new PayRequest();
		if ($transfersett->value == 1) {

			$receiver = array();
			$receiver[0] = new Receiver();
			$receiver[0]->amount = $request->total - $request->transfer_amount;
			// $receiver[0]->email = "testpaypal34@gmail.com";
			$receiver[0]->email = $admins->username;
			$receiver[0]->primary = "true";

			$receiver[1] = new Receiver();
			$receiver[1]->amount = $request->transfer_amount;
			// $receiver[1]->email = "joydhanbad@gmail.com";
			$receiver[1]->email = $provider->email;
		} else {
			$receiver = array();
			$receiver[0] = new Receiver();
			$receiver[0]->amount = $request->total;
			// $receiver[0]->email = "testpaypal34@gmail.com";
			$receiver[0]->email = $admins->username;
			$receiver[0]->primary = "true";
		}
		$receiverList = new ReceiverList($receiver);
		$payRequest->receiverList = $receiverList;

		$requestEnvelope = new RequestEnvelope("en_US");
		$payRequest->requestEnvelope = $requestEnvelope;
		$payRequest->actionType = "PAY";
		$payRequest->cancelUrl = URL::route('userpaypalstatus');
		$payRequest->returnUrl = URL::route('userpaypalstatus');
		$payRequest->currencyCode = "USD";
		$payRequest->ipnNotificationUrl = URL::route('userpaypalipn');

		$sdkConfig = array(
			"mode" => Config::get('app.paypal_sdk_mode'),
			"acct1.UserName" => Config::get('app.paypal_sdk_UserName'),
			"acct1.Password" => Config::get('app.paypal_sdk_Password'),
			"acct1.Signature" => Config::get('app.paypal_sdk_Signature'),
			"acct1.AppId" => Config::get('app.paypal_sdk_AppId')
		);

		$adaptivePaymentsService = new AdaptivePaymentsService($sdkConfig);
		$payResponse = $adaptivePaymentsService->Pay($payRequest);

		//Log::info('payResponse = ' . print_r($payResponse, true));
		return Redirect::to('userpaypalstatus');
	}

	public function paypalstatus() {
		$request = Requests::find(Session::get('request_id'));

		$requestEnvelope = new RequestEnvelope("en_US");
		$paymentDetailsRequest = new PaymentDetailsRequest($requestEnvelope);
		$paymentDetailsRequest->payKey = "AP-2K156847LU642333B";

		$sdkConfig = array(
			"mode" => Config::get('app.paypal_sdk_mode'),
			"acct1.UserName" => Config::get('app.paypal_sdk_UserName'),
			"acct1.Password" => Config::get('app.paypal_sdk_Password'),
			"acct1.Signature" => Config::get('app.paypal_sdk_Signature'),
			"acct1.AppId" => Config::get('app.paypal_sdk_AppId')
		);

		$adaptivePaymentsService = new AdaptivePaymentsService($sdkConfig);
		$paymentDetailsResponse = $adaptivePaymentsService->PaymentDetails($paymentDetailsRequest);

		//Log::info('paymentDetailsResponse = ' . print_r($paymentDetailsResponse, true));
		//Log::info('payKey = ' . print_r($paymentDetailsResponse->{'payKey'}, true));
		$request->payment_id = $paymentDetailsResponse->{'payKey'};
		$request->is_paid = 1;
		$request->save();
		return Redirect::to('/user/request-trip');
	}

	public function userpaypalipn() {
		dd(Input::get($payKey));
	}

	public function request_fare() {
		$latitude = Input::get('latitude');
		$longitude = Input::get('longitude');
		$d_latitude = Input::get('d_latitude');
		$d_longitude = Input::get('d_longitude');
		$type = Input::get('type');
		$provider = Input::get('provider');
		$promo_code = trim(Input::get('promo_code'));
		$time = 0;
		$distance = 0;

		$request_typ = ProviderType::where('id', '=', $type)->first();
		$setbase_distance = $request_typ->base_distance;
		$setbase_price = $request_typ->base_price;
		$setdistance_price = $request_typ->price_per_unit_distance;
		$settime_price = $request_typ->price_per_unit_time;

		/* $json_resp = file_get_contents('http://maps.googleapis.com/maps/api/distancematrix/json?origins=' . $latitude . ',' . $longitude . '&destinations=' . $d_latitude . ',' . $d_longitude); */
		/* $data = json_decode($json_resp);
		  //Log::info('data = ' . print_r($data, true));


		  $distance = $data->rows[0]->elements[0]->distance->value;

		  $time = $data->rows[0]->elements[0]->duration->value; */

		$settings = Settings::where('key', 'default_distance_unit')->first();
		$unit = $settings->value;
		$distance = get_dist($latitude, $longitude, $d_latitude, $d_longitude);
		//Log::info('data = ' . print_r($distance, true));

		if ($unit == 0) {
			$distanceNew = $distance * 0.001;
			/* $setdistance_price = Settings::where('key', 'price_per_unit_distance')->first();
			  $price_per_unit_distance = $setdistance_price->value * $distanceNew; */
			if ($distanceNew <= $setbase_distance) {
				$price_per_unit_distance = 0;
			} else {
				$price_per_unit_distance = $setdistance_price->value * ($distanceNew - $setbase_distance);
			}
		} else {
			$distanceNew = $distance * 0.000621371;
			/* $setdistance_price = Settings::where('key', 'price_per_unit_distance')->first();
			  $price_per_unit_distance = $setdistance_price->value * $distanceNew; */
			if ($distanceNew <= $setbase_distance) {
				$price_per_unit_distance = 0;
			} else {
				$price_per_unit_distance = $setdistance_price->value * ($distanceNew - $setbase_distance);
			}
		}
		$timeMinutes = $time * 0.0166667;


		/* $settime_price = Settings::where('key', 'price_per_unit_time')->first();
		  $price_per_unit_time = $settime_price->value * $timeMinutes; */
		$price_per_unit_time = $settime_price * $timeMinutes;

		$total = 0;

		$base_price = 0;

		if (Input::has('provider')) {
			$pt = ProviderServices::where('provider_id', $provider)
							->where('type', $type)->first();

			$base_price = $pt->base_price;
			if ($distanceNew <= $setbase_distance) {
				$price_per_unit_distance = 0;
			} else {
				$price_per_unit_distance = $pt->price_per_unit_distance * ($distanceNew - $setbase_distance);
			}
			$total = $base_price + $price_per_unit_distance + $price_per_unit_time;
		} else {
			/* $setbase_price = Settings::where('key', 'base_price')->first();
			  $base_price = $setbase_price->value; */
			$base_price = $setbase_price;
			/* $setdistance_price = Settings::where('key', 'price_per_unit_distance')->first();
			  $price_per_unit_distance = $setdistance_price->value * $distanceNew; */
			if ($distanceNew <= $setbase_distance) {
				$price_per_unit_distance = 0;
			} else {
				$price_per_unit_distance = $setdistance_price->value * ($distanceNew - $setbase_distance);
			}
			$total = $base_price + $price_per_unit_distance + $price_per_unit_time;
		}
		if ($promo_code) {
			$promosett = Settings::where('key', 'promotional_code_activation')->first();
			$promo_discount = 0;
			$total_amount = $total;
			if ($promosett->value == 1) {
				/* if ($request->promo_code != "") { */
				$promo_code = PromoCodes::where('coupon_code', $promo_code)->first();
				if (isset($promo_code->id)) {
					$promo_value = $promo_code->value;
					$promo_type = $promo_code->type;
					if ($promo_type == 1) {
						$discount = $total_amount * ($promo_value / 100);
					} elseif ($promo_type == 2) {
						$discount = $promo_value;
					}
					$promo_discount = $discount;
					/* } */
					$total = $total_amount - $promo_discount;
				}
			}
		}

		/* $currency_selected = Keywords::find(5);
		  if ($currency_selected->keyword == '$') { */
		if (Config::get('app.generic_keywords.Currency') == '$') {
			$currency_sel = "USD";
		} else {
			/* $currency_sel = $currency_selected->keyword; */
			$currency_sel = Config::get('app.generic_keywords.Currency');
		}
		if ($currency_sel != 'USD') {
			$httpAdapter = new \Ivory\HttpAdapter\FileGetContentsHttpAdapter();
			// Create the Yahoo Finance provider
			$yahooProvider = new \Swap\Provider\YahooFinanceProvider($httpAdapter);
			// Create Swap with the provider
			$swap = new \Swap\Swap($yahooProvider);
			$rate = $swap->quote("USD/" . $currency_sel);
			$rate = json_decode($rate, true);
			//Log::info($rate);
			$total = $total * $rate;
		}

		$status = 1;
		/* return Response::json(array('success' => true, 'total' => $currency_selected->keyword . " " . $total)); */
		return Response::json(array('success' => true, 'total' => Config::get('app.generic_keywords.Currency') . " " . sprintf2($total, 2)));
	}

	public function request_eta() {
		$time = 0;
		$distance = 0;

		$request_id = Session::get('request_id');
		$request = Requests::where('id', $request_id)->first();
		$d_latitude = $request->D_latitude;
		$d_longitude = $request->D_longitude;
		$walk_loc = RequestLocation::where('request_id', $request_id)->orderBy('id', 'desc')->first();
		$longitude = $walk_loc->longitude;
		$latitude = $walk_loc->latitude;

		$json_resp = file_get_contents('http://maps.googleapis.com/maps/api/distancematrix/json?origins=' . $latitude . ',' . $longitude . '&destinations=' . $d_latitude . ',' . $d_longitude);
		$data = json_decode($json_resp);
		//Log::info('data = ' . print_r($data, true));

		$distance = $data->rows[0]->elements[0]->distance->value;

		$time = $data->rows[0]->elements[0]->duration->text;
		//Log::info('data = ' . print_r($time, true));

		$status = 1;
		return Response::json(array('success' => true, 'eta' => $time));
	}

	public function saveUserRequestTrip() {
		Session::forget('skipReview');
		$latitude = Input::get('latitude');
		$longitude = Input::get('longitude');
		$d_latitude = Input::get('d_latitude');
		$d_longitude = Input::get('d_longitude');
		$type = Input::get('type');
		$user_id = Session::get('user_id');
		$payment_type = Input::get('payment_type');

		if ($type == "") {
			$message = trans('user_provider_controller.select_service');
			$type = "warning";
			return Redirect::route('userrequestTrip')->with('message', $message)->with('type', $type);
		} else {

			if (Input::has('promo_code')) {
				$promo_code = Input::get('promo_code');
				$code_check = PromoCodes::where('coupon_code', $promo_code)->first();

				if ($code_check == NULL) {
					$message = trans('user_provider_controller.invalid_promo_code');
					$type = "error";
					return Redirect::route('userrequestTrip')->with('message', $message)->with('type', $type);
				} else {
					if ($code_check->state == 1 && date('Y-m-d H:i:s', strtotime($code_check->expiry)) > date('Y-m-d H:i:s')) {
						$code_id = $code_check->id;
					} else {
						$message = trans('user_provider_controller.invalid_promo_code');
						$type = "error";
						return Redirect::route('userrequestTrip')->with('message', $message)->with('type', $type);
					}
				}
			} else {
				$code_id = NULL;
			}

			$user_data = User::find($user_id);

			$settings = Settings::where('key', 'default_search_radius')->first();
			$distance = $settings->value;

			if (Input::has('type')) {
				$type = Input::get('type');
				if (!$type) {
					// choose default type
					$provider_type = ProviderType::where('is_default', 1)->first();

					if (!$provider_type) {
						$type = 1;
					} else {
						$type = $provider_type->id;
					}
				}
			}

			$typequery = "SELECT distinct provider_id from provider_services where type IN($type)";
			$typeproviders = DB::select(DB::raw($typequery));

			/* $var = Keywords::where('id', 1)->first(); */

			if (empty($typeproviders)) {
				/* $message = "No " . $var->keyword . " found matching the service type."; */
				$message = trans('user_provider_controller.no_match') . Config::get('app.generic_keywords.Provider') . trans('user_provider_controller.with_request');
			} else {

				//Log::info('typeproviders = ' . print_r($typeproviders, true));
				foreach ($typeproviders as $key) {
					$types[] = $key->provider_id;
				}
				$typestring = implode(",", $types);
				//Log::info('typestring = ' . print_r($typestring, true));

				if ($typestring == '') {
					/* $message = "No " . $var->keyword . " found matching the service type."; */
					$message = trans('user_provider_controller.no_match') . Config::get('app.generic_keywords.Provider') . trans('user_provider_controller.with_request');
				}

				$settings = Settings::where('key', 'default_search_radius')->first();
				$distance = $settings->value;
				$settings = Settings::where('key', 'default_distance_unit')->first();
				$unit = $settings->value;
				if ($unit == 0) {
					$multiply = 1.609344;
				} elseif ($unit == 1) {
					$multiply = 1;
				}
				$query = "SELECT provider.*, ROUND(" . $multiply . " * 3956 * acos( cos( radians('$latitude') ) * cos( radians(latitude) ) * cos( radians(longitude) - radians('$longitude') ) + sin( radians('$latitude') ) * sin( radians(latitude) ) ) ,8) as distance from provider where is_available = 1 and is_active = 1 and is_approved = 1 and ROUND((" . $multiply . " * 3956 * acos( cos( radians('$latitude') ) * cos( radians(latitude) ) * cos( radians(longitude) - radians('$longitude') ) + sin( radians('$latitude') ) * sin( radians(latitude) ) ) ) ,8) <= $distance and provider.deleted_at IS NULL and provider.id IN($typestring) order by distance";


				$providers = DB::select(DB::raw($query));
				$provider_list = array();


				$user = User::find($user_id);
				$user->latitude = $latitude;
				$user->longitude = $longitude;
				$user->save();

				$user_timezone = $user->timezone;
				$default_timezone = Config::get('app.timezone');
				$offset = $this->get_timezone_offset($default_timezone, $user_timezone);

				$request = new Requests;
				$request->user_id = $user_id;
				if ($d_longitude != '' && $d_latitude != '') {
					$request->D_latitude = $d_latitude;
					$request->D_longitude = $d_longitude;
				}
				$request->latitude = $latitude;
				$request->longitude = $longitude;
				$request->request_start_time = date("Y-m-d H:i:s", strtotime(date("Y-m-d H:i:s")) + $offset);
				$request->save();

				$request_service = new RequestServices;
				$request_service->type = $type;
				$request_service->request_id = $request->id;
				$request_service->save();

				$i = 0;
				$first_provider_id = 0;
				foreach ($providers as $provider) {
					$request_meta = new RequestMeta;
					$request_meta->request_id = $request->id;
					$request_meta->provider_id = $provider->id;
					if ($i == 0) {
						$first_provider_id = $provider->id;
						$i++;
					}
					$request_meta->save();
				}
				$req = Requests::find($request->id);
				$req->current_provider = $first_provider_id;
				$req->confirmed_provider = 0;
				$req->payment_mode = $payment_type;
				$req->promo_code = $code_id;
				$req->save();

				$settings = Settings::where('key', 'provider_timeout')->first();
				$time_left = $settings->value;

				/* $var = Keywords::where('id', 1)->first();

				  $message = "Your Request is successful. Please wait while we are finding a nearest " . $var->keyword . " for you."; */
				$message = trans('user_provider_controller.request_success') . Config::get('app.generic_keywords.Provider') . trans('user_provider_controller.for_you');
				$type = "success";
			}
			return Redirect::to('/user/request-trip')->with('message', $message)->with('type', $type);

			// Send Notification
			$provider = Provider::find($first_provider_id);
			if ($provider) {
				$msg_array = array();
				$msg_array['unique_id'] = 1;
				$msg_array['request_id'] = $request->id;
				$msg_array['time_left_to_respond'] = $time_left;
				$user = User::find($user_id);
				$request_data = array();
				$request_data['user'] = array();
				$request_data['user']['name'] = $user->first_name . " " . $user->last_name;
				$request_data['user']['picture'] = $user->picture;
				$request_data['user']['phone'] = $user->phone;
				$request_data['user']['address'] = $user->address;
				$request_data['user']['latitude'] = $request->latitude;
				$request_data['user']['longitude'] = $request->longitude;
				/* $request_data['user']['rating'] = DB::table('review_user')->where('user_id', '=', $user->id)->avg('rating') ? : 0; */
				$request_data['user']['rating'] = $user->rate;
				/* $request_data['user']['num_rating'] = DB::table('review_user')->where('user_id', '=', $user->id)->count(); */
				$request_data['user']['num_rating'] = $user->rate_count;

				$request_data['dog'] = array();
				if ($dog = Dog::find($user->dog_id)) {

					$request_data['dog']['name'] = $dog->name;
					$request_data['dog']['age'] = $dog->age;
					$request_data['dog']['breed'] = $dog->breed;
					$request_data['dog']['likes'] = $dog->likes;
					$request_data['dog']['picture'] = $dog->image_url;
				}
				$msg_array['request_data'] = $request_data;

				$title = trans('user_provider_controller.new_request');
				$message = json_encode($msg_array);
				send_notifications($first_provider_id, "provider", $title, $message);
			}

			// Send SMS 
			$settings = Settings::where('key', 'sms_request_created')->first();
			$pattern = $settings->value;
			$pattern = str_replace('%user%', $user_data->first_name . " " . $user_data->last_name, $pattern);
			$pattern = str_replace('%id%', $request->id, $pattern);
			$pattern = str_replace('%user_mobile%', $user_data->phone, $pattern);
			sms_notification(1, 'admin', $pattern);

			$settings = Settings::where('key', 'admin_email_address')->first();
			$admin_email = $settings->value;
			$follow_url = web_url() . "/user/signin";
			$pattern = array('admin_eamil' => $admin_email, 'trip_id' => $request->id, 'follow_url' => $follow_url);
			$subject = trans('user_provider_web.request_book');
			email_notification(1, 'admin', $pattern, $subject, 'new_request', null);

			return Redirect::to('/user/request-trip')->with('message', $message)->with('type', $type);
		}
	}

	public function userSkipReview() {
		$request_id = Request::segment(3);
		Session::put('skipReview', 1);
		return Redirect::to('/user/request-trip');
	}

	public function userSave() {
		$first_name = Input::get('first_name');
		$last_name = Input::get('last_name');
		$email = Input::get('email');
		$phone = Input::get('phone');
		$password = Input::get('password');
		$referral_code = Input::get('referral_code');
		$validator = Validator::make(
						array(
					'first_name' => $first_name,
					'last_name' => $last_name,
					'email' => $email
						), array(
					'email' => 'required',
					'last_name' => 'required',
					'first_name' => 'required',
						), array(
					'email' => trans('user_provider_controller.mail_needed'),
					'last_name' => trans('user_provider_controller.last_name_needed'),
					'first_name' => trans('user_provider_controller.first_name_needed'),
						)
		);

		$validator1 = Validator::make(
						array(
					'email' => $email,
						), array(
					'email' => 'required|email'
						), array(
					'email' => trans('user_provider_controller.mail_needed')
						)
		);

		$validatorPhone = Validator::make(
						array(
					'phone' => $phone,
						), array(
					'phone' => 'phone'
						), array(
					'phone' => trans('user_provider_controller.phone_needed')
						)
		);
		 $validatorPassword = Validator::make(
						array(
					'password' => $password,
						), array(
					'password' => 'required|min:6'
						), array(
					'password' => trans('user_provider_controller.password_needed')
						)
		);


		if ($validator->fails()) {
			$error_messages = $validator->messages()->first();
			return Redirect::to('user/signup')->with('error', trans('user_provider_controller.all_fields'));
		} else if ($validator1->fails()) {
			return Redirect::to('user/signup')->with('error', trans('user_provider_controller.mail_fields'));
		} else if ($validatorPassword->fails()) {
			return Redirect::to('provider/signup')->with('error', trans('user_provider_controller.password_needed'));
		}/*else if ($validatorPhone->fails()) {
			return Redirect::to('user/signup')->with('error', 'Telefone com formato inválido');
		}*/ else {
			if (User::where('email', $email)->count() == 0) {
				$user = new User;
				$user->first_name = $first_name;
				$user->last_name = $last_name;
				$user->email = $email;
				if (Input::has('timezone')) {
					$user->timezone = Input::get('timezone');
				}

				$user->phone = $phone;
				if ($password != "") {
					$user->password = Hash::make($password);
				}
				$user->token = generate_token();
				$user->token_expiry = generate_expiry();
				$user->save();
				regenerate:
				$referral_code1 = my_random6_number();
				if (Ledger::where('referral_code', $referral_code1)->count()) {
					goto regenerate;
				}
				/* Referral entry */
				$ledger = new Ledger;
				$ledger->user_id = $user->id;
				$ledger->referral_code = $referral_code1;
				$ledger->save();
				if ($referral_code != "") {
					if ($ledger = Ledger::where('referral_code', $referral_code)->first()) {
						$referred_by = $ledger->user_id;
						/* $settings = Settings::where('key', 'default_referral_bonus')->first();
						  $referral_bonus = $settings->value;

						  $ledger = Ledger::find($ledger->id);
						  $ledger->total_referrals = $ledger->total_referrals + 1;
						  $ledger->amount_earned = $ledger->amount_earned + $referral_bonus;
						  $ledger->save(); */
						$settings = Settings::where('key', 'default_referral_bonus_to_refered_user')->first();
						$refered_user = $settings->value;

						$settings = Settings::where('key', 'default_referral_bonus_to_refereel')->first();
						$referral = $settings->value;

						$ledger = Ledger::find($ledger->id);
						$ledger->total_referrals = $ledger->total_referrals + 1;
						$ledger->amount_earned = $ledger->amount_earned + $refered_user;
						$ledger->save();

						$ledger1 = Ledger::where('user_id', $user->id)->first();
						$ledger1 = Ledger::find($ledger1->id);
						$ledger1->amount_earned = $ledger1->amount_earned + $referral;
						$ledger1->save();

						$user->referred_by = $ledger->user_id;

						$response_array = array('success' => true);
						$response_code = 200;
					}
				}
				$user->save();
				
				$settings = Settings::where('key', 'admin_email_address')->first();
				$admin_email = $settings->value;
				$pattern = array('admin_eamil' => $admin_email, 'name' => ucwords($user->first_name . " " . $user->last_name), 'web_url' => web_url());
				$subject = trans('user_provider_controller.welcome') . ucwords(Config::get('app.website_title')) . ", " . ucwords($user->first_name . " " . $user->last_name) . "";
				email_notification($user->id, 'user', $pattern, $subject, 'user_new_register', null);
				return Redirect::to('user/signin')->with('success', trans('user_provider_controller.register_success_login'));
			} else {
				return Redirect::to('user/signup')->with('error', trans('user_provider_controller.mail_registered'));
			}
		}
	}

	public function userForgotPassword() {
		$email = Input::get('email');
		$user = User::where('email', $email)->first();
		if ($user) {
			$new_password = time();
			$new_password .= rand();
			$new_password = sha1($new_password);
			$new_password = substr($new_password, 0, 8);
			$user->password = Hash::make($new_password);
			$user->save();

			// send email
			/* $settings = Settings::where('key', 'email_forgot_password')->first();
			  $pattern = $settings->value;
			  $pattern = str_replace('%password%', $new_password, $pattern);
			  $subject = "Your New Password";
			  email_notification($user->id, 'user', $pattern, $subject); */
			$settings = Settings::where('key', 'admin_email_address')->first();
			$admin_email = $settings->value;
			$login_url = web_url() . "/user/signin";
			$pattern = array('name' => $user->first_name . " " . $user->last_name, 'admin_eamil' => $admin_email, 'new_password' => $new_password, 'login_url' => $login_url);
			$subject = trans('user_provider_controller.new_password');
			email_notification($user->id, 'user', $pattern, $subject, 'reset_password', 'imp');
			return Redirect::to('user/signin')->with('success', trans('user_provider_controller.password_reset'));
		} else {
			return Redirect::to('user/signin')->with('error', trans('user_provider_controller.mail_not_registered'));
		}
	}

	public function userVerify() {
		$email = Input::get('email');
		$password = Input::get('password');
		$user = User::where('email', '=', $email)->first();
		if ($user && Hash::check($password, $user->password)) {
			Session::put('user_id', $user->id);
			Session::put('user_name', $user->first_name . " " . $user->last_name);
			Session::put('user_pic', $user->picture);
			if (Session::has('pre_login_url')) {
				$url = Session::get('pre_login_url');
				Session::forget('pre_login_url');
				return Redirect::to($url);
			} else {
				return Redirect::to('user/trips');
			}
		} else {
			return Redirect::to('user/signin')->with('error', trans('user_provider_controller.invalid_mail_pass'));
		}
	}

	public function userLogout() {
		Session::flush();
		return Redirect::to('/user/signin');
	}

	public function userTripDetail() {
		$id = Request::segment(3);
		$user_id = Session::get('user_id');
		$request = Requests::find($id);
		$request_service = RequestServices::find($id);
		if ($request->user_id == $user_id) {
			$locations = RequestLocation::where('request_id', $id)
					->orderBy('id')
					->get();
			$count = round(count($locations) / 50);
			$start = RequestLocation::where('request_id', $id)
					->orderBy('id')
					->first();
			$end = RequestLocation::where('request_id', $id)
					->orderBy('id', 'desc')
					->first();
			$map = "https://maps-api-ssl.google.com/maps/api/staticmap?size=249x249&style=feature:landscape|visibility:off&style=feature:poi|visibility:off&style=feature:transit|visibility:off&style=feature:road.highway|element:geometry|lightness:39&style=feature:road.local|element:geometry|gamma:1.45&style=feature:road|element:labels|gamma:1.22&style=feature:administrative|visibility:off&style=feature:administrative.locality|visibility:on&style=feature:landscape.natural|visibility:on&scale=2&markers=shadow:false|scale:2|icon:http://d1a3f4spazzrp4.cloudfront.net/receipt-new/marker-start@2x.png|$start->latitude,$start->longitude&markers=shadow:false|scale:2|icon:http://d1a3f4spazzrp4.cloudfront.net/receipt-new/marker-finish@2x.png|$end->latitude,$end->longitude&path=color:0x2dbae4ff|weight:4";

			$skip = 0;
			foreach ($locations as $location) {
				if ($skip == $count) {
					$map .= "|$location->latitude,$location->longitude";
					$skip = 0;
				}
				$skip ++;
			}

			$start_location = json_decode(file_get_contents("https://maps.googleapis.com/maps/api/geocode/json?latlng=$start->latitude,$start->longitude"), TRUE);
			$start_address = $start_location['results'][0]['formatted_address'];

			$end_location = json_decode(file_get_contents("https://maps.googleapis.com/maps/api/geocode/json?latlng=$end->latitude,$end->longitude"), TRUE);
			$end_address = $end_location['results'][0]['formatted_address'];

			$provider = Provider::find($request->confirmed_provider);
			$provider_review = ProviderReview::where('request_id', $id)->first();
			if ($provider_review) {
				$rating = round($provider_review->rating);
			} else {
				$rating = 0;
			}

			/* $var = Keywords::where('id', 4)->first();
			  $currency = Keywords::where('id', 5)->first(); */

			return View::make('web.userTripDetail')
							/* ->with('title', 'My ' . $var->keyword . 's') */
							->with('title', trans('user_provider_controller.my_trips'))
							->with('request', $request)
							->with('request_service', $request_service)
							->with('start_address', $start_address)
							->with('end_address', $end_address)
							/* ->with('currency', $currency->keyword) */
							->with('currency', Config::get('app.generic_keywords.Currency'))
							->with('start', $start)
							->with('end', $end)
							->with('map_url', $map)
							->with('provider', $provider)
							->with('rating', $rating);
		} else {
			echo "false";
		}
	}

	public function userTrips() {
		$user_id = Session::get('user_id');
		$requests = Requests::where('user_id', $user_id)
				->where('is_completed', 1)
				->leftJoin('provider', 'provider.id', '=', 'request.confirmed_provider')
				->leftJoin('request_services', 'request_services.request_id', '=', 'request.id')
				->leftJoin('provider_type', 'provider_type.id', '=', 'request_services.type')
				->orderBy('request_start_time', 'desc')
				->select('request.id', 'request_start_time', 'provider.first_name', 'provider.last_name', 'request.total as total', 'provider_type.name as type')
				->get();

		/* $var = Keywords::where('id', 4)->first(); */

		return View::make('web.userTrips')
						/* ->with('title', 'My ' . $var->keyword . 's') */
						->with('title', trans('user_provider_controller.my_trips'))
						->with('requests', $requests);
	}

	public function userScheduledTrips() {
		$user_id = Session::get('user_id');
		$requests = ScheduledRequests::where('scheduled_requests.user_id', $user_id)
				->leftJoin('user', 'scheduled_requests.user_id', '=', 'user.id')
				->orderBy('scheduled_requests.server_start_time', 'ASC')
				->select(
						'user.first_name as user_first_name', 'user.last_name as user_last_name', 'user.id as user_id', 'scheduled_requests.id as id', 'scheduled_requests.created_at as date', 'scheduled_requests.time_zone', 'scheduled_requests.src_address', 'scheduled_requests.dest_address', 'scheduled_requests.promo_code', 'scheduled_requests.start_time', 'scheduled_requests.start_time', 'scheduled_requests.payment_mode')
				->get();
		$total_schedules = ScheduledRequests::where('user_id', '=', $user_id)->count();
		/* $var = Keywords::where('id', 4)->first(); */

		return View::make('web.userScheduledTrips')
						/* ->with('title', 'My ' . $var->keyword . 's') */
						->with('title', trans('user_provider_controller.my_trips') . " : Total = " . $total_schedules)
						->with('requests', $requests);
	}

	public function delete_future_request() {
		$user_id = Session::get('user_id');
		$request_id = Input::get('id');
		ScheduledRequests::where('user_id', '=', $user_id)->where('id', '=', $request_id)->delete();
		return Redirect::to("/user/scheduledtrips");
	}

	public function userProfile() {
		$user_id = Session::get('user_id');
		$user = User::find($user_id);
		return View::make('web.userProfile')
						->with('title', trans('user_provider_controller.my_profile'))
						->with('user', $user);
	}

	public function updateUserProfile() {

		$user_id = Session::get('user_id');
		$first_name = Input::get('first_name');
		$last_name = Input::get('last_name');
		$phone = Input::get('phone');
		$picture = Input::file('picture');
		$bio = Input::get('bio');
		$address = Input::get('address');
		$state = Input::get('state');
		$country = Input::get('country');
		$zipcode = Input::get('zipcode');
		$timezone = Input::get('timezone');

		$validator = Validator::make(
						array(                    
					'user_id' => $user_id,
					'first_name' => $first_name,
					'last_name' => $last_name,
					'phone' => $phone,
						), array(
					'user_id' => 'required',
					'first_name' => 'required',
					'last_name' =>'required',
					'phone' => 'required',
						), array(
					'user_id' => trans('user_provider_controller.miss_unique_id'),
					'first_name' => trans('user_provider_controller.first_name_needed'),
					'last_name' => trans('user_provider_controller.last_name_needed'),
					'phone' => trans('user_provider_controller.phone_needed'),
						)
		);
		 $validatorPicture = Validator::make(
						array(
					'picture' => $picture,
						), array(
					/* 'picture' => 'mimes:jpeg,bmp,png' */
					'picture' => '',
						), array(
					/* 'picture' => 'mimes:jpeg,bmp,png' */
					'picture' => trans('user_provider_controller.image_needed')
						)
		);

		if ($validator->fails()) {
			$error_messages = $validator->messages();
			return Redirect::to('/user/profile')->with('error', trans('user_provider_controller.all_fields'));
		} elseif ($validatorPicture->fails()) {
			$error_messages = $validator->messages();
			return Redirect::to('/provider/profile')->with('error', trans('user_provider_controller.image_not_allow'));
		} else {

			$user = User::find($user_id);

			if (Input::hasFile('picture')) {
				if ($user->picture != "") {
					$path = $user->picture;
					//Log::info($path);
					$filename = basename($path);
					//Log::info($filename);
					try {
					    //Esse código precisa de tratamento pois pode gerar alguma exceção
					    unlink(public_path() . "/uploads/" . $filename);
					} catch (\Exception $e) {
					    
					}
					
				}

				if(Input::has("picture_cropped")){
					///Salva o arquivo no PC
					$cropped_image = Input::get('picture_cropped');

					// remove the part that we don't need from the provided image and decode it
					$data = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $cropped_image));

					$file_name = time();
					$file_name .= rand();
					$file_name = sha1($file_name);

					$ext = Input::file('picture')->getClientOriginalExtension();

					$filepath = public_path() . "/uploads/" . $file_name . "." . $ext; // or image.jpg

					// Save the image in a defined path
					file_put_contents($filepath, $data);
				}

				$local_url = $file_name . "." . $ext;

				// // upload image
				// $file_name = time();
				// $file_name .= rand();
				// $file_name = sha1($file_name);

				// $ext = Input::file('picture')->getClientOriginalExtension();
				// Input::file('picture')->move(public_path() . "/uploads", $file_name . "." . $ext);
				$local_url = $file_name . "." . $ext;

				// Upload to S3
				if (Config::get('app.s3_bucket') != "") {
					$s3 = App::make('aws')->get('s3');
					$pic = $s3->putObject(array(
						'Bucket' => Config::get('app.s3_bucket'),
						'Key' => $file_name,
						'SourceFile' => public_path() . "/uploads/" . $local_url,
					));

					$s3->putObjectAcl(array(
						'Bucket' => Config::get('app.s3_bucket'),
						'Key' => $file_name,
						'ACL' => 'public-read'
					));

					$s3_url = $s3->getObjectUrl(Config::get('app.s3_bucket'), $file_name);
				} else {
					$s3_url = asset_url() . '/uploads/' . $local_url;
				}

				if (isset($user->picture)) {
					if ($user->picture != "") {
						$icon = $user->picture;
						unlink_image($icon);
					}
				}

				$user->picture = $s3_url;
			}

			$user->first_name = $first_name;
			$user->last_name = $last_name;
			$user->phone = $phone;
			$user->bio = $bio;
			$user->address = $address;
			$user->state = $state;
			$user->country = $country;
			$user->zipcode = $zipcode;
			$user->timezone = $timezone;
			$user->save();
			return Redirect::to('/user/profile')->with('message', trans('user_provider_controller.profile_success_update'))->with('type', 'success');
		}
	}

	public function updateUserPassword() {
		$current_password = Input::get('current_password');
		$new_password = Input::get('new_password');
		$confirm_password = Input::get('confirm_password');

		$user_id = Session::get('user_id');
		$user = User::find($user_id);

		$validator = Validator::make(
						array(                   
					'new_password' => $new_password
						), array(
					'new_password' => 'required|min:6'
						), array(
					'new_password' => 'Senha deve possuir mais que 6 dígitos.'
						)
		);

		if ($validator->fails()) {          
			 return Redirect::to('/provider/profile')->with('error', 'Senha deve possuir mais que 6 dígitos.');

		}elseif ($new_password === $confirm_password) {

			if (Hash::check($current_password, $user->password)) {
				$password = Hash::make($new_password);
				$user->password = $password;
				$user->save();

				$message = trans('user_provider_controller.password_updated');
				$type = "success";
			} else {
				$message = trans('user_provider_controller.password_old_wrong');
				$type = "error";
			}
		} else {
			$message = trans('user_provider_controller.password_dont_match');

			$type = "error";
		}
		return Redirect::to('/user/profile')->with('message', $message)->with('type', $type);
	}

	public function userPayments() {
		$user_id = Session::get('user_id');
		$payments = Payment::where('user_id', $user_id)->get();
		$ledger = Ledger::where('user_id', $user_id)->first();

		return View::make('web.userPayments')
						->with('title', trans('user_provider_controller.payments_and_credits'))
						->with('payments', $payments)
						->with('ledger', $ledger);
	}

	public function deleteUserPayment() {
		$user_id = Session::get('user_id');
		$id = Request::segment(4);
		Payment::where('user_id', $user_id)
				->where('id', $id)
				->delete();
		$message = trans('user_provider_controller.remove_card');
		$type = "success";
		return Redirect::to('/user/payments')->with('message', $message)->with('type', $type);
	}

	public function updateUserCode() {
		$user_id = Session::get('user_id');
		$code = Input::get('code');
		$code_count = Ledger::where('referral_code', '=', $code)->where('user_id', '!=', $user_id)->count();
		if ($code_count) {
			$message = trans('user_provider_web.code_used');
			$type = "danger";
		} else {
			$ledger = Ledger::where('user_id', $user_id)->first();
			if ($ledger) {
				$ledger->referral_code = $code;
				$ledger->save();
				$message = trans('user_provider_controller.referral_updated');
				$type = "success";
			} else {
				$ledger = new Ledger;
				$ledger->user_id = $user_id;
				$ledger->referral_code = $code;
				$ledger->save();
				$message = trans('user_provider_controller.referral_create');
				$type = "success";
			}
		}
		return Redirect::to('/user/payments')->with('message', $message)->with('type', $type);
	}

	public function surroundingCars() {
		if ($_GET) {
			$query = '';
			$latitude = Input::get('latitude');
			$longitude = Input::get('longitude');
			$typestring = Input::get('type');
			$settings = Settings::where('key', 'default_search_radius')->first();

			$distance = $settings->value;
			$settings = Settings::where('key', 'default_distance_unit')->first();
			$unit = $settings->value;
			if ($unit == 0) {
				$multiply = 1.609344;
			} elseif ($unit == 1) {
				$multiply = 1;
			}
			if ($typestring == "") {
				$query = "SELECT provider.id,provider.first_name,provider.last_name,provider.latitude,provider.longitude, ROUND(" . $multiply . " * 3956 * acos( cos( radians('$latitude') ) * cos( radians(latitude) ) * cos( radians(longitude) - radians('$longitude') ) + sin( radians('$latitude') ) * sin( radians(latitude) ) ) ,8) as distance from provider where is_available = 1 and is_active = 1 and is_approved = 1 and ROUND((" . $multiply . " * 3956 * acos( cos( radians('$latitude') ) * cos( radians(latitude) ) * cos( radians(longitude) - radians('$longitude') ) + sin( radians('$latitude') ) * sin( radians(latitude) ) ) ) ,8) <= $distance order by distance";
			} else {
				$query = "SELECT provider.id,provider.first_name,provider.last_name,provider.latitude,provider.longitude, ROUND(" . $multiply . " * 3956 * acos( cos( radians('$latitude') ) * cos( radians(provider.latitude) ) * cos( radians(provider.longitude) - radians('$longitude') ) + sin( radians('$latitude') ) * sin( radians(provider.latitude) ) ) ,8) as distance from provider JOIN provider_services where provider.is_available = 1 and provider.is_active = 1 and provider.is_approved = 1 and ROUND((" . $multiply . " * 3956 * acos( cos( radians('$latitude') ) * cos( radians(provider.latitude) ) * cos( radians(provider.longitude) - radians('$longitude') ) + sin( radians('$latitude') ) * sin( radians(provider.latitude) ) ) ) ,8) <= $distance and provider.id = provider_services.provider_id and provider_services.type = $typestring order by distance";
			}
			$providers = DB::select(DB::raw($query));

			$provider_d = array();

			$inc = 0;

			foreach ($providers as $key) {
				$provider_d[$inc][0] = $key->id;
				$provider_d[$inc][1] = $key->first_name . " " . $key->last_name;
				$provider_d[$inc][2] = $key->latitude;
				$provider_d[$inc][3] = $key->longitude;
				$inc++;
			}

			return Response::json(array('provider' => $provider_d, 'success' => true, 'inc' => $inc));
		}
	}

	public function send_eta_web() {
		if ($_POST) {
			$request_id = Input::get('request_id');
			$mail_ids = Input::get('mail_ids');
			$duration = Input::get('duration');
			$destination = Input::get('destination');
			$source = Input::get('source');

			$request = Requests::where('id', $request_id)->first();

			$splits = explode(',', $mail_ids);
			$user_name = Session::get('user_name');

			foreach ($splits as $key) {

				// send email
				$link = "https://maps.google.com/maps?f=d&hl=en&saddr=" . $source . "&daddr=" . $destination . "&ie=UTF8&om=0&output=kml";

				$pattern = trans('user_provider_controller.hi') ." ". $user_name . trans('user_provider_controller.arrive_destine') . $duration . trans('user_provider_controller.estimated_info') . $link . "<br>";

				$subject = trans('user_provider_controller.eta_from') . $user_name;
				send_eta_email($key, $pattern, $subject);
			}

			return Redirect::to('/user/request-trip')->with('message', trans('user_provider_controller.eta_share_success'));
		}
	}

}
