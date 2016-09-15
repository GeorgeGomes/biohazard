<?php

class ProviderController extends BaseController {

	public function isAdmin($token) {
		return false;
	}

	public function getProviderData($provider_id, $token, $is_admin) {

		if ($provider_data = Provider::where('token', '=', $token)->where('id', '=', $provider_id)->first()) {
			return $provider_data;
		} elseif ($is_admin) {
			$provider_data = Provider::where('id', '=', $provider_id)->first();
			if (!$provider_data) {
				return false;
			}
			return $provider_data;
		} else {
			return false;
		}
	}

	public function register() {

		$first_name 		= ucwords(trim(Input::get('first_name')));
		$last_name 			= ucwords(trim(Input::get('last_name')));
		$email 				= Input::get('email');
		$phone 				= Input::get('phone');
		$password 			= Input::get('password');
		$type 				= Input::get('type');
		$picture 			= Input::file('picture');
		$bio 				= Input::get('bio');

		// get service type and categories
		$jsontypeservice 	= Input::get('jsontypeservice');
		$typeCategories		= @json_decode($jsontypeservice) ;

		//return $typeCategories ;
		
		if (Input::hasfile('picture')) {
			$picture = Input::file('picture');
		} else {
			$picture = '';
		}

		$device_token = 0;
		if (Input::has('device_token')) {
			$device_token = Input::get('device_token');
		}

		$device_type = Input::get('device_type');

		$address = ucwords(trim(Input::get('address')));
		$state = ucwords(trim(Input::get('state')));
		$country = ucwords(trim(Input::get('country')));

		$zipcode = 0;
		if (Input::has('zipcode')) {
			$zipcode = Input::get('zipcode');
		}

		$login_by = Input::get('login_by');
		$car_model = 0;
		if (Input::has('car_model')) {
			$car_model = ucwords(trim(Input::get('car_model')));
		}
		
		$car_number = 0;
		if (Input::has('car_number')) {
			$car_number = Input::get('car_number');

			//Inicia a validação da Placa do Carro
			$car_number_db = Settings::where('key', 'car_number_format')->first();

			$car_number_letter = strlen(preg_replace("/.*?([a-zA-Z]*).*?/i", "$1", $car_number_db->value));
			$car_number_number = strlen(preg_replace("/.*?([0-9]*).*?/i", "$1", $car_number_db->value));

			$first_letter = substr($car_number_db->value,0,1);

			if(preg_match('/^[a-zA-Z]{1}$/', $first_letter)){
				if (preg_match('/^[a-zA-Z]{' . $car_number_letter . '}\-?[0-9]{' . $car_number_number . '}$/', Input::get('car_number'))) {
					$car_number = Input::get('car_number');
					
				} else {
					$car_number = 0;
					$error_messages = trans('adminController.invalid_car_number'). $car_number_db->value;
					
					$response_array = array('success' => false, 'error' => trans('adminController.invalid_car_number'), 'error_code' => 418, 'error_messages' => $error_messages);

					$response_code = 200;

					goto response;
				}
			} else {
				if (preg_match('/^[0-9]{' . $car_number_number . '}\-?[a-zA-Z]{' . $car_number_letter . '}$/', Input::get('car_number'))) {
					$car_number = Input::get('car_number');
				} else {
					$car_number = 0;
					$error_messages = trans('adminController.invalid_car_number') . $car_number_db->value;

					$response_array = array('success' => false, 'error' => trans('adminController.invalid_car_number'), 'error_code' => 418, 'error_messages' => $error_messages);

					$response_code = 200;
					goto response;
				}
			}
		}

		$car_brand = 0;
		if (Input::has('car_brand')) {
			$car_brand = ucwords(trim(Input::get('car_brand')));
		}
		$social_unique_id = trim(Input::get('social_unique_id'));

		$zipcode 				= preg_replace("/(\D)/", "", Input::get('zipcode'));
		$address 				= Input::get('address');
		$address_number 		= Input::get('address_number');
		$address_complements 	= Input::get('address_complements');
		$address_neighbour 		= Input::get('address_neighbour');
		$address_city 			= Input::get('address_city');
		$state 					= Input::get('state');
		$country 				= Input::get('country');

		if ($password != "" and $social_unique_id == '') {
			$validator = Validator::make(
						array(
							'password' => $password,
							'email' => $email,
							'first_name' => $first_name,
							'last_name' => $last_name,
							'picture' => $picture,
							'device_token' => $device_token,
							'device_type' => $device_type,
							/* 'zipcode' => $zipcode, */
							'login_by' => $login_by,
							'type' => $jsontypeservice
						), 
						array(
							'password' => 'required',
							'email' => 'required|email',
							'first_name' => 'required',
							'last_name' => 'required',
							/* 'picture' => 'required|mimes:jpeg,bmp,png', */
							'picture' => '',
							'device_token' => 'required',
							'device_type' => 'required|in:android,ios',
							/* 'zipcode' => 'integer', */
							'login_by' => 'required|in:manual,facebook,google',
							'type' => 'required'
						), 
						array(
							'password' => trans('providerController.password_required'),
							'email' => trans('providerController.email_required'),
							'first_name' => trans('providerController.fname_required'),
							'last_name' => trans('providerController.lname_required'),
							/* 'picture' => 'required|mimes:jpeg,bmp,png', */
							'picture' => trans('providerController.image_required'),
							'device_token' => trans('providerController.push_token_required'),
							'device_type' => trans('providerController.device_ios_android'),
							/* 'zipcode' => '', */
							'login_by' => trans('providerController.login_type_required'),
							'type' => trans('providerController.type_required')
						)
			);

			$validatorPhone = Validator::make(
						array(
							'phone' => $phone,
						), 
						array(
							'phone' => 'phone'
						), 
						array(
							'phone' => trans('providerController.phone_must_required')
						)
			);
		} elseif ($social_unique_id != "" and $password == '') {
			$validator = Validator::make(
						array(
							'email' => $email,
							'phone' => $phone,
							'first_name' => $first_name,
							'last_name' => $last_name,
							'picture' => $picture,
							'device_token' => $device_token,
							'device_type' => $device_type,
							'bio' => $bio,
							'address' => $address,
							'state' => $state,
							'country' => $country,
							/* 'zipcode' => $zipcode, */
							'login_by' => $login_by,
							'social_unique_id' => $social_unique_id ,
							'type' => $jsontypeservice
						), 
						array(
							'email' => 'required|email',
							'phone' => 'required',
							'first_name' => 'required',
							'last_name' => 'required',
							/* 'picture' => 'required|mimes:jpeg,bmp,png', */
							'picture' => '',
							'device_token' => 'required',
							'device_type' => 'required|in:android,ios',
							'bio' => '',
							'address' => '',
							'state' => '',
							'country' => '',
							/* 'zipcode' => 'integer', */
							'login_by' => 'required|in:manual,facebook,google',
							'social_unique_id' => 'required|unique:provider',
							'type' => 'required'
						), 
						array(
							'email' => trans('providerController.email_required'),
							'phone' => trans('providerController.phone_must_required'),
							'first_name' => trans('providerController.fname_required'),
							'last_name' => trans('providerController.lname_required'),
							/* 'picture' => 'required|mimes:jpeg,bmp,png', */
							'picture' => trans('providerController.image_required'),
							'device_token' => trans('providerController.push_token_required'),
							'device_type' => trans('providerController.device_ios_android'),
							'bio' => '',
							'address' => '',
							'state' => '',
							'country' => '',
							/* 'zipcode' => '', */
							'login_by' => '',
							'social_unique_id' => trans('providerController.social_unique_required') ,
							'type' => trans('providerController.type_required')
						)
			);

			$validatorPhone = Validator::make(
							array(
						'phone' => $phone,
							), array(
						'phone' => 'phone'
							), array(
						'phone' => trans('providerController.phone_must_required')
							)
			);
		} elseif ($social_unique_id != "" and $password != '') {
			$response_array = array('success' => false, 'error' => trans('providerController.invalid_social_password_passed'), 'error_code' => 401);
			$response_code = 200;
			goto response;
		}

		//verificar se social id já esta cadastrado
		if($social_unique_id != "" && DB::table('provider')->where('social_unique_id', '=', $social_unique_id)->get()){
		  $response_array = array('success' => false, 'error' => trans('providerController.social_id_already_registed'), 'error_code' => 418);
		  $response_code = 200;          
		}
		else if ($validator->fails()) {
			$error_messages = $validator->messages();
			//Log::info('Error while during provider registration = ' . print_r($error_messages, true));
			$error_messages = $validator->messages()->all();
			$response_array = array('success' => false, 'error' => trans('providerController.invalid_input'), 'error_code' => 401, 'error_messages' => $error_messages);
			$response_code = 200;
		} else if ($validatorPhone->fails()) {
			$error_messages = $validator->messages()->all();
			$response_array = array('success' => false, 'error' => trans('providerController.invalid_phone_number'), 'error_code' => 401, 'error_messages' => $error_messages);
			$response_code = 200;
		} else {

			if (Provider::where('email', '=', $email)->first()) {
				$response_array = array('success' => false, 'error' => trans('providerController.email_already_registed'), 'error_code' => 402);
				$response_code = 200;
			} else {

				
				$activation_code = uniqid();

				$provider = new Provider;
				$provider->first_name = $first_name;
				$provider->last_name = $last_name;
				$provider->email = $email;
				$provider->phone = $phone;
				$provider->activation_code = $activation_code;
				$provider->email_activation = 1;

				if ($password != '') {
					$provider->password = Hash::make($password);
				}

				$provider->token = generate_token();
				$provider->token_expiry = generate_expiry();


				// upload image
				$file_name = time();
				$file_name .= rand();
				$file_name = sha1($file_name);

				$s3_url = '';
				if (Input::hasfile('picture')) {
					$ext = Input::file('picture')->getClientOriginalExtension();
					Input::file('picture')->move(public_path() . "/uploads", $file_name . "." . $ext);
					$local_url = $file_name . "." . $ext;

					// Upload to S3
					if (Config::get('app.s3_bucket') != '') {
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
				}

				$provider->picture 		= $s3_url;
				$provider->device_token = $device_token;
				$provider->device_type 	= $device_type;
				$provider->bio 			= $bio;

				// salva endereco 
				$provider->zipcode 				= preg_replace("/(\D)/", "", $zipcode);
				$provider->address 				= $address;
				$provider->address_number 		= $address_number;
				$provider->address_complements 	= $address_complements;
				$provider->address_neighbour 	= $address_neighbour;
				$provider->address_city 		= $address_city;
				$provider->state 				= $state;
				$provider->country 				= $country;

				$provider->login_by = $login_by;
				$provider->is_available = 1;
				$provider->is_active = 0;
				$provider->is_approved = 0;

				// set status under review
				$providerStatus = ProviderStatus::where('name', 'EM_ANALISE')->first();
				$txt_approve = $txt_approve = trans('providerController.Under_review');

				$provider->status_id = $providerStatus->id;

				$provider->type = $type;
				$provider->car_model = $car_model;
				$provider->car_brand = $car_brand;
				$provider->car_number = $car_number;
				$provider->email_activation = 1;

				if ($social_unique_id != '') {
					$password = my_random6_number();
					$provider->social_unique_id = $social_unique_id;
					$provider->password = Hash::make($password);
				}

				$provider->timezone = "America/Sao_Paulo";
				If (Input::has('timezone')) {
					$provider->timezone = Input::get('timezone');
				}

				$provider->save();
				$provider_id = $provider->id ;

				// salvar associacoes dos tipos e categorias
				if($jsontypeservice && is_array($typeCategories)){

					$providerServiceArray = [] ;

					foreach ($typeCategories as $typeCategory) {

						if($typeCategory->type == "category"){
							$type_id = $typeCategory->parentId ;
							$category_id = $typeCategory->id ;
						} 
						else {
							$type_id = $typeCategory->id ;
							$category_id = 0 ;
						}

						$providerServiceDefault = ProviderServices::findDefaultByTypeIdAndCategoryId($type_id, $category_id);

						
						if($providerServiceDefault){
							
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
						}
					}

				}

				// se for login social, enviar para usuário seus dados de acesso futuro
				if ($social_unique_id != '') {
					$pattern = trans('providerController.hello_') . "" . ucwords($first_name) . ". " . trans('providerController.your') . "" . Config::get('app.website_title') . "" . trans('providerController.web_login_password') . "" . $password;
					sms_notification($provider->id, 'provider', $pattern);
					$subject = trans('providerController.your') . "" . Config::get('app.website_title') . "" . trans('providerController.web_login_password2');
					email_notification($provider->id, 'provider', $pattern, $subject);
				}

				
				// envia e-mail de boas vindas
				$settings = Settings::where('key', 'admin_email_address')->first();
				$admin_email = $settings->value;
				$pattern = array('admin_eamil' => $admin_email, 'name' => ucwords($provider->first_name . " " . $provider->last_name), 'web_url' => web_url());
				$subject = trans('providerController.welcome_to') . ucwords(Config::get('app.website_title')) . ", " . ucwords($provider->first_name . " " . $provider->last_name) . '';
				email_notification($provider->id, 'provider', $pattern, $subject, 'provider_new_register', null);
				
				

				$response_array = array(
					'success' => true,
					'id' => $provider->id,
					'first_name' => $provider->first_name,
					'last_name' => $provider->last_name,
					'phone' => $provider->phone,
					'email' => $provider->email,
					'picture' => $provider->picture,
					'bio' => $provider->bio,
					'address' => $provider->address,
					'state' => $provider->state,
					'country' => $provider->country,
					'zipcode' => $provider->zipcode,
					'login_by' => $provider->login_by,
					'social_unique_id' => $provider->social_unique_id ? $provider->social_unique_id : '',
					'device_token' => $provider->device_token,
					'device_type' => $provider->device_type,
					'token' => $provider->token,
					'timezone' => $provider->timezone,
					'car_model' => $provider->car_model,
					'car_brand' => $provider->car_brand,
					'car_number' => $provider->car_number,
					'type' => $provider->type,
					'is_approved' => $provider->is_approved,
					'status_id' => $provider->status ? $provider->status->name : "EM_ANALISE" ,
					'is_approved_txt' => $txt_approve,
					'is_available' => $provider->is_active,
				);
				$response_code = 200;
			}
		}

		response:
		$response = Response::json($response_array, $response_code);
		return $response;
	}

	public function login() {
		$login_by = Input::get('login_by');
		$device_token = 0;
		if (Input::has('device_token')) {
			$device_token = Input::get('device_token');
		}
		$device_type = Input::get('device_type');
		if (Input::has('email') && Input::has('password')) {
			$email = Input::get('email');
			$password = Input::get('password');

			$validator = Validator::make(
							array(
						'password' => $password,
						'email' => $email,
						'device_token' => $device_token,
						'device_type' => $device_type,
						'login_by' => $login_by
							), array(
						'password' => 'required',
						'email' => 'required|email',
						'device_token' => 'required',
						'device_type' => 'required|in:android,ios',
						'login_by' => 'required|in:manual,facebook,google'
							), array(
						'password' => trans('providerController.password_required'),
						'email' => trans('providerController.email_required'),
						'device_token' => trans('providerController.push_token_required'),
						'device_type' => trans('providerController.device_ios_android'),
						'login_by' => ''
							)
			);

			if ($validator->fails()) {
				$error_messages = $validator->messages();
				//Log::error('Validation error during manual login for provider = ' . print_r($error_messages, true));
				$error_messages = $validator->messages()->all();
				$response_array = array('success' => false, 'error' => trans('providerController.invalid_input'), 'error_code' => 401, 'error_messages' => $error_messages);
				$response_code = 200;
			} else {
				if ($provider = Provider::where('email', '=', $email)->first()) {
					if (Hash::check($password, $provider->password)) {
						if ($login_by != "manual") {
							$response_array = array('success' => false, 'error' => trans('providerController.login_mismatch'), 'error_code' => 417);
							$response_code = 200;
						} else {
							Provider::where('id', '!=', $provider->id)->where('device_token', '=', $device_token)->update(array('device_token' => 0));
							/* if ($provider->device_type != $device_type) { */
							$provider->device_type = $device_type;
							/* }
							  if ($provider->device_token != $device_token) { */
							$provider->device_token = $device_token;
							/* } */
							$provider->token = generate_token();
							$provider->token_expiry = generate_expiry();
							$provider->save();

							$providerStatusAnalise = ProviderStatus::where('name', 'EM_ANALISE')->first();
							$providerStatusApproved = ProviderStatus::where('name', 'APROVADO')->first();
							$providerStatusRejected = ProviderStatus::where('name', 'REJEITADO')->first();
							$providerStatusSuspended = ProviderStatus::where('name', 'SUSPENSO')->first();
							
							$txt_approve = trans('providerController.decline');
							if($provider->status_id == $providerStatusApproved->id){ ///Aprovado
								$txt_approve = trans('providerController.Approved');
							}
							elseif ($provider->status_id == $providerStatusRejected->id) { ///Rejeitado
								$txt_approve = trans('providerController.Rejected');
							}
							elseif ($provider->status_id == $providerStatusAnalise->id) { ///Em Analise
								$txt_approve = trans('providerController.Under_review');
							}
							elseif ($provider->status_id == $providerStatusSuspended->id) { ///Suspenso
								$txt_approve = trans('providerController.Suspended');
							}

							$provider_services  = ProviderServices::where('provider_id', $provider->id)->first();

							Log::info('Provider logged:'. $provider->id . " " . $provider->first_name . " " . $provider->token);
							

							$response_array = array(
								'success' => true,
								'id' => $provider->id,
								'first_name' => $provider->first_name,
								'last_name' => $provider->last_name,
								'phone' => $provider->phone,
								'email' => $provider->email,
								'picture' => $provider->picture,
								'bio' => $provider->bio,
								'address' => $provider->address,
								'state' => $provider->state,
								'country' => $provider->country,
								'zipcode' => $provider->zipcode,
								'login_by' => $provider->login_by,
								'social_unique_id' => $provider->social_unique_id,
								'device_token' => $provider->device_token,
								'device_type' => $provider->device_type,
								'token' => $provider->token,
								'type' => ($provider_services ? $provider_services->type : null),
								'timezone' => $provider->timezone,
								'is_approved' => $provider->is_approved,
								'status_id' => $provider->status ? $provider->status->name : "EM_ANALISE" ,
								'car_model' => $provider->car_model,
								'car_number' => $provider->car_number,
								'is_approved_txt' => $txt_approve,
								'is_available' => $provider->is_active,
							);
							$response_code = 200;
						}
					} else {
						$response_array = array('success' => false, 'error' => trans('providerController.username_password_and_invalid'), 'error_code' => 403);
						$response_code = 200;
					}
				} else {
					$response_array = array('success' => false, 'error' => trans('providerController.not_registered_user'), 'error_code' => 404);
					$response_code = 200;
				}
			}
		} elseif (Input::has('social_unique_id')) {
			$social_unique_id = trim(Input::get('social_unique_id'));
			$socialValidator = Validator::make(
							array(
						'social_unique_id' => $social_unique_id,
						'device_token' => $device_token,
						'device_type' => $device_type,
						'login_by' => $login_by
							), array(
						'social_unique_id' => 'required|exists:provider,social_unique_id',
						'device_token' => 'required',
						'device_type' => 'required|in:android,ios',
						'login_by' => 'required|in:manual,facebook,google'
							), array(
						'social_unique_id' => trans('providerController.social_unique_required'),
						'device_token' => trans('providerController.push_token_required'),
						'device_type' => trans('providerController.device_ios_android'),
						'login_by' => ''
							)
			);
			if ($socialValidator->fails()) {
				$error_messages = $socialValidator->messages();
				//Log::error('Validation error during social login for provider = ' . print_r($error_messages, true));
				$error_messages = $socialValidator->messages()->all();
				$response_array = array('success' => false, 'error' => trans('providerController.invalid_input'), 'error_code' => 401, 'error_messages' => $error_messages);
				$response_code = 200;
			} else {
				if ($provider = Provider::where('social_unique_id', '=', $social_unique_id)->first()) {
					if (!in_array($login_by, array('facebook', 'google'))) {
						$response_array = array('success' => false, 'error' => trans('providerController.login_mismatch'), 'error_code' => 417);
						$response_code = 200;
					} else {
						if ($provider->device_type != $device_type) {
							$provider->device_type = $device_type;
						}
						if ($provider->device_token != $device_token) {
							$provider->device_token = $device_token;
						}
						$provider->token_expiry = generate_expiry();
						$provider->save();

						$txt_approve = trans('providerController.decline');

						$providerStatusAnalise = ProviderStatus::where('name', 'EM_ANALISE')->first();
						$providerStatusApproved = ProviderStatus::where('name', 'APROVADO')->first();
						$providerStatusRejected = ProviderStatus::where('name', 'REJEITADO')->first();
						$providerStatusSuspended = ProviderStatus::where('name', 'SUSPENSO')->first();

						if($provider->status_id == $providerStatusApproved->id){ ///Aprovado
							$txt_approve = trans('providerController.Approved');
						}
						elseif ($provider->status_id == $providerStatusRejected->id) { ///Rejeitado
							$txt_approve = trans('providerController.Rejected');
						}
						elseif ($provider->status_id == $providerStatusAnalise->id) { ///Em Analise
							$txt_approve = trans('providerController.Under_review');
						}
						elseif ($provider->status_id == $providerStatusSuspended->id) { ///Suspenso
							$txt_approve = trans('providerController.Suspended');
						}
						
						// $txt_approve = trans('providerController.decline');
						// if ($provider->is_approved) {
						// 	$txt_approve = trans('providerController.Approved');
						// }
						$provider_services  = ProviderServices::where('provider_id', $provider->id)->first();


						$response_array = array(
							'success' => true,
							'id' => $provider->id,
							'first_name' => $provider->first_name,
							'last_name' => $provider->last_name,
							'phone' => $provider->phone,
							'email' => $provider->email,
							'picture' => $provider->picture,
							'bio' => $provider->bio,
							'address' => $provider->address,
							'state' => $provider->state,
							'country' => $provider->country,
							'zipcode' => $provider->zipcode,
							'login_by' => $provider->login_by,
							'social_unique_id' => $provider->social_unique_id,
							'device_token' => $provider->device_token,
							'device_type' => $provider->device_type,
							'token' => $provider->token,
							'timezone' => $provider->timezone,
							'type' => ($provider_services ? $provider_services->type : null),
							'is_approved' => $provider->is_approved,
							'status_id' => $provider->status ? $provider->status->name : "EM_ANALISE" ,
							'car_model' => $provider->car_model,
							'car_number' => $provider->car_number,
							'is_approved_txt' => $txt_approve,
							'is_available' => $provider->is_active,
						);
						$response_code = 200;
					}
				} else {
					$response_array = array('success' => false, 'error' => trans('providerController.not_registered_user_social'), 'error_code' => 404);
					$response_code = 200;
				}
			}
		} else {
			$response_array = array('success' => false, 'error' => trans('providerController.invalid_input'));
			$response_code = 200;
		}
		$response = Response::json($response_array, $response_code);
		return $response;
	}

	// Rate User

	public function set_user_rating() {
		if (Request::isMethod('post')) {
			$comment = '';
			if (Input::has('comment')) {
				$comment = Input::get('comment');
			}
			$request_id = Input::get('request_id');
			$rating = 0;
			if (Input::has('rating')) {
				$rating = Input::get('rating');
			}
			$token = Input::get('token');
			$provider_id = Input::get('id');

			$validator = Validator::make(
							array(
						'request_id' => $request_id,
						/* 'rating' => $rating, */
						'token' => $token,
						'provider_id' => $provider_id,
							), array(
						'request_id' => 'required|integer',
						/* 'rating' => 'required|integer', */
						'token' => 'required',
						'provider_id' => 'required|integer'
							), array(
						'request_id' => trans('providerController.id_request_required'),
						/* 'rating' => 'required|integer', */
						'token' => '',
						'provider_id' => trans('providerController.unique_id_missing')
							)
			);
			/* $var = Keywords::where('id', 1)->first(); */
			if ($validator->fails()) {
				$error_messages = $validator->messages()->all();
				$response_array = array('success' => false, 'error' => trans('providerController.invalid_input'), 'error_code' => 401, 'error_messages' => $error_messages);
				$response_code = 200;
			} else {
				$is_admin = $this->isAdmin($token);
				if ($provider_data = $this->getProviderData($provider_id, $token, $is_admin)) {
					// check for token validity
					if (is_token_active($provider_data->token_expiry) || $is_admin) {
						// Do necessary operations
						if ($request = Requests::find($request_id)) {
							if ($request->confirmed_provider == $provider_id) {

								if ($request->is_user_rated == 0) {

									$user = User::find($request->user_id);

									$user_review = new UserReview;
									$user_review->request_id = $request_id;
									$user_review->provider_id = $provider_id;
									$user_review->rating = $rating;
									$user_review->user_id = $user->id;
									$user_review->comment = $comment;
									$user_review->save();

									$request->is_user_rated = 1;
									$request->save();

									if ($rating) {
										if ($user = User::find($request->user_id)) {
											$old_rate = $user->rate;
											$old_rate_count = $user->rate_count;
											$new_rate_counter = ($user->rate_count + 1);
											$new_rate = (($user->rate * $user->rate_count) + $rating) / $new_rate_counter;
											$user->rate_count = $new_rate_counter;
											$user->rate = $new_rate;
											$user->save();
										}
									}

									$response_array = array('success' => true);
									$response_code = 200;
								} else {
									$response_array = array('success' => false, 'error' => trans('providerController.already_rated'), 'error_code' => 409);
									$response_code = 200;
								}
							} else {
								/* $response_array = array('success' => false, 'error' => trans('providerController.service_id_not_match') . "" . $var->keyword . "" . trans('providerController.id'), 'error_code' => 407); */
								$response_array = array('success' => false, 'error' => trans('providerController.service_id_not_match') . "" . Config::get('app.generic_keywords.Provider') . "" . trans('providerController.id'), 'error_code' => 407);
								$response_code = 200;
							}
						} else {
							$response_array = array('success' => false, 'error' => trans('providerController.service_id_not_found'), 'error_code' => 408);
							$response_code = 200;
						}
					} else {
						$response_array = array('success' => false, 'error' => trans('providerController.token_expired'), 'error_code' => 405);
						$response_code = 200;
					}
				} else {
					if ($is_admin) {
						/* $response_array = array('success' => false, 'error' => "" . $var->keyword . ' ID not Found', 'error_code' => 410); */
						$response_array = array('success' => false, 'error' => trans('providerController.id_of') . "" . Config::get('app.generic_keywords.Provider') . "" . trans('providerController.not_found'), 'error_code' => 410);
					} else {
						$response_array = array('success' => false, 'error' => trans('providerController.token_not_valid'), 'error_code' => 406);
					}
					$response_code = 200;
				}
			}
		}
		$response = Response::json($response_array, $response_code);
		return $response;
	}

	// Cancel request

	public function cancel_request() {
		if (Request::isMethod('post')) {
			$request_id = Input::get('request_id');
			$token = Input::get('token');
			$provider_id = Input::get('id');

			$validator = Validator::make(
							array(
						'request_id' => $request_id,
						'token' => $token,
						'provider_id' => $provider_id,
							), array(
						'request_id' => 'required|integer',
						'token' => 'required',
						'provider_id' => 'required|integer'
							), array(
						'request_id' => trans('providerController.id_request_required'),
						'token' => '',
						'provider_id' => trans('providerController.unique_id_missing')
							)
			);

			/* $var = Keywords::where('id', 1)->first(); */

			if ($validator->fails()) {
				$error_messages = $validator->messages()->all();
				$response_array = array('success' => false, 'error' => trans('providerController.invalid_input'), 'error_code' => 401, 'error_messages' => $error_messages);
				$response_code = 200;
			} else {
				$is_admin = $this->isAdmin($token);
				if ($provider_data = $this->getProviderData($provider_id, $token, $is_admin)) {
					// check for token validity
					if (is_token_active($provider_data->token_expiry) || $is_admin) {
						// Do necessary operations
						if ($request = Request::find($request_id)) {
							if ($request->provider_id == $provider_id) {

								if ($request->is_request_started == 0) {
									$request->provider_id = 0;
									$request->is_confirmed = 0;
									$request->save();

									$response_array = array('success' => true);
									$response_code = 200;
								} else {
									$response_array = array('success' => false, 'error' => trans('providerController.service_already_started'), 'error_code' => 416);
									$response_code = 200;
								}
							} else {
								/* $response_array = array('success' => false, 'error' => trans('providerController.service_id_doesnt_match') . $var->keyword . "" . trans('providerController.id'), 'error_code' => 407); */
								$response_array = array('success' => false, 'error' => trans('providerController.service_id_doesnt_match') . Config::get('app.generic_keywords.Provider') . "" . trans('providerController.id'), 'error_code' => 407);
								$response_code = 200;
							}
						} else {
							$response_array = array('success' => false, 'error' => trans('providerController.service_id_not_found'), 'error_code' => 408);
							$response_code = 200;
						}
					} else {
						$response_array = array('success' => false, 'error' => trans('providerController.token_expired'), 'error_code' => 405);
						$response_code = 200;
					}
				} else {
					if ($is_admin) {
						/* $response_array = array('success' => false, 'error' => "" . $var->keyword . ' ID not Found', 'error_code' => 410); */
						$response_array = array('success' => false, 'error' => trans('providerController.id_of') . "" . Config::get('app.generic_keywords.Provider') . "" . trans('providerController.not_found'), 'error_code' => 410);
					} else {
						$response_array = array('success' => false, 'error' => trans('providerController.token_not_valid'), 'error_code' => 406);
					}
					$response_code = 200;
				}
			}
		}
		$response = Response::json($response_array, $response_code);
		return $response;
	}

	// Add provider Location Data
	public function provider_location() {
		if (Request::isMethod('post')) {
			$token = Input::get('token');
			$provider_id = Input::get('id');
			$latitude = Input::get('latitude');
			$longitude = Input::get('longitude');
			if (Input::has('bearing')) {
				$angle = Input::get('bearing');
			}

			$validator = Validator::make(
							array(
						'token' => $token,
						'provider_id' => $provider_id,
						'latitude' => $latitude,
						'longitude' => $longitude,
							), array(
						'token' => 'required',
						'provider_id' => 'required|integer',
						'latitude' => 'required',
						'longitude' => 'required',
							), array(
						'token' => '',
						'provider_id' => trans('providerController.unique_id_missing'),
						'latitude' => trans('providerController.location_point_missing'),
						'longitude' => trans('providerController.location_point_missing'),
							)
			);

			if ($validator->fails()) {
				$error_messages = $validator->messages()->all();
				$response_array = array('success' => false, 'error' => trans('providerController.invalid_input'), 'error_code' => 401, 'error_messages' => $error_messages);
				$response_code = 200;
			} else {
				$is_admin = $this->isAdmin($token);
				if ($provider_data = $this->getProviderData($provider_id, $token, $is_admin)) {
					// check for token validity
					$status_txt = trans('providerController.not_active');
					if ($provider_data->is_active) {
						$status_txt = trans('providerController.active');
					}
					if (is_token_active($provider_data->token_expiry) || $is_admin) {
						$provider = Provider::find($provider_id);

						$location = get_location($latitude, $longitude);
						$latitude = $location['lat'];
						$longitude = $location['long'];

						if (!isset($angle)) {
							$angle = get_angle($provider->latitude, $provider->longitude, $latitude, $longitude);
						}
						$provider->old_latitude = $provider->latitude;
						$provider->old_longitude = $provider->longitude;
						$provider->latitude = $latitude;
						$provider->longitude = $longitude;
						$provider->bearing = $angle;
						$provider->save();

						$response_array = array(
							'success' => true,
							'is_active' => $provider_data->is_active,
							'is_approved' => $provider_data->is_approved,
							'is_active_txt' => $status_txt,
						);
					} else {
						$response_array = array(
							'success' => false,
							'error' => trans('providerController.token_expired'),
							'error_code' => 412,
							'is_active' => $provider_data->is_active,
							'is_approved' => $provider_data->is_approved,
							'is_active_txt' => $status_txt,
						);
					}
				} else {
					if ($is_admin) {
						/* $driver = Keywords::where('id', 1)->first();
						  $response_array = array('success' => false, 'error' => "" . $driver->keyword . ' ID not Found', 'error_code' => 410); */
						$response_array = array('success' => false, 'error' => trans('providerController.id_of') . "" . Config::get('app.generic_keywords.Provider') . "" . trans('providerController.not_found'), 'error_code' => 410);
					} else {
						$response_array = array('success' => false, 'error' => trans('providerController.token_not_valid'), 'error_code' => 406);
					}
				}
				$response_code = 200;
			}
		}
		$response = Response::json($response_array, $response_code);
		return $response;
	}

	// Get Profile

	public function get_requests() {

		$token = Input::get('token');
		$provider_id = Input::get('id');

		$validator = Validator::make(
						array(
							'token' => $token,
							'provider_id' => $provider_id,
						), 
						array(
							'token' => 'required',
							'provider_id' => 'required|integer'
						), 
						array(
							'token' => '',
							'provider_id' => trans('providerController.unique_id_missing')
						)
					);

		if ($validator->fails()) {
			$error_messages = $validator->messages()->all();
			$response_array = array('success' => false, 'error' => trans('providerController.invalid_input'), 'error_code' => 401, 'error_messages' => $error_messages);
			$response_code = 200;
		} else {
			$is_admin = $this->isAdmin($token);
			if ($provider_data = $this->getProviderData($provider_id, $token, $is_admin)) {
				// check for token validity
				if (is_token_active($provider_data->token_expiry)) {
					$txt_approve = trans('providerController.decline');
					if ($provider_data->is_approved) {
						$txt_approve = trans('providerController.Approved');
					}
					$time = date("Y-m-d H:i:s");
					$provider_timeout = Settings::where('key', 'provider_timeout')->first();
					$timeout = $provider_timeout->value;

					$query = "SELECT request.*,id, later, D_latitude, D_longitude, payment_mode, request_start_time , user_id,TIMESTAMPDIFF(SECOND,updated_at, '$time') as diff from request where is_cancelled = 0 and status = 0 and current_provider = $provider_id and TIMESTAMPDIFF(SECOND,updated_at, '$time') <= $timeout";

					$requests = DB::select(DB::raw($query));
					$all_requests = array();
					$counter = 0;
					foreach ($requests as $request) {
						$counter++;
						$data['request_id'] = $request->id;
						$requestData = RequestServices::where('request_id', $request->id)->first();
						$data['request_services'] = $requestData->type;

						// get options
						$requestOptions = RequestOptions::where('request_id', $request->id)->first();

						$rservc = RequestServices::where('request_id', $request->id)->get();
						$typs = array();
						$typi = array();
						$typp = array();
						$totalPrice = 0;

						foreach ($rservc as $typ) {
							$typ1 = ProviderType::where('id', $typ->type)->first();
							$typ_price = ProviderServices::where('provider_id', $provider_id)->where('type', $typ->type)->first();

							if ($typ_price->base_price > 0) {
								$typp1 = 0.00;
								$typp1 = $typ_price->base_price;
							} else {
								$typp1 = 0.00;
							}

							$typs['name'] = $typ1->name;
							$typs['price'] = $typp1;
							$totalPrice = $totalPrice + $typp1;

							array_push($typi, $typs);
						}
						$data['type'] = $typi;

						if ($request->later == 0)
							$data['time_left_to_respond'] = $timeout - $request->diff;
						else
							$data['time_left_to_respond'] = $timeout;

						$user = User::find($request->user_id);
						$user_timezone = $user->timezone;
						$default_timezone = Config::get('app.timezone');

						$date_time = get_user_time($default_timezone, $user_timezone, $request->request_start_time);


						$data['later'] = $request->later;
						$data['datetime'] = $date_time;

						$request_data = array();
						$request_data['user'] = array();
						$request_data['user']['name'] = $user->first_name . " " . $user->last_name;
						$request_data['user']['picture'] = $user->picture;
						$request_data['user']['phone'] = $user->phone;
						$request_data['user']['address'] = $user->address;
						$request_data['user']['latitude'] = $request->latitude;
						$request_data['user']['longitude'] = $request->longitude;
						$request_data['user']['src_address'] = $request->src_address;
						$request_data['user']['dest_latitude'] = $request->D_latitude;
						$request_data['user']['dest_longitude'] = $request->D_longitude;
						$request_data['user']['dest_address'] = $request->dest_address;
						if ($request->D_latitude != NULL) {
							/* Log::info('D_latitude = ' . print_r($request->D_latitude, true)); */
							$request_data['user']['d_latitude'] = $request->D_latitude;
							$request_data['user']['d_longitude'] = $request->D_longitude;
						}
						$request_data['user']['src_address'] = $request->src_address;
						$request_data['user']['dest_address'] = $request->dest_address;
						$request_data['user']['rating'] = $user->rate;
						$request_data['user']['num_rating'] = $user->rate_count;
						
						$request_data['user']['payment_type'] = $request->payment_mode;
						$request_data['payment_mode'] = $request->payment_mode;

						// get options if exists
						if($requestOptions){
							// service type
							$request_data['options']['type_id'] 		= $requestOptions->service->type ;
							$request_data['options']['type'] 			= $requestOptions->service->getType->name ;
							$request_data['options']['type_icon'] 		= $requestOptions->service->getType->icon ;

							// service category
							$request_data['options']['category_id'] 	= $requestOptions->service->category ;
							$request_data['options']['category'] 		= $requestOptions->service->getTypeCategory->name ;

							// service price
							$request_data['options']['base_price_provider'] 	= $requestOptions->service->base_price_provider ;
							$request_data['options']['base_distance'] 			= $requestOptions->service->base_distance ;
							$request_data['options']['price_per_unit_distance'] = $requestOptions->service->price_per_unit_distance ;

							// request  options
							$request_data['options']['vehicle_brand'] = $requestOptions->vehicle_brand ;
							$request_data['options']['vehicle_plate'] = $requestOptions->vehicle_plate ;
							$request_data['options']['vehicle_observations'] = $requestOptions->vehicle_observations ;
						}
						
						$data['request_data'] = $request_data;
						array_push($all_requests, $data);
					}

					/* if ($counter) { */

					$setting_deactivation_time = Settings::where('key', 'deactivate_provider_time')->first();

					$response_array = array('success' => true, 'is_approved' => $provider_data->is_approved, 'is_approved_txt' => $txt_approve, 'is_available' => $provider_data->is_active, 'logoff_minimal_time' => $setting_deactivation_time ?  $setting_deactivation_time->value : 10, 'incoming_requests' => $all_requests);
					$response_code = 200;
					/* } else {
					  $response_array = array('success' => false, 'error' => 'no request found', 'error_code' => 505);
					  $response_code = 200;
					  } */
				} else {
					$response_array = array('success' => false, 'error' => trans('providerController.token_expired'), 'error_code' => 405);
					$response_code = 200;
				}
			} else {
				if ($is_admin) {
					/* $var = Keywords::where('id', 1)->first();
					  $response_array = array('success' => false, 'error' => "" . $var->keyword . ' ID not Found', 'error_code' => 410); */
					$response_array = array('success' => false, 'error' => trans('providerController.id_of') . "" . Config::get('app.generic_keywords.Provider') . "" . trans('providerController.not_found'), 'error_code' => 410);
				} else {
					$response_array = array('success' => false, 'error' => trans('providerController.token_not_valid'), 'error_code' => 406);
				}
				$response_code = 200;
			}
		}

		$response = Response::json($response_array, $response_code);
		return $response;
	}

	// Respond To Request

	public function respond_request() {

		$token = Input::get('token');
		$provider_id = Input::get('id');
		$request_id = Input::get('request_id');
		$accepted = Input::get('accepted');

		$date_time = Input::get('datetime');


		$validator = Validator::make(
						array(
					'token' => $token,
					'provider_id' => $provider_id,
					'request_id' => $request_id,
					'accepted' => $accepted,
						), array(
					'token' => 'required',
					'provider_id' => 'required|integer',
					'accepted' => 'required|integer',
					'request_id' => 'required|integer'
						), array(
					'token' => '',
					'provider_id' => trans('providerController.unique_id_missing'),
					'accepted' => trans('providerController.accept_reject_required'),
					'request_id' => trans('providerController.id_request_required')
						)
		);

		/* $driver = Keywords::where('id', 1)->first(); */

		if ($validator->fails()) {
			$error_messages = $validator->messages()->all();
			$response_array = array('success' => false, 'error' => trans('providerController.invalid_input'), 'error_code' => 401, 'error_messages' => $error_messages);
			$response_code = 200;
		} 
		else {
			$is_admin = $this->isAdmin($token);
			if ($provider_data = $this->getProviderData($provider_id, $token, $is_admin)) {
				// check for token validity
				if (is_token_active($provider_data->token_expiry) || $is_admin) {
					// Retrive and validate the Request
					if ($request = Requests::find($request_id)) {
						if ($request->is_cancelled != 1) {
							if ($request->current_provider == $provider_id) {
								// aceitou a requisicao
								if ($accepted == 1) {

									if ($request->later == 1) {
										// request ended
										Requests::where('id', '=', $request_id)->update(array('confirmed_provider' => $provider_id, 'status' => 1));
									} else {
										Requests::where('id', '=', $request_id)->update(array('confirmed_provider' => $provider_id, 'status' => 1, 'request_start_time' => date('Y-m-d H:i:s')));
									}

									//reload request object
									$request = Requests::find($request_id);

									$request->provider_acceptance_time = date("Y-m-d H:i:s");
									$request->save();

									// confirm provider
									RequestMeta::where('request_id', '=', $request_id)->where('provider_id', '=', $provider_id)->update(array('status' => RequestMeta::Confirm));

									// Update Provider availability - set unavailable
									Provider::where('id', '=', $provider_id)->update(array('is_available' => 0));

									// remove other schedule_meta
									RequestMeta::where('request_id', '=', $request_id)->where('status', '=', RequestMeta::Schedule)->delete();


									// Send Notification
									$provider = Provider::find($provider_id);

									$provider_services  = ProviderServices::where('provider_id', $provider->id)->first();
									
									$provider_data = array();
									$provider_data['first_name'] = $provider->first_name;
									$provider_data['last_name'] = $provider->last_name;
									$provider_data['phone'] = $provider->phone;
									$provider_data['bio'] = $provider->bio;
									$provider_data['picture'] = $provider->picture;
									$provider_data['latitude'] = $provider->latitude;
									$provider_data['longitude'] = $provider->longitude;
									$provider_data['type'] = ($provider_services ? $provider_services->type : null);
									$provider_data['rating'] = $provider->rate;
									$provider_data['num_rating'] = $provider->rate_count;
									$provider_data['car_model'] = $provider->car_model;
									$provider_data['car_number'] = $provider->car_number;

									$settings = Settings::where('key', 'default_distance_unit')->first();
									$default_distance_unit = $settings->value;
									if ($default_distance_unit == 0) {
										$default_distance_unit_set = 'kms';
									} elseif ($default_distance_unit == 1) {
										$default_distance_unit_set = 'miles';
									}

									$bill = array();

									if ($request->is_completed == 1) {
										$bill['distance'] = (string) convert($request->distance, $default_distance_unit);
										$bill['unit'] = $default_distance_unit_set;
										$bill['time'] = $request->time;
										$bill['base_price'] = $request->base_price;
										$bill['distance_cost'] = $request->distance_cost;
										$bill['time_cost'] = $request->time_cost;
										$bill['total'] = $request->total;
										$bill['is_paid'] = $request->is_paid;
									}

									if ($request->later == 1) {

										$date_time = $request->request_start_time;

										$datewant = new DateTime($date_time);
										$datetime = $datewant->format('Y-m-d H:i:s');

										$end_time = $datewant->add(new DateInterval('P0Y0M0DT2H0M0S'))->format('Y-m-d H:i:s');

										$provavail = ProviderAvail::where('provider_id', $provider_id)->where('start', '<=', $datetime)->where('end', '>=', $end_time)->first();
										$starttime = $provavail->start;
										$endtime = $provavail->end;
										$provavail->delete();

										if ($starttime == $datetime) {
											$provavail1 = new ProviderAvail;
											$provavail1->provider_id = $provider_id;
											$provavail1->start = $end_time;
											$provavail1->end = $endtime;
											$provavail1->save();
										} elseif ($endtime == $end_time) {
											$provavail1 = new ProviderAvail;
											$provavail1->provider_id = $provider_id;
											$provavail1->start = $starttime;
											$provavail1->end = $datetime;
											$provavail1->save();
										} else {
											$provavail1 = new ProviderAvail;
											$provavail1->provider_id = $provider_id;
											$provavail1->start = $starttime;
											$provavail1->end = $datetime;
											$provavail1->save();

											$provavail2 = new ProviderAvail;
											$provavail2->provider_id = $provider_id;
											$provavail2->start = $end_time;
											$provavail2->end = $endtime;
											$provavail2->save();
										}
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
										'is_cancelled' => $request->is_cancelled,
										'provider' => $provider_data,
										'bill' => $bill,
									);

									$title = "" . Config::get('app.generic_keywords.Provider') . trans('providerController.has_accepted_the') . Config::get('app.generic_keywords.Trip');

									$message = $response_array;

									send_notifications($request->user_id, 'user', $title, $message);

									// Send SMS 
									$user = User::find($request->user_id);
									$settings = Settings::where('key', 'sms_when_provider_accepts')->first();
									$pattern = $settings->value;
									$pattern = str_replace('%user%', $user->first_name . " " . $user->last_name, $pattern);
									$pattern = str_replace('%driver%', $provider->first_name . " " . $provider->last_name, $pattern);

									$pattern = str_replace('%driver_mobile%', $provider->phone, $pattern);
									sms_notification($request->user_id, 'user', $pattern);

									// Send SMS 
									$user = User::find($request->user_id);
									$src_address = get_address($request->latitude, $request->longitude);
									$pattern = Config::get('app.generic_keywords.User') . "" . trans('providerController.pickup_address') . "" . $src_address;
									sms_notification($provider_id, 'provider', $pattern);

									// Send SMS 

									$settings = Settings::where('key', 'sms_request_completed')->first();
									$pattern = $settings->value;
									$pattern = str_replace('%user%', $user->first_name . " " . $user->last_name, $pattern);
									$pattern = str_replace('%id%', $request->id, $pattern);
									$pattern = str_replace('%user_mobile%', $user->phone, $pattern);
									sms_notification(1, 'admin', $pattern);
									//email to client for accept the request
									$settings = Settings::where('key', 'admin_email_address')->first();
									$admin_email = $settings->value;
									$pattern = array(
										'admin_eamil' => $admin_email,
										'client_name' => ucwords($user->first_name . " " . $user->last_name),
										'web_url' => web_url(),
										'provider_name' => ucwords($provider->first_name . " " . $provider->last_name),
										'provider_contact' => $provider->phone,
										'provider_car_model' => $provider->car_model,
										'provider_licence' => $provider->car_number,
									);
									$subject = trans('providerController.get_ready_ride');
									email_notification($user->id, 'user', $pattern, $subject, 'user_request_accept_by_driver', null);
								}
								 else { //RESPOSTA NEGATIVA PARA A SOLICITACAO

									$time = date("Y-m-d H:i:s");
									$query = "SELECT id,user_id,current_provider,TIMESTAMPDIFF(SECOND,request_start_time, '$time') as diff from request where id = '$request_id'";
									$results = DB::select(DB::raw($query));
									$settings = Settings::where('key', 'provider_timeout')->first();
									$timeout = $settings->value;

									// Archiving Old Provider
									RequestMeta::where('request_id', '=', $request_id)->where('provider_id', '=', $provider_id)->update(array('status' => RequestMeta::Archive));

									$providerIdArray = RequestMeta::getProviderIdArray($request_id);
									$providerIdArray[] = $provider_id ;

									// update request 

									// pega o outro provedor mais proximo, ignorando usuarios ja chamados
									$providerNearest = Provider::getNearest($request->latitude, $request->longitude, $request->Type, $request->Category, $providerIdArray);

									if ($providerNearest) {

										// atualiza o prestador corrente
										Requests::where('id', '=', $request_id)->update(array('current_provider' => $providerNearest->id, 'request_start_time' => date("Y-m-d H:i:s")));

										// salva novo meta
										$request_meta = new RequestMeta;
										$request_meta->request_id = $request->id;
										$request_meta->provider_id = $providerNearest->id;
										$request_meta->save();

										// Send Notification

										$settings = Settings::where('key', 'provider_timeout')->first();
										$time_left = $settings->value;

										$user = User::find($request->user_id);
										$msg_array = array();
										$msg_array['unique_id'] 	= 1;
										$msg_array['request_id'] 	= $request->id;
										$msg_array['id'] 			= $providerNearest->id;
										if ($providerNearest) {
											$msg_array['token']	 	= $providerNearest->token;
										}
										$msg_array['time_left_to_respond'] = $time_left;
										$msg_array['payment_mode'] = $request->payment_mode;
										$msg_array['payment_type'] = $request->payment_mode;
										$msg_array['time_left_to_respond'] = $timeout;
										$msg_array['client_profile'] 			= array();
										$msg_array['client_profile']['name'] 	= $user->first_name . " " . $user->last_name;
										$msg_array['client_profile']['picture'] = $user->picture;
										$msg_array['client_profile']['bio'] 	= $user->bio;
										$msg_array['client_profile']['address'] = $user->address;
										$msg_array['client_profile']['phone'] 	= $user->phone;

										$request_data 					= array();
										$request_data['user'] 			= array();
										$request_data['user']['name'] 	= $user->first_name . " " . $user->last_name;
										$request_data['user']['picture'] 	= $user->picture;
										$request_data['user']['phone'] 		= $user->phone;
										$request_data['user']['address'] 	= $user->address;
										$request_data['user']['latitude'] 	= $request->latitude;
										$request_data['user']['longitude'] 	= $request->longitude;
										if ($request->d_latitude != NULL) {
											$request_data['user']['d_latitude'] 	= $request->D_latitude;
											$request_data['user']['d_longitude'] 	= $request->D_longitude;
										}
										$request_data['user']['user_dist_lat'] 	= $request->D_latitude;
										$request_data['user']['user_dist_long'] = $request->D_longitude;
										$request_data['user']['dest_latitude'] 	= $request->D_latitude;
										$request_data['user']['dest_longitude'] = $request->D_longitude;
										$request_data['user']['payment_type'] 	= $request->payment_mode;
										$request_data['user']['rating'] 		= $user->rate;
										$request_data['user']['num_rating']	 	= $user->rate_count;
										
										$msg_array['request_data'] = $request_data;

										$title = trans('providerController.new_request');

										$message = $msg_array;
										//enviar notificação para proximo provedor
										send_notifications($providerNearest->id, "provider", $title, $message);

										$provider_services  = ProviderServices::where('provider_id', $providerNearest->id)->first();

										$provider_data = array();
										$provider_data['first_name'] 	= $providerNearest->first_name;
										$provider_data['last_name'] 	= $providerNearest->last_name;
										$provider_data['phone'] 		= $providerNearest->phone;
										$provider_data['bio'] 			= $providerNearest->bio;
										$provider_data['picture'] 		= $providerNearest->picture;
										$provider_data['latitude'] 		= $providerNearest->latitude;
										$provider_data['longitude'] 	= $providerNearest->longitude;
										$provider_data['type'] 			= ($provider_services ? $provider_services->type : null);
										$provider_data['rating'] 		= $providerNearest->rate;
										$provider_data['num_rating'] 	= $providerNearest->rate_count;
										$provider_data['car_model'] 	= $providerNearest->car_model;
										$provider_data['car_number'] 	= $providerNearest->car_number;

										$message = array(
											'success' 				=> true,
											'request_id' 			=> $request_id,
											'status' 				=> $request->status,
											'confirmed_provider' 	=> $request->confirmed_provider,
											'is_provider_refused'	=> 1,
											'is_provider_started' 	=> $request->is_provider_started,
											'is_provider_arrived' 	=> $request->is_provider_arrived,
											'is_request_started' 	=> $request->is_started,
											'is_completed' 			=> $request->is_completed,
											'is_provider_rated' 	=> $request->is_provider_rated,
											'is_cancelled' 			=> $request->is_cancelled,
											'provider' 				=> $provider_data,
											'bill' 					=> array(),
										);

										$title = "" . Config::get('app.generic_keywords.Provider') . trans('providerController.has_rejected_the') . Config::get('app.generic_keywords.Trip');

										send_notifications($request->user_id, 'user', $title, $message);

									} 
									else { // nao achou nenhum provider
										// request ended
										Requests::where('id', '=', $request_id)->update(array('current_provider' => 0, 'status' => 1));

										// atualiza dado do provider
										// Update Provider availability - set unavailable
										Provider::where('id', '=', $provider_id)->update(array('is_available' => 1));

										$user = User::where('id', $request->user_id)->first();

										$driver_keyword = Config::get('app.generic_keywords.Provider');
										$user_data_id = $user->id;

										$message = array(
											'success' => false,
											'error_code' => 415,
										);

										send_notifications($user_data_id, 'user', trans('providerController.no_notification') . "" . $driver_keyword . "" . trans('providerController.found_notification'), $message);
									}
								}
								
								$response_array = array('success' => true,
									'request_id' => $request_id,
									'status' => $request->status,
									'confirmed_provider' => $request->confirmed_provider,
									'is_provider_started' => $request->is_provider_started,
									'is_provider_arrived' => $request->is_provider_arrived,
									'is_request_started' => $request->is_started,
									'is_completed' => $request->is_completed,
									'is_provider_rated' => $request->is_provider_rated,
									'is_cancelled' => $request->is_cancelled,);
								$response_code = 200;
							} else {
								
								$response_array = array('success' => false, 'error' => trans('providerController.request_id_not_match') . "" . Config::get('app.generic_keywords.Provider') . "" . trans('providerController.id'), 'error_code' => 472);
								$response_code = 200;
							}
						} else {
							$response_array = array('success' => false, 'error' => trans('providerController.request_canceled'), 'error_code' => 405);
							$response_code = 200;
						}
					} else {
						$response_array = array('success' => false, 'error' => trans('providerController.request_id_not_found'), 'error_code' => 405);
						$response_code = 200;
					}
				} else {
					$response_array = array('success' => false, 'error' => trans('providerController.token_expired'), 'error_code' => 405);
					$response_code = 200;
				}
			} else {
				if ($is_admin) {
					/* $response_array = array('success' => false, 'error' => "" . $driver->keyword . ' ID not Found', 'error_code' => 410); */
					$response_array = array('success' => false, 'error' => trans('providerController.id_of') . "" . Config::get('app.generic_keywords.Provider') . "" . trans('providerController.not_found'), 'error_code' => 410);
				} else {
					$response_array = array('success' => false, 'error' => trans('providerController.token_not_valid'), 'error_code' => 406);
				}
				$response_code = 200;
			}
		}

		$response = Response::json($response_array, $response_code);
		return $response;
	}

	// Get Request Status
	public function request_in_progress() {

		$token = Input::get('token');
		$provider_id = Input::get('id');

		$validator = Validator::make(
						array(
					'token' => $token,
					'provider_id' => $provider_id,
						), array(
					'token' => 'required',
					'provider_id' => 'required|integer',
						), array(
					'token' => '',
					'provider_id' => trans('providerController.unique_id_missing'),
						)
		);

		if ($validator->fails()) {
			$error_messages = $validator->messages()->all();
			$response_array = array('success' => false, 'error' => trans('providerController.invalid_input'), 'error_code' => 401, 'error_messages' => $error_messages);
			$response_code = 200;
		} else {
			$is_admin = $this->isAdmin($token);
			if ($provider_data = $this->getProviderData($provider_id, $token, $is_admin)) {
				// check for token validity
				if (is_token_active($provider_data->token_expiry) || $is_admin) {

					$request = Requests::where('status', '=', 1)->where('is_cancelled', '=', 0)->where('is_completed', '=', 0)->where('confirmed_provider', '=', $provider_id)->first();
					if ($request) {
						$request_id = $request->id;
					} else {
						$request_id = -1;
					}

					$txt_approve = trans('providerController.decline');
					if ($provider_data->is_approved) {
						$txt_approve = trans('providerController.Approved');
					}

					$response_array = array(
						'request_id' => $request_id,
						'is_approved' => $provider_data->is_approved,
						'is_available' => $provider_data->is_active,
						'is_approved_txt' => $txt_approve,
						'status_id' => $provider_data->status ? $provider_data->status->name : "EM_ANALISE" ,
						'success' => true,
					);
					$response_code = 200;
				} else {
					$response_array = array('success' => false, 'error' => trans('providerController.token_expired'), 'error_code' => 405);
					$response_code = 200;
				}
			} else {
				if ($is_admin) {
					/* $driver = Keywords::where('id', 1)->first();
					  $response_array = array('success' => false, 'error' => "" . $driver->keyword . "" . trans('providerController.id_not_found'), 'error_code' => 410); */
					$response_array = array('success' => false, 'error' => trans('providerController.id_of') . "" . Config::get('app.generic_keywords.Provider') . "" . trans('providerController.not_found'), 'error_code' => 410);
				} else {
					$response_array = array('success' => false, 'error' => trans('providerController.token_not_valid'), 'error_code' => 406);
				}
				$response_code = 200;
			}
		}

		$response = Response::json($response_array, $response_code);
		return $response;
	}

	// Get Request Status
	public function get_request() {

		$request_id = Input::get('request_id');
		$token = Input::get('token');
		$provider_id = Input::get('id');

		$validator = Validator::make(
						array(
					'request_id' => $request_id,
					'token' => $token,
					'provider_id' => $provider_id,
						), array(
					'request_id' => 'required|integer',
					'token' => 'required',
					'provider_id' => 'required|integer',
						), array(
					'request_id' => trans('providerController.id_request_required'),
					'token' => '',
					'provider_id' => trans('providerController.unique_id_missing'),
						)
		);

		if ($validator->fails()) {
			$error_messages = $validator->messages()->all();
			$response_array = array('success' => false, 'error' => trans('providerController.invalid_input'), 'error_code' => 401, 'error_messages' => $error_messages);
			$response_code = 200;
		} 
		else {
			$is_admin = $this->isAdmin($token);
			if ($provider_data = $this->getProviderData($provider_id, $token, $is_admin)) {
				// check for token validity
				if (is_token_active($provider_data->token_expiry)) {
					$txt_approve = trans('providerController.decline');
					if ($provider_data->is_approved) {
						$txt_approve = trans('providerController.Approved');
					}
					// Do necessary operations
					if ($request = Requests::find($request_id)) {
						if ($request->confirmed_provider == $provider_id) {

							$user = User::find($request->user_id);
							$request_data = array();
							$request_data['is_provider_started'] = $request->is_provider_started;
							$request_data['is_provider_arrived'] = $request->is_provider_arrived;
							$request_data['is_started'] = $request->is_started;
							$request_data['is_completed'] = $request->is_completed;
							$request_data['is_user_rated'] = $request->is_user_rated;
							$request_data['is_cancelled'] = $request->is_cancelled;
							$request_data['dest_latitude'] = $request->D_latitude;
							$request_data['dest_longitude'] = $request->D_longitude;
							$request_data['dest_address'] = $request->dest_address;
							$request_data['src_address'] = $request->src_address;

							$user_timezone = $user->timezone;
							$default_timezone = Config::get('app.timezone');

							$date_time = get_user_time($default_timezone, $user_timezone, $request->request_start_time);

							$request_data['accepted_time'] = $date_time;
							$request_data['payment_mode'] = $request->payment_mode;
							$request_data['payment_type'] = $request->payment_mode;
							if ($request->promo_code != '') {
								if ($request->promo_code != '') {
									$promo_code = PromoCodes::where('id', $request->promo_id)->first();
									$promo_value = $promo_code->value;
									$promo_type = $promo_code->type;
									if ($promo_type == 1) {
										$discount = $request->total * $promo_value / 100;
									} elseif ($promo_type == 2) {
										$discount = $promo_value;
									}
									$request_data['promo_discount'] = $discount;
								}
							}
							if ($request->is_started == 1) {

								$time = DB::table('request_location')
										->where('request_id', $request_id)
										->min('created_at');

								$date_time = get_user_time($default_timezone, $user_timezone, $time);

								$request_data['start_time'] = $date_time;

								$settings = Settings::where('key', 'default_distance_unit')->first();
								$default_distance_unit = $settings->value;

								$distance = DB::table('request_location')->where('request_id', $request_id)->max('distance');
								$request_data['distance'] = (string) convert($distance, $default_distance_unit);
								if ($default_distance_unit == 0) {
									$default_distance_unit_set = 'kms';
								} elseif ($default_distance_unit == 1) {
									$default_distance_unit_set = 'miles';
								}
								$request_data['unit'] = $default_distance_unit_set;

								$loc1 = RequestLocation::where('request_id', $request->id)->first();
								$loc2 = RequestLocation::where('request_id', $request->id)->orderBy('id', 'desc')->first();
								if ($loc1) {
									$time1 = strtotime($loc2->created_at);
									$time2 = strtotime($loc1->created_at);
									$difference = intval(($time1 - $time2) / 60);
								} else {
									$difference = 0;
								}
								$request_data['time'] = $difference;
								$request_data['time'] = $request->time;
							}

							if ($request->is_completed == 1) {
								$time = DB::table('request_location')
										->where('request_id', $request_id)
										->min('created_at');

								$date_time = get_user_time($default_timezone, $user_timezone, $time);

								$request_data['start_time'] = $date_time;

								$settings = Settings::where('key', 'default_distance_unit')->first();
								$default_distance_unit = $settings->value;

								$distance = DB::table('request_location')->where('request_id', $request_id)->max('distance');
								$request_data['distance'] = (string) convert($distance, $default_distance_unit);
								if ($default_distance_unit == 0) {
									$default_distance_unit_set = 'kms';
								} elseif ($default_distance_unit == 1) {
									$default_distance_unit_set = 'miles';
								}
								$request_data['unit'] = $default_distance_unit_set;

								$time = DB::table('request_location')
										->where('request_id', $request_id)
										->max('created_at');

								$end_time = get_user_time($default_timezone, $user_timezone, $time);

								$request_data['end_time'] = $end_time;
							}

							$request_data['user'] = array();
							$request_data['user']['name'] = $user->first_name . " " . $user->last_name;
							$request_data['user']['picture'] = $user->picture;
							$request_data['user']['phone'] = $user->phone;
							$request_data['user']['address'] = $user->address;
							$request_data['user']['latitude'] = $request->latitude;
							$request_data['user']['longitude'] = $request->longitude;
							$request_data['user']['src_address'] = $request->src_address;
							if ($request->D_latitude != NULL) {
								$request_data['user']['d_latitude'] = $request->D_latitude;
								$request_data['user']['d_longitude'] = $request->D_longitude;
							}
							$request_data['user']['user_dist_lat'] = $request->D_latitude;
							$request_data['user']['user_dist_long'] = $request->D_longitude;
							$request_data['user']['dest_latitude'] = $request->D_latitude;
							$request_data['user']['dest_longitude'] = $request->D_longitude;
							$request_data['user']['dest_address'] = $request->dest_address;
							$request_data['user']['rating'] = $user->rate;
							$request_data['user']['num_rating'] = $user->rate_count;
							/* $request_data['user']['rating'] = DB::table('review_user')->where('user_id', '=', $user->id)->avg('rating') ? : 0;
							  $request_data['user']['num_rating'] = DB::table('review_user')->where('user_id', '=', $user->id)->count(); */
							
							$request_data['bill'] = array();
							$bill = array();
							$settings = Settings::where('key', 'default_distance_unit')->first();
							$default_distance_unit = $settings->value;
							if ($default_distance_unit == 0) {
								$default_distance_unit_set = 'kms';
							} elseif ($default_distance_unit == 1) {
								$default_distance_unit_set = 'miles';
							}
							$requestserv = RequestServices::where('request_id', $request->id)->first();

							$request_typ = ProviderType::where('id', '=', $requestserv->type)->first();
							$setbase_distance = $request_typ->base_distance;
							$base_price = $request_typ->base_price;
							$price_per_unit_distance = $request_typ->price_per_unit_distance;
							$price_per_unit_time = $request_typ->price_per_unit_time;

							/* $currency_selected = Keywords::find(5); */
							if ($request->is_completed == 1) {
								$bill['distance'] = (string) $request->distance;
								$bill['unit'] = $default_distance_unit_set;
								$bill['time'] = $request->time;
								if ($requestserv->base_price != 0) {
									$bill['base_distance'] = $setbase_distance;
									$bill['base_price'] = currency_converted($requestserv->base_price);
									$bill['distance_cost'] = currency_converted($requestserv->distance_cost);
									$bill['time_cost'] = currency_converted($requestserv->time_cost);
								} else {
									/* $setbase_price = Settings::where('key', 'base_price')->first();
									  $bill['base_price'] = currency_converted($setbase_price->value);
									  $setdistance_price = Settings::where('key', 'price_per_unit_distance')->first();
									  $bill['distance_cost'] = currency_converted($setdistance_price->value);
									  $settime_price = Settings::where('key', 'price_per_unit_time')->first();
									  $bill['time_cost'] = currency_converted($settime_price->value); */
									$bill['base_distance'] = $setbase_distance;
									$bill['base_price'] = currency_converted($base_price);
									$bill['distance_cost'] = currency_converted($price_per_unit_distance);
									$bill['time_cost'] = currency_converted($price_per_unit_time);
								}

								$admins = Admin::first();
								$provider = Provider::where('id', $provider_id)->first();
								$bill['provider']['email'] = $provider->email;
								$bill['admin']['email'] = $admins->username;
								if ($request->transfer_amount != 0) {
									$bill['provider']['amount'] = currency_converted($request->total - $request->transfer_amount);
									$bill['admin']['amount'] = currency_converted($request->transfer_amount);
								} else {
									$bill['provider']['amount'] = currency_converted($request->transfer_amount);
									$bill['admin']['amount'] = currency_converted($request->total - $request->transfer_amount);
								}
								$discount = 0;
								if ($request->promo_code != '') {
									if ($request->promo_code != '') {
										$promo_code = PromoCodes::where('id', $request->promo_code)->first();
										if ($promo_code) {
											$promo_value = $promo_code->value;
											$promo_type = $promo_code->type;
											if ($promo_type == 1) {
												// Percent Discount
												$discount = $request->total * $promo_value / 100;
											} elseif ($promo_type == 2) {
												// Absolute Discount
												$discount = $promo_value;
											}
										}
									}
								}
								/* $bill['currency'] = $currency_selected->keyword; */
								$bill['currency'] = Config::get('app.generic_keywords.Currency');
								$bill['total'] = currency_converted($request->total);
								$bill['main_total'] = currency_converted($request->total);
								$bill['actual_total'] = currency_converted($request->total + $request->ledger_payment + $discount);
								$bill['total'] = currency_converted($request->total + $request->ledger_payment + $request->promo_payment);
								$bill['provider_value'] = currency_converted($request->provider_commission);
								$bill['referral_bonus'] = currency_converted($request->ledger_payment);
								$bill['promo_bonus'] = currency_converted($request->promo_payment);
								$bill['payment_type'] = $request->payment_mode;
								$bill['is_paid'] = $request->is_paid;
							}
							$request_data['bill'] = $bill;

							$cards = '';
							$cardlist = Payment::where('user_id', $user->id)->where('is_default', 1)->first();
							if (count($cardlist) >= 1) {
								$cards = array();
								$default = $cardlist->is_default;
								if ($default == 1) {
									$cards['is_default_text'] = "default";
								} else {
									$cards['is_default_text'] = "not_default";
								}
								$cards['card_id'] = $cardlist->id;
								$cards['user_id'] = $cardlist->user_id;
								$cards['customer_id'] = $cardlist->customer_id;
								$cards['last_four'] = $cardlist->last_four;
								$cards['card_token'] = $cardlist->card_token;
								$cards['card_type'] = $cardlist->card_type;
								$cards['is_default'] = $default;
							}
							$request_data['card_details'] = $cards;

							$chagre = array();

							/* $settings = Settings::where('key', 'default_distance_unit')->first();
							  $default_distance_unit = $settings->value;
							  if ($default_distance_unit == 0) {
							  $default_distance_unit_set = 'kms';
							  } elseif ($default_distance_unit == 1) {
							  $default_distance_unit_set = 'miles';
							  } */
							$chagre['unit'] = $default_distance_unit_set;

							$requestserv = RequestServices::where('request_id', $request->id)->first();
							if ($requestserv->base_price != 0) {
								$chagre['base_price'] = $requestserv->base_price;
								$chagre['distance_price'] = $requestserv->distance_cost;
								$chagre['price_per_unit_time'] = $requestserv->time_cost;
							} else {
								/* $setbase_price = Settings::where('key', 'base_price')->first();
								  $chagre['base_price'] = $setbase_price->value;
								  $setdistance_price = Settings::where('key', 'price_per_unit_distance')->first();
								  $chagre['distance_price'] = $setdistance_price->value;
								  $settime_price = Settings::where('key', 'price_per_unit_time')->first();
								  $chagre['price_per_unit_time'] = $settime_price->value; */
								$chagre['base_distance'] = $setbase_distance;
								$chagre['base_price'] = currency_converted($base_price);
								$chagre['distance_price'] = currency_converted($price_per_unit_distance);
								$chagre['price_per_unit_time'] = currency_converted($price_per_unit_time);
							}
							$chagre['total'] = $request->total;
							$chagre['is_paid'] = $request->is_paid;

							$request_data['charge_details'] = $chagre;

							$response_array = array('success' => true, 'is_available' => $provider_data->is_active, 'is_approved' => $provider_data->is_approved, 'is_approved_txt' => $txt_approve, 'request' => $request_data, 'bill' => $bill);
							$response_code = 200;
						} else {
							/* $driver = Keywords::where('id', 1)->first();
							  $response_array = array('success' => false, 'error' => trans('providerController.service_id_not_match') . "" . $driver->keyword . "" . trans('providerController.id'), 'error_code' => 407); */
							$response_array = array('success' => false, 'error' => trans('providerController.service_id_not_match') . "" . Config::get('app.generic_keywords.Provider') . "" . trans('providerController.id'), 'is_available' => $provider_data->is_active, 'is_approved' => $provider_data->is_approved, 'is_approved_txt' => $txt_approve, 'error_code' => 407);
							$response_code = 200;
						}
					} else {
						$response_array = array('success' => false, 'error' => trans('providerController.service_id_not_found'), 'is_available' => $provider_data->is_active, 'is_approved' => $provider_data->is_approved, 'is_approved_txt' => $txt_approve, 'error_code' => 408);
						$response_code = 200;
					}
				} else {
					$response_array = array('success' => false, 'error' => trans('providerController.token_expired'), 'error_code' => 405);
					$response_code = 200;
				}
			} 
			else {
				if ($is_admin) {
					/* $driver = Keywords::where('id', 1)->first();
					  $response_array = array('success' => false, 'error' => "" . $driver->keyword . ' ID not Found', 'error_code' => 410); */
					$response_array = array('success' => false, 'error' => trans('providerController.id_of') . "" . Config::get('app.generic_keywords.Provider') . "" . trans('providerController.not_found'), 'error_code' => 410);
				} else {
					$response_array = array('success' => false, 'error' => trans('providerController.token_not_valid'), 'error_code' => 406);
				}
				
				$response_code = 200;
			}
		}

		$response = Response::json($response_array, $response_code);
		return $response;
	}

	// Get Request Status
	public function get_request_location() {

		$request_id = Input::get('request_id');
		$token = Input::get('token');
		$provider_id = Input::get('id');
		$timestamp = Input::get('ts');

		$validator = Validator::make(
						array(
					'request_id' => $request_id,
					'token' => $token,
					'provider_id' => $provider_id,
						), array(
					'request_id' => 'required|integer',
					'token' => 'required',
					'provider_id' => 'required|integer',
						), array(
					'request_id' => trans('providerController.id_request_required'),
					'token' => '',
					'provider_id' => trans('providerController.unique_id_missing'),
						)
		);

		if ($validator->fails()) {
			$error_messages = $validator->messages()->all();
			$response_array = array('success' => false, 'error' => trans('providerController.invalid_input'), 'error_code' => 401, 'error_messages' => $error_messages);
			$response_code = 200;
		} else {
			$is_admin = $this->isAdmin($token);
			if ($provider_data = $this->getProviderData($provider_id, $token, $is_admin)) {
				// check for token validity
				if (is_token_active($provider_data->token_expiry) || $is_admin) {
					$status_txt = trans('providerController.not_active');
					if ($provider_data->is_active) {
						$status_txt = trans('providerController.active');
					}
					// Do necessary operations
					if ($request = Requests::find($request_id)) {
						if ($request->confirmed_provider == $provider_id) {

							if (isset($timestamp)) {
								$request_locations = RequestLocation::where('request_id', '=', $request_id)->where('created_at', '>', $timestamp)->orderBy('created_at')->get();
							} else {
								$request_locations = RequestLocation::where('request_id', '=', $request_id)->orderBy('created_at')->get();
							}
							$locations = array();
							$settings = Settings::where('key', 'default_distance_unit')->first();
							$default_distance_unit = $settings->value;
							foreach ($request_locations as $request_location) {
								$location = array();
								$location['latitude'] = $request_location->latitude;
								$location['longitude'] = $request_location->longitude;
								$location['distance'] = convert($request_location->distance, $default_distance_unit);
								$location['bearing'] = $request_location->bearing;
								$location['timestamp'] = $request_location->created_at;
								array_push($locations, $location);
							}

							$response_array = array(
								'success' => true,
								'is_active' => $provider_data->is_active,
								'is_approved' => $provider_data->is_approved,
								'locationdata' => $locations,
							);
							$response_code = 200;
						} else {
							/* $driver = Keywords::where('id', 1)->first();
							  $response_array = array('success' => false, 'error' => trans('providerController.service_id_not_match') . "" . $driver->keyword . "" . trans('providerController.id'), 'error_code' => 407); */
							$response_array = array(
								'success' => false,
								'is_active' => $provider_data->is_active,
								'is_approved' => $provider_data->is_approved,
								'error' => trans('providerController.service_id_not_match') . "" . Config::get('app.generic_keywords.Provider') . "" . trans('providerController.id'),
								'error_code' => 407,
							);
							$response_code = 200;
						}
					} else {
						$response_array = array(
							'success' => false,
							'is_active' => $provider_data->is_active,
							'is_approved' => $provider_data->is_approved,
							'error' => trans('providerController.service_id_not_found'),
							'error_code' => 408,
						);
						$response_code = 200;
					}
				} else {
					$response_array = array('success' => false, 'error' => trans('providerController.token_expired'), 'error_code' => 405);
					$response_code = 200;
				}
			} else {
				if ($is_admin) {
					/* $driver = Keywords::where('id', 1)->first();
					  $response_array = array('success' => false, 'error' => "" . $driver->keyword . "" . trans('providerController.id_not_found'), 'error_code' => 410); */
					$response_array = array('success' => false, 'error' => trans('providerController.id_of') . "" . Config::get('app.generic_keywords.Provider') . "" . trans('providerController.not_found'), 'error_code' => 410);
				} else {
					$response_array = array('success' => false, 'error' => trans('providerController.token_not_valid'), 'error_code' => 406);
				}
				$response_code = 200;
			}
		}

		$response = Response::json($response_array, $response_code);
		return $response;
	}

	// provider started
	public function request_provider_started() {
		if (Request::isMethod('post')) {
			$request_id = Input::get('request_id');
			$token = Input::get('token');
			$provider_id = Input::get('id');
			$latitude = Input::get('latitude');
			$longitude = Input::get('longitude');
			if (Input::has('bearing')) {
				$angle = Input::get('bearing');
			}

			$validator = Validator::make(
							array(
						'request_id' => $request_id,
						'token' => $token,
						'provider_id' => $provider_id,
						'latitude' => $latitude,
						'longitude' => $longitude,
							), array(
						'request_id' => 'required|integer',
						'token' => 'required',
						'provider_id' => 'required|integer',
						'latitude' => 'required',
						'longitude' => 'required',
							), array(
						'request_id' => trans('providerController.id_request_required'),
						'token' => '',
						'provider_id' => trans('providerController.unique_id_missing'),
						'latitude' => trans('providerController.location_point_missing'),
						'longitude' => trans('providerController.location_point_missing'),
							)
			);

			if ($validator->fails()) {
				$error_messages = $validator->messages()->all();
				$response_array = array('success' => false, 'error' => trans('providerController.invalid_input'), 'error_code' => 401, 'error_messages' => $error_messages);
				$response_code = 200;
			} else {
				$is_admin = $this->isAdmin($token);
				if ($provider_data = $this->getProviderData($provider_id, $token, $is_admin)) {
					// check for token validity
					if (is_token_active($provider_data->token_expiry) || $is_admin) {
						// Do necessary operations
						if ($request = Requests::find($request_id)) {
							if ($request->confirmed_provider == $provider_id) {

								if ($request->confirmed_provider != 0) {
									$request->is_provider_started = 1;
									$request->save();

									$location = get_location($latitude, $longitude);
									$latitude = $location['lat'];
									$longitude = $location['long'];

									if (!isset($angle)) {
										$angle = get_angle($provider_data->latitude, $provider_data->longitude, $latitude, $longitude);
									}

									$provider_data->old_latitude = $provider_data->latitude;
									$provider_data->old_longitude = $provider_data->longitude;
									$provider_data->bearing = $angle;
									$provider_data->latitude = $latitude;
									$provider_data->longitude = $longitude;
									$provider_data->save();

									// Send Notification
									$msg_array = array();
									$provider = Provider::find($request->confirmed_provider);
									$provider_services  = ProviderServices::where('provider_id', $provider->id)->first();

									$provider_data = array();
									$provider_data['first_name'] = $provider->first_name;
									$provider_data['last_name'] = $provider->last_name;
									$provider_data['phone'] = $provider->phone;
									$provider_data['bio'] = $provider->bio;
									$provider_data['picture'] = $provider->picture;
									$provider_data['latitude'] = $provider->latitude;
									$provider_data['longitude'] = $provider->longitude;
									$provider_data['type'] = ($provider_services ? $provider_services->type : null);
									$provider_data['rating'] = $provider->rate;
									$provider_data['num_rating'] = $provider->rate_count;
									$provider_data['car_model'] = $provider->car_model;
									$provider_data['car_number'] = $provider->car_number;
									/* $provider_data['rating'] = DB::table('review_provider')->where('provider_id', '=', $provider->id)->avg('rating') ? : 0;
									  $provider_data['num_rating'] = DB::table('review_provider')->where('provider_id', '=', $provider->id)->count(); */

									$settings = Settings::where('key', 'default_distance_unit')->first();
									$default_distance_unit = $settings->value;
									if ($default_distance_unit == 0) {
										$default_distance_unit_set = 'kms';
									} elseif ($default_distance_unit == 1) {
										$default_distance_unit_set = 'miles';
									}
									$bill = array();
									if ($request->is_completed == 1) {
										$bill['distance'] = (string) convert($request->distance, $default_distance_unit);
										$bill['unit'] = $default_distance_unit_set;
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
										'payment_mode' => $request->payment_data,
										'provider' => $provider_data,
										'bill' => $bill,
									);

									$message = $response_array;
									/* $driver = Keywords::where('id', 1)->first();
									  $title = "" . $driver->keyword . "" . trans('providerController.started_moving_you'); */
									$title = "" . Config::get('app.generic_keywords.Provider') . "" . trans('providerController.started_moving_you');

									send_notifications($request->user_id, 'user', $title, $message);


									$response_array = array('success' => true);
									$response_code = 200;
								} else {
									/* $driver = Keywords::where('id', 1)->first();
									  $response_array = array('success' => false, 'error' => "" . $driver->keyword . "" . trans('providerController.not_yet_confirmed'), 'error_code' => 413); */
									$response_array = array('success' => false, 'error' => "" . Config::get('app.generic_keywords.Provider') . "" . trans('providerController.not_yet_confirmed'), 'error_code' => 413);
									$response_code = 200;
								}
							} else {
								/* $driver = Keywords::where('id', 1)->first();
								  $response_array = array('success' => false, 'error' => trans('providerController.service_id_not_match') . "" . $driver->keyword . "" . trans('providerController.id'), 'error_code' => 407); */
								$response_array = array('success' => false, 'error' => trans('providerController.service_id_not_match') . "" . Config::get('app.generic_keywords.Provider') . "" . trans('providerController.id'), 'error_code' => 407);
								$response_code = 200;
							}
						} else {
							$response_array = array('success' => false, 'error' => trans('providerController.service_id_not_found'), 'error_code' => 408);
							$response_code = 200;
						}
					} else {
						$response_array = array('success' => false, 'error' => trans('providerController.token_expired'), 'error_code' => 405);
						$response_code = 200;
					}
				} else {
					if ($is_admin) {
						/* $driver = Keywords::where('id', 1)->first();
						  $response_array = array('success' => false, 'error' => "" . $driver->keyword . ' ID not Found',"" . trans('providerController.id_not_found') 'error_code' => 410); */
						$response_array = array('success' => false, 'error' => trans('providerController.id_of') . "" . Config::get('app.generic_keywords.Provider') . "" . trans('providerController.not_found'), 'error_code' => 410);
					} else {
						$response_array = array('success' => false, 'error' => trans('providerController.token_not_valid'), 'error_code' => 406);
					}
					$response_code = 200;
				}
			}
		}
		$response = Response::json($response_array, $response_code);
		return $response;
	}

	// prestador chegou a localizacao do cliente
	public function request_provider_arrived() {
		if (Request::isMethod('post')) {
			$request_id = Input::get('request_id');
			$token = Input::get('token');
			$provider_id = Input::get('id');
			$latitude = Input::get('latitude');
			$longitude = Input::get('longitude');
			if (Input::has('bearing')) {
				$angle = Input::get('bearing');
			}

			$validator = Validator::make(
							array(
						'request_id' => $request_id,
						'token' => $token,
						'provider_id' => $provider_id,
						'latitude' => $latitude,
						'longitude' => $longitude,
							), array(
						'request_id' => 'required|integer',
						'token' => 'required',
						'provider_id' => 'required|integer',
						'latitude' => 'required',
						'longitude' => 'required',
							), array(
						'request_id' => trans('providerController.id_request_required'),
						'token' => '',
						'provider_id' => trans('providerController.unique_id_missing'),
						'latitude' => trans('providerController.location_point_missing'),
						'longitude' => trans('providerController.location_point_missing'),
							)
			);


			if ($validator->fails()) {
				$error_messages = $validator->messages()->all();
				$response_array = array('success' => false, 'error' => trans('providerController.invalid_input'), 'error_code' => 401, 'error_messages' => $error_messages);
				$response_code = 200;
			}

			else {
				$is_admin = $this->isAdmin($token);
				if ($provider_data = $this->getProviderData($provider_id, $token, $is_admin)) {
					// check for token validity
					if (is_token_active($provider_data->token_expiry) || $is_admin) {
						// Do necessary operations
						if ($request = Requests::find($request_id)) {
							if ($request->confirmed_provider == $provider_id) {

								if ($request->is_provider_started == 1) {
									//atualizar requisição
									$request->is_provider_arrived = 1;
									$request->save();

									//atualizar localização do prestador
									$location = get_location($latitude, $longitude);
									$latitude = $location['lat'];
									$longitude = $location['long'];
									if (!isset($angle)) {
										$angle = get_angle($provider_data->latitude, $provider_data->longitude, $latitude, $longitude);
									}
									$provider_data->old_latitude = $provider_data->latitude;
									$provider_data->old_longitude = $provider_data->longitude;
									$provider_data->bearing = $angle;
									$provider_data->latitude = $latitude;
									$provider_data->longitude = $longitude;
									$provider_data->save();
										
									//cobrar valor base do cliente		
									RequestCharging::request_charge_base_price($request->id);

									$request = Requests::find($request->id);

									//problema na cobrança da taxa base: cancelar corrida
									if($request->is_base_fee_paid == 0){
			                            //cancela corrida
			                            Requests::where('id', $request->id)->update(array('is_cancelled' => 1));
			                            RequestMeta::where('request_id', $request->id)->update(array('is_cancelled' => 1));

			                            if ($request->confirmed_provider) {

			                            	//seta prestador como disponivel
			                                $provider = Provider::find($request->confirmed_provider);
			                                $provider->is_available = 1;
			                                $provider->save();

			                            	//envia notificacao para prestador	                            
			                                $msg_array = array(
                       							'success' => true,
												'request_id' => $request->id,
												'status' => $request->status,
												'confirmed_provider' => $request->confirmed_provider,
												'is_cancelled' => 1,
												'is_provider_started' => $request->is_provider_started,
												'is_provider_arrived' => $request->is_provider_arrived,
												'is_request_started' => $request->is_started,
												'is_completed' => $request->is_completed,
												'is_provider_rated' => $request->is_provider_rated,
												'is_cancelled' => $request->is_cancelled
											);
			                                $msg_array['request_id'] = $request_id;
			                                $msg_array['unique_id'] = 2;
      

			                                $user = User::find($request->user_id);
			                                $request_data = array();
			                                $request_data['user'] = array();
			                                $request_data['user']['name'] = $user->first_name . " " . $user->last_name;
			                                $request_data['user']['picture'] = $user->picture;
			                                $request_data['user']['phone'] = $user->phone;
			                                $request_data['user']['address'] = $user->address;
			                                $request_data['user']['latitude'] = $request->latitude;
			                                $request_data['user']['longitude'] = $request->longitude;
			                                $request_data['user']['rating'] = $user->rate;
			                                $request_data['user']['num_rating'] = $user->rate_count;

			                                $msg_array['request_data'] = $request_data;

			                                $title = trans('customerController.request_cancelled');
			                                $message = $msg_array;
			                                send_notifications($request->current_provider, "provider", $title, $message);

			                                $title = trans('customerController.request_cancelled');
			                                $message = $msg_array;
			                                send_notifications($request->user_id, "user", $title, $message);
			                            }

										$response_array = array('success' => false);
										$response_code = 200;
									}

									//taxa base cobrada com sucesso
									else{
										// enviar notificação para cliente
										$provider = Provider::find($request->confirmed_provider);
										$provider_services  = ProviderServices::where('provider_id', $provider->id)->first();

										$provider_data = array();
										$provider_data['first_name'] = $provider->first_name;
										$provider_data['last_name'] = $provider->last_name;
										$provider_data['phone'] = $provider->phone;
										$provider_data['bio'] = $provider->bio;
										$provider_data['picture'] = $provider->picture;
										$provider_data['latitude'] = $provider->latitude;
										$provider_data['longitude'] = $provider->longitude;
										$provider_data['type'] = ($provider_services ? $provider_services->type : null);
										$provider_data['rating'] = $provider->rate;
										$provider_data['num_rating'] = $provider->rate_count;
										$provider_data['car_model'] = $provider->car_model;
										$provider_data['car_number'] = $provider->car_number;

										$settings = Settings::where('key', 'default_distance_unit')->first();
										$default_distance_unit = $settings->value;
										if ($default_distance_unit == 0) {
											$default_distance_unit_set = 'kms';
										}
										elseif ($default_distance_unit == 1) {
											$default_distance_unit_set = 'miles';
										}
										$bill = array();
										if ($request->is_completed == 1) {
											$bill['distance'] = (string) convert($request->distance, $default_distance_unit);
											$bill['unit'] = $default_distance_unit_set;
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
											'payment_mode' => $request->payment_data,
											'bill' => $bill,
										);
		
										$title = "" . Config::get('app.generic_keywords.Provider') . "" . trans('providerController.arrived_your_place');

										$message = $response_array;

										send_notifications($request->user_id, 'user', $title, $message);

										// Enviar SMS
										$user = User::find($request->user_id);
										$settings = Settings::where('key', 'sms_when_provider_arrives')->first();
										$pattern = $settings->value;
										$pattern = str_replace('%user%', $user->first_name . " " . $user->last_name, $pattern);
										$pattern = str_replace('%driver%', $provider->first_name . " " . $provider->last_name, $pattern);
										$pattern = str_replace('%driver_mobile%', $provider->phone, $pattern);
										sms_notification($request->user_id, 'user', $pattern);

										$response_array = array('success' => true);
										$response_code = 200;
									}
								} else {
									$response_array = array('success' => false, 'error' => trans('providerController.service_not_started'), 'error_code' => 413);
									$response_code = 200;
								}
							}
							else {
								$response_array = array('success' => false, 'error' => trans('providerController.service_id_not_match') . "" . Config::get('app.generic_keywords.Provider') . "" . trans('providerController.id'), 'error_code' => 407);
								$response_code = 200;
							}
						} else {
							$response_array = array('success' => false, 'error' => trans('providerController.service_id_not_found'), 'error_code' => 408);
							$response_code = 200;
						}
					} else {
						$response_array = array('success' => false, 'error' => trans('providerController.token_expired'), 'error_code' => 405);
						$response_code = 200;
					}
				} else {
					if ($is_admin) {
						$response_array = array('success' => false, 'error' => trans('providerController.id_of') . "" . Config::get('app.generic_keywords.Provider') . "" . trans('providerController.not_found'), 'error_code' => 410);
					} else {
						$response_array = array('success' => false, 'error' => trans('providerController.token_not_valid'), 'error_code' => 406);
					}
					$response_code = 200;
				}
			}
		}
		$response = Response::json($response_array, $response_code);
		return $response;
	}

	// request started
	public function request_started() {
		if (Request::isMethod('post')) {
			$request_id = Input::get('request_id');
			$token = Input::get('token');
			$provider_id = Input::get('id');
			$latitude = Input::get('latitude');
			$longitude = Input::get('longitude');
			if (Input::has('bearing')) {
				$angle = Input::get('bearing');
			}

			$validator = Validator::make(
							array(
						'request_id' => $request_id,
						'token' => $token,
						'provider_id' => $provider_id,
						'latitude' => $latitude,
						'longitude' => $longitude,
							), array(
						'request_id' => 'required|integer',
						'token' => 'required',
						'provider_id' => 'required|integer',
						'latitude' => 'required',
						'longitude' => 'required',
							), array(
						'request_id' => trans('providerController.id_request_required'),
						'token' => '',
						'provider_id' => trans('providerController.unique_id_missing'),
						'latitude' => trans('providerController.location_point_missing'),
						'longitude' => trans('providerController.location_point_missing'),
							)
			);

			/* $var = Keywords::where('id', 1)->first(); */

			if ($validator->fails()) {
				$error_messages = $validator->messages()->all();
				$response_array = array('success' => false, 'error' => trans('providerController.invalid_input'), 'error_code' => 401, 'error_messages' => $error_messages);
				$response_code = 200;
			} else {
				$is_admin = $this->isAdmin($token);
				if ($provider_data = $this->getProviderData($provider_id, $token, $is_admin)) {
					// check for token validity
					if (is_token_active($provider_data->token_expiry) || $is_admin) {
						// Do necessary operations
						if ($request = Requests::find($request_id)) {
							if ($request->confirmed_provider == $provider_id) {

								if ($request->is_provider_arrived == 1) {
									$request->is_started = 1;
									$request->save();

									$location = get_location($latitude, $longitude);
									$latitude = $location['lat'];
									$longitude = $location['long'];
									if (!isset($angle)) {
										$angle = get_angle($provider_data->latitude, $provider_data->longitude, $latitude, $longitude);
									}

									$countRequestLocation = RequestLocation::where('request_id', $request->id)->count();
									if($countRequestLocation == 0){
										$request_location = new RequestLocation;
										$request_location->latitude = $latitude;
										$request_location->longitude = $longitude;
										$request_location->request_id = $request_id;
										$request_location->bearing = $angle;
										$request_location->save();
									}

									// Send Notification
									$provider = Provider::find($request->confirmed_provider);
									$provider->old_latitude = $provider->latitude;
									$provider->old_longitude = $provider->longitude;
									$provider->latitude = $latitude;
									$provider->longitude = $longitude;
									$provider->bearing = $angle;
									$provider->save();

									$provider_services  = ProviderServices::where('provider_id', $provider->id)->first();

									$provider_data = array();
									$provider_data['first_name'] = $provider->first_name;
									$provider_data['last_name'] = $provider->last_name;
									$provider_data['phone'] = $provider->phone;
									$provider_data['bio'] = $provider->bio;
									$provider_data['picture'] = $provider->picture;
									$provider_data['latitude'] = $provider->latitude;
									$provider_data['longitude'] = $provider->longitude;
									$provider_data['type'] = ($provider_services ? $provider_services->type : null);
									$provider_data['rating'] = $provider->rate;
									$provider_data['num_rating'] = $provider->rate_count;
									$provider_data['car_model'] = $provider->car_model;
									$provider_data['car_number'] = $provider->car_number;
									/* $provider_data['rating'] = DB::table('review_provider')->where('provider_id', '=', $provider->id)->avg('rating') ? : 0;
									  $provider_data['num_rating'] = DB::table('review_provider')->where('provider_id', '=', $provider->id)->count(); */

									$settings = Settings::where('key', 'default_distance_unit')->first();
									$default_distance_unit = $settings->value;
									if ($default_distance_unit == 0) {
										$default_distance_unit_set = 'kms';
									} elseif ($default_distance_unit == 1) {
										$default_distance_unit_set = 'miles';
									}
									$bill = array();
									if ($request->is_completed == 1) {
										$bill['distance'] = (string) convert($request->distance, $default_distance_unit);
										$bill['unit'] = $default_distance_unit_set;
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
										'payment_mode' => $request->payment_data,
										'bill' => $bill,
									);
									/* $var = Keywords::where('id', 4)->first();
									  $title = trans('providerController.your2') . "" . $var->keyword . "" . trans('providerController.has_been_started'); */
									$title = trans('providerController.your3') . "" . Config::get('app.generic_keywords.Trip') . "" . trans('providerController.has_been_started');

									$message = $response_array;

									send_notifications($request->user_id, 'user', $title, $message);


									$response_array = array('success' => true);
									$response_code = 200;
								} else {
									/* $response_array = array('success' => false, 'error' => "" . $var->keyword . "" . trans('providerController.not_yet_arrived'), 'error_code' => 413); */
									$response_array = array('success' => false, 'error' => "" . Config::get('app.generic_keywords.Provider') . "" . trans('providerController.not_yet_arrived'), 'error_code' => 413);
									$response_code = 200;
								}
							} else {
								/* $response_array = array('success' => false, 'error' => trans('providerController.service_id_not_match') . "" . $var->keyword . "" . trans('providerController.id'), 'error_code' => 407); */
								$response_array = array('success' => false, 'error' => trans('providerController.service_id_not_match') . "" . Config::get('app.generic_keywords.Provider') . "" . trans('providerController.id'), 'error_code' => 407);
								$response_code = 200;
							}
						} else {
							$response_array = array('success' => false, 'error' => trans('providerController.service_id_not_found'), 'error_code' => 408);
							$response_code = 200;
						}
					} else {
						$response_array = array('success' => false, 'error' => trans('providerController.token_expired'), 'error_code' => 405);
						$response_code = 200;
					}
				} else {
					if ($is_admin) {
						/* $response_array = array('success' => false, 'error' => "" . $var->keyword . ' ID not Found', 'error_code' => 410); */
						$response_array = array('success' => false, 'error' => trans('providerController.id_of') . "" . Config::get('app.generic_keywords.Provider') . "" . trans('providerController.not_found'), 'error_code' => 410);
					} else {
						$response_array = array('success' => false, 'error' => trans('providerController.token_not_valid'), 'error_code' => 406);
					}
					$response_code = 200;
				}
			}
		}
		$response = Response::json($response_array, $response_code);
		return $response;
	}

	// request completed
	public function request_completed() {
		if (Request::isMethod('post')) {
			$request_id = Input::get('request_id');
			$token = Input::get('token');
			$provider_id = Input::get('id');
			$latitude = Input::get('latitude');
			$longitude = Input::get('longitude');
			$distance = Input::get('distance');
			$time = Input::get('time');
			if (Input::has('bearing')) {
				$angle = Input::get('bearing');
			}

			$validator = Validator::make(
					array(
						'request_id' => $request_id,
						'token' => $token,
						'provider_id' => $provider_id,
						'latitude' => $latitude,
						'longitude' => $longitude,
						'distance' => $distance,
					),
					array(
						'request_id' => 'required|integer',
						'token' => 'required',
						'provider_id' => 'required|integer',
						'latitude' => 'required',
						'longitude' => 'required',
						'distance' => 'required',
					),
					array(
						'request_id' => trans('providerController.id_request_required'),
						'token' => '',
						'provider_id' => trans('providerController.unique_id_missing'),
						'latitude' => trans('providerController.location_point_missing'),
						'longitude' => trans('providerController.location_point_missing'),
						'distance' => "" . trans('providerController.distance_required'),
					)
			);

			if ($validator->fails()) {
				$error_messages = $validator->messages()->all();
				$response_array = array('success' => false, 'error' => trans('providerController.invalid_input'), 'error_code' => 401, 'error_messages' => $error_messages);
				$response_code = 200;
			}
			else {
				$request_payment_mode = '';
				$payment_type = '';
				$provider_payment_remaining = $provider_refund_remaining = 0;
				$is_admin = $this->isAdmin($token);

				if ($provider = $this->getProviderData($provider_id, $token, $is_admin)) {

					// check for token validity
					if (is_token_active($provider->token_expiry) || $is_admin) {

						$provider_services  = ProviderServices::where('provider_id', $provider->id)->first();
						$provider_type = ProviderType::where('id', $provider_services->type)->first();
						$provider_bank_account = ProviderBankAccount::where('provider_id', $provider->id)->first();

						// Do necessary operations 
						if ($request = Requests::find($request_id)) {
							if ($request->confirmed_provider == $provider_id) {		
								if ($request->is_started == 1) {

									//salvar hora do fim da corrida
									$request->request_finish_time = date("Y-m-d H:i:s");
									$request->save();

									//calcular tempo total da corrida
									$time = floor((strtotime($request->request_finish_time) - strtotime($request->request_start_time))/60);

									//cobrança
									RequestCharging::request_complete_charge($request->id, $distance, $time);
									
									$request = Requests::find($request->id);

									//atualizar dados do prestador
									$provider->is_available = 1;
									$location = get_location($latitude, $longitude);
									$latitude = $location['lat'];
									$longitude = $location['long'];
									if (!isset($angle)) {
										$angle = get_angle($provider->latitude, $provider->longitude, $latitude, $longitude);
									}
									$provider->payment_remaining = $provider->payment_remaining + $provider_payment_remaining;
									$provider->refund_remaining = $provider->refund_remaining + $provider_refund_remaining;
									$provider->old_latitude = $provider->latitude;
									$provider->old_longitude = $provider->longitude;
									$provider->latitude = $latitude;
									$provider->longitude = $longitude;
									$provider->bearing = $angle;
									$provider->save();

									$request_location = new RequestLocation;
									$request_location->latitude = $latitude;
									$request_location->longitude = $longitude;
									$request_location->request_id = $request_id;
									$request_location->distance = $distance;
									$request_location->bearing = $angle;
									$request_location->save();

									/* RETORNOS */

									$provider_services  = ProviderServices::where('provider_id', $provider->id)->first();
									
									// Send Notification
									$provider_data = array();
									$provider_data['first_name'] = $provider->first_name;
									$provider_data['last_name'] = $provider->last_name;
									$provider_data['phone'] = $provider->phone;
									$provider_data['bio'] = $provider->bio;
									$provider_data['picture'] = $provider->picture;
									$provider_data['latitude'] = $provider->latitude;
									$provider_data['longitude'] = $provider->longitude;
									$provider_data['type'] = ($provider_services ? $provider_services->type : null);
									$provider_data['rating'] = $provider->rate;
									$provider_data['num_rating'] = $provider->rate_count;
									$provider_data['car_model'] = $provider->car_model;
									$provider_data['car_number'] = $provider->car_number;


									$requestserv = RequestServices::where('request_id', $request->id)->first();
									$bill = array();
									/* $currency_selected = Keywords::find(5); */
									if ($request->is_completed == 1) {
										$settings = Settings::where('key', 'default_distance_unit')->first();
										$default_distance_unit = $settings->value;
										$bill['payment_mode'] = $request->payment_mode;
										$bill['distance'] = (string) $distance;
										if ($default_distance_unit == 0) {
											$default_distance_unit_set = 'kms';
										} elseif ($default_distance_unit == 1) {
											$default_distance_unit_set = 'miles';
										}
										$bill['unit'] = $default_distance_unit_set;
										$bill['time'] = floatval(sprintf2($request->time, 2));
										if ($requestserv->base_price != 0) {
											$bill['base_price'] = currency_converted($requestserv->base_price);
											$bill['distance_cost'] = currency_converted($requestserv->distance_cost);
											$bill['time_cost'] = currency_converted(floatval(sprintf2($requestserv->time_cost, 2)));
										} else {
											$bill['base_price'] = currency_converted($provider_type->base_price);
											$bill['distance_cost'] = currency_converted($provider_type->price_per_unit_distance);
											$bill['time_cost'] = currency_converted(floatval(sprintf2($provider_type->price_per_unit_time, 2)));
										}
										$bill['price_per_unit_distance'] = currency_converted($provider_type->price_per_unit_distance);
										$bill['price_per_unit_time'] = currency_converted($provider_type->price_per_unit_time);

										$admins = Admin::first();
										$bill['provider']['email'] = $provider->email;
										$bill['admin']['email'] = $admins->username;
										if ($request->transfer_amount != 0) {
											$bill['provider']['amount'] = currency_converted($request->total - $request->transfer_amount);
											$bill['admin']['amount'] = currency_converted($request->transfer_amount);
										} else {
											$bill['provider']['amount'] = currency_converted($request->transfer_amount);
											$bill['admin']['amount'] = currency_converted($request->total - $request->transfer_amount);
										}

										$bill['currency'] = Config::get('app.generic_keywords.Currency');
										$bill['actual_total'] = currency_converted($request->total);
										$bill['total'] = currency_converted($request->total);
										$bill['is_paid'] = $request->is_paid;
										$bill['promo_discount'] = currency_converted($request->promo_payment);

										$bill['main_total'] = currency_converted($request->total);
										$bill['total'] = currency_converted($request->total - $request->ledger_payment - $request->promo_payment);
										$bill['referral_bonus'] = currency_converted($request->ledger_payment);
										$bill['promo_bonus'] = currency_converted($request->promo_payment);
										$bill['provider_value'] = currency_converted($request->provider_commission);
										$bill['payment_type'] = $request->payment_mode;
										$bill['is_paid'] = $request->is_paid;
									}

									$rservc = RequestServices::where('request_id', $request->id)->get();
									$typs = array();
									$typi = array();
									$typp = array();
									foreach ($rservc as $typ) {
										$typ1 = ProviderType::where('id', $typ->type)->first();
										$typ_price = ProviderServices::where('provider_id', $request->confirmed_provider)->where('type', $typ->type)->first();

										if ($typ_price->base_price > 0) {
											$typp1 = 0.00;
											$typp1 = $typ_price->base_price;
										} elseif ($typ_price->price_per_unit_distance > 0) {
											$typp1 = 0.00;
											foreach ($rservc as $key) {
												$typp1 = $typp1 + $key->distance_cost;
											}
										} else
											$typp1 = 0.00;

										$typs['name'] = $typ1->name;
										// $typs['icon']=$typ1->icon;
										$typs['price'] = $typp1;

										array_push($typi, $typs);
									} $bill['type'] = $typi;
									$rserv = RequestServices::where('request_id', $request_id)->get();
									$typs = array();
									foreach ($rserv as $typ) {
										$typ1 = ProviderType::where('id', $typ->type)->first();
										array_push($typs, $typ1->name);
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
										'payment_mode' => $request->payment_mode,
										'bill' => $bill,
										'payment_option' => $request->payment_mode,
										'is_paid' => $request->is_paid,
									);

									$user_data = User::find($request->user_id);
									$user_data1 = array();
									$user_data1['name'] = $user_data->first_name . " " . $user_data->last_name;
									$user_data1['picture'] = $user_data->picture;
									$user_data1['phone'] = $user_data->phone;
									$user_data1['address'] = $user_data->address;
									$user_data1['bio'] = $user_data->bio;
									$user_data1['latitude'] = $request->latitude;
									$user_data1['longitude'] = $request->longitude;
									$user_data1['user_dist_lat'] = $request->D_latitude;
									$user_data1['user_dist_long'] = $request->D_longitude;
									$user_data1['dest_latitude'] = $request->D_latitude;
									$user_data1['dest_longitude'] = $request->D_longitude;
									$user_data1['payment_type'] = $request->payment_mode;
									$user_data1['rating'] = $user_data->rate;
									$user_data1['num_rating'] = $user_data->rate_count;
									$title = trans('providerController.trip_completed');
									
									$cards = '';
									$cardlist = Payment::where('user_id', $user_data->id)->where('is_default', 1)->first();
									if (count($cardlist) >= 1) {
										$cards = array();
										$default = $cardlist->is_default;
										if ($default == 1) {
											$cards['is_default_text'] = "default";
										} else {
											$cards['is_default_text'] = "not_default";
										}
										$cards['card_id'] = $cardlist->id;
										$cards['user_id'] = $cardlist->user_id;
										$cards['customer_id'] = $cardlist->customer_id;
										$cards['last_four'] = $cardlist->last_four;
										$cards['card_token'] = $cardlist->card_token;
										$cards['card_type'] = $cardlist->card_type;
										$cards['is_default'] = $default;
									}

									$chagre = array();
									$settings = Settings::where('key', 'default_distance_unit')->first();
									$default_distance_unit = $settings->value;
									if ($default_distance_unit == 0) {
										$default_distance_unit_set = 'kms';
									}
									elseif ($default_distance_unit == 1) {
										$default_distance_unit_set = 'miles';
									}
									$chagre['unit'] = $default_distance_unit_set;
									$requestserv = RequestServices::where('request_id', $request->id)->first();
									if ($requestserv->base_price != 0) {
										$chagre['base_price'] = currency_converted($requestserv->base_price);
										$chagre['distance_price'] = currency_converted($requestserv->distance_cost);
										$chagre['price_per_unit_time'] = currency_converted($requestserv->time_cost);
									}
									else {
										$chagre['base_price'] = currency_converted($provider_type->base_price);	
										$chagre['distance_price'] = currency_converted($provider_type->price_per_unit_distance);
										$chagre['price_per_unit_time'] = currency_converted($provider_type->price_per_unit_time);
									}
									$chagre['total'] = currency_converted($request->total);
									$chagre['is_paid'] = $request->is_paid;
									/* $var = Keywords::where('id', 4)->first(); */
									$title = trans('providerController.your3') . "" . Config::get('app.generic_keywords.Trip') . "" . trans('providerController.is_completed');
 
									$message = $response_array;

									send_notifications($request->user_id, 'user', $title, $message);

									// Send SMS 
									$user = User::find($request->user_id);
									$settings = Settings::where('key', 'sms_when_provider_completes_job')->first();
									$pattern = $settings->value;
									$pattern = str_replace('%user%', $user->first_name . " " . $user->last_name, $pattern);
									$pattern = str_replace('%driver%', $provider->first_name . " " . $provider->last_name, $pattern);
									$pattern = str_replace('%driver_mobile%', $provider->phone, $pattern);
									$pattern = str_replace('%amount%', $request->total, $pattern);
									sms_notification($request->user_id, 'user', $pattern);
									$id = $request->id;
	
									$email_data = array();

									$email_data['name'] = $user->first_name;
									$email_data['emailType'] = 'user';
									$email_data['base_price'] = $bill['base_price'];
									$email_data['distance'] = $bill['distance'];
									$email_data['time'] = $bill['time'];
									$email_data['unit'] = $bill['unit'];
									$email_data['total'] = $bill['total'];
									$email_data['payment_mode'] = $bill['payment_mode'];
									
									// $email_data['actual_total'] = currency_converted($actual_total);
									// $email_data['is_paid'] = $request->is_paid;
									// $email_data['promo_discount'] = currency_converted($promo_total);

									$request_services = RequestServices::where('request_id', $request->id)->first();

									$locations = RequestLocation::where('request_id', $request->id)
											->orderBy('id')
											->get();
									$count = round(count($locations) / 50);
									$start = RequestLocation::where('request_id', $request->id)
											->orderBy('id')
											->first();
									$end = RequestLocation::where('request_id', $request->id)
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
									$start_address = trans('providerController.address_not_found');
									if (isset($start_location['results'][0]['formatted_address'])) {
										$start_address = $start_location['results'][0]['formatted_address'];
									}
									$end_location = json_decode(file_get_contents("https://maps.googleapis.com/maps/api/geocode/json?latlng=$end->latitude,$end->longitude"), TRUE);
									$end_address = trans('providerController.address_not_found');
									if (isset($end_location['results'][0]['formatted_address'])) {
										$end_address = $end_location['results'][0]['formatted_address'];
									}

									$email_data['start_location'] = $start_location;
									$email_data['end_location'] = $end_location;

									$provider = Provider::find($request->confirmed_provider);
									$provider_review = ProviderReview::where('request_id', $id)->first();
									if ($provider_review) {
										$rating = round($provider_review->rating);
									} else {
										$rating = 0;
									}

									$email_data['map'] = $map;
									$settings = Settings::where('key', 'admin_email_address')->first();
									$admin_email = $settings->value;
									$requestserv = RequestServices::where('request_id', $request->id)->orderBy('id', 'DESC')->first();
									$get_type_name = ProviderType::where('id', $requestserv->type)->first();
									$detail = array(
										'admin_eamil' => $admin_email,
										'request' => $request,
										'start_address' => $start_address,
										'end_address' => $end_address,
										'start' => $start,
										'end' => $end,
										'map_url' => $map,
										'provider' => $provider,
										'rating' => $rating,
										'base_price' => $requestserv->base_price,
										'price_per_time' => $provider_type->price_per_unit_time,
										'price_per_dist' => $provider_type->price_per_unit_distance,
										'ref_bonus' => $request->ledger_payment,
										'promo_bonus' => '',
										'dist_cost' => $requestserv->distance_cost,
										'time_cost' => $requestserv->time_cost,
										'type_name' => ucwords($get_type_name->name)
									);


									$subject = trans('providerController.invoice_generated');
									email_notification($request->user_id, 'user', $detail, $subject, 'invoice');

									$subject = trans('providerController.request_completed');
									email_notification(1, 'admin', $detail, $subject, 'invoice');

									//send email to provider
									$subject = trans('providerController.invoice_generated');
									email_notification($request->confirmed_provider, 'provider', $detail, $subject, 'invoice');

									if ($request->is_paid == 1) {
										// send email
										$settings = Settings::where('key', 'admin_email_address')->first();
										$admin_email = $settings->value;
										$pattern = array('admin_eamil' => $admin_email, 'name' => trans('providerController.Administrator'), 'amount' => $request->total, 'req_id' => $request_id, 'web_url' => web_url());
										$subject = trans('providerController.payment_done_with') . "" . $request_id . '';
										email_notification(1, 'admin', $pattern, $subject, 'payment_charged', null);
									}

									
									$settings = Settings::where('key', 'default_distance_unit')->first();
									$default_distance_unit = $settings->value;
									if ($default_distance_unit == 0) {
										$default_distance_unit_set = 'kms';
									} elseif ($default_distance_unit == 1) {
										$default_distance_unit_set = 'miles';
									}
									$distance = DB::table('request_location')->where('request_id', $request_id)->max('distance');

									$end_time = DB::table('request_location')
											->where('request_id', $request_id)
											->max('created_at');
									$request_data_1 = array('request_id' => $request_id,
										'status' => $request->status,
										'confirmed_provider' => $request->confirmed_provider,
										'is_provider_started' => $request->is_provider_started,
										'is_provider_arrived' => $request->is_provider_arrived,
										'is_started' => $request->is_started,
										'is_request_started' => $request->is_started,
										'is_completed' => $request->is_completed,
										'is_user_rated' => $request->is_user_rated,
										'is_cancelled' => $request->is_cancelled,
										'is_provider_rated' => $request->is_provider_rated,
										'dest_latitude' => $request->D_latitude,
										'dest_longitude' => $request->D_longitude,
										'accepted_time' => $request->request_start_time,
										'payment_type' => $request->payment_mode,
										'distance' => (string) convert($distance, $default_distance_unit),
										'unit' => $default_distance_unit_set,
										'end_time' => $end_time,
										'user' => $user_data1,
										'bill' => $bill,
										'card_details' => $cards,
										'charge_details' => $chagre,
										'payment_option' => $request->is_paid);
									$response_array = array(
										'success' => true,
										'total' => currency_converted($request->total),
										'error' => $payment_type,
										/* 'currency' => $currency_selected->keyword, */
										'currency' => Config::get('app.generic_keywords.Currency'),
										'is_paid' => $request->is_paid,
										'request_id' => $request_id,
										'status' => $request->status,
										'confirmed_provider' => $request->confirmed_provider,
										'is_provider_started' => $request->is_provider_started,
										'is_provider_arrived' => $request->is_provider_arrived,
										'is_request_started' => $request->is_started,
										'is_completed' => $request->is_completed,
										'is_provider_rated' => $request->is_provider_rated,
										'provider' => $provider_data,
										'payment_mode' => $request->payment_mode,
										'bill' => $bill,
										'user' => $user_data1,
										'payment_option' => $request->is_paid,
										'request' => $request_data_1,
									);
									$response_code = 200;
								} else {
									$response_array = array('success' => false, 'error' => trans('providerController.service_not_started'), 'error_code' => 413);
									$response_code = 200;
								}
							} else {
								$response_array = array('success' => false, 'error' => trans('providerController.service_id_not_match') . "" . Config::get('app.generic_keywords.Provider') . "" . trans('providerController.id'), 'error_code' => 407);
								$response_code = 200;
							}
						} else {
							$response_array = array('success' => false, 'error' => trans('providerController.service_id_not_found'), 'error_code' => 408);
							$response_code = 200;
						}
					} else {
						$response_array = array('success' => false, 'error' => trans('providerController.token_expired'), 'error_code' => 405);
						$response_code = 200;
					}
				} else {
					if ($is_admin) {
						$response_array = array('success' => false, 'error' => trans('providerController.id_of') . "" . Config::get('app.generic_keywords.Provider') . "" . trans('providerController.not_found'), 'error_code' => 410);
					} else {
						$response_array = array('success' => false, 'error' => trans('providerController.token_not_valid'), 'error_code' => 406);
					}
					$response_code = 200;
				}
			}
		}
		$response = Response::json($response_array, $response_code);
		return $response;
	}

	public function get_provider_profile()
	{
		$token = Input::get('token');
		$provider_id = Input::get('id');

		$validator = Validator::make(
						array(
					'token' => $token,
					'provider_id' => $provider_id,
						), array(
					'token' => 'required',
					'provider_id' => 'required|integer',
						), array(
					'token' => '',
					'provider_id' => trans('customerController.unique_id_missing'),
						)
		);
		if ($validator->fails()) {
			$error_messages = $validator->messages()->all();
			$response_array = array('success' => false, 'error' => trans('customerController.invalid_input'), 'error_code' => 401, 'error_messages' => $error_messages);
			$response_code = 200;
		} else {

			if ($provider = Provider::find($provider_id))
			{
				$response_array = array(
					'success' => true,
					'id' => $provider->id,
					'email' => $provider->email,
					'first_name' => $provider->first_name,
					'last_name' =>  $provider->last_name,
					'address'   =>  $provider->address,
					'bio'       =>  $provider->bio,
					'zipcode'   =>  $provider->zipcode,
					'picture'   =>  $provider->picture,
					'phone'     =>  $provider->phone,
					'car_model' =>  $provider->car_model,
					'car_number' =>  $provider->car_number
				);

				$response_code = 200;
			}
			else
			{
				$response_array = array('success' => false, 'error' => trans('customerController.user_id_not_found'), 'error_code' => 404, 'error_messages' => $error_messages);
				$response_code = 200;
			}
		}

		$response = Response::json($response_array, $response_code);
		return $response;
	}

	//Payment before starting
	public function pre_payment() {
		if (Request::isMethod('post')) {
			$request_id = Input::get('request_id');
			$token = Input::get('token');
			$provider_id = Input::get('id');
			$time = Input::get('time');

			$validator = Validator::make(
							array(
						'request_id' => $request_id,
						'token' => $token,
						'provider_id' => $provider_id,
						'time' => $time,
							), array(
						'request_id' => 'required|integer',
						'token' => 'required',
						'provider_id' => 'required|integer',
						'time' => 'required',
							), array(
						'request_id' => trans('providerController.id_request_required'),
						'token' => '',
						'provider_id' => trans('providerController.unique_id_missing'),
						'time' => trans('providerController.time_required'),
							)
			);

			if ($validator->fails()) {
				$error_messages = $validator->messages()->all();
				$response_array = array('success' => false, 'error' => trans('providerController.invalid_input'), 'error_code' => 401, 'error_messages' => $error_messages);
				$response_code = 200;
			} else {
				$is_admin = $this->isAdmin($token);
				if ($provider_data = $this->getProviderData($provider_id, $token, $is_admin)) {
					// check for token validity
					if (is_token_active($provider_data->token_expiry) || $is_admin) {
						// Do necessary operations
						if ($request = Requests::find($request_id)) {
							if ($request->confirmed_provider == $provider_id) {
								$request_service = RequestServices::find($request_id);
								$request_typ = ProviderType::where('id', '=', $request_service->type)->first();

								if (!$provider_data->type) {

									$price_per_unit_distance = $request_typ->price_per_unit_distance;
									$price_per_unit_time = $request_typ->price_per_unit_time;
									$base_price = $request_typ->base_price;
								} else {
									$provider_type = ProviderServices::find($provider_data->type);
									$base_price = $provider_type->base_price;
									$price_per_unit_distance = $provider_type->price_per_unit_distance;
									$price_per_unit_time = $provider_type->price_per_unit_time;
								}

								$settings = Settings::where('key', 'default_charging_method_for_users')->first();
								$pricing_type = $settings->value;
								$settings = Settings::where('key', 'default_distance_unit')->first();
								$default_distance_unit = $settings->value;
								if ($pricing_type == 1) {
									$distance_cost = $price_per_unit_distance;
									$time_cost = $price_per_unit_time;

									

									$total = $base_price + $distance_cost + $time_cost;
								} else {
									$distance_cost = 0;
									$time_cost = 0;
									$total = $base_price;
								}

								//Log::info('req');
								$request_service = RequestServices::find($request_id);
								$request_service->base_price = $base_price;
								$request_service->distance_cost = $distance_cost;
								$request_service->time_cost = $time_cost;

								// $request_service->distance_cost = 12;
								// $request_service->time_cost = 12;

								$request_service->total = $total;
								$request_service->save();
								$request->distance = $distance_cost;

								$request->time = $time_cost;
								$request->total = $total;

								//Log::info('in ');
								// charge client
								$ledger = Ledger::where('user_id', $request->user_id)->first();

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

								//Log::info('out');
								if ($total == 0) {
									$request->is_paid = 1;
								} else {

									$payment_data = Payment::where('user_id', $request->user_id)->where('is_default', 1)->first();
									if (!$payment_data)
										$payment_data = Payment::where('user_id', $request->user_id)->first();

									if ($payment_data) {
										$customer_id = $payment_data->customer_id;
										try {
											if (Config::get('app.default_payment') == 'stripe') {
												Stripe::setApiKey(Config::get('app.stripe_secret_key'));

												try {
													Stripe_Charge::create(array(
														"amount" => floor($total) * 100,
														"currency" => "usd",
														"customer" => $customer_id)
													);
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

												$setting = Settings::where('key', 'paypal')->first();
												$settng1 = Settings::where('key', 'service_fee')->first();
												if ($setting->value == 2 && $provider_data->merchant_id != NULL) {
													// dd($amount$request->transfer_amount);
													$transfer = Stripe_Transfer::create(array(
																"amount" => ($total - $settng1->value) * 100, // amount in cents
																"currency" => "usd",
																"recipient" => $provider_data->merchant_id)
													);
												}
											} else {
												$amount = $total;
												Braintree_Configuration::environment(Config::get('app.braintree_environment'));
												Braintree_Configuration::merchantId(Config::get('app.braintree_merchant_id'));
												Braintree_Configuration::publicKey(Config::get('app.braintree_public_key'));
												Braintree_Configuration::privateKey(Config::get('app.braintree_private_key'));
												$card_id = $payment_data->card_token;
												$setting = Settings::where('key', 'paypal')->first();
												$settng1 = Settings::where('key', 'service_fee')->first();
												if ($setting->value == 2 && $provider_data->merchant_id != NULL) {
													// escrow
													$result = Braintree_Transaction::sale(array(
																'amount' => $amount,
																'paymentMethodToken' => $card_id
													));
												} else {
													$result = Braintree_Transaction::sale(array(
																'amount' => $amount,
																'paymentMethodToken' => $card_id
													));
												}
												//Log::info('result = ' . print_r($result, true));
												if ($result->success) {
													$request->is_paid = 1;
												} else {
													$request->is_paid = 0;
												}
											}
										} catch (Exception $e) {
											$response_array = array('success' => false, 'error' => $e, 'error_code' => 405);
											$response_code = 200;
											$response = Response::json($response_array, $response_code);
											return $response;
										}
									}
								}

								$request->card_payment = $total;
								$request->ledger_payment = $request->total - $total;

								$request->save();
								//Log::info('Request = ' . print_r($request, true));

								if ($request->is_paid == 1) {
									$user = User::find($request->user_id);
									$settings = Settings::where('key', 'sms_payment_generated')->first();
									$pattern = $settings->value;
									$pattern = str_replace('%user%', $user->first_name . " " . $user->last_name, $pattern);
									$pattern = str_replace('%id%', $request->id, $pattern);
									$pattern = str_replace('%user_mobile%', $user->phone, $pattern);
									sms_notification(1, 'admin', $pattern);
								}

								$provider = Provider::find($provider_id);
								$provider->is_available = 1;
								$provider->save();

								// Send Notification
								$provider = Provider::find($request->confirmed_provider);
								$provider_services  = ProviderServices::where('provider_id', $provider->id)->first();

								$provider_data = array();
								$provider_data['first_name'] = $provider->first_name;
								$provider_data['last_name'] = $provider->last_name;
								$provider_data['phone'] = $provider->phone;
								$provider_data['bio'] = $provider->bio;
								$provider_data['picture'] = $provider->picture;
								$provider_data['type'] = ($provider_services ? $provider_services->type : null);
								$provider_data['rating'] = $provider->rate;
								$provider_data['num_rating'] = $provider->rate_count;
								$provider_data['car_model'] = $provider->car_model;
								$provider_data['car_number'] = $provider->car_number;
								/* $provider_data['rating'] = DB::table('review_provider')->where('provider_id', '=', $provider->id)->avg('rating') ? : 0;
								  $provider_data['num_rating'] = DB::table('review_provider')->where('provider_id', '=', $provider->id)->count(); */

								$settings = Settings::where('key', 'default_distance_unit')->first();
								$default_distance_unit = $settings->value;
								if ($default_distance_unit == 0) {
									$default_distance_unit_set = 'kms';
								} elseif ($default_distance_unit == 1) {
									$default_distance_unit_set = 'miles';
								}
								$bill = array();
								if ($request->is_paid == 1) {
									$bill['distance'] = (string) convert($request->distance, $default_distance_unit);
									$bill['unit'] = $default_distance_unit_set;
									$bill['time'] = $request->time;
									$bill['base_price'] = currency_converted($base_price);
									$bill['distance_cost'] = currency_converted($distance_cost);
									$bill['time_cost'] = currency_converted($time_cost);
									$bill['total'] = currency_converted($request->total);
									$bill['is_paid'] = $request->is_paid;
								}

								$response_array = array(
									'success' => true,
									'request_id' => $request_id,
									'status' => $request->status,
									'confirmed_provider' => $request->confirmed_provider,
									'provider' => $provider_data,
									'bill' => $bill,
								);
								$title = trans('adminController.pay_has_made');

								$message = $response_array;

								send_notifications($provider->id, "provider", $title, $message);


								$settings = Settings::where('key', 'email_notification')->first();
								$condition = $settings->value;
								if ($condition == 1) {
									/* $settings = Settings::where('key', 'payment_made_client')->first();
									  $pattern = $settings->value;

									  $pattern = str_replace('%id%', $request->id, $pattern);
									  $pattern = str_replace('%amount%', $request->total, $pattern);

									  $subject = "Payment Charged";
									  email_notification($provider->id, 'provider', $pattern, $subject); */
									$settings = Settings::where('key', 'admin_email_address')->first();
									$admin_email = $settings->value;
									$pattern = array('admin_eamil' => $admin_email, 'name' => ucwords($provider->first_name . " " . $provider->last_name), 'amount' => $total, 'req_id' => $request_id, 'web_url' => web_url());
									$subject = trans('providerController.payment_done_with') . "" . $request_id . '';
									email_notification($provider->id, 'provider', $pattern, $subject, 'payment_made_client', null);
								}

								// Send SMS
								$user = User::find($request->user_id);
								$settings = Settings::where('key', 'sms_when_provider_completes_job')->first();
								$pattern = $settings->value;
								$pattern = str_replace('%user%', $user->first_name . " " . $user->last_name, $pattern);
								$pattern = str_replace('%driver%', $provider->first_name . " " . $provider->last_name, $pattern);
								$pattern = str_replace('%driver_mobile%', $provider->phone, $pattern);
								$pattern = str_replace('%amount%', $request->total, $pattern);
								sms_notification($request->user_id, 'user', $pattern);

								$email_data = array();

								$email_data['name'] = $user->first_name;
								$email_data['emailType'] = 'user';
								$email_data['base_price'] = $bill['base_price'];
								$email_data['distance'] = $bill['distance'];
								$email_data['time'] = $bill['time'];
								$email_data['unit'] = $bill['unit'];
								$email_data['total'] = $bill['total'];

								if ($bill['payment_mode']) {
									$email_data['payment_mode'] = $bill['payment_mode'];
								} else {
									$email_data['payment_mode'] = '---';
								}

								if ($request->is_paid == 1) {
									
									$settings = Settings::where('key', 'admin_email_address')->first();
									$admin_email = $settings->value;
									$pattern = array('admin_eamil' => $admin_email, 'name' => trans('providerController.Administrator'), 'amount' => $total, 'req_id' => $request_id, 'web_url' => web_url());
									$subject = trans('providerController.payment_done_with') . "" . $request_id . '';
									email_notification(1, 'admin', $pattern, $subject, 'payment_charged', null);
								}

								$response_array = array(
									'success' => true,
									'base_fare' => currency_converted($base_price),
									'distance_cost' => currency_converted($distance_cost),
									'time_cost' => currency_converted($time_cost),
									'total' => currency_converted($total),
									'is_paid' => $request->is_paid,
								);
								$response_code = 200;
							} else {
								/* $var = Keywords::where('id', 1)->first();
								  $response_array = array('success' => false, 'error' => trans('providerController.service_id_not_match') . "" . $var->keyword . "" . trans('providerController.id'), 'error_code' => 407); */
								$response_array = array('success' => false, 'error' => trans('providerController.service_id_not_match') . "" . Config::get('app.generic_keywords.Provider') . "" . trans('providerController.id'), 'error_code' => 407);
								$response_code = 200;
							}
						} else {
							$response_array = array('success' => false, 'error' => trans('providerController.service_id_not_found'), 'error_code' => 408);
							$response_code = 200;
						}
					} else {
						$response_array = array('success' => false, 'error' => trans('providerController.token_expired'), 'error_code' => 405);
						$response_code = 200;
					}
				} else {
					if ($is_admin) {
						/* $var = Keywords::where('id', 1)->first();
						  $response_array = array('success' => false, 'error' => "" . $var->keyword . "" . trans('providerController.id_not_found'), 'error_code' => 410); */
						$response_array = array('success' => false, 'error' => trans('providerController.id_of') . "" . Config::get('app.generic_keywords.Provider') . "" . trans('providerController.not_found'), 'error_code' => 410);
					} else {
						$response_array = array('success' => false, 'error' => trans('providerController.token_not_valid'), 'error_code' => 406);
					}
					$response_code = 200;
				}
			}
		}
		$response = Response::json($response_array, $response_code);
		return $response;
	}

// Add Location Data
	public function request_location() {
		if (Request::isMethod('post')) {
			$request_id = Input::get('request_id');
			$token = Input::get('token');
			$provider_id = Input::get('id');
			$latitude = Input::get('latitude');
			$longitude = Input::get('longitude');
			if (Input::has('bearing')) {
				$angle = Input::get('bearing');
			}

			$validator = Validator::make(
							array(
						'request_id' => $request_id,
						'token' => $token,
						'provider_id' => $provider_id,
						'latitude' => $latitude,
						'longitude' => $longitude,
							), array(
						'request_id' => 'required|integer',
						'token' => 'required',
						'provider_id' => 'required|integer',
						'latitude' => 'required',
						'longitude' => 'required',
							), array(
						'request_id' => trans('providerController.id_request_required'),
						'token' => '',
						'provider_id' => trans('providerController.unique_id_missing'),
						'latitude' => trans('providerController.location_point_missing'),
						'longitude' => trans('providerController.location_point_missing'),
							)
			);

			if ($validator->fails()) {
				$error_messages = $validator->messages()->all();
				$response_array = array('success' => false, 'error' => trans('providerController.invalid_input'), 'error_code' => 401, 'error_messages' => $error_messages);
				$response_code = 200;
			} else {
				try{
				$default_distance_unit = $default_distance_unit_set = -1;
				$settings = Settings::where('key', 'default_distance_unit')->first();
				$default_distance_unit = $settings->value;
				if ($default_distance_unit == 0) {
					$default_distance_unit_set = 'kms';
				} elseif ($default_distance_unit == 1) {
					$default_distance_unit_set = 'miles';
				}

				$is_admin = $this->isAdmin($token);
				if ($provider_data = $this->getProviderData($provider_id, $token, $is_admin)) {
					// check for token validity
					if (is_token_active($provider_data->token_expiry) || $is_admin) {
						// Do necessary operations
						$settings = Settings::where('key', 'request_time_costing_type')->first();
						$time_fare_type = $settings->value;
						if ($request = Requests::find($request_id)) {
							if ($request->confirmed_provider == $provider_id) {

								$setting_distance_count_on_provider_start = Settings::getDistanceCountOnProviderStart();

								if (($setting_distance_count_on_provider_start == 1 && $request->is_provider_started) ||
									($request->is_started == 1)) {

									$request_location_last = RequestLocation::where('request_id', $request_id)->orderBy('created_at', 'desc')->first();

									if ($request_location_last) {
										$distance_old = $request_location_last->distance;
										$distance_new = distanceGeoPoints($request_location_last->latitude, $request_location_last->longitude, $latitude, $longitude);
										$distance = $distance_old + $distance_new;
										$settings = Settings::where('key', 'default_distance_unit')->first();
										$default_distance_unit = $settings->value;
										if ($default_distance_unit == 0) {
											$default_distance_unit_set = 'kms';
										} elseif ($default_distance_unit == 1) {
											$default_distance_unit_set = 'miles';
										}
										$distancecon = convert($distance, $default_distance_unit);
									} else {
										$distance = 0;
									}

									$provider = Provider::find($provider_id);

									$location = get_location($latitude, $longitude);
									$latitude = $location['lat'];
									$longitude = $location['long'];
									if (!isset($angle)) {
										$angle = get_angle($provider->latitude, $provider->longitude, $latitude, $longitude);
									}
									$provider->old_latitude = $provider->latitude;
									$provider->old_longitude = $provider->longitude;
									$provider->latitude = $latitude;
									$provider->longitude = $longitude;
									$provider->bearing = $angle;
									$provider->save();

									/* GET SECOND LAST ENTY FOR TIME */
									if ($time_fare_type) {
										$loc1 = RequestLocation::where('request_id', $request->id)->orderBy('id', 'desc')->first();
									} else {
										$loc1 = RequestLocation::where('request_id', $request->id)->orderBy('id', 'asc')->first();
									}
									/* GET SECOND LAST ENTY FOR TIME END */
									if ($request->is_completed != 1) {
										$request_location = new RequestLocation;
										$request_location->request_id = $request_id;
										$request_location->latitude = $latitude;
										$request_location->longitude = $longitude;
										$request_location->distance = $distance;
										$request_location->bearing = $angle;
										$request_location->save();
									}
									$one_minut_old_time = date("Y-m-d H:i:s", strtotime(date("Y-m-d H:i:s")) - 60);
									$loc2 = RequestLocation::where('request_id', $request->id)->orderBy('id', 'desc')->first();
									if ($loc1) {
										$time1 = strtotime($loc2->created_at);
										$time2 = strtotime($loc1->created_at);
										/* echo $difference = intval(($time1 - $time2) / 60); */
										$difference = ($time1 - $time2) / 60;
										$loc1min = RequestLocation::where('request_id', $request->id)->where('created_at', '<=', $one_minut_old_time)->orderBy('id', 'desc')->first();
										if($loc1min){
											$distence = distanceGeoPoints($loc1min->latitude, $loc1min->longitude, $latitude, $longitude);
											if ($request->is_completed != 1) {
												if ($time_fare_type) {
													if ($distence <= 50) {
														$request->time = $request->time + $difference;
													} else {
														$request->time = $request->time;
													}
												} else {
													$request->time = $difference;
												}
											}
										}
									} else {
										$request->time = 0;
									}
									$request->save();

									$response_array = array(
										'success' => true,
										'dest_latitude' => $request->D_latitude,
										'dest_longitude' => $request->D_longitude,
										'payment_type' => $request->payment_mode,
										'is_cancelled' => $request->is_cancelled,
										'distance' => $distancecon,
										'unit' => $default_distance_unit_set,
										'time' => $difference,
									);
									$response_code = 200;
								} else {
									$provider = Provider::find($provider_id);

									$location = get_location($latitude, $longitude);
									$latitude = $location['lat'];
									$longitude = $location['long'];
									if (!isset($angle)) {
										$angle = get_angle($provider->latitude, $provider->longitude, $latitude, $longitude);
									}
									$provider->old_latitude = $provider->latitude;
									$provider->old_longitude = $provider->longitude;
									$provider->latitude = $latitude;
									$provider->longitude = $longitude;
									$provider->bearing = $angle;
									$provider->save();
									$response_array = array(
										'success' => false,
										'dest_latitude' => $request->D_latitude,
										'dest_longitude' => $request->D_longitude,
										'payment_type' => $request->payment_mode,
										'is_cancelled' => $request->is_cancelled,
										'unit' => $default_distance_unit_set,
										'error' => trans('providerController.service_not_started'),
										'error_code' => 414,
									);
									$response_code = 200;
								}
							} else {
								
								$response_array = array(
									'success' => false,
									'dest_latitude' => $request->D_latitude,
									'dest_longitude' => $request->D_longitude,
									'payment_type' => $request->payment_mode,
									'is_cancelled' => $request->is_cancelled,
									'unit' => $default_distance_unit_set,
									'error' => trans('providerController.request_id_doesnot_match') . "" . Config::get('app.generic_keywords.Provider') . "" . trans('providerController.id'),
									'error_code' => 407);
								$response_code = 200;
							}
						} else {
							$response_array = array('success' => false, 'error' => trans('providerController.service_id_not_found'), 'error_code' => 408);
							$response_code = 200;
						}
					} else {
						$response_array = array('success' => false, 'error' => trans('providerController.token_expired'), 'error_code' => 405);
						$response_code = 200;
					}
				} else {
					if ($is_admin) {
						$response_array = array('success' => false, 'error' => trans('providerController.id_of') . "" . Config::get('app.generic_keywords.Provider') . "" . trans('providerController.not_found'), 'error_code' => 410);
					} else {
						$response_array = array('success' => false, 'error' => trans('providerController.token_not_valid'), 'error_code' => 406);
					}
					$response_code = 200;
				}
			}
			catch(Exception $ex){
				return $ex->getTrace();
				
			}
			}
		}
		$response = Response::json($response_array, $response_code);
		return $response;
	}

// Add Location Data
	public function check_state() {

		$provider_id = Input::get('id');
		$token = Input::get('token');

		$validator = Validator::make(
						array(
					'provider_id' => $provider_id,
					'token' => $token,
						), array(
					'provider_id' => 'required|integer',
					'token' => 'required',
						), array(
					'provider_id' => trans('providerController.unique_id_missing'),
					'token' => '',
						)
		);

		if ($validator->fails()) {
			$error_messages = $validator->messages()->all();
			$response_array = array('success' => false, 'error' => trans('providerController.invalid_input'), 'error_code' => 401, 'error_messages' => $error_messages);
			$response_code = 200;
		} else {
			$is_admin = $this->isAdmin($token);
			if ($provider_data = $this->getProviderData($provider_id, $token, $is_admin)) {
				// check for token validity
				if (is_token_active($provider_data->token_expiry) || $is_admin) {

					$response_array = array('success' => true, 'is_active' => $provider_data->is_active);
					$response_code = 200;
				} else {
					$response_array = array('success' => false, 'error' => trans('providerController.token_expired'), 'error_code' => 405);
					$response_code = 200;
				}
			} else {
				if ($is_admin) {
					/* $var = Keywords::where('id', 1)->first();
					  $response_array = array('success' => false, 'error' => "" . $var->keyword . "" . trans('providerController.id_not_found'), 'error_code' => 410); */
					$response_array = array('success' => false, 'error' => trans('providerController.id_of') . "" . Config::get('app.generic_keywords.Provider') . "" . trans('providerController.not_found'), 'error_code' => 410);
				} else {
					$response_array = array('success' => false, 'error' => trans('providerController.token_not_valid'), 'error_code' => 406);
				}
				$response_code = 200;
			}
		}

		$response = Response::json($response_array, $response_code);
		return $response;
	}

	// Add Location Data
	public function toggle_state() {

		$provider_id = Input::get('id');
		$token = Input::get('token');

		$validator = Validator::make(
						array(
					'provider_id' => $provider_id,
					'token' => $token,
						), array(
					'provider_id' => 'required|integer',
					'token' => 'required',
						), array(
					'provider_id' => trans('providerController.unique_id_missing'),
					'token' => '',
						)
		);

		if ($validator->fails()) {
			$error_messages = $validator->messages()->all();
			$response_array = array('success' => false, 'error' => trans('providerController.invalid_input'), 'error_code' => 401, 'error_messages' => $error_messages);
			$response_code = 200;
		} else {
			$is_admin = $this->isAdmin($token);
			if ($provider_data = $this->getProviderData($provider_id, $token, $is_admin)) {
				// check for token validity
				if (is_token_active($provider_data->token_expiry) || $is_admin) {
					$provider = Provider::find($provider_id);
					$provider->is_active 	= ($provider->is_active + 1) % 2 ;
					$provider->is_available = $provider->is_active ;
					$provider->save();
					$response_array = array('success' => true, 'is_active' => $provider->is_active);
					$response_code = 200;
				} else {
					$response_array = array('success' => false, 'error' => trans('providerController.token_expired'), 'error_code' => 405);
					$response_code = 200;
				}
			} else {
				if ($is_admin) {
					/* $var = Keywords::where('id', 1)->first();
					  $response_array = array('success' => false, 'error' => "" . $var->keyword . "" . trans('providerController.id_not_found'), 'error_code' => 410); */
					$response_array = array('success' => false, 'error' => trans('providerController.id_of') . "" . Config::get('app.generic_keywords.Provider') . "" . trans('providerController.not_found'), 'error_code' => 410);
				} else {
					$response_array = array('success' => false, 'error' => trans('providerController.token_not_valid'), 'error_code' => 406);
				}
				$response_code = 200;
			}
		}

		$response = Response::json($response_array, $response_code);
		return $response;
	}

	// Update Profile

	public function update_profile() {

		$token = Input::get('token');
		$provider_id = Input::get('id');
		$first_name = Input::get('first_name');
		$last_name = Input::get('last_name');
		$phone = Input::get('phone');
		$password = Input::get('password');
		$new_password = Input::get('new_password');
		$old_password = Input::get('old_password');
		$picture = Input::file('picture');
		$bio = Input::get('bio');
		$address = Input::get('address');
		$state = Input::get('state');
		$country = Input::get('country');
		$zipcode = Input::get('zipcode');
		$car_model = $car_number = '';
		if (Input::has('car_model')) {
			$car_model = trim(Input::get('car_model'));
		}

		$car_number_validation = 0;

		if (Input::has('car_number')) {
			$car_number = trim(Input::get('car_number'));
		}

		$validator = Validator::make(
						array(
					'token' => $token,
					'provider_id' => $provider_id,
					'picture' => $picture,
						/* 'zipcode' => $zipcode */
						), array(
					'token' => 'required',
					'provider_id' => 'required|integer',
					/* 'picture' => 'mimes:jpeg,bmp,png', */
					'picture' => 'mimes:jpeg,bmp,png|min:200px',
						/* 'zipcode' => 'integer' */
						), array(
					'token' => '',
					'provider_id' => trans('providerController.unique_id_missing'),
					/* 'picture' => 'mimes:jpeg,bmp,png', */
					'picture' => 'TAMANHO ERRADO',
						/* 'zipcode' => '' */
						)
		);

		if ($validator->fails()) {
			$error_messages = $validator->messages()->all();
			$response_array = array('success' => false, 'error' => trans('providerController.invalid_input'), 'error_code' => 401, 'error_messages' => $error_messages);
			$response_code = 200;
		} else {
			$is_admin = $this->isAdmin($token);
			if ($provider_data = $this->getProviderData($provider_id, $token, $is_admin)) {
				// check for token validity
				if (is_token_active($provider_data->token_expiry) || $is_admin) {
					if ($new_password != "" || $new_password != NULL) {
						if ($old_password != "" || $old_password != NULL) {
							if (Hash::check($old_password, $provider_data->password)) {

								$provider = Provider::find($provider_id);
								if ($first_name) {
									$provider->first_name = $first_name;
								}
								if ($last_name) {
									$provider->last_name = $last_name;
								}
								if ($phone) {
									$provider->phone = $phone;
								}
								if ($bio) {
									$provider->bio = $bio;
								}
								if ($address) {
									$provider->address = $address;
								}
								if ($state) {
									$provider->state = $state;
								}
								if ($country) {
									$provider->country = $country;
								}
								if ($zipcode) {
									$provider->zipcode = $zipcode;
								}
								if ($new_password) {
									$provider->password = Hash::make($new_password);
								}
								if ($car_model != '') {
									$provider->car_model = $car_model;
								}
								if ($car_number != '') {
									$provider->car_number = $car_number;
								}

								if (Input::hasFile('picture')) {
									if ($provider->picture != '') {
										$path = $provider->picture;
										//Log::info($path);
										$filename = basename($path);
										//Log::info($filename);
										if (file_exists($path)) {
											unlink(public_path() . "/uploads/" . $filename);
										}
									}
									// upload image
									$file_name = time();
									$file_name .= rand();
									$file_name = sha1($file_name);

									$ext = Input::file('picture')->getClientOriginalExtension();
									//Log::info('ext = ' . print_r($ext, true));
									Input::file('picture')->move(public_path() . "/uploads", $file_name . "." . $ext);
									$local_url = $file_name . "." . $ext;

									// Upload to S3

									if (Config::get('app.s3_bucket') != '') {
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
										if ($provider->picture != '') {
											$icon = $provider->picture;
											unlink_image($icon);
										}
									}

									$provider->picture = $s3_url;
								}
								If (Input::has('timezone')) {
									$provider->timezone = Input::get('timezone');
								}

								$provider->save();

								$provider_services  = ProviderServices::where('provider_id', $provider->id)->first();

								$response_array = array(
									'success' => true,
									'id' => $provider->id,
									'first_name' => $provider->first_name,
									'last_name' => $provider->last_name,
									'phone' => $provider->phone,
									'email' => $provider->email,
									'picture' => $provider->picture,
									'bio' => $provider->bio,
									'address' => $provider->address,
									'state' => $provider->state,
									'country' => $provider->country,
									'zipcode' => $provider->zipcode,
									'login_by' => $provider->login_by,
									'social_unique_id' => $provider->social_unique_id,
									'device_token' => $provider->device_token,
									'device_type' => $provider->device_type,
									'token' => $provider->token,
									'timezone' => $provider->timezone,
									'type' => ($provider_services ? $provider_services->type : null),
									'car_model' => $provider->car_model,
									'car_number' => $provider->car_number,
								);
								$response_code = 200;
							} else {
								$response_array = array('success' => false, 'error' => trans('providerController.old_password_invalid'), 'error_code' => 501);
								$response_code = 200;
							}
						} else {
							$response_array = array('success' => false, 'error' => trans('providerController.old_password_blank'), 'error_code' => 502);
							$response_code = 200;
						}
					} else {

						$provider = Provider::find($provider_id);
						if ($first_name) {
							$provider->first_name = $first_name;
						}
						if ($last_name) {
							$provider->last_name = $last_name;
						}
						if ($phone) {
							$provider->phone = $phone;
						}
						if ($bio) {
							$provider->bio = $bio;
						}
						if ($address) {
							$provider->address = $address;
						}
						if ($state) {
							$provider->state = $state;
						}
						if ($country) {
							$provider->country = $country;
						}
						if ($zipcode) {
							$provider->zipcode = $zipcode;
						}
						if ($car_model != '') {
							$provider->car_model = $car_model;
						}
						if ($car_number != '') {
							$provider->car_number = $car_number;
						}

						if (Input::hasFile('picture')) {
							if ($provider->picture != '') {
								$path = $provider->picture;
								//Log::info($path);
								$filename = basename($path);
								//Log::info($filename);
								if (file_exists($path)) {
									unlink(public_path() . "/uploads/" . $filename);
								}
							}
							// upload image
							$file_name = time();
							$file_name .= rand();
							$file_name = sha1($file_name);

							$ext = Input::file('picture')->getClientOriginalExtension();
							//Log::info('ext = ' . print_r($ext, true));
							Input::file('picture')->move(public_path() . "/uploads", $file_name . "." . $ext);
							$local_url = $file_name . "." . $ext;

							// Upload to S3

							if (Config::get('app.s3_bucket') != '') {
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
								if ($provider->picture != '') {
									$icon = $provider->picture;
									unlink_image($icon);
								}
							}

							$provider->picture = $s3_url;
						}
						If (Input::has('timezone')) {
							$provider->timezone = Input::get('timezone');
						}

						$provider->save();

						$provider_services  = ProviderServices::where('provider_id', $provider->id)->first();

						$response_array = array(
							'success' => true,
							'id' => $provider->id,
							'first_name' => $provider->first_name,
							'last_name' => $provider->last_name,
							'phone' => $provider->phone,
							'email' => $provider->email,
							'picture' => $provider->picture,
							'bio' => $provider->bio,
							'address' => $provider->address,
							'state' => $provider->state,
							'country' => $provider->country,
							'zipcode' => $provider->zipcode,
							'login_by' => $provider->login_by,
							'social_unique_id' => $provider->social_unique_id,
							'device_token' => $provider->device_token,
							'device_type' => $provider->device_type,
							'token' => $provider->token,
							'timezone' => $provider->timezone,
							'type' => ($provider_services ? $provider_services->type : null),
							'car_model' => $provider->car_model,
							'car_number' => $provider->car_number,
						);
						$response_code = 200;
					}
				} else {
					$response_array = array('success' => false, 'error' => trans('providerController.token_expired'), 'error_code' => 405);
					$response_code = 200;
				}
			} else {
				if ($is_admin) {
					/* $var = Keywords::where('id', 1)->first();
					  $response_array = array('success' => false, 'error' => "" . $var->keyword . "" . trans('providerController.id_not_found'), 'error_code' => 410); */
					$response_array = array('success' => false, 'error' => trans('providerController.id_of') . "" . Config::get('app.generic_keywords.Provider') . "" . trans('providerController.not_found'), 'error_code' => 410);
				} else {
					$response_array = array('success' => false, 'error' => trans('providerController.token_not_valid'), 'error_code' => 406);
				}
				$response_code = 200;
			}
		}

		$response = Response::json($response_array, $response_code);
		return $response;
	}

	public function get_completed_requests() {
		$provider_id = Input::get('id');
		$token = Input::get('token');

		$to_date = Input::get('to_date');
		if($to_date==""){
			$to_date = date('Y/m/d H:i:s');
		}

		$from = Input::get('from_date');
		if($from==""){
			$from = date('Y-m-d', strtotime( $to_date. "-7 days"));
		}


		$validator = Validator::make(
						array(
					'provider_id' => $provider_id,
					'token' => $token,
						), array(
					'provider_id' => 'required|integer',
					'token' => 'required',
						), array(
					'provider_id' => trans('providerController.unique_id_missing'),
					'token' => '',
						)
		);

		if ($validator->fails()) {
			$error_messages = $validator->messages()->all();
			$response_array = array('success' => false, 'error' => trans('providerController.invalid_input'), 'error_code' => 401, 'error_messages' => $error_messages);
			$response_code = 200;
		} else {
			$is_admin = $this->isAdmin($token);
			if ($provider_data = $this->getProviderData($provider_id, $token, $is_admin)) {
				// check for token validity
				if (is_token_active($provider_data->token_expiry) || $is_admin) {
					if ($from != "" && $to_date != '') {
						$request_data = DB::table('request')
								->where('request.confirmed_provider', $provider_id)
								->where('request.is_completed', 1)
								->where('request_start_time', '>=', $from)
								->where('request_start_time', '<=', $to_date)
								->leftJoin('user', 'request.user_id', '=', 'user.id')
								->leftJoin('request_services', 'request_services.request_id', '=', 'request.id')
								->select('request.*', 'request.request_start_time', 'request.transfer_amount', 'user.first_name', 'user.last_name', 'user.phone', 'user.email', 'user.picture', 'user.bio', 'request.distance', 'request.time', 'request.promo_code', 'request_services.base_price', 'request_services.distance_cost', 'request_services.time_cost', 'request.total')
								->groupBy('request.id')
								->get();
						$sum = Requests::where('request.confirmed_provider', $provider_id)
									->where('request_start_time', '>=', $from)
									->where('request_start_time', '<=', $to_date)
									->sum('total');
					} else {
						$request_data = DB::table('request')
								->where('request.confirmed_provider', $provider_id)
								->where('request.is_completed', 1)
								->leftJoin('user', 'request.user_id', '=', 'user.id')
								->leftJoin('request_services', 'request_services.request_id', '=', 'request.id')
								->select('request.*', 'request.request_start_time', 'request.transfer_amount', 'user.first_name', 'user.last_name', 'user.phone', 'user.email', 'user.picture', 'user.bio', 'request.distance', 'request.time', 'request.promo_code', 'request_services.base_price', 'request_services.distance_cost', 'request_services.time_cost', 'request.total')
								->groupBy('request.id')
								->get();
						$sum = Requests::where('request.confirmed_provider', $provider_id)
									->where('request_start_time', '>=', $from)
									->where('request_start_time', '<=', $to_date)
									->sum('total');

					}
					$requests = array();
					$settings = Settings::where('key', 'default_distance_unit')->first();

					$default_distance_unit = $settings->value;
					if ($default_distance_unit == 0) {
						$default_distance_unit_set = 'kms';
					} elseif ($default_distance_unit == 1) {
						$default_distance_unit_set = 'miles';
					}
					$provider = Provider::where('id', $provider_id)->first();
					foreach ($request_data as $data) {
						$discount = 0;
						if ($data->promo_id != '') {
							$promo_code = PromoCodes::where('id', $data->promo_id)->first();
							if (isset($promo_code->id)) {
								$promo_value = $promo_code->value;
								$promo_type = $promo_code->type;
								if ($promo_type == 1) {
									// Percent Discount
									$discount = $data->total * $promo_value / 100;
								} elseif ($promo_type == 2) {
									// Absolute Discount
									$discount = $promo_value;
								}
							}
						}
						$is_multiple_service = Settings::where('key', 'allow_multiple_service')->first();

						if ($is_multiple_service->value == 0) {

							$requestserv = RequestServices::where('request_id', $data->id)->first();

							$request_typ = ProviderType::where('id', '=', $requestserv->type)->first();

							$request['id'] = $data->id;
							$request['date'] = $data->request_start_time;
							$request['distance'] = (string) $data->distance;
							$request['unit'] = $default_distance_unit_set;
							$request['time'] = $data->time;
							$request['base_distance'] = $request_typ->base_distance;
							/* $currency = Keywords::where('alias', 'Currency')->first();
							  $request['currency'] = $currency->keyword; */
							$request['currency'] = Config::get('app.generic_keywords.Currency');
							if ($requestserv->base_price != 0) {
								$request['base_price'] = currency_converted($data->base_price);
								$request['distance_cost'] = currency_converted($data->distance_cost);
								$request['time_cost'] = currency_converted($data->time_cost);
							} else {
								/* $setbase_price = Settings::where('key', 'base_price')->first();
								  $request['base_price'] = currency_converted($setbase_price->value);
								  $setdistance_price = Settings::where('key', 'price_per_unit_distance')->first();
								  $request['distance_cost'] = currency_converted($setdistance_price->value);
								  $settime_price = Settings::where('key', 'price_per_unit_time')->first();
								  $request['time_cost'] = currency_converted($settime_price->value); */
								$request['base_price'] = currency_converted($data->base_price);
								$request['distance_cost'] = currency_converted($data->distance_cost);
								$request['time_cost'] = currency_converted($data->time_cost);
							}

							$admins = Admin::first();
							$request['provider']['email'] = $provider->email;
							$request['admin']['email'] = $admins->username;
							if ($data->transfer_amount != 0) {
								$request['provider']['amount'] = currency_converted($data->total - $data->transfer_amount);
								$request['admin']['amount'] = currency_converted($data->transfer_amount);
							} else {
								$request['provider']['amount'] = currency_converted($data->transfer_amount);
								$request['admin']['amount'] = currency_converted($data->total - $data->transfer_amount);
							}

							$request['total'] = currency_converted($data->total + $data->ledger_payment + $discount);
						} else {

							$request['id'] = $data->id;
							$request['date'] = $data->request_start_time;
							$request['distance'] = (string) $data->distance;
							$request['unit'] = $default_distance_unit_set;
							$request['time'] = $data->time;
							/* $currency = Keywords::where('alias', 'Currency')->first();
							  $request['currency'] = $currency->keyword; */
							$request['currency'] = Config::get('app.generic_keywords.Currency');

							$rserv = RequestServices::where('request_id', $data->id)->get();
							$typs = array();
							$typi = array();
							$typp = array();
							$total_price = 0;
							foreach ($rserv as $typ) {
								$typ1 = ProviderType::where('id', $typ->type)->first();
								$typ_price = ProviderServices::where('provider_id', $data->confirmed_provider)->where('type', $typ->type)->first();

								if ($typ_price->base_price > 0) {
									$typp1 = 0.00;
									$typp1 = $typ_price->base_price;
								} elseif ($typ_price->price_per_unit_distance > 0) {
									$typp1 = 0.00;
									foreach ($rserv as $key) {
										$typp1 = $typp1 + $key->distance_cost;
									}
								} else {
									$typp1 = 0.00;
								}
								$typs['name'] = $typ1->name;
								$typs['price'] = currency_converted($typp1);
								$typs['type_icon'] = $typ1->icon;
								$total_price = $total_price + $typp1;
								array_push($typi, $typs);
							}
							$request['type'] = $typi;

							$base_price = 0;
							$distance_cost = 0;
							$time_cost = 0;
							foreach ($rserv as $key) {
								$base_price = $base_price + $key->base_price;
								$distance_cost = $distance_cost + $key->distance_cost;
								$time_cost = $time_cost + $key->time_cost;
							}
							$request['base_price'] = currency_converted($base_price);
							$request['distance_cost'] = currency_converted($distance_cost);
							$request['time_cost'] = currency_converted($time_cost);
							$request['total'] = currency_converted($total_price);
						}
						/* path */
						$id = $data->id;
						$locations = RequestLocation::where('request_id', $data->id)->orderBy('id')->get();
						$count = round(count($locations) / 50);
						$start = $end = $map = '';
						if (count($locations) >= 1) {
							$start = RequestLocation::where('request_id', $id)
									->orderBy('id')
									->first();
							$end = RequestLocation::where('request_id', $id)
									->orderBy('id', 'desc')
									->first();
							$map = "https://maps-api-ssl.google.com/maps/api/staticmap?size=249x249&scale=2&markers=shadow:true|scale:2|icon:http://d1a3f4spazzrp4.cloudfront.net/receipt-new/marker-start@2x.png|$start->latitude,$start->longitude&markers=shadow:false|scale:2|icon:http://d1a3f4spazzrp4.cloudfront.net/receipt-new/marker-finish@2x.png|$end->latitude,$end->longitude&path=color:0x2dbae4ff|weight:4";
							$skip = 0;
							foreach ($locations as $location) {
								if ($skip == $count) {
									$map .= "|$location->latitude,$location->longitude";
									$skip = 0;
								}
								$skip ++;
							}
							/* $map.="&key=" . Config::get('app.gcm_browser_key'); */
						}
						$request['start_lat'] = '';
						if (isset($start->latitude)) {
							$request['start_lat'] = $start->latitude;
						}
						$request['start_long'] = '';
						if (isset($start->longitude)) {
							$request['start_long'] = $start->longitude;
						}
						$request['end_lat'] = '';
						if (isset($end->latitude)) {
							$request['end_lat'] = $end->latitude;
						}
						$request['end_long'] = '';
						if (isset($end->longitude)) {
							$request['end_long'] = $end->longitude;
						}
						$request['map_url'] = $map;

						$request['src_address'] = $data->src_address;
						$request['dest_address'] = $data->dest_address;
						$request['base_price'] = currency_converted($data->base_price);
						$request['distance_cost'] = currency_converted($data->distance_cost);
						$request['time_cost'] = currency_converted($data->time_cost);
						$request['total'] = currency_converted($data->total - $data->ledger_payment - $data->promo_payment);
						$request['main_total'] = currency_converted($data->total);
						$request['referral_bonus'] = currency_converted($data->ledger_payment);
						$request['promo_bonus'] = currency_converted($data->promo_payment);
						$request['payment_type'] = $data->payment_mode;
						$request['is_paid'] = $data->is_paid;
						$request['promo_id'] = $data->promo_id;
						$request['promo_code'] = $data->promo_code;
						$request['user']['first_name'] = $data->first_name;
						$request['user']['last_name'] = $data->last_name;
						$request['user']['phone'] = $data->phone;
						$request['user']['email'] = $data->email;
						$request['user']['picture'] = $data->picture;
						$request['user']['bio'] = $data->bio;
						$request['user']['payment_opt'] = $data->payment_mode;

						array_push($requests, $request);
					}
					// $sum_total = currency_converted($sum);

					$response_array = array(
						'success' => true,
						'requests' => $requests,
						'sum_total' => $sum
					);
					$response_code = 200;
				} else {
					$response_array = array('success' => false, 'error' => trans('providerController.token_expired'), 'error_code' => 405);
					$response_code = 200;
				}
			} else {
				if ($is_admin) {
					/* $var = Keywords::where('id', 1)->first();
					  $response_array = array('success' => false, 'error' => "" . $var->keyword . ' ID not Found', 'error_code' => 410); */
					$response_array = array('success' => false, 'error' => trans('providerController.id_of') . "" . Config::get('app.generic_keywords.Provider') . "" . trans('providerController.not_found'), 'error_code' => 410);
				} else {
					$response_array = array('success' => false, 'error' => trans('providerController.token_not_valid'), 'error_code' => 406);
				}
				$response_code = 200;
			}
		}

		$response = Response::json($response_array, $response_code);
		return $response;
	}

	public function provider_services_update() {
		$token = Input::get('token');
		$provider_id = Input::get('id');

		$validator = Validator::make(
						array(
					'token' => $token,
					'provider_id' => $provider_id,
						), array(
					'token' => 'required',
					'provider_id' => 'required|integer',
						), array(
					'token' => '',
					'provider_id' => trans('providerController.unique_id_missing'),
						)
		);

		if ($validator->fails()) {
			$error_messages = $validator->messages()->all();
			$response_array = array('success' => false, 'error' => trans('providerController.invalid_input'), 'error_code' => 401, 'error_messages' => $error_messages);
			$response_code = 200;
			//Log::info('validation error =' . print_r($response_array, true));
		} else {
			$is_admin = $this->isAdmin($token);
			if ($provider_data = $this->getProviderData($provider_id, $token, $is_admin)) {
				// check for token validity
				if (is_token_active($provider_data->token_expiry) || $is_admin) {
					foreach (Input::get('service') as $key) {
						$serv = ProviderType::where('id', $key)->first();
						$pserv[] = $serv->name;
					}
					foreach (Input::get('service') as $ke) {
						$proviserv = ProviderServices::where('provider_id', $provider_id)->first();
						if ($proviserv != NULL) {
							DB::delete("delete from provider_services where provider_id = '" . $provider_id . "';");
						}
					}
					$base_price = Input::get('service_base_price');
					$service_price_distance = Input::get('service_price_distance');
					$service_price_time = Input::get('service_price_time');
					foreach (Input::get('service') as $key) {
						$prserv = new ProviderServices;
						$prserv->provider_id = $provider_id;
						$prserv->type = $key;
						$prserv->base_price = $base_price[$key - 1];
						$prserv->price_per_unit_distance = $service_price_distance[$key - 1];
						$prserv->price_per_unit_time = $service_price_time[$key - 1];
						$prserv->save();
					}
					$response_array = array(
						'success' => true,
					);
					$response_code = 200;
					//Log::info('success = ' . print_r($response_array, true));
				} else {
					$response_array = array('success' => false, 'error' => trans('providerController.token_expired'), 'error_code' => 405);
					$response_code = 200;
				}
			} else {
				if ($is_admin) {
					/* $var = Keywords::where('id', 1)->first();
					  $response_array = array('success' => false, 'error' => "" . $var->keyword . "" . trans('providerController.id_not_found'), 'error_code' => 410); */
					$response_array = array('success' => false, 'error' => trans('providerController.id_of') . "" . Config::get('app.generic_keywords.Provider') . "" . trans('providerController.not_found'), 'error_code' => 410);
				} else {
					$response_array = array('success' => false, 'error' => trans('providerController.token_not_valid'), 'error_code' => 406);
				}
				$response_code = 200;
			}
		}
		$response = Response::json($response_array, $response_code);
		//Log::info('repsonse final = ' . print_r($response, true));
		return $response;
	}

	public function services_details() {
		$provider_id = Input::get('id');
		$token = Input::get('token');

		$validator = Validator::make(
						array(
					'provider_id' => $provider_id,
					'token' => $token,
						), array(
					'provider_id' => 'required|integer',
					'token' => 'required',
						), array(
					'provider_id' => trans('providerController.unique_id_missing'),
					'token' => '',
						)
		);

		if ($validator->fails()) {
			$error_messages = $validator->messages()->all();
			$response_array = array('success' => false, 'error' => trans('providerController.invalid_input'), 'error_code' => 401, 'error_messages' => $error_messages);
			$response_code = 200;
		} else {
			$is_admin = $this->isAdmin($token);
			if ($provider_data = $this->getProviderData($provider_id, $token, $is_admin)) {
				// check for token validity
				if (is_token_active($provider_data->token_expiry) || $is_admin) {
					$provserv = ProviderServices::where('provider_id', $provider_id)->get();
					foreach ($provserv as $key) {
						$type = ProviderType::where('id', $key->type)->first();
						$serv_name[] = $type->name;
						$serv_base_price[] = $key->base_price;
						$serv_per_distance[] = $key->price_per_unit_distance;
						$serv_per_time[] = $key->price_per_unit_time;
					}
					$response_array = array(
						'success' => true,
						'serv_name' => $serv_name,
						'serv_base_price' => $serv_base_price,
						'serv_per_distance' => $serv_per_distance,
						'serv_per_time' => $serv_per_time
					);
					$response_code = 200;
				} else {
					$response_array = array('success' => false, 'error' => trans('providerController.token_expired'), 'error_code' => 405);
					$response_code = 200;
				}
			} else {
				if ($is_admin) {
					/* $var = Keywords::where('id', 1)->first();
					  $response_array = array('success' => false, 'error' => "" . $var->keyword . "" . trans('providerController.id_not_found'), 'error_code' => 410); */
					$response_array = array('success' => false, 'error' => trans('providerController.id_of') . "" . Config::get('app.generic_keywords.Provider') . "" . trans('providerController.not_found'), 'error_code' => 410);
				} else {
					$response_array = array('success' => false, 'error' => trans('providerController.token_not_valid'), 'error_code' => 406);
				}
				$response_code = 200;
			}
		}

		$response = Response::json($response_array, $response_code);
		return $response;
	}

	public function panic() {
		$token = Input::get('token');
		$provider_id = Input::get('id');
		$is_admin = $this->isAdmin($token);
		if ($provider_data = $this->getProviderData($provider_id, $token, $is_admin)) {
			// check for token validity
			if (is_token_active($provider_data->token_expiry) || $is_admin) {
				$lat = Input::get('latitude');
				$long = Input::get('longitude');
				$location = 'http://maps.google.com/maps?z=12&t=m&q=loc:lat+long';
				$location = str_replace('lat', $lat, $location);
				$location = str_replace('long', $long, $location);

				/* $var = Keywords::where('id', 1)->first(); */

				/* $email_body = "" . $var->keyword . ' id = ' . $provider_id . '. ' . trans('providerController.my_current_location') . ':  <br/>' . $location; */
				$email_body = "" . Config::get('app.generic_keywords.Provider') . ' id = ' . $provider_id . '. ' . trans('providerController.my_current_location') . ':  <br/>' . $location;
				$subject = trans('providerController.panic_alert');
				email_notification($provider_id, 'admin', $email_body, $subject);
				$response_array = array('success' => true, 'is_active' => $provider_data->is_active);
				$response_code = 200;
			} else {
				$response_array = array('success' => false, 'error' => trans('providerController.token_expired'), 'error_code' => 405);
				$response_code = 200;
			}
		} else {
			if ($is_admin) {
				/* $var = Keywords::where('id', 1)->first();
				  $response_array = array('success' => false, 'error' => "" . $var->keyword . "" . trans('providerController.id_not_found'), 'error_code' => 410); */
				$response_array = array('success' => false, 'error' => trans('providerController.id_of') . "" . Config::get('app.generic_keywords.Provider') . "" . trans('providerController.not_found'), 'error_code' => 410);
			} else {
				$response_array = array('success' => false, 'error' => trans('providerController.token_not_valid'), 'error_code' => 406);
			}
			$response_code = 200;
		}
	}

	public function check_banking() {
		$token = Input::get('token');
		$provider_id = Input::get('id');
		$is_admin = $this->isAdmin($token);
		if ($provider_data = $this->getProviderData($provider_id, $token, $is_admin)) {
			// check for token validity
			if (is_token_active($provider_data->token_expiry) || $is_admin) {
				// do
				$default_banking = Config::get('app.default_payment');
				$resp = array();
				$resp['default_banking'] = $default_banking;
				$provider = Provider::where('id', $provider_id)->first();
				if ($provider->merchant_id != NULL) {
					$resp['provider']['merchant_id'] = $provider->merchant_id;
				}
				$response_array = array('success' => true, 'details' => $resp);
				$response_code = 200;
			}
		}
		$response = Response::json($response_array, $response_code);
		return $response;
	}

	public function logout() {
		if (Request::isMethod('post')) {
			$provider_id = Input::get('id');
			$token = Input::get('token');

			$validator = Validator::make(
							array(
						'provider_id' => $provider_id,
						'token' => $token,
							), array(
						'provider_id' => 'required|integer',
						'token' => 'required',
							), array(
						'provider_id' => trans('providerController.unique_id_missing'),
						'token' => '',
							)
			);

			if ($validator->fails()) {
				$error_messages = $validator->messages()->all();
				$response_array = array('success' => false, 'error' => trans('providerController.invalid_input'), 'error_code' => 401, 'error_messages' => $error_messages);
				$response_code = 200;
			} else {
				$is_admin = $this->isAdmin($token);
				if ($provider_data = $this->getProviderData($provider_id, $token, $is_admin)) {
					// check for token validity
					if (is_token_active($provider_data->token_expiry) || $is_admin) {

						//$provider = Provider::find($provider_id);
						$provider_data->latitude = 0;
						$provider_data->longitude = 0;
						$provider_data->old_latitude = 0;
						$provider_data->old_longitude = 0;
						$provider_data->device_token = 0;
						$provider_data->is_active = 0;
						$provider_data->save();

						$response_array = array('success' => true, 'error' => trans('providerController.success_logout'));
						$response_code = 200;
					} else {
						$response_array = array('success' => false, 'error' => trans('providerController.token_expired'), 'error_code' => 405);
						$response_code = 200;
					}
				} else {
					if ($is_admin) {
						$response_array = array('success' => false, 'error' => trans('providerController.provider_id_not_found'), 'error_code' => 410);
					} else {
						$response_array = array('success' => false, 'error' => trans('providerController.token_not_valid'), 'error_code' => 406);
					}
					$response_code = 200;
				}
			}
		}
		$response = Response::json($response_array, $response_code);
		return $response;
	}

	//atualiza no banco de dados o token do dispositivo usado pelo provedor para o envio de notificacoes
	public function update_device_token() {
		if (Request::isMethod('post')) {
			$token = Input::get('token');
			$provider_id = Input::get('id');
			$device_token = Input::get('device_token');

			$validator = Validator::make(
							array(
								'token' => $token,
								'provider_id' => $provider_id,
								'device_token' => $device_token 
							),
							array(
								'token' => 'required',
								'provider_id' => 'required|integer',
								'device_token' => 'required'
							),
							array(
								'token' => '',
								'provider_id' => trans('providerController.unique_id_missing'),
								'device_token' => ''
							)
			);

			if ($validator->fails()) {
				$error_messages = $validator->messages()->all();
				$response_array = array('success' => false, 'error' => trans('providerController.invalid_input'), 'error_code' => 401, 'error_messages' => $error_messages);
				$response_code = 200;
			} else {

				if ($provider_data = $this->getProviderData($provider_id, $token, false)) {
					// check for token validity
					if (is_token_active($provider_data->token_expiry)) {
						$provider = Provider::find($provider_id);
						$provider->device_token = $device_token;
						$provider->save();

						$response_array = array(
							'success' => true
						);
					} else {
						$response_array = array(
							'success' => false,
							'error' => trans('providerController.token_expired'),
							'error_code' => 412
						);
					}
				} 
				else {
					$response_array = array('success' => false, 'error' => trans('providerController.token_not_valid'), 'error_code' => 406);
				}
				$response_code = 200;
			}
		}
		$response = Response::json($response_array, $response_code);
		return $response;
	}

	public function deactivate_offline_providers(){
		$setting_deactivation_time = Settings::where('key', 'deactivate_provider_time')->first();
		
		DB::update(
			sprintf(
				"UPDATE `provider`
				SET `is_active` = 0
				WHERE `updated_at` < DATE_SUB('" . date("Y-m-d H:i:s") . "', INTERVAL %d MINUTE)
				AND `is_active` = 1 AND `is_available` = 1",
			$setting_deactivation_time? $setting_deactivation_time->value : 10)
		);


	}
}
