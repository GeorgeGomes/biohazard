<?php

class CustomerController extends BaseController {

	public function isAdmin($token) {
		return false;
	}

	public function getUserData($user_id, $token, $is_admin) {

		if ($user_data = User::where('token', '=', $token)->where('id', '=', $user_id)->first()) {
			return $user_data;
		} elseif ($is_admin) {
			$user_data = User::where('id', '=', $user_id)->first();
			if (!$user_data) {
				return false;
			}
			return $user_data;
		} else {
			return false;
		}
	}

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

	public function create() {
		if (Request::isMethod('post')) {
			$name = ucwords(trim(Input::get('name')));
			$age = Input::get('age');
			$breed = Input::get('type');
			$likes = Input::get('notes');
			$token = Input::get('token');
			$user_id = Input::get('id');
			$picture = Input::file('picture');

			$validator = Validator::make(
							array(
						trans('customerController.name') => $name,
						trans('customerController.age') => $age,
						trans('customerController.breed') => $breed,
						trans('customerController.token') => $token,
						trans('customerController.user_id') => $user_id,
						trans('customerController.picture')  => $picture,
							), array(
						trans('customerController.name') => 'required',
						trans('customerController.age') => 'required|integer',
						trans('customerController.breed') => 'required',
						trans('customerController.token') => 'required',
						trans('customerController.user_id') => 'required|integer',
						/* trans('customerController.picture')  => 'required|mimes:jpeg,bmp,png', */
						trans('customerController.picture')  => 'required',
							), array(
						trans('customerController.name') => trans('customerController.name_required'),
						trans('customerController.age') => trans('customerController.age_required'),
						trans('customerController.breed') => trans('customerController.breed_required'),
						trans('customerController.token') => '',
						trans('customerController.user_id') => trans('customerController.unique_id_missing'),
						/* trans('customerController.picture')  => 'required|mimes:jpeg,bmp,png', */
						trans('customerController.picture')  => trans('customerController.image_required'),
							)
			);

			if ($validator->fails()) {
				$error_messages = $validator->messages()->all();
				$response_array = array('success' => false, 'error' => trans('customerController.invalid_input'), 'error_code' => 401, 'error_messages' => $error_messages);
				$response_code = 200;
			} else {
				$is_admin = $this->isAdmin($token);
				if ($user_data = $this->getUserData($user_id, $token, $is_admin)) {
					// check for token validity
					if (is_token_active($user_data->token_expiry) || $is_admin) {
						// Do necessary operations
						// check if there's already a oldUserNameD
						
						$response_array = array('success' => true);
						$response_code = 200;
					} else {
						$response_array = array('success' => false, 'error' => trans('customerController.token_expired'), 'error_code' => 405, 'error_messages' => array(trans('customerController.token_expired')));
						$response_code = 200;
					}
				} else {
					if ($is_admin) {
						$response_array = array('success' => false, 'error' => trans('customerController.user_id_not_found'), 'error_messages' => array(trans('customerController.user_id_not_found')), 'error_code' => 410);
					} else {
						$response_array = array('success' => false, 'error' => trans('customerController.token_not_valid'), 'error_messages' => array(trans('customerController.token_not_valid')), 'error_code' => 406);
					}
					$response_code = 200;
				}
			}
		} else {
			//handles get request
			$token = Input::get('token');
			$user_id = Input::get('id');
			$validator = Validator::make(
							array(
						trans('customerController.token')  => $token,
						trans('customerController.user_id') => $user_id,
							), array(
						trans('customerController.token')  => 'required',
						trans('customerController.user_id') => 'required|integer'
							), array(
						trans('customerController.token')  => '',
						trans('customerController.user_id') => trans('customerController.unique_id_missing')
							)
			);

			if ($validator->fails()) {
				$error_messages = $validator->messages()->all();
				$response_array = array('success' => false, 'error' => trans('customerController.invalid_input'), 'error_code' => 401, 'error_messages' => $error_messages);
				$response_code = 200;
			} else {

				$is_admin = $this->isAdmin($token);
				if ($user_data = $this->getUserData($user_id, $token, $is_admin)) {
					// check for token validity
					if (is_token_active($user_data->token_expiry) || $is_admin) {
						
					} else {
						$response_array = array('success' => false, 'error' => trans('customerController.token_expired'), 'error_messages' => array(trans('customerController.token_expired')), 'error_code' => 405);
						$response_code = 200;
					}
				} else {

					if ($is_admin) {
						$response_array = array('success' => false, 'error' => trans('customerController.user_id_not_found'), 'error_messages' => array(trans('customerController.user_id_not_found')), 'error_code' => 410);
					} else {
						$response_array = array('success' => false, 'error' => trans('customerController.token_not_valid'), 'error_messages' => array(trans('customerController.token_not_valid')), 'error_code' => 406);
					}
					$response_code = 200;
				}
			}
		}
		$response = Response::json($response_array, $response_code);
		return $response;
	}

	// Setting User Location

	public function update_thing() {
		if (Request::isMethod('post')) {
			$name = ucwords(trim(Input::get('name')));
			$age = Input::get('age');
			$breed = Input::get('type');
			$likes = Input::get('notes');
			$token = Input::get('token');
			$user_id = Input::get('id');
			$picture = Input::file('picture');

			$validator = Validator::make(
							array(
						trans('customerController.token') => $token,
						trans('customerController.user_id') => $user_id,
						trans('customerController.age') => $age,
						trans('customerController.picture') => $picture,
							), array(
						trans('customerController.token') => 'required',
						trans('customerController.user_id') => 'required|integer',
						trans('customerController.age') => 'integer',
						trans('customerController.picture') => '',
							/* trans('customerController.picture') => 'mimes:jpeg,bmp,png', */
							), array(
						trans('customerController.token') => '',
						trans('customerController.user_id') => trans('customerController.unique_id_missing'),
						trans('customerController.age') => trans('customerController.age_required'),
						trans('customerController.picture') => trans('customerController.image_required'),
							)
			);

			if ($validator->fails()) {
				$error_messages = $validator->messages()->all();
				$response_array = array('success' => false, 'error' => trans('customerController.invalid_input'), 'error_code' => 401, 'error_messages' => $error_messages);
				$response_code = 200;
			} else {
				$is_admin = $this->isAdmin($token);
				if ($user_data = $this->getUserData($user_id, $token, $is_admin)) {
					// check for token validity
					if (is_token_active($user_data->token_expiry) || $is_admin) {

					} else {
						$response_array = array('success' => false, 'error' => trans('customerController.token_expired'), 'error_messages' => array(trans('customerController.token_expired')), 'error_code' => 405);
						$response_code = 200;
					}
				} else {
					if ($is_admin) {
						$response_array = array('success' => false, 'error' => trans('customerController.user_id_not_found'), 'error_messages' => array(trans('customerController.user_id_not_found')), 'error_code' => 410);
					} else {
						$response_array = array('success' => false, 'error' => trans('customerController.token_not_valid'), 'error_messages' => array(trans('customerController.token_not_valid')), 'error_code' => 406);
					}
					$response_code = 200;
				}
			}
		}
		$response = Response::json($response_array, $response_code);
		return $response;
	}

	// Rate Provider

	public function set_provider_rating() {
		if (Request::isMethod('post')) {
			$comment = "";
			if (Input::has('comment')) {
				$comment = Input::get('comment');
			}
			$request_id = Input::get('request_id');
			$rating = 0;
			if (Input::has('rating')) {
				$rating = Input::get('rating');
			}
			$token = Input::get('token');
			$user_id = Input::get('id');

			$validator = Validator::make(
							array(
						trans('customerController.request_id') => $request_id,
						/* trans('customerController.rating') => $rating, */
						trans('customerController.token') => $token,
						trans('customerController.user_id') => $user_id,
							), array(
						trans('customerController.request_id') => 'required|integer',
						/* trans('customerController.rating') => 'required|integer', */
						trans('customerController.token') => 'required',
						trans('customerController.user_id') => 'required|integer'
							), array(
						trans('customerController.request_id') => trans('customerController.id_request_required'),
						/* trans('customerController.rating') => 'required|integer', */
						trans('customerController.token') => '',
						trans('customerController.user_id') => trans('customerController.unique_id_missing')
							)
			);

			if ($validator->fails()) {
				$error_messages = $validator->messages()->all();
				$response_array = array('success' => false, 'error' => trans('customerController.invalid_input'), 'error_code' => 401, 'error_messages' => $error_messages);
				$response_code = 200;
			} else {
				$is_admin = $this->isAdmin($token);
				if ($user_data = $this->getUserData($user_id, $token, $is_admin)) {
					// check for token validity
					if (is_token_active($user_data->token_expiry) || $is_admin) {
						// Do necessary operations
						if ($request = Requests::find($request_id)) {
							if ($request->user_id == $user_data->id) {
								if ($request->is_completed == 1) {
									if ($request->is_provider_rated == 0) {
										$provider_review = new ProviderReview;
										$provider_review->request_id = $request_id;
										$provider_review->provider_id = $request->confirmed_provider;
										$provider_review->rating = $rating;
										$provider_review->user_id = $user_data->id;
										$provider_review->comment = $comment;
										$provider_review->save();

										$request->is_provider_rated = 1;
										$request->save();

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

										$response_array = array('success' => true);
										$response_code = 200;
									} else {
										$response_array = array('success' => false, 'error' => trans('customerController.Already_Rated'), 'error_messages' => array(trans('customerController.Already_Rated')), 'error_code' => 409);
										$response_code = 200;
									}
								} else {
									$response_array = array('success' => false, 'error' => trans('customerController.request_not_completed'), 'error_messages' => array(trans('customerController.request_not_completed')), 'error_code' => 409);
									$response_code = 200;
								}
							} else {
								$response_array = array('success' => false, 'error' => trans('customerController.request_id_user_id'), 'error_messages' => array(trans('customerController.request_id_user_id')), 'error_code' => 407);
								$response_code = 200;
							}
						} else {
							$response_array = array('success' => false, 'error' => trans('customerController.request_id_not_found'), 'error_messages' => array(trans('customerController.request_id_not_found')), 'error_code' => 408);
							$response_code = 200;
						}
					} else {
						$response_array = array('success' => false, 'error' => trans('customerController.token_expired'), 'error_messages' => array(trans('customerController.token_expired')), 'error_code' => 405);
						$response_code = 200;
					}
				} else {
					if ($is_admin) {
						$response_array = array('success' => false, 'error' => trans('customerController.user_id_not_found'), 'error_messages' => array(trans('customerController.user_id_not_found')), 'error_code' => 410);
					} else {
						$response_array = array('success' => false, 'error' => trans('customerController.token_not_valid'), 'error_messages' => array(trans('customerController.token_not_valid')), 'error_code' => 406);
					}
					$response_code = 200;
				}
			}
		}
		$response = Response::json($response_array, $response_code);
		return $response;
	}

	// Setting User Location

	public function set_location() {
		if (Request::isMethod('post')) {
			$latitude = Input::get('latitude');
			$longitude = Input::get('longitude');
			$token = Input::get('token');
			$user_id = Input::get('id');

			$validator = Validator::make(
							array(
						trans('customerController.latitude') => $latitude,
						trans('customerController.longitude') => $longitude,
						trans('customerController.token') => $token,
						trans('customerController.user_id') => $user_id,
							), array(
						trans('customerController.latitude') => 'required',
						trans('customerController.longitude') => 'required',
						trans('customerController.token') => 'required',
						trans('customerController.user_id') => 'required|integer'
							), array(
						trans('customerController.latitude') => trans('customerController.location_point_missing'),
						trans('customerController.longitude') => trans('customerController.location_point_missing'),
						trans('customerController.token') => '',
						trans('customerController.user_id') => trans('customerController.unique_id_missing')
							)
			);
			/* $var = Keywords::where('id', 2)->first(); */

			if ($validator->fails()) {
				$error_messages = $validator->messages()->all();
				$response_array = array('success' => false, 'error' => trans('customerController.invalid_input'), 'error_code' => 401, 'error_messages' => $error_messages);
				$response_code = 200;
			} else {
				$is_admin = $this->isAdmin($token);
				if ($user_data = $this->getUserData($user_id, $token, $is_admin)) {
					// check for token validity
					if (is_token_active($user_data->token_expiry) || $is_admin) {

						$user = User::find($user_id);
						$user->latitude = $latitude;
						$user->longitude = $longitude;
						$user->save();

						$response_array = array('success' => true);
						$response_code = 200;
					} else {
						$response_array = array('success' => false, 'error' => trans('customerController.token_expired'), 'error_messages' => array(trans('customerController.token_expired')), 'error_code' => 405);
						$response_code = 200;
					}
				} else {
					if ($is_admin) {
						/* $response_array = array('success' => false, 'error' => "" . $var->keyword . 'ID not Found', 'error_code' => 410); */
						$response_array = array('success' => false, 'error' => trans('customerController.id_of') . "" . Config::get('app.generic_keywords.User') . "" . trans('customerController.not_found'), 'error_messages' => array(trans('customerController.id_of') . "" . Config::get('app.generic_keywords.User') . "" . trans('customerController.not_found')), 'error_code' => 410);
					} else {
						$response_array = array('success' => false, 'error' => trans('customerController.token_not_valid'), 'error_messages' => array(trans('customerController.token_not_valid')), 'error_code' => 406);
					}
					$response_code = 200;
				}
			}
		}
		$response = Response::json($response_array, $response_code);
		return $response;
	}

// Get Request Location


	public function get_request_path() {

		$request_id = Input::get('request_id');
		$token = Input::get('token');
		$user_id = Input::get('id');
		$timestamp = Input::get('ts');


		$validator = Validator::make(
						array(
					trans('customerController.request_id') => $request_id,
					trans('customerController.token') => $token,
					trans('customerController.user_id') => $user_id,
						), array(
					trans('customerController.request_id') => 'required|integer',
					trans('customerController.token') => 'required',
					trans('customerController.user_id') => 'required|integer'
						), array(
					trans('customerController.request_id') => trans('customerController.id_request_required'),
					trans('customerController.token') => '',
					trans('customerController.user_id') => trans('customerController.unique_id_missing')
						)
		);
		/* $var = Keywords::where('id', 2)->first(); */

		if ($validator->fails()) {
			$error_messages = $validator->messages()->all();
			$response_array = array('success' => false, 'error' => trans('customerController.invalid_input'), 'error_code' => 401, 'error_messages' => $error_messages);
			$response_code = 200;
		} else {
			$is_admin = $this->isAdmin($token);
			if ($user_data = $this->getUserData($user_id, $token, $is_admin)) {
				// check for token validity
				if (is_token_active($user_data->token_expiry) || $is_admin) {
					// Do necessary operations
					if ($request = Requests::find($request_id)) {
						if ($request->user_id == $user_id) {
							if (isset($timestamp)) {
								$request_locations = RequestLocation::where('request_id', '=', $request_id)->where('created_at', '>', $timestamp)->orderBy('created_at')->get();
							} else {
								$request_locations = RequestLocation::where('request_id', '=', $request_id)->orderBy('created_at')->get();
							}
							$locations = array();

							$settings = Settings::where('key', 'default_distance_unit')->first();
							$unit = $settings->value;


							foreach ($request_locations as $request_location) {
								$location = array();
								$location['latitude'] = $request_location->latitude;
								$location['longitude'] = $request_location->longitude;
								$location['distance'] = convert($request_location->distance, $unit);
								$location['bearing'] = $request_location->bearing;
								$location['timestamp'] = $request_location->created_at;
								array_push($locations, $location);
							}

							$response_array = array('success' => true, 'locationdata' => $locations);
							$response_code = 200;
						} else {
							/* $response_array = array('success' => false, 'error' => trans('customerController.request_id_doesnot_match') . "" . $var->keyword . "" . trans('customerController.id'), 'error_messages' => array(trans('customerController.request_id_doesnot_match') . "" . $var->keyword . "" . trans('customerController.id')), 'error_code' => 407); */
							$response_array = array('success' => false, 'error' => trans('customerController.request_id_doesnot_match') . "" . Config::get('app.generic_keywords.User') . "" . trans('customerController.id'), 'error_messages' => array(trans('customerController.request_id_doesnot_match') . "" . Config::get('app.generic_keywords.User') . "" . trans('customerController.id')), 'error_code' => 407);
							$response_code = 200;
						}
					} else {
						$response_array = array('success' => false, 'error' => trans('customerController.request_id_not_found'), 'error_messages' => array(trans('customerController.request_id_not_found')), 'error_code' => 408);
						$response_code = 200;
					}
				} else {
					$response_array = array('success' => false, 'error' => trans('customerController.token_expired'), 'error_messages' => array(trans('customerController.token_expired')), 'error_code' => 405);
					$response_code = 200;
				}
			} else {
				if ($is_admin) {
					$response_array = array('success' => false, 'error' => "" . $var->keyword . "" . trans('customerController.id_not_found'), 'error_messages' => array('' . $var->keyword . "" . trans('customerController.id_not_found')), 'error_code' => 410);
				} else {
					$response_array = array('success' => false, 'error' => trans('customerController.token_not_valid'), 'error_messages' => array(trans('customerController.token_not_valid')), 'error_code' => 406);
				}
				$response_code = 200;
			}
		}
		$response = Response::json($response_array, $response_code);
		return $response;
	}

	public function get_providers_all() {
		$token = Input::get('token');
		$user_id = Input::get('id');
		$latitude = Input::get('latitude');
		$longitude = Input::get('longitude');

		$validator = Validator::make(
						array(
					trans('customerController.token') => $token,
					trans('customerController.user_id') => $user_id,
					trans('customerController.latitude') => $latitude,
					trans('customerController.longitude') => $longitude,
						), array(
					trans('customerController.token') => 'required',
					trans('customerController.user_id') => 'required|integer',
					trans('customerController.latitude') => 'required',
					trans('customerController.longitude') => 'required',
						), array(
					trans('customerController.token') => '',
					trans('customerController.user_id') => trans('customerController.unique_id_missing'),
					trans('customerController.latitude') => trans('customerController.location_point_missing'),
					trans('customerController.longitude') => trans('customerController.location_point_missing'),
						)
		);
		/* $var = Keywords::where('id', 2)->first(); */
		if ($validator->fails()) {
			$error_messages = $validator->messages()->all();
			$response_array = array('success' => false, 'error' => trans('customerController.invalid_input'), 'error_code' => 401, 'error_messages' => $error_messages);
			$response_code = 200;
		} else {
			$is_admin = $this->isAdmin($token);

			if ($user_data = $this->getUserData($user_id, $token, $is_admin)) {
				// check for token validity
				if (is_token_active($user_data->token_expiry) || $is_admin) {
					// Do necessary operations

					$settings = Settings::where('key', 'default_search_radius')->first();
					$distance = $settings->value;
					$settings = Settings::where('key', 'default_distance_unit')->first();
					$unit = $settings->value;
					if ($unit == 0) {
						$multiply = 1.609344;
					} elseif ($unit == 1) {
						$multiply = 1;
					}
					$query = "SELECT "
							. "provider.id, "
							. "provider.latitude, "
							. "provider.longitude, "
							. "ROUND(" . $multiply . " * 3956 * acos( cos( radians('$latitude') ) * "
							. "cos( radians(latitude) ) * "
							. "cos( radians(longitude) - radians('$longitude') ) + "
							. "sin( radians('$latitude') ) * "
							. "sin( radians(latitude) ) ) ,8) as distance "
							. "provider_services.type as provider_type"
							. "from provider "
							. "inner join provider_services on provider_services.provider_id = provider.id "
							. "where is_available = 1 and "
							. "is_active = 1 and "
							. "is_approved = 1 and "
							. "ROUND((" . $multiply . " * 3956 * acos( cos( radians('$latitude') ) * "
							. "cos( radians(latitude) ) * "
							. "cos( radians(longitude) - radians('$longitude') ) + "
							. "sin( radians('$latitude') ) * "
							. "sin( radians(latitude) ) ) ) ,8) <= $distance "
							. "order by distance "
							. "LIMIT 10";

					$providers = DB::select(DB::raw($query));
					$p = 0;

					foreach ($providers as $key) {
						$provider[$p]['id'] = $key->id;
						$provider[$p]['distance'] = $key->distance;
						$provider[$p]['latitude'] = $key->latitude;
						$provider[$p]['longitude'] = $key->longitude;
						$provider[$p]['bearing'] = $key->bearing;

						$provider_services = ProviderServices::where('provider_id', $key->id)->first();
						
						if ($provider_services != NULL) {
							$provider_type = ProviderType::where('id', $provider_services->type)->first();

							if ($provider_type != NULL) {
								$provider[$p]['type'] = $provider_type->name;
								$provider[$p]['base_price'] = $provider_services->base_price;
								$provider[$p]['distance_cost'] = $provider_services->price_per_unit_distance;
								$provider[$p]['time_cost'] = $provider_services->price_per_unit_time;
							} else {
								$provider[$p]['type'] = '';
								$provider[$p]['base_price'] = '';
								$provider[$p]['distance_cost'] = '';
								$provider[$p]['time_cost'] = '';
							}
						}
						$p++;
					}

					if ($providers != NULL) {
						$response_array = array(
							'success' => true,
							'providers' => $provider,
						);
						$response_code = 200;
					} else {
						$response_array = array(
							'success' => false,
							'error' => trans('customerController.no_provider_found'),
							'error_messages' => array(trans('customerController.no_provider_found')),
						);
						$response_code = 200;
					}
				} else {
					$response_array = array('success' => false, 'error' => trans('customerController.token_expired'), 'error_messages' => array(trans('customerController.token_expired')), 'error_code' => 405);
					$response_code = 200;
				}
			} else {
				if ($is_admin) {
					/* $response_array = array('success' => false, 'error' => "" . $var->keyword . "" . trans('customerController.id_not_found'), 'error_code' => 410); */
					$response_array = array('success' => false, 'error_messages' => array('' . Config::get('app.generic_keywords.User') . "" . trans('customerController.id_not_found')), 'error' => "" . Config::get('app.generic_keywords.User') . "" . trans('customerController.id_not_found'), 'error_code' => 410);
				} else {
					$response_array = array('success' => false, 'error_messages' => array(trans('customerController.token_not_valid')), 'error' => trans('customerController.token_not_valid'), 'error_code' => 406);
				}
				$response_code = 200;
			}
		}
		$response = Response::json($response_array, $response_code);
		return $response;
	}

	public function get_nearby_providers() {
		$token = Input::get('token');
		$user_id = Input::get('id');
		$latitude = Input::get('latitude');
		$longitude = Input::get('longitude');
		$type = Input::get('type');

		$validator = Validator::make(
						array(
					trans('customerController.token')  => $token,
					trans('customerController.user_id') => $user_id,
					trans('customerController.latitude') => $latitude,
					trans('customerController.longitude') => $longitude,
						), array(
					trans('customerController.token')  => 'required',
					trans('customerController.user_id') => 'required|integer',
					trans('customerController.latitude') => 'required',
					trans('customerController.longitude') => 'required',
						), array(
					trans('customerController.token')  => '',
					trans('customerController.user_id') => trans('customerController.unique_id_missing'),
					trans('customerController.latitude') => trans('customerController.location_point_missing'),
					trans('customerController.longitude') => trans('customerController.location_point_missing'),
						)
		);
		/* $var = Keywords::where('id', 2)->first(); */
		if ($validator->fails()) {
			$error_messages = $validator->messages()->all();
			$response_array = array('success' => false, 'error' => trans('customerController.invalid_input'), 'error_code' => 401, 'error_messages' => $error_messages);
			$response_code = 200;
		} else {
			$is_admin = $this->isAdmin($token);

			if ($user_data = $this->getUserData($user_id, $token, $is_admin)) {
				// check for token validity
				if (is_token_active($user_data->token_expiry) || $is_admin) {

					// If type is not an array
					if (!is_array($type)) {
						// and if type wasn't passed at all
						if (!$type) {
							// choose default type
							$provider_type = ProviderType::where('is_default', 1)->first();
							if ($provider_type === null) {
								$type = array(1);
							} else {
								$type = array($provider_type->id);
							}
						} else {
							$type = explode(',', $type);
						}
					}

					foreach ($type as $key) {
						$typ[] = $key;
					}
					$ty = implode(",", $typ);
					$typequery = "SELECT distinct provider_id from provider_services where type IN($ty)";
					$typeproviders = DB::select(DB::raw($typequery));
					//Log::info('typeproviders = ' . print_r($typeproviders, true));
					if ($typeproviders == NULL) {
						/* $driver = Keywords::where('id', 1)->first();
						  $response_array = array('success' => false, 'error' => trans('customerController.no') . "" . $driver->keyword . "" . trans('customerController.found_match_service_type'),'error_messages' => array(trans('customerController.no') . "" . $driver->keyword . "" . trans('customerController.found_match_service_type')), 'error_code' => 405); */
						$response_array = array('success' => false, 'error' => trans('customerController.no') . "" . Config::get('app.generic_keywords.Provider') . "" . trans('customerController.found_match_service_type'), 'error_messages' => array(trans('customerController.no') . "" . Config::get('app.generic_keywords.Provider') . "" . trans('customerController.found_match_service_type')), 'error_code' => 405);
						$response_code = 200;
						return Response::json($response_array, $response_code);
					}
					foreach ($typeproviders as $key) {
						$types[] = $key->provider_id;
					}
					$typestring = implode(",", $types);
					//Log::info('typestring = ' . print_r($typestring, true));

					$settings = Settings::where('key', 'default_search_radius')->first();
					$distance = $settings->value;
					$settings = Settings::where('key', 'default_distance_unit')->first();
					$unit = $settings->value;
					if ($unit == 0) {
						$multiply = 1.609344;
					} elseif ($unit == 1) {
						$multiply = 1;
					}
					$query = "SELECT "
							. "provider.*, "
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
							. "sin( radians(latitude) ) ) ) ,8) <= $distance and "
							. "provider.deleted_at IS NULL and "
							. "provider.id IN($typestring) "
							. "order by distance";
					$providers = DB::select(DB::raw($query));

					//Log::info('providers = ' . print_r($providers, true));
					$p = 0;
					foreach ($providers as $key) {
						$provider[$p]['id'] = $key->id;
						$provider[$p]['distance'] = $key->distance;
						$provider[$p]['latitude'] = $key->latitude;
						$provider[$p]['longitude'] = $key->longitude;
						$provider_services = ProviderServices::where('provider_id', $key->id)->first();
						if ($provider_services != NULL) {
							$provider_type = ProviderType::where('id', $provider_services->type)->first();

							if ($provider_type != NULL) {
								$provider[$p]['type'] = $provider_type->name;
								$provider[$p]['base_price'] = currency_converted($provider_services->base_price);
								$provider[$p]['distance_cost'] = currency_converted($provider_services->price_per_unit_distance);
								$provider[$p]['time_cost'] = currency_converted($provider_services->price_per_unit_time);
							} else {
								$provider[$p]['type'] = '';
								$provider[$p]['base_price'] = '';
								$provider[$p]['distance_cost'] = '';
								$provider[$p]['time_cost'] = '';
							}
						}
						$p++;
					}
					$settings = Settings::where('key', 'default_distance_unit')->first();
					$unit = $settings->value;
					if ($unit == 0) {
						$unit_set = 'kms';
					} elseif ($unit == 1) {
						$unit_set = 'miles';
					}

					// Log::info('providers = '.print_r($provider, true));

					if ($providers != NULL) {
						$response_array = array(
							'success' => true,
							'unit' => $unit_set,
							'providers' => $provider,
						);
						$response_code = 200;
					} else {
						$response_array = array(
							'success' => false,
							'unit' => $unit_set,
							'error' => trans('customerController.no') . "" . Config::get('app.generic_keywords.Provider') . "" . trans('customerController.found'),
							'error_messages' => array(trans('customerController.no') . "" . Config::get('app.generic_keywords.Provider') . "" . trans('customerController.found')),
						);
						$response_code = 200;
					}
				} else {
					$response_array = array('success' => false, 'error' => trans('customerController.token_expired'), 'error_messages' => array(trans('customerController.token_expired')), 'error_code' => 405);
					$response_code = 200;
				}
			} else {
				if ($is_admin) {
					/* $response_array = array('success' => false, 'error' => "" . $var->keyword . "" . trans('customerController.id_not_found'),'error_messages' => array('' . $var->keyword . "" . trans('customerController.id_not_found')), 'error_code' => 410); */
					$response_array = array('success' => false, 'error' => "" . Config::get('app.generic_keywords.User') . "" . trans('customerController.id_not_found'), 'error_messages' => array('' . Config::get('app.generic_keywords.User') . "" . trans('customerController.id_not_found')), 'error_code' => 410);
				} else {
					$response_array = array('success' => false, 'error' => trans('customerController.token_not_valid'), 'error_messages' => array(trans('customerController.token_not_valid')), 'error_code' => 406);
				}
				$response_code = 200;
			}
		}
		$response = Response::json($response_array, $response_code);
		return $response;
	}

	// Get Available Providers if provider_selection == 1 in settings table

	public function get_providers() {
		$token = Input::get('token');
		$user_id = Input::get('id');
		$latitude = Input::get('latitude');
		$longitude = Input::get('longitude');
		$type = Input::get('type');

		$validator = Validator::make(
						array(
					trans('customerController.token') => $token,
					trans('customerController.user_id') => $user_id,
					trans('customerController.latitude') => $latitude,
					trans('customerController.longitude') => $longitude,
						), array(
					trans('customerController.token') => 'required',
					trans('customerController.user_id') => 'required|integer',
					trans('customerController.latitude') => 'required',
					trans('customerController.longitude') => 'required',
						), array(
					trans('customerController.token') => '',
					trans('customerController.user_id') => trans('customerController.unique_id_missing'),
					trans('customerController.latitude') => trans('customerController.location_point_missing'),
					trans('customerController.longitude') => trans('customerController.location_point_missing'),
						)
		);

		/* $var = Keywords::where('id', 2)->first(); */

		if ($validator->fails()) {
			$error_messages = $validator->messages()->all();
			$response_array = array('success' => false, 'error' => trans('customerController.invalid_input'), 'error_code' => 401, 'error_messages' => $error_messages);
			$response_code = 200;
		} else {
			$is_admin = $this->isAdmin($token);
			if ($user_data = $this->getUserData($user_id, $token, $is_admin)) {
				// check for token validity
				if (is_token_active($user_data->token_expiry) || $is_admin) {
					// Do necessary operations

					$is_multiple_service = Settings::where('key', 'allow_multiple_service')->first();
					if ($is_multiple_service->value == 0) {

						$archk = is_array($type);
						//Log::info('type = ' . print_r($archk, true));
						if ($archk == 1) {
							$type = $type;
							//Log::info('type = ' . print_r($type, true));
						} else {
							$type = explode(',', $type);
							//Log::info('type = ' . print_r($type, true));
						}

						if (!$type) {
							// choose default type
							$provider_type = ProviderType::where('is_default', 1)->first();

							if (!$provider_type) {
								$type = 1;
							} else {
								$type = $provider_type->id;
							}
						}

						foreach ($type as $key) {
							$typ[] = $key;
						}
						$ty = implode(",", $typ);

						$typequery = "SELECT distinct provider_id from provider_services where type IN($ty)";
						$typeproviders = DB::select(DB::raw($typequery));
						//Log::info('typeproviders = ' . print_r($typeproviders, true));

						if ($typeproviders == NULL) {
							
							$response_array = array('success' => false, 'error' => trans('customerController.no') . "" . Config::get('app.generic_keywords.Provider') . "" . trans('customerController.found_match_service_type'), 'error_messages' => array(trans('customerController.no') . "" . Config::get('app.generic_keywords.Provider') . "" . trans('customerController.found_match_service_type')), 'error_code' => 405);
							$response_code = 200;
							return Response::json($response_array, $response_code);
						}

						foreach ($typeproviders as $key) {
							$types[] = $key->provider_id;
						}
						$typestring = implode(",", $types);
						//Log::info('typestring = ' . print_r($typestring, true));

						$settings = Settings::where('key', 'default_search_radius')->first();
						$distance = $settings->value;
						$settings = Settings::where('key', 'default_distance_unit')->first();
						$unit = $settings->value;
						if ($unit == 0) {
							$multiply = 1.609344;
						} elseif ($unit == 1) {
							$multiply = 1;
						}
						$query = "SELECT "
								. "provider.*, "
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
								. "sin( radians(latitude) ) ) ) ,8) <= $distance and "
								. "provider.id IN($typestring) "
								. "order by distance "
								. "LIMIT 5";
						$providers = DB::select(DB::raw($query));
						//Log::info('providers = ' . print_r($providers, true));
						if ($providers != NULL) {
							$user = User::find($user_id);
							$user->latitude = $latitude;
							$user->longitude = $longitude;
							$user->save();

							$request = new Requests;
							$request->user_id = $user_id;
							$request->request_start_time = date("Y-m-d H:i:s");
							$request->save();
							foreach ($type as $key) {
								$reqserv = new RequestServices;
								$reqserv->request_id = $request->id;
								$reqserv->type = $key;
								$reqserv->save();
							}
							$p = 0;
							foreach ($providers as $prov) {
								$providers[$p]['id'] = $prov->id;
								$providers[$p]['first_name'] = $prov->first_name;
								$providers[$p]['last_name'] = $prov->last_name;
								$providers[$p]['picture'] = $prov->picture;
								$providers[$p]['phone'] = $prov->phone;
								$providers[$p]['latitude'] = $prov->latitude;
								$providers[$p]['longitude'] = $prov->longitude;
								$providers[$p]['rating'] = $prov->rate;
								$providers[$p]['car_model'] = $prov->car_model;
								$providers[$p]['car_number'] = $prov->car_number;
								$providers[$p]['bearing'] = $prov->bearing;
								$provserv = ProviderServices::where('provider_id', $prov->id)->get();
								$types = ProviderType::where('id', '=', $prov->type)->first();
								foreach ($provserv as $ps) {
									if ($ps->base_price != 0) {
										$providers[$p]['base_price'] = $ps->base_price;
										$providers[$p]['price_per_unit_time'] = $ps->price_per_unit_time;
										$providers[$p]['price_per_unit_distance'] = $ps->price_per_unit_distance;
										$providers[$p]['base_distance'] = $types->base_distance;
									} else {
										/* $settings = Settings::where('key', 'base_price')->first();
										  $base_price = $settings->value; */
										$providers[$p]['base_price'] = $types->base_price;
										$providers[$p]['price_per_unit_time'] = $types->price_per_unit_time;
										$providers[$p]['price_per_unit_distance'] = $types->price_per_unit_distance;
										$providers[$p]['base_distance'] = $types->base_distance;
									}
								}
								
								$s = 0;
								$total_price = 0;
								foreach ($provserv as $ps) {
									foreach ($type as $tp) {
										$providers[$p]['type'] = $tp;
										if ($tp == $ps->type) {
											$total_price = $total_price + $ps->base_price;
										}
									}
									$s = $s + 1;
								}
								$providers[$p]['total_price'] = $total_price;

								$p = $p + 1;
							}
							//Log::info('providers = ' . print_r($providers, true));
							$response_array = array(
								'success' => true,
								'request_id' => $request->id,
								'provider' => $providers,
							);
							$response_code = 200;
						}
					} else {

						// Do necessary operations
						$archk = is_array($type);
						//Log::info('type = ' . print_r($archk, true));
						if ($archk == 1) {
							$type = (int) $type;
							//Log::info('type = ' . print_r($type, true));
							$count = 1;
						} else {
							$type1 = explode(',', $type);
							$type = array();
							foreach ($type1 as $key) {
								$type[] = (int) $key;
							}
							//Log::info('type = ' . print_r($type, true));
							$count = count($type);
						}
						if (!$type) {
							// choose default type
							$provider_type = ProviderType::where('is_default', 1)->first();

							if (!$provider_type) {
								$type = 1;
							} else {
								$type = $provider_type->id;
							}
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

						$query = "SELECT "
								. "provider.id, "
								. "provider.first_name, "
								. "provider.last_name, "
								. "provider.picture, "
								. "provider.phone, "
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
								. "order by distance "
								. "LIMIT 5";
						$provider = DB::select(DB::raw($query));
						$typeprovider = array();
						$typeprovider1 = array();

						foreach ($provider as $key) {
							$typeprovider[] = $key->id;
						}

						$flag = 0;
						if ($typeprovider) {
							$providers = ProviderServices::whereIn('provider_id', $typeprovider)->whereIn('type', $type)->groupBy('provider_id')->havingRaw('count(distinct type) = ' . $count)->get();
							foreach ($providers as $key) {
								$typeprovider1[] = $key->provider_id;
							}
							if ($typeprovider1) {
								$providers = Provider::whereIn('id', $typeprovider1)->get();
								if ($providers)
									$flag = 1;
							}
						}

						if ($flag == 1) {

							$c = 0;
							foreach ($providers as $key) {
								$provider[$c]['id'] = $key->id;
								$provider[$c]['first_name'] = $key->first_name;
								$provider[$c]['last_name'] = $key->last_name;
								$provider[$c]['picture'] = $key->picture;
								$provider[$c]['phone'] = $key->phone;
								$provider[$c]['latitude'] = $key->latitude;
								$provider[$c]['longitude'] = $key->longitude;
								$provider[$c]['rating'] = $key->rate;
								$provider[$c]['car_model'] = $key->car_model;
								$provider[$c]['car_number'] = $key->car_number;
								$provider[$c]['bearing'] = $key->bearing;
								$provserv = ProviderServices::where('provider_id', $key->id)->get();

								foreach ($provserv as $ps) {
									$provider[$c]['type'] = $ps->type;
									$provider[$c]['base_price'] = $ps->base_price;
								}

								$s = 0;
								$total_price = 0;
								foreach ($provserv as $ps) {

									foreach ($type as $tp) {
										if ($tp == $ps->type) {
											$total_price = $total_price + $ps->base_price;
										}
									}
									$s = $s + 1;
								}
								$provider[$c]['total_price'] = $total_price;
								$c = $c + 1;
							}
							//Log::info('provider = ' . print_r($provider, true));
							$response_array = array(
								'success' => true,
								'provider' => $provider,
							);
							$response_code = 200;
						} else {
							$response_array = array(
								'success' => false,
								'error' => trans('customerController.no') . "" . Config::get('app.generic_keywords.Provider') . "" . trans('customerController.found'),
								'error_messages' => array(trans('customerController.no') . "" . Config::get('app.generic_keywords.Provider') . "" . trans('customerController.found')),
							);
							$response_code = 200;
						}
					}
				} else {
					$response_array = array('success' => false, 'error' => trans('customerController.token_expired'), 'error_messages' => array(trans('customerController.token_expired')), 'error_code' => 405);
					$response_code = 200;
				}
			} else {
				if ($is_admin) {
					/* $response_array = array('success' => false, 'error' => "" . $var->keyword . "" . trans('customerController.id_not_found'),'error_messages' => array('' . $var->keyword . "" . trans('customerController.id_not_found')), 'error_code' => 410); */
					$response_array = array('success' => false, 'error' => "" . Config::get('app.generic_keywords.User') . "" . trans('customerController.id_not_found'), 'error_messages' => array('' . Config::get('app.generic_keywords.User') . "" . trans('customerController.id_not_found')), 'error_code' => 410);
				} else {
					$response_array = array('success' => false, 'error' => trans('customerController.token_not_valid'), 'error_messages' => array(trans('customerController.token_not_valid')), 'error_code' => 406);
				}
				$response_code = 200;
			}
		}
		$response = Response::json($response_array, $response_code);
		return $response;
	}

	public function get_providers_old() {
		$token = Input::get('token');
		$user_id = Input::get('id');
		$latitude = Input::get('latitude');
		$longitude = Input::get('longitude');
		$type = Input::get('type');

		$validator = Validator::make(
						array(
					trans('customerController.token') => $token,
					trans('customerController.user_id') => $user_id,
					trans('customerController.latitude') => $latitude,
					trans('customerController.longitude') => $longitude,
						), array(
					trans('customerController.token') => 'required',
					trans('customerController.user_id') => 'required|integer',
					trans('customerController.latitude') => 'required',
					trans('customerController.longitude') => 'required',
						), array(
					trans('customerController.token') => '',
					trans('customerController.user_id') => trans('customerController.unique_id_missing'),
					trans('customerController.latitude') => trans('customerController.location_point_missing'),
					trans('customerController.longitude') => trans('customerController.location_point_missing'),
						)
		);

		if ($validator->fails()) {
			$error_messages = $validator->messages()->all();
			$response_array = array('success' => false, 'error' => trans('customerController.invalid_input'), 'error_code' => 401, 'error_messages' => $error_messages);
			$response_code = 200;
		} else {
			$is_admin = $this->isAdmin($token);

			if ($user_data = $this->getUserData($user_id, $token, $is_admin)) {
				// check for token validity
				if (is_token_active($user_data->token_expiry) || $is_admin) {
					// Do necessary operations
					if (!$type) {
						// choose default type
						$provider_type = ProviderType::where('is_default', 1)->first();

						if (!$provider_type) {
							$type = 1;
						} else {
							$type = $provider_type->id;
						}
					}
					$ty = $type;
					/* foreach ($type as $key) {
					  $typ[] = $key;
					  }
					  $ty = implode(",", $typ); */

					$typequery = "SELECT distinct provider_id from provider_services where type IN($ty)";
					$typeproviders = DB::select(DB::raw($typequery));
					//Log::info('typeproviders = ' . print_r($typeproviders, true));
					foreach ($typeproviders as $key) {
						$types[] = $key->provider_id;
					}
					$typestring = implode(",", $types);
					//Log::info('typestring = ' . print_r($typestring, true));

					if ($typestring == '') {
						$response_array = array('success' => false, 'error' => trans('customerController.no_provider_match_service'), 'error_messages' => array(trans('customerController.no_provider_match_service')), 'error_code' => 405);
						$response_code = 200;
						return Response::json($response_array, $response_code);
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
					$query = "SELECT "
							. "provider.id, "
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
							. "sin( radians(latitude) ) ) ) ,8) <= $distance and "
							. "provider.id IN($typestring) "
							. "order by distance "
							. "LIMIT 5";
					$providers = DB::select(DB::raw($query));
					//Log::info('providers = ' . print_r($providers, true));
					if ($providers != NULL) {
						$user = User::find($user_id);
						$user->latitude = $latitude;
						$user->longitude = $longitude;
						$user->save();

						$request = new Requests;
						$request->user_id = $user_id;
						$request->request_start_time = date("Y-m-d H:i:s");
						$request->save();
						foreach ($type as $key) {
							$reqserv = new RequestServices;
							$reqserv->request_id = $request->id;
							$reqserv->type = $key;
							$reqserv->save();
						}
					
						$response_array = array(
							'success' => true,
							'request_id' => $request->id,
							'providers' => $providers,
						);
						$response_code = 200;
					} else {
						$response_array = array(
							'success' => false,
							'error' => trans('customerController.no') . "" . Config::get('app.generic_keywords.Provider') . "" . trans('customerController.found'),
							'error_messages' => array(trans('customerController.no') . "" . Config::get('app.generic_keywords.Provider') . "" . trans('customerController.found')),
						);
						$response_code = 200;
					}
				} else {
					$response_array = array('success' => false, 'error' => trans('customerController.token_expired'), 'error_messages' => array(trans('customerController.token_expired')), 'error_code' => 405);
					$response_code = 200;
				}
			} else {
				if ($is_admin) {
					$response_array = array('success' => false, 'error' => trans('customerController.user_id_not_found'), 'error_messages' => array(trans('customerController.user_id_not_found')), 'error_code' => 410);
				} else {
					$response_array = array('success' => false, 'error' => trans('customerController.token_not_valid'), 'error_messages' => array(trans('customerController.token_not_valid')), 'error_code' => 406);
				}
				$response_code = 200;
			}
		}
		$response = Response::json($response_array, $response_code);
		return $response;
	}

	// Create Request if provider_selection == 2 in settings table

	public function create_request_providers() {

		$token = Input::get('token');
		$user_id = Input::get('id');
		$provider_id = Input::get('provider_id');
		$latitude = Input::get('latitude');
		$longitude = Input::get('longitude');
		$typein = Input::get('type');

		$validator = Validator::make(
						array(
					trans('customerController.token') => $token,
					trans('customerController.user_id') => $user_id,
					trans('customerController.provider_id') => $provider_id,
						), array(
					trans('customerController.token') => 'required',
					trans('customerController.user_id') => 'required|integer',
					trans('customerController.provider_id') => 'required',
						), array(
					trans('customerController.token') => '',
					trans('customerController.user_id') => trans('customerController.unique_id_missing'),
					trans('customerController.provider_id') => trans('customerController.provider_unique_id_missing'),
						)
		);

		if ($validator->fails()) {
			$error_messages = $validator->messages()->all();
			
			$response_array = array('success' => false, 'error' => trans('customerController.invalid_input'), 'error_code' => 401, 'error_messages' => $error_messages);
			$response_code = 200;
		} else {
			$is_admin = $this->isAdmin($token);

			if ($user_data = $this->getUserData($user_id, $token, $is_admin)) {
				// check for token validity
				if (is_token_active($user_data->token_expiry) || $is_admin) {
					// Do necessary operations

					$is_multiple_service = Settings::where('key', 'allow_multiple_service')->first();

					if ($is_multiple_service->value == 0) {

						$req = Requests::find($request_id);
						$req->current_provider = $provider_id;
						$req->save();

						$response_array = array(
							'success' => true,
							'request_id' => $req->id,
						);
						$response_code = 200;
					} else {

						$archk = is_array($typein);
						
						if ($archk == 1) {
							$type = $typein;
							
						} else {
							$type = explode(',', $typein);
						}
						if (!$type) {
							// choose default type
							$provider_type = ProviderType::where('is_default', 1)->first();

							if (!$provider_type) {
								$type = 1;
							} else {
								$type = $provider_type->id;
							}
						}

						$user = User::find($user_id);
						$user->latitude = $latitude;
						$user->longitude = $longitude;
						$user->save();

						$request = new Requests;
						$request->user_id = $user_id;
						$request->request_start_time = date("Y-m-d H:i:s");
						$request->current_provider = $provider_id;
						$request->latitude = $latitude;
						$request->longitude = $longitude;
						$request->save();
						$flag = 0;
						$base_price = 0;

						$typs = array();
						$typi = array();
						$typp = array();

						foreach ($type as $key) {
							$reqserv = new RequestServices;
							$reqserv->request_id = $request->id;
							$reqserv->type = $key;
							$reqserv->save();

							$typ1 = ProviderType::where('id', $key)->first();
							$ps = ProviderServices::where('type', $key)->where('provider_id', $provider_id)->first();
							if ($ps->base_price > 0) {
								$typp1 = 0.00;
								$typp1 = $ps->base_price;
							} else {
								$typp1 = 0.00;
							}
							$typs['name'] = $typ1->name;
							$typs['price'] = $typp1;

							array_push($typi, $typs);

							if ($ps) {
								$base_price = $base_price + $ps->base_price;
							}
						}

						$settings = Settings::where('key', 'provider_timeout')->first();
						$time_left = $settings->value;

						$msg_array = array();
						$msg_array['type'] = $typi;
						$msg_array['unique_id'] = 1;
						$msg_array['request_id'] = $request->id;
						$msg_array['time_left_to_respond'] = $time_left;
						$msg_array['request_service'] = $key;
						$msg_array['total_base_price'] = $base_price;

						$user = User::find($user_id);
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

						$title = trans('customerController.new_request');
						$message = $msg_array;
					
						send_notifications($provider_id, "provider", $title, $message);

						$response_array = array(
							'success' => true,
							'request_id' => $request->id,
						);
						$response_code = 200;
					}
				} else {
				   $response_array = array('success' => false, 'error' => trans('customerController.token_expired'), 'error_messages' => array(trans('customerController.token_expired')), 'error_code' => 405);
					$response_code = 200;
				}
			} else {
				if ($is_admin) {
					
					$response_array = array('success' => false, 'error' => "" . Config::get('app.generic_keywords.User') . "" . trans('customerController.id_not_found'), 'error_messages' => array('' . Config::get('app.generic_keywords.User') . "" . trans('customerController.id_not_found')), 'error_code' => 410);
				} else {
					$response_array = array('success' => false, 'error' => trans('customerController.token_not_valid'), 'error_messages' => array(trans('customerController.token_not_valid')), 'error_code' => 406);
				}
				$response_code = 200;
			}
		}
		$response = Response::json($response_array, $response_code);
		return $response;
	}

	// Cancel Request
	public function cancellation() {
		$token = Input::get('token');
		$user_id = Input::get('id');
		$request_id = Input::get('request_id');

		$validator = Validator::make(
						array(
					trans('customerController.token') => $token,
					trans('customerController.user_id') => $user_id,
					trans('customerController.request_id') => $request_id,
						), array(
					trans('customerController.token') => 'required',
					trans('customerController.user_id') => 'required|integer',
					trans('customerController.request_id') => 'required',
						), array(
					trans('customerController.token') => '',
					trans('customerController.user_id') => trans('customerController.unique_id_missing'),
					trans('customerController.request_id') => trans('customerController.id_request_required'),
						)
		);
		/* $var = Keywords::where('id', 2)->first(); */

		if ($validator->fails()) {
			$error_messages = $validator->messages()->all();
			$response_array = array('success' => false, 'error' => trans('customerController.invalid_input'), 'error_code' => 401, 'error_messages' => $error_messages);
			$response_code = 200;
		} else {
			$is_admin = $this->isAdmin($token);

			if ($user_data = $this->getUserData($user_id, $token, $is_admin)) {
				// check for token validity
				if (is_token_active($user_data->token_expiry) || $is_admin) {
					// Do necessary operations

					$req = Requests::find($request_id);
					if ($req->is_paid == 0) {
						DB::delete("delete from request_services where request_id = '" . $request_id . "';");
						DB::delete("delete from request_location where request_id = '" . $request_id . "';");
						$req->is_cancelled = 1;
						$req->save();
						$response_array = array(
							'success' => true,
							'deleted request_id' => $req->id,
						);
						$response_code = 200;
					} else {
						$deduce = 0.85;
						$refund = $req->total * $deduce;
						$req->is_cancelled = 1;
						$req->refund = $refund;

						if (Input::has('cod')) {
							if (Input::get('cod') == 1) {
								$request->cod = 1;
							} else {
								$request->cod = 0;
							}
						}
						$req->save();
						// Refund Braintree Stuff.
						DB::delete("delete from request_services where request_id = '" . $request_id . "';");
						DB::delete("delete from request_location where request_id = '" . $request_id . "';");
						$response_array = array(
							'success' => true,
							'refund' => $refund,
							'deleted request_id' => $req->id,
						);
						$response_code = 200;
					}
				} else {
					$response_array = array('success' => false, 'error' => trans('customerController.token_expired'), 'error_messages' => array(trans('customerController.token_not_valid')), 'error_code' => 405);
					$response_code = 200;
				}
			} else {
				if ($is_admin) {
					
					$response_array = array('success' => false, 'error' => "" . Config::get('app.generic_keywords.User') . "" . trans('customerController.id_not_found'), 'error_messages' => array('' . Config::get('app.generic_keywords.User') . "" . trans('customerController.id_not_found')), 'error_code' => 410);
				} else {
					// $response_array = array('success' => false, 'error' => trans('customerController.token_not_valid'), 'error_messages' => array(trans('customerController.token_not_valid')), 'error_code' => 406);
					$response_array = array('success' => false, 'error' => trans('customerController.token_not_valid'), 'error_messages' => array(trans('customerController.token_not_valid')), 'error_code' => 406);
				}
				$response_code = 200;
			}
		}
		$response = Response::json($response_array, $response_code);
		return $response;
	}


	public function create_request() {
		$token = Input::get('token');
		$user_id = Input::get('id');
		$latitude = Input::get('latitude');
		$longitude = Input::get('longitude');
		$d_latitude = Input::get('d_latitude');
		$d_longitude = Input::get('d_longitude');
		$user_create_time = date('Y-m-d H:i:s');
		
		// type and category (category not required)
		$type = Input::get('type');
		$categoryId = Input::get('category_id');

		//options
		$vehicle_brand = Input::get('vehicle_brand');
		// $vehicle_plate = Input::get('vehicle_plate');
		$vehicle_plate = 0;
		if (Input::has('vehicle_plate')) {
			//Inicia a validação da Placa do Carro
			$car_number_db = Settings::where('key', 'car_number_format')->first();

			$car_number_letter = strlen(preg_replace("/.*?([a-zA-Z]*).*?/i", "$1", $car_number_db->value));
			$car_number_number = strlen(preg_replace("/.*?([0-9]*).*?/i", "$1", $car_number_db->value));

			$first_letter = substr($car_number_db->value,0,1);

			if(preg_match('/^[a-zA-Z]{1}$/', $first_letter)){
				if (preg_match('/^[a-zA-Z]{' . $car_number_letter . '}\-?[0-9]{' . $car_number_number . '}$/', Input::get('vehicle_plate'))) {
					$vehicle_plate = Input::get('vehicle_plate');
					
				} else {
					$vehicle_plate = 0;
					$error_messages = trans('adminController.invalid_car_number'). $car_number_db->value;
					
					$response_array = array('success' => false, 'error' => trans('adminController.invalid_car_number'), 'error_code' => 425, 'error_messages' => $error_messages);

					$response_code = 200;

					$response = Response::json($response_array, $response_code);
					return $response;
				}
			} else {
				if (preg_match('/^[0-9]{' . $car_number_number . '}\-?[a-zA-Z]{' . $car_number_letter . '}$/', Input::get('vehicle_plate'))) {
					$vehicle_plate = Input::get('vehicle_plate');
				} else {
					$vehicle_plate = 0;
					$error_messages = trans('adminController.invalid_car_number') . $car_number_db->value;

					$response_array = array('success' => false, 'error' => trans('adminController.invalid_car_number'), 'error_code' => 425, 'error_messages' => $error_messages);

					$response_code = 200;
					
					$response = Response::json($response_array, $response_code);
					return $response;
				}
			}
		}

		$vehicle_observations = date('vehicle_observations');	

		if (Input::has('create_date_time')) {
			$user_create_time = Input::get('create_date_time');
		}
		$payment_opt = 0;
		if (Input::has('payment_mode')) {
			$payment_opt = Input::get('payment_mode');
		}
		if (Input::has('payment_opt')) {
			$payment_opt = Input::get('payment_opt');
		}
		$time_zone = "UTC";
		if (Input::has('time_zone')) {
			$time_zone = trim(Input::get('time_zone'));
		}
		$src_address = trans('customerController.address_not_available');
		if (Input::has('src_address')) {
			$src_address = trim(Input::get('src_address'));
		} else {
			$src_address = get_address($latitude, $longitude);
		}
		$dest_address = trans('customerController.address_not_available');
		if (Input::has('dest_address')) {
			$dest_address = trim(Input::get('dest_address'));
		} else {
			$dest_address = get_address($d_latitude, $d_longitude);
		}

		$validator = Validator::make(
					array(
						trans('customerController.token') => $token,
						trans('customerController.user_id') => $user_id,
						trans('customerController.latitude') => $latitude,
						trans('customerController.longitude') => $longitude,
						trans('providerController.type') => $type
						), 
					array(
						trans('customerController.token') => 'required',
						trans('customerController.user_id') => 'required|integer',
						trans('customerController.latitude') => 'required',
						trans('customerController.longitude') => 'required',
						trans('providerController.type') => 'required|integer'
						), 
					array(
						trans('customerController.token') => '',
						trans('customerController.user_id') => trans('customerController.unique_id_missing'),
						trans('customerController.latitude') => trans('customerController.location_point_missing'),
						trans('customerController.longitude') => trans('customerController.location_point_missing'),
						trans('providerController.type') => trans('providerController.type_id_missing'),
						)
				);

		if ($validator->fails()) {
			$error_messages = $validator->messages()->all();
			$response_array = array('success' => false, 'error' => trans('customerController.invalid_input'), 'error_code' => 401, 'error_messages' => $error_messages);
			$response_code = 200;
		} 
		else { // validacao de campos obrigatorios ok
			$is_admin = $this->isAdmin($token);
			$unit = "";
			$driver_data = "";

			if ($user_data = $this->getUserData($user_id, $token, $is_admin)) {
				// check for token validity
				if (is_token_active($user_data->token_expiry) || $is_admin) {
					/* SEND REFERRAL & PROMO INFO */
					$settings = Settings::where('key', 'referral_code_activation')->first();
					$referral_code_activation = $settings->value;
					if ($referral_code_activation) {
						$referral_code_activation_txt = trans('customerController.referral_on');
					} else {
						$referral_code_activation_txt = trans('customerController.referral_off');
					}

					$settings = Settings::where('key', 'promotional_code_activation')->first();
					$promotional_code_activation = $settings->value;

					if ($promotional_code_activation) {
						$promotional_code_activation_txt = trans('customerController.promo_on');
					} else {
						$promotional_code_activation_txt = trans('customerController.promo_off');
					}
					/* SEND REFERRAL & PROMO INFO */

					// se o usuario tem requisicoes pendentes e ja cancela o fluxo
					if (Requests::UserHasPendings($user_data->id)) {
						$response_array = array('success' => false, 'error' => trans('customerController.previous_request_pending'), 'error_messages' => array(trans('customerController.previous_request_pending')), 'error_code' => 419);

						$response_code = 200;

						$response = Response::json($response_array, $response_code);
						return $response;
					} 

					/* SEND REFERRAL & PROMO INFO */
					if ($payment_opt == 0) {
						$card_count = Payment::where('user_id', '=', $user_id)->where('is_active', '=', 1)->count();
						if ($card_count <= 0) {
							$response_array = array('success' => false, 'error' => trans('customerController.please_add_card'), 'error_messages' => array(trans('customerController.please_add_card')), 'error_code' => 420);
							$response_code = 200;
							$response = Response::json($response_array, $response_code);
							return $response;
						}
					}

					//verificar se o usuário possui alguma dívida
					if ($user_data->debt > 0) {
						$response_array = array('success' => false, 'error' => trans('customerController.you_already_in') . " \$$user_data->debt debt", 'error_messages' => array(trans('customerController.you_already_in') . " \$$user_data->debt debt"), 'error_code' => 424);
						$response_code = 200;
						$response = Response::json($response_array, $response_code);

						return $response;
					}

					
					$providerNearest = Provider::getNearest($latitude, $longitude, $type, $categoryId);

					// caso nao houver nenhum provedor disponivel alertar ao app
					if(!$providerNearest) { 

						// envia notificacao
						send_notifications($user_id, "user", trans('customerController.no') . "" . Config::get('app.generic_keywords.Provider') . "" . trans('customerController.found'), trans('customerController.no') . "" . Config::get('app.generic_keywords.Provider') . trans('customerController.found_service_current_area'));


						$response_array = array('success' => false, 'error' => trans('customerController.no') . "" . Config::get('app.generic_keywords.Provider') . trans('customerController.found_service_current_area'), 'error_messages' => array(trans('customerController.no') . "" . Config::get('app.generic_keywords.Provider') . trans('customerController.found_service_current_area')), 'error_code' => 415);

						$response_code = 200;

						return Response::json($response_array, $response_code);
					}

					
					$user = User::find($user_id);
					$user->latitude = $latitude;
					$user->longitude = $longitude;
					$user->save();

					$request = new Requests;
					$request->user_id = $user_id;
					$request->payment_mode = $payment_opt;
					$request->time_zone = $time_zone;
					$request->src_address = $src_address;
					$request->dest_address = $dest_address;
					
					// regras para codigo promocional. 
					#TODO validar e organizar
					if (Input::has('promo_code')) {
						$promo_code = Input::get('promo_code');
						$payment_mode = 0;
						$payment_mode = $payment_opt;

						$settings = Settings::where('key', 'promotional_code_activation')->first();
						$prom_act = $settings->value;
						if ($prom_act) {
							if ($payment_mode == 0) {
								$settings = Settings::where('key', 'get_promotional_profit_on_card_payment')->first();
								$prom_act_card = $settings->value;
								if ($prom_act_card) {
									if ($promos = PromoCodes::where('coupon_code', $promo_code)->where('uses', '>', 0)->where('state', '=', 1)->first()) {
										if ((date("Y-m-d H:i:s") >= date("Y-m-d H:i:s", strtotime(trim($promos->expiry)))) || (date("Y-m-d H:i:s") <= date("Y-m-d H:i:s", strtotime(trim($promos->start_date))))) {
											$response_array = array('success' => FALSE, 'error' => trans('customerController.promotional_not_available'), 'error_messages' => array(trans('customerController.promotional_not_available')), 'error_code' => 505);
											$response_code = 200;
											return Response::json($response_array, $response_code);
										} else {
											$promo_is_used = UserPromoUse::where('user_id', '=', $user_id)->where('code_id', '=', $promos->id)->count();
											if ($promo_is_used) {
												$response_array = array('success' => FALSE, 'error' => trans('customerController.promotional_already_used'), 'error_messages' => array(trans('customerController.promotional_already_used')), 'error_code' => 512);
												$response_code = 200;
												return Response::json($response_array, $response_code);
											} else {
												$promo_update_counter = PromoCodes::find($promos->id);
												$promo_update_counter->uses = $promo_update_counter->uses - 1;
												$promo_update_counter->save();

												$user_promo_entry = new UserPromoUse;
												$user_promo_entry->code_id = $promos->id;
												$user_promo_entry->user_id = $user_id;
												$user_promo_entry->save();

												$user = User::find($user_id);
												$user->promo_count = $user->promo_count + 1;
												$user->save();

												$request->promo_id = $promos->id;
												$request->promo_code = $promos->coupon_code;
											}
										}
									} else {
										$response_array = array('success' => FALSE, 'error' => trans('customerController.promotional_not_available'), 'error_messages' => array(trans('customerController.promotional_not_available')), 'error_code' => 505);
										$response_code = 200;
										return Response::json($response_array, $response_code);
									}
								} else {
									$response_array = array('success' => FALSE, 'error' => trans('customerController.promotion_not_card'), 'error_messages' => array(trans('customerController.promotion_not_card')), 'error_code' => 505);
									$response_code = 200;
									return Response::json($response_array, $response_code);
								}
							} else if (($payment_mode == 1)) {
								$settings = Settings::where('key', 'get_promotional_profit_on_cash_payment')->first();
								$prom_act_cash = $settings->value;
								if ($prom_act_cash) {
									if ($promos = PromoCodes::where('coupon_code', $promo_code)->where('uses', '>', 0)->where('state', '=', 1)->first()) {
										if ((date("Y-m-d H:i:s") >= date("Y-m-d H:i:s", strtotime(trim($promos->expiry)))) || (date("Y-m-d H:i:s") <= date("Y-m-d H:i:s", strtotime(trim($promos->start_date))))) {
											$response_array = array('success' => FALSE, 'error' => trans('customerController.promotional_not_available'), 'error_messages' => array(trans('customerController.promotional_not_available')), 'error_code' => 505);
											$response_code = 200;
											return Response::json($response_array, $response_code);
										} else {
											$promo_is_used = UserPromoUse::where('user_id', '=', $user_id)->where('code_id', '=', $promos->id)->count();
											if ($promo_is_used) {
												$response_array = array('success' => FALSE, 'error' => trans('customerController.promotional_already_used'), 'error_messages' => array(trans('customerController.promotional_not_available')), 'error_code' => 512);
												$response_code = 200;
												return Response::json($response_array, $response_code);
											} else {
												$promo_update_counter = PromoCodes::find($promos->id);
												$promo_update_counter->uses = $promo_update_counter->uses - 1;
												$promo_update_counter->save();

												$user_promo_entry = new UserPromoUse;
												$user_promo_entry->code_id = $promos->id;
												$user_promo_entry->user_id = $user_id;
												$user_promo_entry->save();

												$user = User::find($user_id);
												$user->promo_count = $user->promo_count + 1;
												$user->save();

												$request->promo_id = $promos->id;
												$request->promo_code = $promos->coupon_code;
											}
										}
									} else {
										$response_array = array('success' => FALSE, 'error' => trans('customerController.promotional_not_available'), 'error_messages' => array(trans('customerController.promotional_not_available')), 'error_code' => 505);
										$response_code = 200;
										return Response::json($response_array, $response_code);
									}
								} else {
									$response_array = array('success' => FALSE, 'error' => trans('customerController.promotion_not_cash'), 'error_messages' => array(trans('customerController.promotion_not_cash')), 'error_code' => 505);
									$response_code = 200;
									return Response::json($response_array, $response_code);
								}
							} else if (($payment_mode == 2)) {
								$settings = Settings::where('key', 'get_promotional_profit_on_voucher_payment')->first();
								$prom_act_voucher = $settings->value;
								if ($prom_act_voucher) {
									if ($promos = PromoCodes::where('coupon_code', $promo_code)->where('uses', '>', 0)->where('state', '=', 1)->first()) {
										if ((date("Y-m-d H:i:s") >= date("Y-m-d H:i:s", strtotime(trim($promos->expiry)))) || (date("Y-m-d H:i:s") <= date("Y-m-d H:i:s", strtotime(trim($promos->start_date))))) {
											$response_array = array('success' => FALSE, 'error' => trans('customerController.promotional_not_available'), 'error_messages' => array(trans('customerController.promotional_not_available')), 'error_code' => 505);
											$response_code = 200;
											return Response::json($response_array, $response_code);
										} else {
											$promo_is_used = UserPromoUse::where('user_id', '=', $user_id)->where('code_id', '=', $promos->id)->count();
											if ($promo_is_used) {
												$response_array = array('success' => FALSE, 'error' => trans('customerController.promotional_already_used'), 'error_messages' => array(trans('customerController.promotional_not_available')), 'error_code' => 512);
												$response_code = 200;
												return Response::json($response_array, $response_code);
											} else {
												$promo_update_counter = PromoCodes::find($promos->id);
												$promo_update_counter->uses = $promo_update_counter->uses - 1;
												$promo_update_counter->save();

												$user_promo_entry = new UserPromoUse;
												$user_promo_entry->code_id = $promos->id;
												$user_promo_entry->user_id = $user_id;
												$user_promo_entry->save();

												$user = User::find($user_id);
												$user->promo_count = $user->promo_count + 1;
												$user->save();

												$request->promo_id = $promos->id;
												$request->promo_code = $promos->coupon_code;
											}
										}
									} else {
										$response_array = array('success' => FALSE, 'error' => trans('customerController.promotional_not_available'), 'error_messages' => array(trans('customerController.promotional_not_available')), 'error_code' => 505);
										$response_code = 200;
										return Response::json($response_array, $response_code);
									}
								} else {
									$response_array = array('success' => FALSE, 'error' => trans('customerController.promotion_not_cash'), 'error_messages' => array(trans('customerController.promotion_not_cash')), 'error_code' => 505);
									$response_code = 200;
									return Response::json($response_array, $response_code);
								}
							}
						} else {
							$response_array = array('success' => FALSE, 'error' => trans('customerController.promotion_not_active'), 'error_messages' => array(trans('customerController.promotion_not_active')), 'error_code' => 505);
							$response_code = 200;
							return Response::json($response_array, $response_code);
						}

					}

					$user_timezone = $user->timezone;
					$default_timezone = Config::get('app.timezone');
					$date_time = get_user_time($default_timezone, $user_timezone, date("Y-m-d H:i:s"));
					$request->D_latitude = 0;

					if (isset($d_latitude)) {
						$request->D_latitude = $d_latitude;
					}
					$request->D_longitude = 0;
					if (isset($d_longitude)) {
						$request->D_longitude = $d_longitude;
					}
					
					$request->request_start_time = $date_time;
					$request->latitude = $latitude;
					$request->longitude = $longitude;
					$request->req_create_user_time = $user_create_time;
					$request->current_provider = $providerNearest->id ;
					$request->save();

					$reqserv = new RequestServices;
					$reqserv->request_id = $request->id;
					$reqserv->type = $type;
					$reqserv->save();

					// salva prestador corrente no meta , antes era utilizado para salvar as possibilidades
					$request_meta = new RequestMeta;
					$request_meta->request_id = $request->id;
					$request_meta->provider_id = $providerNearest->id;
					$request_meta->save();

					// save request option
					$requestOptions = $this->save_request_options($providerNearest->id, $request->id);

					$driver_data = array();
					$driver_data['unique_id'] 	= 1;
					$driver_data['id'] 			= $providerNearest->id;
					$driver_data['first_name'] 	= $providerNearest->first_name;
					$driver_data['last_name'] 	= $providerNearest->last_name;
					$driver_data['phone'] 		= $providerNearest->phone;
					$driver_data['picture'] 	= $providerNearest->picture;
					$driver_data['bio'] 		= $providerNearest->bio;
					$driver_data['latitude'] 	= $providerNearest->latitude;
					$driver_data['longitude'] 	= $providerNearest->longitude;
					$driver_data['type'] 		= $providerNearest->type;
					$driver_data['car_model'] 	= $providerNearest->car_model;
					$driver_data['car_number'] 	= $providerNearest->car_number;
					$driver_data['rating'] 		= $providerNearest->rate;
					$driver_data['num_rating'] 	= $providerNearest->rate_count;
				
					$time_left = Settings::getProviderTimeout();

					// Send Notification
				
					$msg_array = array();
					$msg_array['unique_id'] = 1;
					$msg_array['request_id'] = $request->id;
					$msg_array['time_left_to_respond'] = $time_left;


					$msg_array['payment_mode'] = $payment_opt;

					$user = User::find($user_id);
					$request_data = array();
					$request_data['user'] = array();
					$request_data['user']['name'] 		= $user->first_name . " " . $user->last_name;
					$request_data['user']['picture'] 	= $user->picture;
					$request_data['user']['phone'] 		= $user->phone;
					$request_data['user']['address'] 	= $user->address;
					$request_data['user']['latitude'] 	= $request->latitude;
					$request_data['user']['longitude'] 	= $request->longitude;

					if ($d_latitude != NULL) {
						$request_data['user']['d_latitude'] 	= $d_latitude;
						$request_data['user']['d_longitude'] 	= $d_longitude;
					}

					$request_data['user']['user_dist_lat'] 	= $request->D_latitude;
					$request_data['user']['user_dist_long'] = $request->D_longitude;
					$request_data['user']['src_address'] 	= $request->src_address;
					$request_data['user']['dest_address'] 	= $request->dest_address;
					$request_data['user']['payment_type'] 	= $payment_opt;
					$request_data['user']['rating'] 		= $user->rate;
					$request_data['user']['num_rating'] 	= $user->rate_count;

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

					$msg_array['request_data'] = $request_data;

					$title = trans('customerController.new_request');
					$message = $msg_array;
					
					/* don't do json_encode in above line because if */
					send_notifications($providerNearest->id, "provider", $title, $message);
					
					// Send SMS 
					$settings = Settings::where('key', 'sms_request_created')->first();
					$pattern = $settings->value;
					$pattern = str_replace('%user%', $user_data->first_name . " " . $user_data->last_name, $pattern);
					$pattern = str_replace('%id%', $request->id, $pattern);
					$pattern = str_replace('%user_mobile%', $user_data->phone, $pattern);

					sms_notification(1, 'admin', $pattern);

					// send email
					$settings = Settings::where('key', 'admin_email_address')->first();
					$admin_email = $settings->value;
					$follow_url = web_url() . "/user/signin";
					$pattern = array('admin_eamil' => $admin_email, 'trip_id' => $request->id, 'follow_url' => $follow_url);
					$subject = trans('customerController.ride_booking_request');

					email_notification(1, 'admin', $pattern, $subject, 'new_request', null);

					$user = array();
					$user['user_lat'] = $request->latitude;
					$user['user_long'] = $request->longitude;

					$response_array = array(
						'success' => true,
						'unique_id' => 1,
						'is_referral_active' => $referral_code_activation,
						'is_referral_active_txt' => $referral_code_activation_txt,
						'is_promo_active' => $promotional_code_activation,
						'is_promo_active_txt' => $promotional_code_activation_txt,
						'request_id' => $request->id,
						'dest_latitude' => $request->D_latitude,
						'dest_longitude' => $request->D_longitude,
						'provider' => $driver_data,
						'user' => $user
					);

					$response_code = 200;
				
				} else {
					$response_array = array('success' => false, 'error' => trans('customerController.token_expired'), 'error_messages' => array(trans('customerController.token_expired')), 'error_code' => 405);
					$response_code = 200;
				}
			} 
			else { // falha ao obter o usuario
				if ($is_admin) {
					$response_array = array('success' => false, 'error' => "" . Config::get('app.generic_keywords.User') . "" . trans('customerController.id_not_found'), 'error_messages' => array('' . Config::get('app.generic_keywords.User') . "" . trans('customerController.id_not_found')), 'error_code' => 410);
				} else {
					$response_array = array('success' => false, 'error' => trans('customerController.token_not_valid'), 'error_messages' => array(trans('customerController.token_not_valid')), 'error_code' => 406);
				}
				$response_code = 200;
			}
		}

		$response = Response::json($response_array, $response_code);
		return $response;

	}

	// save request options if exists
	public function save_request_options($provider_id, $request_id){
		$type_id 				= Input::get('type');
		$category_id 			= Input::get('category_id');
		$vehicle_observations 	= Input::get('vehicle_observations');
		$vehicle_plate 			= Input::get('vehicle_plate');
		$vehicle_brand 			= Input::get('vehicle_brand');

		if($type_id && $category_id){
			$providerService = ProviderServices::findRecursive($provider_id, $type_id, $category_id);

			if($providerService){
				$requestOptions 						= new RequestOptions ;
				$requestOptions->request_id  			= $request_id ;
				$requestOptions->provider_service_id 	= $providerService->id ;
				$requestOptions->vehicle_observations	= $vehicle_observations ;
				$requestOptions->vehicle_plate  		= $vehicle_plate ;
				$requestOptions->vehicle_brand  		= $vehicle_brand ;
				$requestOptions->save();

				return $requestOptions ;
			}
		}

		return null ;

	}

	//create crequest with fare
	public function create_request_fare() {
		$token = Input::get('token');
		$user_id = Input::get('id');
		$latitude = Input::get('latitude');
		$longitude = Input::get('longitude');
		$distance = Input::get('distance');
		$time = Input::get('time');
		$validator = Validator::make(
						array(
					trans('customerController.token') => $token,
					trans('customerController.user_id') => $user_id,
					trans('customerController.latitude') => $latitude,
					trans('customerController.longitude') => $longitude,
						), array(
					trans('customerController.token') => 'required',
					trans('customerController.user_id') => 'required|integer',
					trans('customerController.latitude') => 'required',
					trans('customerController.longitude') => 'required',
						), array(
					trans('customerController.token') => '',
					trans('customerController.user_id') => trans('customerController.unique_id_missing'),
					trans('customerController.latitude') => trans('customerController.location_point_missing'),
					trans('customerController.longitude') => trans('customerController.location_point_missing'),
						)
		);

		/* $var = Keywords::where('id', 2)->first(); */

		if ($validator->fails()) {
			$error_messages = $validator->messages()->all();
			$response_array = array('success' => false, 'error' => trans('customerController.invalid_input'), 'error_code' => 401, 'error_messages' => $error_messages);
			$response_code = 200;
		} else {
			$is_admin = $this->isAdmin($token);

			if ($user_data = $this->getUserData($user_id, $token, $is_admin)) {
				// check for token validity
				if (is_token_active($user_data->token_expiry) || $is_admin) {
					// Do necessary operations

					if ($user_data->debt > 0) {
						$response_array = array('success' => false, 'error' => trans('customerController.you_already_in') . " \$$user->debt " . trans('customerController.debt'), 'error_messages' => array(trans('customerController.you_already_in') . " \$$user->debt " . trans('customerController.debt')), 'error_code' => 417);
						$response_code = 200;
						$response = Response::json($response_array, $response_code);
						return $response;
					}

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
						$typequery = "SELECT distinct provider_id from provider_services where type IN($type)";
						$typeproviders = DB::select(DB::raw($typequery));

						//Log::info('typeproviders = ' . print_r($typeproviders, true));

						if (count($typeproviders) > 0) {

							foreach ($typeproviders as $key) {

								$types[] = $key->provider_id;
							}

							$typestring = implode(",", $types);
							//Log::info('typestring = ' . print_r($typestring, true));
						} else {
							/* $var = Keywords::where('id', 1)->first();
							  $response_array = array('success' => false, 'error' => trans('customerController.no') . "" . $var->keyword . "" . trans('customerController.found_match_service_type'),'error_messages' => array(trans('customerController.no') . "" . $var->keyword . "" . trans('customerController.found_match_service_type')), 'error_code' => 405); */
							$response_array = array('success' => false, 'error' => trans('customerController.no') . "" . Config::get('app.generic_keywords.Provider') . "" . trans('customerController.found_match_service_type'), 'error_messages' => array(trans('customerController.no') . "" . Config::get('app.generic_keywords.Provider') . "" . trans('customerController.found_match_service_type')), 'error_code' => 405);
							$response_code = 200;
							return Response::json($response_array, $response_code);
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
						$query = "SELECT provider.id, ROUND(" . $multiply . " * 3956 * acos( cos( radians('$latitude') ) * cos( radians(latitude) ) * cos( radians(longitude) - radians('$longitude') ) + sin( radians('$latitude') ) * sin( radians(latitude) ) ) ,8) as distance from provider where is_available = 1 and is_active = 1 and is_approved = 1 and ROUND((" . $multiply . " * 3956 * acos( cos( radians('$latitude') ) * cos( radians(latitude) ) * cos( radians(longitude) - radians('$longitude') ) + sin( radians('$latitude') ) * sin( radians(latitude) ) ) ) ,8) <= $distance and provider.id IN($typestring) order by distance";

						$providers = DB::select(DB::raw($query));
						$provider_list = array();

						$user = User::find($user_id);
						$user->latitude = $latitude;
						$user->longitude = $longitude;
						$user->save();

						$request = new Requests;
						$request->user_id = $user_id;
						$request->request_start_time = date("Y-m-d H:i:s");
						$request->latitude = $latitude;
						$request->longitude = $longitude;
						
						//teste
						//$request->distance = 10;


						$request->save();

						$reqserv = new RequestServices;
						$reqserv->request_id = $request->id;
						$reqserv->type = $type;
						$reqserv->save();
					} else {
						$settings = Settings::where('key', 'default_search_radius')->first();
						$distance = $settings->value;
						$settings = Settings::where('key', 'default_distance_unit')->first();
						$unit = $settings->value;
						if ($unit == 0) {
							$multiply = 1.609344;
						} elseif ($unit == 1) {
							$multiply = 1;
						}
						$query = "SELECT provider.id, ROUND(" . $multiply . " * 3956 * acos( cos( radians('$latitude') ) * cos( radians(latitude) ) * cos( radians(longitude) - radians('$longitude') ) + sin( radians('$latitude') ) * sin( radians(latitude) ) ) ,8) as distance from provider where is_available = 1 and is_active = 1 and is_approved = 1 and ROUND((" . $multiply . " * 3956 * acos( cos( radians('$latitude') ) * cos( radians(latitude) ) * cos( radians(longitude) - radians('$longitude') ) + sin( radians('$latitude') ) * sin( radians(latitude) ) ) ) ,8) <= $distance order by distance";
						$providers = DB::select(DB::raw($query));
						$provider_list = array();

						$user = User::find($user_id);
						$user->latitude = $latitude;
						$user->longitude = $longitude;
						$user->save();

						$request = new Requests;
						$request->user_id = $user_id;
						$request->request_start_time = date("Y-m-d H:i:s");
						$request->latitude = $latitude;
						$request->longitude = $longitude;
						$request->save();

						$reqserv = new RequestServices;
						$reqserv->request_id = $request->id;
						$reqserv->save();
					}
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
					$req->save();

					$settings = Settings::where('key', 'provider_timeout')->first();
					$time_left = $settings->value;

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
						$request_data['user']['rating'] = $user->rate;
						$request_data['user']['num_rating'] = $user->rate_count;
						/* $request_data['user']['rating'] = DB::table('review_user')->where('user_id', '=', $user->id)->avg('rating') ? : 0;
						  $request_data['user']['num_rating'] = DB::table('review_user')->where('user_id', '=', $user->id)->count(); */

						$msg_array['request_data'] = $request_data;

						$title = trans('customerController.new_request');
						$message = $msg_array;
						//Log::info('first_provider_id = ' . print_r($first_provider_id, true));
						//Log::info('New request = ' . print_r($message, true));
						/* don't do json_encode in above line because if */
						send_notifications($first_provider_id, "provider", $title, $message);
					}

					$pt = ProviderServices::where('provider_id', $first_provider_id)->get();

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
					$subject = trans('customerController.ride_booking_request');
					email_notification(1, 'admin', $pattern, $subject, 'new_request', null);

					$response_array = array(
						'success' => true,
						'request_id' => $request->id,
					);
					$response_code = 200;
				} else {
					$response_array = array('success' => false, 'error' => trans('customerController.token_expired'), 'error_messages' => array(trans('customerController.token_expired')), 'error_code' => 405);
					$response_code = 200;
				}
			} else {
				if ($is_admin) {
					/* $response_array = array('success' => false, 'error' => "" . $var->keyword . "" . trans('customerController.id_not_found'),'error_messages' => array('' . $var->keyword . "" . trans('customerController.id_not_found')), 'error_code' => 410); */
					$response_array = array('success' => false, 'error' => "" . Config::get('app.generic_keywords.User') . "" . trans('customerController.id_not_found'), 'error_messages' => array('' . Config::get('app.generic_keywords.User') . "" . trans('customerController.id_not_found')), 'error_code' => 410);
				} else {
					$response_array = array('success' => false, 'error' => trans('customerController.token_not_valid'), 'error_messages' => array(trans('customerController.token_not_valid')), 'error_code' => 406);
				}
				$response_code = 200;
			}
		}

		$response = Response::json($response_array, $response_code);
		return $response;
	}

	//fare calculator

	public function fare_calculator() {

		if (Request::isMethod('post')) {
			$token = Input::get('token');
			$user_id = Input::get('id');
			$distance = Input::get('distance');
			$time = Input::get('time');

			$validator = Validator::make(
							array(
						trans('customerController.token') => $token,
						 trans('customerController.user_id')  => $user_id,
						trans('customerController.distance') => $distance,
						trans('customerController.time') => $time,
							), array(
						trans('customerController.token') => 'required',
						 trans('customerController.user_id')  => 'required|integer',
						trans('customerController.distance') => 'required',
						trans('customerController.time') => 'required',
							), array(
						trans('customerController.token') => '',
						 trans('customerController.user_id')  => trans('customerController.unique_id_missing'),
						trans('customerController.distance') => trans('customerController.distance_required'),
						trans('customerController.time') => trans('customerController.time_required'),
							)
			);

			/* $var = Keywords::where('id', 2)->first(); */

			if ($validator->fails()) {
				$error_messages = $validator->messages()->all();
				$response_array = array('success' => false, 'error' => trans('customerController.invalid_input'), 'error_code' => 401, 'error_messages' => $error_messages);
				$response_code = 200;
			} else {
				$is_admin = $this->isAdmin($token);

				if ($user_data = $this->getUserData($user_id, $token, $is_admin)) {
					// check for token validity
					if (is_token_active($user_data->token_expiry) || $is_admin) {
						$request_typ = ProviderType::where('is_default', '=', 1)->first();

						$setbase_distance = $request_typ->base_distance;
						$base_price1 = $request_typ->base_price;
						$price_per_unit_distance1 = $request_typ->price_per_unit_distance;
						$price_per_unit_time1 = $request_typ->price_per_unit_time;
						// Do necessary operations

						$settings = Settings::where('key', 'default_distance_unit')->first();
						$unit = $settings->value;

						/* $setbase_price = Settings::where('key', 'base_price')->first();
						  $base_price = $setbase_price->value; */
						if ($unit == 0) {
							$distanceKm = $distance * 0.001;
							/* $setdistance_price = Settings::where('key', 'price_per_unit_distance')->first();
							  $price_per_unit_distance = $setdistance_price->value * $distanceKm;
							 */
							if ($distanceKm <= $setbase_distance) {
								$price_per_unit_distance = 0;
							} else {
								$price_per_unit_distance = $price_per_unit_distance1 * ($distanceKm - $setbase_distance);
							}
						} else {
							$distanceMiles = $distance * 0.000621371;
							/* $setdistance_price = Settings::where('key', 'price_per_unit_distance')->first();
							  $price_per_unit_distance = $setdistance_price->value * $distanceMiles; */
							if ($distanceMiles <= $setbase_distance) {
								$price_per_unit_distance = 0;
							} else {
								$price_per_unit_distance = $price_per_unit_distance1 * ($distanceMiles - $setbase_distance);
							}
						}
						$timeMinutes = $time * 0.0166667;
						/* $settime_price = Settings::where('key', 'price_per_unit_time')->first();
						  $price_per_unit_time = $settime_price->value * $timeMinutes; */
						$price_per_unit_time = $price_per_unit_time1 * $timeMinutes;

						/* $total = $base_price + $price_per_unit_distance + $price_per_unit_time; */
						$total = $base_price1 + $price_per_unit_distance + $price_per_unit_time;

						$total = $total;

						/* $currency_selected = Keywords::find(5);
						  $cur_symb = $currency_selected->keyword; */
						$cur_symb = Config::get('app.generic_keywords.Currency');
						$response_array = array(
							'success' => true,
							'setbase_distance' => $setbase_distance,
							'base_price' => currency_converted($base_price1),
							'price_per_unit_distance' => currency_converted($price_per_unit_distance1),
							'price_per_unit_time' => currency_converted($price_per_unit_time1),
							'estimated_fare' => ceil(currency_converted($total)),
							'currency' => $cur_symb,
						);
						$response_code = 200;
					} else {
						$response_array = array('success' => false, 'error' => trans('customerController.token_expired'), 'error_messages' => array(trans('customerController.token_expired')), 'error_code' => 405);
						$response_code = 200;
					}
				} else {
					if ($is_admin) {
						/* $response_array = array('success' => false, 'error' => "" . $var->keyword . "" . trans('customerController.id_not_found'),'error_messages' => array('' . $var->keyword . "" . trans('customerController.id_not_found')), 'error_code' => 410); */
						$response_array = array('success' => false, 'error' => "" . Config::get('app.generic_keywords.User') . "" . trans('customerController.id_not_found'), 'error_messages' => array('' . Config::get('app.generic_keywords.User') . "" . trans('customerController.id_not_found')), 'error_code' => 410);
					} else {
						$response_array = array('success' => false, 'error' => trans('customerController.token_not_valid'), 'error_messages' => array(trans('customerController.token_not_valid')), 'error_code' => 406);
					}
					$response_code = 200;
				}
			}
		}
		$response = Response::json($response_array, $response_code);
		return $response;
	}

	// Get cancel request

	public function cancel_request() {

		$request_id = Input::get('request_id');
		$token = Input::get('token');
		$user_id = Input::get('id');

		$validator = Validator::make(
						array(
					trans('customerController.request_id') => $request_id,
					trans('customerController.token') => $token,
					trans('customerController.user_id') => $user_id,
						), array(
					trans('customerController.request_id') => 'required|integer',
					trans('customerController.token') => 'required',
					trans('customerController.user_id') => 'required|integer',
						), array(
					trans('customerController.request_id') => trans('customerController.id_request_required'),
					trans('customerController.token') => '',
					trans('customerController.user_id') => trans('customerController.unique_id_missing'),
						)
		);

		if ($validator->fails()) {
			$error_messages = $validator->messages()->all();
			$response_array = array('success' => false, 'error' => trans('customerController.invalid_input'), 'error_code' => 401, 'error_messages' => $error_messages);
			$response_code = 200;
		}
		else {
			$is_admin = $this->isAdmin($token);

			if ($user_data = $this->getUserData($user_id, $token, $is_admin)) {
				// check for token validity
				if (is_token_active($user_data->token_expiry) || $is_admin) {
					// Do necessary operations
					if ($request = Requests::find($request_id)) {

						if ($request->user_id == $user_data->id) {

							//cancela requisição
							Requests::where('id', $request_id)->update(array('is_cancelled' => 1));
							RequestMeta::where('request_id', $request_id)->update(array('is_cancelled' => 1));

							//atualiza contagem de cupons de desconto utilizados
							if ($request->promo_id) {
								$promo_update_counter = PromoCodes::find($request->promo_id);
								$promo_update_counter->uses = $promo_update_counter->uses + 1;
								$promo_update_counter->save();

								UserPromoUse::where('user_id', '=', $user_id)->where('code_id', '=', $request->promo_id)->delete();

								$user = User::find($user_id);
								$user->promo_count = $user->promo_count - 1;
								$user->save();

								$request = Requests::find($request_id);
								$request->promo_id = 0;
								$request->promo_code = "";
								$request->save();
							}

							//seta prestador como disponivel
							if ($request->confirmed_provider) {
								$provider = Provider::find($request->confirmed_provider);
								$provider->is_available = 1;
								$provider->save();
							}

							//obter tempo maximo de cancelamento sem pagamento de taxa das configuraçoes
							$settings = Settings::where('key', 'cancel_maximum_trip_time')->first();
							if($settings){
								$cancel_max_minutes = $settings->value;
							}
							else{
								$cancel_max_minutes = 5;
							}

							//cobrar taxa de cancelamento caso tenha se passado tempo maximo desde que o prestador aceitou a requisição
							$dateTimeMinutes = new DateTime(sprintf("- %d minutes", $cancel_max_minutes));
							$dateTimeProviderAcceptance = new DateTime($request->provider_acceptance_time);

							if($dateTimeProviderAcceptance < $dateTimeMinutes){

								RequestCharging::request_charge_cancel_fee($request->id);

								$request = $request::find($request_id);

								//enviar notificacao para o cliente
								if($request->payment_mode == RequestCharging::PAYMENT_MODE_CARD && $request->is_cancel_fee_paid == 1){
									$title = trans('customerController.cancel_fee_charged');
									send_notifications($request->user_id, "user", $title, null);
								}
							}
							
							//envia notificacao para prestador
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
								$request_data['user']['rating'] = $user->rate;
								$request_data['user']['num_rating'] = $user->rate_count;

								$msg_array['request_data'] = $request_data;

								$title = trans('customerController.request_cancelled');
								$message = $msg_array;
								send_notifications($request->current_provider, "provider", $title, $message);
							}
							$response_array = array(
								'success' => true,
							);

							$response_code = 200;
						} else {
							$response_array = array('success' => false, 'error' => trans('customerController.request_id_doesnot_match') . Config::get('app.generic_keywords.User') . "" . trans('customerController.id'), 'error_messages' => array(trans('customerController.request_id_doesnot_match') . Config::get('app.generic_keywords.User') . "" . trans('customerController.id')), 'error_code' => 407);
							$response_code = 200;
						}
					} else {
						$response_array = array('success' => false, 'error' => trans('customerController.request_id_not_found'), 'error_messages' => array(trans('customerController.request_id_not_found')), 'error_code' => 408);
						$response_code = 200;
					}
				} else {
					$response_array = array('success' => false, 'error' => trans('customerController.token_expired'), 'error_messages' => array(trans('customerController.token_expired')), 'error_code' => 405);
					$response_code = 200;
				}
			} else {
				if ($is_admin) {
					$response_array = array('success' => false, 'error' => "" . Config::get('app.generic_keywords.User') . "" . trans('customerController.id_not_found'), 'error_messages' => array('' . Config::get('app.generic_keywords.User') . "" . trans('customerController.id_not_found')), 'error_code' => 410);
				} else {
					$response_array = array('success' => false, 'error' => trans('customerController.token_not_valid'), 'error_messages' => array(trans('customerController.token_not_valid')), 'error_code' => 406);
				}
				$response_code = 200;
			}
		}


		$response = Response::json($response_array, $response_code);
		return $response;
	}

	// Get Request Status

	public function get_running_request() {
		$user_id = Input::get('id');
		$token = Input::get('token');
		$validator = Validator::make(
						array(
					trans('customerController.token') => $token,
					trans('customerController.user_id')  => $user_id,
						), array(
					trans('customerController.token') => 'required',
					trans('customerController.user_id')  => 'required|integer',
						), array(
					trans('customerController.token') => '',
					trans('customerController.user_id')  => trans('customerController.unique_id_missing'),
						)
		);
		if ($validator->fails()) {
			$error_messages = $validator->messages()->all();
			$response_array = array('success' => false, 'error' => trans('customerController.invalid_input'), 'error_code' => 401, 'error_messages' => $error_messages);
			$response_code = 200;
		} else {
			$is_admin = $this->isAdmin($token);
			$request_data = "";
			if ($user_data = $this->getUserData($user_id, $token, $is_admin)) {
				/* SEND REFERRAL & PROMO INFO */
				$settings = Settings::where('key', 'referral_code_activation')->first();
				$referral_code_activation = $settings->value;
				if ($referral_code_activation) {
					$referral_code_activation_txt = trans('customerController.referral_on');
				} else {
					$referral_code_activation_txt = trans('customerController.referral_off');
				}
				$settings = Settings::where('key', 'promotional_code_activation')->first();
				$promotional_code_activation = $settings->value;
				if ($promotional_code_activation) {
					$promotional_code_activation_txt = trans('customerController.promo_on');
				} else {
					$promotional_code_activation_txt = trans('customerController.promo_off');
				}
				/* SEND REFERRAL & PROMO INFO */
				// check for token validity
				if (is_token_active($user_data->token_expiry) || $is_admin) {
					$running_requests = Requests::select('request.id as request_id', 'request.D_latitude as dest_latitude', 'request.D_longitude as dest_longitude', 'request.latitude as src_latitude', 'request.longitude as src_longitude', 'user.id as user_id', 'user.first_name as user_first_name', 'user.last_name as user_last_name', 'user.phone as user_phone', 'user.email as user_email', 'user.picture as user_picture', 'user.bio as user_bio', 'user.address as user_address', 'user.state as user_state', 'user.country as user_country', 'user.zipcode as user_zipcode', 'user.rate as user_rate', 'user.rate_count as user_rate_count', 'provider.id as provider_id', 'provider.first_name as provider_first_name', 'provider.last_name as provider_last_name', 'provider.phone as provider_phone', 'provider.email as provider_email', 'provider.picture as provider_picture', 'provider.bio as provider_bio', 'provider.address as provider_address', 'provider.state as provider_state', 'provider.country as provider_country', 'provider.zipcode as provider_zipcode', 'provider.latitude as provider_latitude', 'provider.longitude as provider_longitude', 'provider.type as provider_type', 'provider.car_model as provider_car_model', 'provider.car_number as provider_car_number', 'provider.rate as provider_rate', 'provider.rate_count as provider_rate_count', 'provider.bearing as bearing')
							->leftJoin('user', 'request.user_id', '=', 'user.id')
							->leftJoin('provider', 'request.current_provider', '=', 'provider.id')
							->leftJoin('provider_type', 'provider.type', '=', 'provider_type.id')
							->where('request.user_id', '=', $user_id)
							->where('request.is_cancelled', '=', 0)
							->where('request.current_provider', '>', 0)
							->where('request.is_provider_rated', '=', 0)
							->orderBy('request.id', 'DESC')
							->get();
					$request_data = array();
					foreach ($running_requests as $requests) {
						$data['request_id'] = $requests->request_id;
						/* $data['user']['user_id'] = $requests->user_id;
						  $data['user']['user_lat'] = $requests->src_latitude;
						  $data['user']['latitude'] = $requests->src_latitude;
						  $data['user']['user_long'] = $requests->src_longitude;
						  $data['user']['longitude'] = $requests->src_longitude;
						  $data['user']['user_dist_lat'] = $requests->dest_latitude;
						  $data['user']['d_latitude'] = $requests->dest_latitude;
						  $data['user']['user_dist_long'] = $requests->dest_longitude;
						  $data['user']['d_longitude'] = $requests->dest_longitude;
						  $data['user']['first_name'] = $requests->user_first_name;
						  $data['user']['last_name'] = $requests->user_last_name;
						  $data['user']['phone'] = $requests->user_phone;
						  $data['user']['email'] = $requests->user_email;
						  $data['user']['picture'] = $requests->user_picture;
						  $data['user']['bio'] = $requests->user_bio;
						  $data['user']['address'] = $requests->user_address;
						  $data['user']['state'] = $requests->user_state;
						  $data['user']['country'] = $requests->user_country;
						  $data['user']['zipcode'] = $requests->user_zipcode;
						  $data['user']['rating'] = $requests->user_rate;
						  $data['user']['num_rating'] = $requests->user_rate_count; */
						$data['provider']['id'] = $requests->provider_id;
						$data['provider']['first_name'] = $requests->provider_first_name;
						$data['provider']['last_name'] = $requests->provider_last_name;
						$data['provider']['phone'] = $requests->provider_phone;
						$data['provider']['email'] = $requests->provider_email;
						$data['provider']['picture'] = $requests->provider_picture;
						$data['provider']['bio'] = $requests->provider_bio;
						$data['provider']['address'] = $requests->provider_address;
						$data['provider']['state'] = $requests->provider_state;
						$data['provider']['country'] = $requests->provider_country;
						$data['provider']['zipcode'] = $requests->provider_zipcode;
						$data['provider']['latitude'] = $requests->provider_latitude;
						$data['provider']['longitude'] = $requests->provider_longitude;
						$data['provider']['type'] = $requests->provider_type;
						$data['provider']['rating'] = $requests->provider_rate;
						$data['provider']['num_rating'] = $requests->provider_rate_count;
						$data['provider']['car_model'] = $requests->provider_car_model;
						$data['provider']['car_number'] = $requests->provider_car_number;
						$data['provider']['bearing'] = $requests->bearing;
						array_push($request_data, $data);
					}

					if (!empty($request_data)) {
						$response_array = array(
							'success' => true,
							'is_referral_active' => $referral_code_activation,
							'is_referral_active_txt' => $referral_code_activation_txt,
							'is_promo_active' => $promotional_code_activation,
							'is_promo_active_txt' => $promotional_code_activation_txt,
							'requests' => $request_data,
							'error_code' => 502,
							/* 'error' => trans('customerController.searching_for') . "" . $driver->keyword . 's.', */
							'error_messages' => array(trans('customerController.on_going1') . "" . Config::get('app.generic_keywords.Trip') . trans('customerController.on_going2') . '.'),
							'error' => trans('customerController.on_going1') . "" . Config::get('app.generic_keywords.Trip') . trans('customerController.on_going2') . '.',
						);
					} else {
						$response_array = array(
							'success' => false,
							'is_referral_active' => $referral_code_activation,
							'is_referral_active_txt' => $referral_code_activation_txt,
							'is_promo_active' => $promotional_code_activation,
							'is_promo_active_txt' => $promotional_code_activation_txt,
							'requests' => $request_data,
							'error_code' => 584,
							/* 'error' => trans('customerController.searching_for') . "" . $driver->keyword . 's.', */
							'error_messages' => array(trans('customerController.no_on_going1') . "" . Config::get('app.generic_keywords.Trip') . trans('customerController.no_on_going2') . '.'),
							'error' => trans('customerController.no_on_going1') . "" . Config::get('app.generic_keywords.Trip') . trans('customerController.no_on_going2') . '.',
						);
					}
					$response_code = 200;
				} else {
					$response_array = array('success' => false, 'error' => trans('customerController.token_expired'), 'error_messages' => array(trans('customerController.token_expired')), 'error_code' => 405);
					$response_code = 200;
				}
			} else {
				if ($is_admin) {
					/* $response_array = array('success' => false, 'error' => "" . $var->keyword . "" . trans('customerController.id_not_found'),'error_messages' => array('' . $var->keyword . "" . trans('customerController.id_not_found')), 'error_code' => 410); */
					$response_array = array('success' => false, 'error' => "" . Config::get('app.generic_keywords.User') . "" . trans('customerController.id_not_found'), 'error_messages' => array('' . Config::get('app.generic_keywords.User') . "" . trans('customerController.id_not_found')), 'error_code' => 410);
				} else {
					$response_array = array('success' => false, 'error' => trans('customerController.token_not_valid'), 'error_messages' => array(trans('customerController.token_not_valid')), 'error_code' => 406);
				}
				$response_code = 200;
			}
		}
		$response = Response::json($response_array, $response_code);
		return $response;
	}

	public function get_request() {

		$request_id = Input::get('request_id');
		$token = Input::get('token');
		$user_id = Input::get('id');

		$validator = Validator::make(
						array(
					trans('customerController.request_id') => $request_id,
					trans('customerController.token') => $token,
					trans('customerController.user_id') => $user_id,
						), array(
					trans('customerController.request_id') => 'required|integer',
					trans('customerController.token') => 'required',
					trans('customerController.user_id') => 'required|integer',
						), array(
					trans('customerController.request_id') => trans('customerController.id_request_required'),
					trans('customerController.token') => '',
					trans('customerController.user_id') => trans('customerController.unique_id_missing'),
						)
		);

		/* $var = Keywords::where('id', 2)->first(); */

		if ($validator->fails()) {
			$error_messages = $validator->messages()->all();
			$response_array = array('success' => false, 'error' => trans('customerController.invalid_input'), 'error_code' => 401, 'error_messages' => $error_messages);
			$response_code = 200;
		} else {
			$is_admin = $this->isAdmin($token);
			$provider_data = "";
			if ($user_data = $this->getUserData($user_id, $token, $is_admin)) {
				// check for token validity
				if (is_token_active($user_data->token_expiry) || $is_admin) {
					/* SEND REFERRAL & PROMO INFO */
					$settings = Settings::where('key', 'referral_code_activation')->first();
					$referral_code_activation = $settings->value;
					if ($referral_code_activation) {
						$referral_code_activation_txt = trans('customerController.referral_on');
					} else {
						$referral_code_activation_txt = trans('customerController.referral_off');
					}

					$settings = Settings::where('key', 'promotional_code_activation')->first();
					$promotional_code_activation = $settings->value;
					if ($promotional_code_activation) {
						$promotional_code_activation_txt = trans('customerController.promo_on');
					} else {
						$promotional_code_activation_txt = trans('customerController.promo_off');
					}
					/* SEND REFERRAL & PROMO INFO */
					// Do necessary operations
					if ($request = Requests::find($request_id)) {

						if ($request->user_id == $user_data->id) {
							if ($request->current_provider != 0) {

								if ($request->confirmed_provider != 0) {
									$provider = Provider::where('id', $request->confirmed_provider)->first();
									$provider_data = array();
									$provider_data['unique_id'] = 1;
									$provider_data['id'] = $provider->id;
									$provider_data['first_name'] = $provider->first_name;
									$provider_data['last_name'] = $provider->last_name;
									$provider_data['phone'] = $provider->phone;
									$provider_data['bio'] = $provider->bio;
									$provider_data['picture'] = $provider->picture;
									$provider_data['latitude'] = $provider->latitude;
									$provider_data['longitude'] = $provider->longitude;
									if ($request->D_latitude != NULL) {
										$provider_data['d_latitude'] = $request->D_latitude;
										$provider_data['d_longitude'] = $request->D_longitude;
									}
									
									$provider_data['rating'] = $provider->rate;
									$provider_data['num_rating'] = $provider->rate_count;
									$provider_data['car_model'] = $provider->car_model;
									$provider_data['car_number'] = $provider->car_number;
									$provider_data['bearing'] = $provider->bearing;
								
									$settings = Settings::where('key', 'default_distance_unit')->first();
									$unit = $settings->value;
									if ($unit == 0) {
										$unit_set = 'kms';
									} elseif ($unit == 1) {
										$unit_set = 'miles';
									}
									$requestserv = RequestServices::where('request_id', $request->id)->first();
									$bill = array();
									$request_typ = ProviderType::where('id', '=', $requestserv->type)->first();

									$provider_data['type'] = $request->Type;
									$provider_data['category'] = $request->Category;

									/* $currency_selected = Keywords::find(5);
									  $cur_symb = $currency_selected->keyword; */
									$cur_symb = Config::get('app.generic_keywords.Currency');

									if ($request->is_completed == 1) {
										$bill['unit'] = $unit_set;
										$bill['payment_mode'] = $request->payment_mode;
										$bill['distance'] = (string) $request->distance;
										//$bill['distance'] = '15';
										$bill['time'] = $request->time;

										if ($requestserv->base_price != 0) {
											$bill['base_distance'] = $request_typ->base_distance;
											$bill['base_price'] = currency_converted($requestserv->base_price);
											$bill['distance_cost'] = currency_converted($requestserv->distance_cost);

											//$bill['distance_cost'] = 10.00;

											$bill['time_cost'] = currency_converted($requestserv->time_cost);
										} else {
											
											$bill['base_distance'] = $request_typ->base_distance;
											$bill['base_price'] = currency_converted($request_typ->base_price);
											$bill['distance_cost'] = currency_converted($request_typ->price_per_unit_distance);
											$bill['time_cost'] = currency_converted($request_typ->price_per_unit_time);
										}
										$bill['price_per_unit_distance'] = currency_converted($request_typ->price_per_unit_distance);
										$bill['price_per_unit_time'] = currency_converted($request_typ->price_per_unit_time);
										if ($request->payment_mode == 2) {
											$bill['provider']['email'] = $provider->email;
											$bill['provider']['amount'] = currency_converted($request->transfer_amount);
											$admins = Admin::first();
											$bill['admin']['email'] = $admins->username;
											$bill['admin']['amount'] = currency_converted($request->total - $request->transfer_amount);
										}
										$bill['currency'] = $cur_symb;
										$bill['main_total'] = currency_converted($request->total);
										$bill['total'] = currency_converted($request->total - $request->ledger_payment - $request->promo_payment);
										$bill['referral_bonus'] = currency_converted($request->ledger_payment);
										$bill['promo_bonus'] = currency_converted($request->promo_payment);
										$bill['payment_type'] = $request->payment_mode;
										$bill['is_paid'] = $request->is_paid;
										$discount = 0;
										if ($request->promo_code != "") {
											if ($request->promo_code != "") {
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
										$bill['promo_discount'] = currency_converted($discount);
										$bill['actual_total'] = currency_converted($request->total + $request->ledger_payment + $discount);
									}
									$cards = "";
									/* $cards['none'] = ""; */
									$dif_card = 0;
									$cardlist = Payment::where('user_id', $user_id)->where('is_default', 1)->first();
					

									if (count($cardlist) >= 1) {
										$cards = array();
										$default = $cardlist->is_default;
										if ($default == 1) {
											$dif_card = $cardlist->id;
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

									$code_data = Ledger::where('user_id', '=', $user_data->id)->first();
									$user = array();
									$user['user_lat'] = $request->latitude;
									$user['user_long'] = $request->longitude;
									$user['user_dist_lat'] = $request->D_latitude;
									$user['user_dist_long'] = $request->D_longitude;
									$user['payment_type'] = $request->payment_mode;
									$user['default_card'] = $dif_card;
									$user['dest_latitude'] = $request->D_latitude;
									$user['dest_longitude'] = $request->D_longitude;
									$user['referral_code'] = $code_data->referral_code;
									$user['is_referee'] = $user_data->is_referee;
									$user['promo_count'] = $user_data->promo_count;



									$charge = array();

									$settings = Settings::where('key', 'default_distance_unit')->first();
									$unit = $settings->value;
									if ($unit == 0) {
										$unit_set = 'kms';
									} elseif ($unit == 1) {
										$unit_set = 'miles';
									}
									$charge['unit'] = $unit_set;


									if ($requestserv->base_price != 0) {
										$charge['base_distance'] = $request_typ->base_distance;
										$charge['base_price'] = currency_converted($requestserv->base_price);
										$charge['distance_price'] = currency_converted($requestserv->distance_cost);
										$charge['price_per_unit_time'] = currency_converted($requestserv->time_cost);
									} else {
										/* $setbase_price = Settings::where('key', 'base_price')->first();
										  $charge['base_price'] = currency_converted($setbase_price->value);
										  $setdistance_price = Settings::where('key', 'price_per_unit_distance')->first();
										  $charge['distance_price'] = currency_converted($setdistance_price->value);
										  $settime_price = Settings::where('key', 'price_per_unit_time')->first();
										  $charge['price_per_unit_time'] = currency_converted($settime_price->value); */
										$charge['base_distance'] = $request_typ->base_distance;
										$charge['base_price'] = currency_converted($request_typ->base_price);
										$charge['distance_price'] = currency_converted($request_typ->price_per_unit_distance);
										$charge['price_per_unit_time'] = currency_converted($request_typ->price_per_unit_time);
									}
									$charge['total'] = currency_converted($request->total);
									$charge['is_paid'] = $request->is_paid;

									$loc1 = RequestLocation::where('request_id', $request->id)->first();
									$loc2 = RequestLocation::where('request_id', $request->id)->orderBy('id', 'desc')->first();
									if ($loc1) {
										$time1 = strtotime($loc2->created_at);
										$time2 = strtotime($loc1->created_at);
										$difference = intval(($time1 - $time2) / 60);
									} else {
										$difference = 0;
									}
									$difference = $request->time;


									$rserv = RequestServices::where('request_id', $request_id)->get();
									$typs = array();
									$typi = array();
									$typp = array();
									$total_price = 0;
									foreach ($rserv as $typ) {
										$typ1 = ProviderType::where('id', $typ->type)->first();
										$typ_price = ProviderServices::where('provider_id', $request->confirmed_provider)->first();

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
										$total_price = $total_price + $typp1;
										array_push($typi, $typs);
									}
									$bill['type'] = $typi;

									$response_array = array(
										'success' => true,
										'unique_id' => 1,
										'status' => $request->status,
										'is_referral_active' => $referral_code_activation,
										'is_referral_active_txt' => $referral_code_activation_txt,
										'is_promo_active' => $promotional_code_activation,
										'is_promo_active_txt' => $promotional_code_activation_txt,
										'confirmed_provider' => $request->confirmed_provider,
										'is_provider_started' => $request->is_provider_started,
										'is_provider_arrived' => $request->is_provider_arrived,
										'is_request_started' => $request->is_started,
										'is_completed' => $request->is_completed,
										'is_provider_rated' => $request->is_provider_rated,
										'is_cancelled' => $request->is_cancelled,
										'dest_latitude' => $request->D_latitude,
										'dest_longitude' => $request->D_longitude,
										'promo_id' => $request->promo_id,
										'promo_code' => $request->promo_code,
										'provider' => $provider_data,
										'time' => $difference,
										'bill' => $bill,
										'user' => $user,
										'card_details' => $cards,
										'charge_details' => $charge,
										'promocode' =>  $request->promo_code,
									);

									$user_timezone = $provider->timezone;
									$default_timezone = Config::get('app.timezone');

									$accepted_time = get_user_time($default_timezone, $user_timezone, $request->request_start_time);

									$time = DB::table('request_location')
											->where('request_id', $request_id)
											->min('created_at');

									$end_time = get_user_time($default_timezone, $user_timezone, $time);

									$response_array['accepted_time'] = $accepted_time;
									if ($request->is_started == 1) {
										$response_array['start_time'] = DB::table('request_location')
												->where('request_id', $request_id)
												->min('created_at');

										$settings = Settings::where('key', 'default_distance_unit')->first();
										$unit = $settings->value;

										$response_array['distance'] = DB::table('request_location')
												->where('request_id', $request_id)
												->max('distance');

										$response_array['distance'] = (string) convert($response_array['distance'], $unit);
										if ($unit == 0) {
											$unit_set = 'kms';
										} elseif ($unit == 1) {
											$unit_set = 'miles';
										}
										$response_array['unit'] = $unit_set;
									}
									if ($request->is_completed == 1) {
										$response_array['end_time'] = $end_time;
									}
								} else {
									if ($request->current_provider != 0) {
										$provider = Provider::find($request->current_provider);
										$provider_services  = ProviderServices::where('provider_id', $provider->id)->first();

										$provider_data = array();
										$provider_data['unique_id'] = 1;
										$provider_data['id'] = $provider->id;
										$provider_data['first_name'] = $provider->first_name;
										$provider_data['last_name'] = $provider->last_name;
										$provider_data['phone'] = $provider->phone;
										$provider_data['bio'] = $provider->bio;
										$provider_data['picture'] = $provider->picture;
										$provider_data['latitude'] = $provider->latitude;
										$provider_data['longitude'] = $provider->longitude;
										$provider_data['type'] = $provider_services->type;
										$provider_data['car_model'] = $provider->car_model;
										$provider_data['car_number'] = $provider->car_number;
										$provider_data['bearing'] = $provider->bearing;
										// $provider_data['payment_type'] = $request->payment_mode;
										$provider_data['rating'] = $provider->rate;
										$provider_data['num_rating'] = $provider->rate_count;
									}
									$cards = "";
									/* $cards['none'] = ""; */
									$dif_card = 0;
									$cardlist = Payment::where('user_id', $user_id)->where('is_default', 1)->first();
									/* $cardlist = Payment::where('id', $user_data->default_card_id)->first(); */

									if (count($cardlist) >= 1) {
										$cards = array();
										$default = $cardlist->is_default;
										if ($default == 1) {
											$dif_card = $cardlist->id;
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
									$code_data = Ledger::where('user_id', '=', $user_data->id)->first();
									$user = array();
									$user['user_lat'] = $request->latitude;
									$user['user_long'] = $request->longitude;
									$user['user_dist_lat'] = $request->D_latitude;
									$user['user_dist_long'] = $request->D_longitude;
									$user['payment_type'] = $request->payment_mode;
									$user['default_card'] = $dif_card;
									$user['dest_latitude'] = $request->D_latitude;
									$user['dest_longitude'] = $request->D_longitude;
									$user['referral_code'] = $code_data->referral_code;
									$user['is_referee'] = $user_data->is_referee;
									$user['promo_count'] = $user_data->promo_count;
									/* $driver = Keywords::where('id', 1)->first(); */
									$requestserv = RequestServices::where('request_id', $request->id)->first();
									$charge = array();
									$request_typ = ProviderType::where('id', '=', $requestserv->type)->first();
									$settings = Settings::where('key', 'default_distance_unit')->first();
									$unit = $settings->value;
									if ($unit == 0) {
										$unit_set = 'kms';
									} elseif ($unit == 1) {
										$unit_set = 'miles';
									}
									$charge['unit'] = $unit_set;
									if ($requestserv->base_price != 0) {
										$charge['base_distance'] = $request_typ->base_distance;
										$charge['base_price'] = currency_converted($requestserv->base_price);
										$charge['distance_price'] = currency_converted($requestserv->distance_cost);
										$charge['price_per_unit_time'] = currency_converted($requestserv->time_cost);
									} else {
										/* $setbase_price = Settings::where('key', 'base_price')->first();
										  $charge['base_price'] = currency_converted($setbase_price->value);
										  $setdistance_price = Settings::where('key', 'price_per_unit_distance')->first();
										  $charge['distance_price'] = currency_converted($setdistance_price->value);
										  $settime_price = Settings::where('key', 'price_per_unit_time')->first();
										  $charge['price_per_unit_time'] = currency_converted($settime_price->value); */
										$charge['base_distance'] = $request_typ->base_distance;
										$charge['base_price'] = currency_converted($request_typ->base_price);
										$charge['distance_price'] = currency_converted($request_typ->price_per_unit_distance);
										$charge['price_per_unit_time'] = currency_converted($request_typ->price_per_unit_time);
									}
									$charge['total'] = currency_converted($request->total);
									$charge['is_paid'] = $request->is_paid;
									$response_array = array(
										'success' => true,
										'unique_id' => 1,
										'status' => $request->status,
										'is_referral_active' => $referral_code_activation,
										'is_referral_active_txt' => $referral_code_activation_txt,
										'is_promo_active' => $promotional_code_activation,
										'is_promo_active_txt' => $promotional_code_activation_txt,
										'confirmed_provider' => 0,
										'is_provider_started' => $request->is_provider_started,
										'is_provider_arrived' => $request->is_provider_arrived,
										'is_request_started' => $request->is_started,
										'is_completed' => $request->is_completed,
										'is_provider_rated' => $request->is_provider_rated,
										'is_cancelled' => $request->is_cancelled,
										'dest_latitude' => $request->D_latitude,
										'dest_longitude' => $request->D_longitude,
										'promo_id' => $request->promo_id,
										'promo_code' => $request->promo_code,
										'provider' => $provider_data,
										'bill' => "",
										'user' => $user,
										'card_details' => $cards,
										'charge_details' => $charge,
										'confirmed_provider' => 0,
										'promocode' =>  $request->promo_code,
										'error_code' => 484,
										/* 'error' => trans('customerController.searching_for') . "" . $driver->keyword . 's.', */
										'error' => trans('customerController.searching_for') . "" . Config::get('app.generic_keywords.Provider') . 's.',
										'error_messages' => array(trans('customerController.searching_for') . "" . Config::get('app.generic_keywords.Provider') . 's.'),
									);
								}
							} else {
								/* $driver = Keywords::where('id', 1)->first(); */
								if ($request->current_provider != 0) {
									$provider = Provider::find($request->current_provider);
									$provider_services  = ProviderServices::where('provider_id', $provider->id)->first();

									$provider_data = array();
									$provider_data['unique_id'] = 1;
									$provider_data['id'] = $provider->id;
									$provider_data['first_name'] = $provider->first_name;
									$provider_data['last_name'] = $provider->last_name;
									$provider_data['phone'] = $provider->phone;
									$provider_data['bio'] = $provider->bio;
									$provider_data['picture'] = $provider->picture;
									$provider_data['latitude'] = $provider->latitude;
									$provider_data['longitude'] = $provider->longitude;
									$provider_data['type'] = $provider_services->type;
									$provider_data['car_model'] = $provider->car_model;
									$provider_data['car_number'] = $provider->car_number;
									$provider_data['bearing'] = $provider->bearing;
									// $provider_data['payment_type'] = $request->payment_mode;
									$provider_data['rating'] = $provider->rate;
									$provider_data['num_rating'] = $provider->rate_count;
								}
								$cards = "";
								/* $cards['none'] = ""; */
								$dif_card = 0;
								$cardlist = Payment::where('user_id', $user_id)->where('is_default', 1)->first();
								/* $cardlist = Payment::where('id', $user_data->default_card_id)->first(); */

								if (count($cardlist) >= 1) {
									$cards = array();
									$default = $cardlist->is_default;
									if ($default == 1) {
										$dif_card = $cardlist->id;
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
								$code_data = Ledger::where('user_id', '=', $user_data->id)->first();
								$user = array();
								$user['user_lat'] = $request->latitude;
								$user['user_long'] = $request->longitude;
								$user['user_dist_lat'] = $request->D_latitude;
								$user['user_dist_long'] = $request->D_longitude;
								$user['payment_type'] = $request->payment_mode;
								$user['default_card'] = $dif_card;
								$user['dest_latitude'] = $request->D_latitude;
								$user['dest_longitude'] = $request->D_longitude;
								$user['referral_code'] = $code_data->referral_code;
								$user['is_referee'] = $user_data->is_referee;
								$user['promo_count'] = $user_data->promo_count;
								/* $driver = Keywords::where('id', 1)->first(); */
								$requestserv = RequestServices::where('request_id', $request->id)->first();
								$charge = array();
								$request_typ = ProviderType::where('id', '=', $requestserv->type)->first();
								$settings = Settings::where('key', 'default_distance_unit')->first();
								$unit = $settings->value;
								if ($unit == 0) {
									$unit_set = 'kms';
								} elseif ($unit == 1) {
									$unit_set = 'miles';
								}
								$charge['unit'] = $unit_set;
								if ($requestserv->base_price != 0) {
									$charge['base_distance'] = $request_typ->base_distance;
									$charge['base_price'] = currency_converted($requestserv->base_price);
									$charge['distance_price'] = currency_converted($requestserv->distance_cost);
									$charge['price_per_unit_time'] = currency_converted($requestserv->time_cost);
								} else {
									/* $setbase_price = Settings::where('key', 'base_price')->first();
									  $charge['base_price'] = currency_converted($setbase_price->value);
									  $setdistance_price = Settings::where('key', 'price_per_unit_distance')->first();
									  $charge['distance_price'] = currency_converted($setdistance_price->value);
									  $settime_price = Settings::where('key', 'price_per_unit_time')->first();
									  $charge['price_per_unit_time'] = currency_converted($settime_price->value); */
									$charge['base_distance'] = $request_typ->base_distance;
									$charge['base_price'] = currency_converted($request_typ->base_price);
									$charge['distance_price'] = currency_converted($request_typ->price_per_unit_distance);
									$charge['price_per_unit_time'] = currency_converted($request_typ->price_per_unit_time);
								}
								$charge['total'] = currency_converted($request->total);
								$charge['is_paid'] = $request->is_paid;
								$response_array = array(
									'success' => true,
									'unique_id' => 1,
									'status' => $request->status,
									'is_referral_active' => $referral_code_activation,
									'is_referral_active_txt' => $referral_code_activation_txt,
									'is_promo_active' => $promotional_code_activation,
									'is_promo_active_txt' => $promotional_code_activation_txt,
									'confirmed_provider' => 0,
									'is_provider_started' => $request->is_provider_started,
									'is_provider_arrived' => $request->is_provider_arrived,
									'is_request_started' => $request->is_started,
									'is_completed' => $request->is_completed,
									'is_provider_rated' => $request->is_provider_rated,
									'is_cancelled' => $request->is_cancelled,
									'dest_latitude' => $request->D_latitude,
									'dest_longitude' => $request->D_longitude,
									'promo_id' => $request->promo_id,
									'promo_code' => $request->promo_code,
									'provider' => $provider_data,
									'bill' => "",
									'user' => $user,
									'card_details' => $cards,
									'charge_details' => $charge,
									'current_provider' => 0,
									'promocode' =>  $request->promo_code,
									'error_code' => 483,
									/* 'error' => trans('customerController.no') . "" . $driver->keyword . trans('customerController.provider_available_try_again'), */
									'error' => trans('customerController.no') . "" . Config::get('app.generic_keywords.Provider') . trans('customerController.provider_available_try_again'),
									'error_messages' => array(trans('customerController.no') . "" . Config::get('app.generic_keywords.Provider') . trans('customerController.provider_available_try_again')),
								);
							}
							$response_code = 200;
						} else {
							/* $response_array = array('success' => false, 'error' => trans('customerController.request_id_doesnot_match') . $var->keyword . "" . trans('customerController.id'),'error_messages' => array(trans('customerController.request_id_doesnot_match') . $var->keyword . "" . trans('customerController.id')) . trans('customerController.provider_available_try_again')), 'error_code' => 407); */
							$response_array = array('success' => false, 'error' => trans('customerController.request_id_doesnot_match') . Config::get('app.generic_keywords.User') . "" . trans('customerController.id'), 'error_messages' => array(trans('customerController.request_id_doesnot_match') . Config::get('app.generic_keywords.User') . "" . trans('customerController.id')), 'error_code' => 407);
							$response_code = 200;
						}
					} else {
						$response_array = array('success' => false, 'error' => trans('customerController.request_id_not_found'), 'error_messages' => array(trans('customerController.request_id_not_found')), 'error_code' => 408);
						$response_code = 200;
					}
				} else {
					$response_array = array('success' => false, 'error' => trans('customerController.token_expired'), 'error_messages' => array(trans('customerController.token_expired')), 'error_code' => 405);
					$response_code = 200;
				}
			} else {
				if ($is_admin) {
					/* $response_array = array('success' => false, 'error' => "" . $var->keyword . "" . trans('customerController.id_not_found'),'error_messages' => array('' . $var->keyword . "" . trans('customerController.id_not_found')), 'error_code' => 410); */
					$response_array = array('success' => false, 'error' => "" . Config::get('app.generic_keywords.User') . "" . trans('customerController.id_not_found'), 'error_messages' => array('' . Config::get('app.generic_keywords.User') . "" . trans('customerController.id_not_found')), 'error_code' => 410);
				} else {
					$response_array = array('success' => false, 'error' => trans('customerController.token_not_valid'), 'error_messages' => array(trans('customerController.token_not_valid')), 'error_code' => 406);
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
		$user_id = Input::get('id');

		$validator = Validator::make(
						array(
					trans('customerController.request_id') => $request_id,
					trans('customerController.token') => $token,
					trans('customerController.user_id') => $user_id,
						), array(
					trans('customerController.request_id') => 'required|integer',
					trans('customerController.token') => 'required',
					trans('customerController.user_id') => 'required|integer',
						), array(
					trans('customerController.request_id') => trans('customerController.id_request_required'),
					trans('customerController.token') => '',
					trans('customerController.user_id') => trans('customerController.unique_id_missing'),
						)
		);

		/* $var = Keywords::where('id', 2)->first(); */

		if ($validator->fails()) {
			$error_messages = $validator->messages()->all();
			$response_array = array('success' => false, 'error' => trans('customerController.invalid_input'), 'error_code' => 401, 'error_messages' => $error_messages);
			$response_code = 200;
		} else {
			$is_admin = $this->isAdmin($token);

			if ($user_data = $this->getUserData($user_id, $token, $is_admin)) {
				// check for token validity
				if (is_token_active($user_data->token_expiry) || $is_admin) {
					// Do necessary operations
					if ($request = Requests::find($request_id)) {

						if ($request->user_id == $user_data->id) {

							if ($request->confirmed_provider != 0) {
								if ($request->is_started == 0) {
									$provider = Provider::find($request->confirmed_provider);
									$distance = 0;
								} else {
									$provider = RequestLocation::where('request_id', $request->id)->orderBy('created_at', 'desc')->first();
									$distance = RequestLocation::where('request_id', $request->id)->max('distance');
								}

								$settings = Settings::where('key', 'default_distance_unit')->first();
								$unit = $settings->value;
								if ($unit == 0) {
									$unit_set = 'kms';
								} elseif ($unit == 1) {
									$unit_set = 'miles';
								}
								$distance = convert($distance, $unit);

								$loc1 = RequestLocation::where('request_id', $request->id)->first();
								$loc2 = RequestLocation::where('request_id', $request->id)->orderBy('id', 'desc')->first();
								if ($loc1) {
									$time1 = strtotime($loc2->created_at);
									$time2 = strtotime($loc1->created_at);
									$difference = intval(($time1 - $time2) / 60);
								} else {
									$difference = 0;
								}
								$difference = $request->time;

								$new_request_service = RequestServices::where('request_id', $request->id)->first();

								$request_type = $new_request_service->type;

								$response_array = array(
									'success' => true,
									'latitude' => $provider->latitude,
									'longitude' => $provider->longitude,
									'bearing' => $provider->bearing,
									'distance' => (string) $distance,
									'time' => $difference,
									'unit' => $unit_set,
									'request_type' => $request_type
								);

							} else {
								$response_array = array(
									'success' => false,
									'error' => "" . Config::get('app.generic_keywords.Provider') . "" . trans('customerController.not_confirmed_yet'),
									'error_messages' => array('' . Config::get('app.generic_keywords.Provider') . "" . trans('customerController.not_confirmed_yet')),
									'error_code' => 421,
								);
							}
							$response_code = 200;
						} else {
							/* $response_array = array('success' => false, 'error' => trans('customerController.request_id_doesnot_match') . $var->keyword . "" . trans('customerController.id'),'error_messages' => array(trans('customerController.request_id_doesnot_match') . $var->keyword . "" . trans('customerController.id')), 'error_code' => 407); */
							$response_array = array('success' => false, 'error' => trans('customerController.request_id_doesnot_match') . Config::get('app.generic_keywords.User') . "" . trans('customerController.id'), 'error_messages' => array(trans('customerController.request_id_doesnot_match') . Config::get('app.generic_keywords.User') . "" . trans('customerController.id')), 'error_code' => 407);
							$response_code = 200;
						}
					} else {
						$response_array = array('success' => false, 'error' => trans('customerController.request_id_not_found'), 'error_messages' => array(trans('customerController.request_id_not_found')), 'error_code' => 408);
						$response_code = 200;
					}
				} else {
					$response_array = array('success' => false, 'error' => trans('customerController.token_expired'), 'error_messages' => array(trans('customerController.token_expired')), 'error_code' => 405);
					$response_code = 200;
				}
			} else {
				if ($is_admin) {
					/* $response_array = array('success' => false, 'error' => "" . $var->keyword . "" . trans('customerController.id_not_found'),'error_messages' => array('' . $var->keyword . "" . trans('customerController.id_not_found')), 'error_code' => 410); */
					$response_array = array('success' => false, 'error' => "" . Config::get('app.generic_keywords.User') . "" . trans('customerController.id_not_found'), 'error_messages' => array('' . Config::get('app.generic_keywords.User') . "" . trans('customerController.id_not_found')), 'error_code' => 410);
				} else {
					$response_array = array('success' => false, 'error' => trans('customerController.token_not_valid'), 'error_messages' => array(trans('customerController.token_not_valid')), 'error_code' => 406);
				}
				$response_code = 200;
			}
		}


		$response = Response::json($response_array, $response_code);
		return $response;
	}

	// check status and Send Request to provider
	// if request not timed out do nothing
	// else send new request
	// if user accepted change stat of request

	public function schedule_request() {
		
		/* Cronjob counter */
		/* echo asset_url() . "/cron_count.txt"; */
		if(!file_exists(public_path() . "/cron_count.txt")){
			fopen(public_path() . "/cron_count.txt", "w");
			$css_msg = array();
			$css_msg[0] = 0;
		}
		else{
			$css_msg = file(public_path() . "/cron_count.txt");
		}

		if ($css_msg[0] > '100') {
			$css_msg[0] = 0;
		} else {
			$css_msg[0] ++;
		}

		//Log::error('cron count' . 1);
		/* echo $css_msg[0]; */
		$t = file_put_contents(public_path() . '/cron_count.txt', $css_msg[0]);
		$css_msg[0];
		/* Cronjob counter END */

		$time = date("Y-m-d H:i:s");
		$timezone_app = Config::get('app.timezone');
		date_default_timezone_set($timezone_app);
		$timezone_sys = date_default_timezone_get();

		$query = "SELECT request.*,TIMESTAMPDIFF(SECOND,request_start_time, '$time') AS diff FROM request WHERE status = 0 AND is_cancelled = 0";
		$results = DB::select(DB::raw($query));

		$ref_query = "SELECT id FROM user WHERE id NOT IN(SELECT `user_id` FROM ledger)";
		$ref_entry = DB::select(DB::raw($ref_query));
		foreach ($ref_entry as $result) {
			regenerate_code:
			$referral_code = my_random6_number();
			if (Ledger::where('referral_code', $referral_code)->count()) {
				goto regenerate_code;
			}
			$ledger = new Ledger;
			$ledger->user_id = $result->id;
			$ledger->referral_code = $referral_code;
			$ledger->save();
		}

		/* SEND REFERRAL & PROMO INFO */
		$settings = Settings::where('key', 'referral_code_activation')->first();
		$referral_code_activation = $settings->value;
		if ($referral_code_activation) {
			$referral_code_activation_txt = trans('customerController.referral_on');
		} else {
			$referral_code_activation_txt = trans('customerController.referral_off');
		}

		$settings = Settings::where('key', 'promotional_code_activation')->first();
		$promotional_code_activation = $settings->value;
		if ($promotional_code_activation) {
			$promotional_code_activation_txt = trans('customerController.promo_on');
		} else {
			$promotional_code_activation_txt = trans('customerController.promo_off');
		}
		/* SEND REFERRAL & PROMO INFO */
		$driver_data = "";

		foreach ($results as $result) {
			$settings = Settings::where('key', 'provider_timeout')->first();
			$timeout = $settings->value;
			$settings = Settings::where('key', 'change_provider_tolerance')->first();
			$timeout = $timeout + $settings->value;
			if ($result->diff >= $timeout) {
				// Archiving Old Provider
				RequestMeta::where('request_id', '=', $result->id)->where('provider_id', '=', $result->current_provider)->update(array('status' => 2));
				$request = Requests::where('id', $result->id)->first();
				$request_meta = RequestMeta::where('request_id', '=', $result->id)->where('status', '=', 0)->orderBy('created_at')->first();
				// update request
				if (isset($request_meta->provider_id)) {
					// assign new provider
					Requests::where('id', '=', $result->id)->update(array('current_provider' => $request_meta->provider_id, 'request_start_time' => date("Y-m-d H:i:s")));

					// Send Notification

					$provider = Provider::find($request_meta->provider_id);
					$provider_services  = ProviderServices::where('provider_id', $provider->id)->first();

					$settings = Settings::where('key', 'provider_timeout')->first();
					$time_left = $settings->value;

					$user = User::find($result->user_id);

					$msg_array = array();
					$msg_array['unique_id'] = 1;
					$msg_array['request_id'] = $request->id;
					$msg_array['time_left_to_respond'] = $time_left;

					$msg_array['payment_mode'] = $request->payment_mode;
					$msg_array['client_profile'] = array();
					$msg_array['client_profile']['name'] = $user->first_name . " " . $user->last_name;
					$msg_array['client_profile']['picture'] = $user->picture;
					$msg_array['client_profile']['bio'] = $user->bio;
					$msg_array['client_profile']['address'] = $user->address;
					$msg_array['client_profile']['phone'] = $user->phone;

					$user = User::find($result->user_id);
					$request_data = array();
					$request_data['user'] = array();
					$request_data['user']['name'] = $user->first_name . " " . $user->last_name;
					$request_data['user']['picture'] = $user->picture;
					$request_data['user']['phone'] = $user->phone;
					$request_data['user']['address'] = $user->address;
					$request_data['user']['latitude'] = $request->latitude;
					$request_data['user']['longitude'] = $request->longitude;
					if ($request->d_latitude != NULL) {
						$request_data['user']['d_latitude'] = $request->D_latitude;
						$request_data['user']['d_longitude'] = $request->D_longitude;
					}
					$request_data['user']['rating'] = $user->rate;
					$request_data['user']['num_rating'] = $user->rate_count;
					/* $request_data['user']['rating'] = DB::table('review_user')->where('user_id', '=', $user->id)->avg('rating') ? : 0;
					  $request_data['user']['num_rating'] = DB::table('review_user')->where('user_id', '=', $user->id)->count(); */
					$msg_array['request_data'] = $request_data;

					$title = trans('customerController.new_request');

					$message = $msg_array;
					//Log::info('New Request = ' . print_r($message, true));
					send_notifications($request_meta->provider_id, "provider", $title, $message);
					$driver_data = array();
					$driver_data['unique_id'] = 1;
					$driver_data['id'] = "" . $provider->id;
					$driver_data['first_name'] = "" . $provider->first_name;
					$driver_data['last_name'] = "" . $provider->last_name;
					$driver_data['phone'] = "" . $provider->phone;
					/*  $driver_data['email'] = "" . $provider->email; */
					$driver_data['picture'] = "" . $provider->picture;
					$driver_data['bio'] = "" . $provider->bio;
					/* $driver_data['address'] = "" . $provider->address;
					  $driver_data['state'] = "" . $provider->state;
					  $driver_data['country'] = "" . $provider->country;
					  $driver_data['zipcode'] = "" . $provider->zipcode;
					  $driver_data['login_by'] = "" . $provider->login_by;
					  $driver_data['social_unique_id'] = "" . $provider->social_unique_id;
					  $driver_data['is_active'] = "" . $provider->is_active;
					  $driver_data['is_available'] = "" . $provider->is_available; */
					$driver_data['latitude'] = "" . $provider->latitude;
					$driver_data['longitude'] = "" . $provider->longitude;
					/* $driver_data['is_approved'] = "" . $provider->is_approved; */
					$driver_data['type'] = "" . $provider_services->type;
					$driver_data['car_model'] = "" . $provider->car_model;
					$driver_data['car_number'] = "" . $provider->car_number;
					$driver_data['rating'] = $provider->rate;
					$driver_data['num_rating'] = $provider->rate_count;
					/* $driver_data['rating'] = DB::table('review_provider')->where('provider_id', '=', $provider->id)->avg('rating') ? : 0;
					  $driver_data['num_rating'] = DB::table('review_provider')->where('provider_id', '=', $provider->id)->count(); */
					$client_push_data = array(
						'success' => true,
						'unique_id' => 1,
						'is_referral_active' => $referral_code_activation,
						'is_referral_active_txt' => $referral_code_activation_txt,
						'is_promo_active' => $promotional_code_activation,
						'is_promo_active_txt' => $promotional_code_activation_txt,
						'request_id' => $result->id,
						'provider' => $driver_data,
					);
					$message1 = $client_push_data;
					$user_data = User::find($result->user_id);
					$title1 = trans('customerController.new') . "" . Config::get('app.generic_keywords.Provider') . "" . trans('customerController.assigned');
					/*Log::error('Provider data: ' . $message1);
					Log::error('message title' . $title1);*/
					/*Log::error('message title' . $title1);*/
					send_notifications($user_data->id, "user", $title1, $message1);
				} else {
					$user = User::find($result->user_id);
					/* CLIENT PUSH FOR GETTING DRIVER DETAILS */
					$client_push_data = array(
						'success' => false,
						'unique_id' => 1,
						'error' => trans('customerController.no') . "" . Config::get('app.generic_keywords.Provider') . trans('customerController.found_around_you'),
						'error_messages' => array(trans('customerController.no') . "" . Config::get('app.generic_keywords.Provider') . trans('customerController.found_around_you')),
						'is_referral_active' => $referral_code_activation,
						'is_referral_active_txt' => $referral_code_activation_txt,
						'is_promo_active' => $promotional_code_activation,
						'is_promo_active_txt' => $promotional_code_activation_txt,
						'request_id' => $result->id,
						'error_code' => 411,
						'provider' => $driver_data,
					);
					$message1 = $client_push_data;
					$title1 = trans('customerController.no') . "" . Config::get('app.generic_keywords.Provider') . "" . trans('customerController.found') . '.';
					
					return $user;

					send_notifications($user->id, "user", $title1, $message1);
					/* } */
					/* CLIENT PUSH FOR GETTING DRIVER DETAILS END */
					// request ended
					if ($result->promo_id) {
						$promo_update_counter = PromoCodes::find($result->promo_id);
						$promo_update_counter->uses = $promo_update_counter->uses + 1;
						$promo_update_counter->save();

						UserPromoUse::where('user_id', '=', $result->user_id)->where('code_id', '=', $result->promo_id)->delete();

						$user = User::find($result->user_id);
						$user->promo_count = $user->promo_count - 1;
						$user->save();

						$request = Requests::find($result->id);
						$request->promo_id = 0;
						$request->promo_code = "";
						$request->save();
					}
					Requests::where('id', '=', $result->id)->update(array('current_provider' => 0, 'status' => 1, 'is_cancelled' => 1));



					/* $driver = Keywords::where('id', 1)->first(); */
					$owne = User::where('id', $result->user_id)->first();
					/* $driver_keyword = $driver->keyword; */
					$driver_keyword = Config::get('app.generic_keywords.Provider');
					$user_data_id = $owne->id;
					send_notifications($user_data_id, "user", trans('customerController.no') . "" . $driver_keyword . "" . trans('customerController.found'), trans('customerController.no') . "" . $driver_keyword . "" . trans('customerController.available_your_area_try_again'));

					$user = User::find($result->user_id);

					$settings = Settings::where('key', 'sms_request_unanswered')->first();
					$pattern = $settings->value;
					$pattern = str_replace('%id%', $result->id, $pattern);
					$pattern = str_replace('%user%', $user->first_name, $pattern);
					$pattern = str_replace('%user_mobile%', $user->phone, $pattern);
					sms_notification(1, 'admin', $pattern);

					// send email
					/* $settings = Settings::where('key', 'email_request_unanswered')->first();
					  $pattern = $settings->value;
					  $pattern = str_replace('%id%', $result->id, $pattern);
					  $pattern = str_replace('%url%', web_url() . "/admin/request/map/" . $result->id, $pattern);
					  $subject = "New Request Unanswered";
					  email_notification(1, 'admin', $pattern, $subject); */
					$settings = Settings::where('key', 'admin_email_address')->first();
					$admin_email = $settings->value;
					$follow_url = web_url() . "/user/signin";
					$pattern = array('admin_eamil' => $admin_email);
					$subject = trans('customerController.new_request_unansweres');
					email_notification(1, 'admin', $pattern, $subject, 'request_unanswered', null);
				}
			}
		}
		$provider_data = Provider::where('password', '=', "")->get();
		if ($provider_data) {
			foreach ($provider_data as $provider_info) {
				$password = my_random6_number();
				$pattern = trans('customerController.hello_') . "" . ucwords($provider_info->first_name) . '. ' . trans('customerController.your') . "" . Config::get('app.website_title') . "" . trans('customerController.web_login_is') . "" . $provider_info->email . "" . trans('customerController.and_password') . "" . $password;
				sms_notification($provider_info->id, 'provider', $pattern);
				$subject = trans('customerController.your') . "" . Config::get('app.website_title') . "" . trans('customerController.web_login_info');
				email_notification($provider_info->id, 'provider', $pattern, $subject);
				Provider::where('id', $provider_info->id)->update(array('password' => Hash::make($password)));
			}
		}
		$user_data = User::where('password', '=', "")->get();
		if ($user_data) {
			foreach ($user_data as $user) {
				$password = my_random6_number();
				$pattern = trans('customerController.hello_') . "" . ucwords($user->first_name) . '. ' . trans('customerController.your') . "" . Config::get('app.website_title') . "" . trans('customerController.web_login_is') . "" . $user->email . "" . trans('customerController.and_password') . "" . $password;
				sms_notification($user->id, 'user', $pattern);
				$subject = trans('customerController.your') . "" . Config::get('app.website_title') . "" . trans('customerController.web_login_info');
				email_notification($user->id, 'user', $pattern, $subject);
				User::where('id', $user->id)->update(array('password' => Hash::make($password)));
			}
		}
	}

	//dispara solicitações agendadas
	public function schedule_future_request() {
		/* Cronjob counter */

		/*
		if(!file_exists(public_path() . "/cron_count_2.txt")){
			fopen(public_path() . "/cron_count_2.txt", "w");
			$css_msg = array();
			$css_msg[0] = 0;
		}
		else{
			$css_msg = file(asset_url() . "/cron_count_2.txt");
		}

		if ($css_msg[0] > '100') {
			$css_msg[0] = '0';
		} else {
			$css_msg[0] ++;
		}

		$t = file_put_contents(public_path() . '/cron_count_2.txt', $css_msg[0]);
		$css_msg[0];
		*/
		/* Cronjob counter END */

		//obter configurações de codigos de indicação
		$settings = Settings::where('key', 'referral_code_activation')->first();
		$referral_code_activation = $settings->value;
		if ($referral_code_activation) {
			$referral_code_activation_txt = trans('customerController.referral_on');
		} else {
			$referral_code_activation_txt = trans('customerController.referral_off');
		}

		//obter configurações de codigos promocionais
		$settings = Settings::where('key', 'promotional_code_activation')->first();
		$promotional_code_activation = $settings->value;
		if ($promotional_code_activation) {
			$promotional_code_activation_txt = trans('customerController.promo_on');
		} else {
			$promotional_code_activation_txt = trans('customerController.promo_off');
		}

		//obter informações de tempo atual
		$time = date("Y-m-d H:i:s");
		$timezone_app = Config::get('app.timezone');
		date_default_timezone_set($timezone_app);
		$timezone_sys = date_default_timezone_get();

		//obter configuração de  quantos minutos antes de seu início a solicitação é disparada
		$settings = Settings::where('key', 'scheduled_request_pre_start_minutes')->first();
		$pre_request_time = $settings->value;
		$now = date("Y-m-d H:i:s", strtotime("now"));
		$now_30 = date("Y-m-d H:i:s", strtotime("+" . $pre_request_time . " minutes"));

		//obter configuração de numero maximo de tentativas para ativar uma solicitação agendada
		$settings = Settings::where('key', 'number_of_try_for_scheduled_requests')->first();
		$total_retry = $settings->value;

		//REMOVER TODAS AS SOLICITACOES CUJO NÚMERO MÁXIMO DE TENTATIVAS FOI EXCEDIDO
		$scheduled_requests = ScheduledRequests::where('retryflag', '>=', $total_retry)->get();

		foreach ($scheduled_requests as $current_request) {
			$driver_data = "";
			$user = User::find($current_request->user_id);
			
			//notificação via push
			$client_push_data = array(
				'success' => false,
				'unique_id' => 1,
				'error' => trans('customerController.no') . "" . Config::get('app.generic_keywords.Provider') . trans('customerController.found_around_you'),
				'error_messages' => array(trans('customerController.no') . "" . Config::get('app.generic_keywords.Provider') . trans('customerController.found_around_you')),
				'is_referral_active' => $referral_code_activation,
				'is_referral_active_txt' => $referral_code_activation_txt,
				'is_promo_active' => $promotional_code_activation,
				'is_promo_active_txt' => $promotional_code_activation_txt,
				'request_id' => $current_request->id,
				'error_code' => 411,
				'provider' => $driver_data,
			);
			$message1 = $client_push_data;

			$driver_keyword = Config::get('app.generic_keywords.Provider');
			$title1 = trans('customerController.no') . "" . $driver_keyword . "" . trans('customerController.available_your_area_scheduled') . "" . Config::get('app.generic_keywords.Trip') . "" . trans('customerController.was_auto_canceled');

			send_notifications($user->id, "user", $title1, $message1);

			//nottificação via SMS
			sms_notification($user->id, 'user', trans('customerController.hello_') . ',' . $user->first_name . "" . trans('customerController.your_scheduled') . "" . Config::get('app.generic_keywords.Trip') . "" . trans('customerController.auto_cancelled_because') . "" . $driver_keyword . "" . trans('customerController.available_in_your_area'));

			//notificação via e-mail
			$subject = trans('customerController.no') . "" . $driver_keyword . "" . trans('customerController.available_your_area_scheduled') . "" . Config::get('app.generic_keywords.Trip') . "" . trans('customerController.was_auto_canceled');
			$pattern = trans('customerController.hello_') . ',' . $user->first_name . '<br/>No ' . $driver_keyword . "" . trans('customerController.available_your_area_scheduled') . "" . Config::get('app.generic_keywords.Trip') . "" . trans('customerController.with_schedule_date') . "" . $current_request->start_time . "" . trans('customerController.was_auto_canceled');
			email_notification($user->id, 'user', $pattern, $subject, null, 'imp');

			// request ended
			if ($current_request->promo_id) {
				$promo_update_counter = PromoCodes::find($current_request->promo_id);
				$promo_update_counter->uses = $promo_update_counter->uses + 1;
				$promo_update_counter->save();

				UserPromoUse::where('user_id', '=', $current_request->user_id)->where('code_id', '=', $current_request->promo_id)->delete();

				$user->promo_count = $user->promo_count - 1;
				$user->save();
			}

			ScheduledRequests::where('id', '=', $current_request->id)->delete();
		}

		//DISPARAR SOLICITAÇÕES CUJO TEMPO DE INÍCIO É DAQUI A X MINUTOS
		$scheduled_requests = ScheduledRequests::where('server_start_time', '<=', $now_30)->where('retryflag', '<', $total_retry)->get();
		$details = array();
		foreach ($scheduled_requests as $schedules) {
			$details[] = $schedules;

			$user_id = $schedules->user_id;
			$latitude = $schedules->latitude;
			$longitude = $schedules->longitude;
			$d_latitude = $schedules->dest_latitude;
			$d_longitude = $schedules->dest_longitude;
			$payment_opt = $schedules->payment_mode;
			$time_zone = $schedules->time_zone;
			$src_address = $schedules->src_address;
			$dest_address = $schedules->dest_address;
			$usr_strt_time = $schedules->start_time;
			$unit = "";
			$driver_data = "";
			$type_id = $schedules->type;
			$category_id = $schedules->category_id;
			$vehicle_brand = $schedules->vehicle_brand;
			$vehicle_plate = $schedules->vehicle_plate;
			$vehicle_observations = $schedules->vehicle_observations;

			if($user = User::find($user_id)){

				$providerNearest = Provider::getNearest($latitude, $longitude, $type_id, $category_id);

				if ($providerNearest) {
					$provider_list = array();

					$request = new Requests;
					$request->user_id = $user_id;
					$request->payment_mode = $payment_opt;
					$request->promo_id = $schedules->promo_id;
					$request->promo_code = $schedules->promo_code;

					$default_timezone = $user_timezone = Config::get('app.timezone');
					$date_time = get_user_time($default_timezone, $user_timezone, date("Y-m-d H:i:s"));
					$request->D_latitude = 0;
					if (isset($d_latitude)) {
						$request->D_latitude = $schedules->dest_latitude;
					}
					$request->D_longitude = 0;
					if (isset($d_longitude)) {
						$request->D_longitude = $schedules->dest_longitude;
					}
					$request->request_start_time = $date_time;
					$request->latitude = $latitude;
					$request->longitude = $longitude;
					$request->time_zone = $time_zone;
					$request->src_address = $src_address;
					$request->dest_address = $dest_address;
					$request->req_create_user_time = $usr_strt_time;
					$request->current_provider = $providerNearest->id;
					$request->save();

					//salvar request-options
					if($type_id && $category_id){
						$providerService = ProviderServices::findRecursive($providerNearest->id, $type_id, $category_id);
						if($providerService){
							$requestOptions 						= new RequestOptions ;
							$requestOptions->request_id  			= $request->id ;
							$requestOptions->provider_service_id 	= $providerService->id ;
							$requestOptions->vehicle_observations	= $vehicle_observations ;
							$requestOptions->vehicle_plate  		= $vehicle_plate ;
							$requestOptions->vehicle_brand  		= $vehicle_brand ;
							$requestOptions->save();
						}
					}
					
					//salvar request_services
					$reqserv = new RequestServices;
					$reqserv->request_id = $request->id;
					$reqserv->type = $type_id;
					$reqserv->save();

					// salva prestador corrente no meta , antes era utilizado para salvar as possibilidades
					$request_meta = new RequestMeta;
					$request_meta->request_id = $request->id;
					$request_meta->provider_id = $providerNearest->id;
					$request_meta->save();


					$settings = Settings::where('key', 'provider_timeout')->first();
					$time_left = $settings->value;

					// Send Notification	
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
					if ($d_latitude != NULL) {
						$request_data['user']['d_latitude'] = $d_latitude;
						$request_data['user']['d_longitude'] = $d_longitude;
					}
					$request_data['user']['user_dist_lat'] = $request->D_latitude;
					$request_data['user']['user_dist_long'] = $request->D_longitude;
					$request_data['user']['payment_type'] = $payment_opt;
					$request_data['user']['rating'] = $user->rate;
					$request_data['user']['num_rating'] = $user->rate_count;
					
					$msg_array['request_data'] = $request_data;

					ScheduledRequests::where('id', '=', $schedules->id)->delete();

					$title = trans('customerController.new_request');
					$message = $msg_array;

					send_notifications($providerNearest->id, "provider", $title, $message);

					$client_push_data = array(
						'success' => true,
						'unique_id' => 1,
						'is_referral_active' => $referral_code_activation,
						'is_referral_active_txt' => $referral_code_activation_txt,
						'is_promo_active' => $promotional_code_activation,
						'is_promo_active_txt' => $promotional_code_activation_txt,
						'request_id' => $request->id,
						'provider' => $driver_data,
					);
					$message = $client_push_data;
					$title = trans('customerController.activated_scheduled1') . "" . Config::get('app.generic_keywords.Trip') . " " . trans('customerController.activated_scheduled2');
					send_notifications($user->id, "user", $title, $message);

					// Send SMS 
					$settings = Settings::where('key', 'sms_request_created')->first();
					$pattern = $settings->value;
					$pattern = str_replace('%user%', $user->first_name . " " . $user->last_name, $pattern);
					$pattern = str_replace('%id%', $request->id, $pattern);
					$pattern = str_replace('%user_mobile%', $user->phone, $pattern);
					sms_notification(1, 'admin', $pattern);

					// send email
					$settings = Settings::where('key', 'admin_email_address')->first();
					$admin_email = $settings->value;
					$follow_url = web_url() . "/user/signin";
					$pattern = array('admin_eamil' => $admin_email, 'trip_id' => $request->id, 'follow_url' => $follow_url);
					$subject = trans('customerController.ride_booking_request');
					email_notification(1, 'admin', $pattern, $subject, 'new_request', null);

				} else {
					$trys_for_request = $schedules->retryflag + 1;
					ScheduledRequests::where('id', $schedules->id)->update(array('retryflag' => $trys_for_request));
				}
				$response_code = 200;
			}
		}
		$response_array = array(
			'details' => $details,
			'current' => $now,
			'pre_request_time' => $now_30,
			'success' => true,
		);
		$response_code = 200;
		$response = Response::json($response_array, $response_code);
		return $response;
	}

	public function auto_transfer_to_providers() {
		/* Cronjob counter */
		/* echo asset_url() . "/cron_count.txt"; */
		$css_msg = file(asset_url() . "/auto_transfer_provider.txt");
		if ($css_msg[0] > '100') {
			$css_msg[0] = '0';
		} else {
			$css_msg[0] ++;
		}
		/* echo $css_msg[0]; */
		$t = file_put_contents(public_path() . '/auto_transfer_provider.txt', $css_msg[0]);
		$css_msg[0];
		/* Cronjob counter END */
		$now = date("Y-m-d H:i:s", strtotime("now"));
		/* echo "\n"; */

		/* AUTO TRANSFER TO SERVICE PROVIDER ACCOUNT */
		$settings = Settings::where('key', 'auto_transfer_schedule_at_after_selected_number_of_days')->first();
		$transfer_schedule_duration = ($settings->value * 1440);
		$transfer_date = date("Y-m-d H:i:s", strtotime("-" . $transfer_schedule_duration . " minutes"));

		$provider_transfer = Provider::where('provider_transfer_date', '<=', $transfer_date)->where('payment_remaining', '>=', 1)->where('refund_remaining', '>=', 1)->get();

		$settings = Settings::where('key', 'auto_transfer_provider_payment')->first();
		$transfer_allow = $settings->value;
		$fail_reason = "";
		foreach ($provider_transfer as $provider_data_trans) {
			if (Config::get('app.default_payment') == 'stripe') {
				if ($transfer_allow == 1 && $provider_data_trans->merchant_id != "" && Config::get('app.currency_symb') == '$' && ($provider_data_trans->account_currency = 'usd' || $provider_data_trans->account_currency = 'USD') && ($provider_data_trans->account_country = 'US' || $provider_data_trans->account_country = 'us')) {

					$transfer_amount = $provider_data_trans->payment_remaining - $provider_data_trans->refund_remaining;
					$payment_ramaining = $provider_data_trans->payment_remaining;
					$refund_ramaining = $provider_data_trans->refund_remaining;

					if ($transfer_amount > 0) {
						/* echo $provider_data_trans->id;
						  echo "\n"; */
						$transfer_floor = floor($transfer_amount);
						Stripe::setApiKey(Config::get('app.stripe_secret_key'));
						try {
							$transfer = Stripe_Transfer::create(array(
										"amount" => $transfer_floor * 100, // amount in cents
										"currency" => "usd",
										"recipient" => $provider_data_trans->merchant_id)
							);
							if ($transfer->status != 'canceled' || $transfer->status != 'failed') {
								/* SUCESS */
								$provider_data = Provider::find($provider_data_trans->id);
								$provider_data->provider_transfer_date = $now;
								$provider_data->payment_remaining = $provider_data->payment_remaining - $transfer_floor - floor($provider_data_trans->refund_remaining);
								$provider_data->refund_remaining = $provider_data->refund_remaining - floor($provider_data->refund_remaining);
								$provider_data->save();

								/* EMAIL NOTIFICATION */
								/* $settings = Settings::where('key', 'auto_transfer_to_provider_account_on_success')->first();
								  $pattern = $settings->value;
								  $pattern = str_replace('%name%', $provider_data->first_name, $pattern);
								  $pattern = str_replace('%payment%', $payment_ramaining, $pattern);
								  $pattern = str_replace('%refund%', $refund_ramaining, $pattern);
								  $pattern = str_replace('%amount%', $transfer_floor, $pattern);
								  $subject = "Credited to Your Account";
								  email_notification($provider_data->id, 'provider', $pattern, $subject); */
								/* EMAIL NOTIFICATION END */
								/* SMS NOTIFICATION */
								/* $settings = Settings::where('key', 'sms_provider_auto_transaction_success')->first();
								  $pattern = $settings->value;
								  $pattern = str_replace('%name%', $provider_data->first_name, $pattern);
								  $pattern = str_replace('%amount%', $transfer_floor, $pattern);
								  sms_notification($provider_data->id, 'provider', $pattern); */
								/* SMS NOTIFICATION END */
							} else {
								/* FAIL */
								/* echo "fail no transfer";
								  echo "\n"; */
								$fail_reason = trans('customerController.transaction_failed');
								$provider_data = Provider::find($provider_data_trans->id);
								$provider_data->provider_transfer_date = $now;
								$provider_data->save();
								/* EMAIL NOTIFICATION */
								/* $settings = Settings::where('key', 'auto_transfer_to_provider_account_on_fail')->first();
								  $pattern = $settings->value;
								  $pattern = str_replace('%name%', $provider_data->first_name, $pattern);
								  $pattern = str_replace('%reason%', $fail_reason, $pattern);
								  $subject = "Credited Fail To Your Account";
								  email_notification($provider_data->id, 'provider', $pattern, $subject); */
								/* EMAIL NOTIFICATION END */
								/* SMS NOTIFICATION */
								/* $settings = Settings::where('key', 'sms_provider_auto_transaction_fail')->first();
								  $pattern = $settings->value;
								  $pattern = str_replace('%name%', $provider_data->first_name, $pattern);
								  $pattern = str_replace('%reason%', $fail_reason, $pattern);
								  sms_notification($provider_data->id, 'provider', $pattern); */
								/* SMS NOTIFICATION END */
							}
						} catch (Stripe_InvalidRequestError $e) {
							/* echo "admin fail";
							  echo "\n"; */
							/* FAIL TO ADMIN */
							$fail_reason = trans('customerController.transaction_failed');
							$provider_data = Provider::find($provider_data_trans->id);
							$provider_data->provider_transfer_date = $now;
							$provider_data->save();
							/* EMAIL NOTIFICATION */
							/* $settings = Settings::where('key', 'auto_transfer_to_provider_account_on_fail')->first();
							  $pattern = $settings->value;
							  $pattern = str_replace('%name%', $provider_data->first_name, $pattern);
							  $pattern = str_replace('%reason%', $fail_reason, $pattern);
							  $subject = "Credited Fail To Your Account";
							  email_notification($provider_data->id, 'provider', $pattern, $subject); */
							/* EMAIL NOTIFICATION END */
							/* SMS NOTIFICATION */
							/* $settings = Settings::where('key', 'sms_provider_auto_transaction_fail')->first();
							  $pattern = $settings->value;
							  $pattern = str_replace('%name%', $provider_data->first_name, $pattern);
							  $pattern = str_replace('%reason%', $fail_reason, $pattern);
							  sms_notification($provider_data->id, 'provider', $pattern); */
							/* SMS NOTIFICATION END */
						}
						/* $request->transfer_amount = floor($total - $settng->value * $total / 100); */
					} else {
						/* echo "fail no enough amount";
						  echo "\n"; */
						$fail_reason = trans('customerController.not_enought_transfer_amount');
						$provider_data = Provider::find($provider_data_trans->id);
						$provider_data->provider_transfer_date = $now;
						$provider_data->save();
						/* EMAIL NOTIFICATION */
						/* $settings = Settings::where('key', 'auto_transfer_to_provider_account_on_fail')->first();
						  $pattern = $settings->value;
						  $pattern = str_replace('%name%', $provider_data->first_name, $pattern);
						  $pattern = str_replace('%reason%', $fail_reason, $pattern);
						  $subject = "Credited Fail To Your Account";
						  email_notification($provider_data->id, 'provider', $pattern, $subject); */
						/* EMAIL NOTIFICATION END */
						/* SMS NOTIFICATION */
						/* $settings = Settings::where('key', 'sms_provider_auto_transaction_fail')->first();
						  $pattern = $settings->value;
						  $pattern = str_replace('%name%', $provider_data->first_name, $pattern);
						  $pattern = str_replace('%reason%', $fail_reason, $pattern);
						  sms_notification($provider_data->id, 'provider', $pattern); */
						/* SMS NOTIFICATION END */
						/* SEND EMAIL OF FAILER */
						/* SEND SMS OF FAILER */
					}
				}
			}
		}
		/* AUTO TRANSFER TO SERVICE PROVIDER ACCOUNT END */
	}

	// Request in Progress

	public function request_in_progress() {


		$token = Input::get('token');
		$user_id = Input::get('id');

		$validator = Validator::make(
						array(
					trans('customerController.token') => $token,
					trans('customerController.user_id') => $user_id,
						), array(
					trans('customerController.token') => 'required',
					trans('customerController.user_id') => 'required|integer'
						), array(
					trans('customerController.token') => '',
					trans('customerController.user_id') => trans('customerController.unique_id_missing')
						)
		);

		/* $var = Keywords::where('id', 2)->first(); */

		if ($validator->fails()) {
			$error_messages = $validator->messages()->all();
			$response_array = array('success' => false, 'error' => trans('customerController.invalid_input'), 'error_code' => 401, 'error_messages' => $error_messages);
			$response_code = 200;
		} else {
			$is_admin = $this->isAdmin($token);

			if ($user_data = $this->getUserData($user_id, $token, $is_admin)) {
				// check for token validity
				if (is_token_active($user_data->token_expiry) || $is_admin) {
					$request = Requests::where('status', '=', 1)->where('is_completed', '=', 0)->where('is_cancelled', '=', 0)->where('user_id', '=', $user_id)->where('current_provider', '!=', 0)->orderBy('created_at', 'desc')->first();
					if ($request) {
						$request_id = $request->id;
					} else {
						$request_id = -1;
					}
					$response_array = array(
						'request_id' => $request_id,
						'success' => true,
					);
					$response_code = 200;
				} else {
					$response_array = array('success' => false, 'error' => trans('customerController.token_expired'), 'error_messages' => array(trans('customerController.token_expired')), 'error_code' => 405);
					$response_code = 200;
				}
			} else {
				if ($is_admin) {
					/* $response_array = array('success' => false, 'error' => "" . $var->keyword . "" . trans('customerController.id_not_found'),'error_messages' => array('' . $var->keyword . "" . trans('customerController.id_not_found')), 'error_code' => 410); */
					$response_array = array('success' => false, 'error' => "" . Config::get('app.generic_keywords.User') . "" . trans('customerController.id_not_found'), 'error_messages' => array('' . Config::get('app.generic_keywords.User') . "" . trans('customerController.id_not_found')), 'error_code' => 410);
				} else {
					$response_array = array('success' => false, 'error' => trans('customerController.token_not_valid'), 'error_messages' => array(trans('customerController.token_not_valid')), 'error_code' => 406);
				}
				$response_code = 200;
			}
		}


		$response = Response::json($response_array, $response_code);
		return $response;
	}

	public function create_future_request() {
		$token = Input::get('token');
		$user_id = Input::get('id');
		$latitude = Input::get('latitude');
		$longitude = Input::get('longitude');
		$d_latitude = 0;		

		if (Input::has('d_latitude')) {
			$d_latitude = Input::get('d_latitude');
		}
		$d_longitude = 0;
		if (Input::has('d_longitude')) {
			$d_longitude = Input::get('d_longitude');
		}
		$time_zone = trim(Input::get('time_zone'));
		$start_time = trim(Input::get('start_time'));
		$src_address = trans('customerController.address_not_available');
		if (Input::has('src_address')) {
			$src_address = trim(Input::get('src_address'));
		}
		$dest_address = trans('customerController.address_not_available');
		if (Input::has('dest_address')) {
			$dest_address = trim(Input::get('dest_address'));
		}
		$payment_opt = 0;
		if (Input::has('payment_mode')) {
			$payment_opt = Input::get('payment_mode');
		}
		if (Input::has('payment_opt')) {
			$payment_opt = Input::get('payment_opt');
		}

		$category_id = Input::get('category_id');
		$vehicle_brand = Input::get('vehicle_brand');
		$vehicle_plate = Input::get('vehicle_plate');
		$vehicle_observations = Input::get('vehicle_observations');

		$validator = Validator::make(
						array(
					trans('customerController.token') => $token,
					trans('customerController.user_id') => $user_id,
					trans('customerController.latitude') => $latitude,
					trans('customerController.longitude') => $longitude,
					trans('customerController.time_zone') => $time_zone,
					trans('customerController.start_time') => $start_time,
						), array(
					trans('customerController.token') => 'required',
					trans('customerController.user_id') => 'required|integer',
					trans('customerController.latitude') => 'required',
					trans('customerController.longitude') => 'required',
					trans('customerController.time_zone') => 'required',
					trans('customerController.start_time') => 'required',
						), array(
					trans('customerController.token') => '',
					trans('customerController.user_id') => trans('customerController.unique_id_missing'),
					trans('customerController.latitude') => trans('customerController.location_point_missing'),
					trans('customerController.longitude') => trans('customerController.location_point_missing'),
					trans('customerController.time_zone') => trans('customerController.time_zone_required'),
					trans('customerController.start_time') => trans('customerController.schedule_date_time_required'),
						)
		);
		if ($validator->fails()) {
			$error_messages = $validator->messages()->all();
			$response_array = array('success' => false, 'error' => trans('customerController.invalid_input'), 'error_code' => 401, 'error_messages' => $error_messages);
			$response_code = 200;
		} else {
			$is_admin = $this->isAdmin($token);
			$unit = "";
			$driver_data = "";
			// SEND REFERRAL & PROMO INFO
			$settings = Settings::where('key', 'referral_code_activation')->first();
			$referral_code_activation = $settings->value;
			if ($referral_code_activation) {
				$referral_code_activation_txt = trans('customerController.referral_on');
			} else {
				$referral_code_activation_txt = trans('customerController.referral_off');
			}

			$settings = Settings::where('key', 'promotional_code_activation')->first();
			$promotional_code_activation = $settings->value;
			if ($promotional_code_activation) {
				$promotional_code_activation_txt = trans('customerController.promo_on');
			} else {
				$promotional_code_activation_txt = trans('customerController.promo_off');
			}
			// SEND REFERRAL & PROMO INFO

			if ($user_data = $this->getUserData($user_id, $token, $is_admin)) {
				$all_scheduled_requests = array();
				// check for token validity
				if (is_token_active($user_data->token_expiry) || $is_admin) {
					/* TIME ZONE BASED TIME CONVERSION */
					date_default_timezone_set($time_zone);
					$datetime = new DateTime($start_time);
					$datetime->format('Y-m-d H:i:s');
					foreach ($datetime as $row1) {
						$start_time = $row1; // returns 2014-06-04 15:00
						break;     // stops at the first position
					}
					$timeEurope = new DateTimeZone(Config::get('app.timezone'));
					$datetime->setTimezone($timeEurope);
					$datetime->format('Y-m-d H:i:s');
					foreach ($datetime as $row) {
						$server_time = $row; // returns 2014-06-04 15:00
						break;     // stops at the first position
					}
					$chk_dt = date_time_differ($server_time, "+2 weeks");
					if ($chk_dt->invert == 0 && $chk_dt->d >= 0 && $chk_dt->d <= 14) {
						/* TIME ZONE BASED TIME CONVERSION END */
						/*
						if ($payment_opt != 1) {
							$card_count = Payment::where('user_id', '=', $user_id)->count();
							if ($card_count <= 0) {
								$ScheduledRequests = ScheduledRequests::where('user_id', $user_id)->orderBy('id', 'DESC')->get();
								foreach ($ScheduledRequests as $data1) {
									$data['id'] = $data1->id;
									$data['user_id'] = $data1->user_id;
									$data['latitude'] = $data1->latitude;
									$data['longitude'] = $data1->longitude;
									$data['dest_latitude'] = $data1->dest_latitude;
									$data['dest_longitude'] = $data1->dest_longitude;
									$data['time_zone'] = $data1->time_zone;
									$data['src_address'] = $data1->src_address;
									$data['dest_address'] = $data1->dest_address;
									$data['promo_code'] = $data1->promo_code;
									$data['promo_id'] = $data1->promo_id;
									$pay_mode_txt = trans('customerController.Card');
									if ($data1->payment_mode) {
										$pay_mode_txt = trans('customerController.Cash');
									}
									$data['payment_mode'] = $data1->payment_mode;
									$data['pay_mode_txt'] = $pay_mode_txt;
									$data['server_start_time'] = $data1->server_start_time;
									$data['start_time'] = $data1->start_time;
									array_push($all_scheduled_requests, $data);
								}
								$response_array = array(
									'success' => false,
									'error' => trans('customerController.please_add_card'),
									'error_messages' => array(trans('customerController.please_add_card')),
									'error_code' => 420,
									// 'now_dt' => $chk_dt,
									'is_referral_active' => $referral_code_activation,
									'is_referral_active_txt' => $referral_code_activation_txt,
									'is_promo_active' => $promotional_code_activation,
									'is_promo_active_txt' => $promotional_code_activation_txt,
									'all_scheduled_requests' => $all_scheduled_requests,
								);
								$response_code = 200;
								$response = Response::json($response_array, $response_code);
								return $response;
							}
						}*/
						$type = trim(Input::get('type'));
						if (!$type) {
							// choose default type
							$provider_type = ProviderType::where('is_default', 1)->first();

							if (!$provider_type) {
								$type = 1;
							} else {
								$type = $provider_type->id;
							}
						}
						$new_request = new ScheduledRequests;
						$new_request->user_id = $user_data->id;
						$new_request->latitude = $latitude;
						$new_request->longitude = $longitude;
						$new_request->dest_latitude = $d_latitude;
						$new_request->dest_longitude = $d_longitude;
						$new_request->time_zone = $time_zone;
						$new_request->src_address = $src_address;
						$new_request->dest_address = $dest_address;
						$new_request->payment_mode = $payment_opt;
						$new_request->server_start_time = $server_time;
						$new_request->start_time = $start_time;
						$new_request->type = $type;

						$new_request->vehicle_brand = $vehicle_brand? $vehicle_brand : '';
						$new_request->vehicle_plate = $vehicle_plate? $vehicle_plate : '';
						$new_request->vehicle_observations = $vehicle_observations? $vehicle_observations : '';
						$new_request->category_id = $category_id;
						

						if (Input::has('promo_code')) {
							$promo_code = Input::get('promo_code');
							$payment_mode = 0;
							if (Input::has('payment_mode')) {
								$payment_mode = $payment_opt = Input::get('payment_mode');
							}
							$settings = Settings::where('key', 'promotional_code_activation')->first();
							$prom_act = $settings->value;
							if ($prom_act) {
								if ($payment_mode == 0) {
									$settings = Settings::where('key', 'get_promotional_profit_on_card_payment')->first();
									$prom_act_card = $settings->value;
									if ($prom_act_card) {
										if ($promos = PromoCodes::where('coupon_code', $promo_code)->where('uses', '>', 0)->where('state', '=', 1)->first()) {
											if ((date("Y-m-d H:i:s") >= date("Y-m-d H:i:s", strtotime(trim($promos->expiry)))) || (date("Y-m-d H:i:s") <= date("Y-m-d H:i:s", strtotime(trim($promos->start_date))))) {
												$ScheduledRequests = ScheduledRequests::where('user_id', $user_id)->orderBy('id', 'DESC')->get();
												foreach ($ScheduledRequests as $data1) {
													$data['id'] = $data1->id;
													$data['user_id'] = $data1->user_id;
													$data['latitude'] = $data1->latitude;
													$data['longitude'] = $data1->longitude;
													$data['dest_latitude'] = $data1->dest_latitude;
													$data['dest_longitude'] = $data1->dest_longitude;
													$data['time_zone'] = $data1->time_zone;
													$data['src_address'] = $data1->src_address;
													$data['dest_address'] = $data1->dest_address;
													$data['promo_code'] = $data1->promo_code;
													$data['promo_id'] = $data1->promo_id;
													$pay_mode_txt = trans('customerController.Card');
													if ($data1->payment_mode) {
														$pay_mode_txt = trans('customerController.Cash');
													}
													$data['payment_mode'] = $data1->payment_mode;
													$data['pay_mode_txt'] = $pay_mode_txt;
													$data['server_start_time'] = $data1->server_start_time;
													$data['start_time'] = $data1->start_time;
													array_push($all_scheduled_requests, $data);
												}
												$response_array = array(
													'success' => FALSE,
													'error' => trans('customerController.promotional_not_available'),
													'error_messages' => array(trans('customerController.promotional_not_available')),
													'error_code' => 505,
													/* 'now_dt' => $chk_dt, */
													'is_referral_active' => $referral_code_activation,
													'is_referral_active_txt' => $referral_code_activation_txt,
													'is_promo_active' => $promotional_code_activation,
													'is_promo_active_txt' => $promotional_code_activation_txt,
													'all_scheduled_requests' => $all_scheduled_requests,
												);
												$response_code = 200;
												return Response::json($response_array, $response_code);
											} else {
												$promo_is_used = UserPromoUse::where('user_id', '=', $user_id)->where('code_id', '=', $promos->id)->count();
												if ($promo_is_used) {
													$ScheduledRequests = ScheduledRequests::where('user_id', $user_id)->orderBy('id', 'DESC')->get();
													foreach ($ScheduledRequests as $data1) {
														$data['id'] = $data1->id;
														$data['user_id'] = $data1->user_id;
														$data['latitude'] = $data1->latitude;
														$data['longitude'] = $data1->longitude;
														$data['dest_latitude'] = $data1->dest_latitude;
														$data['dest_longitude'] = $data1->dest_longitude;
														$data['time_zone'] = $data1->time_zone;
														$data['src_address'] = $data1->src_address;
														$data['dest_address'] = $data1->dest_address;
														$data['promo_code'] = $data1->promo_code;
														$data['promo_id'] = $data1->promo_id;
														$pay_mode_txt = trans('customerController.Card');
														if ($data1->payment_mode) {
															$pay_mode_txt = trans('customerController.Cash');
														}
														$data['payment_mode'] = $data1->payment_mode;
														$data['pay_mode_txt'] = $pay_mode_txt;
														$data['server_start_time'] = $data1->server_start_time;
														$data['start_time'] = $data1->start_time;
														array_push($all_scheduled_requests, $data);
													}
													$response_array = array(
														'success' => FALSE,
														'error' => trans('customerController.promotional_already_used'),
														'error_messages' => array(trans('customerController.promotional_already_used')),
														'error_code' => 512,
														/* 'now_dt' => $chk_dt, */
														'is_referral_active' => $referral_code_activation,
														'is_referral_active_txt' => $referral_code_activation_txt,
														'is_promo_active' => $promotional_code_activation,
														'is_promo_active_txt' => $promotional_code_activation_txt,
														'all_scheduled_requests' => $all_scheduled_requests,
													);
													$response_code = 200;
													return Response::json($response_array, $response_code);
												} else {
													$promo_update_counter = PromoCodes::find($promos->id);
													$promo_update_counter->uses = $promo_update_counter->uses - 1;
													$promo_update_counter->save();

													$user_promo_entry = new UserPromoUse;
													$user_promo_entry->code_id = $promos->id;
													$user_promo_entry->user_id = $user_id;
													$user_promo_entry->save();

													$user = User::find($user_id);
													$user->promo_count = $user->promo_count + 1;
													$user->save();

													$new_request->promo_id = $promos->id;
													$new_request->promo_code = $promos->coupon_code;
												}
											}
										} else {
											$ScheduledRequests = ScheduledRequests::where('user_id', $user_id)->orderBy('id', 'DESC')->get();
											foreach ($ScheduledRequests as $data1) {
												$data['id'] = $data1->id;
												$data['user_id'] = $data1->user_id;
												$data['latitude'] = $data1->latitude;
												$data['longitude'] = $data1->longitude;
												$data['dest_latitude'] = $data1->dest_latitude;
												$data['dest_longitude'] = $data1->dest_longitude;
												$data['time_zone'] = $data1->time_zone;
												$data['src_address'] = $data1->src_address;
												$data['dest_address'] = $data1->dest_address;
												$data['promo_code'] = $data1->promo_code;
												$data['promo_id'] = $data1->promo_id;
												$pay_mode_txt = trans('customerController.Card');
												if ($data1->payment_mode) {
													$pay_mode_txt = trans('customerController.Cash');
												}
												$data['payment_mode'] = $data1->payment_mode;
												$data['pay_mode_txt'] = $pay_mode_txt;
												$data['server_start_time'] = $data1->server_start_time;
												$data['start_time'] = $data1->start_time;
												array_push($all_scheduled_requests, $data);
											}
											$response_array = array(
												'success' => FALSE,
												'error' => trans('customerController.promotional_not_available'),
												'error_messages' => array(trans('customerController.promotional_not_available')),
												'error_code' => 505,
												/* 'now_dt' => $chk_dt, */
												'is_referral_active' => $referral_code_activation,
												'is_referral_active_txt' => $referral_code_activation_txt,
												'is_promo_active' => $promotional_code_activation,
												'is_promo_active_txt' => $promotional_code_activation_txt,
												'all_scheduled_requests' => $all_scheduled_requests,
											);
											$response_code = 200;
											return Response::json($response_array, $response_code);
										}
									} else {
										$ScheduledRequests = ScheduledRequests::where('user_id', $user_id)->orderBy('id', 'DESC')->get();
										foreach ($ScheduledRequests as $data1) {
											$data['id'] = $data1->id;
											$data['user_id'] = $data1->user_id;
											$data['latitude'] = $data1->latitude;
											$data['longitude'] = $data1->longitude;
											$data['dest_latitude'] = $data1->dest_latitude;
											$data['dest_longitude'] = $data1->dest_longitude;
											$data['time_zone'] = $data1->time_zone;
											$data['src_address'] = $data1->src_address;
											$data['dest_address'] = $data1->dest_address;
											$data['promo_code'] = $data1->promo_code;
											$data['promo_id'] = $data1->promo_id;
											$pay_mode_txt = trans('customerController.Card');
											if ($data1->payment_mode) {
												$pay_mode_txt = trans('customerController.Cash');
											}
											$data['payment_mode'] = $data1->payment_mode;
											$data['pay_mode_txt'] = $pay_mode_txt;
											$data['server_start_time'] = $data1->server_start_time;
											$data['start_time'] = $data1->start_time;
											array_push($all_scheduled_requests, $data);
										}
										$response_array = array(
											'success' => FALSE,
											'error' => trans('customerController.promotion_not_card'),
											'error_messages' => array(trans('customerController.promotion_not_card')),
											'error_code' => 505,
											/* 'now_dt' => $chk_dt, */
											'is_referral_active' => $referral_code_activation,
											'is_referral_active_txt' => $referral_code_activation_txt,
											'is_promo_active' => $promotional_code_activation,
											'is_promo_active_txt' => $promotional_code_activation_txt,
											'all_scheduled_requests' => $all_scheduled_requests,
										);
										$response_code = 200;
										return Response::json($response_array, $response_code);
									}
								} else if (($payment_mode == 1)) {
									$settings = Settings::where('key', 'get_promotional_profit_on_cash_payment')->first();
									$prom_act_cash = $settings->value;
									if ($prom_act_cash) {
										if ($promos = PromoCodes::where('coupon_code', $promo_code)->where('uses', '>', 0)->where('state', '=', 1)->first()) {
											if ((date("Y-m-d H:i:s") >= date("Y-m-d H:i:s", strtotime(trim($promos->expiry)))) || (date("Y-m-d H:i:s") <= date("Y-m-d H:i:s", strtotime(trim($promos->start_date))))) {
												$ScheduledRequests = ScheduledRequests::where('user_id', $user_id)->orderBy('id', 'DESC')->get();
												foreach ($ScheduledRequests as $data1) {
													$data['id'] = $data1->id;
													$data['user_id'] = $data1->user_id;
													$data['latitude'] = $data1->latitude;
													$data['longitude'] = $data1->longitude;
													$data['dest_latitude'] = $data1->dest_latitude;
													$data['dest_longitude'] = $data1->dest_longitude;
													$data['time_zone'] = $data1->time_zone;
													$data['src_address'] = $data1->src_address;
													$data['dest_address'] = $data1->dest_address;
													$data['promo_code'] = $data1->promo_code;
													$data['promo_id'] = $data1->promo_id;
													$pay_mode_txt = trans('customerController.Card');
													if ($data1->payment_mode) {
														$pay_mode_txt = trans('customerController.Cash');
													}
													$data['payment_mode'] = $data1->payment_mode;
													$data['pay_mode_txt'] = $pay_mode_txt;
													$data['server_start_time'] = $data1->server_start_time;
													$data['start_time'] = $data1->start_time;
													array_push($all_scheduled_requests, $data);
												}
												$response_array = array(
													'success' => FALSE,
													'error' => trans('customerController.promotional_not_available'),
													'error_messages' => array(trans('customerController.promotional_not_available')),
													'error_code' => 505,
													/* 'now_dt' => $chk_dt, */
													'is_referral_active' => $referral_code_activation,
													'is_referral_active_txt' => $referral_code_activation_txt,
													'is_promo_active' => $promotional_code_activation,
													'is_promo_active_txt' => $promotional_code_activation_txt,
													'all_scheduled_requests' => $all_scheduled_requests,
												);
												$response_code = 200;
												return Response::json($response_array, $response_code);
											} else {
												$promo_is_used = UserPromoUse::where('user_id', '=', $user_id)->where('code_id', '=', $promos->id)->count();
												if ($promo_is_used) {
													$ScheduledRequests = ScheduledRequests::where('user_id', $user_id)->orderBy('id', 'DESC')->get();
													foreach ($ScheduledRequests as $data1) {
														$data['id'] = $data1->id;
														$data['user_id'] = $data1->user_id;
														$data['latitude'] = $data1->latitude;
														$data['longitude'] = $data1->longitude;
														$data['dest_latitude'] = $data1->dest_latitude;
														$data['dest_longitude'] = $data1->dest_longitude;
														$data['time_zone'] = $data1->time_zone;
														$data['src_address'] = $data1->src_address;
														$data['dest_address'] = $data1->dest_address;
														$data['promo_code'] = $data1->promo_code;
														$data['promo_id'] = $data1->promo_id;
														$pay_mode_txt = trans('customerController.Card');
														if ($data1->payment_mode) {
															$pay_mode_txt = trans('customerController.Cash');
														}
														$data['payment_mode'] = $data1->payment_mode;
														$data['pay_mode_txt'] = $pay_mode_txt;
														$data['server_start_time'] = $data1->server_start_time;
														$data['start_time'] = $data1->start_time;
														array_push($all_scheduled_requests, $data);
													}
													$response_array = array(
														'success' => FALSE,
														'error' => trans('customerController.promotional_already_used'),
														'error_messages' => array(trans('customerController.promotional_already_used')),
														'error_code' => 512,
														/* 'now_dt' => $chk_dt, */
														'is_referral_active' => $referral_code_activation,
														'is_referral_active_txt' => $referral_code_activation_txt,
														'is_promo_active' => $promotional_code_activation,
														'is_promo_active_txt' => $promotional_code_activation_txt,
														'all_scheduled_requests' => $all_scheduled_requests,
													);
													$response_code = 200;
													return Response::json($response_array, $response_code);
												} else {
													$promo_update_counter = PromoCodes::find($promos->id);
													$promo_update_counter->uses = $promo_update_counter->uses - 1;
													$promo_update_counter->save();

													$user_promo_entry = new UserPromoUse;
													$user_promo_entry->code_id = $promos->id;
													$user_promo_entry->user_id = $user_id;
													$user_promo_entry->save();

													$user = User::find($user_id);
													$user->promo_count = $user->promo_count + 1;
													$user->save();

													$new_request->promo_id = $promos->id;
													$new_request->promo_code = $promos->coupon_code;
												}
											}
										} else {
											$ScheduledRequests = ScheduledRequests::where('user_id', $user_id)->orderBy('id', 'DESC')->get();
											foreach ($ScheduledRequests as $data1) {
												$data['id'] = $data1->id;
												$data['user_id'] = $data1->user_id;
												$data['latitude'] = $data1->latitude;
												$data['longitude'] = $data1->longitude;
												$data['dest_latitude'] = $data1->dest_latitude;
												$data['dest_longitude'] = $data1->dest_longitude;
												$data['time_zone'] = $data1->time_zone;
												$data['src_address'] = $data1->src_address;
												$data['dest_address'] = $data1->dest_address;
												$data['promo_code'] = $data1->promo_code;
												$data['promo_id'] = $data1->promo_id;
												$pay_mode_txt = trans('customerController.Card');
												if ($data1->payment_mode) {
													$pay_mode_txt = trans('customerController.Cash');
												}
												$data['payment_mode'] = $data1->payment_mode;
												$data['pay_mode_txt'] = $pay_mode_txt;
												$data['server_start_time'] = $data1->server_start_time;
												$data['start_time'] = $data1->start_time;
												array_push($all_scheduled_requests, $data);
											}
											$response_array = array(
												'success' => FALSE,
												'error' => trans('customerController.promotional_not_available'),
												'error_messages' => array(trans('customerController.promotional_not_available')),
												'error_code' => 505,
												/* 'now_dt' => $chk_dt, */
												'is_referral_active' => $referral_code_activation,
												'is_referral_active_txt' => $referral_code_activation_txt,
												'is_promo_active' => $promotional_code_activation,
												'is_promo_active_txt' => $promotional_code_activation_txt,
												'all_scheduled_requests' => $all_scheduled_requests,
											);
											$response_code = 200;
											return Response::json($response_array, $response_code);
										}
									} else {
										$ScheduledRequests = ScheduledRequests::where('user_id', $user_id)->orderBy('id', 'DESC')->get();
										foreach ($ScheduledRequests as $data1) {
											$data['id'] = $data1->id;
											$data['user_id'] = $data1->user_id;
											$data['latitude'] = $data1->latitude;
											$data['longitude'] = $data1->longitude;
											$data['dest_latitude'] = $data1->dest_latitude;
											$data['dest_longitude'] = $data1->dest_longitude;
											$data['time_zone'] = $data1->time_zone;
											$data['src_address'] = $data1->src_address;
											$data['dest_address'] = $data1->dest_address;
											$data['promo_code'] = $data1->promo_code;
											$data['promo_id'] = $data1->promo_id;
											$pay_mode_txt = trans('customerController.Card');
											if ($data1->payment_mode) {
												$pay_mode_txt = trans('customerController.Cash');
											}
											$data['payment_mode'] = $data1->payment_mode;
											$data['pay_mode_txt'] = $pay_mode_txt;
											$data['server_start_time'] = $data1->server_start_time;
											$data['start_time'] = $data1->start_time;
											array_push($all_scheduled_requests, $data);
										}
										$response_array = array(
											'success' => FALSE,
											'error' => trans('customerController.promotion_not_cash'),
											'error_messages' => array(trans('customerController.promotion_not_cash')),
											'error_code' => 505,
											/* 'now_dt' => $chk_dt, */
											'is_referral_active' => $referral_code_activation,
											'is_referral_active_txt' => $referral_code_activation_txt,
											'is_promo_active' => $promotional_code_activation,
											'is_promo_active_txt' => $promotional_code_activation_txt,
											'all_scheduled_requests' => $all_scheduled_requests,
										);
										$response_code = 200;
										return Response::json($response_array, $response_code);
									}
								}
							} else {
								$ScheduledRequests = ScheduledRequests::where('user_id', $user_id)->orderBy('id', 'DESC')->get();
								foreach ($ScheduledRequests as $data1) {
									$data['id'] = $data1->id;
									$data['user_id'] = $data1->user_id;
									$data['latitude'] = $data1->latitude;
									$data['longitude'] = $data1->longitude;
									$data['dest_latitude'] = $data1->dest_latitude;
									$data['dest_longitude'] = $data1->dest_longitude;
									$data['time_zone'] = $data1->time_zone;
									$data['src_address'] = $data1->src_address;
									$data['dest_address'] = $data1->dest_address;
									$data['promo_code'] = $data1->promo_code;
									$data['promo_id'] = $data1->promo_id;
									$pay_mode_txt = trans('customerController.Card');
									if ($data1->payment_mode) {
										$pay_mode_txt = trans('customerController.Cash');
									}
									$data['payment_mode'] = $data1->payment_mode;
									$data['pay_mode_txt'] = $pay_mode_txt;
									$data['server_start_time'] = $data1->server_start_time;
									$data['start_time'] = $data1->start_time;
									array_push($all_scheduled_requests, $data);
								}
								$response_array = array(
									'success' => FALSE,
									'error' => trans('customerController.promotion_not_active'),
									'error_messages' => array(trans('customerController.promotion_not_active')),
									'error_code' => 505,
									/* 'now_dt' => $chk_dt, */
									'is_referral_active' => $referral_code_activation,
									'is_referral_active_txt' => $referral_code_activation_txt,
									'is_promo_active' => $promotional_code_activation,
									'is_promo_active_txt' => $promotional_code_activation_txt,
									'all_scheduled_requests' => $all_scheduled_requests,
								);
								$response_code = 200;
								return Response::json($response_array, $response_code);
							}
						}
						$new_request->save();
						$ScheduledRequests = ScheduledRequests::where('user_id', $user_id)->orderBy('id', 'DESC')->get();
						foreach ($ScheduledRequests as $data1) {
							$data['id'] = $data1->id;
							$data['user_id'] = $data1->user_id;
							$data['latitude'] = $data1->latitude;
							$data['longitude'] = $data1->longitude;
							$data['dest_latitude'] = $data1->dest_latitude;
							$data['dest_longitude'] = $data1->dest_longitude;
							$data['time_zone'] = $data1->time_zone;
							$data['src_address'] = $data1->src_address;
							$data['dest_address'] = $data1->dest_address;
							$data['promo_code'] = $data1->promo_code;
							$data['promo_id'] = $data1->promo_id;
							$pay_mode_txt = trans('customerController.Card');
							if ($data1->payment_mode) {
								$pay_mode_txt = trans('customerController.Cash');
							}
							$data['payment_mode'] = $data1->payment_mode;
							$data['pay_mode_txt'] = $pay_mode_txt;
							$data['server_start_time'] = $data1->server_start_time;
							$data['start_time'] = $data1->start_time;
							array_push($all_scheduled_requests, $data);
						}
						$response_array = array(
							'success' => true,
							'is_referral_active' => $referral_code_activation,
							'is_referral_active_txt' => $referral_code_activation_txt,
							'is_promo_active' => $promotional_code_activation,
							'is_promo_active_txt' => $promotional_code_activation_txt,
							'all_scheduled_requests' => $all_scheduled_requests,
						);
						$response_code = 200;
					} else {
						$response_array = array(
							'success' => false,
							'error' => trans('customerController.sorry_cant_create_schedule_two_weeks'),
							'error_messages' => array(trans('customerController.sorry_cant_create_schedule_two_weeks')),
							'error_code' => 506,
							/* 'now_dt' => $chk_dt, */
							'is_referral_active' => $referral_code_activation,
							'is_referral_active_txt' => $referral_code_activation_txt,
							'is_promo_active' => $promotional_code_activation,
							'is_promo_active_txt' => $promotional_code_activation_txt,
							'all_scheduled_requests' => $all_scheduled_requests,
						);
						$response_code = 200;
					}
				} else {
					$response_array = array(
						'success' => false,
						'error' => trans('customerController.token_expired'),
						'error_messages' => array(trans('customerController.token_expired')),
						'error_code' => 405
					);
					$response_code = 200;
				}
			} else {
				if ($is_admin) {
					$response_array = array('success' => false, 'error' => "" . Config::get('app.generic_keywords.User') . "" . trans('customerController.id_not_found'), 'error_messages' => array('' . Config::get('app.generic_keywords.User') . "" . trans('customerController.id_not_found')), 'error_code' => 410);
				} else {
					$response_array = array('success' => false, 'error' => trans('customerController.token_not_valid'), 'error_messages' => array(trans('customerController.token_not_valid')), 'error_code' => 406);
				}
				$response_code = 200;
			}
		}
		$response = Response::json($response_array, $response_code);
		return $response;
	}

	public function get_future_request() {
		$token = Input::get('token');
		$user_id = Input::get('id');

		$validator = Validator::make(
						array(
					trans('customerController.token') => $token,
					trans('customerController.user_id') => $user_id,
						), array(
					trans('customerController.token') => 'required',
					trans('customerController.user_id') => 'required|integer',
						), array(
					trans('customerController.token') => '',
					trans('customerController.user_id') => trans('customerController.unique_id_missing'),
						)
		);
		if ($validator->fails()) {
			$error_messages = $validator->messages()->all();
			$response_array = array('success' => false, 'error' => trans('customerController.invalid_input'), 'error_code' => 401, 'error_messages' => $error_messages);
			$response_code = 200;
		} else {
			$is_admin = $this->isAdmin($token);
			$unit = "";
			$driver_data = "";
			// SEND REFERRAL & PROMO INFO
			$settings = Settings::where('key', 'referral_code_activation')->first();
			$referral_code_activation = $settings->value;
			if ($referral_code_activation) {
				$referral_code_activation_txt = trans('customerController.referral_on');
			} else {
				$referral_code_activation_txt = trans('customerController.referral_off');
			}

			$settings = Settings::where('key', 'promotional_code_activation')->first();
			$promotional_code_activation = $settings->value;
			if ($promotional_code_activation) {
				$promotional_code_activation_txt = trans('customerController.promo_on');
			} else {
				$promotional_code_activation_txt = trans('customerController.promo_off');
			}
			// SEND REFERRAL & PROMO INFO

			if ($user_data = $this->getUserData($user_id, $token, $is_admin)) {
				$all_scheduled_requests = array();
				$ScheduledRequests = ScheduledRequests::select('scheduled_requests.*', 'provider_type.name AS type_name', 'provider_type.icon AS type_icon')
								->leftJoin('provider_type', 'scheduled_requests.type', '=', 'provider_type.id')
								->where('user_id', $user_id)->orderBy('id', 'DESC')->get();
				foreach ($ScheduledRequests as $data1) {
					$data['id'] = $data1->id;
					$data['user_id'] = $data1->user_id;
					$data['latitude'] = $data1->latitude;
					$data['longitude'] = $data1->longitude;
					$data['dest_latitude'] = $data1->dest_latitude;
					$data['dest_longitude'] = $data1->dest_longitude;
					$data['time_zone'] = $data1->time_zone;
					$data['src_address'] = $data1->src_address;
					$data['dest_address'] = $data1->dest_address;
					$data['promo_code'] = $data1->promo_code;
					$data['promo_id'] = $data1->promo_id;
					$pay_mode_txt = trans('customerController.Card');
					if ($data1->payment_mode) {
						$pay_mode_txt = trans('customerController.Cash');
					}
					$data['payment_mode'] = $data1->payment_mode;
					$data['pay_mode_txt'] = $pay_mode_txt;
					$data['server_start_time'] = $data1->server_start_time;
					$data['start_time'] = $data1->start_time;
					$data['type_name'] = $data1->type_name;
					$data['type_icon'] = $data1->type_icon;
					if ($data1->dest_latitude != 0 || $data1->dest_longitude != 0) {
						$data['map_image'] = "https://maps-api-ssl.google.com/maps/api/staticmap?"
								. "size=249x249&"
								. "style=feature:landscape|visibility:off&"
								. "style=feature:poi|visibility:off&"
								. "style=feature:transit|visibility:off&"
								. "style=feature:road.highway|element:geometry|lightness:39&"
								. "style=feature:road.local|element:geometry|gamma:1.45&"
								. "style=feature:road|element:labels|gamma:1.22&"
								. "style=feature:administrative|visibility:off&"
								. "style=feature:administrative.locality|visibility:on&"
								. "style=feature:landscape.natural|visibility:on&"
								. "scale=2&"
								. "markers=shadow:false|scale:2|icon:http://d1a3f4spazzrp4.cloudfront.net/receipt-new/marker-start@2x.png|" . $data1->latitude . "," . $data1->longitude . "&"
								. "markers=shadow:false|scale:2|icon:http://d1a3f4spazzrp4.cloudfront.net/receipt-new/marker-finish@2x.png|" . $data1->dest_latitude . "," . $data1->dest_longitude . "";
					} else {
						$data['map_image'] = "https://maps-api-ssl.google.com/maps/api/staticmap?"
								. "size=249x249&"
								. "style=feature:landscape|visibility:off&"
								. "style=feature:poi|visibility:off&"
								. "style=feature:transit|visibility:off&"
								. "style=feature:road.highway|element:geometry|lightness:39&"
								. "style=feature:road.local|element:geometry|gamma:1.45&"
								. "style=feature:road|element:labels|gamma:1.22&"
								. "style=feature:administrative|visibility:off&"
								. "style=feature:administrative.locality|visibility:on&"
								. "style=feature:landscape.natural|visibility:on&"
								. "scale=2&"
								. "markers=shadow:false|scale:2|icon:http://d1a3f4spazzrp4.cloudfront.net/receipt-new/marker-start@2x.png|" . $data1->latitude . "," . $data1->longitude . "";
					}
					$data['is_running'] = 0;
					$data['provider'] = "";
					array_push($all_scheduled_requests, $data);
				}
				$request_data = array();
				$running_requests = Requests::select('request.id as request_id', 'request.req_create_user_time', 'request.dest_address as dest_address', 'request.src_address as source_address', 'request.is_completed as is_completed', 'request.is_started as is_started', 'request.is_provider_arrived as is_provider_arrived', 'request.is_provider_started as is_provider_started', 'request.confirmed_provider as confirmed_provider', 'request.D_latitude as dest_latitude', 'request.D_longitude as dest_longitude', 'request.latitude as src_latitude', 'request.longitude as src_longitude', 'user.id as user_id', 'user.first_name as user_first_name', 'user.last_name as user_last_name', 'user.phone as user_phone', 'user.email as user_email', 'user.picture as user_picture', 'user.bio as user_bio', 'user.address as user_address', 'user.state as user_state', 'user.country as user_country', 'user.zipcode as user_zipcode', 'user.rate as user_rate', 'user.rate_count as user_rate_count', 'provider.id as provider_id', 'provider.first_name as provider_first_name', 'provider.last_name as provider_last_name', 'provider.phone as provider_phone', 'provider.email as provider_email', 'provider.picture as provider_picture', 'provider.bio as provider_bio', 'provider.address as provider_address', 'provider.state as provider_state', 'provider.country as provider_country', 'provider.zipcode as provider_zipcode', 'provider.latitude as provider_latitude', 'provider.longitude as provider_longitude', 'provider.type as provider_type', 'provider.car_model as provider_car_model', 'provider.car_number as provider_car_number', 'provider.rate as provider_rate', 'provider.rate_count as provider_rate_count', 'provider.bearing as bearing')
						->leftJoin('user', 'request.user_id', '=', 'user.id')
						->leftJoin('provider', 'request.current_provider', '=', 'provider.id')
						->leftJoin('provider_type', 'provider.type', '=', 'provider_type.id')
						->where('request.user_id', '=', $user_id)
						->where('request.is_cancelled', '=', 0)
						->where('request.current_provider', '>', 0)
						->where('request.is_provider_rated', '=', 0)
						->orderBy('request.id', 'DESC')
						->get();
				foreach ($running_requests as $requests) {
					$data2['request_id'] = $requests->request_id;
					$data2['latitude'] = $requests->src_latitude;
					$data2['longitude'] = $requests->src_longitude;
					$data2['d_latitude'] = $requests->dest_latitude;
					$data2['d_longitude'] = $requests->dest_longitude;
					/* $data2['user']['user_id'] = $requests->user_id;
					  $data2['user']['user_lat'] = $requests->src_latitude;
					  $data2['user']['latitude'] = $requests->src_latitude;
					  $data2['user']['user_long'] = $requests->src_longitude;
					  $data2['user']['longitude'] = $requests->src_longitude;
					  $data2['user']['user_dist_lat'] = $requests->dest_latitude;
					  $data2['user']['d_latitude'] = $requests->dest_latitude;
					  $data2['user']['user_dist_long'] = $requests->dest_longitude;
					  $data2['user']['d_longitude'] = $requests->dest_longitude;
					  $data2['user']['first_name'] = $requests->user_first_name;
					  $data2['user']['last_name'] = $requests->user_last_name;
					  $data2['user']['phone'] = $requests->user_phone;
					  $data2['user']['email'] = $requests->user_email;
					  $data2['user']['picture'] = $requests->user_picture;
					  $data2['user']['bio'] = $requests->user_bio;
					  $data2['user']['address'] = $requests->user_address;
					  $data2['user']['state'] = $requests->user_state;
					  $data2['user']['country'] = $requests->user_country;
					  $data2['user']['zipcode'] = $requests->user_zipcode;
					  $data2['user']['rating'] = $requests->user_rate;
					  $data2['user']['num_rating'] = $requests->user_rate_count; */
					if ($requests->confirmed_provider) {
						$status = "" . Config::get('app.generic_keywords.Provider') . "" . trans('customerController.Confirm');
					}
					if ($requests->confirmed_provider == 0) {
						$status = "" . Config::get('app.generic_keywords.Provider') . "" . trans('customerController.not_yet_confirmed');
					}
					if ($requests->is_provider_started) {
						$status = "" . Config::get('app.generic_keywords.Provider') . "" . trans('customerController.started');
					}
					if ($requests->is_provider_arrived) {
						$status = "" . Config::get('app.generic_keywords.Provider') . "" . trans('customerController.Arrived');
					}

					if ($requests->is_started) {
						$status = "" . Config::get('app.generic_keywords.Trip') . "" . trans('customerController.started2');
					}
					if ($requests->is_completed) {
						$status = "" . Config::get('app.generic_keywords.Trip') . "" . trans('customerController.Completed');
					}
					$data2['provider']['id'] = $requests->provider_id;
					$data2['provider']['first_name'] = $requests->provider_first_name;
					$data2['provider']['last_name'] = $requests->provider_last_name;
					$data2['provider']['phone'] = $requests->provider_phone;
					$data2['provider']['email'] = $requests->provider_email;
					$data2['provider']['picture'] = $requests->provider_picture;
					$data2['provider']['bio'] = $requests->provider_bio;
					$data2['provider']['address'] = $requests->provider_address;
					$data2['provider']['state'] = $requests->provider_state;
					$data2['provider']['country'] = $requests->provider_country;
					$data2['provider']['zipcode'] = $requests->provider_zipcode;
					$data2['provider']['latitude'] = $requests->provider_latitude;
					$data2['provider']['longitude'] = $requests->provider_longitude;
					$data2['provider']['type'] = $requests->provider_type;
					$data2['provider']['rating'] = $requests->provider_rate;
					$data2['provider']['num_rating'] = $requests->provider_rate_count;
					$data2['provider']['car_model'] = $requests->provider_car_model;
					$data2['provider']['car_number'] = $requests->provider_car_number;
					$data2['provider']['bearing'] = $requests->bearing;
					$data2['request_id'] = $requests->request_id;
					$data2['src_address'] = $requests->source_address;
					$data2['dest_address'] = $requests->dest_address;
					$data2['confirmed_provider'] = $requests->confirmed_provider;
					$data2['is_provider_started'] = $requests->is_provider_started;
					$data2['is_provider_arrived'] = $requests->is_provider_arrived;
					$data2['is_request_started'] = $requests->is_started;
					$data2['is_completed'] = $requests->is_completed;
					$data2['is_provider_rated'] = $requests->is_provider_rated? 1: 0;
					$data2['is_cancelled'] = $requests->is_cancelled;

					//$data2['request_type'] = $request_services->type;
					$data2['status'] = 1;
					$data2['create_date_time'] = $requests->req_create_user_time;
					array_push($request_data, $data2);
				}
				// check for token validity
				if (is_token_active($user_data->token_expiry) || $is_admin) {
					$response_array = array(
						'success' => true,
						'is_referral_active' => $referral_code_activation,
						'is_referral_active_txt' => $referral_code_activation_txt,
						'is_promo_active' => $promotional_code_activation,
						'is_promo_active_txt' => $promotional_code_activation_txt,
						'all_scheduled_requests' => $all_scheduled_requests,
						'requests' => $request_data,
					);
					$response_code = 200;
				} else {
					$response_array = array(
						'success' => false,
						'error' => trans('customerController.token_expired'),
						'error_messages' => array(trans('customerController.token_expired')),
						'error_code' => 405,
						'is_referral_active' => $referral_code_activation,
						'is_referral_active_txt' => $referral_code_activation_txt,
						'is_promo_active' => $promotional_code_activation,
						'is_promo_active_txt' => $promotional_code_activation_txt,
						'all_scheduled_requests' => $all_scheduled_requests,
						'requests' => $request_data,
					);
					$response_code = 200;
				}
			} else {
				if ($is_admin) {
					/* $response_array = array('success' => false, 'error' => "" . $var->keyword . "" . trans('customerController.id_not_found'),'error_messages' => array('' . $var->keyword . "" . trans('customerController.id_not_found')), 'error_code' => 410); */
					$response_array = array('success' => false, 'error' => "" . Config::get('app.generic_keywords.User') . "" . trans('customerController.id_not_found'), 'error_messages' => array('' . Config::get('app.generic_keywords.User') . "" . trans('customerController.id_not_found')), 'error_code' => 410);
				} else {
					$response_array = array('success' => false, 'error' => trans('customerController.token_not_valid'), 'error_messages' => array(trans('customerController.token_not_valid')), 'error_code' => 406);
				}
				$response_code = 200;
			}
		}
		$response = Response::json($response_array, $response_code);
		return $response;
	}

	public function get_user_profile()
	{
		$token = Input::get('token');
		$user_id = Input::get('id');

		$validator = Validator::make(
						array(
					trans('customerController.token') => $token,
					trans('customerController.user_id') => $user_id,
						), array(
					trans('customerController.token') => 'required',
					trans('customerController.user_id') => 'required|integer',
						), array(
					trans('customerController.token') => '',
					trans('customerController.user_id') => trans('customerController.unique_id_missing'),
						)
		);
		if ($validator->fails()) {
			$error_messages = $validator->messages()->all();
			$response_array = array('success' => false, 'error' => trans('customerController.invalid_input'), 'error_code' => 401, 'error_messages' => $error_messages);
			$response_code = 200;
		} else {

			if ($user = User::find($user_id))
			{
				$response_array = array(
					'success' => true,
					'id' => $user->id,
					'email' => $user->email,
					'first_name' => $user->first_name,
					'last_name' =>  $user->last_name,
					'address'   =>  $user->address,
					'bio'       =>  $user->bio,
					'zipcode'   =>  $user->zipcode,
					'picture'   =>  $user->picture,
					'phone'     =>  $user->phone
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

	public function delete_future_request() {
		$token = Input::get('token');
		$user_id = Input::get('id');
		$request_id = Input::get('request_id');


	   
		$validator = Validator::make(
						array(
					trans('customerController.token') => $token,
					trans('customerController.user_id') => $user_id,
					trans('customerController.request_id') => $request_id,
						), array(
					trans('customerController.request_id') => 'required|integer',
					trans('customerController.token') => 'required',
					trans('customerController.user_id') => 'required|integer',
						), array(
					trans('customerController.request_id') => trans('customerController.id_request_required'),
					trans('customerController.token') => '',
					trans('customerController.user_id') => trans('customerController.unique_id_missing'),
						)
		);
		if ($validator->fails()) {
			$error_messages = $validator->messages()->all();
			$response_array = array('success' => false, 'error' => trans('customerController.invalid_input'), 'error_code' => 401, 'error_messages' => $error_messages);
			$response_code = 200;
		} else {
			$is_admin = $this->isAdmin($token);
			$unit = "";
			$driver_data = "";
			// SEND REFERRAL & PROMO INFO
			$settings = Settings::where('key', 'referral_code_activation')->first();
			$referral_code_activation = $settings->value;
			if ($referral_code_activation) {
				$referral_code_activation_txt = trans('customerController.referral_on');
			} else {
				$referral_code_activation_txt = trans('customerController.referral_off');
			}

			$settings = Settings::where('key', 'promotional_code_activation')->first();
			$promotional_code_activation = $settings->value;
			if ($promotional_code_activation) {
				$promotional_code_activation_txt = trans('customerController.promo_on');
			} else {
				$promotional_code_activation_txt = trans('customerController.promo_off');
			}
			// SEND REFERRAL & PROMO INFO

			if ($user_data = $this->getUserData($user_id, $token, $is_admin)) {
				$all_scheduled_requests = array();
				// check for token validity
				if (is_token_active($user_data->token_expiry) || $is_admin) {
					if ($request = ScheduledRequests::find($request_id)) {
						if ($request->user_id == $user_data->id) {
							if ($request->promo_id) {
								$promo_update_counter = PromoCodes::find($request->promo_id);
								$promo_update_counter->uses = $promo_update_counter->uses + 1;
								$promo_update_counter->save();

								UserPromoUse::where('user_id', '=', $user_id)->where('code_id', '=', $request->promo_id)->delete();

								$user = User::find($user_id);
								$user->promo_count = $user->promo_count - 1;
								$user->save();
							}
							ScheduledRequests::where('user_id', '=', $user_id)->where('id', '=', $request_id)->delete();
							$ScheduledRequests = ScheduledRequests::where('user_id', $user_id)->orderBy('id', 'DESC')->get();
							foreach ($ScheduledRequests as $data1) {
								$data['id'] = $data1->id;
								$data['user_id'] = $data1->user_id;
								$data['latitude'] = $data1->latitude;
								$data['longitude'] = $data1->longitude;
								$data['dest_latitude'] = $data1->dest_latitude;
								$data['dest_longitude'] = $data1->dest_longitude;
								$data['time_zone'] = $data1->time_zone;
								$data['src_address'] = $data1->src_address;
								$data['dest_address'] = $data1->dest_address;
								$data['promo_code'] = $data1->promo_code;
								$data['promo_id'] = $data1->promo_id;
								$pay_mode_txt = trans('customerController.Card');
								if ($data1->payment_mode) {
									$pay_mode_txt = trans('customerController.Cash');
								}
								$data['payment_mode'] = $data1->payment_mode;
								$data['pay_mode_txt'] = $pay_mode_txt;
								$data['server_start_time'] = $data1->server_start_time;
								$data['start_time'] = $data1->start_time;
								array_push($all_scheduled_requests, $data);
							}
							$response_array = array(
								'success' => true,
								'is_referral_active' => $referral_code_activation,
								'is_referral_active_txt' => $referral_code_activation_txt,
								'is_promo_active' => $promotional_code_activation,
								'is_promo_active_txt' => $promotional_code_activation_txt,
								'all_scheduled_requests' => $all_scheduled_requests,
							   
							);
							$response_code = 200;
						} else {
							$response_array = array(
								'success' => false,
								'error' => trans('customerController.request_id_doesnot_match') . Config::get('app.generic_keywords.User') . "" . trans('customerController.id'),
								'error_messages' => array(trans('customerController.request_id_doesnot_match') . Config::get('app.generic_keywords.User') . "" . trans('customerController.id')),
								'error_code' => 407,
								'is_referral_active' => $referral_code_activation,
								'is_referral_active_txt' => $referral_code_activation_txt,
								'is_promo_active' => $promotional_code_activation,
								'is_promo_active_txt' => $promotional_code_activation_txt,
							);
							$response_code = 200;
						}
					} else {
						$response_array = array(
							'success' => false,
							'error' => trans('customerController.request_id_not_found'),
							'error_messages' => array(trans('customerController.request_id_not_found')),
							'error_code' => 408,
							'is_referral_active' => $referral_code_activation,
							'is_referral_active_txt' => $referral_code_activation_txt,
							'is_promo_active' => $promotional_code_activation,
							'is_promo_active_txt' => $promotional_code_activation_txt,
						);
						$response_code = 200;
					}
				} else {
					$ScheduledRequests = ScheduledRequests::where('user_id', $user_id)->orderBy('id', 'DESC')->get();
					foreach ($ScheduledRequests as $data1) {
						$data['id'] = $data1->id;
						$data['user_id'] = $data1->user_id;
						$data['latitude'] = $data1->latitude;
						$data['longitude'] = $data1->longitude;
						$data['dest_latitude'] = $data1->dest_latitude;
						$data['dest_longitude'] = $data1->dest_longitude;
						$data['time_zone'] = $data1->time_zone;
						$data['src_address'] = $data1->src_address;
						$data['dest_address'] = $data1->dest_address;
						$data['promo_code'] = $data1->promo_code;
						$data['promo_id'] = $data1->promo_id;
						$pay_mode_txt = trans('customerController.Card');
						if ($data1->payment_mode) {
							$pay_mode_txt = trans('customerController.Cash');
						}
						$data['payment_mode'] = $data1->payment_mode;
						$data['pay_mode_txt'] = $pay_mode_txt;
						$data['server_start_time'] = $data1->server_start_time;
						$data['start_time'] = $data1->start_time;
						array_push($all_scheduled_requests, $data);
					}
					$response_array = array(
						'success' => false,
						'error' => trans('customerController.token_expired'),
						'error_messages' => array(trans('customerController.token_expired')),
						'error_code' => 405,
						'is_referral_active' => $referral_code_activation,
						'is_referral_active_txt' => $referral_code_activation_txt,
						'is_promo_active' => $promotional_code_activation,
						'is_promo_active_txt' => $promotional_code_activation_txt,
						'all_scheduled_requests' => $all_scheduled_requests,
					);
					$response_code = 200;
				}
			} else {
				if ($is_admin) {
					/* $response_array = array('success' => false, 'error' => "" . $var->keyword . "" . trans('customerController.id_not_found'),'error_messages' => array('' . $var->keyword . "" . trans('customerController.id_not_found')), 'error_code' => 410); */
					$response_array = array('success' => false, 'error' => "" . Config::get('app.generic_keywords.User') . "" . trans('customerController.id_not_found'), 'error_messages' => array('' . Config::get('app.generic_keywords.User') . "" . trans('customerController.id_not_found')), 'error_code' => 410);
				} else {
					$response_array = array('success' => false, 'error' => trans('customerController.token_not_valid'), 'error_messages' => array(trans('customerController.token_not_valid')), 'error_code' => 406);
				}
				$response_code = 200;
			}
		}
		$response = Response::json($response_array, $response_code);
		return $response;
	}

	public function create_request_later() {
		$token = Input::get('token');
		$user_id = Input::get('id');
		$latitude = Input::get('latitude');
		$longitude = Input::get('longitude');
		$date_time = Input::get('datetime');

		// dd(date('Y-m-d h:i:s', strtotime("$date_time + 2 hours")));


		$validator = Validator::make(
						array(
					trans('customerController.token') => $token,
					trans('customerController.user_id') => $user_id,
					trans('customerController.latitude') => $latitude,
					trans('customerController.longitude') => $longitude,
					trans('customerController.datetime') => $date_time,
						), array(
					trans('customerController.token') => 'required',
					trans('customerController.user_id') => 'required|integer',
					trans('customerController.latitude') => 'required',
					trans('customerController.longitude') => 'required',
					trans('customerController.datetime') => 'required',
						), array(
					trans('customerController.token') => '',
					trans('customerController.user_id') => trans('customerController.unique_id_missing'),
					trans('customerController.latitude') => trans('customerController.location_point_missing'),
					trans('customerController.longitude') => trans('customerController.location_point_missing'),
					trans('customerController.datetime') => trans('customerController.schedule_date_time_required'),
						)
		);

		if ($validator->fails()) {
			$error_messages = $validator->messages()->all();
			$response_array = array('success' => false, 'error' => trans('customerController.invalid_input'), 'error_code' => 401, 'error_messages' => $error_messages);
			$response_code = 200;
		} else {
			$is_admin = $this->isAdmin($token);

			if ($user_data = $this->getUserData($user_id, $token, $is_admin)) {
				// check for token validity
				if (is_token_active($user_data->token_expiry) || $is_admin) {
					// Do necessary operations


					if ($user_data->debt > 0) {

						$response_array = array('success' => false, 'error' => trans('customerController.you_already_in') . " \$$user->debt " . trans('customerController.debt'), 'error_messages' => array(trans('customerController.you_already_in') . " \$$user->debt " . trans('customerController.debt')), 'error_code' => 417);
						$response_code = 200;
						$response = Response::json($response_array, $response_code);
						return $response;
					}

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


						$typequery = "SELECT distinct provider_id from provider_services where type IN($type)";
						$typeproviders = DB::select(DB::raw($typequery));
						//Log::info('typeproviders = ' . print_r($typeproviders, true));
						foreach ($typeproviders as $key) {
							$types[] = $key->provider_id;
						}
						$typestring = implode(",", $types);
						//Log::info('typestring = ' . print_r($typestring, true));

						if ($typestring == '') {
							/* $driver = Keywords::where('id', 1)->first();
							  $response_array = array('success' => false, 'error' => trans('customerController.no') . "" . $driver->keyword . "" . trans('customerController.found_match_service_type'),'error_messages' => array(trans('customerController.no') . "" . $driver->keyword . "" . trans('customerController.found_match_service_type')), 'error_code' => 405); */
							$response_array = array('success' => false, 'error' => trans('customerController.no') . "" . Config::get('app.generic_keywords.Provider') . "" . trans('customerController.found_match_service_type'), 'error_messages' => array(trans('customerController.no') . "" . Config::get('app.generic_keywords.Provider') . "" . trans('customerController.found_match_service_type')), 'error_code' => 405);
							$response_code = 200;
							return Response::json($response_array, $response_code);
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
						$query1 = "SELECT provider.id, ROUND(" . $multiply . " * 3956 * acos( cos( radians('$latitude') ) * cos( radians(latitude) ) * cos( radians(longitude) - radians('$longitude') ) + sin( radians('$latitude') ) * sin( radians(latitude) ) ) ,8) as distance from provider where is_available = 1 and is_active = 1 and is_approved = 1 and ROUND((" . $multiply . " * 3956 * acos( cos( radians('$latitude') ) * cos( radians(latitude) ) * cos( radians(longitude) - radians('$longitude') ) + sin( radians('$latitude') ) * sin( radians(latitude) ) ) ) ,8) <= $distance and provider.id IN($typestring);";

						$ssstrings = DB::select(DB::raw($query1));
						foreach ($ssstrings as $ssstrin) {
							$ssstri[] = $ssstrin->id;
						}
						$ssstring = implode(",", $ssstri);

						$datewant = new DateTime($date_time);
						$datetime = $datewant->format('Y-m-d H:i:s');

						$dategiven = $datewant->sub(new DateInterval('P0Y0M0DT1H59M59S'))->format('Y-m-d H:i:s');
						$end_time = $datewant->add(new DateInterval('P0Y0M0DT1H59M59S'))->format('Y-m-d H:i:s');


						/* $setting = Settings::where('key', 'allow_calendar')->first();
						  if ($setting->value == 1)
						  $pvquery = "SELECT distinct provider_id from provider_availability where start <= '" . $datetime . "' and end >= '" . $datetime . "' and provider_id IN($ssstring) and provider_id NOT IN(SELECT confirmed_provider FROM request where request_start_time>='" . $dategiven . "' and request_start_time<='" . $end_time . "');";
						  else */
						$pvquery = "SELECT id from provider where id IN($ssstring) and id NOT IN(SELECT confirmed_provider FROM request where request_start_time>='" . $dategiven . "' and request_start_time<='" . $end_time . "');";
						$pvques = DB::select(DB::raw($pvquery));
						//  dd($pvques);
						$ssstr = array();
						foreach ($pvques as $ssstn) {
							$ssstr[] = $ssstn->provider_id;
						}
						$pvque = implode(",", $ssstr);
						$providers = array();
						if ($pvque) {
							$settings = Settings::where('key', 'default_distance_unit')->first();
							$unit = $settings->value;
							if ($unit == 0) {
								$multiply = 1.609344;
							} elseif ($unit == 1) {
								$multiply = 1;
							}
							$query = "SELECT provider.id, ROUND(" . $multiply . " * 3956 * acos( cos( radians('$latitude') ) * cos( radians(latitude) ) * cos( radians(longitude) - radians('$longitude') ) + sin( radians('$latitude') ) * sin( radians(latitude) ) ) ,8) as distance from provider where is_available = 1 and is_active = 1 and is_approved = 1 and ROUND((" . $multiply . " * 3956 * acos( cos( radians('$latitude') ) * cos( radians(latitude) ) * cos( radians(longitude) - radians('$longitude') ) + sin( radians('$latitude') ) * sin( radians(latitude) ) ) ) ,8) <= $distance and provider.id IN($typestring) and id IN($pvque) order by distance;";

							$providers = DB::select(DB::raw($query));
						}
						$provider_list = array();

						$user = User::find($user_id);
						$user->latitude = $latitude;
						$user->longitude = $longitude;
						$user->save();

						$request = new Requests;
						$request->user_id = $user_id;
						$request->request_start_time = $datetime;
						$request->latitude = $latitude;
						$request->longitude = $longitude;
						$request->later = 1;
						if (Input::has('cod')) {
							if (Input::get('cod') == 1) {
								$request->cod = 1;
							} else {
								$request->cod = 0;
							}
						}
						$request->save();

						$reqserv = new RequestServices;
						$reqserv->request_id = $request->id;
						$reqserv->type = $type;
						$reqserv->save();
					} else {
						$settings = Settings::where('key', 'default_search_radius')->first();
						$distance = $settings->value;
						$settings = Settings::where('key', 'default_distance_unit')->first();
						$unit = $settings->value;
						if ($unit == 0) {
							$multiply = 1.609344;
						} elseif ($unit == 1) {
							$multiply = 1;
						}
						$query1 = "SELECT provider.id, ROUND(" . $multiply . " * 3956 * acos( cos( radians('$latitude') ) * cos( radians(latitude) ) * cos( radians(longitude) - radians('$longitude') ) + sin( radians('$latitude') ) * sin( radians(latitude) ) ) ,8) as distance from provider where is_available = 1 and is_active = 1 and is_approved = 1 and ROUND((" . $multiply . " * 3956 * acos( cos( radians('$latitude') ) * cos( radians(latitude) ) * cos( radians(longitude) - radians('$longitude') ) + sin( radians('$latitude') ) * sin( radians(latitude) ) ) ) ,8) <= $distance;";

						$ssstrings = DB::select(DB::raw($query1));
						foreach ($ssstrings as $ssstrin) {
							$ssstri[] = $ssstrin->id;
						}
						$ssstring = implode(",", $ssstri);

						$datewant = new DateTime($date_time);
						$datetime = $datewant->format('Y-m-d H:i:s');

						$dategiven = $datewant->sub(new DateInterval('P0Y0M0DT1H59M59S'))->format('Y-m-d H:i:s');
						$end_time = $datewant->add(new DateInterval('P0Y0M0DT1H59M59S'))->format('Y-m-d H:i:s');

						/* $setting = Settings::where('key', 'allow_calendar')->first();
						  if ($setting->value == 1)
						  $pvquery = "SELECT distinct provider_id from provider_availability where start <= '" . $datetime . "' and end >= '" . $datetime . "' and provider_id IN($ssstring) and provider_id NOT IN(SELECT confirmed_provider FROM request where request_start_time>='" . $dategiven . "' and request_start_time<='" . $end_time . "');";
						  else */
						$pvquery = "SELECT id from provider where id IN($ssstring) and id NOT IN(SELECT confirmed_provider FROM request where request_start_time>='" . $dategiven . "' and request_start_time<='" . $end_time . "');";

						$pvques = DB::select(DB::raw($pvquery));

						$ssstr = array();
						foreach ($pvques as $ssstn) {
							$ssstr[] = $ssstn->provider_id;
						}
						$pvque = implode(",", $ssstr);
						$providers = array();
						if ($pvque) {
							$settings = Settings::where('key', 'default_distance_unit')->first();
							$unit = $settings->value;
							if ($unit == 0) {
								$multiply = 1.609344;
							} elseif ($unit == 1) {
								$multiply = 1;
							}
							$query = "SELECT provider.id, ROUND(" . $multiply . " * 3956 * acos( cos( radians('$latitude') ) * cos( radians(latitude) ) * cos( radians(longitude) - radians('$longitude') ) + sin( radians('$latitude') ) * sin( radians(latitude) ) ) ,8) as distance from provider where is_available = 1 and is_active = 1 and is_approved = 1 and ROUND((" . $multiply . " * 3956 * acos( cos( radians('$latitude') ) * cos( radians(latitude) ) * cos( radians(longitude) - radians('$longitude') ) + sin( radians('$latitude') ) * sin( radians(latitude) ) ) ) ,8) <= $distance and id IN($pvque) order by distance;";

							$providers = DB::select(DB::raw($query));
						}
						$provider_list = array();

						$user = User::find($user_id);
						$user->latitude = $latitude;
						$user->longitude = $longitude;
						$user->save();

						$request = new Requests;
						$request->user_id = $user_id;
						$request->request_start_time = $datetime;
						$request->latitude = $latitude;
						$request->longitude = $longitude;
						$request->save();

						$reqserv = new RequestServices;
						$reqserv->request_id = $request->id;
						$reqserv->save();
					}
					$i = 0;
					$first_provider_id = 0;
					if ($providers) {
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
						$req->save();
					}
					$settings = Settings::where('key', 'provider_timeout')->first();
					$time_left = $settings->value;

					// Send Notification
					$provider = Provider::find($first_provider_id);
					if ($provider) {
						$msg_array = array();
						$msg_array['unique_id'] = 3;
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
						$request_data['user']['rating'] = $user->rate;
						$request_data['user']['num_rating'] = $user->rate_count;
						/* $request_data['user']['rating'] = DB::table('review_user')->where('user_id', '=', $user->id)->avg('rating') ? : 0;
						  $request_data['user']['num_rating'] = DB::table('review_user')->where('user_id', '=', $user->id)->count(); */
						$date_want = new DateTime($date_time);
						$datetime1 = $date_want->format('Y-m-d H:i:s');
						$request_data['datetime'] = $datetime1;
						
						$msg_array['request_data'] = $request_data;

						$title = trans('customerController.new_request');
						$message = $msg_array;
						//Log::info('first_provider_id = ' . print_r($first_provider_id, true));
						//Log::info('New request = ' . print_r($message, true));
						/* don't do json_encode in above line because if */
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
					$subject = trans('customerController.ride_booking_request');
					email_notification(1, 'admin', $pattern, $subject, 'new_request', null);

					$response_array = array(
						'success' => true,
						'request_id' => $request->id,
					);
					$response_code = 200;
				} else {
					$response_array = array('success' => false, 'error' => trans('customerController.token_expired'), 'error_messages' => array(trans('customerController.token_expired')), 'error_code' => 405);
					$response_code = 200;
				}
			} else {
				if ($is_admin) {
					/* $var = Keywords::where('id', 2)->first();
					  $response_array = array('success' => false, 'error' => "" . $var->keyword . "" . trans('customerController.id_not_found'),'error_messages' => array('' . $var->keyword . "" . trans('customerController.id_not_found')), 'error_code' => 410); */
					$response_array = array('success' => false, 'error' => "" . Config::get('app.generic_keywords.User') . "" . trans('customerController.id_not_found'), 'error_messages' => array('' . Config::get('app.generic_keywords.User') . "" . trans('customerController.id_not_found')), 'error_code' => 410);
				} else {
					$response_array = array('success' => false, 'error' => trans('customerController.token_not_valid'), 'error_messages' => array(trans('customerController.token_not_valid')), 'error_code' => 406);
				}
				$response_code = 200;
			}
		}

		$response = Response::json($response_array, $response_code);
		return $response;
	}

	public function eta() {

		$secret = Input::get('secret');
		$token = Input::get('token');
		$user_id = Input::get('id');

		$validator = Validator::make(
					array(
					trans('customerController.secret') => $secret,
					trans('customerController.token') => $token,
					trans('customerController.user_id') => $user_id,
						), array(
					trans('customerController.secret') => 'required|integer',
					trans('customerController.token') => 'required',
					trans('customerController.user_id') => 'required|integer',
						), array(
					trans('customerController.secret') => trans('customerController.security_key_required'),
					trans('customerController.token') => '',
					trans('customerController.user_id') => trans('customerController.unique_id_missing'),
						)
		);

		if ($validator->fails()) {
			$error_messages = $validator->messages()->all();
			$response_array = array('success' => false, 'error' => trans('customerController.invalid_input'), 'error_code' => 401, 'error_messages' => $error_messages);
			$response_code = 200;
		} else {
			$is_admin = $this->isAdmin($token);

			if ($user_data = $this->getUserData($user_id, $token, $is_admin)) {
				// check for token validity
				if (is_token_active($user_data->token_expiry) || $is_admin) {
					// Do necessary operations

					$request = Requests::where('security_key', $secret)->first();
					if ($request) {

						if ($request->is_started == 0) {
							$provider = Provider::find($request->confirmed_provider);
							$distance = 0;
						} else {
							$provider = RequestLocation::where('request_id', $request->id)->orderBy('created_at', 'desc')->first();
							$distance = RequestLocation::where('request_id', $request->id)->max('distance');
						}

						$settings = Settings::where('key', 'default_distance_unit')->first();
						$unit = $settings->value;
						if ($unit == 0) {
							$unit_set = 'kms';
						} elseif ($unit == 1) {
							$unit_set = 'miles';
						}
						$distance = convert($distance, $unit);


						$response_array = array(
							'success' => true,
							'latitude' => $provider->latitude,
							'longitude' => $provider->longitude,
							'destination_latitude' => $request->D_latitude,
							'destination longitude' => $request->D_longitude,
							'distance' => (string) $distance,
							'unit' => $unit_set
						);

						$response_code = 200;
					} else {
						$response_array = array('success' => false, 'error' => trans('customerController.request_id_not_found'), 'error_messages' => array(trans('customerController.request_id_not_found')), 'error_code' => 408);
						$response_code = 200;
					}
				} else {
					$response_array = array('success' => false, 'error' => trans('customerController.token_expired'), 'error_messages' => array(trans('customerController.token_expired')), 'error_code' => 405);
					$response_code = 200;
				}
			} else {
				if ($is_admin) {
					/* $var = Keywords::where('id', 2)->first();
					  $response_array = array('success' => false, 'error' => "" . $var->keyword . "" . trans('customerController.id_not_found'),'error_messages' => array('' . $var->keyword . "" . trans('customerController.id_not_found')), 'error_code' => 410); */
					$response_array = array('success' => false, 'error' => "" . Config::get('app.generic_keywords.User') . "" . trans('customerController.id_not_found'), 'error_messages' => array('' . Config::get('app.generic_keywords.User') . "" . trans('customerController.id_not_found')), 'error_code' => 410);
				} else {
					$response_array = array('success' => false, 'error' => trans('customerController.token_not_valid'), 'error_messages' => array(trans('customerController.token_not_valid')), 'error_code' => 406);
				}
				$response_code = 200;
			}
		}


		$response = Response::json($response_array, $response_code);
		return $response;
	}

	public function check_promo_code() {
		$promo_code = Input::get('promo_code');
		$token = Input::get('token');
		$user_id = Input::get('id');

		$validator = Validator::make(
						array(
					trans('customerController.promo_code') => $promo_code,
					trans('customerController.token') => $token,
					trans('customerController.user_id') => $user_id,
						), array(
					trans('customerController.promo_code') => 'required',
					trans('customerController.token') => 'required',
					trans('customerController.user_id') => 'required|integer',
						), array(
					trans('customerController.promo_code') => trans('customerController.promotional_code_required'),
					trans('customerController.token') => '',
					trans('customerController.user_id') => trans('customerController.unique_id_missing'),
						)
		);

		if ($validator->fails()) {
			$error_messages = $validator->messages()->all();
			$response_array = array('success' => false, 'error' => trans('customerController.invalid_input'), 'error_code' => 401, 'error_messages' => $error_messages);
			$response_code = 200;
		} else {
			$is_admin = $this->isAdmin($token);

			if ($user_data = $this->getUserData($user_id, $token, $is_admin)) {
				// check for token validity
				if (is_token_active($user_data->token_expiry) || $is_admin) {
					$settings = Settings::where('key', 'promotional_code_activation')->first();
					$prom_act = $settings->value;
					if ($prom_act) {
						// check promo code
						$check_code = PromoCodes::where('coupon_code', $promo_code)->where('uses', '>', 0)->first();
						if ($check_code != NULL) {
							if ($check_code->state == 1 && date('Y-m-d H:i:s', strtotime($check_code->expiry)) > date('Y-m-d H:i:s') && date('Y-m-d H:i:s', strtotime($check_code->start_date)) <= date('Y-m-d H:i:s')) {
								if ($check_code->type == 1) {
									$discount = $check_code->value . " %";
								} elseif ($check_code->type == 2) {
									$discount = "$ " . $check_code->value;
								}
								$response_array = array('success' => true, 'discount' => $discount);
							} else {
								$response_array = array('success' => false, 'error' => trans('customerController.invalid_promo_code'), 'error_messages' => array(trans('customerController.invalid_promo_code')), 'error_code' => 418);
							}
						} else {
							$response_array = array('success' => false, 'error' => trans('customerController.invalid_promo_code'), 'error_messages' => array(trans('customerController.invalid_promo_code')), 'error_code' => 419);
						}
					} else {
						$response_array = array('success' => false, 'error' => trans('customerController.promotion_not_active'), 'error_messages' => array(trans('customerController.promotion_not_active')), 'error_code' => 419);
					}
				} else {
					$response_array = array('success' => false, 'error' => trans('customerController.request_id_not_found'), 'error_messages' => array(trans('customerController.request_id_not_found')), 'error_code' => 408);
				}
			} else {
				$response_array = array('success' => false, 'error' => trans('customerController.token_expired'), 'error_messages' => array(trans('customerController.token_expired')), 'error_code' => 405);
			}
			$response_code = 200;
		}
		$response = Response::json($response_array, $response_code);
		return $response;
	}

	public function payment_select() {
		/*
		 * 0=payment with credit card
		 * 1=payment with Cash
		 */
		$payment_opt = Input::get('payment_opt');
		$user_id = Input::get('id');

		$validator = Validator::make(
						array(
					trans('customerController.payment_select') => $payment_opt,
					trans('customerController.user_id') => $user_id,
						), array(
					trans('customerController.payment_select') => 'required',
					trans('customerController.user_id') => 'required|integer'
						), array(
					trans('customerController.payment_select') => trans('customerController.payment_type_required'),
					trans('customerController.user_id') => trans('customerController.unique_id_missing')
						)
		);
		//echo "test";

		if ($validator->fails()) {
			$error_messages = $validator->messages()->all();
			$response_array = array('success' => false, 'error' => trans('customerController.invalid_input'), 'error_code' => 401, 'error_messages' => $error_messages);
			$response_code = 200;
		} else {
			$request = Requests::where('user_id', '=', $user_id)->where('status', '=', 0)->orderBy('created_at', 'desc')->first();
			if ($request) {
				if (isset($request->id)) {
					/* $request = Requests::find($request->id);
					  $request->payment_mode = $payment_opt;
					  $request->save(); */
					Requests::where('id', $request->id)->update(array('payment_mode' => $payment_opt));

					/* User::where('id', $user_id)->update(array('payment_select' => $payment_opt)); */
					$response_array = array('success' => true, 'error' => 'update successfully', 'error_messages' => array('update successfully'), 'error_code' => 407);
					$response_code = 200;
				} else {
					$response_array = array('success' => false, 'error' => trans('customerController.payment_mode_not_updated'), 'error_messages' => array(trans('customerController.payment_mode_not_updated')), 'error_code' => 507);
					$response_code = 200;
				}
			} else {
				$response_array = array('success' => false, 'error' => trans('customerController.payment_mode_not_updated'), 'error_messages' => array(trans('customerController.payment_mode_not_updated')), 'error_code' => 507);
				$response_code = 200;
			}
		}


		$response = Response::json($response_array, $response_code);
		return $response;
	}

	public function get_provider_list() {

		$latitude = Input::get('usr_lat');
		$longitude = Input::get('user_long');

		$set = Settings::where('key', 'future_request_time')->first();

		$validator = Validator::make(
						array(
					trans('customerController.usr_lat') => $latitude,
					trans('customerController.user_long') => $longitude,
						), array(
					trans('customerController.usr_lat') => 'required',
					trans('customerController.user_long') => 'required',
						), array(
					trans('customerController.usr_lat') => trans('customerController.location_point_missing'),
					trans('customerController.user_long') => trans('customerController.location_point_missing'),
						)
		);

		if ($validator->fails()) {
			$error_messages = $validator->messages()->all();
			$response_array = array('success' => false, 'error' => trans('customerController.invalid_input'), 'error_code' => 401, 'error_messages' => $error_messages);
			$response_code = 200;
		} 
		else {


			$providers_list = Provider::getNearests($latitude, $longitude);

			$provider_data = array();


			if ($providers_list && count($providers_list) > 0) {
				//return "encontrou providers" ;
				foreach ($providers_list as $providers) {

					$provider_list = array();
					$provider_list['id'] 			= $providers->id;
					$provider_list['first_name'] 	= $providers->first_name;
					$provider_list['last_name'] 	= $providers->id;
					$provider_list['phone'] 		= $providers->phone;
					$provider_list['email'] 		= $providers->email;
					$provider_list['bio'] 			= $providers->bio;
					$provider_list['address'] 		= $providers->address;
					$provider_list['state'] 		= $providers->state;
					$provider_list['country'] 		= $providers->country;
					$provider_list['zipcode'] 		= $providers->zipcode;
					$provider_list['latitude'] 		= $providers->latitude;
					$provider_list['longitude'] 	= $providers->longitude;
					$provider_list['type'] 			= $providers->provider_type;
					$provider_list['car_model'] 	= $providers->car_model;
					$provider_list['car_number'] 	= $providers->car_number;
					$provider_list['bearing'] 		= $providers->bearing;

					array_push($provider_data, $provider_list);
				}

				if (!empty($provider_data)) {
					$response_array = array(
						'success' => true,
						'provider_list' => $provider_data,
						'schedule_time' => $set? $set->value : NULL
					);
				} else {
					$response_array = array(
						'success' => false,
						'error' => trans('customerController.no_provider_found'),
						'error_messages' => array(trans('customerController.no_provider_found')),
						'error_code' => 411,
						'provider_list' => $provider_data,
					);
				}
				$response_code = 200;
			}  // não encontrou providers
			else {
				//return "nao encontrou providers" ;
				$response_array = array(
					'success' => false,
					'error' => trans('customerController.no_provider_found'),
					'error_messages' => array(trans('customerController.no_provider_found')),
					'error_code' => 411,
					'provider_list' => $provider_data,
				);
				$response_code = 201;
			}
		} 
		
		$response = Response::json($response_array, $response_code);
		return $response;
	}

	public function user_set_destination() {
		$request_id = Input::get('request_id');
		$token = Input::get('token');
		$user_id = Input::get('id');
		$dest_lat = Input::get('dest_lat');
		$dest_long = Input::get('dest_long');
		$dest_address = trans('customerController.address_not_available');
		if (Input::has('dest_address')) {
			$dest_address = trim(Input::get('dest_address'));
		}

		$validator = Validator::make(
						array(
					trans('customerController.request_id') => $request_id,
					trans('customerController.token') => $token,
					trans('customerController.user_id') => $user_id,
					trans('customerController.dest_lat') => $dest_lat,
					trans('customerController.dest_long')  => $dest_long,
						), array(
					trans('customerController.request_id') => 'required|integer',
					trans('customerController.token') => 'required',
					trans('customerController.user_id') => 'required|integer',
					trans('customerController.dest_lat') => 'required',
					trans('customerController.dest_long')  => 'required',
						), array(
					trans('customerController.request_id') => trans('customerController.id_request_required'),
					trans('customerController.token') => '',
					trans('customerController.user_id') => trans('customerController.unique_id_missing'),
					trans('customerController.dest_lat') => trans('customerController.location_point_missing'),
					trans('customerController.dest_long')  => trans('customerController.location_point_missing'),
						)
		);
		if ($validator->fails()) {
			$error_messages = $validator->messages()->all();
			$response_array = array('success' => false, 'error' => trans('customerController.invalid_input'), 'error_code' => 401, 'error_messages' => $error_messages);
			$response_code = 200;
		} else {
			$is_admin = $this->isAdmin($token);
			if ($user_data = $this->getUserData($user_id, $token, $is_admin)) {
				// check for token validity
				if (is_token_active($user_data->token_expiry) || $is_admin) {
					// Do necessary operations
					if ($request = Requests::find($request_id)) {

						if ($request->user_id == $user_data->id) {
							Requests::where('id', $request_id)->update(array('D_latitude' => $dest_lat, 'D_longitude' => $dest_long, 'dest_address' => $dest_address));
							if ($request->current_provider) {
								$msg_array = array();
								$msg_array['request_id'] = $request_id;
								$msg_array['unique_id'] = 4;

								$last_destination = Requests::find($request_id);
								$user = User::find($user_id);
								$request_data = array();
								$request_data['user'] = array();
								$request_data['user']['name'] = $user->first_name . " " . $user->last_name;
								$request_data['user']['picture'] = $user->picture;
								$request_data['user']['phone'] = $user->phone;
								$request_data['user']['address'] = $user->address;
								$request_data['user']['latitude'] = $request->latitude;
								$request_data['user']['longitude'] = $request->longitude;
								$request_data['user']['dest_latitude'] = $last_destination->D_latitude;
								$request_data['user']['dest_longitude'] = $last_destination->D_longitude;
								$request_data['user']['rating'] = $user->rate;
								$request_data['user']['num_rating'] = $user->rate_count;

								$msg_array['request_data'] = $request_data;

								$title = trans('customerController.set_destination');
								$message = $msg_array;
								if ($request->confirmed_provider == $request->current_provider) {
									send_notifications($request->confirmed_provider, "provider", $title, $message);
								}
							}
							$response_array = array(
								'success' => true,
								'error' => trans('customerController.set_destination_success'),
								'error_messages' => array(trans('customerController.set_destination_success')),
							);
							$response_code = 200;
						} else {
							$response_array = array('success' => false, 'error' => trans('customerController.request_id_user_id'), 'error_messages' => array(trans('customerController.request_id_user_id')), 'error_code' => 407);
							$response_code = 200;
						}
					} else {
						$response_array = array('success' => false, 'error' => trans('customerController.request_id_not_found'), 'error_messages' => array(trans('customerController.request_id_not_found')), 'error_code' => 408);
						$response_code = 200;
					}
				} else {
					$response_array = array('success' => false, 'error' => trans('customerController.token_expired'), 'error_messages' => array(trans('customerController.token_expired')), 'error_code' => 405);
					$response_code = 200;
				}
			} else {
				if ($is_admin) {
					$response_array = array('success' => false, 'error' => trans('customerController.user_id_not_found'), 'error_messages' => array(trans('customerController.user_id_not_found')), 'error_code' => 410);
				} else {
					$response_array = array('success' => false, 'error' => trans('customerController.token_not_valid'), 'error_messages' => array(trans('customerController.token_not_valid')), 'error_code' => 406);
				}
				$response_code = 200;
			}
		}


		$response = Response::json($response_array, $response_code);
		return $response;
	}

	//atualiza no banco de dados o token do dispositivo usado pelo provedor para o envio de notificacoes
	public function update_device_token() {
		if (Request::isMethod('post')) {
			$token = Input::get('token');
			$user_id = Input::get('id');
			$device_token = Input::get('device_token');

			$validator = Validator::make(
							array(
								trans('customerController.token')  => $token,
								trans('customerController.user_id')  => $user_id,
								trans('customerController.device_token') => $device_token 
							),
							array(
								trans('customerController.token')  => 'required',
								trans('customerController.user_id')  => 'required|integer',
								trans('customerController.device_token') => 'required'
							),
							array(
								trans('customerController.token')  => '',
								trans('customerController.user_id')  => trans('customerController.unique_id_missing'),
								trans('customerController.device_token') => ''
							)
			);

			if ($validator->fails()) {
				$error_messages = $validator->messages()->all();
				$response_array = array('success' => false, 'error' => trans('customerController.invalid_input'), 'error_code' => 401, 'error_messages' => $error_messages);
				$response_code = 200;
			}else{

				if ($user_data = $this->getUserData($user_id, $token, false)) {
					// check for token validity
					if (is_token_active($user_data->token_expiry)) {
						$user = User::find($user_id);
						$user->device_token = $device_token;
						$user->save();

						$response_array = array(
							'success' => true
						);
					} else {
						$response_array = array('success' => false, 'error' => trans('customerController.token_expired'), 'error_messages' => array(trans('customerController.token_expired')), 'error_code' => 405);
						$response_code = 200;
					}
				} 
				else {
					$response_array = array('success' => false, 'error' => trans('customerController.token_not_valid'), 'error_messages' => array(trans('customerController.token_not_valid')), 'error_code' => 406);
				}
				$response_code = 200;
			}
		}
		$response = Response::json($response_array, $response_code);
		return $response;
	}

	//retorna informações das requisicões do usuário cujo pagamento não foi recebido 
	public function get_pending_debts() {
		$token = Input::get('token');
		$user_id = Input::get('id');

		$validator = Validator::make(
						array(
							trans('customerController.token')  => $token,
							trans('customerController.user_id')  => $user_id,
						),
						array(
							trans('customerController.token') => 'required',
							trans('customerController.user_id') => 'required|integer',
						),
						array(
							trans('customerController.token') => '',
							trans('customerController.user_id') => trans('customerController.unique_id_missing'),
						)
		);

		if ($validator->fails()) {
			$error_messages = $validator->messages()->all();
			$response_array = array('success' => false, 'error' => trans('customerController.invalid_input'), 'error_code' => 401, 'error_messages' => $error_messages);
			$response_code = 200;
		}

		else {
			$is_admin = $this->isAdmin($token);
			if ($user = $this->getUserData($user_id, $token, $is_admin)) {
				// check for token validity
				if (is_token_active($user->token_expiry) || $is_admin) {  
						$completed_request_in_debt = Requests::where('user_id', '=', $user->id)->where('is_completed', '=', 1)->where('is_paid', '=', 0)->first();                
						$canceled_request_in_debt = Requests::where('user_id', '=', $user->id)->where('is_cancelled', '=', 1)->where('is_cancel_fee_paid', '=', 0)->first();

						if($completed_request_in_debt){
							$pending_request = $completed_request_in_debt;
						}
						else{
							$pending_request = $canceled_request_in_debt;
						}
						
						if($pending_request){                  
							$request = array(
								'src_address' => $pending_request->src_address,
								'dest_address' => $pending_request->dest_address,
								'distance' => $pending_request->distance,
								'time' => $pending_request->time,
								'total' => $pending_request->total
							);


							$response_array = array(
								'success' => true,
								'total_in_debt' => $user->debt,
								'request' => $request
							);
						}
						else{
							$response_array = array(
								'success' => true,
								'total_in_debt' => $user->debt
							);
						}

						$response_code = 200;
			  
				}
				else {
					$response_array = array('success' => false, 'error' => trans('customerController.token_expired'), 'error_messages' => array(trans('customerController.token_expired')), 'error_code' => 405);
					$response_code = 200;
				}
			}

			else {
				if ($is_admin) {
					$response_array = array('success' => false, 'error' => "" . Config::get('app.generic_keywords.User') . "" . trans('customerController.id_not_found'), 'error_messages' => array('' . Config::get('app.generic_keywords.User') . "" . trans('customerController.id_not_found')), 'error_code' => 410);
				}
				else {
					$response_array = array('success' => false, 'error' => trans('customerController.token_not_valid'), 'error_messages' => array(trans('customerController.token_not_valid')), 'error_code' => 406);
				}
				$response_code = 200;
			}
		}


		$response = Response::json($response_array, $response_code);
		return $response;
	}

}