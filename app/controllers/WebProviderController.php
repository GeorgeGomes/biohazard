<?php

use Intervention\Image\ImageManagerStatic as Image;

class WebProviderController extends BaseController {
	/**
	 * Display a listing of the resource.
	 *
	 *
	 * @return Response
	 */
	public function __construct() {
		if (Config::get('app.production')) {
			echo trans('user_provider_web.something_cool');
			die();
		}

		$this->beforeFilter(function() {
			if (!Session::has('provider_id')) {
				//return "sessao caiu";
				return Redirect::to('/provider/signin');
			} else {
				$provider_id = Session::get('provider_id');
				$provider = Provider::find($provider_id);

				$provider_doc = ProviderDocument::where('provider_id', $provider_id)->first();

				Session::put('is_approved', $provider->is_approved);
				Session::put('status', $provider->status->name);
				Session::put('provider_name', $provider->first_name . " " . $provider->last_name);
				Session::put('provider_pic', $provider->picture);
				//Session::save();
			}
		}, array('except' => array(
				'providerLogin',
				'providerVerify',
				'providerForgotPassword',
				'providerRegister',
				'providerSave',
				'providerActivation',
				'surroundingCars',
		)));
	}

	public function toggle_availability() {
		$provider_id = Session::get('provider_id');
		$provider = Provider::find($provider_id);
		$provider->is_active = ($provider->is_active + 1 ) % 2;
		$provider->save();
	}

	public function set_location() {
		$provider_id = Session::get('provider_id');
		$provider = Provider::find($provider_id);
		$location = get_location(Input::get('lat'), Input::get('lng'));
		$latitude = $location['lat'];
		$longitude = $location['long'];
		$angle = get_angle($provider->latitude, $provider->longitude, $latitude, $longitude);
		$provider->old_latitude = $provider->latitude;
		$provider->old_longitude = $provider->longitude;
		$provider->latitude = $latitude;
		$provider->longitude = $longitude;
		$provider->bearing = $angle;
		$provider->save();
	}

	public function providerRequestPing() {
		//Session::forget('skipReviewProvider');
		$provider_id = Session::get('provider_id');
		$time = date("Y-m-d H:i:s");
		$query = "SELECT id,latitude,longitude,user_id,TIMESTAMPDIFF(SECOND,request_start_time, '$time') as diff from request where is_cancelled = 0 and status = 0 and current_provider=$provider_id and TIMESTAMPDIFF(SECOND,request_start_time, '$time') <= 600 limit 1";
		$requests = DB::select(DB::raw($query));
		$request_data = array();
		foreach ($requests as $request) {
			$request_data['success'] = "true";
			$request_data['request_id'] = $request->id;
			$request_data['time_left_to_respond'] = 600 - $request->diff;

			$user = User::find($request->user_id);

			$request_data['user'] = array();
			$request_data['user']['name'] = $user->first_name . " " . $user->last_name;
			$request_data['user']['picture'] = $user->picture;
			$request_data['user']['phone'] = $user->phone;
			$request_data['user']['address'] = $user->address;
			$request_data['user']['latitude'] = $request->latitude;
			$request_data['user']['longitude'] = $request->longitude;
			$request_data['user']['rating'] = $user->rate;
			$request_data['user']['num_rating'] = $user->rate_count;
			/* $request_data['user']['rating'] = DB::table('review_user')->where('user_id', '=', $user->id)->avg('rating') ? : 0;
			  $request_data['user']['num_rating'] = DB::table('review_user')->where('user_id', '=', $user->id)->count(); */
		}

		$response_code = 200;
		$response = Response::json($request_data, $response_code);
		return $response;
	}

	public function providerLogin() {
		return View::make('web.providerLogin');
	}

	public function providerActivation($act) {
		//verify the email activation
		if ($act) {
			$get_token = Provider::where('activation_code', '=', $act)->first();
			if ($get_token) {
				if ($get_token->email_activation == 1) {

					return View::make('web.providerLogin')->with('success', trans('user_provider_controller.mail_activated'));
				} else {
					$provider = Provider::find($get_token->id);
					$provider->email_activation = 1;
					$provider->save();

					if ($provider->save()) {
						return View::make('web.providerLogin')->with('success', trans('user_provider_controller.mail_activated'));
					} else {
						return View::make('web.providerLogin')->with('error', trans('user_provider_controller.something_wrong'));
					}
				}
			} else {
				return View::make('web.providerLogin')->with('error', trans('user_provider_controller.something_wrong'));
			}
		} else {
			return Redirect::to('provider/signup');
		}
	}

	public function providerRegister() {
		$treeServiceCategories = ProviderType::buildTreeData();

		$fields = array(
			'first_name'			=> '',
			'last_name'				=> '',
			'email'					=> '',
			'phone'					=> '+55',
			'car_number'			=> '',
			'car_brand'				=> '',
			'car_model'				=> '',
			'zipcode'				=> '',
			'address'				=> '',
			'address_number'		=> '',
			'address_complements'	=> '',
			'address_neighbour'		=> '',
			'address_city'			=> '',
			'state'					=> '',
			'country'				=> '',
		);
		

		return View::make('web.providerSignup')
						->with('treeServiceCategories', json_encode($treeServiceCategories))
						->with('fields', $fields);
	}

	//Salva provedor durante o cadastro 
	public function providerSave() {

		$first_name 	= Input::get('first_name');
		$last_name 		= Input::get('last_name');
		$email 			= Input::get('email');
		$password 		= Input::get('password');
		$phone 			= Input::get('phone');
		
		$car_number 	= Input::get('car_number');
		$car_brand 		= Input::get('car_brand');
		$car_model 		= Input::get('car_model');

		$zipcode 				= preg_replace("/(\D)/", "", Input::get('zipcode'));
		$address 				= Input::get('address');
		$address_number 		= Input::get('address_number');
		$address_complements 	= Input::get('address_complements');
		$address_neighbour 		= Input::get('address_neighbour');
		$address_city 			= Input::get('address_city');
		$state 					= Input::get('state');
		$country 				= Input::get('country');

		$treeData = json_decode(Input::get('provider_type'));

		//return $treeData ;

		$array_message_car = [];

		if (Input::has('car_number')) {
			$car_number = Input::get('car_number');

			//Inicia a validação da Placa do Carro
			$car_number_format = Settings::getCarNumberFormat();

			$car_number_letter = strlen(preg_replace("/.*?([a-zA-Z]*).*?/i", "$1", $car_number_format));
			$car_number_number = strlen(preg_replace("/.*?([0-9]*).*?/i", "$1", $car_number_format));

			$first_letter = substr($car_number_format,0,1);

			if(preg_match('/^[a-zA-Z]{1}$/', $first_letter)){
				if (!preg_match('/^[a-zA-Z]{' . $car_number_letter . '}\-?[0-9]{' . $car_number_number . '}$/', $car_number)) {
					$error_messages_car_number = trans('adminController.invalid_car_number'). $car_number_format;
					$array_message_car = array('message' => $error_messages_car_number);
				}
			} else {
				if (!preg_match('/^[0-9]{' . $car_number_number . '}\-?[a-zA-Z]{' . $car_number_letter . '}$/', $car_number)) {
					$error_messages_car_number = trans('adminController.invalid_car_number'). $car_number_format;
					$array_message_car = array('message' => $error_messages_car_number);
				}
			}
		}

		$validator = Validator::make(
			array(
				'first_name' => $first_name,
				'last_name' => $last_name,
				'email' => $email,
			), array(
				'first_name' => 'required',
				'last_name' => 'required',
				'email' => 'required|email|unique:provider,email,NULL,deleted_at',
			), array(
				'first_name' => trans('user_provider_controller.first_name_needed'),
				'last_name' => trans('user_provider_controller.last_name_needed'),
				'email.required' => trans('user_provider_controller.mail_needed'),
				'email.email' => trans('user_provider_controller.mail_invalid'),
				'email.unique' => trans('user_provider_controller.mail_unique'),
			)
		);
		$validatorPhone = Validator::make(
			array(
				'phone' => $phone,
			), array(
				'phone' => 'required|unique:provider,phone,NULL,id,country,'.$country,
			), array(
				'phone.required' => trans('user_provider_controller.phone_needed'),
				'phone.unique' => trans('user_provider_controller.phone_used'),
			)
		);
		$validatorPassword = Validator::make(
			array(
				'password' => $password,
			), array(
				'password' => 'required|min:6'
			), array(
				'password.required' => trans('user_provider_controller.password_needed'),
				'password.min' => trans('user_provider_controller.password_invalid'),
			)
		);

		$error_messages = array_merge(
			$validator->messages()->all(),
			$validatorPhone->messages()->all(),
			$validatorPassword->messages()->all(),
			$array_message_car
		);
		
		// Save filled fields in the form
		$fields = array(
			'first_name'			=> $first_name,
			'last_name'				=> $last_name,
			'email'					=> $email,
			'phone'					=> $phone,
			'car_number'			=> $car_number,
			'car_brand'				=> $car_brand,
			'car_model'				=> $car_model,
			'zipcode'				=> $zipcode,
			'address'				=> $address,
			'address_number'		=> $address_number,
			'address_complements'	=> $address_complements,
			'address_neighbour'		=> $address_neighbour,
			'address_city'			=> $address_city,
			'state'					=> $state,
			'country'				=> $country,
		);

		//Get all checked services and its respective categories

		if (count($error_messages) > 0) {
			$error_messages = join("<br />", $error_messages);
			return Redirect::to('provider/signup')
						->with('error', $error_messages )
						->with('fields', $fields)
						->with('treeServiceCategories', $treeData );
		} else {
			//Informacoes basicas do prestador
			$activation_code = uniqid();
			$provider = new Provider;
			$provider->first_name = $first_name;
			$provider->last_name = $last_name;
			$provider->email = $email;
			$provider->phone = $phone;
			$provider->activation_code = $activation_code;

			$provider->car_number = $car_number;
			$provider->car_model = $car_model;
			$provider->car_brand = $car_brand;

			$provider->zipcode = preg_replace("/(\D)/", "", $zipcode);
			$provider->address = $address;
			$provider->address_number = $address_number;
			$provider->address_complements = $address_complements;
			$provider->address_neighbour = $address_neighbour;
			$provider->address_city = $address_city;
			$provider->state = $state;
			$provider->country = $country;

			$providerStatus = ProviderStatus::where('name', 'EM_ANALISE')->first();
			$provider->status_id = $providerStatus->id;
			
			$provider->is_approved = false;
			$provider->is_available = 1;
			$provider->email_activation = 1;

			if ($password != "") {
				$provider->password = Hash::make($password);
			}

			$provider->token = generate_token();
			$provider->token_expiry = generate_expiry();

			$provider->timezone = "America/Sao_Paulo";
			if (Input::has('timezone')) {
				$provider->timezone = Input::get('timezone');
			}

			$provider->save();
			$provider_id = $provider->id ;
			//$provider_id = 113;
			// Create new price policy table based on the general spreadsheet


			$prices = ProviderServices::where('provider_id', 0)->get();
	
			foreach($treeData as $service){
				//dd($service->nodes);
				$countCategories = 0 ;
				$type_id = $service->href ;
				$providerType = ProviderType::find($type_id);
				//return $provider_type['categories'] ;
				
				if($providerType){

					if(isset($service->nodes)){

						foreach ($service->nodes as $node) {
							//dd($node);
							//return $category ;
							$category_id = $node->href ;
							// salvara se a categoria estiver selecionado
							if($node->state->selected){

								$providerServiceDefault = ProviderServices::findDefaultByTypeIdAndCategoryId($type_id, $category_id);
							
								$providerService = new ProviderServices;
								$providerService->provider_id 				= $provider_id ; 
								$providerService->type 						= $type_id; 
								$providerService->category					= $category_id ; 
								$providerService->price_per_unit_distance 	= $providerServiceDefault->price_per_unit_distance; 
								$providerService->price_per_unit_time 		= $providerServiceDefault->price_per_unit_time; 
								$providerService->base_price 				= $providerServiceDefault->base_price ; 
								$providerService->base_distance 			= $providerServiceDefault->base_distance ; 
								$providerService->base_time 				= $providerServiceDefault->base_time ; 
								$providerService->distance_unit 			= $providerServiceDefault->base_price ; 
								$providerService->time_unit 				= $providerServiceDefault->time_unit ; 
								$providerService->base_price_provider 		= $providerServiceDefault->base_price_provider ; 
								$providerService->base_price_user 			= $providerServiceDefault->base_price_user ; 
								$providerService->commission_rate 			= $providerServiceDefault->commission_rate ; 
								$providerService->is_visible				= $providerServiceDefault->is_visible ;


								$providerService->save();
								$countCategories++ ;
							}

						}
					}
					// faz uma simples associacao somente dos tipos principais
					if($countCategories == 0 && $service->state->selected) {

						$providerServiceDefault = ProviderServices::findDefaultByTypeIdAndCategoryId($type_id, 0);

						//dd($providerServiceDefault);

						$providerService = new ProviderServices;
						$providerService->provider_id 				= $provider_id ; 
						$providerService->type 						= $type_id; 
						$providerService->category					= 0 ; 
						$providerService->price_per_unit_distance 	= $providerServiceDefault->price_per_unit_distance; 
						$providerService->price_per_unit_time 		= $providerServiceDefault->price_per_unit_time; 
						$providerService->base_price 				= $providerServiceDefault->base_price ; 
						$providerService->base_distance 			= $providerServiceDefault->base_distance ; 
						$providerService->base_time 				= $providerServiceDefault->base_time ; 
						$providerService->distance_unit 			= $providerServiceDefault->base_price ; 
						$providerService->time_unit 				= $providerServiceDefault->time_unit ; 
						$providerService->base_price_provider 		= $providerServiceDefault->base_price_provider ; 
						$providerService->base_price_user 			= $providerServiceDefault->base_price_user ; 
						$providerService->commission_rate 			= $providerServiceDefault->commission_rate ; 
						$providerService->is_visible				= $providerServiceDefault->is_visible ;
						

						$providerService->save();
					}
				}

			}			
			
			
			$settings = Settings::where('key', 'admin_email_address')->first();
			$admin_email = $settings->value;
			$pattern = array('admin_eamil' => $admin_email, 'name' => ucwords($provider->first_name . " " . $provider->last_name), 'web_url' => web_url());
			$subject = trans('user_provider_controller.welcome') . ucwords(Config::get('app.website_title')) . ", " . ucwords($provider->first_name . " " . $provider->last_name) . "";
			email_notification($provider->id, 'provider', $pattern, $subject, 'provider_new_register', "imp");

			return Redirect::to('provider/signin')->with('success', trans('user_provider_controller.user_registered'));
		}
	}

	public function providerForgotPassword() {
		$email = Input::get('email');
		$provider = Provider::where('email', $email)->first();
		if ($provider) {
			$new_password = time();
			$new_password .= rand();
			$new_password = sha1($new_password);
			$new_password = substr($new_password, 0, 8);
			$provider->password = Hash::make($new_password);
			$provider->save();

			$settings = Settings::where('key', 'admin_email_address')->first();
			$admin_email = $settings->value;
			$login_url = web_url() . "/provider/signin";
			$pattern = array('name' => ucwords($provider->first_name . " " . $provider->last_name), 'admin_eamil' => $admin_email, 'new_password' => $new_password, 'login_url' => $login_url);
			$subject = trans('user_provider_controller.new_password');
			email_notification($provider->id, 'provider', $pattern, $subject, 'reset_password', 'imp');

			// echo $pattern;
			return Redirect::to('provider/signin')->with('success', trans('user_provider_controller.password_reset'));
		} else {
			return Redirect::to('provider/signin')->with('error', trans('user_provider_controller.mail_not_registered'));
		}
	}

	public function providerVerify() {
		$email = Input::get('email');
		$password = Input::get('password');
		$provider = Provider::where('email', '=', $email)->first();

		if ($provider) {
			if ($provider->email_activation == 1) {
				if ($provider && Hash::check($password, $provider->password)) {

					Session::put('provider_id', $provider->id);
					Session::put('is_approved', $provider->is_approved);
					Session::put('status', $provider->status->name);
					Session::put('provider_name', $provider->first_name . " " . $provider->last_name);
					Session::put('provider_pic', $provider->picture);
					//Session::save();
					return Redirect::to('provider/trips');
				} else {
					return Redirect::to('provider/signin')->with('error', trans('user_provider_controller.invalid_mail_pass'));
				}
			} else {
				return Redirect::to('provider/signin')->with('error', trans('user_provider_controller.activate_mail'));
			}
		} else {
			return Redirect::to('provider/signin')->with('error', trans('user_provider_controller.invalid_mail'));
		}
	}

	public function providerLogout() {
		Session::flush();
		return Redirect::to('/provider/signin');
	}

	public function providerTripChangeState() {
		$date = date("Y-m-d H:i:s");
		$time_limit = date("Y-m-d H:i:s", strtotime($date) - (3 * 60 * 60));
		$provider_id = Session::get('provider_id');
		$state = $request_id = Request::segment(4);
		$current_request = Requests::where('confirmed_provider', $provider_id)
				->where('is_cancelled', 0)
				->where('is_user_rated', 0)
				->where('created_at', '>', $time_limit)
				->orderBy('created_at', 'desc')
				->where(function($query) {
					$query->where('status', 0)->orWhere(function($query_inner) {
						$query_inner->where('status', 1)
						->where('is_user_rated', 0);
					});
				})
				->first();
		if ($current_request && $state) {

			if ($state == 2) {
				$current_request->is_provider_started = 1;

				$user = User::find($current_request->user_id);

				$provider = Provider::find($provider_id);
				$location = get_location($current_request->latitude, $current_request->longitude);
				$latitude = $location['lat'];
				$longitude = $location['long'];

				$angle = get_angle($provider->latitude, $provider->longitude, $latitude, $longitude);
				$provider->old_latitude = $provider->latitude;
				$provider->old_longitude = $provider->longitude;
				$provider->latitude = $latitude;
				$provider->longitude = $longitude;
				$provider->bearing = $angle;
				$provider->save();

				$request_location = new RequestLocation;
				$request_location->request_id = $current_request->id;
				$request_location->latitude = $latitude;
				$request_location->longitude = $longitude;
				$request_location->distance = 0;
				$request_location->save();
			}
			if ($state == 3) {
				$current_request->is_provider_arrived = 1;
			}
			if ($state == 4) {
				$current_request->is_started = 1;
			}

			if ($state == 6) {
				$rating = 0;
				if (Input::has('rating')) {
					$rating = Input::get('rating');
				}
				$current_request->is_user_rated = 1;
				$current_request->save();
				$review_user = new UserReview;
				$review_user->provider_id = $current_request->confirmed_provider;
				$review_user->comment = Input::get('review');
				$review_user->rating = $rating;
				$review_user->user_id = $current_request->user_id;
				$review_user->request_id = $current_request->id;
				$review_user->save();

				if ($rating) {
					if ($user = User::find($current_request->user_id)) {
						$old_rate = $user->rate;
						$old_rate_count = $user->rate_count;
						$new_rate_counter = ($user->rate_count + 1);
						$new_rate = (($user->rate * $user->rate_count) + $rating) / $new_rate_counter;
						$user->rate_count = $new_rate_counter;
						$user->rate = $new_rate;
						$user->save();
					}
				}

				$message = trans('user_provider_controller.success_rate');
				$type = "success";
				return Redirect::to('/provider/trips')->with('message', $message)->with('type', $type);
			}

			if ($state == 5) {
				$request_services = RequestServices::where('request_id', $current_request->id)->first();
				$request_typ = ProviderType::where('id', '=', $request_services->type)->first();

				$address = urlencode(Input::get('address'));
				$end_address = json_decode(file_get_contents("https://maps.googleapis.com/maps/api/geocode/json?address=$address"), TRUE);

				$end_location = $end_address['results'][0]['geometry'];
				$latitude = $end_location['location']['lat'];
				$longitude = $end_location['location']['lng'];

				$location = get_location($latitude, $longitude);
				$latitude = $location['lat'];
				$longitude = $location['long'];

				$request_id = $current_request->id;
				$request_location_last = RequestLocation::where('request_id', $request_id)->orderBy('created_at', 'desc')->first();

				if ($request_location_last) {
					$distance_old = $request_location_last->distance;
					$distance_new = distanceGeoPoints($request_location_last->latitude, $request_location_last->longitude, $latitude, $longitude);
					$distance = $distance_old + $distance_new;
					$settings = Settings::where('key', 'default_distance_unit')->first();
					$unit = $settings->value;
					$distance = $distance;
				} else {
					$distance = 0;
				}
				$provider = Provider::find($provider_id);

				$angle = get_angle($provider->latitude, $provider->longitude, $latitude, $longitude);
				$provider->old_latitude = $provider->latitude;
				$provider->old_longitude = $provider->longitude;
				$provider->latitude = $latitude;
				$provider->longitude = $longitude;
				$provider->bearing = $angle;
				$provider->save();

				$request_location = new RequestLocation;
				$request_location->request_id = $request_id;
				$request_location->latitude = $latitude;
				$request_location->longitude = $longitude;
				$request_location->distance = $distance;
				$request_location->save();

				Provider::where('id', '=', $provider_id)->update(array('is_available' => 1));

				// Calculate Rerquest Stats

				$time = 0;

				$time_query = "SELECT TIMESTAMPDIFF(SECOND,MIN(created_at),MAX(created_at)) as diff
				FROM request_location where request_id = $current_request->id
				GROUP BY request_id limit 1 ";

				$time_data = DB::select(DB::raw($time_query));
				foreach ($time_data as $time_diff) {
					$time = $time_diff->diff;
				}
				$time = $time / 60;

				/* TIME CALCULATION REDIRECTED */
				$time = 0;
				/* TIME CALCULATION REDIRECTED END */

				$provider_data = Provider::find($current_request->confirmed_provider);
				$provider_type = ProviderServices::where('type', $provider_data->type)->where('provider_id', $provider_id)->first();
				if ($provider_type == NULL) {
					/* $settings = Settings::where('key', 'price_per_unit_distance')->first();
					  $price_per_unit_distance = $settings->value;
					  $settings = Settings::where('key', 'price_per_unit_time')->first();
					  $price_per_unit_time = $settings->value;
					  $settings = Settings::where('key', 'base_price')->first();
					  $base_price = $settings->value; */
					$setbase_distance = $request_typ->base_distance;
					$base_price = $request_typ->base_price;
					$price_per_unit_distance = $request_typ->price_per_unit_distance;
					$price_per_unit_time = $request_typ->price_per_unit_time;
				} else {
					$setbase_distance = $request_typ->base_distance;
					$provider_type = ProviderServices::where('type', $provider_data->type)->where('provider_id', $provider_id)->first();
					$base_price = $provider_type->base_price;
					$price_per_unit_distance = $provider_type->price_per_unit_distance;
					$price_per_unit_time = $provider_type->price_per_unit_time;
				}

				$settings = Settings::where('key', 'default_charging_method_for_users')->first();
				$pricing_type = $settings->value;
				$settings = Settings::where('key', 'default_distance_unit')->first();
				$unit = $settings->value;
				$distance = convert($distance, $unit);
				if ($pricing_type == 1) {
					if ($distance <= $setbase_distance) {
						$distance_cost = 0;
					} else {
						$distance_cost = $price_per_unit_distance * ($distance - $setbase_distance);
					}
					$time_cost = $price_per_unit_time * $time;
					$total = $base_price + $distance_cost + $time_cost;
				} else {
					$distance_cost = 0;
					$time_cost = 0;
					$total = $base_price;
				}

				$current_request->is_completed = 1;
				$current_request->distance = $distance;
				$current_request->time = $time;
				$request_services->base_price = $base_price;
				$request_services->distance_cost = $distance_cost;
				$request_services->time_cost = $time_cost;
				$request_services->total = $total;
				$current_request->total = $total;
				$request_services->save();
				// charge client
				// charge client


				$ledger = Ledger::where('user_id', $current_request->user_id)->first();

				if ($ledger) {
					$balance = $ledger->amount_earned - $ledger->amount_spent;
					if ($balance > 0) {
						if ($total > $balance) {
							$ledger_temp = Ledger::find($ledger->id);
							$ledger_temp->amount_spent = $ledger_temp->amount_spent + $balance;
							$ledger_temp->save();
							$total = $total - $balance;
						} else {
							$ledger_temp = Ledger::find($ledger->id);
							$ledger_temp->amount_spent = $ledger_temp->amount_spent + $total;
							$ledger_temp->save();
							$total = 0;
						}
					}
				}

				$promo_discount = 0;
				if ($pcode = PromoCodes::where('id', $current_request->promo_code)->where('type', 1)->first()) {
					$discount = ($pcode->value) / 100;
					$promo_discount = $total * $discount;
					$total = $total - $promo_discount;
					if ($total < 0) {
						$total = 0;
					}
				}
				$current_request->total = $total;
				$current_request->save();

				$cod_sett = Settings::where('key', 'payment_money')->first();
				$allow_cod = $cod_sett->value;
				if ($current_request->payment_mode == 1 and $allow_cod == 1) {
					// Pay by Cash
					$current_request->is_paid = 1;
					//Log::info('allow_cod');
				} elseif ($current_request->payment_mode == 2) {
					// paypal
					//Log::info('paypal payment');
				} else {
					//Log::info('normal payment. Stored cards');
					// stored cards
					if ($total == 0) {
						$current_request->is_paid = 1;
					} else {
						$payment_data = Payment::where('user_id', $current_request->user_id)->where('is_default', 1)->first();
						if (!$payment_data)
							$payment_data = Payment::where('user_id', $current_request->user_id)->first();

						if ($payment_data) {
							$customer_id = $payment_data->customer_id;

							$setransfer = Settings::where('key', 'transfer')->first();
							$transfer_allow = $setransfer->value;
							if (Config::get('app.default_payment') == 'stripe') {
								//dd($customer_id);
								Stripe::setApiKey(Config::get('app.stripe_secret_key'));

								try {
									$charge = Stripe_Charge::create(array(
												"amount" => ceil($total * 100),
												"currency" => "usd",
												"customer" => $customer_id)
									);
									//Log::info($charge);
								} catch (Stripe_InvalidRequestError $e) {
									// Invalid parameters were supplied to Stripe's API
									$ownr = User::find($current_request->user_id);
									$ownr->debt = $total;
									$ownr->save();
									$message = array('error' => $e->getMessage());
									$type = "success";
									//Log::info($message);
									return Redirect::to('/provider/tripinprogress')->with('message', $message)->with('type', $type);
								}
								$current_request->is_paid = 1;
								$settng = Settings::where('key', 'service_fee')->first();
								$settng_mode = Settings::where('key', 'payment_mode')->first();
								if ($settng_mode->value == 2 and $transfer_allow == 1) {
									$transfer = Stripe_Transfer::create(array(
												"amount" => ($total - ($settng->value * $total / 100)) * 100, // amount in cents
												"currency" => "usd",
												"recipient" => $provider_data->merchant_id)
									);
									$current_request->transfer_amount = ($total - ($settng->value * $total / 100));
								}
							} else {
								try {
									Braintree_Configuration::environment(Config::get('app.braintree_environment'));
									Braintree_Configuration::merchantId(Config::get('app.braintree_merchant_id'));
									Braintree_Configuration::publicKey(Config::get('app.braintree_public_key'));
									Braintree_Configuration::privateKey(Config::get('app.braintree_private_key'));
									if ($settng_mode->value == 2 and $transfer_allow == 1) {
										$sevisett = Settings::where('key', 'service_fee')->first();
										$service_fee = $sevisett->value * $total / 100;
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

									if ($result->success) {
										$request->is_paid = 1;
									} else {
										$request->is_paid = 0;
									}
								} catch (Exception $e) {
									$message = trans('user_provider_controller.payment_wrong');
									$type = "success";
									return Redirect::to('/provider/tripinprogress')->with('message', $message)->with('type', $type);
								}
							}
							$current_request->card_payment = $total;
							$current_request->ledger_payment = $current_request->total - $total;
						}
					}
				}
				$current_request->save();
			}
			$current_request->save();
		}
		return Redirect::to('/provider/tripinprogress');
	}

	public function providerTripInProgress() {
		$date = date("Y-m-d H:i:s");
		$time_limit = date("Y-m-d H:i:s", strtotime($date) - (3 * 60 * 60));
		$provider_id = Session::get('provider_id');

		$current_request = Requests::where('confirmed_provider', $provider_id)
				->where('is_cancelled', 0)
				->where('created_at', '>', $time_limit)
				->orderBy('created_at', 'desc')
				->where(function($query) {
					$query->where('status', 0)->orWhere(function($query_inner) {
						$query_inner->where('status', 1);
					});
				})
				->first();

		if (!$current_request or Session::has('skipReviewProvider') or $current_request->is_user_rated == 1) {
			/* $var = Keywords::where('id', 4)->first();
			  $message = "You don't have any " . $var->keyword . "s currently in progress."; */
			$message = trans('user_provider_controller.have_any') . trans('customize.Trip') . trans('user_provider_controller.in_progress');
			$type = "danger";
			$status = 6;
			return Redirect::to('/provider/trips')->with('message', $message)->with('type', $type)->with('status', $status);
		} else {
			$request_services = RequestServices::where('request_id', $current_request->id)->first();
			$user = User::find($current_request->user_id);
			$type = ProviderType::find($request_services->type);
			$status = 0;

			if ($current_request->is_user_rated) {
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
			}

			if ($current_request->confirmed_provider) {
				$provider = Provider::find($current_request->confirmed_provider);
				/* $rating = DB::table('review_user')->where('user_id', '=', $current_request->user_id)->avg('rating') ? : 0; */
				$rating = $user->rate;
				/* $var = Keywords::where('id', 4)->first(); */

				return View::make('web.providerRequestTripStatus')
								->with('title', trans('user_provider_controller.status_of') . trans('customize.Trip'))
								->with('page', '' . trans('customize.Trip') . '-status')
								->with('request', $current_request)
								->with('user', $user)
								->with('provider', $provider)
								->with('type', $type)
								->with('status', $status)
								->with('rating', $rating);
			}
		}
	}

	public function providerSkipReview() {
		$request_id = Request::segment(3);
		Session::put('skipReviewProvider', 1);
		return Redirect::to('/provider/tripinprogress');
	}

	public function approve_request() {
		$request_id = Request::segment(4);
		$provider_id = Session::get('provider_id');
		$request = Requests::find($request_id);
		if ($request->current_provider == $provider_id) {
			// request ended
			Requests::where('id', '=', $request_id)->update(array('confirmed_provider' => $provider_id, 'status' => 1, 'request_start_time' => date('Y-m-d H:i:s')));

			// confirm provider
			RequestMeta::where('request_id', '=', $request_id)->where('provider_id', '=', $provider_id)->update(array('status' => 1));

			// Update Provider availability

			Provider::where('id', '=', $provider_id)->update(array('is_available' => 0));

			// remove other schedule_meta
			RequestMeta::where('request_id', '=', $request_id)->where('status', '=', 0)->delete();

			// Send Notification
			$provider = Provider::find($provider_id);
			$provider_data = array();
			$provider_data['first_name'] = $provider->first_name;
			$provider_data['last_name'] = $provider->last_name;
			$provider_data['phone'] = $provider->phone;
			$provider_data['bio'] = $provider->bio;
			$provider_data['picture'] = $provider->picture;
			$provider_data['latitude'] = $provider->latitude;
			$provider_data['longitude'] = $provider->longitude;
			$provider_data['rating'] = $provider->rate;
			$provider_data['num_rating'] = $provider->rate_count;
			$provider_data['car_model'] = $provider->car_model;
			$provider_data['car_number'] = $provider->car_number;
			/* $provider_data['rating'] = DB::table('review_provider')->where('provider_id', '=', $provider->id)->avg('rating') ? : 0;
			  $provider_data['num_rating'] = DB::table('review_provider')->where('provider_id', '=', $provider->id)->count(); */

			$settings = Settings::where('key', 'default_distance_unit')->first();
			$unit = $settings->value;
			$bill = array();
			if ($request->is_completed == 1) {

				$bill['distance'] = convert($request->distance, $unit);
				$bill['time'] = $request->time;
				$bill['base_price'] = $request->base_price;
				$bill['distance_cost'] = $request->distance_cost;
				$bill['time_cost'] = $request->time_cost;
				$bill['total'] = $request->total;
				$bill['is_paid'] = $request->is_paid;
			}

			$response_array = array(
				'success' => true,
				'request_id' => $request_id,
				'status' => $request->status,
				'confirmed_provider' => $request->confirmed_provider,
				'is_provider_started' => $request->is_provider_started,
				'is_provider_arrived' => $request->is_provider_arrived,
				'is_request_started' => $request->is_started,
				'is_completed' => $request->is_completed,
				'is_provider_rated' => $request->is_provider_rated,
				'provider' => $provider_data,
				'bill' => $bill,
			);
			/* $var = Keywords::where('id', 1)->first();
			  $title = "" . $var->keyword . " Accepted"; */
			$title = "" . trans('customize.Provider') . " ". trans('user_provider_controller.accepted');
			$message = $response_array;
			send_notifications($request->user_id, "user", $title, $message);

			// Send SMS 
			$user = User::find($request->user_id);
			$settings = Settings::where('key', 'sms_when_provider_accepts')->first();
			$pattern = $settings->value;
			$pattern = str_replace('%user%', $user->first_name . " " . $user->last_name, $pattern);
			$pattern = str_replace('%driver%', $provider->first_name . " " . $provider->last_name, $pattern);

			$pattern = str_replace('%driver_mobile%', $provider->phone, $pattern);
			sms_notification($request->user_id, 'user', $pattern);

			// Send SMS 

			$settings = Settings::where('key', 'sms_request_completed')->first();
			$pattern = $settings->value;
			$pattern = str_replace('%user%', $user->first_name . " " . $user->last_name, $pattern);
			$pattern = str_replace('%id%', $request->id, $pattern);
			$pattern = str_replace('%user_mobile%', $user->phone, $pattern);
			sms_notification(1, 'admin', $pattern);
		}

		return Redirect::to('/provider/tripinprogress');
	}

	public function decline_request() {
		$request_id = Request::segment(4);
		$provider_id = Session::get('provider_id');
		$request = Requests::find($request_id);
		if ($request->current_provider == $provider_id) {
			// Archiving Old Provider
			RequestMeta::where('request_id', '=', $request_id)->where('provider_id', '=', $provider_id)->update(array('status' => 3));
			$request_meta = RequestMeta::where('request_id', '=', $request_id)->where('status', '=', 0)->orderBy('created_at')->first();

			// update request
			if (isset($request_meta->provider_id)) {
				// assign new provider
				Requests::where('id', '=', $request_id)->update(array('current_provider' => $request_meta->provider_id, 'request_start_time' => date("Y-m-d H:i:s")));

				// Send Notification

				$provider = Provider::find($request_meta->provider_id);
				$user_data = User::find($request->user_id);
				$msg_array = array();
				$msg_array['request_id'] = $request->id;
				$msg_array['id'] = $request_meta->provider_id;
				if ($provider) {
					$msg_array['token'] = $provider->token;
				}
				$msg_array['client_profile'] = array();
				$msg_array['client_profile']['name'] = $user_data->first_name . " " . $user_data->last_name;
				$msg_array['client_profile']['picture'] = $user_data->picture;
				$msg_array['client_profile']['bio'] = $user_data->bio;
				$msg_array['client_profile']['address'] = $user_data->address;
				$msg_array['client_profile']['phone'] = $user_data->phone;

				$title = trans('user_provider_controller.new_request');
				$message = $msg_array;
				send_notifications($request_meta->provider_id, "provider", $title, $message);
			} else {
				// request ended
				Requests::where('id', '=', $request_id)->update(array('current_provider' => 0, 'status' => 1));
			}
		}
		return Redirect::to('/provider/trips');
	}

	public function providerTrips() {
		$start_date = Input::get('start_date');
		$end_date = Input::get('end_date');       
		$submit = Input::get('submit');

		$start_time = date("Y-m-d H:i:s", strtotime($start_date));
		$end_time = date("Y-m-d H:i:s", strtotime($end_date) + 86400);
		//$end_time->modify('+1 day');
		$start_date = date("Y-m-d", strtotime($start_date));
		$end_date = date("Y-m-d", strtotime($end_date));

	   // if (!Input::get('start_date') && !Input::get('end_date')) {
		if (!Input::get('start_date')) {

			$provider_id = Session::get('provider_id');
			$requests = Requests::where('confirmed_provider', $provider_id)
					->where('is_completed', 1)
					->leftJoin('provider', 'provider.id', '=', 'request.confirmed_provider')
					->leftJoin('request_services', 'request_services.request_id', '=', 'request.id')
					->leftJoin('provider_type', 'provider_type.id', '=', 'request_services.type')
					->leftJoin('user', 'user.id', '=', 'request.user_id')
					->orderBy('request_start_time', 'desc')
					->select('request.id', 'request_start_time', 'user.first_name', 'user.last_name', 'request.total as total', 'provider_type.name as type', 'request.distance', 'request.time', 'request.user_id')
					->get();

			$total_rides = Requests::where('confirmed_provider', $provider_id)
					->where('is_completed', 1)
					->count();

			$total_distance = Requests::where('confirmed_provider', $provider_id)
					->where('is_completed', 1)
					->sum('distance');

			$settings = Settings::where('key', 'default_distance_unit')->first();
			$unit = $settings->value;

			$total_earnings = Requests::where('confirmed_provider', $provider_id)
					->where('is_completed', 1)
					->sum('total');

			/* $average_rating = UserReview::where('provider_id', $provider_id)
			  ->avg('rating'); */
			$rating_avg = Provider::where('id', $provider_id)->first();
			$average_rating = $rating_avg->rate;
		} else {

			$provider_id = Session::get('provider_id');
			$requests = Requests::where('confirmed_provider', $provider_id)
					->where('is_completed', 1)
					->where('request_start_time', '>=', $start_time)
					->where('request_start_time', '<=', $end_time)
					->leftJoin('provider', 'provider.id', '=', 'request.confirmed_provider')
					->leftJoin('provider_type', 'provider_type.id', '=', 'provider.type')
					->leftJoin('user', 'user.id', '=', 'request.user_id')
					->orderBy('request_start_time', 'desc')
					->select('request.id', 'request_start_time', 'user.first_name', 'user.last_name', 'request.total as total', 'provider_type.name as type', 'request.distance', 'request.time', 'request.user_id')
					->get();

			$total_rides = Requests::where('confirmed_provider', $provider_id)
					->where('is_completed', 1)
					->where('request_start_time', '=', $start_time)
					->where('request_start_time', '<=', $end_time)
					->count();

			$total_distance = Requests::where('confirmed_provider', $provider_id)
					->where('is_completed', 1)
					->where('request_start_time', '>=', $start_time)
					->where('request_start_time', '<=', $end_time)
					->sum('distance');

			$total_earnings = Requests::where('confirmed_provider', $provider_id)
					->where('is_completed', 1)
					->where('request_start_time', '>=', $start_time)
					->where('request_start_time', '<=', $end_time)
					->sum('total');

			/* $average_rating = UserReview::where('provider_id', $provider_id)
			  ->where('created_at', '>=', $start_time)
			  ->where('created_at', '<=', $end_time)
			  ->avg('rating'); */
			$rating_avg = Provider::where('id', $provider_id)->first();
			$average_rating = $rating_avg->rate;
		}

		if (!Input::get('submit') || Input::get('submit') == 'filter') {
			/* $var = Keywords::where('id', 4)->first(); */
			/* $currency = Keywords::where('id', 5)->first(); */
			$provider_id = Session::get('provider_id');
			$count_bank_account = ProviderBankAccount::where('provider_id', $provider_id)->count();

			return View::make('web.providerTrips')
							/* ->with('title', 'My ' . $var->keyword . 's') */
							->with('title', trans('user_provider_controller.my_service'))
							->with('requests', $requests)
							->with('total_rides', $total_rides)
							/* ->with('currency', $currency->keyword) */
							->with('currency', Config::get('app.generic_keywords.Currency'))
							->with('total_distance', $total_distance)
							->with('total_earnings', $total_earnings)
							->with('average_rating', $average_rating)
							->with('count_bank_account', $count_bank_account);
		} else {

			header('Content-Type: text/csv; charset=utf-8');
			header('Content-Disposition: attachment; filename=data.csv');

			$handle = fopen('php://output', 'w');
			fputcsv($handle, array(trans('user_provider_controller.Date'), trans('user_provider_controller.Cliente_name'), trans('user_provider_controller.Service_type'), trans('user_provider_controller.Distance'), trans('user_provider_controller.time'), trans('user_provider_controller.earning')));

			foreach ($requests as $request) {
				setlocale(LC_ALL, 'pt_BR', 'pt_BR.utf-8', 'pt_BR.utf-8', 'portuguese');
				date_default_timezone_set('America/Sao_Paulo');

				fputcsv($handle, array(strftime('%d de %B, %Y', strtotime($request->request_start_time)), $request->first_name . " " . $request->last_name, $request->type, $request->distance, $request->time, $request->total));
				//fputcsv($handle, array(date('l, F d Y h:i A', strtotime($request->request_start_time)), $request->first_name . " " . $request->last_name, $request->type, $request->distance, $request->time, $request->total));
			}

			fputcsv($handle, array());
			fputcsv($handle, array());
			fputcsv($handle, array(trans('user_provider_controller.total_ride'), $total_rides));
			fputcsv($handle, array(trans('user_provider_controller.total_distance'), $total_distance));
			fputcsv($handle, array(trans('user_provider_controller.average_rate'), $average_rating));
			fputcsv($handle, array(trans('user_provider_controller.total_earning'), $total_earnings));

			fclose($handle);
		}
	}

	public function providerTripDetail() {
		$id = Request::segment(3);

		$provider_id = Session::get('provider_id');
		$request = Requests::find($id);
		$request_services = RequestServices::where('request_id', $request->id)->first();
		if ($request->confirmed_provider == $provider_id) {
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

			$user = User::find($request->user_id);
			$user_review = UserReview::where('request_id', $id)->first();
			if ($user_review) {
				$rating = round($user_review->rating);
			} else {
				$rating = 0;
			}
			/* $var = Keywords::where('id', 4)->first(); */
			/* $currency = Keywords::where('id', 5)->first(); */

			return View::make('web.providerTripDetail')
							/* ->with('title', 'My ' . $var->keyword . 's') */
							->with('title', trans('user_provider_controller.my_trips'))
							->with('request', $request)
							->with('request_services', $request_services)
							->with('start_address', $start_address)
							->with('end_address', $end_address)
							/* ->with('currency', $currency->keyword) */
							->with('currency', Config::get('app.generic_keywords.Currency'))
							->with('start', $start)
							->with('end', $end)
							->with('map_url', $map)
							->with('user', $user)
							->with('rating', $rating);
		} else {
			echo "false";
		}
	}

	public function providerProfile() {
		$provider_id = Session::get('provider_id');
		$user = Provider::find($provider_id);
		$type = ProviderType::where('is_visible', '=', 1)->get();
		$ps = ProviderServices::where('provider_id', $provider_id)->get();
		//$provider_type = ProviderType::find($user->type);

		return View::make('web.providerProfile')
						->with('title', trans('user_provider_controller.my_profile'))
						->with('user', $user)
						->with('type', $type)                       
						->with('ps', $ps);
	}

	public function updateProviderProfile() {
		$provider_id = Session::get('provider_id');
		$first_name = Input::get('first_name');
		$last_name = Input::get('last_name');
		$phone_country = Input::get('phone-ddi');
		$phone = Input::get('phone');
	
		$picture = Input::file('picture');

		$bio = Input::get('bio');

		$car_number = trim(Input::get('car_number'));
		$car_model = trim(Input::get('car_model'));
		$car_brand = trim(Input::get('car_brand'));

		$zipcode = preg_replace("/(\D)/", "", Input::get('zipcode'));
		$address = Input::get('address');
		$address_number = Input::get('address_number');
		$address_complements = Input::get('address_complements');
		$address_neighbour = Input::get('address_neighbour');
		$address_city = Input::get('address_city');
		$state = Input::get('state');
		$country = Input::get('country');
		
		$timezone = Input::get('timezone');
		
		//Get all checked services
		$provider_types_id_array = Input::get('service');

		$validator = Validator::make(
			array(
				'first_name' => $first_name,
				'last_name' => $last_name,
				'phone' => $phone,
			), array(
				'last_name' => 'required',
				'first_name' => 'required',
				'phone' => 'required|unique:provider,phone,'.$provider_id,
			), array(
				'last_name' => trans('user_provider_controller.last_name_needed'),
				'first_name' => trans('user_provider_controller.first_name_needed'),
				'phone.required' => trans('user_provider_controller.phone_needed'),
				'phone.unique' => trans('user_provider_controller.phone_used'),
			)
		);

		$validatorPicture = Validator::make(
			array(
				trans('user_provider_web.picture') => $picture,
			), array(	
				/* trans('user_provider_web.picture') => 'mimes:jpeg,bmp,png' */
				trans('user_provider_web.picture') => '',
			), array(
				/* trans('user_provider_web.picture') => 'mimes:jpeg,bmp,png' */
				trans('user_provider_web.picture') => trans('user_provider_controller.image_needed')
			)
		);

		if ($validator->fails()) {
			$error_messages = $validator->messages();
			return Redirect::to('/provider/profile')->with('error', trans('user_provider_controller.all_fields'));
		}elseif ($validatorPicture->fails()) {
			$error_messages = $validator->messages();
			return Redirect::to('/provider/profile')->with('error', trans('user_provider_controller.image_not_allow'));
		} else {

			$provider = Provider::find($provider_id);

			if (Input::hasFile('picture')) {
				
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

					$ext = Input::file('picture')->getClientOriginalExtension();

					$filepath = public_path() . "/uploads/" . $file_name . "." . $ext; // or image.jpg

					// Save the image in a defined path
					file_put_contents($filepath, $data);
				}

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
					$s3_url = asset_url() . '/uploads/' . $file_name . "." . $ext;
				}

				if (isset($provider->picture)) {
					if ($provider->picture != "") {
						$icon = $provider->picture;
						unlink_image($icon);
					}
				}
				
				$provider->picture = $s3_url;
			}

			if ($car_number != "") {
				$provider->car_number = $car_number;
			}
			if ($car_model != "") {
				$provider->car_model = $car_model;
			}
			$provider->car_brand = $car_brand;
			$provider->first_name = $first_name;
			$provider->last_name = $last_name;

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
			
			$provider->timezone = $timezone;
			$provider->save();
			if (Input::has('service')) {
				foreach (Input::get('service') as $ke) {
					$proviserv = ProviderServices::where('provider_id', $provider->id)->first();
					if ($proviserv != NULL) {
						DB::delete("delete from provider_services where provider_id = '" . $provider->id . "';");
					}
				}
			}
			$base_price = Input::get('service_base_price');
			$service_price_distance = Input::get('service_price_distance');
			$service_price_time = Input::get('service_price_time');

			$cnkey = count(Input::get('service'));
			$type_id = trim((Input::get('service')[0]));
		
			$key = Input::get('service');

			 for ($i = 0; $i < $cnkey; $i++) { 
			 if (isset($key[$i])) {
			  $prserv = new ProviderServices;
			  $prserv->provider_id = $provider->id;
			  $prserv->type = $key[$i];
			  //Log::info('key = ' . print_r($key, true));
			  if (Input::has('service_base_price')) {
			  $prserv->base_price = $base_price[$i];
			  } else {
			  $prserv->base_price = 0;
			  }
			  if (Input::has('service_price_distance')) {

			  $prserv->price_per_unit_distance = $service_price_distance[$i];
			  } else {
			  $prserv->price_per_unit_distance = 0;
			  }
			  if (Input::has('service_price_time')) {
			  $prserv->price_per_unit_time = $service_price_time[$i];
			  } else {
			  $prserv->price_per_unit_time = 0;
			  }
			  $prserv->save();
			  } 
			} 
			  
			
			return Redirect::to('/provider/profile')->with('message', trans('user_provider_controller.profile_success_update'))->with('type', 'success');
		}
	}

	public function updateProviderPassword() {

		$current_password = Input::get('current_password');
		$new_password = Input::get('new_password');
		$confirm_password = Input::get('confirm_password');

		$provider_id = Session::get('provider_id');
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
		 	return Redirect::to('/provider/profile')->withErrors($validator->messages());
		}
		elseif ($new_password == $confirm_password) {

			//return 435;

			if ($provider && Hash::check($current_password, $provider->password)) {
				$password = Hash::make($new_password);
				$provider->password = $password;
				$provider->save();

				$message = trans('user_provider_controller.password_updated');
				$type = "success";
			} 
			else {
				$message = trans('user_provider_controller.password_old_wrong');
				$type = "error";
			}
		 	
		 	return Redirect::to('/provider/profile')
		 						->with('flash_message', $message)
		 						->with('flash_type', $type);
		} else {
			//return 12312313;
			$message = trans('user_provider_controller.password_dont_match');
			$type = "error";

			return Redirect::to('/provider/profile')
							->with('flash_message', $message)
							->with('flash_type', $type);
		}
		
	}

	public function providerBankAccount(){
		$provider_id = Session::get('provider_id');
		$bank_account = ProviderBankAccount::where('provider_id', $provider_id)->first();
		$banks  = Bank::all();

		return View::make('web.providerBankAccount')
						->with('title', trans('user_provider_web.bank_account'))
						->with('bank_account', $bank_account)
						->with('banks', $banks);
	}

	public function updateProviderBankAccount() {

		$provider_id 	= Session::get('provider_id');
		$holder 		= Input::get('holder');
		$document 		= Input::get('document');
		$bank_id 		= Input::get('bank_id'); 
		$agency 		= Input::get('agency');
		$account 		= Input::get('account');
		$account_digit 	= Input::get('account_digit');
		$option_document = Input::get('option_document');

		$validator = Validator::make(
						array(
							'holder' => $holder,
							'document' => $document,
							'bank_id' => $bank_id,
							'agency' => $agency,
							'account' => $account,
							'account_digit' => $account_digit
						),
						array(
							'holder' => 'required',
							'document' => 'required',
							'bank_id' => 'required',
							'agency' => 'required',
							'account' => 'required',
							'account_digit' => 'required'
						),
						array(
							'holder' => trans('user_provider_controller.holder_required'),
							'document' => trans('user_provider_controller.document_required'),
							'bank_id' => trans('user_provider_controller.bank_required'),
							'agency' => trans('user_provider_controller.agency_required'),
							'account' => trans('user_provider_controller.account_required'),
							'account_digit' => trans('user_provider_controller.account_digit_required'),
						));

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


		$error_messages = $validator->messages();
		//return $error_messages ;
		if ($validator->fails()) {	
			return Redirect::to('/provider/bank_account')
						->withErrors($error_messages);
		}
		else if($validatorDocument->fails()){
			$error_messages = $validatorDocument->messages();
			return Redirect::to('/provider/bank_account')
						->withErrors($error_messages);
		}
		else {
			
			$bank = Bank::where('id', $bank_id)->first();
			$settingTransferInterval = Settings::where('key', 'provider_transfer_interval')->first();
			$settingTransferDay = Settings::where('key', 'provider_transfer_day')->first();
			$provider_bank_account = ProviderBankAccount::where('provider_id', $provider_id)->first();
			if(!$provider_bank_account){
				$provider_bank_account = new ProviderBankAccount();
			}

			//atualizar informações no Pagar.me
			if(Config::get('app.default_payment') == 'pagarme'){
				$payment_token = Input::get('card_hash');

			    PagarMe::setApiKey(Config::get('app.pagarme_api_key'));

		    	try{
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
				}
				catch(PagarMe_Exception $ex){
					//return $ex ;
					return Redirect::to('/provider/bank_account')
							->withErrors([$ex->getMessage()]);
				}

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

				return Redirect::to('/provider/bank_account')->with('success', trans('user_provider_controller.bank_account_success_update'));
	    	}
	    	else {
	    		return Redirect::to('/provider/bank_account')->withErrors([trans('user_provider_controller.pagarme_not_defined')]);
	    	}

			
		}
	}

	public function providerDocuments() {
		$provider_id = Session::get('provider_id');
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

		return View::make('web.providerDocuments')
			->with('title', trans('user_provider_controller.my_doc'))
			->with('documents', $documents)
			->with('provider_document', $provider_document)
			->with('status', $status);
	}

	public function providerUpdateDocuments() {
		$inputs = Input::all();
		$provider_id = Session::get('provider_id');


		foreach ($inputs as $key => $input) {
			$provider_document = ProviderDocument::where('provider_id', $provider_id)->where('document_id', $key)->first();
			if (!$provider_document) {
				$provider_document = new ProviderDocument;
			}
			$provider_document->provider_id = $provider_id;
			$provider_document->document_id = $key;

			if ($input) {
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

				/* if ($provider_document->save()) {
				  echo 'asdasd';
				  } */
			}
		}

		$message = trans('user_provider_controller.doc_updated');
		$type = "success";
		return Redirect::to('/provider/documents')->with('message', $message)->with('type', $type);
	}
	
	/**
	 *  Setup and call Price Policy View
	 *
	 *  @return void
	 */
	public function providerPricePolicy() {
		$id = Session::get('provider_id');
		
		$providerTypes = ProviderType::where('is_visible', '=', true)->get();
		$prices = ProviderServices::where('provider_id', '=', $id)->get();

		$filteredPrices = $prices->filter(function ($item) {
			return ($item && $item->getType && $item->getTypeCategory && ($item->getType->is_visible == true));
		});
		
		$filteredPrices->all();
		
        return View::make('web.providerPricePolicy')
			->with('title', trans('user_provider_controller.price_policy'))
			->with('providerTypes', $providerTypes)
			->with('prices', $filteredPrices);
	}
	
	/**
	 *  Modify the Provider Price Policy spreadsheet
	 *
	 *  @return void
	 */
	public function update_providerPricePolicy() {
		$provider_id = Session::get('provider_id');
		
		if ($provider_id <= 0) {
			return Redirect::to("/provider/price-policy");
		}
		
		//Get all checked provider_types and its respective provider_type_categories
		$provider_types_id_array = Input::get('provider_type');
		$provider_type_categories_id_array = array();
		
		foreach ($provider_types_id_array as $provider_type) {
			$provider_type_categories_id_array[$provider_type] = Input::get('provider_type_category_' . $provider_type);
		}
		
		//Update the spreadsheet values
		$prices = ProviderServices::where('provider_id', '=', $provider_id)->get();
		
		$filteredPrices = $prices->filter(function ($item) {
			return $item->getType->is_visible == true;
		});
		
		$filteredPrices->all();
		
		foreach ($filteredPrices as $filteredPrice) {
			//If service/category is checked, set visibility true. Else, set visibility false
			if ( in_array($filteredPrice->type, $provider_types_id_array) &&
				 in_array($filteredPrice->category, $provider_type_categories_id_array[$filteredPrice->type])) {
				$filteredPrice->is_visible = true;
			} else {
				$filteredPrice->is_visible = false;
			}
			
			$filteredPrice->save();
		}
		
		return Redirect::to("/provider/price-policy");
	}

	public function provideravailabilitySubmit() {

		$proavis = $_POST['proavis'];
		$proavie = $_POST['proavie'];
		$length = $_POST['length'];
		$provid = Session::get('provider_id');
		//Log::info('Start end time Array Length = ' . print_r($length, true));
		DB::delete("delete from provider_availability where provider_id = '" . $provid . "';");
		for ($l = 0; $l < $length; $l++) {
			$pv = new ProviderAvail;
			$pv->provider_id = $provid;
			$pv->start = $proavis[$l];
			$pv->end = $proavie[$l];
			$pv->save();
		}
		//Log::info('providers availability start = ' . print_r($proavis, true));
		//Log::info('providers availability end = ' . print_r($proavie, true));
		return Response::json(array('success' => true));
	}

	public function provideravailability() {


		if (Session::has('provider_id')) {
			$pavail = ProviderAvail::where('provider_id', Session::get('provider_id'))->get();
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
			return View::make('web.provideravailability')->with('pvjson', $pvjson)->with('title', 'Calendar')->with('page', 'yo');
		}
	}

	//create manual request

	public function create_manual_request() {
		$latitude = Input::get('latitude');
		$longitude = Input::get('longitude');
		$d_latitude = Input::get('d_latitude');
		$d_longitude = Input::get('d_longitude');
		$type = Input::get('type');
		$provider = Input::get('provider');
		$user_id = Session::get('user_id');

		$time = date("Y-m-d H:i:s");

		$provider_details = Provider::where('id', '=', $provider)->first();

		$user = User::where('id', '=', $user_id)->first();

		$request = new Requests;
		$request->user_id = $user_id;
		$request->request_start_time = $time;
		$request->confirmed_provider = $provider;
		if ($d_longitude != '' && $d_latitude != '') {
			$request->D_latitude = $d_latitude;
			$request->D_longitude = $d_longitude;
		}
		$request->current_provider = $provider;
		$request->status = 1;
		$request->latitude = $latitude;
		$request->longitude = $longitude;
		$request->save();
		$reqid = $request->id;

		$request_service = new RequestServices;
		$request_service->type = $type;
		$request_service->request_id = $request->id;
		$request_service->save();

		$user = User::find($user_id);
		$user->latitude = $latitude;
		$user->longitude = $longitude;
		$user->save();

		$providerlocation = new RequestLocation;
		$providerlocation->request_id = $request->id;
		$providerlocation->distance = 0.00;
		$providerlocation->latitude = $latitude;
		$providerlocation->longitude = $longitude;
		$providerlocation->save();


		if ($request->save()) {

			$current_request = Requests::where('id', '=', $reqid)->first();

			return Redirect::to('/user/request-trip');
		}
	}

	// getting near by users

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

	   // return $providers;

		foreach ($providers as $key) {
			echo "<option value=" . $key->id . ">" . $key->first_name . " " . $key->last_name . "</option>";
		}

	}

	public function requests_payment() {
		$provider_id = Session::get('provider_id');
		$requests = DB::table('request')
				->leftJoin('provider', 'request.confirmed_provider', '=', 'provider.id')
				->select(DB::raw('SUM(request.card_payment)as total, SUM(request.total)as total2,COUNT(request.id)as trips,request.created_at,request.id, WEEK(request.created_at,1) as payoutweek'))
				->where('request.status', '=', 1)
				->where('request.is_completed', '=', 1)
				->where('provider.id', '=', $provider_id)
				->groupBy('payoutweek')
				->orderBy('request.id', 'desc')
				->paginate(10);
		$response = Response::json($requests);

		return View::make('web.providerPayment')
						->with('title', trans('user_provider_controller.week_pay'))
						->with('page', 'payment')
						->with('requests', $requests);
	}

	public function providers_payout() {
		Session::forget('che');
		$start = Input::get('start');
		$end = Input::get('end');
		$weekend = Input::get('weekend');
		$provider_id = Session::get('provider_id');

		if($end == 0){
			$end = Requests::where('is_completed',1)->orderBy('id', 'desc')->first()->id;
		}

		$requests = DB::table('request')
				->leftJoin('provider', 'request.confirmed_provider', '=', 'provider.id')
				->leftJoin('user', 'request.user_id', '=', 'user.id')
				->select('user.first_name as user_first_name',
						'provider.type', 'user.last_name as user_last_name',
						'provider.first_name as provider_first_name',
						'provider.last_name as provider_last_name',
						'user.id as user_id',
						'provider.id as provider_id',
						'request.id as id',
						'request.created_at as date',
						'request.is_started',
						'request.is_provider_arrived',
						'request.is_completed',
						'request.is_paid',
						'request.is_provider_started',
						'request.confirmed_provider',
						'request.status',
						'provider.type',
						'request.request_start_time',
						'request.card_payment',
						'request.time',
						'request.payment_mode as cash_or_card',
						'request.distance', 'request.total',
						'request.is_cancelled',
						'request.payment_remaining',
						'request.refund_remaining',
						'request.payment_platform_rate',
						'request.provider_commission',
						'request.provider_commission')
				->where('request.id', '>=', $start)
				->where('request.id', '<=', $end)
				->where('request.status', '=', 1)
				->where('request.is_completed', '=', 1)
				->orderBy('request.created_at', 'desc')
				->get();

		$query = 	"SELECT provider.*,
					count('request.*') as 'total_requests',
					sum(request.total) as 'total_payment',
					sum(request.provider_commission) as 'provider_commission',
					sum(request.ledger_payment) + sum(request.promo_payment) as 'promo_payment',
					sum(request.payment_platform_rate) as 'payment_platform_taxes'
					FROM provider
					INNER JOIN request ON provider.id = request.confirmed_provider
					WHERE provider.id = " . $provider_id . "
					AND request.is_completed = 1
					AND request.id BETWEEN '" . $start . "' AND '" . $end . "'
					ORDER BY request.id";

		$providers =  DB::select(DB::raw($query));

		/*

		if (Input::get('submit1') && Input::get('submit1') == 'Download Report') {
			$pdf = App::make('dompdf');
			$parameter = array();
			$parameter['title'] = trans('user_provider_controller.driver_list');
			$parameter['page'] = trans('payout.drivers');
			$parameter['providers'] = $providers;
			$parameter['total_requests'] = $providers;
			$parameter['accepted_requests'] = $providers;
			$parameter['requests'] = $requests;

			$pdf = PDF::loadView('web.providers_payout_newpdf', $parameter)->setPaper('legal')->setOrientation('landscape')->setWarnings(false);
			return $pdf->download(ucwords(Config::get('app.website_title')) . '_week_ending' . $weekend . '.pdf');
		}*/

		return View::make('web.providers_payout')
						->with('title', trans('user_provider_controller.driver_list'))
						->with('page', trans('payout.drivers'))
						->with('providers', $providers)
						->with('requests', $requests);
	}

}
