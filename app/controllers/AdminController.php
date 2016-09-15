<?php

class AdminController extends BaseController {

	public function __construct() {
		$this->beforeFilter(function () {
			if (!Auth::check()) {
				$url = URL::current();
				$routeName = Route::currentRouteName();
				//Log::info('current route =' . print_r(Route::currentRouteName(), true));

				if ($routeName != "AdminLogin" && $routeName != 'admin') {
					Session::put('pre_admin_login_url', $url);
				}

				return Redirect::to('/admin/login');
			}

			$adminPermission = AdminPermission::where('admin_id', Session::get('admin_id'))->get();

			Session::put('adminPermission', $adminPermission);

		}, array('except' => array('login', 'verify', 'add', 'provider_xml')));
	}

	private function _braintreeConfigure() {
		Braintree_Configuration::environment(Config::get('app.braintree_environment'));
		Braintree_Configuration::merchantId(Config::get('app.braintree_merchant_id'));
		Braintree_Configuration::publicKey(Config::get('app.braintree_public_key'));
		Braintree_Configuration::privateKey(Config::get('app.braintree_private_key'));
	}

	public function add() {
		$user = new Admin;
		$user->username = Input::get('username');
		$user->password = $user->password = Hash::make(Input::get('password'));
		$user->save();
	}




	public function report() {
		$braintree_environment = Config::get('app.braintree_environment');
		$braintree_merchant_id = Config::get('app.braintree_merchant_id');
		$braintree_public_key = Config::get('app.braintree_public_key');
		$braintree_private_key = Config::get('app.braintree_private_key');
		$braintree_cse = Config::get('app.braintree_cse');
		$twillo_account_sid = Config::get('app.twillo_account_sid');
		$twillo_auth_token = Config::get('app.twillo_auth_token');
		$twillo_number = Config::get('app.twillo_number');
		$stripe_publishable_key = Config::get('app.stripe_publishable_key');
		$default_payment = Config::get('app.default_payment');
		$stripe_secret_key = Config::get('app.stripe_secret_key');
		$mail_driver = Config::get('mail.mail_driver');
		$email_name = Config::get('mail.from.name');
		$email_address = Config::get('mail.from.address');
		$mandrill_secret = Config::get('services.mandrill_secret');
		$sendgrid_secret = Config::get('services.sendgrid_secret');

		/* DEVICE PUSH NOTIFICATION DETAILS */
		$customer_certy_url = Config::get('app.customer_certy_url');
		$customer_certy_pass = Config::get('app.customer_certy_pass');
		$customer_certy_type = Config::get('app.customer_certy_type');
		$provider_certy_url = Config::get('app.provider_certy_url');
		$provider_certy_pass = Config::get('app.provider_certy_pass');
		$provider_certy_type = Config::get('app.provider_certy_type');
		$gcm_browser_key = Config::get('app.gcm_browser_key');
		/* DEVICE PUSH NOTIFICATION DETAILS END */
		$install = array(
			'braintree_environment' => $braintree_environment,
			'braintree_merchant_id' => $braintree_merchant_id,
			'braintree_public_key' => $braintree_public_key,
			'braintree_private_key' => $braintree_private_key,
			'braintree_cse' => $braintree_cse,
			'twillo_account_sid' => $twillo_account_sid,
			'twillo_auth_token' => $twillo_auth_token,
			'twillo_number' => $twillo_number,
			'stripe_publishable_key' => $stripe_publishable_key,
			'stripe_secret_key' => $stripe_secret_key,
			'mail_driver' => $mail_driver,
			'email_address' => $email_address,
			'email_name' => $email_name,
			'mandrill_secret' => $mandrill_secret,
			'sendgrid_secret' => $sendgrid_secret,
			'default_payment' => $default_payment,
			
			/* DEVICE PUSH NOTIFICATION DETAILS */
			'customer_certy_url' => $customer_certy_url,
			'customer_certy_pass' => $customer_certy_pass,
			'customer_certy_type' => $customer_certy_type,
			'provider_certy_url' => $provider_certy_url,
			'provider_certy_pass' => $provider_certy_pass,
			'provider_certy_type' => $provider_certy_type,
			'gcm_browser_key' => $gcm_browser_key,
				/* DEVICE PUSH NOTIFICATION DETAILS END */
		);

		$start_date = Input::get('start_date');
		$end_date = Input::get('end_date');
		$status = Input::get('status');
		$provider_id = Input::get('provider_id');
		$user_id = Input::get('user_id');
		$pay_status = Input::get('pay_status');
		$request_id = Input::get('request_id');
		$submit = Input::get('submit');

		if(!Input::has('payment_card') && !Input::has('payment_money') && !Input::has('payment_voucher')){
			$payment_card = 1;
			$payment_money = 1;
			$payment_voucher = 1;
		}
		else{
			$payment_card = Input::get('payment_card');
			$payment_money = Input::get('payment_money');
			$payment_voucher = Input::get('payment_voucher');
		}

		Session::put('start_date', $start_date);
		Session::put('end_date', $end_date);		
		Session::put('status', $status);
		Session::put('provider_id', $provider_id);		
		Session::put('user_id', $user_id);	
		Session::put('pay_status', $pay_status);	
		Session::put('payment_card', $payment_card);	
		Session::put('payment_money', $payment_money);	
		Session::put('payment_voucher', $payment_voucher);	
		Session::put('request_id', $request_id);	
		Session::put('submit', $submit);		

		$var = $start_date;
		$date = str_replace('/', '-', $var);
		$start_date =  date('Y-m-d H:i:s', strtotime($date));

		$var2 = $end_date;
		$date2 = str_replace('/', '-', $var2);
		$end_date =  date('m/d/Y', strtotime($date2));

		$start_time = date("Y-m-d 00:00:00", strtotime($start_date));
		$end_time = date("Y-m-d 23:59:59", strtotime($end_date));
		$start_date = date("Y-m-d", strtotime($start_date));
		$end_date = date("Y-m-d", strtotime($end_date));

		$requests_query = DB::table('request')
				->leftJoin('user', 'request.user_id', '=', 'user.id')
				->leftJoin('provider', 'request.confirmed_provider', '=', 'provider.id')
				->leftJoin('provider_type', 'provider.type', '=', 'provider_type.id');

		$query = $requests_query;

		//FILTROS	


		//date de inicio
		if (Input::get('request_id') != "") {
			$query = $query->where('request.id', '=', $request_id);
		}

		//date de inicio
		if (Input::get('start_date') != "") {
			$query = $query->where('request.created_at', '>=', $start_time);
		}

		//data de fim
		if(Input::get('end_date') != ""){
			$query = $query->where('request.created_at', '<=', $end_time);
		}

		//status da solicitacao
		if (Input::has('status') && Input::get('status') != 0) {
			if ($status == 1) {
				$query = $query->where('request.is_completed', '=', 1);
			} else {
				$query = $query->where('request.is_cancelled', '=', 1);
			}
		}
		else {
			$query = $query->where(function ($que) {
				$que->where('request.is_completed', '=', 1)
					->orWhere('request.is_cancelled', '=', 1);
			});
		}

		//prestador
		if (Input::has('provider_id')) {
			$query = $query->whereIn('request.confirmed_provider', $provider_id);
		}

		//usuario
		if (Input::has('user_id')) {
			$query = $query->whereIn('request.user_id', $user_id);	
		}

		//status de pagamento
		if (Input::has('pay_status') && Input::get('pay_status') != 0) {

			//pagamento completo
			if($pay_status == 1){
				$query = $query->where('request.is_paid', '=', $pay_status);	
			}
			//requisicao cancelada e taxa de cancelamento OK (taxa paga ou nao há necessidade de pagar)
			else if($pay_status == 2){
				$query = $query->where('request.is_cancelled', '=', 1)
						->where('request.is_cancel_fee_paid', '=', 1)
						->orWhere('request.payment_mode','=',RequestCharging::PAYMENT_MODE_MONEY)
						->orWhere('request.payment_mode','=',RequestCharging::PAYMENT_MODE_VOUCHER);
			}

			//requisicao cancelada, mas pagamento de taxa de cancelamento está pendente
			else if($pay_status == 3){
				$query = $query->where('request.is_cancelled', '=', 1)
						->where('request.payment_mode', '=', RequestCharging::PAYMENT_MODE_CARD)
						->Where('request.is_cancel_fee_paid','=',0);
			}

			//requisicao finalizada e pagamento pendente
			else{
				$query = $query->where('request.is_completed', '=', 1)
						->Where('request.is_paid','=',0);
			}
			
		}

		//metodos de pagamento
		$payment_mode_array = array();
		if ($payment_card == 1) {
			array_push($payment_mode_array, RequestCharging::PAYMENT_MODE_CARD);
		}
		if ($payment_money == 1) {
			array_push($payment_mode_array, RequestCharging::PAYMENT_MODE_MONEY);
		}
		if ($payment_voucher == 1) {
			array_push($payment_mode_array, RequestCharging::PAYMENT_MODE_VOUCHER);
		}
		$query = $query->whereIn('request.payment_mode', $payment_mode_array);

		//itens da tabela
		$requests = $query->select(
			'request.request_start_time',
			'provider_type.name as type',
			'request.ledger_payment',
			'request.card_payment',
			'user.first_name as user_first_name',
			'user.last_name as user_last_name',
			'provider.first_name as provider_first_name',
			'provider.last_name as provider_last_name',
			'user.id as user_id', 'provider.id as provider_id',
			'request.id as id', 'request.created_at as date',
			'request.is_started', 'request.is_provider_arrived',
			'request.payment_mode', 'request.is_completed',
			'request.is_paid', 'request.is_provider_started',
			'request.confirmed_provider',
			'request.status',
			'request.time',
			'request.distance',
			'request.total',
			'request.is_cancelled',
			'request.promo_payment',
			'request.payment_platform_rate',
			'request.provider_commission',
			'request.is_cancel_fee_paid',
			'request.request_price_transaction_id')
			->orderBy('request.id', 'DESC')
			->paginate(10);

		//estatisticas no topo da pagina
		$completed_rides = DB::table('request')->where('request.is_completed', 1)->count();
		$cancelled_rides = DB::table('request')->where('request.is_cancelled', 1)->count();
		$card_payment = DB::table('request')->where('request.is_completed', 1)->where('request.payment_mode', 0)->sum('request.total');
		$cash_payment = DB::table('request')->where('request.payment_mode', 1)->where('request.payment_mode', 1)->sum('request.total');
		$voucher_payment = DB::table('request')->where('request.payment_mode', 1)->where('request.payment_mode', 2)->sum('request.total');		
		$credit_payment = DB::table('request')->where('request.is_completed', 1)->sum('request.total');
		$promo_payment = DB::table('request')->where('request.is_completed', 1)->sum('request.promo_payment');		
		
		$scheduled_rides = ScheduledRequests::count();

		//opcao de download do relatorio
		if (Input::get('submit') && Input::get('submit') == 'Download_Report') {

			header('Content-Type: text/csv; charset=utf-8');
			header('Content-Disposition: attachment; filename=data.csv');
			$handle = fopen('php://output', 'w');
			$settings = Settings::where('key', 'default_distance_unit')->first();
			$unit = $settings->value;
			if ($unit == 0) {
				$unit_set = trans('setting.km');
			} elseif ($unit == 1) {
				$unit_set = trans('setting.miles');
			}

			fputcsv($handle, array(trans('adminController.id'), trans('adminController.data'), trans('adminController.service_type'), trans('customize.Provider'), trans('adminController.user'), trans('adminController.distance') . '(' . $unit_set . ')', trans('adminController.time'), trans('adminController.pay_mode'), trans('adminController.earning'), trans('adminController.refferal_bonus'), trans('adminController.promo_code'), trans('adminController.card_pay')));
			foreach ($requests as $request) {
				$pay_mode = trans('adminController.card_pay');
				if ($request->payment_mode == 1) {
					$pay_mode = trans('adminController.cash_pay');
				}
				if($request->payment_mode == 2){
					$pay_mode = trans('adminController.voucher_pay');
				}
				fputcsv($handle, array(
					$request->id,
					date('l, F d Y h:i A', strtotime($request->request_start_time)),
					$request->type,
					$request->provider_first_name . " " . $request->provider_last_name,
					$request->user_first_name . " " . $request->user_last_name,
					sprintf2($request->distance, 2),
					sprintf2($request->time, 2),
					$pay_mode,
					sprintf2($request->total, 2),
					sprintf2($request->ledger_payment, 2),
					sprintf2($request->promo_payment, 2),
					sprintf2($request->card_payment, 2),
				));
			}

			fputcsv($handle, array());
			fputcsv($handle, array());
			// fputcsv($handle, array('Total Trips', $completed_rides + $cancelled_rides));
			// fputcsv($handle, array('Completed Trips', $completed_rides));
			// fputcsv($handle, array('Cancelled Trips', $cancelled_rides));
			// fputcsv($handle, array('Scheduled Trips', $scheduled_rides));
			// fputcsv($handle, array('Total Payments', sprintf2(($credit_payment + $card_payment), 2)));
			// fputcsv($handle, array('Card Payment', sprintf2($card_payment, 2)));
			// fputcsv($handle, array('Referral Payment', $credit_payment));
			// fputcsv($handle, array('Cash Payment', sprintf2($cash_payment, 2)));
			// fputcsv($handle, array('Promotional Payment', sprintf2($promo_payment, 2)));
			fputcsv($handle, array(trans('adminController.total_trip'), $completed_rides + $cancelled_rides));
			fputcsv($handle, array(trans('adminController.complete_trip'), $completed_rides));
			fputcsv($handle, array(trans('adminController.cancel_trip'), $cancelled_rides));
			fputcsv($handle, array(trans('adminController.scheduled_trip'), $scheduled_rides));
			fputcsv($handle, array(trans('adminController.total_pay'), sprintf2(($credit_payment + $card_payment), 2)));
			fputcsv($handle, array(trans('adminController.card_pay'), sprintf2($card_payment, 2)));
			fputcsv($handle, array(trans('adminController.referral_pay'), $credit_payment));
			fputcsv($handle, array(trans('adminController.cash_pay'), sprintf2($cash_payment, 2)));
			fputcsv($handle, array(trans('adminController.promo_pay'), sprintf2($promo_payment, 2)));

			fclose($handle);

			$headers = array(
				'Content-Type' => 'text/csv',
			);
		} else {
			$currency_sel = Config::get('app.generic_keywords.Currency');
			$providers = DB::table('provider')->orderBy("first_name", "ASC")->orderBy("last_name", "ASC")->get();
			$users = DB::table('user')->orderBy("first_name", "ASC")->orderBy("last_name", "ASC")->get();

			$title = ucwords(trans('customize.Dashboard'));
			return View::make('dashboard')
							->with('title', $title)
							->with('page', 'dashboard')
							->with('requests', $requests)
							->with('users', $users)
							->with('providers', $providers)
							->with('install', $install)
							->with('currency_sel', $currency_sel)

							->with('completed_rides', $completed_rides)
							->with('cancelled_rides', $cancelled_rides)

							->with('card_payment', $card_payment)		
							->with('cash_payment', $cash_payment)		
							->with('voucher_payment', $voucher_payment)

							->with('promo_payment', $promo_payment)
							->with('scheduled_rides', $scheduled_rides)
							
							->with('credit_payment', $credit_payment)
							

							->with('payment_card', $payment_card)
							->with('payment_money', $payment_money)
							->with('payment_voucher', $payment_voucher);
		}
	}

	//admin control

	public function admins() {
		Session::forget('type');
		Session::forget('valu');
		$admins = Admin::paginate(10);
		$title = ucwords(trans('customize.admin_control'));
		return View::make('admins')
						->with('title', $title)
						->with('page', 'settings')
						->with('admin', $admins);
	}

	public function add_admin() {
		$admin = Admin::all();
		$permissions = DB::table('permission')->get();
		return View::make('add_admin')
						->with('title', trans('adminController.add_admin'))
						->with('page', 'add_admin')
						->with('admin', $admin)
						->with('permission', $permissions);
	}

	public function add_admin_do() {
		$username = Input::get('username');
		$password = Input::get('password');
		$permissions = Input::get('permission');

		$validator = Validator::make(
						array(
					trans('adminController.username') => $username,
					trans('adminController.password') => $password,
						), array(
					trans('adminController.username') => 'required|unique:admin,username',
					trans('adminController.password') => 'required|min:6'
						), array(
					trans('adminController.username') => trans('adminController.name_need'),
					trans('adminController.username.unique') => trans('adminController.name_unique'),
					trans('adminController.password') => trans('adminController.password_six')
						)
		);

		if ($validator->fails()) {
			$error_messages = $validator->messages()->first();
			Session::put('msg', $error_messages);
			$permissions = DB::table('permission')->get();
			$admin = Admin::all();
			return View::make('add_admin')
							->with('title', trans('adminController.add_admin'))
							->with('page', 'add_admin')
							->with('admin', $admin)
							->with('permission', $permissions);
		} else {

			$admin = new Admin;
			$password = Hash::make(Input::get('password'));
			$admin->username = $username;
			$admin->password = $admin->password = $password;
			$admin->save();

			if(Input::has('permission')){
				foreach ($permissions as $permission) {

					$admin_id = Admin::where('username', $username)->first();
					$admin_permission = new AdminPermission;
					$admin_permission->admin_id = $admin_id->id;
					$admin_permission->permission_id = $permission;

					$admin_permission->save();
				}
			}

			return Redirect::to("/admin/admins?success=1");
		}
	}

	public function edit_admins() {
		$id = Request::segment(4);
		$success = Input::get('success');
		$admin = Admin::find($id);
		$permissions = DB::table('permission')->get();
		$admin_permission = AdminPermission::where('admin_id', $id)->get();
		//Log::info("admin = " . print_r($admin, true));
		$title = ucwords(trans('adminController.edit_admin') . ": " . $admin->username);
		if ($admin) {
			return View::make('edit_admin')
							->with('title', $title)
							->with('page', 'settings')
							->with('success', $success)
							->with('admin', $admin)
							->with('permission', $permissions)
							->with('adminPermission', $admin_permission);
		} else {
			return View::make('notfound')->with('title', trans('adminController.page_not_found'))->with('page', trans('adminController.page_not_found'));
		}
	}

	public function update_admin() {

		$admin = Admin::find(Input::get('id'));
		$username = Input::get('username');
		$old_pass = Input::get('old_password');
		$new_pass = Input::get('new_password');
		$address = Input::get('my_address');
		$latitude = Input::get('latitude');
		$longitude = Input::get('longitude');
		$permissions = Input::get('permission');
		$permissions2 = DB::table('permission')->get();

		$admin_permission = AdminPermission::where('admin_id', Input::get('id'))->get();

		$validator = Validator::make(
						array(
					trans('adminController.username') => $username,
					trans('adminController.old_pass') => $old_pass,
					trans('adminController.new_pass') => $new_pass,
						), array(
					trans('adminController.username') => 'required',
					trans('adminController.old_pass') => 'required',
					trans('adminController.new_pass') => 'required|min:6'
						), array(
					trans('adminController.username') => trans('adminController.name_need'),
					trans('adminController.old_pass') => trans('adminController.password_not_valid'),
					trans('adminController.new_pass') => trans('adminController.password_six')
						)
		);

		if ($validator->fails()) {
			$error_messages = $validator->messages()->first();
			Session::put('msg', $error_messages);
			if ($admin) {
				$title = ucwords(trans('adminController.edit_admin') . ": " . $admin->username);
				return View::make('edit_admin')
								->with('title', $title)
								->with('page', 'admins')
								->with('success', '')
								->with('admin', $admin)
								->with('permission', $permissions2)
								->with('adminPermission', $permissions);
			} else {
				return View::make('notfound')->with('title', trans('adminController.page_not_found'))->with('page', trans('adminController.page_not_found'));
			}
		} else {

			$admin->username = $username;
			$admin->latitude = $latitude;
			$admin->longitude = $longitude;
			$admin->address = $address;
			$helpAdmin = 0;

			if ($new_pass != NULL) {
				$check_pass = Hash::check($old_pass, $admin->password);
				if ($check_pass) {
					$admin->password = $admin->password = Hash::make($new_pass);
					//Log::info('admin password changed');
				}
			}
			$admin->save();

			if(Input::has('permission')){
				foreach ($permissions as $permission) {

					if($helpAdmin == 0){
						AdminPermission::where('admin_id', Input::get('id'))->delete();
						$helpAdmin = 1;
					}

					$admin_permission = new AdminPermission;
					$admin_permission->admin_id = Input::get('id');
					$admin_permission->permission_id = $permission;

					$admin_permission->save();
				}
			}
			return Redirect::to("/admin/admins");
		}
	}

	public function delete_admin() {
		$id = Request::segment(4);
		$success = Input::get('success');
		$admin = Admin::find($id);
		if ($admin) {
			Admin::where('id', $id)->delete();
			return Redirect::to("/admin/admins?success=1");
		} else {
			return View::make('notfound')->with('title', trans('adminController.page_not_found'))->with('page', trans('adminController.page_not_found'));
		}
	}

	public function banking_provider() {
		$id = Request::segment(4);
		$success = Input::get('success');
		$provider = Provider::find($id);
		if ($provider) {
			$title = ucwords(trans('provider.bank_detail') . ": " . $provider->first_name . " " . $provider->last_name);
			if (Config::get('app.default_payment') == 'stripe') {
				return View::make('banking_provider_stripe')
								->with('title', $title)
								->with('page', 'providers')
								->with('success', $success)
								->with('provider', $provider);
			} else {
				return View::make('banking_provider_braintree')
								->with('title', $title)
								->with('page', 'providers')
								->with('success', $success)
								->with('provider', $provider);
			}
		} else {
			return View::make('notfound')->with('title', trans('adminController.page_not_found'))->with('page', trans('adminController.page_not_found'));
		}
	}

	public function providerB_bankingSubmit() {
		$this->_braintreeConfigure();
		$result = new stdClass();
		$result = Braintree_MerchantAccount::create(
						array(
							'individual' => array(
								'firstName' => Input::get('first_name'),
								'lastName' => Input::get('last_name'),
								'email' => Input::get('email'),
								'phone' => Input::get('phone'),
								'dateOfBirth' => date('Y-m-d', strtotime(Input::get('dob'))),
								'ssn' => Input::get('ssn'),
								'address' => array(
									'streetAddress' => Input::get('streetAddress'),
									'locality' => Input::get('locality'),
									'region' => Input::get('region'),
									'postalCode' => Input::get('postalCode')
								)
							),
							'funding' => array(
								'descriptor' => 'UberForX',
								'destination' => Braintree_MerchantAccount::FUNDING_DESTINATION_BANK,
								'email' => Input::get('bankemail'),
								'mobilePhone' => Input::get('bankphone'),
								'accountNumber' => Input::get('accountNumber'),
								'routingNumber' => Input::get('routingNumber')
							),
							'tosAccepted' => true,
							'masterMerchantAccountId' => Config::get('app.braintree_merchant_id'),
							'id' => "taxinow" . Input::get('id')
						)
		);

		//Log::info('res = ' . print_r($result, true));
		if ($result->success) {
			$pro = Provider::where('id', Input::get('id'))->first();
			$pro->merchant_id = $result->merchantAccount->id;
			$pro->save();
			//Log::info(print_r($pro, true));
			//Log::info('Adding banking details to provider from Admin = ' . print_r($result, true));
			return Redirect::to("/admin/providers");
		} else {
			//Log::info('Error in adding banking details: ' . $result->message);
			return Redirect::to("/admin/providers");
		}
	}

	public function providerS_bankingSubmit() {
		$id = Input::get('id');
		Stripe::setApiKey(Config::get('app.stripe_secret_key'));
		$token_id = Input::get('stripeToken');
		// Create a Recipient
		try {
			$recipient = Stripe_Recipient::create(array(
						"name" => Input::get('first_name') . " " . Input::get('last_name'),
						"type" => Input::get('type'),
						"bank_account" => $token_id,
						"email" => Input::get('email')
							)
			);

			//Log::info('recipient = ' . print_r($recipient, true));

			$pro = Provider::where('id', Input::get('id'))->first();
			$pro->merchant_id = $recipient->id;
			$pro->account_id = $recipient->active_account->id;
			$pro->last_4 = $recipient->active_account->last4;
			$pro->save();

			//Log::info('recipient added = ' . print_r($recipient, true));
		} catch (Exception $e) {
			//Log::info('Error in Stripe = ' . print_r($e, true));
		}
		return Redirect::to("/admin/providers");
	}

	public function index() {
		return Redirect::to('/admin/login');
	}

	public function get_document_types() {
		Session::forget('type');
		Session::forget('valu');
		$types = Document::paginate(10);
		$title = ucwords(trans('customize.Documents')); /* 'Document Types' */
		return View::make('list_document_types')
						->with('title', $title)
						->with('page', 'document-type')
						->with('types', $types);
	}

	public function get_promo_codes() {
		Session::forget('type');
		Session::forget('valu');
		$success = Input::get('success');
		// $promo_codes = PromoCodes::paginate(20)->orderBy('promo_codes.created_at', 'DESC');

		$promo_codes = DB::table('promo_codes')
			->orderBy('promo_codes.created_at', 'asc')
			->paginate(20);

		$title = ucwords(trans('customize.promo_codes')); /* 'Promo Codes' */
		return View::make('list_promo_codes')
						->with('title', $title)
						->with('page', 'promo_code')
						->with('success', $success)
						->with('promo_codes', $promo_codes)
						->with('order_type', 'id')
						->with('order', 0);

				// 		$requests = DB::table('request')
				// ->leftJoin('provider', 'request.confirmed_provider', '=', 'provider.id')
				// ->leftJoin('user', 'request.user_id', '=', 'user.id')
				// ->select('user.first_name as user_first_name', 'user.last_name as user_last_name', 'provider.first_name as provider_first_name', 'provider.last_name as provider_last_name', 'user.id as user_id', 'provider.id as provider_id', 'provider.merchant_id as provider_merchant', 'request.id as id', 'request.created_at as date', 'request.payment_mode', 'request.is_started', 'request.is_provider_arrived', 'request.payment_mode', 'request.is_completed', 'request.is_paid', 'request.is_provider_started', 'request.confirmed_provider'
				// 		, 'request.status', 'request.time', 'request.distance', 'request.total', 'request.is_cancelled', 'request.transfer_amount')
				// ->orderBy('request.created_at', 'DESC')
				// ->paginate(20);


						
	}

	public function searchdoc() {
		$valu = $_GET['valu'];
		$type = $_GET['type'];
		Session::put('valu', $valu);
		Session::put('type', $type);
		if ($type == 'docid') {
			$types = Document::where('id', $valu)->paginate(10);
		} elseif ($type == 'docname') {
			$types = Document::where('name', 'like', '%' . $valu . '%')->paginate(10);
		}
		$title = ucwords(trans('customize.Documents')); /* 'Document Types' */
		return View::make('list_document_types')
						->with('title', $title)
						->with('page', 'document-type')
						->with('types', $types);
	}

	public function delete_document_type() {
		$id = Request::segment(4);
		Document::where('id', $id)->delete();
		return Redirect::to("/admin/document-types");
	}

	public function edit_document_type() {
		$id = Request::segment(4);
		$success = Input::get('success');
		$document_type = Document::find($id);

		if ($document_type) {
			$id = $document_type->id;
			$name = $document_type->name;
			$title = ucwords(trans('adminController.edit_type_doc') . " : " . $name);
		} else {
			$id = 0;
			$name = "";
			$title = trans('adminController.add_type_doc');
		}

		return View::make('edit_document_type')
						->with('title', $title)
						->with('page', 'document-type')
						->with('success', $success)
						->with('id', $id)
						->with('name', $name);
	}

	public function update_document_type() {
		$id = Input::get('id');
		$name = Input::get('name');

		if ($id == 0) {
			$document_type = new Document;
		} else {
			$document_type = Document::find($id);
		}


		$document_type->name = $name;
		$document_type->save();

		return Redirect::to("/admin/document-type/edit/$document_type->id?success=1");
	}

	public function get_provider_types() {

		$settings = Settings::where('key', 'default_distance_unit')->first();
		$unit = $settings->value;
		if ($unit == 0) {
			$unit_set = trans('setting.km');
		} elseif ($unit == 1) {
			$unit_set = trans('setting.miles');
		}
		$types = ProviderType::paginate(10);
		$title = ucwords(trans('adminController.type_provider')); /* 'Provider Types' */
		return View::make('list_provider_types')
						->with('title', $title)
						->with('page', 'provider-type')
						->with('unit_set', $unit_set)
						->with('types', $types);
	}

	public function searchpvtype() {
		$valu = $_GET['valu'];
		$type = $_GET['type'];
		Session::put('valu', $valu);
		Session::put('type', $type);
		if ($type == 'provid') {
			$types = ProviderType::where('id', $valu)->paginate(10);
		} elseif ($type == 'provname') {
			$types = ProviderType::where('name', 'like', '%' . $valu . '%')->paginate(10);
		}
		$settings = Settings::where('key', 'default_distance_unit')->first();
		$unit = $settings->value;
		if ($unit == 0) {
			$unit_set = trans('setting.km');
		} elseif ($unit == 1) {
			$unit_set = trans('setting.miles');
		}
		//  $title = ucwords(trans('customize.Provider') . " " . trans('customize.Types')); /* 'Provider Types' */
		$title = ucwords(trans('customize.Types') . " de " . trans('customize.Provider')); /* 'Provider Types' */
		return View::make('list_provider_types')
						->with('title', $title)
						->with('page', 'provider-type')
						->with('unit_set', $unit_set)
						->with('types', $types);
	}

	public function delete_provider_type() {
		$id = Request::segment(4);
		ProviderType::where('id', $id)->where('is_default', 0)->delete();
		return Redirect::to("/admin/provider-types");
	}

	public function edit_provider_type() {
		$id = Request::segment(4);
		$success = Input::get('success');
		$providers_type = ProviderType::find($id);
		$settings = Settings::where('key', 'default_distance_unit')->first();
		$unit = $settings->value;
		if ($unit == 0) {
			$unit_set = trans('setting.km');
		} elseif ($unit == 1) {
			$unit_set = trans('setting.miles');
		}
		$business_model = Settings::getDefaultBusinessModel();

		if ($providers_type) {
			$id 						= $providers_type->id;
			$name 						= $providers_type->name;
			$is_default 				= $providers_type->is_default;
			$base_price 				= $providers_type->base_price;
			$base_distance 				= $providers_type->base_distance;
			$price_per_unit_distance 	= $providers_type->price_per_unit_distance;
			$price_per_unit_time 		= $providers_type->price_per_unit_time;
			$icon 						= $providers_type->icon;
			$icon_maps 					= $providers_type->icon_maps;
			$base_price 				= $providers_type->base_price;
			$max_size 					= $providers_type->max_size;
			$is_visible 				= $providers_type->is_visible;
			$destination_visible 		= $providers_type->destination_visible;
			$commission_rate 			= $providers_type->commission_rate;
			$maximum_distance 			= $providers_type->maximum_distance;
			$sub_category_screen_visible= $providers_type->sub_category_screen_visible;
			$charge_provider_return		= $providers_type->charge_provider_return;
			$color 						= $providers_type->color;

			$title = ucwords(trans('adminController.edit_type_provider') . " : " . $name);
		} else {
			$id 						= 0;
			$name 						= "";
			$is_default 				= "";
			$base_distance 				= 1;
			$base_price 				= "";
			$price_per_unit_time 		= "";
			$price_per_unit_distance 	= "";
			$icon 						= "";
			$icon_maps 					= null;
			$base_price 				= '';
			$max_size 					= '';
			$is_visible 				= "";
			$destination_visible 		= "";
			$commission_rate 			= "";
			$maximum_distance 			= "";
			$sub_category_screen_visible= 0;
			$charge_provider_return		= 0;
			$color 						= "#333333" ;
			$title = trans('adminController.add_type_provider');
		}

		$categories = ProviderTypeCategory::all();

		return View::make('edit_provider_type')
						->with('title', $title)
						->with('page', 'provider-type')
						->with('success', $success)
						->with('id', $id)
						->with('base_price', $base_price)
						->with('base_distance', $base_distance)
						->with('max_size', $max_size)
						->with('name', $name)
						->with('is_default', $is_default)
						->with('base_price', $base_price)
						->with('icon', $icon)
						->with('icon_maps', $icon_maps)
						->with('is_visible', $is_visible)
						->with('destination_visible', $destination_visible)
						->with('price_per_unit_time', $price_per_unit_time)
						->with('price_per_unit_distance', $price_per_unit_distance)
						->with('unit_set', $unit_set)
						->with('commission_rate', $commission_rate)
						->with('categories', $categories)
						->with('maximum_distance', $maximum_distance)
						->with('charge_provider_return', $charge_provider_return)
						->with('sub_category_screen_visible', $sub_category_screen_visible)
						->with('color', $color)
						->with('business_model', $business_model)
						;
	}

	public function update_provider_type() {
		$id = Input::get('id');
		$name = ucwords(trim(Input::get('name')));
		$base_distance = trim(Input::get('base_distance'));
		if ($base_distance == "" || $base_distance == 0) {
			$base_distance = 1;
		}
		$base_price = trim(Input::get('base_price'));
		if ($base_price == "" || $base_price == 0) {
			$base_price = 0;
		}
		$distance_price = trim(Input::get('distance_price'));
		if ($distance_price == "" || $distance_price == 0) {
			$distance_price = 0;
		}
		$time_price = trim(Input::get('time_price'));
		if ($time_price == "" || $time_price == 0) {
			$time_price = 0;
		} 
		
		$max_size = trim(Input::get('max_size'));
		if ( ctype_digit($max_size) ) {
			$max_size = intval($max_size);
			if ($max_size <= 0) {
				$max_size = 1;
			}
		} else {
			$max_size = 1;
		}

		$is_default 			= Input::get('is_default');
		$is_visible 			= trim(Input::get('is_visible'));
		$destination_visible 	= trim(Input::get('destination_visible'));
		$commission_rate 		= trim(Input::get('commission_rate'));
		$maximum_distance 		= trim(Input::get('maximum_distance'));
		$charge_provider_return	= trim(Input::get('charge_provider_return'));
		$sub_category_screen_visible	= Input::get('sub_category_screen_visible');
		$color 					= trim(Input::get('color'));

		if ($is_default) {
			if ($is_default == 1) {
				ProviderType::where('is_default', 1)->update(array('is_default' => 0));
			}
		} else {
			$is_default = 0;
		}

		if ($id == 0) {
			$providers_type = new ProviderType;
		} else {
			$providers_type = ProviderType::find($id);
		}
		
		if (Input::hasFile('icon')) {
			// Upload File
			$file_name = time();
			$file_name .= rand();
			$ext = Input::file('icon')->getClientOriginalExtension();
			list($width, $height) = getimagesize(Input::file('icon'));
			/* echo "width : " . $width;
			  echo "height : " . $height; */
			if ($width == $height && $width >= 300 && $height >= 300) {
				Input::file('icon')->move(public_path() . "/uploads", $file_name . "." . $ext);
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

				if (isset($providers_type->icon)) {
					if ($providers_type->icon != "") {
						$icon = $providers_type->icon;
						unlink_image($icon);
					}
				}
				$providers_type->icon = $s3_url;
			} else {
				return Redirect::to("/admin/provider-type/edit/$providers_type->id?success=4");
			}
		}
		if (Input::hasFile('icon_maps')) {
			// Upload File
			$file_name = time();
			$file_name .= rand();
			$ext = Input::file('icon_maps')->getClientOriginalExtension();
			list($width, $height) = getimagesize(Input::file('icon_maps'));
			/* echo "width : " . $width;
			  echo "height : " . $height; */
			if ($width == $height && $width >= 300 && $height >= 300) {
				Input::file('icon_maps')->move(public_path() . "/uploads", $file_name . "." . $ext);
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

				if (isset($providers_type->icon_maps)) {
					if ($providers_type->icon_maps != "") {
						$icon_maps = $providers_type->icon_maps;
						unlink_image($icon_maps);
					}
				}
				$providers_type->icon_maps = $s3_url;
			} else {
				return Redirect::to("/admin/provider-type/edit/$providers_type->id?success=4");
			}
		}

		if ($base_price <= 0 || $distance_price <= 0 || $time_price <= 0) {
			return Redirect::to("/admin/provider-type/edit/$providers_type->id?success=3");
		}

		$providers_type->name 			= $name;
		$providers_type->base_distance 	= $base_distance;
		$providers_type->base_price 	= $base_price;
		$providers_type->price_per_unit_distance 	= $distance_price;
		$providers_type->price_per_unit_time 		= $time_price;
		$providers_type->max_size 	= $max_size;
		$providers_type->is_default = $is_default;
		$providers_type->is_visible = $is_visible;
		$providers_type->base_time = 0;
		$providers_type->time_unit = 0;
		$providers_type->base_price_provider = 0;
		$providers_type->base_price_user = 0;
		$providers_type->commission_rate 		= floatval($commission_rate);
		$providers_type->destination_visible 	= boolval($destination_visible);
		$providers_type->maximum_distance 		= $maximum_distance;
		$providers_type->charge_provider_return	= $charge_provider_return;
		$providers_type->sub_category_screen_visible	= $sub_category_screen_visible;
		$providers_type->color 					= $color;
		$providers_type->save();

		// salvar provider_services associacao
		$idsProviderServices = array();
		if(Input::has('categories')){
			foreach (Input::get('categories') as $category_id) {
				$providerService = ProviderServices::findByProviderIdAndTypeIdAndCategoryId(0, $providers_type->id, $category_id);

				$providertypecategory = ProviderTypeCategory::find($category_id);

				$providerType = $providers_type;

				if(!$providerService){
					$providerService = new ProviderServices;
					$providerService->provider_id 				= 0 ; 
					$providerService->type 						= $providerType->id; 
					$providerService->category					= $providertypecategory->id ; 
					$providerService->price_per_unit_distance 	= $providerType->price_per_unit_distance; 
					$providerService->price_per_unit_time 		= $providerType->price_per_unit_time; 
					$providerService->base_price 				= $providerType->base_price ; 
					$providerService->base_distance 			= $providerType->base_distance ; 
					$providerService->base_time 				= $providerType->base_time ; 
					$providerService->distance_unit 			= $providerType->base_price ; 
					$providerService->time_unit 				= $providerType->time_unit ; 
					$providerService->base_price_provider 		= $providerType->base_price_provider ; 
					$providerService->base_price_user 			= $providerType->base_price_user ; 
					$providerService->commission_rate 			= $providerType->commission_rate ; 
					$providerService->is_visible				= $providertypecategory->is_visible ;

					$providerService->save();
				}

				array_push($idsProviderServices, $providerService->id);
			}
		}

		// remove provider_services foram desselecionados
		if(count($idsProviderServices)){
			ProviderServices::where('provider_id', '=', '0')
							->where('type', '=', $providers_type->id)
							->whereNotIn('id', $idsProviderServices)
							->delete();
		}

		if(count($idsProviderServices) == 0){
			ProviderServices::where('provider_id', '=', '0')
							->where('type', '=', $providers_type->id)
							->whereNotIn('id', $idsProviderServices)
							->delete();
		}

		return Redirect::to("/admin/provider-type/edit/$providers_type->id?success=1");
	}

	public function get_info_pages() {

		$informations = Information::paginate(10);
		$title = ucwords(trans('adminController.page_info')); /* 'Information Pages' */
		return View::make('list_info_pages')
						->with('title', $title)
						->with('page', 'information')
						->with('informations', $informations);
	}

	public function searchinfo() {
		$valu = $_GET['valu'];
		$type = $_GET['type'];
		Session::put('valu', $valu);
		Session::put('type', $type);
		if ($type == 'infoid') {
			$informations = Information::where('id', $valu)->paginate(10);
		} elseif ($type == 'infotitle') {
			$informations = Information::where('title', 'like', '%' . $valu . '%')->paginate(10);
		}
		// $title = ucwords(trans('customize.Information') . " Pages | Search Result"); /* 'Information Pages | Search Result' */
		$title = ucwords(trans('customize.Information') . " | " . trans('adminController.search_result')); /* 'Information Pages | Search Result' */
		return View::make('list_info_pages')
						->with('title', $title)
						->with('page', 'information')
						->with('informations', $informations);
	}

	public function delete_info_page() {
		$id = Request::segment(4);
		Information::where('id', $id)->delete();
		return Redirect::to("/admin/informations");
	}

	public function skipSetting() {
		setcookie("skipInstallation", "admincookie", time() + (86400 * 30));
		return Redirect::to("/admin/report/");
	}

	public function edit_info_page() {
		$id = Request::segment(4);
		$success = Input::get('success');
		$information = Information::find($id);
		if ($information) {
			$id = $information->id;
			$title = $information->title;
			$description = $information->content;
			$icon = $information->icon;
			$type = $information->type;
			$page_title = ucwords(trans('adminController.edit_info') . " : " . $title);
		} else {
			$id = 0;
			$title = "";
			$description = "";
			$icon = "";
			$type = "";
			$page_title = trans('adminController.add_info');
		}
		return View::make('edit_info_page')
						->with('title', $page_title)
						->with('page', 'information')
						->with('success', $success)
						->with('id', $id)
						->with('info_title', $title)
						->with('icon', $icon)
						->with('type', $type)
						->with('description', $description);
	}

	public function update_info_page() {
		$id 			= Input::get('id');
		$title 			= Input::get('title');
		$type 			= Input::get('type');
		$content 		= Input::get('description');
		$urlKey 		= sanitize_title_with_dashes($title) ; 

		if ($id == 0) {
			$information = new Information;
			$urlKeyValidation = 'required|unique:information,url_key' ;
		} else {
			$information = Information::find($id);
			$urlKeyValidation = 'required|unique:information,url_key,'.$id ;
		}

		$validator = Validator::make(
			array(
				'title' 		=> $title,
				'content' 		=> $content,
				'urlKey' 		=> $urlKey
			),
			array(
				'title' 		=> 'required',
				'content' 		=> 'required',
				'urlKey' 		=> $urlKeyValidation
			)
		);

		if($validator->fails()){
			return Redirect::to("admin/information/edit/".$id)->withErrors($validator);
		}
		else {

			if (Input::hasFile('icon')) {
				// Upload File
				$file_name = time();
				$file_name .= rand();
				$ext = Input::file('icon')->getClientOriginalExtension();
				Input::file('icon')->move(public_path() . "/uploads", $file_name . "." . $ext);
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

				if (isset($information->icon)) {
					if ($information->icon != "") {
						$icon = $information->icon;
						unlink_image($icon);
					}
				}
				$information->icon = $s3_url;
			}

			$information->title 	= $title;
			$information->url_key 	= $urlKey;
			$information->type 		= $type;
			$information->content 	= $content;

			$information->save();

			return Redirect::to("/admin/informations");

		}
	}

	public function map_view() {
		$settings = Settings::where('key', 'map_center_latitude')->first();
		$center_latitude = $settings->value;
		$settings = Settings::where('key', 'map_center_longitude')->first();
		$center_longitude = $settings->value;
		$title = ucwords(trans('customize.map_view')); /* 'Map View' */
		return View::make('map_view')
						->with('title', $title)
						->with('page', 'map-view')
						->with('center_longitude', $center_longitude)
						->with('center_latitude', $center_latitude);
	}

	public function providers() {
		Session::forget('type');
		Session::forget('valu');
		Session::forget('che');
		
		$subQuery = DB::table('request_meta')
				->select(DB::raw('count(*)'))
				->whereRaw('provider_id = provider.id and status != 0');
		$subQuery1 = DB::table('request_meta')
				->select(DB::raw('count(*)'))
				->whereRaw('provider_id = provider.id and status=1');

		$providers = Provider::select('provider.*', 'provider_status.name as status_name', DB::raw("(" . $subQuery->toSql() . ") as 'total_requests'"), DB::raw("(" . $subQuery1->toSql() . ") as 'accepted_requests'"))
				->leftJoin('provider_status', 'provider.status_id', '=', 'provider_status.id')
				->where('deleted_at', NULL)
				/* ->where('provider.is_deleted', 0) */
				->orderBy('provider.created_at', 'DESC')
				->paginate(20);

		$title = ucwords(trans('customize.Provider') . trans('adminController.plural1')); /* 'Providers' */
		return View::make('providers')
						->with('title', $title)
						->with('page', 'providers')
						->with('providers', $providers)
						->with('type','id')
						->with('order',1);
	}

	//Referral Statistics
	public function referral_details() {
		$user_id = Request::segment(4);
		$ledger = Ledger::where('user_id', $user_id)->first();
		$users = User::where('referred_by', $user_id)->paginate(10);
		$title = ucwords(trans('customize.User') . trans('adminController.plural1') . " | " . trans('adminController.cupon_statistics')); /* 'User | Coupon Statistics' */
		return View::make('referred')
						->with('page', 'users')
						->with('title', $title)
						->with('users', $users)
						->with('ledger', $ledger);
	}

	// Search Providers from Admin Panel
	public function searchpv() {
		$valu = $_GET['valu'];
		$type = $_GET['type'];
		Session::put('valu', $valu);
		Session::put('type', $type);
		if ($type == 'provid') {
			/* $providers = Provider::where('id', $valu)->paginate(10); */
			$subQuery = DB::table('request_meta')
					->select(DB::raw('count(*)'))
					->whereRaw('provider_id = provider.id and status != 0');
			$subQuery1 = DB::table('request_meta')
					->select(DB::raw('count(*)'))
					->whereRaw('provider_id = provider.id and status=1');

			$providers = Provider::select('provider.*', 'provider_status.name as status_name', DB::raw("(" . $subQuery->toSql() . ") as 'total_requests'"), DB::raw("(" . $subQuery1->toSql() . ") as 'accepted_requests'"))
					->leftJoin('provider_status', 'provider.status_id', '=', 'provider_status.id')
					->where('deleted_at', NULL)
					/* ->where('provider.is_deleted', 0) */
					->where('provider.id', $valu)
					->paginate(10);
		} elseif ($type == 'pvname') {
			/* $providers = Provider::where('first_name', 'like', '%' . $valu . '%')->orWhere('last_name', 'like', '%' . $valu . '%')->paginate(10); */
			$subQuery = DB::table('request_meta')
					->select(DB::raw('count(*)'))
					->whereRaw('provider_id = provider.id and status != 0');
			$subQuery1 = DB::table('request_meta')
					->select(DB::raw('count(*)'))
					->whereRaw('provider_id = provider.id and status=1');

			$providers = Provider::select ('provider.*', 'provider_status.name as status_name', DB::raw("(" . $subQuery->toSql() . ") as 'total_requests'"), DB::raw("(" . $subQuery1->toSql() . ") as 'accepted_requests'"))
					->leftJoin('provider_status', 'provider.status_id', '=', 'provider_status.id')
					->where('deleted_at', NULL)
					/* ->where('provider.is_deleted', 0) */
					->where('provider.first_name', 'like', '%' . $valu . '%')->orWhere('provider.last_name', 'like', '%' . $valu . '%')
					->paginate(10);
		} elseif ($type == 'pvemail') {
			/* $providers = Provider::where('email', 'like', '%' . $valu . '%')->paginate(10); */
			$subQuery = DB::table('request_meta')
					->select(DB::raw('count(*)'))
					->whereRaw('provider_id = provider.id and status != 0');
			$subQuery1 = DB::table('request_meta')
					->select(DB::raw('count(*)'))
					->whereRaw('provider_id = provider.id and status=1');

			$providers = Provider::select('provider.*', 'provider_status.name as status_name', DB::raw("(" . $subQuery->toSql() . ") as 'total_requests'"), DB::raw("(" . $subQuery1->toSql() . ") as 'accepted_requests'"))
					->leftJoin('provider_status', 'provider.status_id', '=', 'provider_status.id')
					->where('deleted_at', NULL)
					/* ->where('provider.is_deleted', 0) */
					->where('provider.email', 'like', '%' . $valu . '%')
					->paginate(10);
		} elseif ($type == 'bio') {
			/* $providers = Provider::where('bio', 'like', '%' . $valu . '%')->paginate(10); */
			$subQuery = DB::table('request_meta')
					->select(DB::raw('count(*)'))
					->whereRaw('provider_id = provider.id and status != 0');
			$subQuery1 = DB::table('request_meta')
					->select(DB::raw('count(*)'))
					->whereRaw('provider_id = provider.id and status=1');

			$providers = Provider::select('provider.*', 'provider_status.name as status_name', DB::raw("(" . $subQuery->toSql() . ") as 'total_requests'"), DB::raw("(" . $subQuery1->toSql() . ") as 'accepted_requests'"))
					->leftJoin('provider_status', 'provider.status_id', '=', 'provider_status.id')
					->where('deleted_at', NULL)
					/* ->where('provider.is_deleted', 0) */
					->where('provider.bio', 'like', '%' . $valu . '%')
					->paginate(10);
		} elseif ($type == 'pvaddress') {
			/* $providers = Provider::where('bio', 'like', '%' . $valu . '%')->paginate(10); */
			$subQuery = DB::table('request_meta')
					->select(DB::raw('count(*)'))
					->whereRaw('provider_id = provider.id and status != 0');
			$subQuery1 = DB::table('request_meta')
					->select(DB::raw('count(*)'))
					->whereRaw('provider_id = provider.id and status=1');

			$providers =Provider::select('provider.*', 'provider_status.name as status_name', DB::raw("(" . $subQuery->toSql() . ") as 'total_requests'"), DB::raw("(" . $subQuery1->toSql() . ") as 'accepted_requests'"))
					->leftJoin('provider_status', 'provider.status_id', '=', 'provider_status.id')
					->where('deleted_at', NULL)
					/* ->where('provider.is_deleted', 0) */
					->where('provider.address', 'like', '%' . $valu . '%')
					->paginate(10);
		}
		$title = ucwords(trans('customize.Provider') . " | " . trans('adminController.search_result')); /* 'Providers | Search Result' */
		//echo  "<script>console.log( 'PROVIDERS: " . $type . "' );</script>";
		return View::make('providers')
						->with('title', $title)
						->with('page', 'providers')
						->with('providers', $providers);
	}

	public function providers_xml() {

		$providers = Provider::where('');
		$response = "";
		$response .= '<markers>';

		$providers = DB::table('provider')
				->select('provider.*')
				->get();
		$provider_ids = array();
		foreach ($providers as $provider) {
			if ($provider->is_active == 1 && $provider->is_available == 1 && $provider->is_approved == 1/* && $provider->is_deleted == 0 */) {
				$response .= '<marker ';
				$response .= 'name="' . $provider->first_name . " " . $provider->last_name . '" ';
				$response .= 'client_name="' . $provider->first_name . " " . $provider->last_name . '" ';
				$response .= 'contact="' . $provider->phone . '" ';
				$response .= 'amount="' . 0 . '" ';
				$response .= 'angl="' . $provider->bearing . '" ';
				$response .= 'lat="' . $provider->latitude . '" ';
				$response .= 'lng="' . $provider->longitude . '" ';
				$response .= 'id="' . $provider->id . '" ';
				$response .= 'type="driver_free" ';
				$response .= '/>';
				array_push($provider_ids, $provider->id);
			} else if ($provider->is_active == 1 && $provider->is_available == 0 && $provider->is_approved == 1/* && $provider->is_deleted == 0 */) {
				$response .= '<marker ';
				$response .= 'name="' . $provider->first_name . " " . $provider->last_name . '" ';
				$response .= 'client_name="' . $provider->first_name . " " . $provider->last_name . '" ';
				$response .= 'contact="' . $provider->phone . '" ';
				$response .= 'amount="' . 0 . '" ';
				$response .= 'angl="' . $provider->bearing . '" ';
				$response .= 'lat="' . $provider->latitude . '" ';
				$response .= 'lng="' . $provider->longitude . '" ';
				$response .= 'id="' . $provider->id . '" ';
				$response .= 'type="driver_on_trip" ';
				$response .= '/>';
				array_push($provider_ids, $provider->id);
			} else if (($provider->is_active == 0 || $provider->is_active == 1) && ($provider->is_available == 0 || $provider->is_available == 1) && $provider->is_approved == 0 /* && $provider->is_deleted == 0 */) {
				$response .= '<marker ';
				$response .= 'name="' . $provider->first_name . " " . $provider->last_name . '" ';
				$response .= 'client_name="' . $provider->first_name . " " . $provider->last_name . '" ';
				$response .= 'contact="' . $provider->phone . '" ';
				$response .= 'amount="' . 0 . '" ';
				$response .= 'angl="' . $provider->bearing . '" ';
				$response .= 'lat="' . $provider->latitude . '" ';
				$response .= 'lng="' . $provider->longitude . '" ';
				$response .= 'id="' . $provider->id . '" ';
				$response .= 'type="driver_not_approved" ';
				$response .= '/>';
				array_push($provider_ids, $provider->id);
			} /* else if (($provider->is_active == 0 || $provider->is_active == 1) && ($provider->is_available == 0 || $provider->is_available == 1) && ($provider->is_approved == 0 || $provider->is_approved == 1) && $provider->is_deleted == 1) {
			  $response .= '<marker ';
			  $response .= 'name="' . $provider->first_name . " " . $provider->last_name . '" ';
			  $response .= 'client_name="' . $provider->first_name . " " . $provider->last_name . '" ';
			  $response .= 'contact="' . $provider->phone . '" ';
			  $response .= 'amount="' . $provider->topup_bal . '" ';
			  $response .= 'licence_plate="' . $provider->licence_plate . '" ';
			  $response .= 'lat="' . $provider->latitude . '" ';
			  $response .= 'lng="' . $provider->longitude . '" ';
			  $response .= 'id="' . $provider->id . '" ';
			  $response .= 'company_name="' . $provider->company_name . '" ';
			  $response .= 'type="driver_deleted" ';
			  $response .= '/>';
			  array_push($provider_ids, $provider->id);
			  } */
		}

		/* // busy providers
		  $providers = DB::table('provider')
		  ->where('provider.is_active', 1)
		  ->where('provider.is_available', 0)
		  ->where('provider.is_approved', 1)
		  ->select('provider.id', 'provider.phone', 'provider.first_name', 'provider.last_name', 'provider.latitude', 'provider.longitude')
		  ->get();

		  $provider_ids = array();


		  foreach ($providers as $provider) {
		  $response .= '<marker ';
		  $response .= 'name="' . $provider->first_name . " " . $provider->last_name . '" ';
		  $response .= 'client_name="' . $provider->first_name . " " . $provider->last_name . '" ';
		  $response .= 'contact="' . $provider->phone . '" ';
		  $response .= 'amount="' . 0 . '" ';
		  $response .= 'lat="' . $provider->latitude . '" ';
		  $response .= 'lng="' . $provider->longitude . '" ';
		  $response .= 'id="' . $provider->id . '" ';
		  $response .= 'type="client_pay_done" ';
		  $response .= '/>';
		  array_push($provider_ids, $provider->id);
		  }

		  $provider_ids = array_unique($provider_ids);
		  $provider_ids_temp = implode(",", $provider_ids);

		  $providers = DB::table('provider')
		  ->where('provider.is_active', 0)
		  ->where('provider.is_approved', 1)
		  ->select('provider.id', 'provider.phone', 'provider.first_name', 'provider.last_name', 'provider.latitude', 'provider.longitude')
		  ->get();
		  foreach ($providers as $provider) {
		  $response .= '<marker ';
		  $response .= 'name="' . $provider->first_name . " " . $provider->last_name . '" ';
		  $response .= 'client_name="' . $provider->first_name . " " . $provider->last_name . '" ';
		  $response .= 'contact="' . $provider->phone . '" ';
		  $response .= 'amount="' . 0 . '" ';
		  $response .= 'lat="' . $provider->latitude . '" ';
		  $response .= 'lng="' . $provider->longitude . '" ';
		  $response .= 'id="' . $provider->id . '" ';
		  $response .= 'type="client_no_pay" ';
		  $response .= '/>';
		  array_push($provider_ids, $provider->id);
		  }
		  $provider_ids = array_unique($provider_ids);
		  $provider_ids = implode(",", $provider_ids);
		  if ($provider_ids) {
		  $query = "select * from provider where is_approved = 1 and id NOT IN ($provider_ids)";
		  } else {
		  $query = "select * from provider where is_approved = 1";
		  }
		  // free providers
		  $providers = DB::select(DB::raw($query));
		  foreach ($providers as $provider) {
		  $response .= '<marker ';
		  $response .= 'name="' . $provider->first_name . " " . $provider->last_name . '" ';
		  $response .= 'client_name="' . $provider->first_name . " " . $provider->last_name . '" ';
		  $response .= 'contact="' . $provider->phone . '" ';
		  $response .= 'amount="' . 0 . '" ';
		  $response .= 'lat="' . $provider->latitude . '" ';
		  $response .= 'lng="' . $provider->longitude . '" ';
		  $response .= 'id="' . $provider->id . '" ';
		  $response .= 'type="client" ';
		  $response .= '/>';
		  } */
		$response .= '</markers>';
		$content = View::make('providers_xml')->with('response', $response);
		return Response::make($content, '200')->header('Content-Type', 'text/xml');
	}

	public function users() {
		Session::forget('type');
		Session::forget('valu');
		$users = User::orderBy('id', 'DESC')->paginate(20);
		$title = ucwords(trans('customize.User')); /* 'Users' */
		
		Log::info('Teste');
		
		return View::make('users')
						->with('title', $title)
						->with('page', 'users')
						->with('users', $users)
						->with('type','id')
						->with('order',1);
	}


	public function orderfilterprovider() {

		$id = Input::get('id');
		$name = Input::get('name');
		$email = Input::get('email');
		$state = Input::get('state');
		$city = Input::get('city');
		$brand = Input::get('brand');
		$status = Input::get('status');
		$order = Input::get('order');
		$type = Input::get('type');


		if(ProviderStatus::where('name', $status)->first()){
			$status_id = ProviderStatus::where('name', $status)->first()->id;
		}
		else{
			$status_id = 0;
		}

		Session::put('id', $id);
		Session::put('name', $name);
		Session::put('email', $email);
		Session::put('state', $state);
		Session::put('city', $city);
		Session::put('order', $order);
		Session::put('status', $status);
		Session::put('type', $type);
		Session::put('brand', $brand);

		$query = Provider::WhereNotNull('provider.id');

		$subQuery = DB::table('request_meta')
				->select(DB::raw('count(*)'))
				->whereRaw('provider_id = provider.id and status != 0');

		$subQuery1 = DB::table('request_meta')
				->select(DB::raw('count(*)'))
				->whereRaw('provider_id = provider.id and status=1');

		$query = Provider::select('provider.*', 'provider_status.name as status_name', DB::raw("(" . $subQuery->toSql() . ") as 'total_requests'"), DB::raw("(" . $subQuery1->toSql() . ") as 'accepted_requests'"))
			->leftJoin('provider_status', 'provider.status_id', '=', 'provider_status.id');

		// filtros
		if($id != ""){
			$query = $query->where('provider.id', '=', $id);
		}
		if($name != ""){
			$query = $query->where('first_name', 'like', '%' . $name . '%')->orWhere('last_name', 'like', '%' . $name . '%');
		}
		if($email != ""){
			$query = $query->where('email', 'like', '%' . $email . '%');
		}
		if($state != ""){
			$query = $query->where('state', 'like', '%' . $state . '%');
		}
		if($city != ""){
			$query = $query->where('address_city', 'like', '%' . $city . '%');   
		}

		if ($brand!= "") {
			$query = $query->where('car_brand', 'like', '%' . $brand . '%');
		}

		if (Input::has('status') && $status_id != 0) {
			$query = $query->where('status_id', '=', $status_id);
			
		}

		// Ordenação 
		if($order == "" ){
			$query = $query->orderBy('id', 'asc');
		}else{
			
			if($order == 0){
				$query = $query->orderBy($type, 'asc');
			} else if($order == 1){
				$query = $query->orderBy($type, 'desc');
			}
		}

		$title = ucwords(trans('customize.Provider') . " | " . trans('adminController.search_result')); 

		return View::make('providers')
			->with('title', $title)
			->with('page', 'providers')
			->with('name', $name)
			->with('id', $id)
			->with('brand', $brand)
			->with('email', $email)
			->with('order', $order)
			->with('type', $type)
			->with('city', $city)
			->with('state', $state)
			->with('status', $status)
			->with('providers', $query->paginate(20));
	}

	//Filtro e ordenação na lista de usuarios
	public function orderfilteruser() {

		$id = Input::get('id');
		$name = Input::get('name');
		$email = Input::get('email');
		$state = Input::get('state');
		$address = Input::get('address');
		$order = Input::get('order');
		$debt = Input::get('debt');
		$type = Input::get('type');

		Session::put('id', $id);
		Session::put('name', $name);
		Session::put('email', $email);
		Session::put('state', $state);
		Session::put('address', $address);
		Session::put('order', $order);
		Session::put('debt', $debt);
		Session::put('type', $type);

		$query = User::WhereNotNull('user.id');

		// filtros
		if($id != ""){
			$query->where('id', '=', $id);
		}
		if($name != ""){
			$query->where('first_name', 'like', '%' . $name . '%');

			// $query = mysql_query('SELECT * WHERE %'.$name.'% like first_name or %'.$name.'% like last_name'  );
		}
		if($email != ""){
			$query->where('email', 'like', '%' . $email . '%');
		}
		if($state != ""){
			$query->where('state', 'like', '%' . $state . '%');
		}
		if($address != ""){
			$query->where('address', 'like', '%' . $address . '%');   
		}

		if (Input::has('debt') && Input::get('debt') != 0) {
			if ($debt == 1) {
				$query->where('debt', '>', 0);
			} else {
				$query->where('debt', '==', 0);
			}
		}

		// Ordenação (nome, email)

		
		if($order == "" ){
			$query->orderBy('id', 'asc');
		}else{
			
			if($order == 0){
				$query->orderBy($type, 'asc');
			} else if($order == 1){
				$query->orderBy($type, 'desc');
			}
		}

		$title = ucwords(trans('customize.User') . " | " . trans('adminController.search_result')); /* 'Users | Search Result' */

		return View::make('users')
			->with('title', $title)
			->with('page', 'users')
			->with('name', $name)
			->with('id', $id)
			->with('debt', $debt)
			->with('email', $email)
			->with('order', $order)
			->with('type', $type)
			->with('address', $address)
			->with('state', $state)
			->with('users', $query->paginate(20));
	}

	public function searchur() {
		$valu = $_GET['valu'];
		$type = $_GET['type'];
		$debt = $_GET['debt'];
		Session::put('valu', $valu);
		Session::put('type', $type);
		if ($type == 'userid') {
			$users = User::where('id', $valu)->paginate(10);
		} elseif ($type == 'username') {
			$users = User::where('first_name', 'like', '%' . $valu . '%')->orWhere('last_name', 'like', '%' . $valu . '%')->paginate(10);
		} elseif ($type == 'useremail') {
			$users = User::where('email', 'like', '%' . $valu . '%')->paginate(10);
		} elseif ($type == 'useraddress') {
			$users = User::where('address', 'like', '%' . $valu . '%')->orWhere('state', 'like', '%' . $valu . '%')->orWhere('country', 'like', '%' . $valu . '%')->paginate(10);
		} elseif ($type == '') {
			$users = User::where('address', 'like', '%' . $valu . '%')->orWhere('state', 'like', '%' . $valu . '%')->orWhere('country', 'like', '%' . $valu . '%')->paginate(10);
		} elseif ($type == 'debt') {
			if($dept == false){
				$users = User::where('debt', '!=', 0 )->paginate(10);
			} else{
				$users = User::where('debt', '>', 0 )->paginate(10);
			}
		}

		$title = ucwords(trans('customize.User') . " | " . trans('adminController.search_result')); /* 'Users | Search Result' */
		return View::make('users')
						->with('title', $title)
						->with('page', 'users')
						->with('users', $users);
	}


	// public function searchByKey() {
	// 	$valu = $_GET['valu'];
	// 	$key = $_GET['key']
	// 	Session::put('valu', $valu);
	// 	Session::put('key', $key);

	// 	if (gettype($key) == 'string'){
	// 		$users = User::where($key, 'like', '%' . $valu . '%')->paginate(10);
	// 	}
		
	// 	$title = ucwords(trans('customize.User') . " | " . trans('adminController.search_result')); /* 'Users | Search Result' */
	// 	return View::make('users')
	// 					->with('title', $title)
	// 					->with('page', 'users')
	// 					->with('users', $users);
	// }

	public function requests() {
		Session::forget('type');
		Session::forget('valu');
		$requests = DB::table('request')
				->leftJoin('provider', 'request.confirmed_provider', '=', 'provider.id')
				->leftJoin('user', 'request.user_id', '=', 'user.id')
				->select('user.first_name as user_first_name', 'user.last_name as user_last_name', 'provider.first_name as provider_first_name', 'provider.last_name as provider_last_name', 'user.id as user_id', 'provider.id as provider_id', 'provider.merchant_id as provider_merchant', 'request.id as id', 'request.created_at as date', 'request.payment_mode', 'request.is_started', 'request.is_provider_arrived', 'request.payment_mode', 'request.is_completed', 'request.is_paid', 'request.is_provider_started', 'request.confirmed_provider'
						, 'request.status', 'request.time', 'request.distance', 'request.total', 'request.is_cancelled', 'request.transfer_amount')
				->orderBy('request.created_at', 'DESC')
				->paginate(20);
		$setting = Settings::where('key', 'paypal')->first();
		$title = ucwords(trans('customize.Request')); /* 'Requests' */
		return View::make('requests')
						->with('title', $title)
						->with('page', 'requests')
						->with('requests', $requests)
						->with('setting', $setting)
						->with('order', 1)
						->with('type', 'id');
	}
	
	public function requestDelete(){
		$id = Request::segment(4);
		Requests::where('id', '=',$id)->delete();
		
		return Redirect::to('/admin/requests');
	}

	public function scheduled_requests() {
		Session::forget('type');
		Session::forget('valu');
		$schedules = DB::table('scheduled_requests')
				->leftJoin('user', 'scheduled_requests.user_id', '=', 'user.id')
				->select('user.first_name as user_first_name', 'user.last_name as user_last_name', 'user.id as user_id', 'scheduled_requests.id as id', 'scheduled_requests.created_at as date', 'scheduled_requests.time_zone', 'scheduled_requests.src_address', 'scheduled_requests.dest_address', 'scheduled_requests.promo_code', 'scheduled_requests.server_start_time', 'scheduled_requests.start_time', 'scheduled_requests.payment_mode')
				->orderBy('scheduled_requests.server_start_time', 'ASC')
				->paginate(10);
		$total_schedules = ScheduledRequests::count();
		$setting = Settings::where('key', 'paypal')->first();
		$title = ucwords(trans('customize.Schedules') . ": Total = " . $total_schedules);
		return View::make('schedules')
						->with('title', $title)
						->with('page', 'schedule')
						->with('schedules', $schedules)
						->with('setting', $setting);
	}

	// Search Providers from Admin Panel
	public function searchreq() {
		$valu = $_GET['valu'];
		$type = $_GET['type'];
		Session::put('valu', $valu);
		Session::put('type', $type);
		if ($type == 'reqid') {
			$requests = DB::table('request')
					->leftJoin('user', 'request.user_id', '=', 'user.id')
					->leftJoin('provider', 'request.current_provider', '=', 'provider.id')
					->groupBy('request.id')
					->select('user.first_name as user_first_name', 'user.last_name as user_last_name', 'provider.first_name as provider_first_name', 'provider.last_name as provider_last_name', 'user.id as user_id', 'provider.id as provider_id', 'request.id as id', 'request.created_at as date', 'request.*', 'request.is_provider_arrived', 'request.is_completed', 'request.is_paid', 'request.is_provider_started', 'request.confirmed_provider'
							, 'request.status', 'request.time', 'request.distance', 'request.total', 'request.is_cancelled', 'request.payment_mode')
					->where('request.id', $valu)
					->orderBy('request.created_at')
					->paginate(10);
		} elseif ($type == 'user') {
			$requests = DB::table('request')
					->leftJoin('user', 'request.user_id', '=', 'user.id')
					->leftJoin('provider', 'request.current_provider', '=', 'provider.id')
					->groupBy('request.id')
					->select('user.first_name as user_first_name', 'user.last_name as user_last_name', 'provider.first_name as provider_first_name', 'provider.last_name as provider_last_name', 'user.id as user_id', 'provider.id as provider_id', 'request.id as id', 'request.created_at as date', 'request.*', 'request.is_provider_arrived', 'request.is_completed', 'request.is_paid', 'request.is_provider_started', 'request.confirmed_provider'
							, 'request.status', 'request.time', 'request.distance', 'request.total', 'request.is_cancelled', 'request.payment_mode')
					->where('user.first_name', 'like', '%' . $valu . '%')
					->orWhere('user.last_name', 'like', '%' . $valu . '%')
					->orderBy('request.created_at')
					->paginate(10);
		} elseif ($type == 'provider') {
			$requests = DB::table('request')
					->leftJoin('user', 'request.user_id', '=', 'user.id')
					->leftJoin('provider', 'request.current_provider', '=', 'provider.id')
					->groupBy('request.id')
					->select('user.first_name as user_first_name', 'user.last_name as user_last_name', 'provider.first_name as provider_first_name', 'provider.last_name as provider_last_name', 'user.id as user_id', 'provider.id as provider_id', 'request.id as id', 'request.created_at as date', 'request.*', 'request.is_provider_arrived', 'request.is_completed', 'request.is_paid', 'request.is_provider_started', 'request.confirmed_provider'
							, 'request.status', 'request.time', 'request.distance', 'request.total', 'request.is_cancelled', 'request.payment_mode')
					->where('provider.first_name', 'like', '%' . $valu . '%')
					->orWhere('provider.last_name', 'like', '%' . $valu . '%')
					->orderBy('request.created_at')
					->paginate(10);
		} elseif ($type == 'payment') {
			if((strcasecmp($valu, trans('adminController.search_stored_card')) == 0) || (strcasecmp($valu, trans('adminController.search_cards')) == 0)){
				$value = 0;
			} elseif((strcasecmp($valu, trans('adminController.search_Pay_by_Cash')) == 0) || (strcasecmp($valu, trans('adminController.search_Cash')) == 0)){
				$value = 1;
			} elseif((strcasecmp($valu, trans('adminController.paypal')) == 0)){
				$value = 2;
			}

			$requests = DB::table('request')
					->leftJoin('user', 'request.user_id', '=', 'user.id')
					->leftJoin('provider', 'request.current_provider', '=', 'provider.id')
					->groupBy('request.id')
					->select('user.first_name as user_first_name', 'user.last_name as user_last_name', 'provider.first_name as provider_first_name', 'provider.last_name as provider_last_name', 'user.id as user_id', 'provider.id as provider_id', 'request.id as id', 'request.created_at as date', 'request.is_started', 'request.is_provider_arrived', 'request.is_completed', 'request.is_paid', 'request.is_provider_started', 'request.confirmed_provider'
							, 'request.status', 'request.time', 'request.distance', 'request.total', 'request.is_cancelled', 'request.payment_mode')
					->Where('request.payment_mode', $value)
					->orderBy('request.created_at')
					->paginate(10);
		}

		$setting = Settings::where('key', 'paypal')->first();
		$title = ucwords(trans('customize.Request') . " | " . trans('adminController.search_result')); /* 'Requests | Search Result' */
		return View::make('requests')
						->with('title', $title)
						->with('page', 'requests')
						->with('setting', $setting)
						->with('valu', $valu)
						->with('requests', $requests);
	}

	public function reviews() {
		Session::forget('type');
		Session::forget('valu');
		$provider_reviews = DB::table('review_provider')
				->leftJoin('provider', 'review_provider.provider_id', '=', 'provider.id')
				->leftJoin('user', 'review_provider.user_id', '=', 'user.id')
				->select('review_provider.id as review_id', 'review_provider.rating', 'review_provider.comment', 'user.first_name as user_first_name', 'user.last_name as user_last_name', 'provider.first_name as provider_first_name', 'provider.last_name as provider_last_name', 'user.id as user_id', 'provider.id as provider_id', 'review_provider.created_at')
				->orderBy('review_provider.id', 'DESC')
				->paginate(10);

		$user_reviews = DB::table('review_user')
				->leftJoin('provider', 'review_user.provider_id', '=', 'provider.id')
				->leftJoin('user', 'review_user.user_id', '=', 'user.id')
				->select('review_user.id as review_id', 'review_user.rating', 'review_user.comment', 'user.first_name as user_first_name', 'user.last_name as user_last_name', 'provider.first_name as provider_first_name', 'provider.last_name as provider_last_name', 'user.id as user_id', 'provider.id as provider_id', 'review_user.created_at')
				->orderBy('review_user.id', 'DESC')
				->paginate(10);
		$title = ucwords(trans('customize.Reviews')); /* 'Reviews' */
		return View::make('reviews')
						->with('title', $title)
						->with('page', 'reviews')
						->with('provider_reviews', $provider_reviews)
						->with('user_reviews', $user_reviews);

	}

	public function searchrev() {

		$valu = $_GET['valu'];
		$type = $_GET['type'];
		Session::put('valu', $valu);
		Session::put('type', $type);
		if ($type == 'user') {
			$provider_reviews = DB::table('review_provider')
					->leftJoin('provider', 'review_provider.provider_id', '=', 'provider.id')
					->leftJoin('user', 'review_provider.user_id', '=', 'user.id')
					->select('review_provider.id as review_id', 'review_provider.rating', 'review_provider.comment', 'user.first_name as user_first_name', 'user.last_name as user_last_name', 'provider.first_name as provider_first_name', 'provider.last_name as provider_last_name', 'user.id as user_id', 'provider.id as provider_id', 'review_provider.created_at')
					->where('user.first_name', 'like', '%' . $valu . '%')->orWhere('user.last_name', 'like', '%' . $valu . '%')
					->paginate(10);

			$reviews = DB::table('review_user')
					->leftJoin('provider', 'review_user.provider_id', '=', 'provider.id')
					->leftJoin('user', 'review_user.user_id', '=', 'user.id')
					->select('review_user.id as review_id', 'review_user.rating', 'review_user.comment', 'user.first_name as user_first_name', 'user.last_name as user_last_name', 'provider.first_name as provider_first_name', 'provider.last_name as provider_last_name', 'user.id as user_id', 'provider.id as provider_id', 'review_user.created_at')
					->where('user.first_name', 'like', '%' . $valu . '%')->orWhere('user.last_name', 'like', '%' . $valu . '%')
					->paginate(10);
		} elseif ($type == 'provider') {
			$provider_reviews = DB::table('review_provider')
					->leftJoin('provider', 'review_provider.provider_id', '=', 'provider.id')
					->leftJoin('user', 'review_provider.user_id', '=', 'user.id')
					->select('review_provider.id as review_id', 'review_provider.rating', 'review_provider.comment', 'user.first_name as user_first_name', 'user.last_name as user_last_name', 'provider.first_name as provider_first_name', 'provider.last_name as provider_last_name', 'user.id as user_id', 'provider.id as provider_id', 'review_provider.created_at')
					->where('provider.first_name', 'like', '%' . $valu . '%')->orWhere('provider.last_name', 'like', '%' . $valu . '%')
					->paginate(10);

			$reviews = DB::table('review_user')
					->leftJoin('provider', 'review_user.provider_id', '=', 'provider.id')
					->leftJoin('user', 'review_user.user_id', '=', 'user.id')
					->select('review_user.id as review_id', 'review_user.rating', 'review_user.comment', 'user.first_name as user_first_name', 'user.last_name as user_last_name', 'provider.first_name as provider_first_name', 'provider.last_name as provider_last_name', 'user.id as user_id', 'provider.id as provider_id', 'review_user.created_at')
					->where('provider.first_name', 'like', '%' . $valu . '%')->orWhere('provider.last_name', 'like', '%' . $valu . '%')
					->paginate(10);
		}
		$title = ucwords(trans('customize.Reviews') . " | " . trans('adminController.search_result')); /* 'Reviews | Search Result' */
		return View::make('reviews')
						->with('title', $title)
						->with('page', 'reviews')
						->with('provider_reviews', $provider_reviews)
						->with('user_reviews', $reviews)
		/* ->with('reviews', $reviews) */;
	}

	public function search() {
		Session::forget('type');
		Session::forget('valu');
		$type = Input::get('type');
		$q = Input::get('q');
		if ($type == 'user') {
			$users = User::where('first_name', 'like', '%' . $q . '%')
					->where('deleted_at', NULL)
					->orWhere('last_name', 'like', '%' . $q . '%')
					->paginate(10);
			$title = ucwords(trans('customize.User')); /* 'Users' */
			return View::make('users')
							->with('title', $title)
							->with('page', 'users')
							->with('users', $users);
		} else {

			$providers = Provider::where('deleted_at', NULL)
					->where('deleted_at', NULL)
					->where('first_name', 'like', '%' . $q . '%')
					->orWhere('last_name', 'like', '%' . $q . '%')
					->paginate(10);
			$title = ucwords(trans('customize.Provider')); /* 'Providers' */
			return View::make('providers')
							->with('title', $title)
							->with('page', 'providers')
							->with('providers', $providers);
		}
	}

	public function logout() {
		Auth::logout();
		return Redirect::to('/admin/login');
	}

	public function verify() {
		$username = Input::get('username');
		$password = Input::get('password');
		if (!Admin::count()) {
			$user = new Admin;
			$user->username = Input::get('username');
			$user->password = $user->password = Hash::make(Input::get('password'));
			$user->save();
			return Redirect::to('/admin/login');
		} else {
			if (Auth::attempt(array('username' => $username, 'password' => $password))) {
				if (Session::has('pre_admin_login_url')) {
					$url = Session::get('pre_admin_login_url');
					Session::forget('pre_admin_login_url');
					return Redirect::to($url);
				} else {
					$admin = Admin::where('username', 'like', '%' . $username . '%')->first();
					Session::put('admin_id', $admin->id);
					// return Redirect::to('/admin/report')->with('notify', 'installation Notification');
					return Redirect::to('/admin/report')->with('notify', trans('setting.install_notification'));
				}
			} else {
				return Redirect::to('/admin/login?error=1');
			}
		}
	}

	public function login() {
		$error = Input::get('error');
		if (Admin::count()) {

			return View::make('login')->with('title', trans('adminController.Enter'))->with('button', trans('adminController.Enter'))->with('error', $error);
		} else {
			
			return View::make('login')->with('title', trans('adminController.Create_admin'))->with('button', trans('adminController.Create'))->with('error', $error);
		}
	}
	
	/**
	 *  Calls Provider Profile View for editing
	 *
	 *  @return View::edit_provider
	 */
	public function edit_provider() {
		$id = Request::segment(4);
		$type = ProviderType::where('is_visible', '=', 1)->get();        
		$provserv = ProviderServices::where('provider_id', $id)->get();
		$success = Input::get('success');
		$provider = Provider::find($id);
		
		$providerTypes = ProviderType::where('is_visible', '=', true)->orderBy('name')->get();
		$prices = ProviderServices::where('provider_id', '=', $id)->get();

		$bank_account = ProviderBankAccount::where('provider_id', $id)->first();
		$banks  = Bank::all();

		$countries  = Country::all()->lists('NamePhoneCode', 'PlusPhoneCode');
		
		if ($provider) {
			$title = ucwords(trans('adminController.edit') . " " . trans('customize.Provider') . ": " . $provider->first_name . " " . $provider->last_name); /* 'Edit Provider' */
			return View::make('edit_provider')
							->with('title', $title)
							->with('page', 'providers')
							->with('success', $success)
							->with('type', $type)
							->with('ps', $provserv)
							->with('provider', $provider)
							->with('providerTypes', $providerTypes)
							->with('prices', $prices)
							->with('bank_account', $bank_account)
							->with('banks', $banks)
							->with('countries', $countries);
		} else {
			// return View::make('notfound')->with('title', 'Error Page Not Found')->with('page', 'Error Page Not Found');
			return View::make('notfound')->with('title', trans('adminController.page_not_found'))->with('page', trans('adminController.page_not_found'));
		}
	}
	
	/**
	 *  Get the updated Provider profile data and save on the Database
	 *
	 *  @return View::admin/providers
	 */
	public function update_provider() {

		if (Input::get('id') != 0) {
			$provider = Provider::find(Input::get('id'));
		} else {

			$findProvider = Provider::where('email', Input::get('email'))->first();

			if ($findProvider) {
				Session::put('new_provider', 0);
				$error_messages = trans('adminController.mail_exist');
				Session::put('msg', $error_messages);
				$title = ucwords(trans('adminController.add') . " " . trans('customize.Provider')); 

				return View::make('add_provider')
								->with('title', $title)
								->with('page', 'providers');
			} else {
				Session::put('new_provider', 1);
				$provider = new Provider;
			}
		}


		$provider_id = $provider->id ;
		$status = Input::get('status');
		$first_name = Input::get('first_name');
		$last_name = Input::get('last_name');
		$email = Input::get('email');
		$phone = Input::get('phone');
		$bio = Input::get('bio');


		$zipcode = preg_replace("/(\D)/", "", Input::get('zipcode'));
		$address = Input::get('address');
		$address_number = Input::get('address_number');
		$address_complements = Input::get('address_complements');
		$address_neighbour = Input::get('address_neighbour');
		$address_city = Input::get('address_city');
		$state = Input::get('state');
		$country = Input::get('country');


		$validator = Validator::make(
			array(
				trans('adminController.first_name') => $first_name,
				trans('adminController.last_name') => $last_name,
				trans('adminController.email') => $email,
				trans('adminController.phone') => $phone,
				trans('adminController.state') => $state,
				trans('adminController.country') => $country
			),
			array(
				trans('adminController.last_name') => 'required',

				trans('adminController.first_name') => 'required',
				trans('adminController.email') => 'required|email|unique:provider,email,'.$provider->id.',id,deleted_at,NULL',
				trans('adminController.phone') => 'required|unique:provider,phone,'.$provider->id.',id,deleted_at,NULL',				
				trans('adminController.state') => 'required',
				trans('adminController.country') => 'required'
			),
			array(
				trans('adminController.last_name') => trans('user_provider_controller.last_name_needed'),
				trans('adminController.first_name') => trans('user_provider_controller.first_name_needed'),
				trans('adminController.email.required') => trans('user_provider_controller.mail_needed'),
				trans('adminController.email.email') => trans('user_provider_controller.mail_invalid'),
				trans('adminController.email.unique') => trans('user_provider_controller.mail_unique'),
				trans('adminController.phone.required') => trans('user_provider_controller.phone_needed'),
				trans('adminController.phone.unique') => trans('user_provider_controller.phone_used'),

				trans('adminController.bio') => '',
				trans('adminController.state') => '',
				trans('adminController.country') => ''		
			)
		);

		if ($validator->fails()) {
			$error_messages = $validator->messages();
			//return $error_messages ;
			Session::put('msg', $error_messages);
			$title = ucwords(trans('adminController.add') . " " . trans('customize.Provider')); /* 'Add Provider' */
			//TODO: Redirect to /admin/provider/edit/{id} with apropriate parameters
			/*
			return View::make('add_provider')
							->with('title', $title)
							->with('page', 'providers');
			*/
			return Redirect::to("/admin/provider/edit/". $provider_id ."?error=1")
								->withErrors($error_messages);
		} 
		else {

			if($provider->status->name != $status){
				$provider->changeStatusByName($status);
			}
			$provider->first_name = $first_name;
			$provider->last_name = $last_name;
			$provider->email = $email;
			$provider->phone = $phone;
			$provider->bio = $bio;
			
			$provider->zipcode = $zipcode;
			$provider->address = $address;
			$provider->address_number = $address_number;
			$provider->address_complements = $address_complements;
			$provider->address_neighbour = $address_neighbour;
			$provider->address_city = $address_city;
			$provider->state = $state;
			$provider->country = $country;
			
			//$provider->is_approved = 1;
			$provider->email_activation = 1;
			if(Input::has('car_number')){
				$car_number = strtoupper(trim(Input::get('car_number')));

				//Inicia a validação da Placa do Carro
				$car_number_db = Settings::where('key', 'car_number_format')->first();

				$car_number_letter = strlen(preg_replace("/.*?([a-zA-Z]*).*?/i", "$1", $car_number_db->value));
				$car_number_number = strlen(preg_replace("/.*?([0-9]*).*?/i", "$1", $car_number_db->value));

				$first_letter = substr($car_number_db->value,0,1);

				if(preg_match('/^[a-zA-Z]{1}$/', $first_letter)){
					if (preg_match('/^[a-zA-Z]{' . $car_number_letter . '}\-?[0-9]{' . $car_number_number . '}$/', $car_number)) {
						$provider->car_number = $car_number;
					} else {
						$error_messages = trans('adminController.invalid_car_number') . $car_number_db->value;
						//return $error_messages ;
						Session::put('msg', $error_messages);
						$title = ucwords(trans('adminController.add') . " " . trans('customize.Provider')); /* 'Add Provider' */
						return Redirect::to("/admin/provider/edit/". $provider_id ."?error=2")
											->withErrors($error_messages);
					}
				} else {
					if (preg_match('/^[0-9]{' . $car_number_number . '}\-?[a-zA-Z]{' . $car_number_letter . '}$/', $car_number)) {
						$provider->car_number = $car_number;
					} else {
						$error_messages = trans('adminController.invalid_car_number') . $car_number_db->value;;
						//return $error_messages ;
						Session::put('msg', $error_messages);
						$title = ucwords(trans('adminController.add') . " " . trans('customize.Provider')); /* 'Add Provider' */
						return Redirect::to("/admin/provider/edit/". $provider_id ."?error=2")
											->withErrors($error_messages);
					}
				}
			}
			$car_brand = trim(Input::get('car_brand'));
			if ($car_brand != "") {
				$provider->car_brand = $car_brand;
			}
			$car_model = trim(Input::get('car_model'));
			if ($car_model != "") {
				$provider->car_model = $car_model;
			}

			if (Input::hasFile('pic')) {

				if ($provider->picture != "") {
					$path = $provider->picture;
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

					$ext = Input::file('pic')->getClientOriginalExtension();

					$filepath = public_path() . "/uploads/" . $file_name . "." . $ext; // or image.jpg

					// Save the image in a defined path
					file_put_contents($filepath, $data);

					$local_url = $file_name . "." . $ext;
				}
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

				if (isset($provider->picture)) {
					if ($provider->picture != "") {
						$icon = $provider->picture;
						unlink_image($icon);
					}
				}

				$provider->picture = $s3_url;
			}

			$provider->save();

			if (Session::get('new_provider') == 1) {
				// send email
				$settings = Settings::where('key', 'email_forgot_password')->first();
				$pattern = $settings->value;
				// $pattern = "Hi, " . Config::get('app.website_title') . " is Created a New Account for you , Your Username is:" . Input::get('email') . " and Your Password is " . $new_password . ". Please dont forget to change the password once you log in next time.";
				// $subject = "Welcome On Board";
				$pattern = trans('adminController.hi') . ", " . Config::get('app.website_title') . trans('adminController.new_account') . " " . Input::get('email') . " " . trans('adminController.password') . " " . $new_password . ". " . trans('adminController.change_password');
				$subject = trans('adminController.wellcome');
				email_notification($provider->id, 'provider', $pattern, $subject); 
			}

			return Redirect::to("/admin/providers");
		}
	}
	
	/**
	 *  Modify the Provider Price Policy spreadsheet
	 *
	 *  @return View::admin/provider/update_policy/{$id}
	 */
	public function update_providerPricePolicy() {

		$provider_id = Input::get('id-provider');
		
		if ($provider_id <= 0) {
			return Redirect::to("/admin/providers");
		}
		
		//Get all checked provider_types and its respective provider_type_categories
		//return Input::get('provider_type') ;
		$provider_types = Input::get('provider_type');

		$validator = Validator::make(
			array(				
				trans('adminController.provider_types') => $provider_types,
			),
			array(				
				trans('adminController.provider_types') => 'required',
			),
			array(				
				trans('adminController.provider_types') => trans('user_provider_controller.provider_types_required'),		
			)
		);

		if ($validator->fails()) {
			$error_messages = $validator->messages()->first();
			Session::put('msg', $error_messages);
			return Redirect::to("/admin/provider/edit/".$provider_id);
		}

		$idsProviderServices = array();
		
		foreach ($provider_types as $type_id => $provider_type) {
			// salvara se o tipo de servico estiver selecionado
			if(isset($provider_type['selected'])){
				$countCategories = 0 ;
				$providerType = ProviderType::find($type_id);
				//return $provider_type['categories'] ;
				
				if(array_key_exists('categories', $provider_type)) {
					foreach ($provider_type['categories'] as $category_id => $category) {
						//return $category ;
						// salvara se a categoria estiver selecionado
						if(isset($category['selected'])){

							$providerService = ProviderServices::findByProviderIdAndTypeIdAndCategoryId($provider_id, $type_id, $category_id);

							if(!$providerService){
								$providerService = new ProviderServices;
								$providerService->provider_id 				= $provider_id ; 
								$providerService->type 						= $providerType->id; 
								$providerService->category					= $category_id ; 
								$providerService->price_per_unit_distance 	= $providerType->price_per_unit_distance; 
								$providerService->price_per_unit_time 		= $providerType->price_per_unit_time; 
								$providerService->base_price 				= $providerType->base_price ; 
								$providerService->base_distance 			= $providerType->base_distance ; 
								$providerService->base_time 				= $providerType->base_time ; 
								$providerService->distance_unit 			= $providerType->base_price ; 
								$providerService->time_unit 				= $providerType->time_unit ; 
								$providerService->base_price_provider 		= $providerType->base_price_provider ; 
								$providerService->base_price_user 			= $providerType->base_price_user ; 
								$providerService->commission_rate 			= $providerType->commission_rate ; 
								$providerService->is_visible				= $providerType->is_visible ;
							}

							$providerService->base_price_provider 		= round($category['base_price_provider'], 2) ; 
							$providerService->base_price_user 			= round($category['base_price_user'], 2) ; 
							$providerService->price_per_unit_distance	= round($category['price_per_unit_distance'], 2) ;

							$providerService->save();

							array_push($idsProviderServices, $providerService->id);
							$countCategories++ ;
						}

					}	
				}
				
				// faz uma simples associacao somente dos tipos principais
				if($countCategories == 0) {

					$providerService = ProviderServices::findByProviderIdAndTypeId($provider_id, $type_id);

					if(!$providerService){
						$providerService = new ProviderServices;
						$providerService->provider_id 				= $provider_id ; 
						$providerService->type 						= $providerType->id; 
						$providerService->category					= 0 ; 
						$providerService->price_per_unit_distance 	= $providerType->price_per_unit_distance; 
						$providerService->price_per_unit_time 		= $providerType->price_per_unit_time; 
						$providerService->base_price 				= $providerType->base_price ; 
						$providerService->base_distance 			= $providerType->base_distance ; 
						$providerService->base_time 				= $providerType->base_time ; 
						$providerService->distance_unit 			= $providerType->base_price ; 
						$providerService->time_unit 				= $providerType->time_unit ; 
						$providerService->base_price_provider 		= $providerType->base_price_provider ; 
						$providerService->base_price_user 			= $providerType->base_price_user ; 
						$providerService->commission_rate 			= $providerType->commission_rate ; 
						$providerService->is_visible				= $providerType->is_visible ;
					}

					$providerService->save();

					array_push($idsProviderServices, $providerService->id);
				}
			}
			
		}

		// remove servicos foram desselecionados
		if(count($idsProviderServices)){
			ProviderServices::where('provider_id', '=', $provider_id)
							->whereNotIn('id', $idsProviderServices)
							->delete();
		}
		
		
		
		return Redirect::to("/admin/provider/edit/".$provider_id);
	}

	/**
	 *  Modify the Provider attendance history
	 *
	 *  @return View::admin/provider/edit/{$id}
	 */
	public function update_providerHistory() {
		$provider_id = Input::get('id-provider-history');
		$attendance_history = Input::get('attendance-notes');

		$provider = Provider::findOrFail($provider_id);
		$provider->attendance_history = $attendance_history;
		$provider->save();

		return Redirect::to("/admin/provider/edit/".$provider_id);
	}

	/**
	 *  Modify the Provider bank account
	 *
	 *  @return View::admin/provider/edit/{$id}
	 */
	public function update_provider_bank_account() {
		$provider_id = Input::get('id-provider-bank-account');
		$bank_account_info = Input::get('bank-account');

		//conta bancária
		$holder = Input::get('holder');
		$document = Input::get('document');
		$bank_id = Input::get('bank_id'); 
		$agency = Input::get('agency');
		$account = Input::get('account');
		$account_digit = Input::get('account_digit');
		$option_document = Input::get('option_document');

		$validator = Validator::make(
			array(				
				trans('adminController.holder') => $holder,
				trans('adminController.document') => $document,
				trans('adminController.bank_id') => $bank_id,
				trans('adminController.agency') => $agency,
				trans('adminController.account') => $account,
				trans('adminController.account_digit') => $account_digit,
				trans('adminController.option_document') => $option_document,
			),
			array(				
				trans('adminController.holder') => 'required',
				trans('adminController.document') => 'required',
				trans('adminController.bank_id') => 'required',
				trans('adminController.agency') => 'required',
				trans('adminController.account') => 'required',
				trans('adminController.account_digit') => 'required',
				trans('adminController.option_document') => 'required'
			),
			array(				
				trans('adminController.holder') => trans('user_provider_controller.holder_required'),
				trans('adminController.document') => trans('user_provider_controller.document_required'),
				trans('adminController.bank_id') => trans('user_provider_controller.bank_required'),
				trans('adminController.agency') => trans('user_provider_controller.agency_required'),
				trans('adminController.account') => trans('user_provider_controller.account_required'),
				trans('adminController.account_digit') => trans('user_provider_controller.account_digit_required'),			
				trans('adminController.option_document') => trans('user_provider_controller.option_document_required'),		
			)
		);

		if($option_document == ProviderBankAccount::INDIVIDUAL){
			$validatorDocument = Validator::make(
							array(
								'cpf' => $document,
							), 
							array(
								'cpf' => 'cpf'
							), 
							array(
								'cpf' => trans('providerController.cpf_invalid')
							)
			);
		}

		else{
			$validatorDocument = Validator::make(
							array(
								'cnpj' => $document,
							), 
							array(
								'cnpj' => 'cnpj'
							), 
							array(
								'cnpj' => trans('providerController.cnpj_invalid')
							)
			);

		}

		if ($validator->fails()) {
			$error_messages = $validator->messages();
			return Redirect::to("/admin/provider/edit/". $provider_id ."?error=document_error")
							->withErrors($error_messages);
		}
		else if($validatorDocument->fails()){
			$error_messages = $validatorDocument->messages();
			return Redirect::to("/admin/provider/edit/". $provider_id ."?error=document_error")
							->withErrors($error_messages);
		}
		else {

			// salvar informações da conta bancária
			$bank = Bank::where('id', $bank_id)->first();
			$provider_bank_account = ProviderBankAccount::where('provider_id', $provider_id)->first();
			if(!$provider_bank_account){
				$provider_bank_account = new ProviderBankAccount();
			}

			//atualizar informações no Pagar.me
			if(Config::get('app.default_payment') == 'pagarme'){
				$payment_token = Input::get('card_hash');

				try {

				    PagarMe::setApiKey(Config::get('app.pagarme_api_key'));

	    			$settingTransferInterval = Settings::where('key', 'provider_transfer_interval')->first();
					$settingTransferDay = Settings::where('key', 'provider_transfer_day')->first();

				    $recipient = new PagarMe_Recipient(array(
				        "transfer_interval" => $settingTransferInterval? $settingTransferInterval->value : "daily", 
				        "transfer_day" => $settingTransferDay? $settingTransferDay->value : "5", 
				        "transfer_enabled" => true, //recebe pagamento automaticamente

				        "bank_account" => array(
					        "bank_code" => $bank->code,
					        "agencia" => $agency,
					        "conta" => $account,
					        "conta_dv" => $account_digit,
					        "document_number" => $document,
					        "legal_name" => $holder
			        	)
				    ));
				    $recipient->create();

			    	//atualizar informacoes no banco de dados 
			    	if($recipient->id){ 
						$provider_bank_account->provider_id = $provider_id;
						$provider_bank_account->holder = $holder;
						$provider_bank_account->document = $document;
						$provider_bank_account->bank_id = $bank_id;
						$provider_bank_account->agency = $agency;
						$provider_bank_account->account = $account;
						$provider_bank_account->account_digit = $account_digit;
						$provider_bank_account->recipient_id = $recipient->id;
						$provider_bank_account->person_type = $option_document;
						$provider_bank_account->save();
					}

					return Redirect::to("/admin/provider/edit/".$provider_id);
				}
				catch (Exception $ex){
					return Redirect::to("/admin/provider/edit/". $provider_id ."?error=api_pagarme_error")
								->withErrors([$ex->getMessage()]);
				}
	    	}
	    	else {
	    		return Redirect::to("/admin/provider/edit/". $provider_id ."?error=api_pagarme_not_configured");
	    	}
			
	    }
	}

	public function updateProviderPassword() {
		
		$new_password = Input::get('new_password');
		$confirm_password = Input::get('confirm_password');

		$provider_id = Input::get('provider_id');
		$provider = Provider::find($provider_id);

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
			 return Redirect::to("/admin/provider/edit/". $provider_id)->withErrors(['Senha deve possuir mais que 6 dígitos.']);

		}
		elseif ($new_password === $confirm_password) {

			$password = Hash::make($new_password);
			$provider->password = $password;
			$provider->save();

			$message = trans('user_provider_controller.password_updated');
			$type = "success";
		 	return Redirect::to("/admin/provider/edit/". $provider_id)->with($type, $message);

		} else {
			$message = trans('user_provider_controller.password_dont_match');
			$type = "errors";
		   return Redirect::to("/admin/provider/edit/". $provider_id)->with($type, $message);
		}
		
	}

	public function provider_availabilty() {
		$id = Request::segment(5);
		$type = ProviderType::where('is_visible', '=', 1)->get();
		$provserv = ProviderServices::where('provider_id', $id)->get();
		$success = Input::get('success');
		$provider = Provider::find($id);
		$title = ucwords(trans('adminController.edit') ." " . trans('customize.Provider') . ": " . trans('adminController.availability2')); /* 'Edit Provider Availability' */
		return View::make('edit_provider_availability')
						->with('title', $title)
						->with('page', 'providers')
						->with('success', $success)
						->with('type', $type)
						->with('ps', $provserv)
						->with('provider', $provider);
	}

	/**
	 *  Setup and call Price Policy View
	 *
	 *  @return View::admin_price_policy
	 */
	public function adminPricePolicy() {
		// Standard provider id for general purporses
		$id = Request::segment(3);
		
		$prices = ProviderServices::where('provider_id', '=', $id)->get();
		
		// Create spreadsheet with visible services only
		$filteredPrices = $prices->filter(function ($item) {
			return ($item && $item->getType && $item->getTypeCategory && ($item->getType->is_visible == true));
		});
		
		$filteredPrices->all();
		
		return View::make('admin_price_policy')
			->with('title', trans('user_provider_controller.price_policy'))
			->with('page', 'price-policy')
			->with('id', $id)
			->with('prices', $filteredPrices);
	}
	
	/**
	 *  Modify the general Price Policy spreadsheet
	 *
	 *  @return View::admin/price-policy/{$id}
	 */
	public function update_pricePolicy() {
		// Standard provider id for general purporses is 0
		$id = Input::get('id');
		
		$prices = ProviderServices::where('provider_id', $id)->get();
		
		foreach ($prices as $price) {
		
			$base_price_provider = trim(Input::get('base_price_provider-'.$price->id));
			if ($base_price_provider == "" || $base_price_provider < 0 || !is_numeric($base_price_provider)) {
				$base_price_provider = 0;
			}
			$base_price_user = trim(Input::get('base_price_user-'.$price->id));
			if ($base_price_user == "" || $base_price_user < 0 || !is_numeric($base_price_user)) {
				$base_price_user = 0;
			}
			$price_per_unit_distance = trim(Input::get('price_per_unit_distance-'.$price->id));
			if ($price_per_unit_distance == "" || $price_per_unit_distance < 0 || !is_numeric($price_per_unit_distance)) {
				$price_per_unit_distance = 0;
			}
			
			$price->base_price_provider = $base_price_provider;
			$price->base_price_user = $base_price_user;
			$price->price_per_unit_distance = $price_per_unit_distance;
			$price->save();
		}
		
		return Redirect::to("/admin/price-policy/$id");
	}

	public function add_provider() {
		$title = ucwords(trans('adminController.add') . " " . trans('customize.Provider')); /* 'Add Provider' */
		return View::make('add_provider')
						->with('title', $title)
						->with('page', 'providers');
	}



	public function deactivate_promo_code() {
		$id = Request::segment(4);
		$promo_code = PromoCodes::where('id', $id)->first();
		$promo_code->state = 2;
		$promo_code->save();
		return Redirect::route('AdminPromoCodes');
	}

	public function activate_promo_code() {
		$id = Request::segment(4);
		$promo_code = PromoCodes::where('id', $id)->first();
		$promo_code->state = 1;
		$promo_code->save();
		return Redirect::route('AdminPromoCodes');
	}

	public function edit_promo_code() {
		$id = Request::segment(4);
		$promo_code = PromoCodes::where('id', $id)->first();
		$title = ucwords(trans('adminController.edit') ." " . trans('customize.promo_codes')); /* 'Edit Promo Code' */
		return View::make('edit_promo_code')
						->with('id', $id)
						->with('title', $title)
						->with('page', 'promo_code')
						->with('promo_code', $promo_code);
	}

	public function update_promo_code() {
		$check = PromoCodes::where('coupon_code', '=', Input::get('code_name'))->where('id', '!=', Input::get('id'))->count();
		if ($check > 0) {
			return Redirect::to("admin/promo_code?success=1");
		}
		if (Input::get('id') != 0) {
			$promo = PromoCodes::find(Input::get('id'));
		} else {
			$promo = new PromoCodes;
		}

		$code_name = Input::get('code_name');
		$code_type = Input::get('code_type');
		$code_uses = Input::get('code_uses');
		$code_value = Input::get('code_value');
		
		$var = Input::get('start_date');
		$date = str_replace('/', '-', $var);
		$start_date =  date('Y-m-d H:i:s', strtotime($date));

		$var2 = Input::get('code_expiry');
		$date2 = str_replace('/', '-', $var2);
		$expiry_code =  date('m/d/Y', strtotime($date2));

		$start_date = date("Y-m-d H:i:s", strtotime($start_date));
		$code_expiry = date("Y-m-d H:i:s", strtotime($expiry_code) + ((((23 * 60) + 59) * 60) + 59));

		$code_value = preg_replace( '/[^0-9\.,]/', '', $code_value);
		$code_value = str_replace(',', '.', $code_value);

		$validator = Validator::make(
						array(
					trans('adminController.code_name') => $code_name,
					trans('adminController.code_value') => $code_value,
					trans('adminController.code_type') => $code_type,
					trans('adminController.code_uses') => $code_uses,
					trans('adminController.code_expiry') => $code_expiry,
					trans('adminController.start_date') => $start_date,
						), array(
					trans('adminController.code_name') => 'required',
					trans('adminController.code_value') => 'required',
					trans('adminController.code_type') => 'required|integer',
					trans('adminController.code_uses') => 'required|integer',
					trans('adminController.code_expiry') => 'required',
					trans('adminController.start_date') => 'required',
						), array(
					trans('adminController.code_name') => trans('adminController.code_name'),
					trans('adminController.code_value') => trans('adminController.code_value'),
					trans('adminController.code_type') => trans('adminController.code_type'),
					trans('adminController.code_uses') => trans('adminController.code_uses'),
					trans('adminController.code_expiry') => trans('adminController.code_expire'),
					trans('adminController.start_date') => trans('adminController.start_date'),
						)
		);

		

		if ($validator->fails()) {
			$error_messages = $validator->messages()->first();
			Session::put('msg', $error_messages);
			$title = ucwords(trans('adminController.add') . " " . trans('customize.promo_codes')); /* 'Add Promo Code' */
			return View::make('add_promo_code')
							->with('title', $title)
							->with('page', 'promo_codes');


		} else {
			$expirydate = date("Y-m-d H:i:s", strtotime($code_expiry));

			$promo->coupon_code = $code_name;
			$promo->value = $code_value;
			$promo->type = $code_type;
			$promo->uses = $code_uses;
			$promo->start_date = $start_date;
			$promo->expiry = $expirydate;
			$promo->state = 1;
			$promo->save();
		}
		return Redirect::route('AdminPromoCodes');
	}

	public function change_provider_status(){
		$action = Request::segment(5);
		$id = Request::segment(6);
		$provider = Provider::find($id);

		if($provider){
			$provider->changeStatus($action);
		}

		return Redirect::to("/admin/providers");
	}
	

	public function delete_provider() {
		$id = Request::segment(4);
		$success = Input::get('success');
		RequestMeta::where('provider_id', $id)->delete();
		Provider::where('id', $id)->delete();
		return Redirect::to("/admin/providers");
	}

	public function delete_user() {
		$id = Request::segment(4);
		$success = Input::get('success');
		User::where('id', $id)->delete();
		return Redirect::to("/admin/users");
	}

	public function provider_history() {
		$provider_id = Request::segment(4);
		$requests = DB::table('request')
				->where('request.confirmed_provider', $provider_id)
				->where('request.is_completed', 1)
				->leftJoin('provider', 'request.confirmed_provider', '=', 'provider.id')
				->leftJoin('user', 'request.user_id', '=', 'user.id')
				->select('user.first_name as user_first_name', 'user.last_name as user_last_name', 'provider.first_name as provider_first_name', 'provider.last_name as provider_last_name', 'user.id as user_id', 'provider.id as provider_id', 'request.id as id', 'request.created_at as date', 'request.is_started', 'request.is_provider_arrived', 'request.is_completed', 'request.is_paid', 'request.is_provider_started', 'request.confirmed_provider', 'request.status', 'request.time', 'request.distance', 'request.total', 'request.is_cancelled', 'request.payment_mode')
				->orderBy('request.created_at')
				->paginate(10);
		// $title = ucwords(trans('customize.Provider') . " History"); /* 'Trip History' */
		$title = ucwords(trans('adminController.history_provider')); /* 'Trip History' */
		foreach ($requests as $request) {
			$title = ucwords(trans('adminController.history_provider') . " : " . $request->provider_first_name . " " . $request->provider_last_name);
		}
		$setting = Settings::where('key', 'transfer')->first();

		return View::make('requests')
						->with('title', $title)
						->with('page', 'providers')
						->with('setting', $setting)
						->with('requests', $requests);
	}

	public function providerDocuments($provider_id) {
		$documents = Document::all();

		$provider_document = ProviderDocument::where('provider_id', $provider_id)->get();

		$provider = Provider::find($provider_id);

		$status = -1;

		if(count($provider_document) > 0){
			$status = 0;
		}

		if ($provider->is_approved) {
			$status = 1;
		}

		return View::make('provider_document_list')
					->with('title', trans('adminController.driver_doc'))
					->with('page', 'providers')
					->with('provider', $provider)
					->with('documents', $documents)
					->with('status', $status)
					->with('provider_document', $provider_document);
	}

	public function providerUpdateDocuments() {
		$inputs = Input::all();
		$provider_id = Input::get('provider_id');


		foreach ($inputs as $key => $input) {
			//dd(gettype($input));
			if(gettype($input) != "string" && $input){
				//dd(gettype($input));
				$provider_document = ProviderDocument::where('provider_id', $provider_id)->where('document_id', $key)->first();
				if (!$provider_document) {
					$provider_document = new ProviderDocument;
				}
				$provider_document->provider_id = $provider_id;
				$provider_document->document_id = $key;

			
				$file_name = time();
				$file_name .= rand();
				$file_name = sha1($file_name);

				$ext = $input->getClientOriginalExtension();
				$input->move(public_path() . "/uploads", $file_name . "." . $ext);
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


				// send email

				$get = Provider::where('id', '=', $provider_id)->first();
				$pattern = trans('user_provider_controller.hi') . " " . $get->first_name . ", ID " . $provider_id . trans('user_provider_controller.doc_wait');
				$subject = trans('user_provider_controller.wait_approval');
				/* email_notification('', 'admin', $pattern, $subject); */

				if (isset($provider_document->url)) {
					if ($provider_document->url != "") {
						$icon = $provider_document->url;
						unlink_image($icon);
					}
				}

				$provider_document->url = $s3_url;
				$provider_document->save();
				
			}
		}

		$message = trans('user_provider_controller.doc_updated');
		$type = "success";
		return Redirect::to('/admin/providers')->with('flash_message', $message)->with('flash_type', $type);
	}

	public function provider_upcoming_requests() {
		$provider_id = Request::segment(4);
		$requests = DB::table('request')
				->where('request.provider_id', $provider_id)
				->where('request.is_completed', 0)
				->leftJoin('provider', 'request.confirmed_provider', '=', 'provider.id')
				->leftJoin('user', 'request.user_id', '=', 'user.id')
				->select('user.first_name as user_first_name', 'user.last_name as user_last_name', 'provider.first_name as provider_first_name', 'provider.last_name as provider_last_name', 'user.id as user_id', 'provider.id as provider_id', 'request.id as id', 'request.created_at as date', 'request.is_started', 'request.is_provider_arrived', 'request.is_completed', 'request.is_paid', 'request.is_provider_started', 'request.confirmed_provider', 'request.status', 'request.time', 'request.distance', 'request.total')
				->orderBy('request.created_at')
				->paginate(10);
		$title = ucwords(trans('customize.Provider') . " ".trans('adminController.next')." " . trans('customize.Request'));
		foreach ($requests as $request) {
		$title = ucwords(trans('customize.Provider') . " ".trans('adminController.next')." " . trans('customize.Request') . ": " . $request->provider_first_name . " " . $request->provider_last_name);
		}
		return View::make('requests')
						->with('title', $title)
						->with('page', 'providers')
						->with('requests', $requests);
	}

	public function edit_user() {
		$id = Request::segment(4);
		$success = Input::get('success');
		$user = User::find($id);
		if ($user) {
			$title = ucwords(trans('adminController.edit') ." " . trans('customize.User') . ": " . $user->first_name . " " . $user->last_name); /* 'Edit User' */
			return View::make('edit_user')
							->with('title', $title)
							->with('page', 'users')
							->with('success', $success)
							->with('user', $user);
		} else {
			return View::make('notfound')
							->with('title', trans('adminController.page_not_found'))
							->with('page', trans('adminController.page_not_found'));
		}
	}

	public function update_user() {
		$user = User::find(Input::get('id'));
		$user->first_name = Input::get('first_name');
		$user->last_name = Input::get('last_name');
		$user->email = Input::get('email');
		$user->phone = Input::get('phone');
		$user->address = Input::get('address');
		$user->state = Input::get('state');
		$user->zipcode = Input::get('zipcode');
		$user->save();
		return Redirect::to("/admin/users/");
	}

	public function user_history() {
		$setting = Settings::where('key', 'transfer')->first();
		$user_id = Request::segment(4);
		$user = User::find($user_id);
		$requests = DB::table('request')
				->where('request.user_id', $user->id)
				->where('request.is_completed', 1)
				->leftJoin('provider', 'request.confirmed_provider', '=', 'provider.id')
				->leftJoin('user', 'request.user_id', '=', 'user.id')
				->select('user.first_name as user_first_name', 'user.last_name as user_last_name', 'provider.first_name as provider_first_name', 'provider.last_name as provider_last_name', 'user.id as user_id', 'provider.id as provider_id', 'request.id as id', 'request.created_at as date', 'request.is_started', 'request.is_provider_arrived', 'request.is_completed', 'request.is_paid', 'request.is_provider_started', 'request.confirmed_provider', 'request.status', 'request.time', 'request.distance', 'request.total', 'request.is_cancelled', 'request.payment_mode')
				->orderBy('request.created_at')
				->paginate(10);
		$title = ucwords(trans('adminController.history_provider')); /* 'Trip History' */
		foreach ($requests as $request) {
			$title = ucwords(trans('adminController.history_user') . " : " . $request->user_first_name . " " . $request->user_last_name);
		}

		return View::make('requests')
						->with('title', $title)
						->with('page', 'users')
						->with('setting', $setting)
						->with('requests', $requests);
	}

	public function user_upcoming_requests() {
		$user_id = Request::segment(4);
		$user = User::find($user_id);
		$requests = DB::table('request')
				->where('request.user_id', $user->id)
				->where('request.is_completed', 0)
				->leftJoin('provider', 'request.confirmed_provider', '=', 'provider.id')
				->leftJoin('user', 'request.user_id', '=', 'user.id')
				->select('user.first_name as user_first_name', 'user.last_name as user_last_name', 'provider.first_name as provider_first_name', 'provider.last_name as provider_last_name', 'user.id as user_id', 'provider.id as provider_id', 'request.id as id', 'request.created_at as date', 'request.is_started', 'request.is_provider_arrived', 'request.is_completed', 'request.is_paid', 'request.is_provider_started', 'request.confirmed_provider', 'request.status', 'request.time', 'request.distance', 'request.total')
				->orderBy('request.created_at')
				->paginate(10);
		$title = ucwords(trans('customize.User') . " ".trans('adminController.next')." " . trans('customize.Request'));
		foreach ($requests as $request) {
			$title = ucwords(trans('customize.User') . " ".trans('adminController.next')." " . trans('customize.Request') . ": " . $request->user_first_name . " " . $request->user_last_name);
		}
		return View::make('requests')
						->with('title', $title)
						->with('page', 'users')
						->with('requests', $requests);
	}

	public function delete_review(){
		$id = Request::segment(4);
		$provider = ProviderReview::where('id', $id)->delete();
		return Redirect::to("/admin/reviews");
	}

	public function delete_review_user() {
		$id = Request::segment(4);
		$provider = UserReview::where('id', $id)->delete();
		return Redirect::to("/admin/reviews");
	}

	public function approve_request() {
		$id = Request::segment(4);
		$request = Request::find($id);
		$request->is_confirmed = 1;
		$request->save();
		return Redirect::to("/admin/requests");
	}

	public function decline_request() {
		$id = Request::segment(4);
		$request = Request::find($id);
		$request->is_confirmed = 0;
		$request->save();
		return Redirect::to("/admin/requests");
	}

	public function view_map() {
		$id = Request::segment(4);
		$request = Requests::find($id);
		$user = User::where('id', $request->user_id)->first();
		$provider = Provider::where('id', $request->confirmed_provider)->first();

		if ($request->is_paid) {
			$status = trans('adminController.done_pay');
		} elseif ($request->is_completed) {
			$status = trans('adminController.complete_request');
		} elseif ($request->is_started) {
			$status = trans('adminController.started_request');
		} elseif ($request->is_provider_started) {
			$status = "" . Config::get('app.generic_keywords.Provider') . " ".trans('adminController.started');
		} elseif ($request->confirmed_provider) {
			$status = "" . Config::get('app.generic_keywords.Provider') . " ".trans('adminController.yet_started');
		} else {
			$status = "" . Config::get('app.generic_keywords.Provider') . " ".trans('adminController.not_confirmed');
		}
		if ($request->is_cancelled == 1) {
			$status1 = "<span class='badge bg-red'>".trans('adminController.cancel')."</span>";
		} elseif ($request->is_completed == 1) {
			$status1 = "<span class='badge bg-green'>".trans('adminController.completed')."</span>";
		} elseif ($request->is_started == 1) {
			$status1 = "<span class='badge bg-yellow'>".trans('adminController.started')."</span>";
		} elseif ($request->is_provider_arrived == 1) {
			$status1 = "<span class='badge bg-yellow'>" . Config::get('app.generic_keywords.Provider') . " ".trans('adminController.arrived')."</span>";
		} elseif ($request->is_provider_started == 1) {
			$status1 = "<span class='badge bg-yellow'>" . Config::get('app.generic_keywords.Provider') . " ".trans('adminController.started')."</span>";
		} else {
			$status1 = "<span class='badge bg-light-blue'>".trans('adminController.yet_start')."</span>";
		}
		if ($request->payment_mode == 0) {
			$pay_mode = "<span class='badge bg-orange'>".trans('adminController.stored_card')."</span>";
		} elseif ($request->payment_mode == 1) {
			$pay_mode = "<span class='badge bg-blue'>".trans('adminController.cash_pay')."</span>";
		} elseif ($request->payment_mode == 2) {
			$pay_mode = "<span class='badge bg-purple'>".trans('adminController.paypal')."</span>";
		}
		if ($request->is_paid == 1) {
			$pay_status = "<span class='badge bg-green'>".trans('adminController.completed')."</span>";
		} elseif ($request->is_paid == 0 && $request->is_completed == 1) {
			$pay_status = "<span class='badge bg-red'>".trans('adminController.pending')."</span>";
		} else {
			$pay_status = "<span class='badge bg-yellow'>".trans('adminController.request_not_complete')."</span>";
		}


		if ($request->is_completed) {
			$full_request = RequestLocation::where('request_id', '=', $id)->orderBy('created_at')->get();
			$request_location_start = RequestLocation::where('request_id', $id)->orderBy('created_at')->first();
			$request_location_end = RequestLocation::where('request_id', $id)->orderBy('created_at', 'desc')->first();
			$user_latitude = $request_location_start->latitude;
			$user_longitude = $request_location_start->longitude;
			$provider_latitude = $request_location_end->latitude;
			$provider_longitude = $request_location_end->longitude;
		} else {
			$full_request = RequestLocation::where('request_id', '=', $id)->orderBy('created_at')->get();
			/* $full_request = array(); */
			if ($request->confirmed_provider) {
				$provider_latitude = $provider->latitude;
				$provider_longitude = $provider->longitude;
			} else {
				$provider_latitude = 0;
				$provider_longitude = 0;
			}
			/* $user_latitude = $user->latitude;
			  $user_longitude = $user->longitude; */
			$user_latitude = $request->latitude;
			$user_longitude = $request->longitude;
		}

		$request_meta = DB::table('request_meta')
				->where('request_id', $id)
				->leftJoin('provider', 'request_meta.provider_id', '=', 'provider.id')
				->paginate(10);

		if ($provider) {
			$provider_name = $provider->first_name . " " . $provider->last_name;
			$provider_phone = $provider->phone;
		} else {
			$provider_name = "";
			$provider_phone = "";
		}

		if ($request->confirmed_provider) {
			$title = ucwords(trans('customize.map_view'));
			return View::make('request_map')
							->with('title', $title)
							->with('page', 'requests')
							->with('request_id', $id)
							->with('is_started', $request->is_started)
							->with('time', $request->time)
							->with('start_time', $request->request_start_time)
							->with('amount', $request->total)
							->with('user_name', $user->first_name . " " . $user->last_name)
							->with('provider_name', $provider_name)
							->with('provider_latitude', $provider_latitude)
							->with('provider_longitude', $provider_longitude)
							->with('user_latitude', $user_latitude)
							->with('user_longitude', $user_longitude)
							->with('provider_phone', $provider_phone)
							->with('user_phone', $user->phone)
							->with('status', $status)
							->with('status1', $status1)
							->with('pay_mode', $pay_mode)
							->with('pay_status', $pay_status)
							->with('full_request', $full_request)
							->with('request_meta', $request_meta);
		} else {
			$title = ucwords(trans('customize.map_view'));
			return View::make('request_map')
							->with('title', $title)
							->with('page', 'requests')
							->with('request_id', $id)
							->with('is_started', $request->is_started)
							->with('time', $request->time)
							->with('start_time', $request->request_start_time)
							->with('amount', $request->total)
							->with('user_name', $user->first_name . " ", $user->last_name)
							->with('provider_name', "")
							->with('provider_latitude', $provider_latitude)
							->with('provider_longitude', $provider_longitude)
							->with('user_latitude', $user_latitude)
							->with('user_longitude', $user_longitude)
							->with('provider_phone', "")
							->with('user_phone', $user->phone)
							->with('request_meta', $request_meta)
							->with('full_request', $full_request)
							->with('status1', $status1)
							->with('pay_mode', $pay_mode)
							->with('pay_status', $pay_status)
							->with('status', $status);
		}
	}

	public function change_provider() {
		$id = Request::segment(4);
		$title = ucwords(trans('customize.map_view'));
		return View::make('reassign_provider')
						->with('title', $title)
						->with('page', 'requests')
						->with('request_id', $id);
	}

	public function alternative_providers_xml() {
		$id = Request::segment(4);
		$request = Request::find($id);
		$schedule = Schedules::find($request->schedule_id);
		$user = User::find($request->user_id);
		$current_provider = Provider::find($request->provider_id);
		$latitude = $user->latitude;
		$longitude = $user->longitude;
		$distance = 5;

		// Get Latitude
		$schedule_meta = ScheduleMeta::where('schedule_id', '=', $schedule->id)
				->orderBy('started_on', 'DESC')
				->get();

		$flag = 0;
		$date = "0000-00-00";
		$days = array();
		foreach ($schedule_meta as $meta) {
			if ($flag == 0) {
				$date = $meta->started_on;
				$flag++;
			}
			array_push($days, $meta->day);
		}

		$start_time = date('H:i:s', strtotime($schedule->start_time) - (60 * 60));
		$end_time = date('H:i:s', strtotime($schedule->end_time) + (60 * 60));
		$days_str = implode(',', $days);
		$settings = Settings::where('key', 'default_distance_unit')->first();
		$unit = $settings->value;
		if ($unit == 0) {
			$multiply = 1.609344;
		} elseif ($unit == 1) {
			$multiply = 1;
		}

		$query = "SELECT provider.id,provider.bio,provider.first_name,provider.last_name,provider.phone,provider.latitude,provider.longitude from provider where id NOT IN ( SELECT distinct schedules.provider_id FROM `schedule_meta` left join schedules on schedule_meta.schedule_id = schedules.id where schedules.is_confirmed     != 0 and schedule_meta.day IN ($days_str) and schedule_meta.ends_on >= '$date' and schedule_meta.started_on <= '$date' and ((schedules.start_time > '$start_time' and schedules.start_time < '$end_time') OR ( schedules.end_time > '$start_time' and schedules.end_time < '$end_time' )) ) and "
				. "ROUND((" . $multiply . " * 3956 * acos( cos( radians('$latitude') ) * "
				. "cos( radians(latitude) ) * "
				. "cos( radians(longitude) - radians('$longitude') ) + "
				. "sin( radians('$latitude') ) * "
				. "sin( radians(latitude) ) ) ) ,8) <= $distance ";

		$providers = DB::select(DB::raw($query));
		$response = "";
		$response .= '<markers>';

		foreach ($providers as $provider) {
			$response .= '<marker ';
			$response .= 'name="' . $provider->first_name . " " . $provider->last_name . '" ';
			$response .= 'client_name="' . $provider->first_name . " " . $provider->last_name . '" ';
			$response .= 'contact="' . $provider->phone . '" ';
			$response .= 'amount="' . 0 . '" ';
			$response .= 'lat="' . $provider->latitude . '" ';
			$response .= 'lng="' . $provider->longitude . '" ';
			$response .= 'id="' . $provider->id . '" ';
			$response .= 'type="client" ';
			$response .= '/>';
		}

		// Add Current provider
		if ($current_provider) {
			$response .= '<marker ';
			$response .= 'name="' . $current_provider->first_name . " " . $current_provider->last_name . '" ';
			$response .= 'client_name="' . $current_provider->first_name . " " . $current_provider->last_name . '" ';
			$response .= 'contact="' . $current_provider->phone . '" ';
			$response .= 'amount="' . 0 . '" ';
			$response .= 'lat="' . $current_provider->latitude . '" ';
			$response .= 'lng="' . $current_provider->longitude . '" ';
			$response .= 'id="' . $current_provider->id . '" ';
			$response .= 'type="driver" ';
			$response .= '/>';
		}

		// Add User
		$response .= '<marker ';
		$response .= 'name="' . $user->first_name . " " . $user->last_name . '" ';
		$response .= 'client_name="' . $user->first_name . " " . $user->last_name . '" ';
		$response .= 'contact="' . $user->phone . '" ';
		$response .= 'amount="' . 0 . '" ';
		$response .= 'lat="' . $user->latitude . '" ';
		$response .= 'lng="' . $user->longitude . '" ';
		$response .= 'id="' . $user->id . '" ';
		$response .= 'type="client_pay_done" ';
		$response .= '/>';

		// Add Busy Providers

		$providers = DB::table('request')
				->where('request.is_started', 1)
				->where('request.is_completed', 0)
				->join('provider', 'request.provider_id', '=', 'provider.id')
				->select('provider.id', 'provider.phone', 'provider.first_name', 'provider.last_name', 'provider.latitude', 'provider.longitude')
				->distinct()
				->get();


		foreach ($providers as $provider) {
			$response .= '<marker ';
			$response .= 'name="' . $provider->first_name . " " . $provider->last_name . '" ';
			$response .= 'client_name="' . $provider->first_name . " " . $provider->last_name . '" ';
			$response .= 'contact="' . $provider->phone . '" ';
			$response .= 'amount="' . 0 . '" ';
			$response .= 'lat="' . $provider->latitude . '" ';
			$response .= 'lng="' . $provider->longitude . '" ';
			$response .= 'id="' . $user->id . '" ';
			$response .= 'type="client_no_pay" ';
			$response .= '/>';
		}


		$response .= '</markers>';

		$content = View::make('providers_xml')->with('response', $response);
		return Response::make($content, '200')->header('Content-Type', 'text/xml');
	}

	public function save_changed_provider() {
		$request_id = Input::get('request_id');
		$type = Input::get('type');
		$provider_id = Input::get('provider_id');
		$request = Request::find($request_id);
		if ($type == 1) {
			$request->provider_id = $provider_id;
			$request->save();
		} else {
			Request::where('schedule_id', $request->schedule_id)->where('is_started', 0)->update(array('provider_id' => $provider_id));
			Schedules::where('id', $request->schedule_id)->update(array('provider_id' => $provider_id));
		}
		return Redirect::to('/admin/request/change_provider/' . $request_id);
	}

//settings
	public function get_settings() {
		$braintree_environment = Config::get('app.braintree_environment');
		$braintree_merchant_id = Config::get('app.braintree_merchant_id');
		$braintree_public_key = Config::get('app.braintree_public_key');
		$braintree_private_key = Config::get('app.braintree_private_key');
		$braintree_cse = Config::get('app.braintree_cse');
		$twillo_account_sid = Config::get('app.twillo_account_sid');
		$twillo_auth_token = Config::get('app.twillo_auth_token');
		$twillo_number = Config::get('app.twillo_number');
		$timezone = Config::get('app.timezone');
		$stripe_publishable_key = Config::get('app.stripe_publishable_key');
		$url = Config::get('app.url');
		$website_title = Config::get('app.website_title');
		$s3_bucket = Config::get('app.s3_bucket');
		$default_payment = Config::get('app.default_payment');
		$stripe_secret_key = Config::get('app.stripe_secret_key');
		$mail_driver = Config::get('mail.mail_driver');
		$email_name = Config::get('mail.from.name');
		$email_address = Config::get('mail.from.address');
		$mandrill_secret = Config::get('services.mandrill_secret');
		$sendgrid_secret = Config::get('services.sendgrid_secret');

		$host = Config::get('mail.host');
		/* DEVICE PUSH NOTIFICATION DETAILS */
		$customer_certy_url = Config::get('app.customer_certy_url');
		$customer_certy_pass = Config::get('app.customer_certy_pass');
		$customer_certy_type = Config::get('app.customer_certy_type');
		$provider_certy_url = Config::get('app.provider_certy_url');
		$provider_certy_pass = Config::get('app.provider_certy_pass');
		$provider_certy_type = Config::get('app.provider_certy_type');
		$gcm_browser_key = Config::get('app.gcm_browser_key');
		/* DEVICE PUSH NOTIFICATION DETAILS END */
		$install = array(
			'braintree_environment' => $braintree_environment,
			'braintree_merchant_id' => $braintree_merchant_id,
			'braintree_public_key' => $braintree_public_key,
			'braintree_private_key' => $braintree_private_key,
			'braintree_cse' => $braintree_cse,
			'twillo_account_sid' => $twillo_account_sid,
			'twillo_auth_token' => $twillo_auth_token,
			'twillo_number' => $twillo_number,
			'stripe_publishable_key' => $stripe_publishable_key,
			'stripe_secret_key' => $stripe_secret_key,
			'mail_driver' => $mail_driver,
			'email_address' => $email_address,
			'email_name' => $email_name,
			'mandrill_secret' => $mandrill_secret,
			'sendgrid_secret' => $sendgrid_secret,
			'default_payment' => $default_payment,
			/* DEVICE PUSH NOTIFICATION DETAILS */
			'customer_certy_url' => $customer_certy_url,
			'customer_certy_pass' => $customer_certy_pass,
			'customer_certy_type' => $customer_certy_type,
			'provider_certy_url' => $provider_certy_url,
			'provider_certy_pass' => $provider_certy_pass,
			'provider_certy_type' => $provider_certy_type,
			'gcm_browser_key' => $gcm_browser_key,
				/* DEVICE PUSH NOTIFICATION DETAILS END */
		);
		$success = Input::get('success');
		$settings = Settings::all();
		$theme = Theme::first();
		if (isset($theme->id)) {
			$theme = Theme::first();
		} else {
			$theme = array();
		}
		$title = ucwords(trans('customize.Settings')); /* 'Settings' */
		return View::make('settings')
						->with('title', $title)
						->with('page', 'settings')
						->with('settings', $settings)
						->with('success', $success)
						->with('install', $install)
						->with('theme', $theme);
	}

	public function edit_keywords() {
		$success = Input::get('success');
		/* $keywords = Keywords::all(); */
		$icons = Icons::all();

		$UIkeywords = array();

		$UIkeywords['keyProvider'] = Lang::get('customize.Provider');
		$UIkeywords['keyUser'] = Lang::get('customize.User');
		$UIkeywords['keyTaxi'] = Lang::get('customize.Taxi');
		$UIkeywords['keyTrip'] = Lang::get('customize.Trip');
		$UIkeywords['keyWalk'] = Lang::get('customize.Walk');
		$UIkeywords['keyRequest'] = Lang::get('customize.Request');
		$UIkeywords['keyDashboard'] = Lang::get('customize.Dashboard');
		$UIkeywords['keyMap_View'] = Lang::get('customize.map_view');
		$UIkeywords['keyReviews'] = Lang::get('customize.Reviews');
		$UIkeywords['keyInformation'] = Lang::get('customize.Information');
		$UIkeywords['keyTypes'] = Lang::get('customize.Types');
		$UIkeywords['keyDocuments'] = Lang::get('customize.Documents');
		$UIkeywords['keyPromo_Codes'] = Lang::get('customize.promo_codes');
		$UIkeywords['keyCustomize'] = Lang::get('customize.Customize');
		$UIkeywords['keyPayment_Details'] = Lang::get('customize.payment_details');
		$UIkeywords['keySettings'] = Lang::get('customize.Settings');
		$UIkeywords['keyAdmin'] = Lang::get('customize.Admin');
		$UIkeywords['keyAdmin_Control'] = Lang::get('customize.admin_control');
		$UIkeywords['keyLog_Out'] = Lang::get('customize.log_out');
		$UIkeywords['keySchedules'] = Lang::get('customize.Schedules');
		$UIkeywords['keyWeekStatement'] = Lang::get('customize.WeekStatement');
		$title = ucwords(trans('customize.Customize')); /* 'Customize' */
		return View::make('keywords')
						->with('title', $title)
						->with('page', 'keywords')
						/* ->with('keywords', $keywords) */
						->with('icons', $icons)
						->with('Uikeywords', $UIkeywords)
						->with('success', $success);
	}

	public function save_keywords() {
		$braintree_cse = $stripe_publishable_key = $url = $timezone = $website_title = $s3_bucket = $twillo_account_sid = $twillo_auth_token = $twillo_number = $default_payment = $stripe_secret_key = $braintree_environment = $braintree_merchant_id = $braintree_public_key = $braintree_private_key = $customer_certy_url = $customer_certy_pass = $customer_certy_type = $provider_certy_url = $provider_certy_pass = $provider_certy_type = $gcm_browser_key = $key_provider = $key_user = $key_taxi = $key_trip = $key_currency = $total_trip = $cancelled_trip = $total_payment = $completed_trip = $card_payment = $credit_payment = $key_ref_pre = $android_client_app_url = $android_provider_app_url = $ios_client_app_url = $ios_provider_app_url = NULL;
		$braintree_environment = Config::get('app.braintree_environment');
		$braintree_merchant_id = Config::get('app.braintree_merchant_id');
		$braintree_public_key = Config::get('app.braintree_public_key');
		$braintree_private_key = Config::get('app.braintree_private_key');
		$braintree_cse = Config::get('app.braintree_cse');
		$twillo_account_sid = Config::get('app.twillo_account_sid');
		$twillo_auth_token = Config::get('app.twillo_auth_token');
		$twillo_number = Config::get('app.twillo_number');
		$timezone = Config::get('app.timezone');
		$stripe_publishable_key = Config::get('app.stripe_publishable_key');
		$url = Config::get('app.url');
		$website_title = Config::get('app.website_title');
		$s3_bucket = Config::get('app.s3_bucket');
		$default_payment = Config::get('app.default_payment');
		$stripe_secret_key = Config::get('app.stripe_secret_key');
		$mail_driver = Config::get('mail.driver');
		$email_name = Config::get('mail.from.name');
		$email_address = Config::get('mail.from.address');
		$mandrill_secret = Config::get('services.mandrill.secret');
		$sendgrid_secret = Config::get('services.sendgrid.secret');

		$host = Config::get('mail.host');
		/* DEVICE PUSH NOTIFICATION DETAILS */
		$customer_certy_url = Config::get('app.customer_certy_url');
		$customer_certy_pass = Config::get('app.customer_certy_pass');
		$customer_certy_type = Config::get('app.customer_certy_type');
		$provider_certy_url = Config::get('app.provider_certy_url');
		$provider_certy_pass = Config::get('app.provider_certy_pass');
		$provider_certy_type = Config::get('app.provider_certy_type');
		$gcm_browser_key = Config::get('app.gcm_browser_key');
		/* DEVICE PUSH NOTIFICATION DETAILS END */
		$install = array(
			'braintree_environment' => $braintree_environment,
			'braintree_merchant_id' => $braintree_merchant_id,
			'braintree_public_key' => $braintree_public_key,
			'braintree_private_key' => $braintree_private_key,
			'braintree_cse' => $braintree_cse,
			'twillo_account_sid' => $twillo_account_sid,
			'twillo_auth_token' => $twillo_auth_token,
			'twillo_number' => $twillo_number,
			'stripe_publishable_key' => $stripe_publishable_key,
			'stripe_secret_key' => $stripe_secret_key,
			'mail_driver' => $mail_driver,
			'email_address' => $email_address,
			'email_name' => $email_name,
			'mandrill_secret' => $mandrill_secret,

			'sendgrid_secret' => $sendgrid_secret,

			'default_payment' => $default_payment,
			/* DEVICE PUSH NOTIFICATION DETAILS */
			'customer_certy_url' => $customer_certy_url,
			'customer_certy_pass' => $customer_certy_pass,
			'customer_certy_type' => $customer_certy_type,
			'provider_certy_url' => $provider_certy_url,
			'provider_certy_pass' => $provider_certy_pass,
			'provider_certy_type' => $provider_certy_type,
			'gcm_browser_key' => $gcm_browser_key,
				/* DEVICE PUSH NOTIFICATION DETAILS END */
		);        // Modifying Database Config
		/* $keywords = Keywords::all();
		  foreach ($keywords as $keyword) {
		  // Log::info('keyword = ' . print_r(Input::get($keyword->id), true));
		  if (Input::get($keyword->id) != NULL) {
		  // Log::info('keyword = ' . print_r(Input::get($keyword->id), true));
		  $temp = Input::get($keyword->id);
		  $temp_setting = Keywords::find($keyword->id);
		  $temp_setting->keyword = Input::get($keyword->id);
		  $temp_setting->save();
		  }
		  } */

		if (Input::has('key_provider')) {
			$key_provider = trim(Input::get('key_provider'));
			if ($key_provider != "") {
				/* $keyword = Keywords::find(1);
				  $keyword->keyword = Input::get('key_provider');
				  // $keyword->alias = Input::get('key_provider');
				  $keyword->save(); */
			} else {
				$key_provider = null;
			}
		}
		if (Input::has('key_user')) {
			$key_user = trim(Input::get('key_user'));
			if ($key_user != "") {
				/* $keyword = Keywords::find(2);
				  $keyword->keyword = Input::get('key_user');
				  // $keyword->alias = Input::get('key_user');
				  $keyword->save(); */
			} else {
				$key_user = null;
			}
		}
		if (Input::has('key_taxi')) {
			$key_taxi = trim(Input::get('key_taxi'));
			if ($key_taxi != "") {
				/* $keyword = Keywords::find(3);
				  $keyword->keyword = Input::get('key_taxi');
				  // $keyword->alias = Input::get('key_taxi');
				  $keyword->save(); */
			} else {
				$key_taxi = null;
			}
		}
		if (Input::has('key_trip')) {
			$key_trip = trim(Input::get('key_trip'));
			if ($key_trip != "") {
				/* $keyword = Keywords::find(4);
				  $keyword->keyword = Input::get('key_trip');
				  // $keyword->alias = Input::get('key_trip');
				  $keyword->save(); */
			} else {
				$key_trip = null;
			}
		}
		if (Input::has('key_currency')) {
			$key_currency = trim(Input::get('key_currency'));
			if ($key_currency != '$' || $key_currency != "usd" || $key_currency != "USD") {
				$setransfer = Settings::where('key', 'transfer')->first();
				$setransfer->value = 2;
				$setransfer->save();
			}
			if ($key_currency != "") {
				/* $keyword = Keywords::find(5);
				  $keyword->keyword = Input::get('key_currency');
				  // $keyword->alias = Input::get('key_currency');
				  $keyword->save(); */
			} else {
				$key_currency = null;
			}
		}
		if (Input::has('total_trip')) {
			$total_trip = trim(Input::get('total_trip'));
			if ($total_trip != "") {
				/* $keyword = Keywords::find(6);
				  $keyword->alias = Input::get('total_trip');
				  $keyword->save(); */
			} else {
				$total_trip = null;
			}
		}
		if (Input::has('cancelled_trip')) {
			$cancelled_trip = trim(Input::get('cancelled_trip'));
			if ($cancelled_trip != "") {
				/* $keyword = Keywords::find(7);
				  $keyword->alias = Input::get('cancelled_trip');
				  $keyword->save(); */
			} else {
				$cancelled_trip = null;
			}
		}
		if (Input::has('total_payment')) {
			$total_payment = trim(Input::get('total_payment'));
			if ($total_payment != "") {
				/* $keyword = Keywords::find(8);
				  $keyword->alias = Input::get('total_payment');
				  $keyword->save(); */
			} else {
				$total_payment = null;
			}
		}
		if (Input::has('completed_trip')) {
			$completed_trip = trim(Input::get('completed_trip'));
			if ($completed_trip != "") {
				/* $keyword = Keywords::find(9);
				  $keyword->alias = Input::get('completed_trip');
				  $keyword->save(); */
			} else {
				$completed_trip = null;
			}
		}
		if (Input::has('card_payment')) {
			$card_payment = trim(Input::get('card_payment'));
			if ($card_payment != "") {
				/* $keyword = Keywords::find(10);
				  $keyword->alias = Input::get('card_payment');
				  $keyword->save(); */
			} else {
				$card_payment = null;
			}
		}
		if (Input::has('credit_payment')) {
			$credit_payment = trim(Input::get('credit_payment'));
			if ($credit_payment != "") {
				/* $keyword = Keywords::find(11);
				  $keyword->alias = Input::get('credit_payment');
				  $keyword->save(); */
			} else {
				$credit_payment = null;
			}
		}
		if (Input::has('key_ref_pre')) {
			$key_ref_pre = trim(Input::get('key_ref_pre'));
			if ($key_ref_pre != "") {
				/* $keyword = Keywords::find(11);
				  $keyword->alias = Input::get('credit_payment');
				  $keyword->save(); */
			} else {
				$key_ref_pre = null;
			}
		}
		if (Input::has('cash_payment')) {
			$cash_payment = trim(Input::get('cash_payment'));
			if ($cash_payment != "") {
				/* $keyword = Keywords::find(11);
				  $keyword->alias = Input::get('credit_payment');
				  $keyword->save(); */
			} else {
				$cash_payment = null;
			}
		}
		if (Input::has('promotional_payment')) {
			$promotional_payment = trim(Input::get('promotional_payment'));
			if ($promotional_payment != "") {
				/* $keyword = Keywords::find(11);
				  $keyword->alias = Input::get('credit_payment');
				  $keyword->save(); */
			} else {
				$promotional_payment = null;
			}
		}
		if (Input::has('schedules_icon')) {
			$schedules_icon = trim(Input::get('schedules_icon'));
			if ($schedules_icon != "") {
				/* $keyword = Keywords::find(11);
				  $keyword->alias = Input::get('credit_payment');
				  $keyword->save(); */
			} else {
				$schedules_icon = null;
			}
		}
		/* $key_provider $key_user $key_taxi $key_trip $key_currency $total_trip $cancelled_trip $total_payment $completed_trip $card_payment $credit_payment */
		$appfile = fopen(app_path() . "/config/app.php", "w") or die(trans('adminController.unable_open_file'));
		/* $appfile_config = generate_app_config($braintree_cse, $stripe_publishable_key, $url, $timezone, $website_title, $s3_bucket, $twillo_account_sid, $twillo_auth_token, $twillo_number, $default_payment, $stripe_secret_key, $braintree_environment, $braintree_merchant_id, $braintree_public_key, $braintree_private_key, $customer_certy_url, $customer_certy_pass, $customer_certy_type, $provider_certy_url, $provider_certy_pass, $provider_certy_type, $gcm_browser_key, $key_provider, $key_user, $key_taxi, $key_trip, $key_currency, $total_trip, $cancelled_trip, $total_payment, $completed_trip, $card_payment, $credit_payment, $key_ref_pre); */
		$appfile_config = generate_app_config($braintree_cse, $stripe_publishable_key, $url, $timezone, $website_title, $s3_bucket, $twillo_account_sid, $twillo_auth_token, $twillo_number, $default_payment, $stripe_secret_key, $braintree_environment, $braintree_merchant_id, $braintree_public_key, $braintree_private_key, $customer_certy_url, $customer_certy_pass, $customer_certy_type, $provider_certy_url, $provider_certy_pass, $provider_certy_type, $gcm_browser_key, $key_provider, $key_user, $key_taxi, $key_trip, $key_currency, $total_trip, $cancelled_trip, $total_payment, $completed_trip, $card_payment, $credit_payment, $key_ref_pre, $android_client_app_url, $android_provider_app_url, $ios_client_app_url, $ios_provider_app_url, $cash_payment, $promotional_payment, $schedules_icon);
		fwrite($appfile, $appfile_config);
		fclose($appfile);

		return Redirect::to('/admin/edit_keywords?success=1');
	}

	public function save_keywords_UI() {
		$dashboard = trim(Input::get('val_dashboard'));
		$map_view = trim(Input::get('val_map_view'));
		$provider = trim(Input::get('val_provider'));
		$user = trim(Input::get('val_user'));
		$taxi = trim(Input::get('val_taxi'));
		$trip = trim(Input::get('val_trip'));
		$walk = trim(Input::get('val_walk'));
		$request = trim(Input::get('val_request'));
		$reviews = trim(Input::get('val_reviews'));
		$information = trim(Input::get('val_information'));
		$types = trim(Input::get('val_types'));
		$documents = trim(Input::get('val_documents'));
		$promo_codes = trim(Input::get('val_promo_codes'));
		$customize = trim(Input::get('val_customize'));
		$payment_details = trim(Input::get('val_payment_details'));
		$settings = trim(Input::get('val_settings'));
		$val_admin = trim(Input::get('val_admin'));
		$admin_control = trim(Input::get('val_admin_control'));
		$log_out = trim(Input::get('val_log_out'));
		$schedule = trim(Input::get('val_schedules'));
		$weekstatement = trim(Input::get('val_weekstatement'));

		if ($weekstatement == null || $weekstatement == "") {
			$weekstatement = Lang::get('customize.Schedules');
		} else {
			$weekstatement = $weekstatement;
		}
		if ($schedule == null || $schedule == "") {
			$schedule = Lang::get('customize.Schedules');
		} else {
			$schedule = $schedule;
		}
		if ($dashboard == null || $dashboard == "") {
			$dashboard = Lang::get('customize.Dashboard');
		} else {
			$dashboard = $dashboard;
		}
		if ($map_view == null || $map_view == "") {
			$map_view = Lang::get('customize.map_view');
		} else {
			$map_view = $map_view;
		}
		if ($provider == null || $provider == "") {
			$provider = Lang::get('customize.Provider');
		} else {
			$provider = $provider;
		}
		if ($user == null || $user == "") {
			$user = Lang::get('customize.User');
		} else {
			$user = $user;
		}
		if ($taxi == null || $taxi == "") {
			$taxi = Lang::get('customize.Taxi');
		} else {
			$taxi = $taxi;
		}
		if ($trip == null || $trip == "") {
			$trip = Lang::get('customize.Trip');
		} else {
			$trip = $trip;
		}
		if ($walk == null || $walk == "") {
			$walk = Lang::get('customize.Walk');
		} else {
			$walk = $walk;
		}
		if ($request == null || $request == "") {
			$request = Lang::get('customize.Request');
		} else {
			$request = $request;
		}
		if ($reviews == null || $reviews == "") {
			$reviews = Lang::get('customize.Reviews');
		} else {
			$reviews = $reviews;
		}
		if ($information == null || $information == "") {
			$information = Lang::get('customize.Information');
		} else {
			$information = $information;
		}
		if ($types == null || $types == "") {
			$types = Lang::get('customize.Types');
		} else {
			$types = $types;
		}
		if ($documents == null || $documents == "") {
			$documents = Lang::get('customize.Documents');
		} else {
			$documents = $documents;
		}
		if ($promo_codes == null || $promo_codes == "") {
			$promo_codes = Lang::get('customize.promo_codes');
		} else {
			$promo_codes = $promo_codes;
		}
		if ($customize == null || $customize == "") {
			$customize = Lang::get('customize.Customize');
		} else {
			$customize = $customize;
		}
		if ($payment_details == null || $payment_details == "") {
			$payment_details = Lang::get('customize.payment_details');
		} else {
			$payment_details = $payment_details;
		}
		if ($settings == null || $settings == "") {
			$settings = Lang::get('customize.Settings');
		} else {
			$settings = $settings;
		}
		if ($val_admin == null || $val_admin == "") {
			$val_admin = Lang::get('customize.Admin');
		} else {
			$val_admin = $val_admin;
		}
		if ($admin_control == null || $admin_control == "") {
			$admin_control = Lang::get('customize.admin_control');
		} else {
			$admin_control = $admin_control;
		}
		if ($log_out == null || $log_out == "") {
			$log_out = Lang::get('customize.log_out');
		} else {
			$log_out = $log_out;
		}
		$appfile = fopen(app_path() . "/lang/en/customize.php", "w") or die(trans('adminController.not_open_file'));
		$appfile_config = generate_custome_key($dashboard, $map_view, $provider, $user, $taxi, $trip, $walk, $request, $reviews, $information, $types, $documents, $promo_codes, $customize, $payment_details, $settings, $val_admin, $admin_control, $log_out, $schedule, $weekstatement);
		fwrite($appfile, $appfile_config);
		fclose($appfile);

		return Redirect::to('/admin/edit_keywords?success=1');
	}

	public function adminCurrency() {
		$currency_selected = $_POST['currency_selected'];
		/* $keycurrency = Keywords::find(5);
		  $original_selection = $keycurrency->keyword; */
		$original_selection = Config::get('app.generic_keywords.Currency');
		if ($original_selection == '$') {
			$original_selection = "USD";
		}
		if ($currency_selected == '$') {
			$currency_selected = "USD";
		}
		if ($currency_selected == $original_selection) {
			// same currency
			$data['success'] = false;
			$data['error_message'] = trans('adminController.same_coin') .'.';
		} else {
			$httpAdapter = new \Ivory\HttpAdapter\FileGetContentsHttpAdapter();
			// Create the Yahoo Finance provider
			$yahooProvider = new \Swap\Provider\YahooFinanceProvider($httpAdapter);
			// Create Swap with the provider
			$swap = new \Swap\Swap($yahooProvider);
			$rate = $swap->quote($original_selection . "/" . $currency_selected);
			$rate = json_decode($rate, true);
			$data['success'] = true;
			$data['rate'] = $rate;
		}
		return $data;
	}

	public function save_settings() {

		if(Input::has('car_number_format')){
			$car_number_key = Settings::where('key', 'car_number_format')->first();
			$car_number_key->value = Input::get('car_number_format');
			$car_number_key->save();
		}

		$settings = Settings::all();
		foreach ($settings as $setting) {
			if (Input::get($setting->id) != NULL) {
				$temp_setting = Settings::find($setting->id);
				if(($temp_setting->key != "google_maps_api_key") && ($temp_setting->key != "car_number_format"))
					$temp_setting->value = Input::get($setting->id);
				$temp_setting->save();
			}
		}
		return Redirect::to('/admin/settings?success=1');
	}

	//Installation Settings
	public function installation_settings() {
		$twillo_account_sid = Config::get('app.twillo_account_sid');
		$twillo_auth_token = Config::get('app.twillo_auth_token');
		$twillo_number = Config::get('app.twillo_number');
		$timezone = Config::get('app.timezone');

		$url = Config::get('app.url');
		$website_title = Config::get('app.website_title');
		$s3_bucket = Config::get('app.s3_bucket');

		//CONFIGURACOES DE E-MAIL
		$host = Config::get('mail.host');
		$mail_driver = Config::get('mail.driver');
		$email_name = Config::get('mail.from.name');
		$email_address = Config::get('mail.from.address');
		$mandrill_secret = Config::get('services.mandrill.secret');
		$sendgrid_secret = Config::get('services.sendgrid.secret');
		$mandrill_username = Config::get('services.mandrill.username');
		$sendgrid_username = Config::get('services.sendgrid.username');

		//CONFIGURACOES DE PAGAMENTO
		//modelo de negócios
		$settingBusinessModel = Settings::where('key', 'default_business_model')->first();
		$settingsProviderTransferInterval = Settings::where('key', 'provider_transfer_interval')->first();
		$settingsProviderTransferDay = Settings::where('key', 'provider_transfer_day')->first();

		//tipos de pagamento
		$settingMoney = Settings::where('key', 'payment_money')->first();
		$settingCard = Settings::where('key', 'payment_card')->first();
		$settingVoucher = Settings::where('key', 'payment_voucher')->first();

		//intermediadores de pagamento
		$default_payment = Config::get('app.default_payment');

		//braintree
		$braintree_environment = Config::get('app.braintree_environment');
		$braintree_merchant_id = Config::get('app.braintree_merchant_id');
		$braintree_public_key = Config::get('app.braintree_public_key');
		$braintree_private_key = Config::get('app.braintree_private_key');
		$braintree_cse = Config::get('app.braintree_cse');

		//stripe
		$stripe_secret_key = Config::get('app.stripe_secret_key');
		$stripe_publishable_key = Config::get('app.stripe_publishable_key');

		//pagar.me
		$pagarme_api_key = Config::get('app.pagarme_api_key');
		$pagarme_encryption_key = Config::get('app.pagarme_encryption_key');

		/* DEVICE PUSH NOTIFICATION DETAILS */
		$customer_certy_url = Config::get('app.customer_certy_url');
		$customer_certy_pass = Config::get('app.customer_certy_pass');
		$customer_certy_type = Config::get('app.customer_certy_type');
		$provider_certy_url = Config::get('app.provider_certy_url');
		$provider_certy_pass = Config::get('app.provider_certy_pass');
		$provider_certy_type = Config::get('app.provider_certy_type');
		$gcm_browser_key = Config::get('app.gcm_browser_key');
		/* DEVICE PUSH NOTIFICATION DETAILS END */
		$install = array(
			'braintree_environment' => $braintree_environment,
			'braintree_merchant_id' => $braintree_merchant_id,
			'braintree_public_key' => $braintree_public_key,
			'braintree_private_key' => $braintree_private_key,
			'braintree_cse' => $braintree_cse,
			'twillo_account_sid' => $twillo_account_sid,
			'twillo_auth_token' => $twillo_auth_token,
			'twillo_number' => $twillo_number,
			
			'stripe_publishable_key' => $stripe_publishable_key,
			'stripe_secret_key' => $stripe_secret_key,
			'pagarme_api_key' => $pagarme_api_key,
			'pagarme_encryption_key' => $pagarme_encryption_key,
			'mail_driver' => $mail_driver,
			'email_address' => $email_address,
			'mandrill_username' => $mandrill_username,
			'sendgrid_username' => $sendgrid_username,
			'email_name' => $email_name,
			'host' => $host,
			'mandrill_secret' => $mandrill_secret,

			'sendgrid_secret' => $sendgrid_secret,            

			'default_business_model' => $settingBusinessModel ? $settingBusinessModel->value : 'monthly',
			'provider_transfer_interval' => $settingsProviderTransferInterval ? $settingsProviderTransferInterval->value : "daily",
			'provider_transfer_day' => $settingsProviderTransferDay ? $settingsProviderTransferDay->value : "1",

			'payment_money' => $settingMoney? $settingMoney->value : '1', 
			'payment_card' => $settingCard? $settingCard->value : '1',
			'payment_voucher' => $settingVoucher? $settingVoucher->value : '1',

			'default_payment' => $default_payment,
			/* DEVICE PUSH NOTIFICATION DETAILS */
			'customer_certy_url' => $customer_certy_url,
			'customer_certy_pass' => $customer_certy_pass,
			'customer_certy_type' => $customer_certy_type,
			'provider_certy_url' => $provider_certy_url,
			'provider_certy_pass' => $provider_certy_pass,
			'provider_certy_type' => $provider_certy_type,
			'gcm_browser_key' => $gcm_browser_key,
				/* DEVICE PUSH NOTIFICATION DETAILS END */                
		);
		$success = Input::get('success');
		$cert_def = 0;
		$cer = Certificates::where('file_type', 'certificate')->where('client', 'apple')->get();
		foreach ($cer as $key) {
			if ($key->default == 1) {
				$cert_def = $key->type;
			}
		}
		$title = ucwords(trans('adminController.install_of'). " " . trans('customize.Settings')); /* 'Installation Settings' */
		return View::make('install_settings')
						->with('title', $title)
						->with('success', $success)
						->with('page', 'settings')
						->with('cert_def', $cert_def)
						->with('install', $install);
	}

	public function finish_install() {
		$braintree_cse = $pagarme_api_key = $pagarme_encryption_key = $stripe_publishable_key = $url = $timezone = $website_title = $s3_bucket = $twillo_account_sid = $twillo_auth_token = $twillo_number = $default_payment = $stripe_secret_key = $braintree_environment = $braintree_merchant_id = $braintree_public_key = $braintree_private_key = $customer_certy_url = $customer_certy_pass = $customer_certy_type = $provider_certy_url = $provider_certy_pass = $provider_certy_type = $gcm_browser_key = $key_provider = $key_user = $key_taxi = $key_trip = $key_currency = $total_trip = $cancelled_trip = $total_payment = $completed_trip = $card_payment = $credit_payment = $key_ref_pre = $android_client_app_url = $android_provider_app_url = $ios_client_app_url = $ios_provider_app_url = NULL;
		
		//CONFIGURACOES DE PAGAMENTO

		//modelo de negócios
		$settingBusinessModel = Settings::where('key', 'default_business_model')->first();
		$settingsProviderTransferInterval = Settings::where('key', 'provider_transfer_interval')->first();
		$settingsProviderTransferDay = Settings::where('key', 'provider_transfer_day')->first();

		if(!$settingBusinessModel){
			$settingBusinessModel = new Settings();
			$settingBusinessModel->key = 'default_business_model';
		}

		if(!$settingsProviderTransferInterval){
			$settingsProviderTransferInterval = new Settings();
			$settingsProviderTransferInterval->key = 'provider_transfer_interval';
		}

		if(!$settingsProviderTransferDay){
			$settingsProviderTransferDay = new Settings();
			$settingsProviderTransferDay->key = 'provider_transfer_day';
		}

		//opções de pagamento
		$settingMoney = Settings::where('key', 'payment_money')->first();
		$settingCard = Settings::where('key', 'payment_card')->first();
		$settingVoucher = Settings::where('key', 'payment_voucher')->first();

		if(!$settingMoney){
			$settingMoney = new Settings();
			$settingMoney->key = 'payment_money';
		}
		if(!$settingCard){
			$settingCard = new Settings();
			$settingCard->key = 'payment_card';
		}
		if(!$settingVoucher){
			$settingVoucher = new Settings();
			$settingVoucher->key = 'payment_voucher';
		}

		//intermediador de pagamento
		$default_payment = Config::get('app.default_payment');
		//pagarme
		$pagarme_api_key = Config::get('app.pagarme_api_key');
		$pagarme_encryption_key = Config::get('app.pagarme_encryption_key');

		//stripe
		$stripe_publishable_key = Config::get('app.stripe_publishable_key');
		$stripe_secret_key = Config::get('app.stripe_secret_key');

		//braintree
		$braintree_environment = Config::get('app.braintree_environment');
		$braintree_merchant_id = Config::get('app.braintree_merchant_id');
		$braintree_public_key = Config::get('app.braintree_public_key');
		$braintree_private_key = Config::get('app.braintree_private_key');
		$braintree_cse = Config::get('app.braintree_cse');

		$twillo_account_sid = Config::get('app.twillo_account_sid');
		$twillo_auth_token = Config::get('app.twillo_auth_token');
		$twillo_number = Config::get('app.twillo_number');
		$timezone = Config::get('app.timezone');

		$url = Config::get('app.url');
		$website_title = Config::get('app.website_title');
		$s3_bucket = Config::get('app.s3_bucket');
		
		
		$mail_driver = Config::get('mail.driver');
		$email_name = Config::get('mail.from.name');
		$email_address = Config::get('mail.from.address');
		$mandrill_secret = Config::get('services.mandrill.secret');
		$sendgrid_secret = Config::get('services.sendgrid.secret');

		$host = Config::get('mail.host');
		/* DEVICE PUSH NOTIFICATION DETAILS */
		$customer_certy_url = Config::get('app.customer_certy_url');
		$customer_certy_pass = Config::get('app.customer_certy_pass');
		$customer_certy_type = Config::get('app.customer_certy_type');
		$provider_certy_url = Config::get('app.provider_certy_url');
		$provider_certy_pass = Config::get('app.provider_certy_pass');
		$provider_certy_type = Config::get('app.provider_certy_type');
		$gcm_browser_key = Config::get('app.gcm_browser_key');
		/* DEVICE PUSH NOTIFICATION DETAILS END */
		$install = array(
			'pagarme_api_key' => $pagarme_api_key,
			'pagarme_encryption_key' => $pagarme_encryption_key,
			'braintree_environment' => $braintree_environment,
			'braintree_merchant_id' => $braintree_merchant_id,
			'braintree_public_key' => $braintree_public_key,
			'braintree_private_key' => $braintree_private_key,
			'braintree_cse' => $braintree_cse,
			'twillo_account_sid' => $twillo_account_sid,
			'twillo_auth_token' => $twillo_auth_token,
			'twillo_number' => $twillo_number,
			'stripe_publishable_key' => $stripe_publishable_key,
			'stripe_secret_key' => $stripe_secret_key,
			'mail_driver' => $mail_driver,
			'email_address' => $email_address,
			'email_name' => $email_name,
			'mandrill_secret' => $mandrill_secret,
			'sendgrid_secret' => $sendgrid_secret,
			'default_business_model' => $settingBusinessModel->value,
			'provider_transfer_interval' => $settingsProviderTransferInterval,
			'provider_transfer_day' => $settingsProviderTransferDay,
			'payment_money' => $settingMoney->value,
			'payment_card' => $settingCard->value,
			'payment_voucher' => $settingVoucher->value,
			'default_payment' => $default_payment,
			/* DEVICE PUSH NOTIFICATION DETAILS */
			'customer_certy_url' => $customer_certy_url,
			'customer_certy_pass' => $customer_certy_pass,
			'customer_certy_type' => $customer_certy_type,
			'provider_certy_url' => $provider_certy_url,
			'provider_certy_pass' => $provider_certy_pass,
			'provider_certy_type' => $provider_certy_type,
			'gcm_browser_key' => $gcm_browser_key,
				/* DEVICE PUSH NOTIFICATION DETAILS END */
		);        // Modifying Database Config
		if (isset($_POST['sms'])) {
			$twillo_account_sid = Input::get('twillo_account_sid');
			$twillo_auth_token = Input::get('twillo_auth_token');
			$twillo_number = Input::get('twillo_number');

			$appfile = fopen(app_path() . "/config/app.php", "w") or die(trans('adminController.not_open_file'));

			$appfile_config = generate_app_config($pagarme_api_key, $pagarme_encryption_key, $braintree_cse, $stripe_publishable_key, $url, $timezone, $website_title, $s3_bucket, $twillo_account_sid, $twillo_auth_token, $twillo_number, $default_payment, $stripe_secret_key, $braintree_environment, $braintree_merchant_id, $braintree_public_key, $braintree_private_key, $customer_certy_url, $customer_certy_pass, $customer_certy_type, $provider_certy_url, $provider_certy_pass, $provider_certy_type, $gcm_browser_key, $key_provider, $key_user, $key_taxi, $key_trip, $key_currency, $total_trip, $cancelled_trip, $total_payment, $completed_trip, $card_payment, $credit_payment, $key_ref_pre, $android_client_app_url, $android_provider_app_url, $ios_client_app_url, $ios_provider_app_url);
			fwrite($appfile, $appfile_config);
			fclose($appfile);
		}

		if (isset($_POST['payment'])) {
			if($settingBusinessModel->value != trim(Input::get('default_business_model'))){
					$settingBusinessModel->value = trim(Input::get('default_business_model'));
					$settingBusinessModel->save();
			}

			if($settingsProviderTransferInterval->value != trim(Input::get('provider_transfer_interval'))){
					$settingsProviderTransferInterval->value = trim(Input::get('provider_transfer_interval'));
					$settingsProviderTransferInterval->save();
			}

			$providerTransferDay = trim(Input::get('provider_transfer_day_weekly'));
			if($settingsProviderTransferDay->value != $providerTransferDay){
					$settingsProviderTransferDay->value = $providerTransferDay;
					$settingsProviderTransferDay->save();
			}

			if(Input::has('payment_money')){
				$settingMoney->value = '1';
			}
			else{
				$settingMoney->value = '0';
			}
			$settingMoney->save();

			if(Input::has('payment_card')){
				$settingCard->value = '1';
			}
			else{
				$settingCard->value = '0';
			}
			$settingCard->save();

			if(Input::has('payment_voucher')){
				$settingVoucher->value = '1';
			}
			else{
				$settingVoucher->value = '0';
			}
			$settingVoucher->save();

			$default_payment = Input::get('default_payment');
			if($default_payment == 'pagarme'){
				if ($pagarme_api_key != trim(Input::get('pagarme_api_key')) || $pagarme_encryption_key != trim(Input::get('pagarme_encryption_key'))) {
					/* DELETE CUSTOMER CARDS FROM DATABASE */
					$delete_un_rq = DB::delete("DELETE FROM payment WHERE 1;");
					/* DELETE CUSTOMER CARDS FROM DATABASE END */
					$pagarme_api_key = Input::get('pagarme_api_key');
					$pagarme_encryption_key = Input::get('pagarme_encryption_key');
					$stripe_secret_key = '';
					$stripe_publishable_key = '';
					$braintree_environment = '';
					$braintree_merchant_id = '';
					$braintree_public_key = '';
					$braintree_private_key = '';
					$braintree_cse = '';
					$appfile = fopen(app_path() . "/config/app.php", "w") or die(trans('adminController.not_open_file'));

					$appfile_config = generate_app_config($pagarme_api_key, $pagarme_encryption_key, $braintree_cse, $stripe_publishable_key, $url, $timezone, $website_title, $s3_bucket, $twillo_account_sid, $twillo_auth_token, $twillo_number, $default_payment, $stripe_secret_key, $braintree_environment, $braintree_merchant_id, $braintree_public_key, $braintree_private_key, $customer_certy_url, $customer_certy_pass, $customer_certy_type, $provider_certy_url, $provider_certy_pass, $provider_certy_type, $gcm_browser_key, $key_provider, $key_user, $key_taxi, $key_trip, $key_currency, $total_trip, $cancelled_trip, $total_payment, $completed_trip, $card_payment, $credit_payment, $key_ref_pre, $android_client_app_url, $android_provider_app_url, $ios_client_app_url, $ios_provider_app_url);
					fwrite($appfile, $appfile_config);
					fclose($appfile);
					
				}
			}
			else if ($default_payment == 'stripe') {
				if ($stripe_secret_key != trim(Input::get('stripe_secret_key')) || $stripe_publishable_key != trim(Input::get('stripe_publishable_key'))) {
					/* DELETE CUSTOMER CARDS FROM DATABASE */
					$delete_un_rq = DB::delete("DELETE FROM payment WHERE 1;");
					/* DELETE CUSTOMER CARDS FROM DATABASE END */
					$pagarme_api_key = '';
					$pagarme_encryption_key = '';
					$stripe_secret_key = Input::get('stripe_secret_key');
					$stripe_publishable_key = Input::get('stripe_publishable_key');
					$braintree_environment = '';
					$braintree_merchant_id = '';
					$braintree_public_key = '';
					$braintree_private_key = '';
					$braintree_cse = '';
					$appfile = fopen(app_path() . "/config/app.php", "w") or die(trans('adminController.not_open_file'));

					$appfile_config = generate_app_config($pagarme_api_key, $pagarme_encryption_key, $braintree_cse, $stripe_publishable_key, $url, $timezone, $website_title, $s3_bucket, $twillo_account_sid, $twillo_auth_token, $twillo_number, $default_payment, $stripe_secret_key, $braintree_environment, $braintree_merchant_id, $braintree_public_key, $braintree_private_key, $customer_certy_url, $customer_certy_pass, $customer_certy_type, $provider_certy_url, $provider_certy_pass, $provider_certy_type, $gcm_browser_key, $key_provider, $key_user, $key_taxi, $key_trip, $key_currency, $total_trip, $cancelled_trip, $total_payment, $completed_trip, $card_payment, $credit_payment, $key_ref_pre, $android_client_app_url, $android_provider_app_url, $ios_client_app_url, $ios_provider_app_url);
					fwrite($appfile, $appfile_config);
					fclose($appfile);
				}
			} else {
				if ($braintree_environment != trim(Input::get('braintree_environment')) || $braintree_merchant_id != trim(Input::get('braintree_merchant_id')) || $braintree_public_key != trim(Input::get('braintree_public_key')) || $braintree_private_key != trim(Input::get('braintree_private_key')) || $braintree_cse != trim(Input::get('braintree_cse'))) {
					/* DELETE CUSTOMER CARDS FROM DATABASE */
					$delete_un_rq = DB::delete("DELETE FROM payment WHERE 1;");
					/* DELETE CUSTOMER CARDS FROM DATABASE END */

					$pagarme_api_key = '';
					$pagarme_encryption_key = '';
					$stripe_secret_key = '';
					$stripe_publishable_key = '';
					$braintree_environment = Input::get('braintree_environment');
					$braintree_merchant_id = Input::get('braintree_merchant_id');
					$braintree_public_key = Input::get('braintree_public_key');
					$braintree_private_key = Input::get('braintree_private_key');
					$braintree_cse = Input::get('braintree_cse');
					$appfile = fopen(app_path() . "/config/app.php", "w") or die(trans('adminController.not_open_file'));
					/* $appfile_config = generate_app_config($braintree_cse, $stripe_publishable_key, $url, $timezone, $website_title, $s3_bucket, $twillo_account_sid, $twillo_auth_token, $twillo_number, $default_payment, $stripe_secret_key, $braintree_environment, $braintree_merchant_id, $braintree_public_key, $braintree_private_key); */
					$appfile_config = generate_app_config($pagarme_api_key, $pagarme_encryption_key, $braintree_cse, $stripe_publishable_key, $url, $timezone, $website_title, $s3_bucket, $twillo_account_sid, $twillo_auth_token, $twillo_number, $default_payment, $stripe_secret_key, $braintree_environment, $braintree_merchant_id, $braintree_public_key, $braintree_private_key, $customer_certy_url, $customer_certy_pass, $customer_certy_type, $provider_certy_url, $provider_certy_pass, $provider_certy_type, $gcm_browser_key, $key_provider, $key_user, $key_taxi, $key_trip, $key_currency, $total_trip, $cancelled_trip, $total_payment, $completed_trip, $card_payment, $credit_payment, $key_ref_pre, $android_client_app_url, $android_provider_app_url, $ios_client_app_url, $ios_provider_app_url);
					fwrite($appfile, $appfile_config);
					fclose($appfile);
				}
			}
		}

		// Modifying Mail Config File

		if (isset($_POST['mail'])) {

			$mail_driver = Input::get('mail_driver');
			$email_name = Input::get('email_name');
			$email_address = Input::get('email_address');

			$mandrill_secret = Input::get('mandrill_secret');
			$sendgrid_secret = Input::get('sendgrid_secret'); 
			$mandrill_hostname = "";
			if ($mail_driver == 'mail') {
				$mandrill_hostname = "localhost";
			} elseif ($mail_driver == 'mandrill') {
				$mandrill_hostname = Input::get('host_name');
			}elseif($mail_driver == 'sendgrid'){
				$mandrill_hostname = Input::get('host_name');
			}
			$mailfile = fopen(app_path() . "/config/mail.php", "w") or die(trans('adminController.not_open_file'));
			$mailfile_config = generate_mail_config($mandrill_hostname, $mail_driver, $email_name, $email_address);
 ;
			fwrite($mailfile, $mailfile_config);
			fclose($mailfile);

			if ($mail_driver == 'mandrill') {
				$mandrill_username = Input::get('user_name');
				$sendgrid_username = Input::get('user_name2');
				$servicesfile = fopen(app_path() . "/config/services.php", "w") or die(trans('adminController.not_open_file'));
				$servicesfile_config = generate_services_config($mandrill_secret, $mandrill_username, $sendgrid_secret, $sendgrid_username);
				fwrite($servicesfile, $servicesfile_config);
				fclose($servicesfile);
			} else if ($mail_driver == 'sendgrid') {
				$sendgrid_username = Input::get('user_name2');
				$mandrill_username = Input::get('user_name');
				$servicesfile = fopen(app_path() . "/config/services.php", "w") or die(trans('adminController.not_open_file'));
				$servicesfile_config = generate_services_config($mandrill_secret, $mandrill_username, $sendgrid_secret, $sendgrid_username);
				fwrite($servicesfile, $servicesfile_config);
				fclose($servicesfile);
			}
		}

		$install = array(
			'braintree_environment' => $braintree_environment,
			'braintree_merchant_id' => $braintree_merchant_id,
			'braintree_public_key' => $braintree_public_key,
			'braintree_private_key' => $braintree_private_key,
			'braintree_cse' => $braintree_cse,
			'twillo_account_sid' => $twillo_account_sid,
			'twillo_auth_token' => $twillo_auth_token,
			'twillo_number' => $twillo_number,
			'stripe_publishable_key' => $stripe_publishable_key,
			'stripe_secret_key' => $stripe_secret_key,
			'mail_driver' => $mail_driver,
			'email_address' => $email_address,
			'email_name' => $email_name,
			'mandrill_secret' => $mandrill_secret,
			'sendgrid_secret' => $sendgrid_secret,

			'default_business_model' => $settingBusinessModel->value, 
			'default_payment' => $default_payment,
			/* DEVICE PUSH NOTIFICATION DETAILS */
			'customer_certy_url' => $customer_certy_url,
			'customer_certy_pass' => $customer_certy_pass,
			'customer_certy_type' => $customer_certy_type,
			'provider_certy_url' => $provider_certy_url,
			'provider_certy_pass' => $provider_certy_pass,
			'provider_certy_type' => $provider_certy_type,
			'gcm_browser_key' => $gcm_browser_key,
			/* DEVICE PUSH NOTIFICATION DETAILS END */
		);

		return Redirect::to('/admin/settings?success=1')
						->with('install', $install);
	}

	public function updateSetSiteDirectory () {

		if(Input::get('website_directory')){
			$website_directory = Settings::where('key', 'website_directory')->first();

			if(!$website_directory)
				$website_directory = new Settings ;

			$website_directory->key 		= 'website_directory' ;
			$website_directory->value 		= Input::get('website_directory');
			$website_directory->tool_tip 	= trans('settings.website_directory') ;
			$website_directory->page 		= 2 ;
			$website_directory->save();

		}
		

		if(Input::get('provider_directory')){
			$provider_directory = Settings::where('key', 'provider_directory')->first();

			if(!$provider_directory)
				$provider_directory = new Settings ;


			$provider_directory->key 		= 'provider_directory' ;
			$provider_directory->value 		= Input::get('provider_directory');
			$provider_directory->tool_tip 	= trans('settings.provider_directory') ;
			$provider_directory->page 		= 2 ;
			$provider_directory->save();
		}

		// url

		if(Input::get('website_url')){
			$website_url = Settings::where('key', 'website_url')->first();

			if(!$website_url)
				$website_url = new Settings ;

			$website_url->key 		= 'website_url' ;
			$website_url->value 	= Input::get('website_url');
			$website_url->tool_tip 	= trans('settings.website_url') ;
			$website_url->page 		= 2 ;
			$website_url->save();
		}

		if(Input::get('provider_url')){

			$provider_url = Settings::where('key', 'provider_url')->first();

			if(!$provider_url)
				$provider_url = new Settings ;


			$provider_url->key 			= 'provider_url' ;
			$provider_url->value 		= Input::get('provider_url');
			$provider_url->tool_tip 	= trans('settings.provider_url') ;
			$provider_url->page 		= 2 ;
			$provider_url->save();
		}

		return Redirect::to('/admin/settings/installation?success=1') ;
	}


	public function addcerti() {
		$braintree_cse = $stripe_publishable_key = $url = $timezone = $website_title = $s3_bucket = $twillo_account_sid = $twillo_auth_token = $twillo_number = $default_payment = $stripe_secret_key = $braintree_environment = $braintree_merchant_id = $braintree_public_key = $braintree_private_key = $customer_certy_url = $customer_certy_pass = $customer_certy_type = $provider_certy_url = $provider_certy_pass = $provider_certy_type = $gcm_browser_key = $key_provider = $key_user = $key_taxi = $key_trip = $key_currency = $total_trip = $cancelled_trip = $total_payment = $completed_trip = $card_payment = $credit_payment = $key_ref_pre = $android_client_app_url = $android_provider_app_url = $ios_client_app_url = $ios_provider_app_url = NULL;

		//CONFIGURACOES DE PAGAMENTO

		//modelo de negócios
		$settingBusinessModel = Settings::where('key', 'default_business_model')->first();
		$settingsProviderTransferInterval = Settings::where('key', 'provider_transfer_interval')->first();
		$settingsProviderTransferDay = Settings::where('key', 'provider_transfer_day')->first();

		if(!$settingBusinessModel){
			$settingBusinessModel = new Settings();
			$settingBusinessModel->key = 'default_business_model';
		}

		if(!$settingsProviderTransferInterval){
			$settingsProviderTransferInterval = new Settings();
			$settingsProviderTransferInterval->key = 'provider_transfer_interval';
		}

		if(!$settingsProviderTransferDay){
			$settingsProviderTransferDay = new Settings();
			$settingsProviderTransferDay->key = 'provider_transfer_day';
		}

		//opções de pagamento
		$settingMoney = Settings::where('key', 'payment_money')->first();
		$settingCard = Settings::where('key', 'payment_card')->first();
		$settingVoucher = Settings::where('key', 'payment_voucher')->first();

		if(!$settingMoney){
			$settingMoney = new Settings();
			$settingMoney->key = 'payment_money';
		}
		if(!$settingCard){
			$settingCard = new Settings();
			$settingCard->key = 'payment_card';
		}
		if(!$settingVoucher){
			$settingVoucher = new Settings();
			$settingVoucher->key = 'payment_voucher';
		}

		//intermediador de pagamento
		$default_payment = Config::get('app.default_payment');
		//pagarme
		$pagarme_api_key = Config::get('app.pagarme_api_key');
		$pagarme_encryption_key = Config::get('app.pagarme_encryption_key');

		//stripe
		$stripe_publishable_key = Config::get('app.stripe_publishable_key');
		$stripe_secret_key = Config::get('app.stripe_secret_key');

		//braintree
		$braintree_environment = Config::get('app.braintree_environment');
		$braintree_merchant_id = Config::get('app.braintree_merchant_id');
		$braintree_public_key = Config::get('app.braintree_public_key');
		$braintree_private_key = Config::get('app.braintree_private_key');
		$braintree_cse = Config::get('app.braintree_cse');

		$twillo_account_sid = Config::get('app.twillo_account_sid');
		$twillo_auth_token = Config::get('app.twillo_auth_token');
		$twillo_number = Config::get('app.twillo_number');
		$timezone = Config::get('app.timezone');

		$url = Config::get('app.url');
		$website_title = Config::get('app.website_title');
		$s3_bucket = Config::get('app.s3_bucket');
		
		
		$mail_driver = Config::get('mail.driver');
		$email_name = Config::get('mail.from.name');
		$email_address = Config::get('mail.from.address');
		$mandrill_secret = Config::get('services.mandrill.secret');
		$sendgrid_secret = Config::get('services.sendgrid.secret');

		$host = Config::get('mail.host');
		/* DEVICE PUSH NOTIFICATION DETAILS */
		$customer_certy_url = Config::get('app.customer_certy_url');
		$customer_certy_pass = Config::get('app.customer_certy_pass');
		$customer_certy_type = Config::get('app.customer_certy_type');
		$provider_certy_url = Config::get('app.provider_certy_url');
		$provider_certy_pass = Config::get('app.provider_certy_pass');
		$provider_certy_type = Config::get('app.provider_certy_type');
		$gcm_browser_key = Config::get('app.gcm_browser_key');
		/* DEVICE PUSH NOTIFICATION DETAILS END */
		$install = array(
			'pagarme_api_key' => $pagarme_api_key,
			'pagarme_encryption_key' => $pagarme_encryption_key,
			'braintree_environment' => $braintree_environment,
			'braintree_merchant_id' => $braintree_merchant_id,
			'braintree_public_key' => $braintree_public_key,
			'braintree_private_key' => $braintree_private_key,
			'braintree_cse' => $braintree_cse,
			'twillo_account_sid' => $twillo_account_sid,
			'twillo_auth_token' => $twillo_auth_token,
			'twillo_number' => $twillo_number,
			'stripe_publishable_key' => $stripe_publishable_key,
			'stripe_secret_key' => $stripe_secret_key,
			'mail_driver' => $mail_driver,
			'email_address' => $email_address,
			'email_name' => $email_name,
			'mandrill_secret' => $mandrill_secret,
			'sendgrid_secret' => $sendgrid_secret,
			'default_business_model' => $settingBusinessModel->value,
			'provider_transfer_interval' => $settingsProviderTransferInterval,
			'provider_transfer_day' => $settingsProviderTransferDay,
			'payment_money' => $settingMoney->value,
			'payment_card' => $settingCard->value,
			'payment_voucher' => $settingVoucher->value,
			'default_payment' => $default_payment,
			/* DEVICE PUSH NOTIFICATION DETAILS */
			'customer_certy_url' => $customer_certy_url,
			'customer_certy_pass' => $customer_certy_pass,
			'customer_certy_type' => $customer_certy_type,
			'provider_certy_url' => $provider_certy_url,
			'provider_certy_pass' => $provider_certy_pass,
			'provider_certy_type' => $provider_certy_type,
			'gcm_browser_key' => $gcm_browser_key,
				/* DEVICE PUSH NOTIFICATION DETAILS END */
		);        // Modifying Database Config
		
		$is_certy_change = 0;
		$braintree_environment = Config::get('app.braintree_environment');
		$braintree_merchant_id = Config::get('app.braintree_merchant_id');
		$braintree_public_key = Config::get('app.braintree_public_key');
		$braintree_private_key = Config::get('app.braintree_private_key');
		$braintree_cse = Config::get('app.braintree_cse');
		$twillo_account_sid = Config::get('app.twillo_account_sid');
		$twillo_auth_token = Config::get('app.twillo_auth_token');
		$twillo_number = Config::get('app.twillo_number');
		$timezone = Config::get('app.timezone');
		$stripe_publishable_key = Config::get('app.stripe_publishable_key');
		$url = Config::get('app.url');
		$website_title = Config::get('app.website_title');
		$s3_bucket = Config::get('app.s3_bucket');
		$default_payment = Config::get('app.default_payment');
		$stripe_secret_key = Config::get('app.stripe_secret_key');
		$mail_driver = Config::get('mail.driver');
		$email_name = Config::get('mail.from.name');
		$email_address = Config::get('mail.from.address');
		$mandrill_secret = Config::get('services.mandrill.secret');
		$sendgrid_secret = Config::get('services.sendgrid.secret');

		$host = Config::get('mail.host');
		/* DEVICE PUSH NOTIFICATION DETAILS */
		$customer_certy_url = Config::get('app.customer_certy_url');
		$customer_certy_pass = Config::get('app.customer_certy_pass');
		$customer_certy_type = Config::get('app.customer_certy_type');
		$provider_certy_url = Config::get('app.provider_certy_url');
		$provider_certy_pass = Config::get('app.provider_certy_pass');
		$provider_certy_type = Config::get('app.provider_certy_type');
		$gcm_browser_key = Config::get('app.gcm_browser_key');
		/* DEVICE PUSH NOTIFICATION DETAILS END */
		$install = array(
			'braintree_environment' => $braintree_environment,
			'braintree_merchant_id' => $braintree_merchant_id,
			'braintree_public_key' => $braintree_public_key,
			'braintree_private_key' => $braintree_private_key,
			'braintree_cse' => $braintree_cse,
			'twillo_account_sid' => $twillo_account_sid,
			'twillo_auth_token' => $twillo_auth_token,
			'twillo_number' => $twillo_number,
			'stripe_publishable_key' => $stripe_publishable_key,
			'stripe_secret_key' => $stripe_secret_key,
			'mail_driver' => $mail_driver,
			'email_address' => $email_address,
			'email_name' => $email_name,
			'mandrill_secret' => $mandrill_secret,

			'sendgrid_secret' => $sendgrid_secret,

			'default_payment' => $default_payment,
			/* DEVICE PUSH NOTIFICATION DETAILS */
			'customer_certy_url' => $customer_certy_url,
			'customer_certy_pass' => $customer_certy_pass,
			'customer_certy_type' => $customer_certy_type,
			'provider_certy_url' => $provider_certy_url,
			'provider_certy_pass' => $provider_certy_pass,
			'provider_certy_type' => $provider_certy_type,
			'gcm_browser_key' => $gcm_browser_key,
				/* DEVICE PUSH NOTIFICATION DETAILS END */
		);
		$count = 0;

		// apple user
		if (Input::hasFile('user_certi_a') && Input::has('user_pass_a') && Input::has('cert_type_a')) {
			// Upload File
			$certy_password_client = $customer_certy_pass = trim(Input::get('user_pass_a'));
			$customer_certy_type = Input::get('cert_type_a');
			if ($customer_certy_type) {
				$client_certy_type = "ssl";
			} else {
				$client_certy_type = "sandboxSsl";
			}
			$file_name = "Client_certy";
			$ext = Input::file('user_certi_a')->getClientOriginalExtension();
			if ($ext == "PEM" || $ext == "pem") {
				/* Input::file('user_certi_a')->move(app_path() . "/ios_push/iph_cert/", $file_name . "." . $ext); */
				Input::file('user_certi_a')->move(public_path() . "/apps/ios_push/iph_cert", $file_name . "." . $ext);

				/* chmod(app_path() . "/ios_push/iph_cert/" . $file_name . "." . $ext, 0777); */

				$local_url = $file_name . "." . $ext;

				// Upload to S3
				if (Config::get('app.s3_bucket') != "") {
					$s3 = App::make('aws')->get('s3');
					$pic = $s3->putObject(array(
						'Bucket' => Config::get('app.s3_bucket'),
						'Key' => $file_name,
						'SourceFile' => app_path() . "/ios_push/iph_cert/" . $local_url,
					));

					$s3->putObjectAcl(array(
						'Bucket' => Config::get('app.s3_bucket'),
						'Key' => $file_name,
						'ACL' => 'public-read'
					));

					$customer_certy_url = $s3->getObjectUrl(Config::get('app.s3_bucket'), $file_name);
				} else {
					/* $customer_certy_url = app_path() . '/ios_push/iph_cert/' . $local_url; */
					/* $customer_certy_url = app_path() . "/ios_push/iph_cert/" . $local_url; */
				}
				/* $customer_certy_url = public_path() . "/apps/ios_push/iph_cert" . $local_url; */
				$customer_certy_url = asset_url() . '/apps/ios_push/iph_cert/' . $local_url;
				/* if (isset($theme->logo)) {
				  $icon = asset_url() . '/uploads/' . $theme->logo;
				  unlink_image($icon);
				  }
				  $theme->logo = $local_url; */
				$update_client_certy = "<?php

//session_start();

//require_once  'database.php';
//error_reporting(false);

class ClientApns {

	public \$ctx;
	public \$fp;
	private \$ssl = 'ssl://gateway.push.apple.com:2195';
	private \$passphrase = '" . $certy_password_client . "';
	private \$sandboxCertificate = 'iph_cert/" . $local_url . "';
	private \$sandboxSsl = 'ssl://gateway.sandbox.push.apple.com:2195';
	private \$sandboxFeedback = 'ssl://feedback.sandbox.push.apple.com:2196';
	private \$message = 'ManagerMaster';

	public function __construct() {
		\$this->initialize_apns();
	}

	private function getCertificatePath() {
		/*return app_path() . '/ios_push/' . \$this->sandboxCertificate;*/
		return public_path().'/apps/ios_push/'.\$this->sandboxCertificate;
	}
	
	public function initialize_apns() {
		try {
			\$this->ctx = stream_context_create();

			//stream_context_set_option(\$ctx, 'ssl', 'cafile', 'entrust_2048_ca.cer');
			stream_context_set_option(\$this->ctx, 'ssl', 'local_cert', \$this->getCertificatePath());
			stream_context_set_option(\$this->ctx, 'ssl', 'passphrase', \$this->passphrase); // use this if you are using a passphrase
			// Open a connection to the APNS servers
			\$this->fp = @stream_socket_client(\$this->" . $client_certy_type . ", \$err, \$errstr, 60, STREAM_CLIENT_CONNECT | STREAM_CLIENT_PERSISTENT, \$this->ctx);

			if (\$this->fp) {
				//Log::info('Successfully connected to server of APNS');
				//echo 'Successfully connected to server of APNS ckUberForXUser.pem';
			} else {
				//Log::error('Error in connection while trying to connect to APNS');
				//echo 'Error in connection while trying to connect to APNS ckUberForXUser.pem';
			}
		} catch (Exception \$e) {
			//Log::error(\$e);
		}
	}

	public function send_notification(\$devices, \$message) {
		try {
			\$errCounter = 0;
			\$payload = json_encode(array('aps' => \$message));
			\$result = 0;
			\$bodyError = '';
			foreach (\$devices as \$key => \$value) {
				\$msg = chr(0) . pack('n', 32) . pack('H*', str_replace(' ', '', \$value)) . pack('n', (strlen(\$payload))) . \$payload;
				\$result = fwrite(\$this->fp, \$msg);
				\$bodyError .= 'result: ' . \$result . ', devicetoken: ' . \$value;
				if (!\$result) {
					\$errCounter = \$errCounter + 1;
				}
			}
			//echo 'Result :- '.\$result;
			if (\$result) {
				//Log::info('Delivered Message to APNS' . PHP_EOL);
				//echo 'Delivered Message to APNS' . PHP_EOL;
				\$bool_result = true;
			} else {
				//Log::info('Could not Deliver Message to APNS' . PHP_EOL);
				//echo 'Could not Deliver Message to APNS' . PHP_EOL;
				\$bool_result = false;
			}

			@socket_close(\$this->fp);
			@fclose(\$this->fp);
			return \$bool_result;
		} catch (Exception \$e) {
			//Log::error(\$e);
		}
	}

}
";
				$t = file_put_contents(app_path() . '/ios_push/apns.php', $update_client_certy);
				/* chmod(app_path() . '/ios_push/apns.php', 0777); */
				$is_certy_change ++;
			} else {
				return Redirect::to('/admin/settings/installation?success=3')
								->with('install', $install);
			}
		}
		if (Input::hasFile('prov_certi_a') && Input::has('prov_pass_a') && Input::has('cert_type_a')) {
			$certy_password_driver = $provider_certy_pass = trim(Input::get('prov_pass_a'));

			$provider_certy_type = Input::get('cert_type_a');
			if ($provider_certy_type) {
				$driver_certy_type = "ssl";
			} else {
				$driver_certy_type = "sandboxSsl";
			}
			// Upload File
			$file_name = "Provider_certy";
			$ext = Input::file('prov_certi_a')->getClientOriginalExtension();
			if ($ext == "PEM" || $ext == "pem") {
				/* Input::file('prov_certi_a')->move(app_path() . "/ios_push/provider/iph_cert/", $file_name . "." . $ext); */
				Input::file('prov_certi_a')->move(public_path() . "/apps/ios_push/provider/iph_cert", $file_name . "." . $ext);

				$local_url = $file_name . "." . $ext;

				/* chmod(app_path() . "/ios_push/provider/iph_cert/" . $file_name . "." . $ext, 0777); */

				// Upload to S3
				if (Config::get('app.s3_bucket') != "") {
					$s3 = App::make('aws')->get('s3');
					$pic = $s3->putObject(array(
						'Bucket' => Config::get('app.s3_bucket'),
						'Key' => $file_name,
						'SourceFile' => app_path() . "/ios_push/provider/iph_cert/" . $local_url,
					));

					$s3->putObjectAcl(array(
						'Bucket' => Config::get('app.s3_bucket'),
						'Key' => $file_name,
						'ACL' => 'public-read'
					));

					$provider_certy_url = $s3->getObjectUrl(Config::get('app.s3_bucket'), $file_name);
				} else {
					/* $provider_certy_url = app_path() . '/ios_push/provider/iph_cert/' . $local_url; */
					/* $provider_certy_url = app_path() . "/ios_push/provider/iph_cert/" . $local_url; */
				}
				$provider_certy_url = asset_url() . '/apps/ios_push/provider/iph_cert/' . $local_url;
				/* if (isset($theme->logo)) {
				  $icon = asset_url() . '/uploads/' . $theme->logo;
				  unlink_image($icon);
				  }
				  $theme->logo = $local_url; */
				$update_client_certy = "<?php

//session_start();

//require_once  'database.php';
//error_reporting(false);

class ProviderApns {

	public \$ctx;
	public \$fp;
	private \$ssl = 'ssl://gateway.push.apple.com:2195';
	private \$passphrase = '" . $certy_password_driver . "';
	private \$sandboxCertificate = 'provider/iph_cert/" . $local_url . "';
	private \$sandboxSsl = 'ssl://gateway.sandbox.push.apple.com:2195';
	private \$sandboxFeedback = 'ssl://feedback.sandbox.push.apple.com:2196';
	private \$message = 'ManagerMaster';

	public function __construct() {
		\$this->initialize_apns();
	}

	private function getCertificatePath() {
		/*return app_path() . '/ios_push/' . \$this->sandboxCertificate;*/
		return public_path().'/apps/ios_push/'.\$this->sandboxCertificate;
	}
	
	public function initialize_apns() {
		try {
			\$this->ctx = stream_context_create();

			//stream_context_set_option(\$ctx, 'ssl', 'cafile', 'entrust_2048_ca.cer');
			stream_context_set_option(\$this->ctx, 'ssl', 'local_cert', \$this->getCertificatePath());
			stream_context_set_option(\$this->ctx, 'ssl', 'passphrase', \$this->passphrase); // use this if you are using a passphrase
			// Open a connection to the APNS servers
			\$this->fp = @stream_socket_client(\$this->" . $driver_certy_type . ", \$err, \$errstr, 60, STREAM_CLIENT_CONNECT | STREAM_CLIENT_PERSISTENT, \$this->ctx);

			if (\$this->fp) {
				//Log::info('Successfully connected to server of APNS');
				/*echo 'Successfully connected to server of APNS ckUberForXProvider.pem';*/
			} else {
				//Log::error('Error in connection while trying to connect to APNS');
				/*echo 'Error in connection while trying to connect to APNS ckUberForXProvider.pem';*/
			}
		} catch (Exception \$e) {
			//Log::error(\$e);
		}
	}

	public function send_notification(\$devices, \$message) {
		try {
			\$errCounter = 0;
			\$payload = json_encode(array('aps' => \$message));
			\$result = 0;
			\$bodyError = '';
			/*print_r(\$devices);*/
			foreach (\$devices as \$key => \$value) {
				/*echo \$value;*/
				\$msg = chr(0) . pack('n', 32) . pack('H*', str_replace(' ', '', \$value)) . pack('n', (strlen(\$payload))) . \$payload;
				\$result = fwrite(\$this->fp, \$msg);
				\$bodyError .= 'result: ' . \$result . ', devicetoken: ' . \$value;
				if (!\$result) {
					\$errCounter = \$errCounter + 1;
				}
			}
			/*echo 'Result :- '.\$result;*/
			if (\$result) {
				//Log::info('Delivered Message to APNS' . PHP_EOL);
				/*echo 'Delivered Message to APNS' . PHP_EOL;*/
				\$bool_result = true;
			} else {
				//Log::info('Could not Deliver Message to APNS' . PHP_EOL);
				/*echo 'Could not Deliver Message to APNS' . PHP_EOL;*/
				\$bool_result = false;
			}

			@socket_close(\$this->fp);
			@fclose(\$this->fp);
			return \$bool_result;
		} catch (Exception \$e) {
			//Log::error(\$e);
		}
	}

}
";
				$t = file_put_contents(app_path() . '/ios_push/provider/apns.php', $update_client_certy);
				/* chmod(app_path() . '/ios_push/provider/apns.php', 0777); */
				$is_certy_change ++;
			} else {
				return Redirect::to('/admin/settings/installation?success=3')
								->with('install', $install);
			}
		}
		if (Input::has('gcm_key')) {
			/* "AIzaSyAKe3XmUV93WvHJvII4Qzpf0R052mxb0KI" */
			$app_gcm_key = $gcm_browser_key = trim(Input::get('gcm_key'));
			if ($app_gcm_key != "") {
				$update_client_certy = "<?php

/*array(
	'GOOGLE_API_KEY' => '" . $app_gcm_key . "',
);*/
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of GCM
 *
 * @author Ravi Tamada
 */
define('GOOGLE_API_KEY', '" . $app_gcm_key . "');
/*define('GOOGLE_API_KEY', 'AIzaSyAKe3XmUV93WvHJvII4Qzpf0R052mxb0KI');*/
/*define('GOOGLE_API_KEY', 'AIzaSyC0JjF-O72-gUvUmUm_dsHHvG5o3aWosp8');*/

class GCM {

	//put your code here
	// constructor
	function __construct() {
		
	}

	/**
	 * Sending Push Notification
	 */
	public function send_notification(\$registatoin_ids, \$message) {
		// include config
		//include_once 'const.php';
		/* include_once 'config.php'; */
		// Set POST variables
		\$url = 'https://android.googleapis.com/gcm/send';

		\$fields = array(
			'registration_ids' => \$registatoin_ids,
			'data' => \$message,
		);

		\$headers = array(
			'Authorization: key=' . GOOGLE_API_KEY,
			'Content-Type: application/json'
		);
		// Open connection
		\$ch = curl_init();

		// Set the url, number of POST vars, POST data
		curl_setopt(\$ch, CURLOPT_URL, \$url);

		curl_setopt(\$ch, CURLOPT_POST, true);
		curl_setopt(\$ch, CURLOPT_HTTPHEADER, \$headers);
		curl_setopt(\$ch, CURLOPT_RETURNTRANSFER, true);

		// Disabling SSL Certificate support temporarly
		curl_setopt(\$ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt(\$ch, CURLOPT_POSTFIELDS, json_encode(\$fields));

		// Execute post
		\$result = curl_exec(\$ch);
		if (\$result === FALSE) {
			//die('Curl failed: ' . curl_error(\$ch));
			//Log::error('Curl failed: ' . curl_error(\$ch));
		}
		else{
			//echo \$result;
			//Log::error(\$result);
		}

		// Close connection
		/*curl_close(\$ch);
		 echo \$result/*.'\n\n'.json_encode(\$fields); */
	}

}
?>
";
				$t = file_put_contents(app_path() . '/gcm/GCM_1.php', $update_client_certy);
				$is_certy_change ++;
			} else {
				return Redirect::to('/admin/settings/installation?success=4')
								->with('install', $install);
			}
		}
		/* if (Input::hasFile('user_certi_a')) {
		  $certi_user_a = Certificates::where('client', 'apple')->where('user_type', 0)->where('file_type', 'certificate')->where('type', Input::get('cert_type_a'))->first();
		  if ($certi_user_a != NULL) {
		  //user
		  $path = $certi_user_a->name;
		  //Log::info($path);
		  $filename = basename($path);
		  //Log::info($filename);
		  if (file_exists($path)) {
		  try {
		  unlink(public_path() . "/apps/ios_push/iph_cert/" . $filename);
		  } catch (Exception $e) {

		  }
		  }
		  $key = Certificates::where('client', 'apple')->where('user_type', 0)->where('file_type', 'certificate')->first();
		  } else {
		  $key = new Certificates();
		  $key->client = 'apple';
		  $key->type = Input::get('cert_type_a');
		  $key->user_type = 0;
		  $key->file_type = 'certificate';
		  }
		  // upload image
		  $file_name = time();
		  $file_name .= rand();
		  $file_name = sha1($file_name);

		  //Log::info(Input::file('user_certi_a'));

		  $ext = Input::file('user_certi_a')->getClientOriginalExtension();
		  Input::file('user_certi_a')->move(public_path() . "/apps/ios_push/iph_cert", $file_name . "." . $ext);
		  $local_url = $file_name . "." . $ext;

		  // Upload to S3
		  if (Config::get('app.s3_bucket') != "") {
		  $s3 = App::make('aws')->get('s3');
		  $pic = $s3->putObject(array(
		  'Bucket' => Config::get('app.s3_bucket'),
		  'Key' => $file_name,
		  'SourceFile' => public_path() . "/apps/ios_push/iph_cert/" . $local_url,
		  ));
		  $s3->putObjectAcl(array(
		  'Bucket' => Config::get('app.s3_bucket'),
		  'Key' => $file_name,
		  'ACL' => 'public-read'
		  ));
		  $s3_url = $s3->getObjectUrl(Config::get('app.s3_bucket'), $file_name);
		  }
		  //Log::info('path = ' . print_r($local_url, true));
		  $key->name = $local_url;
		  $count = $count + 1;
		  $key->save();
		  }

		  // User passphrase file.
		  if (Input::has('user_pass_a')) {
		  $user_key_db = Certificates::where('client', 'apple')->where('user_type', 0)->where('file_type', 'passphrase')->where('type', Input::get('cert_type_a'))->first();
		  if ($user_key_db == NULL) {
		  $key = new Certificates();
		  $key->client = 'apple';
		  $key->type = Input::get('cert_type_a');
		  $key->user_type = 0;
		  $key->file_type = 'passphrase';
		  } else {
		  $key = Certificates::where('client', 'apple')->where('user_type', 0)->where('file_type', 'passphrase')->first();
		  }
		  $key->name = Input::get('user_pass_a');
		  $count = $count + 1;
		  $key->save();
		  }

		  // apple provider
		  if (Input::hasFile('prov_certi_a')) {
		  $certi_prov_a = Certificates::where('client', 'apple')->where('user_type', 1)->where('file_type', 'certificate')->where('type', Input::get('cert_type_a'))->first();
		  if ($certi_prov_a != NULL) {
		  //user
		  $path = $certi_prov_a->name;
		  //Log::info($path);
		  $filename = basename($path);
		  //Log::info($filename);
		  try {
		  unlink(public_path() . "/apps/ios_push/provider/iph_cert/" . $filename);
		  } catch (Exception $e) {

		  }
		  $key = Certificates::where('client', 'apple')->where('user_type', 1)->where('file_type', 'certificate')->first();
		  } else {
		  $key = new Certificates();
		  $key->client = 'apple';
		  $key->type = Input::get('cert_type_a');
		  $key->user_type = 1;
		  $key->file_type = 'certificate';
		  }
		  // upload image
		  $file_name = time();
		  $file_name .= rand();
		  $file_name = sha1($file_name);

		  $ext = Input::file('prov_certi_a')->getClientOriginalExtension();
		  Input::file('prov_certi_a')->move(public_path() . "/apps/ios_push/provider/iph_cert", $file_name . "." . $ext);
		  $local_url = $file_name . "." . $ext;

		  // Upload to S3
		  if (Config::get('app.s3_bucket') != "") {
		  $s3 = App::make('aws')->get('s3');
		  $pic = $s3->putObject(array(
		  'Bucket' => Config::get('app.s3_bucket'),
		  'Key' => $file_name,
		  'SourceFile' => public_path() . "/apps/ios_push/provider/iph_cert/" . $local_url,
		  ));
		  $s3->putObjectAcl(array(
		  'Bucket' => Config::get('app.s3_bucket'),
		  'Key' => $file_name,
		  'ACL' => 'public-read'
		  ));
		  }
		  //Log::info('path = ' . print_r($local_url, true));
		  $key->name = $local_url;
		  $count = $count + 1;
		  $key->save();
		  }

		  // Provider passphrase file.
		  if (Input::has('prov_pass_a')) {
		  $user_key_db = Certificates::where('client', 'apple')->where('user_type', 1)->where('file_type', 'passphrase')->where('type', Input::get('cert_type_a'))->first();
		  if ($user_key_db == NULL) {
		  $key = new Certificates();
		  $key->client = 'apple';
		  $key->type = Input::get('cert_type_a');
		  $key->user_type = 1;
		  $key->file_type = 'passphrase';
		  } else {
		  $key = Certificates::where('client', 'apple')->where('user_type', 1)->where('file_type', 'passphrase')->first();
		  }
		  $key->name = Input::get('prov_pass_a');
		  $count = $count + 1;
		  $key->save();
		  }

		  // gcm key file.
		  if (Input::has('gcm_key')) {
		  $gcm_key_db = Certificates::where('client', 'gcm')->first();
		  if ($gcm_key_db == NULL) {
		  $key = new Certificates();
		  $key->client = 'gcm';
		  $key->type = Input::get('cert_type_a');
		  $key->user_type = 0;
		  $key->file_type = 'browser_key';
		  } else {
		  $key = Certificates::where('client', 'gcm')->first();
		  }
		  $key->name = Input::get('gcm_key');
		  $count = $count + 1;
		  $key->save();
		  }

		  //Log::info("count = " . print_r($count, true));

		  $cert_def = Input::get('cert_default');
		  $certa = Certificates::where('client', 'apple')->get();
		  foreach ($certa as $ca) {
		  $def = Certificates::where('id', $ca->id)->first();
		  $def->default = 0;
		  $def->save();
		  }
		  $certs = Certificates::where('client', 'apple')->where('type', $cert_def)->get();
		  foreach ($certs as $defc) {
		  $def = Certificates::where('id', $defc->id)->first();
		  //Log::info('def = ' . print_r($def, true));
		  $def->default = 1;
		  $def->save();
		  } */
		$android_client_app_url = NULL;
		if (Input::has('android_client_app_url')) {
			$android_client_app_url = Input::get('android_client_app_url');
		}
		$android_provider_app_url = NULL;
		if (Input::has('android_provider_app_url')) {
			$android_provider_app_url = Input::get('android_provider_app_url');
		}
		$ios_client_app_url = NULL;
		if (Input::has('ios_client_app_url')) {
			$ios_client_app_url = Input::get('ios_client_app_url');
		}
		$ios_provider_app_url = NULL;
		if (Input::has('ios_provider_app_url')) {
			$ios_provider_app_url = Input::get('ios_provider_app_url');
		}
		$appfile = fopen(app_path() . "/config/app.php", "w") or die(trans('adminController.not_open_file'));
		/* $appfile_config = generate_app_config($braintree_cse, $stripe_publishable_key, $url, $timezone, $website_title, $s3_bucket, $twillo_account_sid, $twillo_auth_token, $twillo_number, $default_payment, $stripe_secret_key, $braintree_environment, $braintree_merchant_id, $braintree_public_key, $braintree_private_key, $customer_certy_url, $customer_certy_pass, $customer_certy_type, $provider_certy_url, $provider_certy_pass, $provider_certy_type, $gcm_browser_key, $android_client_app_url, $android_provider_app_url, $ios_client_app_url, $ios_provider_app_url); */

		$appfile_config = generate_app_config($pagarme_api_key, $pagarme_encryption_key, $braintree_cse, $stripe_publishable_key, $url, $timezone, $website_title, $s3_bucket, $twillo_account_sid, $twillo_auth_token, $twillo_number, $default_payment, $stripe_secret_key, $braintree_environment, $braintree_merchant_id, $braintree_public_key, $braintree_private_key, $customer_certy_url, $customer_certy_pass, $customer_certy_type, $provider_certy_url, $provider_certy_pass, $provider_certy_type, $gcm_browser_key, $key_provider, $key_user, $key_taxi, $key_trip, $key_currency, $total_trip, $cancelled_trip, $total_payment, $completed_trip, $card_payment, $credit_payment, $key_ref_pre, $android_client_app_url, $android_provider_app_url, $ios_client_app_url, $ios_provider_app_url);
		fwrite($appfile, $appfile_config);
		fclose($appfile);

		$install = array(
			'braintree_environment' => $braintree_environment,
			'braintree_merchant_id' => $braintree_merchant_id,
			'braintree_public_key' => $braintree_public_key,
			'braintree_private_key' => $braintree_private_key,
			'braintree_cse' => $braintree_cse,
			'twillo_account_sid' => $twillo_account_sid,
			'twillo_auth_token' => $twillo_auth_token,
			'twillo_number' => $twillo_number,
			'stripe_publishable_key' => $stripe_publishable_key,
			'stripe_secret_key' => $stripe_secret_key,
			'mail_driver' => $mail_driver,
			'email_address' => $email_address,
			'email_name' => $email_name,
			'mandrill_secret' => $mandrill_secret,
			'sendgrid_secret' => $sendgrid_secret, 
			'default_payment' => $default_payment,
			/* DEVICE PUSH NOTIFICATION DETAILS */
			'customer_certy_url' => $customer_certy_url,
			'customer_certy_pass' => $customer_certy_pass,
			'customer_certy_type' => $customer_certy_type,
			'provider_certy_url' => $provider_certy_url,
			'provider_certy_pass' => $provider_certy_pass,
			'provider_certy_type' => $provider_certy_type,
			'gcm_browser_key' => $gcm_browser_key,
				/* DEVICE PUSH NOTIFICATION DETAILS END */
		);
		/* echo asset_url();
		  echo "<br>";
		  echo $provider_certy_url;
		  echo $customer_certy_url; */
		if (Input::has('maps_key')) {
			$maps_key = Input::get('maps_key');
			$settings_maps = Settings::where('key', 'google_maps_api_key')->first();
			$settings_maps->value = $maps_key;
			$settings_maps->save();
		}

		if ($is_certy_change > 0) {
			return Redirect::to('/admin/settings/installation?success=1');
		} else {
			return Redirect::to('/admin/settings/installation?success=5')
							->with('install', $install);
		}
	}

	//Sort Users
	public function sortur() {
		$valu = $_GET['valu'];
		$type = $_GET['type'];
		Session::put('valu', $valu);
		Session::put('type', $type);
		if ($type == 'userid') {
			$typename = trans('adminController.user_name');
			$users = User::orderBy('id', $valu)->paginate(10);
		} elseif ($type == 'username') {
			$typename = trans('adminController.user_name');
			$users = User::orderBy('first_name', $valu)->paginate(10);
		} elseif ($type == 'useremail') {
			$typename = trans('adminController.provider_mail');
			$users = User::orderBy('email', $valu)->paginate(10);
		}
		$title = ucwords(trans('customize.User'). " | " . trans('adminController.sort_by') . " " . $typename . " " . trans('adminController.in_sort') . " " . $valu); /* 'Users | Sorted by ' . $typename . ' in ' . $valu */
		return View::make('users')
						->with('title', $title)
						->with('page', 'users')
						->with('users', $users);
	}

	public function sortpv() {
		$valu = $_GET['valu'];
		$type = $_GET['type'];
		Session::put('valu', $valu);
		Session::put('type', $type);
		if ($type == 'provid') {
			$typename = trans('adminController.provider_id');
			/* $providers = Provider::orderBy('id', $valu)->paginate(10); */
			$subQuery = DB::table('request_meta')
					->select(DB::raw('count(*)'))
					->whereRaw('provider_id = provider.id and status != 0');
			$subQuery1 = DB::table('request_meta')
					->select(DB::raw('count(*)'))
					->whereRaw('provider_id = provider.id and status=1');

			$providers = DB::table('provider')
					->select('provider.*', DB::raw("(" . $subQuery->toSql() . ") as 'total_requests'"), DB::raw("(" . $subQuery1->toSql() . ") as 'accepted_requests'"))->where('deleted_at', NULL)
					/* ->where('provider.is_deleted', 0) */
					->orderBy('provider.id', $valu)
					->paginate(10);
		} elseif ($type == 'pvname') {
			$typename = trans('adminController.provider_name');
			/* $providers = Provider::orderBy('first_name', $valu)->paginate(10); */
			$subQuery = DB::table('request_meta')
					->select(DB::raw('count(*)'))
					->whereRaw('provider_id = provider.id and status != 0');
			$subQuery1 = DB::table('request_meta')
					->select(DB::raw('count(*)'))
					->whereRaw('provider_id = provider.id and status=1');

			$providers = DB::table('provider')
					->select('provider.*', DB::raw("(" . $subQuery->toSql() . ") as 'total_requests'"), DB::raw("(" . $subQuery1->toSql() . ") as 'accepted_requests'"))->where('deleted_at', NULL)
					/* ->where('provider.is_deleted', 0) */
					->orderBy('provider.first_name', $valu)
					->paginate(10);
		} elseif ($type == 'pvemail') {
			$typename = trans('adminController.provider_mail');
			/* $providers = Provider::orderBy('email', $valu)->paginate(10); */
			$subQuery = DB::table('request_meta')
					->select(DB::raw('count(*)'))
					->whereRaw('provider_id = provider.id and status != 0');
			$subQuery1 = DB::table('request_meta')
					->select(DB::raw('count(*)'))
					->whereRaw('provider_id = provider.id and status=1');

			$providers = DB::table('provider')
					->select('provider.*', DB::raw("(" . $subQuery->toSql() . ") as 'total_requests'"), DB::raw("(" . $subQuery1->toSql() . ") as 'accepted_requests'"))->where('deleted_at', NULL)
					/* ->where('provider.is_deleted', 0) */
					->orderBy('provider.email', $valu)
					->paginate(10);
		} elseif ($type == 'pvaddress') {
			$typename = trans('adminController.provider_address');
			/* $providers = Provider::orderBy('address', $valu)->paginate(10); */
			$subQuery = DB::table('request_meta')
					->select(DB::raw('count(*)'))
					->whereRaw('provider_id = provider.id and status != 0');
			$subQuery1 = DB::table('request_meta')
					->select(DB::raw('count(*)'))
					->whereRaw('provider_id = provider.id and status=1');

			$providers = DB::table('provider')
					->select('provider.*', DB::raw("(" . $subQuery->toSql() . ") as 'total_requests'"), DB::raw("(" . $subQuery1->toSql() . ") as 'accepted_requests'"))->where('deleted_at', NULL)
					/* ->where('provider.is_deleted', 0) */
					->orderBy('provider.address', $valu)
					->paginate(10);
		}
		$title = ucwords(trans('customize.Provider') . " | " . trans('adminController.sort_by') . " " . $typename . " " . trans('adminController.in_sort') . " " . $valu); /* 'Providers | Sorted by ' . $typename . ' in ' . $valu */
		return View::make('providers')
						->with('title', $title)
						->with('page', 'providers')
						->with('providers', $providers);
	}

	public function sortpvtype() {
		$valu = $_GET['valu'];
		$type = $_GET['type'];
		Session::put('valu', $valu);
		Session::put('type', $type);
		if ($type == 'provid') {
			$typename = trans('adminController.provider_id_type');
			$providers = ProviderType::orderBy('id', $valu)->paginate(10);
		} elseif ($type == 'pvname') {
			$typename = trans('adminController.provider_name');
			$providers = ProviderType::orderBy('name', $valu)->paginate(10);
		}
		$settings = Settings::where('key', 'default_distance_unit')->first();
		$unit = $settings->value;
		if ($unit == 0) {
			$unit_set = trans('setting.km');
		} elseif ($unit == 1) {
			$unit_set = trans('setting.miles');
		}
		$title = ucwords("Tipo de " . trans('customize.Provider') . " | " . trans('adminController.sort_by') . " " . $typename . " " . trans('adminController.in_sort') . " " . $valu); /* 'Provider Types | Sorted by ' . $typename . ' in ' . $valu */
		return View::make('list_provider_types')
						->with('title', $title)
						->with('page', 'provider-type')
						->with('unit_set', $unit_set)
						->with('types', $providers);
	}

	public function sortreq() {
		$valu = $_GET["valu"];
		$type = $_GET["type"];
		Session::put('valu', $valu);
		Session::put('type', $type);
		if ($type == 'reqid') {
			$typename = trans('adminController.id');
			$requests = DB::table('request')
					->leftJoin('user', 'request.user_id', '=', 'user.id')
					->leftJoin('provider', 'request.current_provider', '=', 'provider.id')
					->groupBy('request.id')
					->select('user.first_name as user_first_name', 'user.last_name as user_last_name', 'provider.first_name as provider_first_name', 'provider.last_name as provider_last_name', 'user.id as user_id', 'provider.id as provider_id', 'request.id as id', 'request.created_at as date', 'request.is_started', 'request.is_provider_arrived', 'request.is_completed', 'request.is_paid', 'request.is_provider_started', 'request.confirmed_provider'
							, 'request.status', 'request.time', 'request.distance', 'request.total', 'request.is_cancelled', 'request.transfer_amount', 'request.payment_mode')
					->orderBy('request.id', $valu)
					->paginate(10);
		} elseif ($type == 'user') {
			$typename = trans('adminController.user_name');
			$requests = DB::table('request')
					->leftJoin('user', 'request.user_id', '=', 'user.id')
					->leftJoin('provider', 'request.current_provider', '=', 'provider.id')
					->groupBy('request.id')
					->select('user.first_name as user_first_name', 'user.last_name as user_last_name', 'provider.first_name as provider_first_name', 'provider.last_name as provider_last_name', 'user.id as user_id', 'provider.id as provider_id', 'request.id as id', 'request.created_at as date', 'request.is_started', 'request.is_provider_arrived', 'request.is_completed', 'request.is_paid', 'request.is_provider_started', 'request.confirmed_provider'
							, 'request.status', 'request.time', 'request.distance', 'request.total', 'request.is_cancelled', 'request.transfer_amount', 'request.payment_mode')
					->orderBy('user.first_name', $valu)
					->paginate(10);
		} elseif ($type == 'provider') {
			$typename = trans('adminController.provider_name');
			$requests = DB::table('request')
					->leftJoin('provider', 'request.current_provider', '=', 'provider.id')
					->leftJoin('user', 'request.user_id', '=', 'user.id')
					->groupBy('request.id')
					->select('user.first_name as user_first_name', 'user.last_name as user_last_name', 'provider.first_name as provider_first_name', 'provider.last_name as provider_last_name', 'user.id as user_id', 'provider.id as provider_id', 'request.id as id', 'request.created_at as date', 'request.is_started', 'request.is_provider_arrived', 'request.is_completed', 'request.is_paid', 'request.is_provider_started', 'request.confirmed_provider'
							, 'request.status', 'request.time', 'request.distance', 'request.total', 'request.is_cancelled', 'request.transfer_amount', 'request.payment_mode')
					->orderBy('provider.first_name', $valu)
					->paginate(10);
		} elseif ($type == 'payment') {
			$typename = trans('adminController.pay_mode');
			$requests = DB::table('request')
					->leftJoin('provider', 'request.current_provider', '=', 'provider.id')
					->leftJoin('user', 'request.user_id', '=', 'user.id')
					->groupBy('request.id')
					->select('user.first_name as user_first_name', 'user.last_name as user_last_name', 'provider.first_name as provider_first_name', 'provider.last_name as provider_last_name', 'user.id as user_id', 'provider.id as provider_id', 'request.id as id', 'request.created_at as date', 'request.is_started', 'request.is_provider_arrived', 'request.is_completed', 'request.is_paid', 'request.is_provider_started', 'request.confirmed_provider'
							, 'request.status', 'request.time', 'request.distance', 'request.total', 'request.is_cancelled', 'request.transfer_amount', 'request.payment_mode')
					->orderBy('request.payment_mode', $valu)
					->paginate(10);
		}
		$setting = Settings::where('key', 'paypal')->first();
		$title = ucwords(trans('customize.Request') . " | " . trans('adminController.sort_by') . " " . $typename . " " . trans('adminController.in_sort') . " " . $valu); /* 'Requests | Sorted by ' . $typename . ' in ' . $valu */
		return View::make('requests')
						->with('title', $title)
						->with('page', 'requests')
						->with('requests', $requests)
						->with('setting', $setting);
	}

	public function sortpromo() {
		$valu = $_GET["valu"];
		$type = $_GET["type"];
		$success = Input::get('success');
		Session::put('valu', $valu);
		Session::put('type', $type);
		if ($type == 'promoid') {
			$typename = trans('adminController.promo_id');
			$promo_codes = DB::table('promo_codes')
					->orderBy('id', $valu)
					->paginate(10);
		} elseif ($type == 'promo') {
			$typename = trans('adminController.promo_code');
			$promo_codes = DB::table('promo_codes')
					->orderBy('coupon_code', $valu)
					->paginate(10);
		} elseif ($type == 'uses') {
			$typename = trans('adminController.no_of_use');
			$promo_codes = DB::table('promo_codes')
					->orderBy('uses', $valu)
					->paginate(10);
		}
		$setting = Settings::where('key', 'paypal')->first();
		$title = ucwords(trans('customize.promo_codes') . " | " . trans('adminController.sort_by') . " " . $typename . " " . trans('adminController.in_sort') . " " . $valu); /* 'Promocodes | Sorted by ' . $typename . ' in ' . $valu */
		return View::make('list_promo_codes')
						->with('title', $title)
						->with('page', 'promo_code')
						->with('success', $success)
						->with('promo_codes', $promo_codes)
						->with('setting', $setting);
	}

	public function searchpromo() {
		$valu = $_GET['valu'];
		$type = $_GET['type'];
		$success = Input::get('success');
		Session::put('valu', $valu);
		Session::put('type', $type);
		if ($type == 'promo_id') {
			$promo_codes = PromoCodes::where('id', $valu)->paginate(10);
		} elseif ($type == 'promo_name') {
			$promo_codes = PromoCodes::where('coupon_code', 'like', '%' . $valu . '%')->paginate(10);
		} elseif ($type == 'promo_type') {
			if((strcasecmp($valu, '%') == 0) || (strcasecmp($valu, trans('adminController.percentage')) == 0)){
				$promo_codes = PromoCodes::where('type', 1)->paginate(10);
			} elseif ((strcasecmp($valu, '$') == 0) || (strcasecmp($valu, trans('adminController.absolute')) == 0)) {
				$promo_codes = PromoCodes::where('type', 2)->paginate(10);
			}
		} elseif ($type == 'promo_state') {
			if (strcasecmp($valu, trans('adminController.active')) == 0) {
				$promo_codes = PromoCodes::where('state', 1)->paginate(10);
			} elseif (strcasecmp($valu, trans('adminController.Deactivated')) == 0) {
				$promo_codes = PromoCodes::where('state', 2)->paginate(10);
			}
		}
		$title = ucwords(trans('customize.promo_codes') . " | " . trans('adminController.search_result')); /* 'Promo Codes | Search Result' */
		return View::make('list_promo_codes')
						->with('title', $title)
						->with('page', 'promo_code')
						->with('success', $success)
						->with('promo_codes', $promo_codes);
	}


	public function orderfilterpromo() {
		$id = Input::get('id'); 
		$type = Input::get('type');
		$order_type = Input::get('order_type');
		$coupon = Input::get('coupon');;
		$state = Input::get('state');
		$order = Input::get('order');

		Session::put('id', $id);
		Session::put('coupon', $coupon);
		Session::put('state', $state);
		Session::put('order', $order);
		Session::put('type', $type);
		Session::put('order_type', $order_type);

		$query = PromoCodes::WhereNotNull('promo_codes.id');
		
		if($id != "" ){
			$query = $query->where('promo_codes.id', '=', $id);
		}
		
		if($coupon != ""){
			$query = $query->where('coupon_code', 'like', '%' . $coupon . '%');
		}
		if (Input::has('state') && Input::get('state') != 0) {
			
			$query = $query->where('state', '=', $state);
			
		}
		
		if (Input::has('type') && Input::get('type') != 0) {
			
			$query = $query->where('type', '=', $type);
			
		}


		if($order == "" ){
			$query = $query->orderBy('id', 'asc');
		}else{
			
			if($order == 0){
				$query = $query->orderBy($order_type, 'asc');
			} else if($order == 1){
				$query = $query->orderBy($order_type, 'desc');
			}
		}

		$title = ucwords(trans('customize.promo_codes') . " | " . trans('adminController.search_result')); /* 'Promo Codes | Search Result' */
		return View::make('list_promo_codes')
						->with('title', $title)
						->with('page', 'promo_code')
						->with('promo_codes', $query->paginate(20))
						->with('id', $id)
						->with('coupon', $coupon)
						->with('state', $state)
						->with('order', $order)
						->with('type', $type)
						->with('order_type', $order_type);
	}


	//--------- 
	public function orderfilterrequest() {
		$id = Input::get('id'); 
		$user = Input::get('user');
		$type = Input::get('type');
		$provider = Input::get('provider');;
		$date = Input::get('date');
		$order = Input::get('order');
		$payment = Input::get('payment');

		Session::put('id', $id);
		Session::put('user', $user);
		Session::put('provider', $provider);
		Session::put('order', $order);
		Session::put('type', $type);
		Session::put('date', $date);
		Session::put('payment', $payment);

		$query = DB::table('request')
				->leftJoin('provider', 'request.confirmed_provider', '=', 'provider.id')
				->leftJoin('user', 'request.user_id', '=', 'user.id')
				->select('user.first_name as user_first_name', 'user.last_name as user_last_name', 'provider.first_name as provider_first_name', 'provider.last_name as provider_last_name', 'user.id as user_id', 'provider.id as provider_id', 'provider.merchant_id as provider_merchant', 'request.id as id', 'request.created_at as date', 'request.payment_mode', 'request.is_started', 'request.is_provider_arrived', 'request.payment_mode', 'request.is_completed', 'request.is_paid', 'request.is_provider_started', 'request.confirmed_provider'
						, 'request.status', 'request.time', 'request.distance', 'request.total', 'request.is_cancelled', 'request.transfer_amount');

		$setting = Settings::where('key', 'paypal')->first();
		
		
		if($id != ""){
			$query = $query->where('request.id', '=', $id);
		}
		if($user != ""){
			$query = $query->where('user.first_name', 'like', '%' . $user . '%');
		}
		if($provider != ""){
			$query = $query->where('provider.first_name', 'like', '%' . $provider . '%');
		}
		if (Input::has('payment') && Input::get('payment') != 0) {
			$query = $query->where('payment_mode', '=', $payment-1);
		}
		if($date != ""){
			$query = $query->where('request_start_time', 'like', '%' . $date . '%');
		}

		if($order == "" ){
			$query = $query->orderBy('id', 'asc');
		}else{
			if($order == 0){
				$query = $query->orderBy($type, 'asc');
			} else if($order == 1){
				$query = $query->orderBy($type, 'desc');
			}
		}
		// $query = $query->orderBy('user.first_name', 'desc');

		$title = ucwords(trans('customize.Request') . " | " . trans('adminController.search_result')); /* 'Promo Codes | Search Result' */
		return View::make('requests')
						->with('title', $title)
						->with('page', 'requests')
						->with('requests', $query->paginate(20))
						->with('id', $id)
						->with('user', $user)
						->with('provider', $provider)
						->with('order', $order)
						->with('type', $type)
						->with('date', $date)
						->with('setting', $setting)
						->with('payment', $payment);
	}

	public function allow_availability() {
		Settings::where('key', 'allowcal')->update(array('value' => 1));
		return Redirect::to("/admin/providers");
	}

	public function disable_availability() {
		Settings::where('key', 'allowcal')->update(array('value' => 0));
		return Redirect::to("/admin/providers");
	}

	public function availability_provider() {
		$id = Request::segment(4);
		$provider = Provider::where('id', $id)->first();
		if ($provider) {
			$success = Input::get('success');
			$pavail = ProviderAvail::where('provider_id', $id)->paginate(10);
			$prvi = array();
			foreach ($pavail as $pv) {
				$prv = array();
				$prv['title'] = 'available';
				$prv['start'] = date('Y-m-d', strtotime($pv->start)) . "T" . date('H:i:s', strtotime($pv->start));
				$prv['end'] = date('Y-m-d', strtotime($pv->end)) . "T" . date('H:i:s', strtotime($pv->end));
				;
				array_push($prvi, $prv);
			}
			$pvjson = json_encode($prvi);
			//Log::info('Provider availability json = ' . print_r($pvjson, true));
			$title = ucwords(trans('adminController.availability') . ' ' . trans('customize.Provider')); /* 'Provider Availability' */
			return View::make('availability_provider')
							->with('title', $title)
							->with('page', 'providers')
							->with('success', $success)
							->with('pvjson', $pvjson)
							->with('provider', $provider);
		} else {
			return View::make('admin.notfound')->with('title', trans('adminController.page_not_found'))->with('page', trans('adminController.page_not_found'));
		}
	}

	public function provideravailabilitySubmit() {
		$id = Request::segment(4);
		$proavis = $_POST['proavis'];
		$proavie = $_POST['proavie'];
		$length = $_POST['length'];
		//Log::info('Start end time Array Length = ' . print_r($length, true));
		DB::delete("delete from provider_availability where provider_id = '" . $id . "';");
		for ($l = 0; $l < $length; $l++) {
			$pv = new ProviderAvail;
			$pv->provider_id = $id;
			$pv->start = $proavis[$l];
			$pv->end = $proavie[$l];
			$pv->save();
		}
		//Log::info('providers availability start = ' . print_r($proavis, true));
		//Log::info('providers availability end = ' . print_r($proavie, true));
		return Response::json(array('success' => true));
	}

	public function view_documents_provider() {
		$id = Request::segment(4);
		$provider = Provider::where('id', $id)->first();
		$provider_documents = ProviderDocument::where('provider_id', $id)->paginate(10);
		if ($provider) {
			$title = ucwords(trans('adminController.doc_of') .' ' . trans('customize.Provider') . ": " . $provider->first_name . " " . $provider->last_name); /* 'Provider View Documents' */
			return View::make('view_documents')
							->with('title', $title)
							->with('page', 'providers')
							->with('docs', $provider_documents)
							->with('provider', $provider);
		} else {
			return View::make('admin.notfound')->with('title', trans('adminController.page_not_found'))->with('page', trans('adminController.page_not_found'));
		}
	}

	//Providers Who currently requesting
	public function current() {
		Session::put('che', 'current');

		$requests = DB::table('request')
				->leftJoin('provider', 'request.confirmed_provider', '=', 'provider.id')
				->select('provider.id as id', 'provider.first_name as first_name', 'provider.last_name as last_name', 'provider.phone as phone', 'provider.email as email', 'provider.picture as picture', 'provider.merchant_id as merchant_id', 'provider.bio as bio', 'request.total as total_requests', 'request.total as accepted_requests','provider.is_approved as is_approved', 'provider.status_id as status_id')
				->where('deleted_at', NULL)
				->where('request.is_started', 1)
				->where('request.is_completed', 0)
				->paginate(10);
		$title = ucwords(trans('customize.Provider') . " | " . trans('provider.on_duty_now')); /* 'Providers | Currently Providing' */
		return View::make('providers')
						->with('title', $title)
						->with('page', 'providers')
						->with('order', 0)
						->with('type', 'id')
						->with('providers', $requests);
	}

	public function theme() {
		$th = Theme::all()->count();

		if ($th == 1) {
			$theme = Theme::first();
		} else {
			$theme = new Theme;
		}

		$theme->theme_color = '#' . Input::get('color1');
		$theme->secondary_color = '#' . Input::get('color3');
		$theme->primary_color = '#' . Input::get('color2');
		$theme->hover_color = '#' . Input::get('color4');
		$theme->active_color = '#' . Input::get('color5');

		$css_msg = ".btn-default {
  color: #ffffff;
  background-color: $theme->theme_color;
}
.navbar-nav > li {
  float: left;
}
.btn-info{
	color: #000;
	background: #fff;
	border-radius: 0px;
	border:1px solid $theme->theme_color;
}
.nav-admin .dropdown :hover, .nav-admin .dropdown :hover {
	background: $theme->hover_color;
	color: #000;
}
.navbar-nav > li > a {
  border-radius: 0px;
}
.navbar-nav > li + li {
  margin-left: 2px;
}
.navbar-nav > li.active > a,
.navbar-nav> li.active > a:hover,
.navbar-nav > li.active > a:focus {
  color: #ffffff;
  background-color: $theme->active_color!important;
}
.logo_img_login{
border-radius: 30px;border: 4px solid $theme->theme_color;
}
.btn-success {
  color: #ffffff;
  background-color: $theme->theme_color;
  border-color: $theme->theme_color;
}
.btn-success:hover,
.btn-success:focus,
.btn-success:active,
.btn-success.active,
.open .dropdown-toggle.btn-success {
  color: #ffffff;
  background-color: $theme->theme_color;
  border-color: $theme->theme_color;

}


.btn-success.disabled,
.btn-success[disabled],
fieldset[disabled] .btn-success,
.btn-success.disabled:hover,
.btn-success[disabled]:hover,
fieldset[disabled] .btn-success:hover,
.btn-success.disabled:focus,
.btn-success[disabled]:focus,
fieldset[disabled] .btn-success:focus,
.btn-success.disabled:active,
.btn-success[disabled]:active,
fieldset[disabled] .btn-success:active,
.btn-success.disabled.active,
.btn-success[disabled].active,
fieldset[disabled] .btn-success.active {

  background-color: $theme->theme_color;
  border-color: $theme->theme_color;
}
.btn-success .badge {
  color: $theme->theme_color;
  background-color: #ffffff;
}
.btn-info {
  color: #ffffff;
  background-color: $theme->theme_color;
  border-color: $theme->theme_color;
}
.btn-info:hover,
.btn-info:focus,
.btn-info:active,
.btn-info.active,
.open .dropdown-toggle.btn-info {
  color: #000;
  background-color: #FFFF;
  border-color: $theme->theme_color;
}
.btn-info:active,
.btn-info.active,
.open .dropdown-toggle.btn-info {
  background-image: none;
}
.btn-info.disabled,
.btn-info[disabled],
fieldset[disabled] .btn-info,
.btn-info.disabled:hover,
.btn-info[disabled]:hover,
fieldset[disabled] .btn-info:hover,
.btn-info.disabled:focus,
.btn-info[disabled]:focus,
fieldset[disabled] .btn-info:focus,
.btn-info.disabled:active,
.btn-info[disabled]:active,
fieldset[disabled] .btn-info:active,
.btn-info.disabled.active,
.btn-info[disabled].active,
fieldset[disabled] .btn-info.active {
  background-color: $theme->theme_color;
  border-color: $theme->theme_color;
}
.btn-info .badge {
  color: $theme->theme_color;
  background-color: #029acf;
  border-color: #029acf;
}
.btn-success,
.btn-success:hover {
  background-image: -webkit-linear-gradient($theme->theme_color $theme->theme_color 6%, $theme->theme_color);
  background-image: linear-gradient($theme->theme_color, $theme->theme_color 6%, $theme->theme_color);
  background-repeat: no-repeat;
  filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='$theme->theme_color', endColorstr='$theme->theme_color', GradientType=0);
  filter: none;
  border: 1px solid $theme->theme_color;
}
.btn-info,
.btn-info:hover {
  background-image: -webkit-linear-gradient($theme->theme_color, $theme->theme_color 6%, $theme->theme_color);
  background-image: linear-gradient($theme->theme_color, $theme->theme_color 6%, $theme->theme_color);
  background-repeat: no-repeat;
  filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='$theme->theme_color', endColorstr='$theme->theme_color', GradientType=0);
  filter: none;
  border: 1px solid $theme->theme_color;
}
.logo h3{
	margin: 0px;
	color: $theme->theme_color;
}

.second-nav{
	background: $theme->theme_color;
}
.login_back{background-color: $theme->theme_color;}
.no_radious:hover{background-image: -webkit-linear-gradient($theme->theme_color, $theme->theme_color 6%, $theme->theme_color);background-image: linear-gradient(#5d4dd1, #5d4dd1 6%, #5d4dd1);background-repeat: no-repeat;filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#5d4dd1', endColorstr='#5d4dd1', GradientType=0);filter: none;border: 1px solid #5d4dd1;}
.navbar-nav li:nth-child(1) a{
	background: $theme->primary_color;
}

.navbar-nav li:nth-child(2) a{
	background: $theme->secondary_color;
}

.navbar-nav li:nth-child(3) a{
	background: $theme->primary_color;
}

.navbar-nav li:nth-child(4) a{
	background: $theme->secondary_color;
}

.navbar-nav li:nth-child(5) a{
	background: $theme->primary_color;
}

.navbar-nav li:nth-child(6) a{
	background: $theme->secondary_color;
}

.navbar-nav li:nth-child(7) a{
	background: $theme->primary_color;
}

.navbar-nav li:nth-child(8) a{
	background: $theme->secondary_color;
}

.navbar-nav li:nth-child(9) a{
	background: $theme->primary_color;
}

.navbar-nav li:nth-child(10) a{
	background: $theme->secondary_color;
}

.navbar-nav li a:hover{
	background: $theme->hover_color;
}
.btn-green{

	background: $theme->theme_color;
	color: #fff;
}
.btn-green:hover{
	background: $theme->hover_color;
	color: #fff;
}
";
		$t = file_put_contents(public_path() . '/stylesheet/theme_cus.css', $css_msg);
		/* chmod(public_path() . '/stylesheet/theme_cus.css', 0777); */

		if (Input::hasFile('logo')) {
			// Upload File
			$file_name = time();
			$file_name .= rand();
			$ext = Input::file('logo')->getClientOriginalExtension();

			Input::file('logo')->move(public_path() . "/uploads", $file_name . "." . $ext);
			$local_url = $file_name . "." . $ext;

			/* $new = Image::make(public_path() . "/uploads/" . $local_url)->resize(70, 70)->save(); */

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
			if (isset($theme->logo)) {
				$icon = asset_url() . '/uploads/' . $theme->logo;
				unlink_image($icon);
			}
			$theme->logo = $local_url;
		}

		if (Input::hasFile('icon')) {
			// Upload File
			$file_name1 = time();
			$file_name1 .= rand();
			$file_name1 .= 'icon';
			$ext1 = Input::file('icon')->getClientOriginalExtension();
			Input::file('icon')->move(public_path() . "/uploads", $file_name1 . "." . $ext1);
			$local_url1 = $file_name1 . "." . $ext1;

			// Upload to S3
			if (Config::get('app.s3_bucket') != "") {
				$s3 = App::make('aws')->get('s3');
				$pic = $s3->putObject(array(
					'Bucket' => Config::get('app.s3_bucket'),
					'Key' => $file_name1,
					'SourceFile' => public_path() . "/uploads/" . $local_url1,
				));

				$s3->putObjectAcl(array(
					'Bucket' => Config::get('app.s3_bucket'),
					'Key' => $file_name1,
					'ACL' => 'public-read'
				));

				$s3_url1 = $s3->getObjectUrl(Config::get('app.s3_bucket'), $file_name1);
			} else {
				$s3_url1 = asset_url() . '/uploads/' . $local_url1;
			}
			if (isset($theme->favicon)) {
				$icon = asset_url() . '/uploads/' . $theme->favicon;
				unlink_image($icon);
			}
			$theme->favicon = $local_url1;
		}
		$theme->save();

		if(Input::get('layout_color')){
			$settings = Settings::where('key','layout_color')->first();
			if(!$settings) {
				$settings 				= new Settings;
				$settings->key 			= 'layout_color' ;
				$settings->tool_tip 	= trans('settings.layout_color') ;
				$settings->page 		= 2 ;
			}
	        $settings->value = Input::get('layout_color');
	        $settings->save();
	    }

		return Redirect::to("/admin/settings");
	}

	public function transfer_amount() {
		$request = Requests::where('id', Input::get('request_id'))->first();
		$provider = Provider::where('id', $request->confirmed_provider)->first();
		$amount = Input::get("amount");

		if (($amount + $request->transfer_amount) <= $request->total && ($amount + $request->transfer_amount) > 0) {
			if (Config::get('app.default_payment') == 'stripe') {
				Stripe::setApiKey(Config::get('app.stripe_secret_key'));
				// dd($amount$request->transfer_amount);
				$transfer = Stripe_Transfer::create(array(
							"amount" => $amount * 100, // amount in cents
							"currency" => "usd",
							"recipient" => $provider->merchant_id)
				);
			} else {
				Braintree_Configuration::environment(Config::get('app.braintree_environment'));
				Braintree_Configuration::merchantId(Config::get('app.braintree_merchant_id'));
				Braintree_Configuration::publicKey(Config::get('app.braintree_public_key'));
				Braintree_Configuration::privateKey(Config::get('app.braintree_private_key'));
				$payment_data = Payment::where('user_id', $request->user_id)->first();
				$customer_id = $payment_data->customer_id;
				$result = Braintree_Transaction::sale(
								array(
									'merchantAccountId' => $provider->merchant_id,
									'paymentMethodNonce' => $customer_id,
									'options' => array(
										'submitForSettlement' => true,
										'holdInEscrow' => true,
									),
									'amount' => $amount
								)
				);
			}
			$request->transfer_amount += $amount;
			$request->save();
			return Redirect::to("/admin/requests");
		} else {
			// Session::put('error', "Amount exceeds the total amount to be paid");
			Session::put('error', trans('adminController.more_pay'));
			$title = ucwords(trans('adminController.transfer_amount'));
			return View::make('transfer_amount')
							->with('request', $request)
							->with('title', $title)
							->with('page', 'providers');
		}
	}

	public function pay_provider($id) {
		$request = Requests::find($id);
		if (Config::get('app.default_payment') == 'stripe') {
			// $title = ucwords("Transfer amount");
			$title = ucwords(trans('adminController.transfer_amount'));
			return View::make('transfer_amount')
							->with('request', $request)
							->with('title', $title)
							->with('page', 'providers');
		} else {
			$this->_braintreeConfigure();
			$clientToken = Braintree_ClientToken::generate();
			// Session::put('error', 'Manual Transfer is not available in braintree.');
			Session::put('error', trans('adminController.transfer_not_possible').'.');
			$title = ucwords(trans('adminController.transfer_amount'));
			return View::make('transfer_amount')
							->with('request', $request)
							->with('clientToken', $clientToken)
							->with('title', $title)
							->with('page', 'requests');
		}
	}

	public function charge_user($id) {
		$request = Requests::find($id);
		//Log::info('Charge User from admin');
		$total = $request->total;
		$payment_data = Payment::where('user_id', $request->user_id)->first();
		echo "<script>console.log('Payment Data: '". $payment_data .")</script>";
		$customer_id = $payment_data->customer_id;
		$setransfer = Settings::where('key', 'transfer')->first();
		$transfer_allow = $setransfer->value;
		if (Config::get('app.default_payment') == 'stripe') {
			//dd($customer_id);
			Stripe::setApiKey(Config::get('app.stripe_secret_key'));
			try {
				$charge = Stripe_Charge::create(array(
							"amount" => $total * 100,
							"currency" => "usd",
							"customer" => $customer_id)
				);
				//Log::info('charge stripe = ' . print_r($charge, true));
			} catch (Stripe_InvalidRequestError $e) {
				// Invalid parameters were supplied to Stripe's API
				$ownr = User::find($request->user_id);
				$ownr->debt = $total;
				$ownr->save();
				$response_array = array('error' => $e->getMessage());
				$response_code = 200;
				$response = Response::json($response_array, $response_code);
				return $response;
			}
			$request->is_paid = 1;
			$settng = Settings::where('key', 'service_fee')->first();
			$settng_mode = Settings::where('key', 'payment_mode')->first();
			if ($settng_mode->value == 2 and $transfer_allow == 1) {
				$transfer = Stripe_Transfer::create(array(
							"amount" => ($total - $settng->value) * 100, // amount in cents
							"currency" => "usd",
							"recipient" => $provider_data->merchant_id)
				);
				$request->transfer_amount = ($total - $settng->value);
			}
		} else {
			try {
				Braintree_Configuration::environment(Config::get('app.braintree_environment'));
				Braintree_Configuration::merchantId(Config::get('app.braintree_merchant_id'));
				Braintree_Configuration::publicKey(Config::get('app.braintree_public_key'));
				Braintree_Configuration::privateKey(Config::get('app.braintree_private_key'));
				if ($settng_mode->value == 2 and $transfer_allow == 1) {
					$sevisett = Settings::where('key', 'service_fee')->first();
					$service_fee = $sevisett->value;
					$result = Braintree_Transaction::sale(array(
								'amount' => $total - $service_fee,
								'paymentMethodNonce' => $customer_id,
								'merchantAccountId' => $provider_data->merchant_id,
								'options' => array(
									'submitForSettlement' => true,
									'holdInEscrow' => true,
								),
								'serviceFeeAmount' => $service_fee
					));
				} else {
					$result = Braintree_Transaction::sale(array(
								'amount' => $total,
								'paymentMethodNonce' => $customer_id
					));
				}
				//Log::info('result of braintree = ' . print_r($result, true));
				if ($result->success) {
					$request->is_paid = 1;
				} else {
					$request->is_paid = 0;
				}
			} catch (Exception $e) {
				//Log::info('error in braintree payment = ' . print_r($e, true));
			}
		}
		$request->card_payment = $total;
		$request->ledger_payment = $request->total - $total;
		$request->save();
		return Redirect::to('/admin/requests');
	}

	public function add_request() {
		$user_id = Request::segment(3);
		$user = User::find($user_id);
		$services = ProviderType::where('is_visible', '=', 1)->get();
		$total_services = ProviderType::where('is_visible', '=', 1)->count();
		$providers = Provider::whereRaw('is_available = 1 and is_approved = 1')->get();
		// Payment options allowed
		$payment_options = array();

		$payments = Payment::where('user_id', $user_id)->count();

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
		$promosett = Settings::where('key', 'promo_code')->first();
		if ($promosett->value == 1) {
			$promo_allow = 1;
		} else {
			$promo_allow = 0;
		}
		$settdestination = Settings::where('key', 'get_destination')->first();
		$settdestination = $settdestination->value;
		$title = ucwords(trans('adminController.add') . " " . trans('customize.Request')); /* 'Add Request' */
		return View::make('add_request')
						->with('user', $user)
						->with('services', $services)
						->with('providers', $providers)
						->with('total_services', $total_services)
						->with('payment_option', $payment_options)
						->with('settdestination', $settdestination)
						->with('title', $title)
						->with('page', 'requests');
	}

	//create manual request from admin panel

	public function create_manual_request() {
		$latitude = Input::get('latitude');
		$longitude = Input::get('longitude');
		$d_latitude = Input::get('d_latitude');
		$d_longitude = Input::get('d_longitude');
		$type = Input::get('type');
		$provider = Input::get('provider');
		$src_address = Input::get('my_address');
		$dest_address = Input::get('my_dest');
		$user_id = Input::get('user_id');
		$payment_mode = Input::get('payment_type');
		$user_create_time = date('Y-m-d H:i:s');
		$time = date("Y-m-d H:i:s");

		$provider_details = Provider::where('id', '=', $provider)->first();
		$user = User::where('id', '=', $user_id)->first();

		$request = new Requests;
		$request->user_id = $user_id;
		$request->request_start_time = $time;
		$request->latitude = $latitude;
		$request->longitude = $longitude;

		if ($d_longitude != '' && $d_latitude != '') {
			$request->D_latitude = $d_latitude;
			$request->D_longitude = $d_longitude;
		}

		$request->current_provider = $provider; 
		$request->payment_mode = $payment_mode;
		$request->src_address = $src_address;
		$request->dest_address = $dest_address;
		$request->req_create_user_time = $user_create_time;
		$request->save();

		$reqid = $request->id;

		$request_service = new RequestServices;
		$request_service->type = $type;
		$request_service->request_id = $request->id;
		$request_service->save();

		$user = User::find($user_id);

		if($user != null){
			$user->latitude = $latitude;
			$user->longitude = $longitude;
			$user->save();

			$providerlocation = new RequestLocation;
			$providerlocation->request_id = $request->id;
			$providerlocation->latitude = $latitude;
			$providerlocation->longitude = $longitude;
			$providerlocation->save();

			if ($request->save()) {
				$payment_opt = $payment_mode;
				$settings = Settings::where('key', 'provider_timeout')->first();
				$time_left = $settings->value;

				$provider = Provider::find($provider);

				if ($provider) {
					$msg_array = array();
					$msg_array['unique_id'] = 1;
					$msg_array['request_id'] = $request->id;
					$msg_array['time_left_to_respond'] = $time_left;
					$msg_array['payment_mode'] = $payment_opt;
					
					$request_data = array();
					$request_data['user'] = array();
					$request_data['user']['name'] = $user->first_name . " " . $user->last_name;
					$request_data['user']['picture'] = $user->picture;
					$request_data['user']['phone'] = $user->phone;
					$request_data['user']['address'] = $user->address;
					$request_data['user']['latitude'] = $request->latitude;
					$request_data['user']['longitude'] = $request->longitude;
					$request_data['user']['user_dist_lat'] = $request->D_latitude;
					$request_data['user']['user_dist_long'] = $request->D_longitude;
					$request_data['user']['payment_type'] = $payment_opt;
					$request_data['user']['rating'] = $user->rate;
					$request_data['user']['num_rating'] = $user->rate_count;

					//Envia a solicitação para o Prestador selecionado
					$msg_array['request_data'] = $request_data;
					$message = $msg_array;
					send_notifications($provider->id, "provider",trans('adminController.new_request'), $message);
				} else{
					$response_array = array('success' => false, 'error' => trans('providerController.provider_id_not_found'), 'error_messages' => array(trans('providerController.provider_id_not_found')), 'error_code' => 410);
					return Redirect::to('/admin/users');
				}
				$current_request = Requests::where('id', '=', $reqid)->first();
				Session::put('msg', trans('adminController.new_request_msg'));
				return Redirect::to('/admin/users');
			}
		} else{
			$response_array = array('success' => false, 'error' => trans('customerController.user_id_not_found'), 'error_messages' => array(trans('customerController.user_id_not_found')), 'error_code' => 410);
			return Redirect::to('/admin/users');
		}	
	}

	public function get_nearby() {
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
			$query = "SELECT "
					. "provider.id, "
					. "provider.first_name, "
					. "provider.last_name, "
					. "provider.latitude, "
					. "provider.longitude, "
					. "ROUND(" . $multiply . " * 3956 * acos( cos( radians('$latitude') ) * "
					. "cos( radians(latitude) ) * "
					. "cos( radians(longitude) - radians('$longitude') ) + "
					. "sin( radians('$latitude') ) * "
					. "sin( radians(latitude) ) ) ,8) as distance "
					. "from provider "
					. "where is_available = 1 and "
					. "is_active = 1 and "
					. "is_approved = 1 and "
					. "ROUND((" . $multiply . " * 3956 * acos( cos( radians('$latitude') ) * "
					. "cos( radians(latitude) ) * "
					. "cos( radians(longitude) - radians('$longitude') ) + "
					. "sin( radians('$latitude') ) * "
					. "sin( radians(latitude) ) ) ) ,8) <= $distance "
					. "order by distance";
		} else {
			$query = "SELECT "
					. "provider.id, "
					. "provider.first_name, "
					. "provider.last_name, "
					. "provider.latitude, "
					. "provider.longitude, "
					. "ROUND(" . $multiply . " * 3956 * acos( cos( radians('$latitude') ) * "
					. "cos( radians(provider.latitude) ) * "
					. "cos( radians(provider.longitude) - radians('$longitude') ) + "
					. "sin( radians('$latitude') ) * "
					. "sin( radians(provider.latitude) ) ) ,8) as distance "
					. "from provider "
					. "JOIN provider_services "
					. "where provider.is_available = 1 and "
					. "provider.is_active = 1 and "
					. "provider.is_approved = 1 and "
					. "ROUND((" . $multiply . " * 3956 * acos( cos( radians('$latitude') ) * "
					. "cos( radians(provider.latitude) ) * "
					. "cos( radians(provider.longitude) - radians('$longitude') ) + "
					. "sin( radians('$latitude') ) * "
					. "sin( radians(provider.latitude) ) ) ) ,8) <= $distance and "
					. "provider.id = provider_services.provider_id and "
					. "provider_services.type = $typestring "
					. "order by distance";
		}
		$providers = DB::select(DB::raw($query));
	   
		foreach ($providers as $key) {
			echo "<option value=" . $key->id . ">" . $key->first_name . " " . $key->last_name . "</option>";
		}

	}

	public function payment_details() {
		$braintree_environment = Config::get('app.braintree_environment');
		$braintree_merchant_id = Config::get('app.braintree_merchant_id');
		$braintree_public_key = Config::get('app.braintree_public_key');
		$braintree_private_key = Config::get('app.braintree_private_key');
		$braintree_cse = Config::get('app.braintree_cse');
		$twillo_account_sid = Config::get('app.twillo_account_sid');
		$twillo_auth_token = Config::get('app.twillo_auth_token');
		$twillo_number = Config::get('app.twillo_number');
		$stripe_publishable_key = Config::get('app.stripe_publishable_key');
		$default_payment = Config::get('app.default_payment');
		$stripe_secret_key = Config::get('app.stripe_secret_key');
		$mail_driver = Config::get('mail.mail_driver');
		$email_name = Config::get('mail.from.name');
		$email_address = Config::get('mail.from.address');
		$mandrill_secret = Config::get('services.mandrill_secret');
		$sendgrid_secret = Config::get('services.sendgrid_secret');
		
		$install = array(
			'braintree_environment' => $braintree_environment,
			'braintree_merchant_id' => $braintree_merchant_id,
			'braintree_public_key' => $braintree_public_key,
			'braintree_private_key' => $braintree_private_key,
			'braintree_cse' => $braintree_cse,
			'twillo_account_sid' => $twillo_account_sid,
			'twillo_auth_token' => $twillo_auth_token,
			'twillo_number' => $twillo_number,
			'stripe_publishable_key' => $stripe_publishable_key,
			'stripe_secret_key' => $stripe_secret_key,
			'mail_driver' => $mail_driver,
			'email_address' => $email_address,
			'email_name' => $email_name,
			'mandrill_secret' => $mandrill_secret,
			'sendgrid_secret' => $sendgrid_secret, 
			'default_payment' => $default_payment);
		$request_id = Input::get('request_id');
		$start_date = Input::get('start_date');
		$end_date = Input::get('end_date');
		$submit = Input::get('submit');
		$provider_id = Input::get('provider_id');
		$user_id = Input::get('user_id');
		$status = Input::get('status');

		$var = $start_date;
		$date = str_replace('/', '-', $var);
		$start_date =  date('Y-m-d H:i:s', strtotime($date));

		$var2 = $end_date;
		$date2 = str_replace('/', '-', $var2);
		$end_date =  date('m/d/Y', strtotime($date2));

		Session::put('end_date', $end_date);
		Session::put('start_date', $start_date);

		$start_time = date("Y-m-d H:i:s", strtotime($start_date));
		$end_time = date("Y-m-d H:i:s", strtotime($end_date));
		$start_date = date("Y-m-d", strtotime($start_date));
		$end_date = date("Y-m-d", strtotime($end_date));

		$query = DB::table('request')
				->leftJoin('user', 'request.user_id', '=', 'user.id')
				->leftJoin('provider', 'request.confirmed_provider', '=', 'provider.id')
				->leftJoin('provider_type', 'provider.type', '=', 'provider_type.id')
				->leftJoin('transaction as base_transaction', 'request.base_tax_transaction_id', '=', 'base_transaction.id')
				->leftJoin('transaction as complete_transaction', 'request.request_price_transaction_id', '=', 'complete_transaction.id');

		if (Input::get('request_id') && Input::get('request_id')) {
			$query = $query->where('request.id', '=', $request_id);
		}

		if (Input::get('start_date') && Input::get('end_date')) {
			$query = $query->where('request_start_time', '>=', $start_time)
					->where('request_start_time', '<=', $end_time);
		}

		if (Input::get('provider_id') && Input::get('provider_id') != 0) {
			$query = $query->where('request.confirmed_provider', '=', $provider_id);
		}

		if (Input::get('user_id') && Input::get('user_id') != 0) {
			$query = $query->where('request.user_id', '=', $user_id);
		}

		if (Input::get('status') && Input::get('status') != 0) {
			if ($status == 1) {
				$query = $query->where('request.is_completed', '=', 1);
			} else {
				$query = $query->where('request.is_cancelled', '=', 1);
			}
		} else {

			$query = $query->where(function ($que) {
				$que->where('request.is_completed', '=', 1)
						->orWhere('request.is_cancelled', '=', 1);
			});
		}

		$requests = $query->select(
			'request.request_start_time',
	 		'provider_type.name as type',
	 		'request.ledger_payment',
	 		'request.card_payment',
	 		'user.first_name as user_first_name',
	 		'user.last_name as user_last_name',
	 		'provider.first_name as provider_first_name',
	 		'provider.last_name as provider_last_name',
	 		'user.id as user_id',
	 		'provider.id as provider_id',
	 		'request.id as id',
	 		'request.created_at as date',
	 		'request.is_started',
	 		'request.is_provider_arrived',
	 		'request.payment_mode',
	 		'request.is_completed',
	 		'request.is_paid',
	 		'request.is_provider_started',
	 		'request.confirmed_provider',
	 		'request.promo_id',
	 		'request.promo_code',
	 		'request.status',
	 		'request.time',
	 		'request.distance',
	 		'request.total',
	 		'request.is_cancelled',
	 		'request.promo_payment',
	 		'base_transaction.gateway_transaction_id as base_transaction_id',
	 		'base_transaction.split_status as base_transaction_transfer_status',
	 		'complete_transaction.gateway_transaction_id as complete_transaction_id',
	 		'complete_transaction.split_status as complete_transaction_transfer_status');
		$requests = $requests->orderBy('id', 'DESC')->paginate(10);

		$query = DB::table('request')
				->leftJoin('user', 'request.user_id', '=', 'user.id')
				->leftJoin('provider', 'request.confirmed_provider', '=', 'provider.id')
				->leftJoin('provider_type', 'provider.type', '=', 'provider_type.id');

		if (Input::get('start_date') && Input::get('end_date')) {
			$query = $query->where('request_start_time', '>=', $start_time)
					->where('request_start_time', '<=', $end_time);
		}

		//prestador
		if (Input::has('provider_id')) {
			$query = $query->whereIn('request.confirmed_provider', $provider_id);
		}

		//usuario
		if (Input::has('user_id')) {
			$query = $query->whereIn('request.user_id', $user_id);	
		}
		$completed_rides = $query->where('request.is_completed', 1)->count();


		$query = DB::table('request')
				->leftJoin('user', 'request.user_id', '=', 'user.id')
				->leftJoin('provider', 'request.confirmed_provider', '=', 'provider.id')
				->leftJoin('provider_type', 'provider.type', '=', 'provider_type.id');

		if (Input::get('start_date') && Input::get('end_date')) {
			$query = $query->where('request_start_time', '>=', $start_time)
					->where('request_start_time', '<=', $end_time);
		}

		if (Input::get('provider_id') && Input::get('provider_id') != 0) {
			$query = $query->where('request.confirmed_provider', '=', $provider_id);
		}

		if (Input::get('user_id') && Input::get('user_id') != 0) {
			$query = $query->where('request.user_id', '=', $user_id);
		}
		$cancelled_rides = $query->where('request.is_cancelled', 1)->count();


		$query = DB::table('request')
				->leftJoin('user', 'request.user_id', '=', 'user.id')
				->leftJoin('provider', 'request.confirmed_provider', '=', 'provider.id')
				->leftJoin('provider_type', 'provider.type', '=', 'provider_type.id');

		if (Input::get('start_date') && Input::get('end_date')) {
			$query = $query->where('request_start_time', '>=', $start_time)
					->where('request_start_time', '<=', $end_time);
		}

		if (Input::get('provider_id') && Input::get('provider_id') != 0) {
			$query = $query->where('request.confirmed_provider', '=', $provider_id);
		}

		if (Input::get('user_id') && Input::get('user_id') != 0) {
			$query = $query->where('request.user_id', '=', $user_id);
		}
		$card_payment = $query->where('request.is_completed', 1)->sum('request.card_payment');


		$query = DB::table('request')
				->leftJoin('user', 'request.user_id', '=', 'user.id')
				->leftJoin('provider', 'request.confirmed_provider', '=', 'provider.id')
				->leftJoin('provider_type', 'provider.type', '=', 'provider_type.id');

		if (Input::get('start_date') && Input::get('end_date')) {
			$query = $query->where('request_start_time', '>=', $start_time)
					->where('request_start_time', '<=', $end_time);
		}

		if (Input::get('provider_id') && Input::get('provider_id') != 0) {
			$query = $query->where('request.confirmed_provider', '=', $provider_id);
		}

		if (Input::get('user_id') && Input::get('user_id') != 0) {
			$query = $query->where('request.user_id', '=', $user_id);
		}
		$credit_payment = $query->where('request.is_completed', 1)->sum('request.ledger_payment');
		$cash_payment = $query->where('request.payment_mode', 1)->sum('request.total');


		if (Input::get('submit') && Input::get('submit') == 'Download_Report') {

			header('Content-Type: text/csv; charset=utf-8');
			header('Content-Disposition: attachment; filename=data.csv');
			$handle = fopen('php://output', 'w');
			$settings = Settings::where('key', 'default_distance_unit')->first();
			$unit = $settings->value;
			if ($unit == 0) {
				$unit_set = trans('setting.km');
			} elseif ($unit == 1) {
				$unit_set = trans('setting.miles');
			}
			fputcsv($handle, array(trans('adminController.id'), trans('adminController.data'), trans('adminController.service_type'), trans('customize.Provider'), trans('adminController.user'), trans('adminController.distance') . '(' . $unit_set . ')', trans('adminController.time'), trans('adminController.pay_mode'), trans('adminController.earning'), trans('adminController.refferal_bonus'), trans('adminController.promo_code'), trans('adminController.card_pay')));
			
			foreach ($requests as $request) {
				$pay_mode = trans('adminController.card_pay');
				if ($request->payment_mode == 1) {
					$pay_mode = trans('adminController.cash_pay');
				}
				fputcsv($handle, array(
					$request->id,
					date('l, F d Y h:i A', strtotime($request->request_start_time)),
					$request->type,
					$request->provider_first_name . " " . $request->provider_last_name,
					$request->user_first_name . " " . $request->user_last_name,
					sprintf2($request->distance, 2),
					sprintf2($request->time, 2),
					$pay_mode,
					sprintf2($request->total, 2),
					sprintf2($request->ledger_payment, 2),
					sprintf2($request->promo_payment, 2),
					sprintf2($request->card_payment, 2),
				));
			}

			fputcsv($handle, array());
			fputcsv($handle, array());
			fputcsv($handle, array(trans('adminController.total_trip'), $completed_rides + $cancelled_rides));
			fputcsv($handle, array(trans('adminController.complete_trip'), $completed_rides));
			fputcsv($handle, array(trans('adminController.cancel_trip'), $cancelled_rides));
			fputcsv($handle, array(trans('adminController.total_pay'), sprintf2(($credit_payment + $card_payment), 2)));
			fputcsv($handle, array(trans('adminController.card_pay'), sprintf2($card_payment, 2)));
			fputcsv($handle, array(trans('adminController.credit_pay'), $credit_payment));

			fclose($handle);

			$headers = array(
				'Content-Type' => 'text/csv',
			);
		} else {
			/* $currency_selected = Keywords::where('alias', 'Currency')->first();
			  $currency_sel = $currency_selected->keyword; */
			$currency_sel = Config::get('app.generic_keywords.Currency');
			//$providers = Provider::paginate(10);
			//$users = User::paginate(10);
			$providers = DB::table('provider')->orderBy("first_name", "ASC")->orderBy("last_name", "ASC")->get();
			$users = DB::table('user')->orderBy("first_name", "ASC")->orderBy("last_name", "ASC")->get();
			$payment_default = ucfirst(Config::get('app.default_payment'));
			$title = ucwords(trans('customize.payment_details')); /* 'Payments' */
			return View::make('payment')
							->with('title', $title)
							->with('page', 'payments')
							->with('requests', $requests)
							->with('users', $users)
							->with('providers', $providers)
							->with('completed_rides', $completed_rides)
							->with('cancelled_rides', $cancelled_rides)
							->with('card_payment', $card_payment)
							->with('install', $install)
							->with('currency_sel', $currency_sel)
							->with('cash_payment', $cash_payment)
							->with('credit_payment', $credit_payment)
							->with('payment_default', $payment_default);
		}
	}

	public function requests_payment() {
		$requests = DB::table('request')
				->leftJoin('provider', 'request.confirmed_provider', '=', 'provider.id')
				->select(DB::raw('SUM(request.card_payment)as total,SUM(request.payment_remaining) as pay_to_provider,SUM(request.refund_remaining) as take_from_provider,COUNT(request.id)as trips,request.created_at,request.id, WEEK(request.created_at) as payoutweek'))
				->where('request.status', '=', 1)
				->where('request.is_completed', '=', 1)
				->groupBy('payoutweek')
				->orderBy('request.created_at', 'desc')
				->paginate(10);

		$response = Response::json($requests);


		// return $response;
		return View::make('requests_payment')
						// ->with('title', 'Payment Statement')
						->with('title', trans('adminController.week_report'))
						->with('page', 'week_statement')
						->with('requests', $requests);
	}

	public function admin_week_pdf() {
		$id = Input::get('id');
		$total = Input::get('total');
		$trips = Input::get('trips');
		$weekend = Input::get('weekend');
		$pay_to_provider = Input::get('pay_to_provider');
		$take_from_provider = Input::get('take_from_provider');

		$pdf = App::make('dompdf');
		$parameter = array();
		$parameter['title'] = trans('adminController.week_report');
		$parameter['date'] = date('Y-m-d');
		$parameter['page'] = 'dashboard';
		$parameter['id'] = $id;
		$parameter['total'] = $total;
		$parameter['weekend'] = $weekend;
		$parameter['trips'] = $trips;
		$parameter['pay_to_provider'] = $pay_to_provider;
		$parameter['take_from_provider'] = $take_from_provider;
		// return Response::json($parameter); 
		
		return View::make('invoice_pdf')
		  ->with('title', 'Payment Statement')
		  ->with('page', 'week_statement')
		  ->with('title', $parameter['title'])
		  ->with('date', $parameter['date'])
		  ->with('page', $parameter['page'])
		  ->with('id', $parameter['id'])
		  ->with('total', $parameter['total'])
		  ->with('weekend', $parameter['weekend'])
		  ->with('pay_to_provider', $parameter['pay_to_provider'])
		  ->with('take_from_provider', $parameter['take_from_provider'])
		  ->with('trips', $parameter['trips']); 
	
	  	/*
		$pdf = PDF::loadView('invoice_pdf', $parameter)->setPaper('legal')->setOrientation('landscape')->setWarnings(false);
		
		return $pdf->download($weekend . " " . 'weekly_report.pdf'); */
			
	}
	
	public function ngo(){
		$ngos = DB::table('ngo')->orderBy("name", "ASC")->paginate(10);
		$title = ucwords(trans('customize.Ngo'));
			
		return View::make('list_ngo')
			->with('title', $title)
			->with('page', '')
			->with('ngos', $ngos);
	}
	
	public function editNgo() {
		$id = Request::segment(4);
		
		$success = Input::get('success');
		
		$ngo = Ngo::find($id);
		
		$title = "";
		
		if(!$ngo){
			$id = 0;
			$myCodeID = "";
			$networkingCodeID = "";
			$myCode = "";
			$networkingCode = "";
			$name = "";
			$description = "";
			$website = "";
			$address = "";
			$phone = "";
			$logotype = "";
		}else{
			$id = $ngo->id;
			$myCodeID = $ngo->my_code;
			$networkingCodeID = $ngo->networking_code;
			$myCode = $ngo->myCode->code;
			$networkingCode = $ngo->networkingCode->code;
			$name = $ngo->name;
			$description = $ngo->description;
			$website = $ngo->website;
			$address = $ngo->address;
			$phone = $ngo->phone;
			$logotype = $ngo->logotype;
			
		}
		
		return View::make('edit_ngo')
						->with('title', $title)
						->with('page', 'provider-type')
						->with('success', $success)
						->with('id', $id)
						->with('myCodeID', $myCodeID)
						->with('networkingCodeID', $networkingCodeID)
						->with('myCode', $myCode)
						->with('networkingCode', $networkingCode)
						->with('name', $name)
						->with('description', $description)
						->with('website', $website)
						->with('address', $address)
						->with('phone', $phone)
						->with('logotype', $logotype)
						;
	}
	
	public function updateNgo(){	
		$id = Input::get('id');		
			
		if($id == 0){
			$ngo = new Ngo();
			$ngo->id_customer_type = 1;
			$ngo->my_code = Utils::generateCode();
			$ngo->networking_code = Input::get('networking_code');
		}else{
			$ngo = Ngo::find($id);
		}
		
		$ngo->name = Input::get('name');
		$ngo->description = Input::get('description');
		$ngo->website = Input::get('website');
		$ngo->address = Input::get('address');
		$ngo->phone = Input::get('phone');
		
		
		if (Input::hasFile('logotype')) {
			
			Log::info(1);
			
			$file_name = time();
			$file_name .= rand();
			$ext = Input::file('logotype')->getClientOriginalExtension();
			list($width, $height) = getimagesize(Input::file('logotype'));
			
			Log::info(2);
			
			if ($width == $height && $width >= 300 && $height >= 300) {
				Log::info(3);
				Input::file('logotype')->move(public_path() . "/uploads", $file_name . "." . $ext);
				Log::info(4);
				$local_url = $file_name . "." . $ext;
				Log::info($local_url);

				Log::info("app.s3_bucket=".Config::get('app.s3_bucket'));
				
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
				Log::info(5);
				if (isset($ngo->logotype)) {
					Log::info(6);
					if ($ngo->logotype != "") {
						Log::info(7);
						$logotype = $ngo->logotype;
						unlink_image($logotype);
					}
				}
				Log::info("s3_url:".$s3_url);
				$ngo->logotype = $s3_url;
			} else {
				return Redirect::to("/admin/ngo/edit/$ngo->id?success=4");
				//return Redirect::to("/admin/ngo");
			}
		}
		
		$ngo->save();
		
		return Redirect::to("/admin/ngo");
		
	}
	
	public function deleteNgo(){
		$id = Request::segment(4);
		$ngo = Ngo::where('id', $id)->delete();
		return Redirect::to("/admin/ngo");
	}

	public function searchCode(){	
		$code = Request::segment(4);
		
		$id = Code::where("code", "=", $code)->first();
		
		//echo $id;
		
		
		//exit;
		
		if($id == null){
			$statusCode = 400;
			$response['obj'][] = ["message"=>"teste"];
			return Response::json($response, $statusCode);
		}
		
		
		//Find in user's
		$obj = Code::find($id->id)->userMyCode;
		
		//Find in provider's
		if($obj == null || $obj == ""){
			$obj = Code::find($id->id)->providerMyCode;
		}
		//Find in ngo's
		if($obj == null || $obj == ""){
			$obj = Code::find($id->id)->ngoMyCode;
		}		

		$response = $obj;
		
        $statusCode = 200;

        return Response::json($response, $statusCode);
	}
	
}
