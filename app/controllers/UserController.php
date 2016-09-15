<?php

class UserController extends BaseController {

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

	public function get_braintree_token() {

		$token = Input::get('token');
		$user_id = Input::get('id');
		$validator = Validator::make(
						array(
					'token' => $token,
					'user_id' => $user_id,
						), array(
					'token' => 'required',
					'user_id' => 'required|integer'
						), array(
					'token' => '',
					'user_id' => trans('userController.unique_id_missing')
						)
		);

		if ($validator->fails()) {
			$error_messages = $validator->messages();
			$response_array = array('success' => false, 'error' => trans('userController.invalid_input'), 'error_messages' => $error_messages, 'error_code' => 401);
			$response_code = 200;
		} else {
			$is_admin = $this->isAdmin($token);
			if ($user_data = $this->getUserData($user_id, $token, $is_admin)) {
				// check for token validity
				if (is_token_active($user_data->token_expiry) || $is_admin) {
					if (Config::get('app.default_payment') == 'braintree') {

						Braintree_Configuration::environment(Config::get('app.braintree_environment'));
						Braintree_Configuration::merchantId(Config::get('app.braintree_merchant_id'));
						Braintree_Configuration::publicKey(Config::get('app.braintree_public_key'));
						Braintree_Configuration::privateKey(Config::get('app.braintree_private_key'));
						$clientToken = Braintree_ClientToken::generate();
						$response_array = array('success' => true, 'token' => $clientToken);
						$response_code = 200;
					} else {
						$response_array = array('success' => false, 'error' => trans('userController.braintree_change_default'), 'error_messages' => array(trans('userController.braintree_change_default')), 'error_code' => 440);
						$response_code = 200;
					}
				} else {
					$response_array = array('success' => false, 'error' => trans('userController.token_expired'), 'error_messages' => array(trans('userController.token_expired')), 'error_code' => 405);
					$response_code = 200;
				}
			} else {
				if ($is_admin) {
					/* $var = Keywords::where('id', 2)->first();
					  $response_array = array('success' => false, 'error' => "" . $var->keyword . ' ID is not Found','error_messages' => array('' . $var->keyword . ' ID is not Found'), 'error_code' => 410); */
					$response_array = array('success' => false, 'error' => trans('userController.id_of') . "" . Config::get('app.generic_keywords.User') . trans('userController.not_found'), 'error_messages' => array('error' => trans('userController.id_of') . "" . Config::get('app.generic_keywords.User') . trans('userController.not_found')), 'error_code' => 410);
				} else {
					$response_array = array('success' => false, 'error' => trans('userController.token_not_valid'), 'error_messages' => array(trans('userController.token_not_valid')), 'error_code' => 406);
				}
				$response_code = 200;
			}
		}

		$response = Response::json($response_array, $response_code);
		return $response;
	}

	public function apply_referral_code() {
		$referral_code = Input::get('referral_code');
		$token = Input::get('token');
		$user_id = Input::get('id');
		$is_skip = Input::get('is_skip');

		$validator = Validator::make(
						array(
					'token' => $token,
					'user_id' => $user_id,
					'is_skip' => $is_skip,
						), array(
					'token' => 'required',
					'user_id' => 'required|integer',
					'is_skip' => 'required',
						), array(
					'token' => '',
					'user_id' => trans('userController.unique_id_missing'),
					'is_skip' => '',
						)
		);

		if ($validator->fails()) {
			$error_messages = $validator->messages();
			$response_array = array('success' => false, 'error' => trans('userController.invalid_input'), 'error_code' => 401);
			$response_code = 200;
		} else {
			$is_admin = $this->isAdmin($token);
			if ($user_data = $this->getUserData($user_id, $token, $is_admin)) {
				// check for token validity
				if (is_token_active($user_data->token_expiry) || $is_admin) {
					if ($is_skip != 1) {
						if(Ledger::where('referral_code', $referral_code)->count() > 0){
							if ($ledger = Ledger::where('referral_code', $referral_code)->first()) {
								$referred_by = $ledger->user_id;
								if ($referred_by != $user_id) {
									if ($user_data->is_referee) {
										$user = User::find($user_id);
										$code_data = Ledger::where('user_id', '=', $user->id)->first();
										$response_array = array(
											'success' => false,
											'error' => trans('userController.refereel_already_used'),
											'error_code' => 405,
											'id' => $user->id,
											'first_name' => $user->first_name,
											'last_name' => $user->last_name,
											'phone' => $user->phone,
											'email' => $user->email,
											'picture' => $user->picture,
											'bio' => $user->bio,
											'address' => $user->address,
											'state' => $user->state,
											'country' => $user->country,
											'zipcode' => $user->zipcode,
											'login_by' => $user->login_by,
											'social_unique_id' => $user->social_unique_id,
											'device_token' => $user->device_token,
											'device_type' => $user->device_type,
											'token' => $user->token,
											'referral_code' => $code_data->referral_code,
											'is_referee' => $user->is_referee,
											'promo_count' => $user->promo_count,
										);
										$response_code = 200;
									} else {
										$settings = Settings::where('key', 'default_referral_bonus_to_refered_user')->first();
										$refered_user = $settings->value;

										$settings = Settings::where('key', 'default_referral_bonus_to_refereel')->first();
										$referral = $settings->value;

										$ledger = Ledger::find($ledger->id);
										$ledger->total_referrals = $ledger->total_referrals + 1;
										$ledger->amount_earned = $ledger->amount_earned + $refered_user;
										$ledger->save();

										$ledger1 = Ledger::where('user_id', $user_id)->first();
										$ledger1 = Ledger::find($ledger1->id);
										$ledger1->amount_earned = $ledger1->amount_earned + $referral;
										$ledger1->save();

										$user = User::find($user_id);
										$user->referred_by = $ledger->user_id;
										$user->is_referee = 1;
										$user->save();
										$user = User::find($user_id);
										$code_data = Ledger::where('user_id', '=', $user->id)->first();
										$response_array = array(
											'success' => true,
											'error' => trans('userController.referral_success_completed'),
											'id' => $user->id,
											'first_name' => $user->first_name,
											'last_name' => $user->last_name,
											'phone' => $user->phone,
											'email' => $user->email,
											'picture' => $user->picture,
											'bio' => $user->bio,
											'address' => $user->address,
											'state' => $user->state,
											'country' => $user->country,
											'zipcode' => $user->zipcode,
											'login_by' => $user->login_by,
											'social_unique_id' => $user->social_unique_id,
											'device_token' => $user->device_token,
											'device_type' => $user->device_type,
											'token' => $user->token,
											'referral_code' => $code_data->referral_code,
											'is_referee' => $user->is_referee,
											'promo_count' => $user->promo_count,
										);
										$response_code = 200;
									}
								} else {
									$user = User::find($user_id);
									$code_data = Ledger::where('user_id', '=', $user->id)->first();
									$response_array = array(
										'success' => false,
										'error' => trans('userController.refereel_not_apply'),
										'error_code' => 405,
										'id' => $user->id,
										'first_name' => $user->first_name,
										'last_name' => $user->last_name,
										'phone' => $user->phone,
										'email' => $user->email,
										'picture' => $user->picture,
										'bio' => $user->bio,
										'address' => $user->address,
										'state' => $user->state,
										'country' => $user->country,
										'zipcode' => $user->zipcode,
										'login_by' => $user->login_by,
										'social_unique_id' => $user->social_unique_id,
										'device_token' => $user->device_token,
										'device_type' => $user->device_type,
										'token' => $user->token,
										'referral_code' => $code_data->referral_code,
										'is_referee' => $user->is_referee,
										'promo_count' => $user->promo_count,
									);
									$response_code = 200;
								}
							} else {
								$user = User::find($user_id);
								$code_data = Ledger::where('user_id', '=', $user->id)->first();
								$response_array = array(
									'success' => false,
									'error' => trans('userController.invalid_referral_code'),
									'error_code' => 405,
									'id' => $user->id,
									'first_name' => $user->first_name,
									'last_name' => $user->last_name,
									'phone' => $user->phone,
									'email' => $user->email,
									'picture' => $user->picture,
									'bio' => $user->bio,
									'address' => $user->address,
									'state' => $user->state,
									'country' => $user->country,
									'zipcode' => $user->zipcode,
									'login_by' => $user->login_by,
									'social_unique_id' => $user->social_unique_id,
									'device_token' => $user->device_token,
									'device_type' => $user->device_type,
									'token' => $user->token,
									'referral_code' => $code_data->referral_code,
									'is_referee' => $user->is_referee,
									'promo_count' => $user->promo_count,
								);
								$response_code = 200;
							}
						} else {
							$response_array = array('success' => false, 'error' => trans('userController.referral_code_invalid'), 'error_code' => 401);
							$response_code = 200;
						}
					} else {
						$user = User::find($user_id);
						$user->is_referee = 1;
						$user->save();
						$user = User::find($user_id);
						$code_data = Ledger::where('user_id', '=', $user->id)->first();
						$response_array = array(
							'success' => true,
							'error' => trans('userController.skip_referral_process'),
							'id' => $user->id,
							'first_name' => $user->first_name,
							'last_name' => $user->last_name,
							'phone' => $user->phone,
							'email' => $user->email,
							'picture' => $user->picture,
							'bio' => $user->bio,
							'address' => $user->address,
							'state' => $user->state,
							'country' => $user->country,
							'zipcode' => $user->zipcode,
							'login_by' => $user->login_by,
							'social_unique_id' => $user->social_unique_id,
							'device_token' => $user->device_token,
							'device_type' => $user->device_type,
							'token' => $user->token,
							'referral_code' => $code_data->referral_code,
							'is_referee' => $user->is_referee,
							'promo_count' => $user->promo_count,
						);
						$response_code = 200;
					}
				} else {
					$response_array = array('success' => false, 'error' => trans('userController.token_expired'), 'error_code' => 405);
					$response_code = 200;
				}
			} else {
				if ($is_admin) {
					$response_array = array('success' => false, 'error' => trans('userController.user_id_not_found'), 'error_code' => 410);
				} else {
					$response_array = array('success' => false, 'error' => trans('userController.token_not_valid'), 'error_code' => 406);
				}
				$response_code = 200;
			}
		}

		$response = Response::json($response_array, $response_code);
		return $response;
	}

	public function apply_promo_code() {
		$promo_code = Input::get('promo_code');
		$token = Input::get('token');
		$user_id = Input::get('id');

		$validator = Validator::make(
						array(
					'token' => $token,
					'user_id' => $user_id,
						), array(
					'token' => 'required',
					'user_id' => 'required|integer',
						), array(
					'token' => '',
					'user_id' => trans('userController.unique_id_missing'),
						)
		);

		if ($validator->fails()) {
			$error_messages = $validator->messages();
			$response_array = array('success' => false, 'error' => trans('userController.invalid_input'), 'error_code' => 401);
			$response_code = 200;
		} else {
			$request_id = 0;
			$is_apply_on_trip = 0;
			$is_admin = $this->isAdmin($token);
			if ($user_data = $this->getUserData($user_id, $token, $is_admin)) {
				// check for token validity
				if (is_token_active($user_data->token_expiry) || $is_admin) {
					$request = Requests::where('user_id', '=', $user_id)->where('status', '=', 1)->where('is_completed', '=', 0)->where('is_cancelled', '=', 0)->orderBy('created_at', 'desc')->first();
					if ($request) {
						if (isset($request->id)) {
							if ($request->promo_id) {
								$response_array = array('success' => FALSE, 'error' => trans('userController.not_apply_code_multiple_trip'), 'error_code' => 411);
								$response_code = 200;
							} else {
								$settings = Settings::where('key', 'promotional_code_activation')->first();
								$prom_act = $settings->value;
								if ($prom_act) {
									if ($request->payment_mode == 0) {
										$settings = Settings::where('key', 'get_promotional_profit_on_card_payment')->first();
										$prom_act_card = $settings->value;
										if ($prom_act_card) {
											/* if ($ledger = PromotionalCodes::where('promo_code', $promo_code)->first()) { */
											if ($promos = PromoCodes::where('coupon_code', $promo_code)->where('uses', '>', 0)->where('state', '=', 1)->first()) {
												if ((date("Y-m-d H:i:s") >= date("Y-m-d H:i:s", strtotime(trim($promos->expiry)))) || (date("Y-m-d H:i:s") <= date("Y-m-d H:i:s", strtotime(trim($promos->start_date))))) {
													$response_array = array('success' => FALSE, 'error' => trans('userController.promotional_not_available'), 'error_code' => 505);
													$response_code = 200;
												} else {
													/* echo $promos->id;
													  echo $user_id;
													  $promo_is_used = 0; */
													$promo_is_used = UserPromoUse::where('user_id', '=', $user_id)->where('code_id', '=', $promos->id)->count();
													/* $promo_is_used = DB::table('user_promo_used')->where('user_id', '=', $user_id)->where('code_id', '=', $promos->id)->count(); */
													if ($promo_is_used) {
														$response_array = array('success' => FALSE, 'error' => trans('userController.promotional_already_used'), 'error_code' => 512);
														$response_code = 200;
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

														$request = Requests::find($request->id);
														$request->promo_id = $promos->id;
														$request->promo_code = $promos->coupon_code;
														$request->save();

														$user = User::find($user_id);
														$code_data = Ledger::where('user_id', '=', $user->id)->first();
														$response_array = array(
															'success' => true,
															'error' => trans('userController.promotional_applied'),
															'id' => $user->id,
															'first_name' => $user->first_name,
															'last_name' => $user->last_name,
															'phone' => $user->phone,
															'email' => $user->email,
															'picture' => $user->picture,
															'bio' => $user->bio,
															'address' => $user->address,
															'state' => $user->state,
															'country' => $user->country,
															'zipcode' => $user->zipcode,
															'login_by' => $user->login_by,
															'social_unique_id' => $user->social_unique_id,
															'device_token' => $user->device_token,
															'device_type' => $user->device_type,
															'token' => $user->token,
															'referral_code' => $code_data->referral_code,
															'is_referee' => $user->is_referee,
															'promo_count' => $user->promo_count,
															'request_id' => $request->id,
														);
														$response_code = 200;
													}
												}
											} else {
												$response_array = array('success' => FALSE, 'error' => trans('userController.promotional_not_available'), 'error_code' => 505);
												$response_code = 200;
											}
										} else {
											$response_array = array('success' => FALSE, 'error' => trans('userController.promotion_not_card'), 'error_code' => 505);
											$response_code = 200;
										}
									} else if ($request->payment_mode == 1) {
										$settings = Settings::where('key', 'get_promotional_profit_on_cash_payment')->first();
										$prom_act_cash = $settings->value;
										if ($prom_act_cash) {
											/* if ($ledger = PromotionalCodes::where('promo_code', $promo_code)->first()) { */
											if ($promos = PromoCodes::where('coupon_code', $promo_code)->where('uses', '>', 0)->where('state', '=', 1)->first()) {
												if ((date("Y-m-d H:i:s") >= date("Y-m-d H:i:s", strtotime(trim($promos->expiry)))) || (date("Y-m-d H:i:s") <= date("Y-m-d H:i:s", strtotime(trim($promos->start_date))))) {
													$response_array = array('success' => FALSE, 'error' => trans('userController.promotional_not_available'), 'error_code' => 505);
													$response_code = 200;
												} else {
													/* echo $promos->id;
													  echo $user_id;
													  $promo_is_used = 0; */
													$promo_is_used = UserPromoUse::where('user_id', '=', $user_id)->where('code_id', '=', $promos->id)->count();
													/* $promo_is_used = DB::table('user_promo_used')->where('user_id', '=', $user_id)->where('code_id', '=', $promos->id)->count(); */
													if ($promo_is_used) {
														$response_array = array('success' => FALSE, 'error' => trans('userController.promotional_already_used'), 'error_code' => 512);
														$response_code = 200;
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

														$request = Requests::find($request->id);
														$request->promo_id = $promos->id;
														$request->promo_code = $promos->coupon_code;
														$request->save();

														$user = User::find($user_id);
														$code_data = Ledger::where('user_id', '=', $user->id)->first();
														$response_array = array(
															'success' => true,
															'error' => trans('userController.promotional_applied'),
															'id' => $user->id,
															'first_name' => $user->first_name,
															'last_name' => $user->last_name,
															'phone' => $user->phone,
															'email' => $user->email,
															'picture' => $user->picture,
															'bio' => $user->bio,
															'address' => $user->address,
															'state' => $user->state,
															'country' => $user->country,
															'zipcode' => $user->zipcode,
															'login_by' => $user->login_by,
															'social_unique_id' => $user->social_unique_id,
															'device_token' => $user->device_token,
															'device_type' => $user->device_type,
															'token' => $user->token,
															'referral_code' => $code_data->referral_code,
															'is_referee' => $user->is_referee,
															'promo_count' => $user->promo_count,
															'request_id' => $request->id,
														);
														$response_code = 200;
													}
												}
											} else {
												$response_array = array('success' => FALSE, 'error' => trans('userController.promotional_not_available'), 'error_code' => 505);
												$response_code = 200;
											}
										} else {
											$response_array = array('success' => FALSE, 'error' => trans('userController.promotion_not_cash'), 'error_code' => 505);
											$response_code = 200;
										}
									} else {
										$response_array = array('success' => FALSE, 'error' => trans('userController.payment_paypal'), 'error_code' => 505);
										$response_code = 200;
									}
								} else {
									$response_array = array('success' => FALSE, 'error' => trans('userController.promotion_not_active'), 'error_code' => 505);
									$response_code = 200;
								}
							}
						} else {
							$response_array = array('success' => FALSE, 'error' => trans('userController.cannot_promo_without_request'), 'error_code' => 506);
							$response_code = 200;
						}
					} else {
						$response_array = array('success' => FALSE, 'error' => trans('userController.cannot_promo_without_request'), 'error_code' => 506);
						$response_code = 200;
					}
				} else {
					$response_array = array('success' => false, 'error' => trans('userController.token_expired'), 'error_code' => 405);
					$response_code = 200;
				}
			} else {
				if ($is_admin) {
					$response_array = array('success' => false, 'error' => trans('userController.user_id_not_found'), 'error_code' => 410);
				} else {
					$response_array = array('success' => false, 'error' => trans('userController.token_not_valid'), 'error_code' => 406);
				}
				$response_code = 200;
			}
		}

		$response = Response::json($response_array, $response_code);
		return $response;
	}

	public function apply_promo_code_valid() {
		$promo_code = Input::get('promo_code');
		$token = Input::get('token');
		$user_id = Input::get('id');

		$validator = Validator::make(
						array(
					'token' => $token,
					'user_id' => $user_id,
						), array(
					'token' => 'required',
					'user_id' => 'required|integer',
						), array(
					'token' => '',
					'user_id' => trans('userController.unique_id_missing'),
						)
		);

		if ($validator->fails()) {
			$error_messages = $validator->messages();
			$response_array = array('success' => false, 'error' => trans('userController.invalid_input'), 'error_code' => 401);
			$response_code = 200;
		} else {
			$is_apply_on_trip = 0;
			$is_admin = $this->isAdmin($token);
			if ($user_data = $this->getUserData($user_id, $token, $is_admin)) {
				// check for token validity
				if (is_token_active($user_data->token_expiry) || $is_admin) {

					if(PromoCodes::where('coupon_code', $promo_code)->count() > 0){
						$promos = PromoCodes::where('coupon_code', '=', $promo_code)->first();
					
						$promo_is_used = UserPromoUse::where('user_id', '=', $user_id)->where('code_id', '=', $promos->id)->count();

						if($promos->state != 1) {
							$response_array = array('success' => FALSE, 'error' => trans('userController.promotional_expired'), 'error_code' => 422);
							$response_code = 200;
						} elseif ($promo_is_used){
							$response_array = array('success' => FALSE, 'error' => trans('userController.promotional_already_used'), 'error_code' => 423);
							$response_code = 200;
						} else {
							$response_array = array(
								'success' => true,
							);
							$response_code = 200;
						}	
					} else {
						$response_array = array('success' => FALSE, 'error' => trans('userController.promotional_dont_exist'), 'error_code' => 421);
							$response_code = 200;
					}
							
				} else {
					$response_array = array('success' => false, 'error' => trans('userController.token_expired'), 'error_code' => 405);
					$response_code = 200;
				}
			} else {
				if ($is_admin) {
					$response_array = array('success' => false, 'error' => trans('userController.user_id_not_found'), 'error_code' => 410);
				} else {
					$response_array = array('success' => false, 'error' => trans('userController.token_not_valid'), 'error_code' => 406);
				}
				$response_code = 200;
			}
		}

		$response = Response::json($response_array, $response_code);
		return $response;
	}

	// test
	public function register() {
		$first_name = ucwords(trim(Input::get('first_name')));
		$last_name = ucwords(trim(Input::get('last_name')));
		$email = Input::get('email');
		$phone = Input::get('phone');
		$password = Input::get('password');
		$picture = "";
		if (Input::hasfile('picture')) {
			$picture = Input::file('picture');
		}
		$device_token = 0;
		if (Input::has('device_token')) {
			$device_token = Input::get('device_token');
		}
		$device_type = Input::get('device_type');
		$bio = "";
		if (Input::has('bio')) {
			$bio = Input::get('bio');
		}
		$address = "";
		if (Input::has('address')) {
			$address = ucwords(trim(Input::get('address')));
		}
		$state = "";
		if (Input::has('state')) {
			$state = ucwords(trim(Input::get('state')));
		}
		$country = "";
		if (Input::has('country')) {
			$country = ucwords(trim(Input::get('country')));
		}
		$zipcode = "";
		if (Input::has('zipcode')) {
			$zipcode = Input::get('zipcode');
		}
		$login_by = Input::get('login_by');
		$social_unique_id = trim(Input::get('social_unique_id'));

		if ($password != "" and $social_unique_id == "") {
			$validator = Validator::make(
							array(
						'password' => $password,
						'email' => $email,
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
						'login_by' => $login_by
							), array(
						'password' => 'required',
						'email' => 'required|email',
						'first_name' => 'required',
						'last_name' => 'required',
						/* 'picture' => 'mimes:jpeg,bmp,png', */
						'picture' => '',
						'device_token' => 'required',
						'device_type' => 'required|in:android,ios',
						'bio' => '',
						'address' => '',
						'state' => '',
						'country' => '',
						/* 'zipcode' => 'integer', */
						'login_by' => 'required|in:manual,facebook,google',
							), array(
						'password' => trans('userController.password_required'),
						'email' => trans('userController.email_required'),
						'first_name' => trans('userController.fname_required'),
						'last_name' => trans('userController.lname_required'),
						/* 'picture' => 'mimes:jpeg,bmp,png', */
						'picture' => '',
						'device_token' => '',
						'device_type' => '',
						'bio' => '',
						'address' => '',
						'state' => '',
						'country' => '',
						/* 'zipcode' => '', */
						'login_by' => '',
							)
			);

			$validatorPhone = Validator::make(
							array(
						'phone' => $phone,
							), array(
						'phone' => 'phone'
							), array(
						'phone' => trans('userController.phone_must_required')
							)
			);
		} elseif ($social_unique_id != "" and $password == "") {
			$validator = Validator::make(
							array(
						'email' => $email,
						'first_name' => $first_name,
						'last_name' => $last_name,
						'picture' => $picture,
						'device_token' => $device_token,
						'device_type' => $device_type,
						'bio' => $bio,
						'address' => $address,
						'state' => $state,
						'country' => $country,
						'zipcode' => $zipcode,
						'login_by' => $login_by,
						'social_unique_id' => $social_unique_id
							), array(
						'email' => 'required|email',
						'first_name' => 'required',
						'last_name' => 'required',
						/* 'picture' => 'mimes:jpeg,bmp,png', */
						'picture' => '',
						'device_token' => 'required',
						'device_type' => 'required|in:android,ios',
						'bio' => '',
						'address' => '',
						'state' => '',
						'country' => '',
						'zipcode' => 'integer',
						'login_by' => 'required|in:manual,facebook,google',
						'social_unique_id' => 'required|unique:user'
							), array(
						'email' => trans('userController.email_required'),
						'first_name' => trans('userController.fname_required'),
						'last_name' => trans('userController.lname_required'),
						/* 'picture' => 'mimes:jpeg,bmp,png', */
						'picture' => '',
						'device_token' => '',
						'device_type' => '',
						'bio' => '',
						'address' => '',
						'state' => '',
						'country' => '',
						'zipcode' => '',
						'login_by' => '',
						'social_unique_id' => trans('userController.social_unique_required')
							)
			);

			$validatorPhone = Validator::make(
							array(
						'phone' => $phone,
							), array(
						'phone' => 'phone',
							), array(
						'phone' => trans('userController.phone_must_required'),
							)
			);
		} elseif ($social_unique_id != "" and $password != "") {
			$response_array = array('success' => false, 'error' => trans('userController.invalid_social_password_passed'), 'error_code' => 401);
			$response_code = 200;
			goto response;
		}

		//verificar se social id já esta cadastrado
		if($social_unique_id != "" && DB::table('user')->where('social_unique_id', '=', $social_unique_id)->get()){
		  $response_array = array('success' => false, 'error' => trans('userController.social_id_already_registed'), 'error_code' => 418);
		  $response_code = 200;          
		} else if ($validator->fails()) {
			$error_messages = $validator->messages()->all();
			//Log::info('Error while during user registration = ' . print_r($error_messages, true));
			$response_array = array('success' => false, 'error' => trans('userController.invalid_input'), 'error_code' => 401, 'error_messages' => $error_messages);
			$response_code = 200;
		} else if ($validatorPhone->fails()) {
			$error_messages = $validator->messages()->all();
			$response_array = array('success' => false, 'error' => trans('userController.invalid_phone_number'), 'error_code' => 401, 'error_messages' => $error_messages);
			$response_code = 200;
		} else {

			if (User::where('email', '=', $email)->first()) {
				$response_array = array('success' => false, 'error' => trans('userController.email_already_registed'), 'error_code' => 402);
				$response_code = 200;
			} else {
				$settings = Settings::where('key', 'default_referral_bonus_to_refered_user')->first();
				$refered_user = $settings->value;
				$settings = Settings::where('key', 'default_referral_bonus_to_refereel')->first();
				$refereel_user = $settings->value;
				/* SEND REFERRAL & PROMO INFO */
				$settings = Settings::where('key', 'referral_code_activation')->first();
				$referral_code_activation = $settings->value;
				if ($referral_code_activation) {
					$referral_code_activation_txt = trans('userController.referral_on');
				} else {
					$referral_code_activation_txt = trans('userController.referral_off');
				}

				$settings = Settings::where('key', 'promotional_code_activation')->first();
				$promotional_code_activation = $settings->value;
				if ($promotional_code_activation) {
					$promotional_code_activation_txt = trans('userController.promo_on');
				} else {
					$promotional_code_activation_txt = trans('userController.promo_off');
				}
				/* SEND REFERRAL & PROMO INFO */
				$user = new User;
				$user->first_name = $first_name;
				$user->last_name = $last_name;
				$user->email = $email;
				$user->phone = $phone;
				if ($password != "") {
					$user->password = Hash::make($password);
				}
				$user->token = generate_token();
				$user->token_expiry = generate_expiry();

				// upload image
				$file_name = time();
				$file_name .= rand();
				$file_name = sha1($file_name);
				if ($picture) {
					$ext = Input::file('picture')->getClientOriginalExtension();
					Input::file('picture')->move(public_path() . "/uploads", $file_name . "." . $ext);
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
					$user->picture = $s3_url;
				}
				$user->device_token = $device_token;
				$user->device_type = $device_type;
				$user->bio = "";
				if (Input::has('bio'))
					$user->bio = $bio;
				$user->address = "";
				if (Input::has('address'))
					$user->address = $address;
				$user->state = "";
				if (Input::has('state'))
					$user->state = $state;
				$user->login_by = $login_by;
				$user->country = "";
				if (Input::has('country'))
					$user->country = $country;
				$user->zipcode = "0";
				if (Input::has('zipcode'))
					$user->zipcode = $zipcode;
				if ($social_unique_id != "") {
					$password = my_random6_number();
					$user->social_unique_id = $social_unique_id;
					$user->password = Hash::make($password);
				}
				$user->timezone = 'UTC';
				If (Input::has('timezone')) {
					$user->timezone = Input::get('timezone');
				}
				$user->is_referee = 0;
				$user->promo_count = 0;
				$user->save();


				/* $zero_in_code = Config::get('app.referral_zero_len') - strlen($user->id);
				  $referral_code = Config::get('app.referral_prefix');
				  for ($i = 0; $i < $zero_in_code; $i++) {
				  $referral_code .= "0";
				  }
				  $referral_code .= $user->id; */
				regenerate:
				$referral_code = my_random6_number();
				if (Ledger::where('referral_code', $referral_code)->count()) {
					goto regenerate;
				}
				/* Referral entry */
				$ledger = new Ledger;
				$ledger->user_id = $user->id;
				$ledger->referral_code = $referral_code;
				$ledger->save();
				if ($social_unique_id != "") {
					$pattern = trans('userController.hello_') . "" . ucwords($first_name) . "" . trans('userController.your') . "" . Config::get('app.website_title') . "" . trans('userController.web_login_password') . "" . $password;
					sms_notification($user->id, 'user', $pattern);
					$subject = "" . trans('userController.your') . "" . Config::get('app.website_title') . "" . trans('userController.web_login_password2');
					email_notification($user->id, 'user', $pattern, $subject);
				}

				if ($user->picture == NULL) {
					$user_picture = "";
				} else {
					$user_picture = $user->picture;
				}
				if ($user->bio == NULL) {
					$user_bio = "";
				} else {
					$user_bio = $user->bio;
				}
				if ($user->address == NULL) {
					$user_address = "";
				} else {
					$user_address = $user->address;
				}
				if ($user->state == NULL) {
					$user_state = "";
				} else {
					$user_state = $user->state;
				}
				if ($user->country == NULL) {
					$user_country = "";
				} else {
					$user_country = $user->country;
				}
				if ($user->zipcode == NULL) {
					$user_zipcode = "";
				} else {
					$user_zipcode = $user->zipcode;
				}
				if ($user->timezone == NULL) {
					$user_time = Config::get('app.timezone');
				} else {
					$user_time = $user->timezone;
				}
				$settings = Settings::where('key', 'admin_email_address')->first();
				$admin_email = $settings->value;
				$pattern = array('admin_eamil' => $admin_email, 'name' => ucwords($user->first_name . " " . $user->last_name), 'web_url' => web_url());
				$subject = trans('userController.welcome_to') . "" . ucwords(Config::get('app.website_title')) . ", " . ucwords($user->first_name . " " . $user->last_name) . "";
				email_notification($user->id, 'user', $pattern, $subject, 'user_new_register', null);
				$response_array = array(
					'success' => true,
					'id' => $user->id,
					'first_name' => $user->first_name,
					'last_name' => $user->last_name,
					'phone' => $user->phone,
					'email' => $user->email,
					'picture' => $user_picture,
					'bio' => $user_bio,
					'address' => $user_address,
					'state' => $user_state,
					'country' => $user_country,
					'zipcode' => $user_zipcode,
					'login_by' => $user->login_by,
					'social_unique_id' => $user->social_unique_id ? $user->social_unique_id : "",
					'device_token' => $user->device_token,
					'device_type' => $user->device_type,
					'timezone' => $user_time,
					'token' => $user->token,
					'referral_code' => $referral_code,
					'is_referee' => $user->is_referee,
					'promo_count' => $user->promo_count,
					'is_referral_active' => $referral_code_activation,
					'is_referral_active_txt' => $referral_code_activation_txt,
					'is_promo_active' => $promotional_code_activation,
					'is_promo_active_txt' => $promotional_code_activation_txt,
					'refered_user_bonus' => Config::get('app.generic_keywords.Currency') . " " . sprintf2($refered_user, 2),
					'refereel_user_bonus' => Config::get('app.generic_keywords.Currency') . " " . sprintf2($refereel_user, 2),
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
						'password' => trans('userController.password_required'),
						'email' => trans('userController.email_required'),
						'device_token' => trans('userController.push_token_required'),
						'device_type' => trans('userController.device_ios_android'),
						'login_by' => trans('userController.login_type_required')
							)
			);

			if ($validator->fails()) {
				$error_messages = $validator->messages()->all();
				$response_array = array('success' => false, 'error' => trans('userController.username_password_invalid'), 'error_code' => 401, 'error_messages' => $error_messages);
				$response_code = 200;
				//Log::error('Validation error during manual login for user = ' . print_r($error_messages, true));
			} else {
				if ($user = User::where('email', '=', $email)->first()) {
					if (Hash::check($password, $user->password)) {
						if ($login_by !== "manual") {
							$response_array = array('success' => false, 'error' => trans('userController.login_mismatch'), 'error_code' => 417);
							$response_code = 200;
						} else {
							User::where('id', '!=', $user->id)->where('device_token', '=', $device_token)->update(array('device_token' => 0));
							/* if ($user->device_type != $device_type) { */
							$user->device_type = $device_type;
							/* }
							  if ($user->device_token != $device_token) { */
							$user->device_token = $device_token;
							/* } */
							$user->token = generate_token();
							$user->token_expiry = generate_expiry();
							$user->save();
							/* SEND REFERRAL & PROMO INFO */
							/*
							$settings = Settings::where('key', 'referral_code_activation')->first();
							$referral_code_activation = $settings->value;
							if ($referral_code_activation) {
								$referral_code_activation_txt = trans('userController.referral_on');
							} else {
								$referral_code_activation_txt = trans('userController.referral_off');
							}

							$settings = Settings::where('key', 'promotional_code_activation')->first();
							$promotional_code_activation = $settings->value;
							if ($promotional_code_activation) {
								$promotional_code_activation_txt = trans('userController.promo_on');
							} else {
								$promotional_code_activation_txt = trans('userController.promo_off');
							}
							// SEND REFERRAL & PROMO INFO 
							$code_data = Ledger::where('user_id', '=', $user->id)->first();
							$settings = Settings::where('key', 'default_referral_bonus_to_refered_user')->first();
							$refered_user = $settings->value; // 7
							$settings = Settings::where('key', 'default_referral_bonus_to_refereel')->first();
							$refereel_user = $settings->value; // 3
							*/
							$response_array = array(
								'success' => true,
								'id' => $user->id,
								'first_name' => $user->first_name,
								'last_name' => $user->last_name,
								'phone' => $user->phone,
								'email' => $user->email,
								'picture' => $user->picture,
								'bio' => $user->bio,
								'address' => $user->address,
								'state' => $user->state,
								'country' => $user->country,
								'zipcode' => $user->zipcode,
								'login_by' => $user->login_by,
								'social_unique_id' => $user->social_unique_id,
								'device_token' => $user->device_token,
								'device_type' => $user->device_type,
								'timezone' => $user->timezone,
								'token' => $user->token,
								//'referral_code' => $code_data->referral_code,
								'is_referee' => $user->is_referee,
								'promo_count' => $user->promo_count,
								//'is_referral_active' => $referral_code_activation,
								//'is_referral_active_txt' => $referral_code_activation_txt,
								//'is_promo_active' => $promotional_code_activation,
								//'is_promo_active_txt' => $promotional_code_activation_txt,
								//'refered_user_bonus' => Config::get('app.generic_keywords.Currency') . " " . sprintf2($refered_user, 2),
								//'refereel_user_bonus' => Config::get('app.generic_keywords.Currency') . " " . sprintf2($refereel_user, 2),
							);

							$response_code = 200;
						}
					} else {
						$response_array = array('success' => false, 'error' => trans('userController.username_password_and_invalid'), 'error_code' => 403);
						$response_code = 200;
					}
				} else {
					$response_array = array('success' => false, 'error' => trans('userController.not_registered_user'), 'error_code' => 404);
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
						'social_unique_id' => 'required|exists:user,social_unique_id',
						'device_token' => 'required',
						'device_type' => 'required|in:android,ios',
						'login_by' => 'required|in:manual,facebook,google'
							), array(
						'social_unique_id' => trans('userController.social_unique_required'),
						'device_token' => trans('userController.push_token_required'),
						'device_type' => trans('userController.device_ios_android'),
						'login_by' => trans('userController.login_type_required')
							)
			);

			if ($socialValidator->fails()) {
				$error_messages = $socialValidator->messages();
				//Log::error('Validation error during social login for user = ' . print_r($error_messages, true));
				$error_messages = $socialValidator->messages()->all();
				$response_array = array('success' => false, 'error' => trans('userController.invalid_input'), 'error_code' => 401, 'error_messages' => $error_messages);
				$response_code = 200;
			} else {
				if ($user = User::where('social_unique_id', '=', $social_unique_id)->first()) {
					if (!in_array($login_by, array('facebook', 'google'))) {
						$response_array = array('success' => false, 'error' => trans('userController.login_mismatch'), 'error_code' => 417);
						$response_code = 200;
					} else {
						if ($user->device_type != $device_type) {
							$user->device_type = $device_type;
						}
						if ($user->device_token != $device_token) {
							$user->device_token = $device_token;
						}
						$user->token_expiry = generate_expiry();
						$user->save();

						$response_array = array(
							'success' => true,
							'id' => $user->id,
							'first_name' => $user->first_name,
							'last_name' => $user->last_name,
							'phone' => $user->phone,
							'email' => $user->email,
							'picture' => $user->picture,
							'bio' => $user->bio,
							'address' => $user->address,
							'state' => $user->state,
							'country' => $user->country,
							'zipcode' => $user->zipcode,
							'is_referee' => $user->is_referee,
							'login_by' => $user->login_by,
							'social_unique_id' => $user->social_unique_id,
							'device_token' => $user->device_token,
							'device_type' => $user->device_type,
							'timezone' => $user->timezone,
							'token' => $user->token,
						);

						$response_code = 200;
					}
				} else {
					$response_array = array('success' => false, 'error' => trans('userController.not_registered_user_social'), 'error_code' => 404);
					$response_code = 200;
				}
			}
		} else {
			$response_array = array('success' => false, 'error' => trans('userController.invalid_input'), 'error_code' => 404);
			$response_code = 200;
		}

		$response = Response::json($response_array, $response_code);
		return $response;
	}

	public function details() {
		if (Request::isMethod('post')) {
			$address = Input::get('address');
			$state = Input::get('state');
			$zipcode = Input::get('zipcode');
			$token = Input::get('token');
			$user_id = Input::get('id');

			$validator = Validator::make(
							array(
						'address' => $address,
						'state' => $state,
						'zipcode' => $zipcode,
						'token' => $token,
						'user_id' => $user_id,
							), array(
						'address' => 'required',
						'state' => 'required',
						'zipcode' => 'required|integer',
						'token' => 'required',
						'user_id' => 'required|integer'
							), array(
						'address' => '',
						'state' => '',
						'zipcode' => '',
						'token' => '',
						'user_id' => trans('userController.unique_id_missing')
							)
			);

			if ($validator->fails()) {
				$error_messages = $validator->messages()->all();
				$response_array = array('success' => false, 'error' => trans('userController.invalid_input'), 'error_code' => 401, 'error_messages' => $error_messages);
				$response_code = 200;
			} else {
				$is_admin = $this->isAdmin($token);
				if ($user_data = $this->getUserData($user_id, $token, $is_admin)) {
					// check for token validity
					if (is_token_active($user_data->token_expiry) || $is_admin) {
						// Do necessary operations

						$user = User::find($user_data->id);
						$user->address = $address;
						$user->state = $state;
						$user->zipcode = $zipcode;
						$user->save();

						$response_array = array('success' => true);
						$response_code = 200;
					} else {
						$response_array = array('success' => false, 'error' => trans('userController.token_expired'), 'error_code' => 405);
						$response_code = 200;
					}
				} else {
					if ($is_admin) {
						/* $var = Keywords::where('id', 2)->first();
						  $response_array = array('success' => false, 'error' => "" . $var->keyword . ' ID not Found', 'error_code' => 410); */
						$response_array = array('success' => false, 'error' => trans('userController.id_of') . "" . Config::get('app.generic_keywords.User') . "" . trans('userController.not_found'), 'error_code' => 410);
					} else {
						$response_array = array('success' => false, 'error' => trans('userController.token_not_valid'), 'error_code' => 406);
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
						'token' => $token,
						'user_id' => $user_id,
							), array(
						'token' => 'required',
						'user_id' => 'required|integer'
							), array(
						'token' => '',
						'user_id' => trans('userController.unique_id_missing')
							)
			);

			if ($validator->fails()) {
				$error_messages = $validator->messages()->all();
				$response_array = array('success' => false, 'error' => trans('userController.invalid_input'), 'error_code' => 401, 'error_messages' => $error_messages);
				$response_code = 200;
			} else {

				$is_admin = $this->isAdmin($token);
				if ($user_data = $this->getUserData($user_id, $token, $is_admin)) {
					// check for token validity
					if (is_token_active($user_data->token_expiry) || $is_admin) {

						$response_array = array(
							'success' => true,
							'address' => $user_data->address,
							'state' => $user_data->state,
							'zipcode' => $user_data->zipcode,
						);
						$response_code = 200;
					} else {
						$response_array = array('success' => false, 'error' => trans('userController.token_expired'), 'error_code' => 405);
						$response_code = 200;
					}
				} else {
					if ($is_admin) {
						/* $var = Keywords::where('id', 2)->first();
						  $response_array = array('success' => false, 'error' => "" . $var->keyword . ' ID not Found', 'error_code' => 410); */
						$response_array = array('success' => false, 'error' => trans('userController.id_of') . "" . Config::get('app.generic_keywords.User') . "" . trans('userController.not_found'), 'error_code' => 410);
					} else {
						$response_array = array('success' => false, 'error' => trans('userController.token_not_valid'), 'error_code' => 406);
					}
					$response_code = 200;
				}
			}
		}
		$response = Response::json($response_array, $response_code);
		return $response;
	}

	public function addcardtoken() {
		$payment_token = Input::get('payment_token');
		$last_four = Input::get('last_four');
		$token = Input::get('token');
		$user_id = Input::get('id');

		$card_holder = Input::get('card_holder');
		$card_number = Input::get('card_number');
		$card_cvv = Input::get('card_cvv');
		$card_expiration_month = Input::get('card_expiration_month');
		$card_expiration_year = Input::get('card_expiration_year');

		if (Input::has('card_type')) {
			$card_type = strtoupper(Input::get('card_type'));
		} else {
			$card_type = strtoupper("VISA");
		}

		if (Config::get('app.default_payment') == RequestCharging::PAYMENT_GATEWAY_PAGARME){
			$validator = Validator::make(
					array(
						'user_id' => $user_id,
						'card_holder' => $card_holder,
						'card_number' => $card_number,
						'card_expiration_year' => $card_expiration_year,
						'card_expiration_month' => $card_expiration_month,
						'card_cvv' => $card_cvv,
						'token' => $token,						
					),
					array(
						'user_id' => 'required|integer',
						'card_holder' => 'required',
						'card_number' => 'required',
						'card_expiration_year' => 'required',
						'card_expiration_month' => 'required',
						'card_cvv' => 'required',
						'token' => 'required',
						
					),
					array(
						'user_id' => trans('userController.unique_id_missing'),
						'card_holder' => '',
						'card_number' => '',
						'card_expiration_year' => '',
						'card_expiration_month' => '',
						'card_cvv' => '',
						'token' => '',
					)
				);
		}

		else{
			$validator = Validator::make(
							array(
						'last_four' => $last_four,
						'payment_token' => $payment_token,
						'token' => $token,
						'user_id' => $user_id,
							), array(
						'last_four' => 'required',
						'payment_token' => 'required',
						'token' => 'required',
						'user_id' => 'required|integer'
							), array(
						'last_four' => trans('userController.card_last_four_number'),
						'payment_token' => trans('userController.unique_payment_token_missing'),
						'token' => '',
						'user_id' => trans('userController.unique_id_missing')
							)
			);
		}
		$payments = array();

		if ($validator->fails()) {
			$error_messages = $validator->messages();
			$response_array = array('success' => false, 'error' => trans('userController.invalid_input'), 'error_code' => 401, 'payments' => $payments);
			$response_code = 200;
		} else {
			$is_admin = $this->isAdmin($token);
			if ($user_data = $this->getUserData($user_id, $token, $is_admin)) {
				// check for token validity
				if (is_token_active($user_data->token_expiry) || $is_admin) {

					try {

						if (Config::get('app.default_payment') == RequestCharging::PAYMENT_GATEWAY_PAGARME){
							//obter dados do cartão
							$card_number = str_replace('-', '', Input::get('card_number'));
							$card_expiration_month = Input::get('card_expiration_month');
							$card_expiration_year = Input::get('card_expiration_year');
							$card_cvv = Input::get('card_cvv');

							//salvar dados de cartão no pagar.me
							PagarMe::setApiKey(Config::get('app.pagarme_api_key'));
							
						   	$card = new PagarMe_Card(array(
						        "card_number" => $card_number,
						        "card_holder_name" => $card_holder,
						        "card_expiration_month" => str_pad($card_expiration_month, 2, '0', STR_PAD_LEFT),
						        "card_expiration_year" => str_pad($card_expiration_year, 2, '0', STR_PAD_LEFT),
						        "card_cvv" => $card_cvv,
						    ));
					   		$card->create();
 													    				   	

					   		//salvar dados do cartão no banco de dados
							$payment = new Payment;
							$payment->user_id = $user_id;
							$payment->customer_id = $user_id;
							$payment->last_four = $card->last_digits;
							$payment->card_type = $card->brand;
							$payment->card_token = $card->id;

							//definir cartão como padrão
							$card_count = DB::table('payment')->where('user_id', '=', $payment->user_id)->where('is_default', '=', 1)->count();
							if ($card_count > 0) {
								$payment->is_default = 0;
							}
							else {
								$payment->is_default = 1;
							}

							$payment->save();
							
							//se o usuário tiver viagens com pagamento pendente, tentar cobrá-las
							if($user_data->debt > 0){
								RequestCharging::charge_user_debts($user_data->id, $payment->id);
							}
							else{
								//se o usuário não tiver um cartão principal, realizar cobrança de teste
								$default_card_count = DB::table('payment')->where('user_id', '=', $user_id)->where('is_default', '=', 1)->where('is_active', '=', 1)->count();
								if($default_card_count == 0){
									RequestCharging::card_test_charge($payment->id);
								}
							}

							$payment = Payment::find($payment->id);
							
							$payment_data = Payment::where('user_id', $user_id)->orderBy('is_default', 'DESC')->get();
							foreach ($payment_data as $data1) {
								$default = $data1->is_default;
								if ($default == 1) {
									$data['is_default_text'] = "default";
								} else {
									$data['is_default_text'] = "not_default";
								}
								$data['id'] = $data1->id;
								$data['user_id'] = $data1->user_id;
								$data['customer_id'] = $data1->customer_id;
								$data['last_four'] = $data1->last_four;
								$data['card_token'] = $data1->card_token;
								$data['card_type'] = $data1->card_type;
								$data['card_id'] = $data1->card_token;
								$data['is_default'] = $default;
								array_push($payments, $data);
							}
							
							if($payment){
								$response_array = array(
									'success' => true,
									'payments' => $payments,
								);
							}

							$response_code = 200;
						}
						else if (Config::get('app.default_payment') == RequestCharging::PAYMENT_GATEWAY_STRIPE) {
							Stripe::setApiKey(Config::get('app.stripe_secret_key'));

							$customer = Stripe_Customer::create(array(
										"card" => $payment_token,
										"description" => $user_data->email)
							);
							/* Log::info('customer = ' . print_r($customer, true)); */
							if ($customer) {
								$card_count = DB::table('payment')->where('user_id', '=', $user_id)->count();

								$customer_id = $customer->id;
								$payment = new Payment;
								$payment->user_id = $user_id;
								$payment->customer_id = $customer_id;
								$payment->last_four = $last_four;
								$payment->card_type = $card_type;
								$payment->card_token = $customer->sources->data[0]->id;
								if ($card_count > 0) {
									$payment->is_default = 0;
								} else {
									$payment->is_default = 1;
								}
								$payment->save();

								$payment_data = Payment::where('user_id', $user_id)->orderBy('is_default', 'DESC')->get();
								foreach ($payment_data as $data1) {
									$default = $data1->is_default;
									if ($default == 1) {
										$data['is_default_text'] = "default";
									} else {
										$data['is_default_text'] = "not_default";
									}
									$data['id'] = $data1->id;
									$data['user_id'] = $data1->user_id;
									$data['customer_id'] = $data1->customer_id;
									$data['last_four'] = $data1->last_four;
									$data['card_token'] = $data1->card_token;
									$data['card_type'] = $data1->card_type;
									$data['card_id'] = $data1->card_token;
									$data['is_default'] = $default;
									array_push($payments, $data);
								}
								$response_array = array(
									'success' => true,
									'payments' => $payments,
								);
								$response_code = 200;
							} else {
								$payment_data = Payment::where('user_id', $user_id)->orderBy('is_default', 'DESC')->get();
								foreach ($payment_data as $data1) {
									$default = $data1->is_default;
									if ($default == 1) {
										$data['is_default_text'] = "default";
									} else {
										$data['is_default_text'] = "not_default";
									}
									$data['id'] = $data1->id;
									$data['user_id'] = $data1->user_id;
									$data['customer_id'] = $data1->customer_id;
									$data['last_four'] = $data1->last_four;
									$data['card_token'] = $data1->card_token;
									$data['card_type'] = $data1->card_type;
									$data['card_id'] = $data1->card_token;
									$data['is_default'] = $default;
									array_push($payments, $data);
								}
								$response_array = array(
									'success' => false,
									'error' => trans('userController.couldnt_create_client_id'),
									'error_code' => 450,
									'payments' => $payments,
								);
								$response_code = 200;
							}
						} else {
							Braintree_Configuration::environment(Config::get('app.braintree_environment'));
							Braintree_Configuration::merchantId(Config::get('app.braintree_merchant_id'));
							Braintree_Configuration::publicKey(Config::get('app.braintree_public_key'));
							Braintree_Configuration::privateKey(Config::get('app.braintree_private_key'));
							$result = Braintree_Customer::create(array(
										'paymentMethodNonce' => $payment_token
							));
							//Log::info('result = ' . print_r($result, true));
							if ($result->success) {
								$card_count = DB::table('payment')->where('user_id', '=', $user_id)->count();

								$customer_id = $result->customer->id;
								$payment = new Payment;
								$payment->user_id = $user_id;
								$payment->customer_id = $customer_id;
								$payment->last_four = $last_four;
								$payment->card_type = $card_type;
								$payment->card_token = $result->customer->creditCards[0]->token;
								if ($card_count > 0) {
									$payment->is_default = 0;
								} else {
									$payment->is_default = 1;
								}
								$payment->save();

								$payment_data = Payment::where('user_id', $user_id)->orderBy('is_default', 'DESC')->get();
								foreach ($payment_data as $data1) {
									$default = $data1->is_default;
									if ($default == 1) {
										$data['is_default_text'] = "default";
									} else {
										$data['is_default_text'] = "not_default";
									}
									$data['id'] = $data1->id;
									$data['user_id'] = $data1->user_id;
									$data['customer_id'] = $data1->customer_id;
									$data['last_four'] = $data1->last_four;
									$data['card_token'] = $data1->card_token;
									$data['card_type'] = $data1->card_type;
									$data['card_id'] = $data1->card_token;
									$data['is_default'] = $default;
									array_push($payments, $data);
								}

								$response_array = array(
									'success' => true,
									'payments' => $payments,
								);
								$response_code = 200;
							} else {
								$payment_data = Payment::where('user_id', $user_id)->orderBy('is_default', 'DESC')->get();
								foreach ($payment_data as $data1) {
									$default = $data1->is_default;
									if ($default == 1) {
										$data['is_default_text'] = "default";
									} else {
										$data['is_default_text'] = "not_default";
									}
									$data['id'] = $data1->id;
									$data['user_id'] = $data1->user_id;
									$data['customer_id'] = $data1->customer_id;
									$data['last_four'] = $data1->last_four;
									$data['card_token'] = $data1->card_token;
									$data['card_type'] = $data1->card_type;
									$data['card_id'] = $data1->card_token;
									$data['is_default'] = $default;
									array_push($payments, $data);
								}
								$response_array = array(
									'success' => false,
									'error' => trans('userController.couldnt_create_client_id'),
									'error_code' => 450,
									'payments' => $payments,
								);
								$response_code = 200;
							}
						}
					}
					catch (PagarMe_Exception $e) {
						$response_array = array('success' => false, 'error' => "PagarMe: " . $e->getMessage(), 'error_code' => 405);
						$response_code = 200;
					}
					catch (Exception $e) {
						$response_array = array('success' => false, 'error' => $e->getMessage(), 'error_code' => 405);
						$response_code = 200;
					}
					
				} else {
					$response_array = array('success' => false, 'error' => trans('userController.token_expired'), 'error_code' => 405);
					$response_code = 200;
				}
			} else {
				if ($is_admin) {
					/* $var = Keywords::where('id', 2)->first();
					  $response_array = array('success' => false, 'error' => "" . $var->keyword . ' ID not Found', 'error_code' => 410); */
					$response_array = array('success' => false, 'error' => trans('userController.id_of') . "" . Config::get('app.generic_keywords.User') . trans('userController.not_found'), 'error_code' => 410);
				} else {
					$response_array = array('success' => false, 'error' => trans('userController.token_not_valid'), 'error_code' => 406);
				}
				$response_code = 200;
			}
		}

		$response = Response::json($response_array, $response_code);
		return $response;
	}

	public function deletecardtoken() {
		$card_id = Input::get('card_id');
		$token = Input::get('token');
		$user_id = Input::get('id');

		$validator = Validator::make(
						array(
					'card_id' => $card_id,
					'token' => $token,
					'user_id' => $user_id,
						), array(
					'card_id' => 'required',
					'token' => 'required',
					'user_id' => 'required|integer'
						), array(
					'card_id' => trans('userController.unique_card_id_missing'),
					'token' => '',
					'user_id' => trans('userController.unique_id_missing')
						)
		);

		/* $var = Keywords::where('id', 2)->first(); */

		if ($validator->fails()) {
			$error_messages = $validator->messages();
			$response_array = array('success' => false, 'error' => trans('userController.invalid_input'), 'error_code' => 401);
			$response_code = 200;
		} else {
			$is_admin = $this->isAdmin($token);
			if ($user_data = $this->getUserData($user_id, $token, $is_admin)) {
				// check for token validity
				if (is_token_active($user_data->token_expiry) || $is_admin) {
					if ($payment = Payment::find($card_id)) {
						if ($payment->user_id == $user_id) {
							if (Config::get('app.default_payment') == 'stripe') {
								Stripe::setApiKey(Config::get('app.stripe_secret_key'));
								try {
									$get_customer = Stripe_Customer::retrieve($payment->customer_id);
									$get_customer->delete();
								} catch (Exception $e) {
									
								}
							}
							if (Config::get('app.default_payment') == 'braintree') {
								Braintree_Configuration::environment(Config::get('app.braintree_environment'));
								Braintree_Configuration::merchantId(Config::get('app.braintree_merchant_id'));
								Braintree_Configuration::publicKey(Config::get('app.braintree_public_key'));
								Braintree_Configuration::privateKey(Config::get('app.braintree_private_key'));
								try {
									$get_customer = Braintree_Customer::delete($payment->customer_id);
								} catch (Exception $e) {
									
								}
							}

							$pdn = Payment::where('id', $card_id)->first();
							$check = trim($pdn->is_default);
							Payment::find($card_id)->delete();
							if ($check == 1) {
								$card_count = DB::table('payment')->where('user_id', '=', $user_id)->count();
								if ($card_count) {
									$paymnt = Payment::where('user_id', $user_id)->first();
									$paymnt->is_default = 1;
									$paymnt->save();
								}
							}

							$payments = array();
							$card_count = DB::table('payment')->where('user_id', '=', $user_id)->count();
							if ($card_count) {
								$paymnt = Payment::where('user_id', $user_id)->orderBy('is_default', 'DESC')->get();
								/* foreach ($paymnt as $data1) {
								  $default = $data1->is_default;
								  if ($default == 1) {
								  $data['is_default_text'] = "default";
								  } else {
								  $data['is_default_text'] = "not_default";
								  }
								  $data['id'] = $data1->id;
								  $data['customer_id'] = $data1->customer_id;
								  $data['card_id'] = $data1->card_token;
								  $data['last_four'] = $data1->last_four;
								  $data['is_default'] = $default;
								  array_push($payments, $data);
								  } */
								foreach ($paymnt as $data1) {
									$default = $data1->is_default;
									if ($default == 1) {
										$data['is_default_text'] = "default";
									} else {
										$data['is_default_text'] = "not_default";
									}
									$data['id'] = $data1->id;
									$data['user_id'] = $data1->user_id;
									$data['customer_id'] = $data1->customer_id;
									$data['last_four'] = $data1->last_four;
									$data['card_token'] = $data1->card_token;
									$data['card_type'] = $data1->card_type;
									$data['card_id'] = $data1->card_token;
									$data['is_default'] = $default;
									array_push($payments, $data);
								}
								$response_array = array(
									'success' => true,
									'payments' => $payments,
								);
								$response_code = 200;
							} else {
								$response_code = 200;
								$response_array = array(
									'success' => true,
									'error' => trans('userController.no_card_found'),
									'error_code' => 541,
								);
							}
						} else {
							/* $response_array = array('success' => false, 'error' => trans('userController.card_id_and') . "" . $var->keyword . "" . trans('userController.user_id_not_match'), 'error_code' => 440); */
							$response_array = array('success' => false, 'error' => trans('userController.card_id_and') . "" . Config::get('app.generic_keywords.User') . "" . trans('userController.user_id_not_match'), 'error_code' => 440);
							$response_code = 200;
						}
					} else {
						$response_array = array('success' => false, 'error' => trans('userController.card_not_found'), 'error_code' => 441);
						$response_code = 200;
					}
				} else {
					$response_array = array('success' => false, 'error' => trans('userController.token_expired'), 'error_code' => 405);
					$response_code = 200;
				}
			} else {
				if ($is_admin) {
					/* $response_array = array('success' => false, 'error' => "" . $var->keyword . ' ID not Found', 'error_code' => 410); */
					$response_array = array('success' => false, 'error' => trans('userController.id_of') . "" . Config::get('app.generic_keywords.User') . "" . trans('userController.not_found'), 'error_code' => 410);
				} else {
					$response_array = array('success' => false, 'error' => trans('userController.token_not_valid'), 'error_code' => 406);
				}
				$response_code = 200;
			}
		}

		$response = Response::json($response_array, $response_code);
		return $response;
	}

	public function set_referral_code() {
		$code = Input::get('referral_code');
		$token = Input::get('token');
		$user_id = Input::get('id');

		$validator = Validator::make(
						array(
					/* 'code' => $code, */
					'token' => $token,
					'user_id' => $user_id,
						), array(
					/* 'code' => 'required', */
					'token' => 'required',
					'user_id' => 'required|integer'
						), array(
					/* 'code' => 'required', */
					'token' => '',
					'user_id' => trans('userController.unique_id_missing')
						)
		);

		if ($validator->fails()) {
			$error_messages = $validator->messages()->all();
			$response_array = array('success' => false, 'error' => trans('userController.invalid_input'), 'error_code' => 401, 'error_messages' => $error_messages);
			$response_code = 200;
		} else {
			$is_admin = $this->isAdmin($token);
			if ($user_data = $this->getUserData($user_id, $token, $is_admin)) {
				// check for token validity
				if (is_token_active($user_data->token_expiry) || $is_admin) {
					// Do necessary operations

					/* $ledger_count = Ledger::where('referral_code', $code)->count();
					  if ($ledger_count > 0) {
					  $response_array = array('success' => false, 'error' => 'This Code already is taken by another user', 'error_code' => 484);
					  } else {
					  $led = Ledger::where('user_id', $user_id)->first();
					  if ($led) {
					  $ledger = Ledger::where('user_id', $user_id)->first();
					  } else {
					  $ledger = new Ledger;
					  $ledger->user_id = $user_id;
					  }
					  $ledger->referral_code = $code;
					  $ledger->save();

					  $response_array = array('success' => true);
					  } */
					/* $zero_in_code = Config::get('app.referral_zero_len') - strlen($user_id);
					  $referral_code = Config::get('app.referral_prefix');
					  for ($i = 0; $i < $zero_in_code; $i++) {
					  $referral_code .= "0";
					  }
					  $referral_code .= $user_id; */
					regenerate:
					$referral_code = my_random6_number();
					if (Ledger::where('referral_code', $referral_code)->count()) {
						goto regenerate;
					}
					/* $referral_code .= my_random6_number(); */
					if (Ledger::where('user_id', $user_id)->count()) {
						Ledger::where('user_id', $user_id)->update(array('referral_code' => $referral_code));
					} else {
						$ledger = new Ledger;
						$ledger->user_id = $user_id;
						$ledger->referral_code = $referral_code;
						$ledger->save();
					}
					/* $ledger = Ledger::where('user_id', $user_id)->first();
					  $ledger->referral_code = $code;
					  $ledger->save(); */
					/* SEND REFERRAL & PROMO INFO */
					$settings = Settings::where('key', 'referral_code_activation')->first();
					$referral_code_activation = $settings->value;
					if ($referral_code_activation) {
						$referral_code_activation_txt = trans('userController.referral_on');
					} else {
						$referral_code_activation_txt = trans('userController.referral_off');
					}

					$settings = Settings::where('key', 'promotional_code_activation')->first();
					$promotional_code_activation = $settings->value;
					if ($promotional_code_activation) {
						$promotional_code_activation_txt = trans('userController.promo_on');
					} else {
						$promotional_code_activation_txt = trans('userController.promo_off');
					}
					/* SEND REFERRAL & PROMO INFO */
					$response_array = array(
						'success' => true,
						'id' => $user_data->id,
						'first_name' => $user_data->first_name,
						'last_name' => $user_data->last_name,
						'phone' => $user_data->phone,
						'email' => $user_data->email,
						'picture' => $user_data->picture,
						'bio' => $user_data->bio,
						'address' => $user_data->address,
						'state' => $user_data->state,
						'country' => $user_data->country,
						'zipcode' => $user_data->zipcode,
						'login_by' => $user_data->login_by,
						'social_unique_id' => $user_data->social_unique_id,
						'device_token' => $user_data->device_token,
						'device_type' => $user_data->device_type,
						'timezone' => $user_data->timezone,
						'token' => $user_data->token,
						'referral_code' => $referral_code,
						'is_referee' => $user_data->is_referee,
						'promo_count' => $user_data->promo_count,
						'is_referral_active' => $referral_code_activation,
						'is_referral_active_txt' => $referral_code_activation_txt,
						'is_promo_active' => $promotional_code_activation,
						'is_promo_active_txt' => $promotional_code_activation_txt,
					);

					$response_code = 200;
				} else {
					$response_array = array('success' => false, 'error' => trans('userController.token_expired'), 'error_code' => 405);
					$response_code = 200;
				}
			} else {
				if ($is_admin) {
					/* $var = Keywords::where('id', 2)->first();
					  $response_array = array('success' => false, 'error' => "" . $var->keyword . ' ID not Found', 'error_code' => 410); */
					$response_array = array('success' => false, 'error' => trans('userController.id_of') . "" . Config::get('app.generic_keywords.User') . "" . trans('userController.not_found'), 'error_code' => 410);
				} else {
					$response_array = array('success' => false, 'error' => trans('userController.token_not_valid'), 'error_code' => 406);
				}
				$response_code = 200;
			}
		}

		$response = Response::json($response_array, $response_code);
		return $response;
	}

	public function get_referral_code() {

		$token = Input::get('token');
		$user_id = Input::get('id');

		$validator = Validator::make(
						array(
					'token' => $token,
					'user_id' => $user_id,
						), array(
					'token' => 'required',
					'user_id' => 'required|integer'
						), array(
					'token' => '',
					'user_id' => trans('userController.unique_id_missing')
						)
		);

		if ($validator->fails()) {
			$error_messages = $validator->messages()->all();
			$response_array = array('success' => false, 'error' => trans('userController.invalid_input'), 'error_code' => 401, 'error_messages' => $error_messages);
			$response_code = 200;
		} else {
			$settings = Settings::where('key', 'default_referral_bonus_to_refered_user')->first();
			$refered_user = $settings->value;
			$settings = Settings::where('key', 'default_referral_bonus_to_refereel')->first();
			$refereel_user = $settings->value;
			$is_admin = $this->isAdmin($token);
			if ($user_data = $this->getUserData($user_id, $token, $is_admin)) {
				// check for token validity
				if (is_token_active($user_data->token_expiry) || $is_admin) {
					// Do necessary operations

					$ledger = Ledger::where('user_id', $user_id)->first();
					if ($ledger) {
						$response_array = array(
							'success' => true,
							'referral_code' => $ledger->referral_code,
							'total_referrals' => $ledger->total_referrals,
							'amount_earned' => $ledger->amount_earned,
							'amount_spent' => $ledger->amount_spent,
							'balance_amount' => $ledger->amount_earned - $ledger->amount_spent,
							'refered_user_bonus' => Config::get('app.generic_keywords.Currency') . " " . sprintf2($refered_user, 2),
							'refereel_user_bonus' => Config::get('app.generic_keywords.Currency') . " " . sprintf2($refereel_user, 2),
						);
					} else {
						$response_array = array('success' => false, 'error' => trans('userController.user_doesnt_have_referral_code'));
					}


					$response_code = 200;
				} else {
					$response_array = array('success' => false, 'error' => trans('userController.token_expired'), 'error_code' => 405);
					$response_code = 200;
				}
			} else {
				if ($is_admin) {
					/* $var = Keywords::where('id', 2)->first();
					  $response_array = array('success' => false, 'error' => "" . $var->keyword . ' ID not Found', 'error_code' => 410); */
					$response_array = array('success' => false, 'error' => trans('userController.id_of') . "" . Config::get('app.generic_keywords.User') . "" . trans('userController.not_found'), 'error_code' => 410);
				} else {
					$response_array = array('success' => false, 'error' => trans('userController.token_not_valid'), 'error_code' => 406);
				}
				$response_code = 200;
			}
		}

		$response = Response::json($response_array, $response_code);
		return $response;
	}

	public function get_cards() {

		$token = Input::get('token');
		$user_id = Input::get('id');
		if (Input::has('card_id')) {
			$card_id = Input::get('card_id');
			Payment::where('user_id', $user_id)->update(array('is_default' => 0));
			Payment::where('user_id', $user_id)->where('id', $card_id)->update(array('is_default' => 1));
		}

		$validator = Validator::make(
						array(
					'token' => $token,
					'user_id' => $user_id,
						), array(
					'token' => 'required',
					'user_id' => 'required|integer'
						), array(
					'token' => '',
					'user_id' => trans('userController.unique_id_missing')
						)
		);

		if ($validator->fails()) {
			$error_messages = $validator->messages()->all();
			$response_array = array('success' => false, 'error' => trans('userController.invalid_input'), 'error_code' => 401, 'error_messages' => $error_messages);
			$response_code = 200;
		} else {
			$is_admin = $this->isAdmin($token);
			if ($user_data = $this->getUserData($user_id, $token, $is_admin)) {
				// check for token validity
				if (is_token_active($user_data->token_expiry) || $is_admin) {
					// Do necessary operations
					$payments = array();
					$card_count = DB::table('payment')->where('user_id', '=', $user_id)->count();
					if ($card_count) {
						$paymnt = Payment::where('user_id', $user_id)->orderBy('is_default', 'DESC')->get();
						foreach ($paymnt as $data1) {
							$default = $data1->is_default;
							if ($default == 1) {
								$data['is_default_text'] = "default";
							} else {
								$data['is_default_text'] = "not_default";
							}
							$data['id'] = $data1->id;
							$data['user_id'] = $data1->user_id;
							$data['customer_id'] = $data1->customer_id;
							$data['last_four'] = $data1->last_four;
							$data['card_token'] = $data1->card_token;
							$data['card_type'] = $data1->card_type;
							$data['card_id'] = $data1->card_token;
							$data['is_default'] = $default;
							array_push($payments, $data);
						}
						$response_array = array(
							'success' => true,
							'payments' => $payments
						);
					} else {
						$response_array = array(
							'success' => false,
							'error' => trans('userController.no_card_found')
						);
					}


					$response_code = 200;
				} else {
					$response_array = array('success' => false, 'error' => trans('userController.token_expired'), 'error_code' => 405);
					$response_code = 200;
				}
			} else {
				if ($is_admin) {
					/* $var = Keywords::where('id', 2)->first();
					  $response_array = array('success' => false, 'error' => "" . $var->keyword . ' ID not Found', 'error_code' => 410); */
					$response_array = array('success' => false, 'error' => trans('userController.id_of') . "" . Config::get('app.generic_keywords.User') . "" . trans('userController.not_found'), 'error_code' => 410);
				} else {
					$response_array = array('success' => false, 'error' => trans('userController.token_not_valid'), 'error_code' => 406);
				}
				$response_code = 200;
			}
		}

		$response = Response::json($response_array, $response_code);
		return $response;
	}

	public function card_selection() {

		$token = Input::get('token');
		$user_id = Input::get('id');
		$default_card_id = Input::get('default_card_id');
		$validator = Validator::make(
						array(
					'token' => $token,
					'user_id' => $user_id,
					'default_card_id' => $default_card_id,
						), array(
					'token' => 'required',
					'user_id' => 'required|integer',
					'default_card_id' => 'required'
						), array(
					'token' => '',
					'user_id' => trans('userController.unique_id_missing'),
					'default_card_id' => trans('userController.unique_card_id_missing')
						)
		);
		if ($validator->fails()) {
			$error_messages = $validator->messages()->all();
			$response_array = array('success' => false, 'error' => trans('userController.invalid_input'), 'error_code' => 401, 'error_messages' => $error_messages);
			$response_code = 200;
		} else {
			$payments = array();
			/* $payments['none'] = ""; */
			$is_admin = $this->isAdmin($token);
			if ($user_data = $this->getUserData($user_id, $token, $is_admin)) {

				// check for token validity
				if (is_token_active($user_data->token_expiry) || $is_admin) {
					// Do necessary operations
					Payment::where('user_id', $user_id)->update(array('is_default' => 0));
					Payment::where('user_id', $user_id)->where('id', $default_card_id)->update(array('is_default' => 1));
					$payment_data = Payment::where('user_id', $user_id)->orderBy('is_default', 'DESC')->get();
					foreach ($payment_data as $data1) {
						$default = $data1->is_default;
						if ($default == 1) {
							$data['is_default_text'] = "default";
						} else {
							$data['is_default_text'] = "not_default";
						}
						$data['id'] = $data1->id;
						$data['user_id'] = $data1->user_id;
						$data['customer_id'] = $data1->customer_id;
						$data['last_four'] = $data1->last_four;
						$data['card_token'] = $data1->card_token;
						$data['card_type'] = $data1->card_type;
						$data['is_default'] = $default;
						array_push($payments, $data);
					}
					$user = User::find($user_id);

					$response_array = array(
						'success' => true,
						'id' => $user->id,
						'first_name' => $user->first_name,
						'last_name' => $user->last_name,
						'phone' => $user->phone,
						'email' => $user->email,
						'picture' => $user->picture,
						'bio' => $user->bio,
						'address' => $user->address,
						'state' => $user->state,
						'country' => $user->country,
						'zipcode' => $user->zipcode,
						'login_by' => $user->login_by,
						'social_unique_id' => $user->social_unique_id,
						'device_token' => $user->device_token,
						'device_type' => $user->device_type,
						'token' => $user->token,
						'default_card_id' => $default_card_id,
						'payment_type' => 0,
						'is_referee' => $user->is_referee,
						'promo_count' => $user->promo_count,
						'payments' => $payments
					);



					$response_code = 200;
				} else {
					$response_array = array('success' => false, 'error' => trans('userController.token_expired'), 'error_code' => 405);
					$response_code = 200;
				}
			} else {
				if ($is_admin) {
					$response_array = array('success' => false, 'error' => trans('userController.user_id_not_found'), 'error_code' => 410);
				} else {
					$response_array = array('success' => false, 'error' => trans('userController.token_not_valid'), 'error_code' => 406);
				}
				$response_code = 200;
			}
		}

		$response = Response::json($response_array, $response_code);
		return $response;
	}

	public function get_completed_requests() {

		$token = Input::get('token');
		$user_id = Input::get('id');
		$from = Input::get('from_date'); // 2015-03-11 07:45:01
		$to_date = Input::get('to_date'); //2015-03-11 07:45:01
		$to_date = date('Y-m-d', strtotime($to_date . "+1 days"));

		$validator = Validator::make(
						array(
					'token' => $token,
					'user_id' => $user_id,
						), array(
					'token' => 'required',
					'user_id' => 'required|integer'
						), array(
					'token' => '',
					'user_id' => trans('userController.unique_id_missing')
						)
		);

		if ($validator->fails()) {
			$error_messages = $validator->messages()->all();
			$response_array = array('success' => false, 'error' => trans('userController.invalid_input'), 'error_code' => 401, 'error_messages' => $error_messages);
			$response_code = 200;
		} else {
			$is_admin = $this->isAdmin($token);
			if ($user_data = $this->getUserData($user_id, $token, $is_admin)) {
				// check for token validity
				if (is_token_active($user_data->token_expiry) || $is_admin) {
					// Do necessary operations
					if ($from != "" && $to_date != "") {
						$request_data = DB::table('request')
								->where('request.user_id', $user_id)
								->where('is_completed', 1)
								->where('is_cancelled', 0)
								->whereBetween('request_start_time', array($from, $to_date))
								->leftJoin('provider', 'request.confirmed_provider', '=', 'provider.id')
								->leftJoin('provider_services', 'provider.id', '=', 'provider_services.provider_id')
								->leftJoin('provider_type', 'provider_type.id', '=', 'provider_services.type')
								->leftJoin('request_services', 'request_services.request_id', '=', 'request.id')
								->select('request.*', 'request.request_start_time', 'request.promo_code', 'provider.first_name', 'provider.id as provider_id', 'provider.last_name', 'provider.phone', 'provider.email', 'provider.picture', 'provider.bio', 'provider.rate', 'provider_type.name as type', 'provider_type.icon', 'request.distance', 'request.time', 'request_services.base_price as req_base_price', 'request_services.distance_cost as req_dis_cost', 'request_services.time_cost as req_time_cost', 'request_services.type as req_typ', 'request.total')
								->groupBy('request.id')
								->get();
					} else {
						$request_data = DB::table('request')
								->where('request.user_id', $user_id)
								->where('is_completed', 1)
								->where('is_cancelled', 0)
								->leftJoin('provider', 'request.confirmed_provider', '=', 'provider.id')
								->leftJoin('provider_services', 'provider.id', '=', 'provider_services.provider_id')
								->leftJoin('provider_type', 'provider_type.id', '=', 'provider_services.type')
								->leftJoin('request_services', 'request_services.request_id', '=', 'request.id')
								->select('request.*', 'request.request_start_time', 'request.promo_code', 'provider.first_name', 'provider.id as provider_id', 'provider.last_name', 'provider.phone', 'provider.email', 'provider.picture', 'provider.bio', 'provider.rate', 'provider_type.name as type', 'provider_type.icon', 'request.distance', 'request.time', 'request_services.base_price as req_base_price', 'request_services.distance_cost as req_dis_cost', 'request_services.time_cost as req_time_cost', 'request_services.type as req_typ', 'request.total')
								->groupBy('request.id')
								->get();
					}

					$requests = array();

					$settings = Settings::where('key', 'default_distance_unit')->first();
					$unit = $settings->value;
					if ($unit == 0) {
						$unit_set = 'kms';
					} elseif ($unit == 1) {
						$unit_set = 'miles';
					}

					/* $currency_selected = Keywords::find(5); */
					foreach ($request_data as $data) {
						$request_typ = ProviderType::where('id', '=', $data->req_typ)->first();

						/* $setbase_price = Settings::where('key', 'base_price')->first();
						  $setdistance_price = Settings::where('key', 'price_per_unit_distance')->first();
						  $settime_price = Settings::where('key', 'price_per_unit_time')->first(); */
						$setbase_distance = $request_typ->base_distance;
						$setbase_price = $request_typ->base_price;
						$setdistance_price = $request_typ->price_per_unit_distance;
						$settime_price = $request_typ->price_per_unit_time;

						$locations = RequestLocation::where('request_id', $data->id)->orderBy('id')->get();
						$count = round(count($locations) / 50);
						$start = $end = $map = "";
						$id = $data->id;
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
						$request['start_lat'] = "";
						if (isset($start->latitude)) {
							$request['start_lat'] = $start->latitude;
						}
						$request['start_long'] = "";
						if (isset($start->longitude)) {
							$request['start_long'] = $start->longitude;
						}
						$request['end_lat'] = "";
						if (isset($end->latitude)) {
							$request['end_lat'] = $end->latitude;
						}
						$request['end_long'] = "";
						if (isset($end->longitude)) {
							$request['end_long'] = $end->longitude;
						}
						$request['map_url'] = $map;

						$provider = Provider::where('id', $data->provider_id)->first();

						if ($provider != NULL) {
							$user_timezone = $provider->timezone;
						} else {
							$user_timezone = 'America/Sao_Paulo';
						}

						$default_timezone = Config::get('app.timezone');

						$date_time = get_user_time($default_timezone, $user_timezone, $data->request_start_time);

						$dist = number_format($data->distance, 2, '.', '');
						$request['id'] = $data->id;
						$request['date'] = $date_time;
						$request['distance'] = (string) $dist;
						$request['unit'] = $unit_set;
						$request['time'] = $data->time;
						$discount = 0;
						if ($data->promo_code != "") {
							if ($data->promo_code != "") {
								$promo_code = PromoCodes::where('id', $data->promo_code)->first();
								if ($promo_code) {
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
						}

						$request['promo_discount'] = currency_converted($discount);

						$is_multiple_service = Settings::where('key', 'allow_multiple_service')->first();
						if ($is_multiple_service->value == 0) {

							$request['base_price'] = currency_converted($data->req_base_price);

							$request['distance_cost'] = currency_converted($data->req_dis_cost);


							$request['time_cost'] = currency_converted($data->req_time_cost);

							$request['setbase_distance'] = $setbase_distance;
							$request['total'] = currency_converted($data->total);
							$request['actual_total'] = currency_converted($data->total + $data->ledger_payment + $discount);
							$request['type'] = $data->type;
							$request['type_icon'] = $data->icon;
						} else {
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

						$rate = ProviderReview::where('request_id', $data->id)->where('provider_id', $data->confirmed_provider)->first();
						if ($rate != NULL) {
							$request['provider']['rating'] = $rate->rating;
						} else {
							$request['provider']['rating'] = '0.0';
						}

						/* $request['currency'] = $currency_selected->keyword; */
						$request['src_address'] = $data->src_address;
						$request['dest_address'] = $data->dest_address;
						$request['base_price'] = currency_converted($data->req_base_price);
						$request['distance_cost'] = currency_converted($data->req_dis_cost);
						$request['time_cost'] = currency_converted($data->req_time_cost);
						$request['total'] = currency_converted($data->total - $data->ledger_payment - $data->promo_payment);
						$request['main_total'] = currency_converted($data->total);
						$request['referral_bonus'] = currency_converted($data->ledger_payment);
						$request['promo_bonus'] = currency_converted($data->promo_payment);
						$request['payment_type'] = $data->payment_mode;
						$request['is_paid'] = $data->is_paid;
						$request['promo_id'] = $data->promo_id;
						$request['promo_code'] = $data->promo_code;
						$request['currency'] = Config::get('app.generic_keywords.Currency');
						$request['provider']['first_name'] = $data->first_name;
						$request['provider']['last_name'] = $data->last_name;
						$request['provider']['phone'] = $data->phone;
						$request['provider']['email'] = $data->email;
						$request['provider']['picture'] = $data->picture;
						$request['provider']['bio'] = $data->bio;
						$request['provider']['type'] = $data->type;
						/* $request['provider']['rating'] = $data->rate; */
						array_push($requests, $request);
					}

					$response_array = array(
						'success' => true,
						'requests' => $requests
					);

					$response_code = 200;
				} else {
					$response_array = array('success' => false, 'error' => trans('userController.token_expired'), 'error_code' => 405);
					$response_code = 200;
				}
			} else {
				if ($is_admin) {
					/* $var = Keywords::where('id', 2)->first();
					  $response_array = array('success' => false, 'error' => "" . $var->keyword . ' ID not Found', 'error_code' => 410); */
					$response_array = array('success' => false, 'error' => trans('userController.id_of') . "" . Config::get('app.generic_keywords.User') . "" . trans('userController.not_found'), 'error_code' => 410);
				} else {
					$response_array = array('success' => false, 'error' => trans('userController.token_not_valid'), 'error_code' => 406);
				}
				$response_code = 200;
			}
		}

		$response = Response::json($response_array, $response_code);
		return $response;
	}

	public function update_profile() {

		$token = Input::get('token');
		$user_id = Input::get('id');
		$first_name = $last_name = $phone = $password = $picture = $bio = $address = $state = $country = $zipcode = 0;
		if (Input::has('first_name'))
			$first_name = Input::get('first_name');
		if (Input::has('last_name'))
			$last_name = Input::get('last_name');
		if (Input::has('phone'))
			$phone = Input::get('phone');
		if (Input::has('password'))
			$password = Input::get('password');
		if (Input::hasFile('picture'))
			$picture = Input::file('picture');
		if (Input::has('bio'))
			$bio = Input::get('bio');
		if (Input::has('address'))
			$address = Input::get('address');
		if (Input::has('state'))
			$state = Input::get('state');
		if (Input::has('country'))
			$country = Input::get('country');
		if (Input::has('zipcode'))
			$zipcode = Input::get('zipcode');
		$new_password = Input::get('new_password');
		$old_password = Input::get('old_password');
		$validator = Validator::make(
						array(
					'token' => $token,
					'user_id' => $user_id,
					'picture' => $picture,
					'zipcode' => $zipcode
						), array(
					'token' => 'required',
					'user_id' => 'required|integer',
					/* 'picture' => 'mimes:jpeg,bmp,png', */
					'picture' => '',
					'zipcode' => 'integer'
						), array(
					'token' => '',
					'user_id' => trans('userController.unique_id_missing'),
					/* 'picture' => 'mimes:jpeg,bmp,png', */
					'picture' => trans('userController.image_required'),
					'zipcode' => ''
						)
		);

		if ($validator->fails()) {
			$error_messages = $validator->messages()->all();
			$response_array = array('success' => false, 'error' => trans('userController.invalid_input'), 'error_code' => 401, 'error_messages' => $error_messages);
			$response_code = 200;
		} else {
			$is_admin = $this->isAdmin($token);
			if ($user_data = $this->getUserData($user_id, $token, $is_admin)) {
				// check for token validity
				if (is_token_active($user_data->token_expiry) || $is_admin) {
					if ($new_password != "" || $new_password != NULL) {
						if ($old_password != "" || $old_password != NULL) {
							if (Hash::check($old_password, $user_data->password)) {
								// Do necessary operations
								$user = User::find($user_id);
								if ($first_name) {
									$user->first_name = $first_name;
								}
								if ($last_name) {
									$user->last_name = $last_name;
								}
								if ($phone) {
									$user->phone = $phone;
								}
								if ($bio) {
									$user->bio = $bio;
								}
								if ($address) {
									$user->address = $address;
								}
								if ($state) {
									$user->state = $state;
								}
								if ($country) {
									$user->country = $country;
								}
								if ($zipcode) {
									$user->zipcode = $zipcode;
								}
								if ($new_password) {
									$user->password = Hash::make($new_password);
								}
								if (Input::hasFile('picture')) {
									if ($user->picture != "") {
										$path = $user->picture;
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
									Input::file('picture')->move(public_path() . "/uploads", $file_name . "." . $ext);
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
								If (Input::has('timezone')) {
									$user->timezone = Input::get('timezone');
								}
								$user->save();
								$code_data = Ledger::where('user_id', '=', $user->id)->first();

								/* SEND REFERRAL & PROMO INFO */
								$settings = Settings::where('key', 'referral_code_activation')->first();
								$referral_code_activation = $settings->value;
								if ($referral_code_activation) {
									$referral_code_activation_txt = trans('userController.referral_on');
								} else {
									$referral_code_activation_txt = trans('userController.referral_off');
								}

								$settings = Settings::where('key', 'promotional_code_activation')->first();
								$promotional_code_activation = $settings->value;
								if ($promotional_code_activation) {
									$promotional_code_activation_txt = trans('userController.promo_on');
								} else {
									$promotional_code_activation_txt = trans('userController.promo_off');
								}
								/* SEND REFERRAL & PROMO INFO */

								$response_array = array(
									'success' => true,
									'id' => $user->id,
									'first_name' => $user->first_name,
									'last_name' => $user->last_name,
									'phone' => $user->phone,
									'email' => $user->email,
									'picture' => $user->picture,
									'bio' => $user->bio,
									'address' => $user->address,
									'state' => $user->state,
									'country' => $user->country,
									'zipcode' => $user->zipcode,
									'login_by' => $user->login_by,
									'social_unique_id' => $user->social_unique_id,
									'device_token' => $user->device_token,
									'device_type' => $user->device_type,
									'timezone' => $user->timezone,
									'token' => $user->token,
									'referral_code' => $code_data->referral_code,
									'is_referee' => $user->is_referee,
									'promo_count' => $user->promo_count,
									'is_referral_active' => $referral_code_activation,
									'is_referral_active_txt' => $referral_code_activation_txt,
									'is_promo_active' => $promotional_code_activation,
									'is_promo_active_txt' => $promotional_code_activation_txt,
								);
								$response_code = 200;
							} else {
								$response_array = array('success' => false, 'error' => trans('userController.old_password_invalid'), 'error_code' => 501);
								$response_code = 200;
							}
						} else {
							$response_array = array('success' => false, 'error' => trans('userController.old_password_blank'), 'error_code' => 502);
							$response_code = 200;
						}
					} else {
						// Do necessary operations
						$user = User::find($user_id);
						if ($first_name) {
							$user->first_name = $first_name;
						}
						if ($last_name) {
							$user->last_name = $last_name;
						}
						if ($phone) {
							$user->phone = $phone;
						}
						if ($bio) {
							$user->bio = $bio;
						}
						if ($address) {
							$user->address = $address;
						}
						if ($state) {
							$user->state = $state;
						}
						if ($country) {
							$user->country = $country;
						}
						if ($zipcode) {
							$user->zipcode = $zipcode;
						}
						if (Input::hasFile('picture')) {
							if ($user->picture != "") {
								$path = $user->picture;
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
							Input::file('picture')->move(public_path() . "/uploads", $file_name . "." . $ext);
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
						If (Input::has('timezone')) {
							$user->timezone = Input::get('timezone');
						}
						$user->save();
						$code_data = Ledger::where('user_id', '=', $user->id)->first();

						/* SEND REFERRAL & PROMO INFO */
						$settings = Settings::where('key', 'referral_code_activation')->first();
						$referral_code_activation = $settings->value;
						if ($referral_code_activation) {
							$referral_code_activation_txt = trans('userController.referral_on');
						} else {
							$referral_code_activation_txt = trans('userController.referral_off');
						}

						$settings = Settings::where('key', 'promotional_code_activation')->first();
						$promotional_code_activation = $settings->value;
						if ($promotional_code_activation) {
							$promotional_code_activation_txt = trans('userController.promo_on');
						} else {
							$promotional_code_activation_txt = trans('userController.promo_off');
						}
						/* SEND REFERRAL & PROMO INFO */

						$response_array = array(
							'success' => true,
							'id' => $user->id,
							'first_name' => $user->first_name,
							'last_name' => $user->last_name,
							'phone' => $user->phone,
							'email' => $user->email,
							'picture' => $user->picture,
							'bio' => $user->bio,
							'address' => $user->address,
							'state' => $user->state,
							'country' => $user->country,
							'zipcode' => $user->zipcode,
							'login_by' => $user->login_by,
							'social_unique_id' => $user->social_unique_id,
							'device_token' => $user->device_token,
							'device_type' => $user->device_type,
							'timezone' => $user->timezone,
							'token' => $user->token,
							'referral_code' => $code_data->referral_code,
							'is_referee' => $user->is_referee,
							'promo_count' => $user->promo_count,
							'is_referral_active' => $referral_code_activation,
							'is_referral_active_txt' => $referral_code_activation_txt,
							'is_promo_active' => $promotional_code_activation,
							'is_promo_active_txt' => $promotional_code_activation_txt,
						);
						$response_code = 200;
					}
				} else {
					$response_array = array('success' => false, 'error' => trans('userController.token_expired'), 'error_code' => 405);
					$response_code = 200;
				}
			} else {
				if ($is_admin) {
					/* $var = Keywords::where('id', 2)->first();
					  $response_array = array('success' => false, 'error' => "" . $var->keyword . ' ID not Found', 'error_code' => 410); */
					$response_array = array('success' => false, 'error' => trans('userController.id_of') . "" . Config::get('app.generic_keywords.User') . "" . trans('userController.not_found'), 'error_code' => 410);
				} else {
					$response_array = array('success' => false, 'error' => trans('userController.token_not_valid'), 'error_code' => 406);
				}
				$response_code = 200;
			}
		}

		$response = Response::json($response_array, $response_code);
		return $response;
	}

	public function payment_type() {
		$token = Input::get('token');
		$user_id = Input::get('id');
		$request_id = Input::get('request_id');
		$cash_or_card = Input::get('cash_or_card');
		$validator = Validator::make(
						array(
					'token' => $token,
					'user_id' => $user_id,
					'cash_or_card' => $cash_or_card,
					'request_id' => $request_id,
						), array(
					'token' => 'required',
					'user_id' => 'required|integer',
					'cash_or_card' => 'required',
					'request_id' => 'required',
						), array(
					'token' => '',
					'user_id' => trans('userController.unique_id_missing'),
					'cash_or_card' => trans('userController.payment_type_required'),
					'request_id' => trans('userController.id_request_required'),
						)
		);
		if ($validator->fails()) {
			$error_messages = $validator->messages()->all();
			$response_array = array('success' => false, 'error' => trans('userController.invalid_input'), 'error_code' => 401, 'error_messages' => $error_messages);
			$response_code = 200;
		} else {
			$payments = array();
			/* $payments['none'] = ""; */
			$def_card = 0;
			$is_admin = $this->isAdmin($token);
			if ($user_data = $this->getUserData($user_id, $token, $is_admin)) {
				// check for token validity
				if (is_token_active($user_data->token_expiry) || $is_admin) {
					if ($cash_or_card != 1) {
						$card_count = Payment::where('user_id', '=', $user_id)->count();
						if ($card_count <= 0) {
							$response_array = array('success' => false, 'error' => trans('userController.please_add_card'), 'error_code' => 420);
							$response_code = 200;
							$response = Response::json($response_array, $response_code);
							return $response;
						}
					}
					// Do necessary operations
					$user = User::find($user_id);
					$payment_data = Payment::where('user_id', $user_id)->orderBy('is_default', 'DESC')->get();
					foreach ($payment_data as $data1) {
						$default = $data1->is_default;
						if ($default == 1) {
							$def_card = $data1->id;
							$data['is_default_text'] = "default";
						} else {
							$data['is_default_text'] = "not_default";
						}
						$data['id'] = $data1->id;
						$data['user_id'] = $data1->user_id;
						$data['customer_id'] = $data1->customer_id;
						$data['last_four'] = $data1->last_four;
						$data['card_token'] = $data1->card_token;
						$data['card_type'] = $data1->card_type;
						$data['card_id'] = $data1->card_token;
						$data['is_default'] = $default;
						array_push($payments, $data);
					}
					if ($request = Requests::find($request_id)) {
						$request->payment_mode = $cash_or_card;
						$request->save();

						$provider = Provider::where('id', $request->confirmed_provider)->first();
						if ($provider) {
							$msg_array = array();
							$msg_array['unique_id'] = 3;
							$msg_array['request_id'] = $request_id;
							$response_array = array(
								'success' => true,
								'id' => $user->id,
								'first_name' => $user->first_name,
								'last_name' => $user->last_name,
								'phone' => $user->phone,
								'email' => $user->email,
								'picture' => $user->picture,
								'bio' => $user->bio,
								'address' => $user->address,
								'state' => $user->state,
								'country' => $user->country,
								'zipcode' => $user->zipcode,
								'login_by' => $user->login_by,
								'social_unique_id' => $user->social_unique_id,
								'device_token' => $user->device_token,
								'device_type' => $user->device_type,
								'token' => $user->token,
								'default_card_id' => $def_card,
								'payment_type' => $request->payment_mode,
								'is_referee' => $user->is_referee,
								'promo_count' => $user->promo_count,
								'payments' => $payments,
							);
							$response_array['unique_id'] = 3;
							$response_code = 200;
							$msg_array['user_data'] = $response_array;
							$title = trans('userController.payment_type_change');
							$message = $msg_array;
							if ($request->confirmed_provider == $request->current_provider) {
								send_notifications($request->confirmed_provider, "provider", $title, $message);
							}
						} else {
							$response_array = array('success' => false, 'error' => trans('userController.driver_not_found'), 'error_code' => 408);
							$response_code = 200;
						}
					} else {
						$response_array = array('success' => false, 'error' => trans('userController.request_id_not_found'), 'error_code' => 408);
						$response_code = 200;
					}
				} else {
					$response_array = array('success' => false, 'error' => trans('userController.token_expired'), 'error_code' => 405);
					$response_code = 200;
				}
			} else {
				if ($is_admin) {
					$response_array = array('success' => false, 'error' => trans('userController.user_id_not_found'), 'error_code' => 410);
				} else {
					$response_array = array('success' => false, 'error' => trans('userController.token_not_valid'), 'error_code' => 406);
				}
				$response_code = 200;
			}
		}

		$response = Response::json($response_array, $response_code);
		return $response;
	}

	public function select_card() {
		$token = Input::get('token');
		$user_id = Input::get('id');
		$card_token = Input::get('card_id');

		$validator = Validator::make(
						array(
					'token' => $token,
					'user_id' => $user_id,
					'card' => $card_token
						), array(
					'token' => 'required',
					'user_id' => 'required|integer',
					'card' => 'required|integer'
						), array(
					'token' => '',
					'user_id' => trans('userController.unique_id_missing'),
					'card' => trans('userController.unique_card_id_missing')
						)
		);

		if ($validator->fails()) {
			$error_messages = $validator->messages()->all();
			$response_array = array('success' => false, 'error' => trans('userController.invalid_input'), 'error_code' => 401, 'error_messages' => $error_messages);
			$response_code = 200;
		} else {
			$is_admin = $this->isAdmin($token);
			if ($user_data = $this->getUserData($user_id, $token, $is_admin)) {
				// check for token validity
				if (is_token_active($user_data->token_expiry) || $is_admin) {

					Payment::where('user_id', $user_id)->update(array('is_default' => 0));
					Payment::where('user_id', $user_id)->where('id', $card_token)->update(array('is_default' => 1));

					$payments = array();
					$card_count = DB::table('payment')->where('user_id', '=', $user_id)->count();
					if ($card_count) {
						$paymnt = Payment::where('user_id', $user_id)->orderBy('is_default', 'DESC')->get();
						foreach ($paymnt as $data1) {
							$default = $data1->is_default;
							if ($default == 1) {
								$data['is_default_text'] = "default";
							} else {
								$data['is_default_text'] = "not_default";
							}
							$data['id'] = $data1->id;
							$data['user_id'] = $data1->user_id;
							$data['customer_id'] = $data1->customer_id;
							$data['last_four'] = $data1->last_four;
							$data['card_token'] = $data1->card_token;
							$data['card_type'] = $data1->card_type;
							$data['card_id'] = $data1->card_token;
							$data['is_default'] = $default;
							array_push($payments, $data);
						}
						$response_array = array(
							'success' => true,
							'payments' => $payments
						);
					} else {
						$response_array = array(
							'success' => false,
							'error' => trans('userController.no_card_found')
						);
					}
					$response_code = 200;
				}
			} else {
				if ($is_admin) {
					/* $var = Keywords::where('id', 2)->first();
					  $response_array = array('success' => false, 'error' => "" . $var->keyword . ' ID not Found', 'error_code' => 410); */
					$response_array = array('success' => false, 'error' => trans('userController.id_of') . "" . Config::get('app.generic_keywords.User') . "" . trans('userController.not_found'), 'error_code' => 410);
				} else {
					$response_array = array('success' => false, 'error' => trans('userController.token_not_valid'), 'error_code' => 406);
				}
				$response_code = 200;
			}
		}

		$response = Response::json($response_array, $response_code);
		return $response;
	}

	public function pay_debt() {
		$token = Input::get('token');
		$user_id = Input::get('id');

		$validator = Validator::make(
						array(
					'token' => $token,
					'user_id' => $user_id
						), array(
					'token' => 'required',
					'user_id' => 'required|integer'
						), array(
					'token' => '',
					'user_id' => trans('userController.unique_id_missing')
						)
		);

		if ($validator->fails()) {
			$error_messages = $validator->messages()->all();
			$response_array = array('success' => false, 'error' => trans('userController.invalid_input'), 'error_code' => 401, 'error_messages' => $error_messages);
			$response_code = 200;
		} else {
			$is_admin = $this->isAdmin($token);
			if ($user_data = $this->getUserData($user_id, $token, $is_admin)) {
				// check for token validity
				if (is_token_active($user_data->token_expiry) || $is_admin) {
					$total = $user_data->debt;
					if ($total == 0) {
						$response_array = array('success' => true);
						$response_code = 200;
						$response = Response::json($response_array, $response_code);
						return $response;
					}
					$payment_data = Payment::where('user_id', $user_id)->where('is_default', 1)->first();
					if (!$payment_data)
						$payment_data = Payment::where('user_id', $request->user_id)->first();

					if ($payment_data) {
						$customer_id = $payment_data->customer_id;

						if (Config::get('app.default_payment') == 'stripe') {
							Stripe::setApiKey(Config::get('app.stripe_secret_key'));

							try {
								Stripe_Charge::create(array(
									"amount" => $total * 100,
									"currency" => "usd",
									"customer" => $customer_id)
								);
							} catch (Stripe_InvalidRequestError $e) {
								// Invalid parameters were supplied to Stripe's API
								$user = User::find($user_id);
								$user->debt = $total;
								$user->save();
								$response_array = array('error' => $e->getMessage());
								$response_code = 200;
								$response = Response::json($response_array, $response_code);
								return $response;
							}
							$user_data->debt = 0;
							$user_data->save();
						} else {
							$amount = $total;
							Braintree_Configuration::environment(Config::get('app.braintree_environment'));
							Braintree_Configuration::merchantId(Config::get('app.braintree_merchant_id'));
							Braintree_Configuration::publicKey(Config::get('app.braintree_public_key'));
							Braintree_Configuration::privateKey(Config::get('app.braintree_private_key'));
							$card_id = $payment_data->card_token;
							$result = Braintree_Transaction::sale(array(
										'amount' => $amount,
										'paymentMethodToken' => $card_id
							));

							//Log::info('result = ' . print_r($result, true));
							if ($result->success) {
								$user_data->debt = $total;
							} else {
								$user_data->debt = 0;
							}
							$user_data->save();
						}
					}
					$response_array = array('success' => true);
					$response_code = 200;
				} else {
					$response_array = array('success' => false, 'error' => trans('userController.token_expired'), 'error_code' => 405);
					$response_code = 200;
				}
			} else {
				if ($is_admin) {
					/* $var = Keywords::where('id', 2)->first();
					  $response_array = array('success' => false, 'error' => "" . $var->keyword . ' ID not Found', 'error_code' => 410); */
					$response_array = array('success' => false, 'error' => trans('userController.id_of') . "" . Config::get('app.generic_keywords.User') . "" . trans('userController.not_found'), 'error_code' => 410);
				} else {
					$response_array = array('success' => false, 'error' => trans('userController.token_not_valid'), 'error_code' => 406);
				}
				$response_code = 200;
			}
		}
		$response = Response::json($response_array, $response_code);
		return $response;
	}

	public function paybypaypal() {
		$token = Input::get('token');
		$user_id = Input::get('id');
		$request_id = Input::get('request_id');
		$paypal_id = Input::get('paypal_id');

		$validator = Validator::make(
						array(
					'token' => $token,
					'user_id' => $user_id,
					'paypal_id' => $paypal_id
						), array(
					'token' => 'required',
					'user_id' => 'required|integer',
					'paypal_id' => 'required'
						), array(
					'token' => '',
					'user_id' => trans('userController.unique_id_missing'),
					'paypal_id' => trans('userController.paypal_unique_id_missing')
						)
		);

		if ($validator->fails()) {
			$error_messages = $validator->messages()->all();
			$response_array = array('success' => false, 'error' => trans('userController.invalid_input'), 'error_code' => 401, 'error_messages' => $error_messages);
			$response_code = 200;
		} else {
			$is_admin = $this->isAdmin($token);
			if ($user_data = $this->getUserData($user_id, $token, $is_admin)) {
				// check for token validity
				if (is_token_active($user_data->token_expiry) || $is_admin) {
					//Log::info('paypal_id = ' . print_r($paypal_id, true));
					$req = Requests::find($request_id);
					//Log::info('req = ' . print_r($req, true));
					$req->is_paid = 1;
					$req->payment_id = $paypal_id;
					$req->save();
					$response_array = array('success' => true);
					$response_code = 200;
				}
			}
		}
		$response = Response::json($response_array, $response_code);
		return $response;
	}

	public function paybybitcoin() {
		// $token = Input::get('token');
		// $user_id = Input::get('id');
		// $request_id = Input::get('request_id');
		// $validator = Validator::make(
		// 	array(
		// 		'token' => $token,
		// 		'user_id' => $user_id,
		// 	),
		// 	array(
		// 		'token' => 'required',
		// 		'user_id' => 'required|integer',
		// 	)
		// );
		// if ($validator->fails()) {
		// 	$error_messages = $validator->messages()->all();
		// 		$response_array = array('success' => false, 'error' => trans('userController.invalid_input'), 'error_code' => 401, 'error_messages' => $error_messages );
		// 		$response_code = 200;
		// } else {
		// 	$is_admin = $this->isAdmin($token);
		// 	if ($user_data = $this->getUserData($user_id, $token, $is_admin)) {
		// 		// check for token validity
		// 		if (is_token_active($user_data->token_expiry) || $is_admin) {
		$coinbaseAPIKey = Config::get('app.coinbaseAPIKey');
		$coinbaseAPISecret = Config::get('app.coinbaseAPISecret');
		// coinbase
		$coinbase = Coinbase::withApiKey($coinbaseAPIKey, $coinbaseAPISecret);
		// $balance = $coinbase->getBalance() . " BTC";
		$user = $coinbase->getUser();
		// $contacts = $coinbase->getContacts("user");
		// $currencies = $coinbase->getCurrencies();
		// $rates = $coinbase->getExchangeRate();
		// $paymentButton = $coinbase->createButton(
		//     "Request ID",
		//     "19.99", 
		//     "USD", 
		//     "TRACKING_CODE_1", 
		//     array(
		//            "description" => "My 19.99 USD donation to PL",
		//            "cancel_url" => "http://localhost:8000/user/acceptbitcoin",
		//            "success_url" => "http://localhost:8000/user/acceptbitcoin"
		//        )
		// );
		//Log::info('user = ' . print_r($user, true));

		$response_array = array('success' => true);
		// 		}else{
		// 			$response_array = array('success' => false);
		// 			//Log::error('1');
		// 		}
		// 	}else{
		// 		$response_array = array('success' => false);
		// 		//Log::error('2');
		// 	}
		// }
		$response_code = 200;
		$response = Response::json($response_array, $response_code);
		return $response;
	}

	public function acceptbitcoin() {
		$response = Input::get('response');
		/*
		  Sample Response
		  {
		  "order": {
		  "id": "5RTQNACF",
		  "created_at": "2012-12-09T21:23:41-08:00",
		  "status": "completed",
		  "event": {
		  "type": "completed"
		  },
		  "total_btc": {
		  "cents": 100000000,
		  "currency_iso": "BTC"
		  },
		  "total_native": {
		  "cents": 1253,
		  "currency_iso": "USD"
		  },
		  "total_payout": {
		  "cents": 2345,
		  "currency_iso": "USD"
		  },
		  "custom": "order1234",
		  "receive_address": "1NhwPYPgoPwr5hynRAsto5ZgEcw1LzM3My",
		  "button": {
		  "type": "buy_now",
		  "name": "Alpaca Socks",
		  "description": "The ultimate in lightweight footwear",
		  "id": "5d37a3b61914d6d0ad15b5135d80c19f"
		  },
		  "transaction": {
		  "id": "514f18b7a5ea3d630a00000f",
		  "hash": "4a5e1e4baab89f3a32518a88c31bc87f618f76673e2cc77ab2127b7afdeda33b",
		  "confirmations": 0
		  },
		  "refund_address": "1HcmQZarSgNuGYz4r7ZkjYumiU4PujrNYk"
		  },
		  "customer": {
		  "email": "coinbase@example.com",
		  "shipping_address": [
		  "John Smith",
		  "123 Main St.",
		  "Springfield, OR 97477",
		  "United States"
		  ]
		  }
		  }
		 */
		//Log::info('response = ' . print_r($response, true));
		return Response::json(200, $response);
	}

	public function send_eta() {
		$token = Input::get('token');
		$user_id = Input::get('id');
		$phones = Input::get('phone');
		$request_id = Input::get('request_id');
		$eta = Input::get('eta');

		$validator = Validator::make(
						array(
					'token' => $token,
					'user_id' => $user_id,
					'phones' => $phones,
					'eta' => $eta,
						), array(
					'token' => 'required',
					'phones' => 'required',
					'user_id' => 'required|integer',
					'eta' => 'required'
						), array(
					'token' => '',
					'phones' => trans('userController.contact_number_required'),
					'user_id' => trans('userController.unique_id_missing'),
					'eta' => trans('userController.eta_required')
						)
		);

		if ($validator->fails()) {
			$error_messages = $validator->messages()->all();
			$response_array = array('success' => false, 'error' => trans('userController.invalid_input'), 'error_code' => 401, 'error_messages' => $error_messages);
			$response_code = 200;
		} else {
			$is_admin = $this->isAdmin($token);
			if ($user_data = $this->getUserData($user_id, $token, $is_admin)) {
				// check for token validity
				if (is_token_active($user_data->token_expiry) || $is_admin) {
					// If phones is not an array
					if (!is_array($phones)) {
						$phones = explode(',', $phones);
					}

					//Log::info('phones = ' . print_r($phones, true));

					foreach ($phones as $key) {

						$user = User::where('id', $user_id)->first();
						$secret = str_random(6);

						$request = Requests::where('id', $request_id)->first();
						$request->security_key = $secret;
						$request->save();
						$msg = $user->first_name . ' ' . $user->last_name . ' ' . trans('userController.eta') . ': ' . $eta;
						send_eta($key, $msg);
						//Log::info('Send ETA MSG  = ' . print_r($msg, true));
					}

					$response_array = array('success' => true);
					$response_code = 200;
				} else {
					$response_array = array('success' => false, 'error' => trans('userController.token_expired'), 'error_code' => 405);
					$response_code = 200;
				}
			} else {
				if ($is_admin) {
					/* $var = Keywords::where('id', 2)->first();
					  $response_array = array('success' => false, 'error' => "" . $var->keyword . ' ID not Found', 'error_code' => 410); */
					$response_array = array('success' => false, 'error' => trans('userController.id_of') . "" . Config::get('app.generic_keywords.User') . "" . trans('userController.not_found'), 'error_code' => 410);
				} else {
					$response_array = array('success' => false, 'error' => trans('userController.token_not_valid'), 'error_code' => 406);
				}
				$response_code = 200;
			}
		}

		$response = Response::json($response_array, $response_code);
		return $response;
	}

	public function payment_options_allowed() {
		$token = Input::get('token');
		$user_id = Input::get('id');

		$validator = Validator::make(
						array(
					'token' => $token,
					'user_id' => $user_id,
						), array(
					'token' => 'required',
					'user_id' => 'required|integer'
						), array(
					'token' => '',
					'user_id' => trans('userController.unique_id_missing')
						)
		);

		if ($validator->fails()) {
			$error_messages = $validator->messages()->all();
			$response_array = array('success' => false, 'error' => trans('userController.invalid_input'), 'error_code' => 401, 'error_messages' => $error_messages);
			$response_code = 200;
		} else {
			$is_admin = $this->isAdmin($token);
			if ($user_data = $this->getUserData($user_id, $token, $is_admin)) {
				// check for token validity
				if (is_token_active($user_data->token_expiry)) {
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

					//Log::info('payment_options = ' . print_r($payment_options, true));
					/* SEND REFERRAL & PROMO INFO */
					$settings = Settings::where('key', 'referral_code_activation')->first();
					$referral_code_activation = $settings->value;
					if ($referral_code_activation) {
						$referral_code_activation_txt = trans('userController.referral_on');
					} else {
						$referral_code_activation_txt = trans('userController.referral_off');
					}

					$settings = Settings::where('key', 'promotional_code_activation')->first();
					$promotional_code_activation = $settings->value;
					if ($promotional_code_activation) {
						$promotional_code_activation_txt = trans('userController.promo_on');
					} else {
						$promotional_code_activation_txt = trans('userController.promo_off');
					}
					/* SEND REFERRAL & PROMO INFO */

					// Promo code allowed
					/* $promosett = Settings::where('key', 'promo_code')->first(); */
					if ($promotional_code_activation == 1) {
						$promo_allow = 1;
					} else {
						$promo_allow = 0;
					}

					$response_array = array(
						'success' => true,
						'payment_options' => $payment_options,
						'promo_allow' => $promo_allow,
						'is_referral_active' => $referral_code_activation,
						'is_referral_active_txt' => $referral_code_activation_txt,
						'is_promo_active' => $promotional_code_activation,
						'is_promo_active_txt' => $promotional_code_activation_txt,
					);
				} else {
					/* $var = Keywords::where('id', 2)->first();
					  $response_array = array('success' => false, 'error' => "" . $var->keyword . ' ID not Found', 'error_code' => 410); */
					$response_array = array('success' => false, 'error' => trans('userController.id_of') . "" . Config::get('app.generic_keywords.User') . "" . trans('userController.not_found'), 'error_code' => 410);
				}
			} else {
				$response_array = array('success' => false, 'error' => trans('userController.token_not_valid'), 'error_code' => 406);
			}
			$response_code = 200;
		}
		$response = Response::json($response_array, $response_code);
		return $response;
	}

	public function get_credits() {
		$token = Input::get('token');
		$user_id = Input::get('id');
		$validator = Validator::make(
						array(
					'token' => $token,
					'user_id' => $user_id,
						), array(
					'token' => 'required',
					'user_id' => 'required|integer'
						), array(
					'token' => '',
					'user_id' => trans('userController.unique_id_missing')
						)
		);
		if ($validator->fails()) {
			$error_messages = $validator->messages()->all();
			$response_array = array('success' => false, 'error' => trans('userController.invalid_input'), 'error_code' => 401, 'error_messages' => $error_messages);
			$response_code = 200;
		} else {
			$is_admin = $this->isAdmin($token);
			if ($user_data = $this->getUserData($user_id, $token, $is_admin)) {
				// check for token validity
				if (is_token_active($user_data->token_expiry)) {
					/* $currency_selected = Keywords::find(5); */
					$ledger = Ledger::where('user_id', $user_id)->first();
					if ($ledger) {
						$credits['balance'] = currency_converted($ledger->amount_earned - $ledger->amount_spent);
						/* $credits['currency'] = $currency_selected->keyword; */
						$credits['currency'] = Config::get('app.generic_keywords.Currency');
						$response_array = array('success' => true, 'credits' => $credits);
					} else {
						$response_array = array('success' => false, 'error' => trans('userController.no_credit_found'), 'error_code' => 475);
					}
				} else {
					$response_array = array('success' => false, 'error' => trans('userController.token_not_valid'), 'error_code' => 406);
				}
			} else {
				$response_array = array('success' => false, 'error' => trans('userController.user_not_found'), 'error_code' => 402);
			}
			$response_code = 200;
		}
		$response = Response::json($response_array, $response_code);
		return $response;
	}

	public function logout() {
		if (Request::isMethod('post')) {
			$token = Input::get('token');
			$user_id = Input::get('id');

			$validator = Validator::make(
							array(
						'token' => $token,
						'user_id' => $user_id,
							), array(
						'token' => 'required',
						'user_id' => 'required|integer'
							), array(
						'token' => '',
						'user_id' => trans('userController.unique_id_missing')
							)
			);

			if ($validator->fails()) {
				$error_messages = $validator->messages()->all();
				$response_array = array('success' => false, 'error' => trans('userController.invalid_input'), 'error_code' => 401, 'error_messages' => $error_messages);
				$response_code = 200;
			} else {
				$is_admin = $this->isAdmin($token);
				if ($user_data = $this->getUserData($user_id, $token, $is_admin)) {
					// check for token validity
					if (is_token_active($user_data->token_expiry) || $is_admin) {

						$user_data->latitude = 0;
						$user_data->longitude = 0;
						$user_data->device_token = 0;
						/* $user_data->is_login = 0; */
						$user_data->save();

						$response_array = array('success' => true, 'error' => trans('userController.success_logout'));
						$response_code = 200;
					} else {
						$response_array = array('success' => false, 'error' => trans('userController.token_expired'), 'error_code' => 405);
						$response_code = 200;
					}
				} else {
					if ($is_admin) {
						$response_array = array('success' => false, 'error' => trans('userController.user_id_not_found'), 'error_code' => 410);
					} else {
						$response_array = array('success' => false, 'error' => trans('userController.token_not_valid'), 'error_code' => 406);
					}
					$response_code = 200;
				}
			}
		}
		$response = Response::json($response_array, $response_code);
		return $response;
	}

}
