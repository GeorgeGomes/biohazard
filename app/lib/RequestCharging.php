<?php 

class RequestCharging {

	//opcoes de pagamento
	const PAYMENT_MODE_CARD = 0;
	const PAYMENT_MODE_MONEY = 1;
	const PAYMENT_MODE_VOUCHER = 2;

	//modelos de negócios
	const BUSINESS_MODEL_PERCENTAGE = 'percentage';
	const BUSINESS_MODEL_MONTHLY = 'monthly';

	//intermediadores de pagamento
	const PAYMENT_GATEWAY_PAGARME = 'pagarme';
	const PAYMENT_GATEWAY_STRIPE = 'stripe';

	//status de pagamento
	const PAGARME_PAID = 'paid';
	const PAGARME_PROCESSING = 'processing';
	const PAGARME_AUTHORIZED = 'authorized';
	const PAGARME_REFUNDED = 'refunded';
	const PAGARME_WAITING = 'waiting_payment';
	const PAGARME_PENDING_REFUND = 'pending_refund';
	const PAGARME_REFUSED = 'refused';


	//reliza cobrança de um valor pequeno no cartão do usuário para verificar se existe crédito disponível
	public static function card_test_charge($payment_id){
		if($payment = Payment::find($payment_id)){
			
			//realizar cobrança de 1 real
			$transaction = self::charge_customer(1, $payment->card_token);

			//cobranca realizada com sucesso
			if($transaction && $transaction->status == self::PAGARME_PAID){
				$payment->is_active = 1;
				$payment->save();

				//criar credito de 1 real para o usuário
				$ledger = Ledger::where('user_id', $payment->user_id)->first();
				$ledger->amount_earned += 1;
				$ledger->save();

				send_notifications($payment->user_id, "user", trans('userController.card_test_success'), null);
			}	
			//erro na cobrança			
			else{
				send_notifications($payment->user_id, "user", trans('userController.card_test_failed'), null);
			}
		}
	}

	//realizar cobrança dos valores devidos pelo usuário
	public static function charge_user_debts($user_id, $payment_id){
		if($user = User::find($user_id)){
			if($payment_data = Payment::find($payment_id)){

				//obter request com pagamento pendente
                $completed_request_in_debt = Requests::where('user_id', '=', $user->id)->where('is_completed', '=', 1)->where('is_paid', '=', 0)->first();                
                $canceled_request_in_debt = Requests::where('user_id', '=', $user->id)->where('is_cancelled', '=', 1)->where('is_cancel_fee_paid', '=', 0)->first();

                if($completed_request_in_debt){
                    $pending_request = $completed_request_in_debt;
                }
                else{
                    $pending_request = $canceled_request_in_debt;
                }

            	if($pending_request){  		
					$provider_service = ProviderServices::where('provider_id', $pending_request->confirmed_provider)->first();
					$provider_type = ProviderType::where('id', $provider_service->type)->first();
					$provider_bank_account = ProviderBankAccount::where('provider_id', $pending_request->confirmed_provider)->first();
					
					$provider_percentage = $provider_type->commission_rate;
					$total = $user->debt;
					$settings = Settings::where('key', 'default_business_model')->first();
					$default_business_model = $settings->value;	

					//pagar.me
					if (Config::get('app.default_payment') == self::PAYMENT_GATEWAY_PAGARME){

						//split de pagamento com o prestador
				    	if($default_business_model == self::BUSINESS_MODEL_PERCENTAGE && $provider_bank_account && $provider_percentage > 0){
						    $transaction = self::charge_customer_split_with_provider($total, $payment_data->card_token, $provider_bank_account->recipient_id, $provider_percentage);
				    	}

				    	// valor inteiro do pagamento transferido para o admin
				    	else{
			    			$transaction = self::charge_customer($total, $payment_data->card_token);
						}

						//pagamento efetuado com sucesso
						if($transaction->status == self::PAGARME_PAID){

							//ativar cartão
							$card_count = DB::table('payment')->where('user_id', '=', $payment_data->user_id)->where('is_default', '=', 1)->count();
							if ($card_count > 0) {
								$payment_data->is_default = 0;
							}
							else {
								$payment_data->is_default = 1;
							}
							$payment_data->is_active = 1;
							$payment_data->save();

							//enviar notificação ao cliente
							send_notifications($payment_data->user_id, "user", trans('userController.debts_charge_success'), null);
							
							//atualizar request
							if($pending_request->is_cancelled == 1){
								$pending_request->is_cancel_fee_paid = 1;
							}
							else{
								$pending_request->is_paid = 1;
							}
							
							$pending_request->payment_platform_rate += $transaction->cost/100;
							$pending_request->save();

							//zerar divida do usuario
							$user->debt = 0;
							$user->save();
						}
						else{
							send_notifications($payment_data->user_id, "user", trans('userController.debts_charge_failed'), null);
						}
					}

				}
				else{
					$user->debt = 0;
					$user->save();
				}
			}
		}
	}

	//realiza cobrança da taxa base da corrida
	public static function request_charge_base_price($request_id){
		$request = Requests::find($request_id);
		if($request){
			$provider = Provider::find($request->confirmed_provider);
			$user = User::find($request->user_id);
			if($provider && $user){
				$provider_service = ProviderServices::where('provider_id', $provider->id)->first();
				$provider_type = ProviderType::where('id', $provider_service->type)->first();
				$provider_bank_account = ProviderBankAccount::where('provider_id', $provider->id)->first();
				$payment_data = Payment::where('user_id', $request->user_id)->where('is_default', 1)->first();

				$settings = Settings::where('key', 'default_business_model')->first();
				$default_business_model = $settings->value;	
				$provider_commission = $provider_type->commission_rate;	

				$total = $provider_type->base_price;				

				if($total == 0){
					$request->is_base_fee_paid = 1;
				}
				else{				

					//pagamento por meio de dinheiro ou voucher
					if ($request->payment_mode == self::PAYMENT_MODE_MONEY || $request->payment_mode == self::PAYMENT_MODE_VOUCHER){
						$request->is_base_fee_paid = 1;

						//prestador repassa a empresa
						if($default_business_model == self::BUSINESS_MODEL_PERCENTAGE){
							$provider_payment_remaining = $total - $total * $provider_commission/100;
							$provider_refund_remaining = 0;
						}
						//pagamento fica inteiramente com o prestador, nada deve ser repassado à empresa
						else if($default_business_model == self::BUSINESS_MODEL_MONTHLY){
							$provider_payment_remaining = 0;
							$provider_refund_remaining = 0;
						}
					}

					//pagamento com cartão de crédito
					else{
						//pagar.me
						if (Config::get('app.default_payment') == self::PAYMENT_GATEWAY_PAGARME){

						    PagarMe::setApiKey(Config::get('app.pagarme_api_key'));
					    	try{
				    			//modelo de negócios percentual: taxa de cancelamento repassada totalmente para o prestador
						    	if($default_business_model == self::BUSINESS_MODEL_PERCENTAGE && $provider_bank_account && $provider_type->commission_rate > 0){
							    	$transaction = self::charge_customer_split_with_provider($total, $payment_data->card_token, $provider_bank_account->recipient_id, $provider_type->commission_rate);
							    }
							    else{
				    				$transaction = self::charge_customer($total, $payment_data->card_token);
							    }

						    	if($transaction){
							    	//pagamento concluído com sucesso
								    if($transaction->status == self::PAGARME_PAID){
										$request->is_base_fee_paid = 1;								
										$request->payment_platform_rate += $transaction->cost/100;

										$request->provider_commission +=  $total * $provider_commission/100;
																	
										$request->card_payment += $total;

										$provider_payment_remaining = 0;
										$provider_refund_remaining = 0;
								    }

							    	//pagamento recusado
								    else {
								    	//cancelar requisição
								    	$request->is_cancelled = 1;

							    		//enviar notificações ao motorista e ao usuário
		                                $msg_array = array();
		                                $msg_array['request_id'] = $request->id;
		                                $msg_array['unique_id'] = 2;

							    		$title = trans('providerController.payment_creditcard_fail');
										send_notifications($request->user_id, 'user', $title, $msg_array);
										send_notifications($request->confirmed_provider, 'provider', $title, $msg_array);

										//salvar debito do usuário
										$user->debt += $total;
										$user->save();
								    }

							    	$transaction_cost = $transaction->cost/100;
							    	//salvar registro na tabela transactions

								    $provider_value = $total * $provider_commission/100;
								    

								    $net_value = $total - $transaction_cost - $provider_value;
							    	

								    $charge_transaction = new Transaction();
								    $charge_transaction->type = Transaction::BASE_TAX;
								    $charge_transaction->status = $transaction->status;
								    $charge_transaction->gross_value = $total;		
								    $charge_transaction->provider_value = $provider_value;				    
								    $charge_transaction->gateway_tax_value = $transaction_cost;
								    $charge_transaction->gateway_transaction_id = $transaction->id;
								    $charge_transaction->net_value = $net_value;

								    if(isset($transaction->split_rules)){
								    	foreach($transaction->split_rules as $rule){
								    		if($rule->recipient_id != ""){
								    			$charge_transaction->split_id = $rule->id;
								    			$charge_transaction->split_status = Transaction::SPLIT_WAITING_FUNDS;
								    		}
								    	}
								    }

								    $charge_transaction->save();

								    $request->base_tax_transaction_id = $charge_transaction->id;
						    	}
							}
							//impossível realizar pagamento
							catch(PagarMe_Exception $ex){							
								$user->debt += $total;
								$user->save();
							}
						}
						//stripe
						else if(Config::get('app.default_payment') == self::PAYMENT_GATEWAY_STRIPE){

						}
						//brain trees
						else{

						}
					}
				}

				$request->save();
									
			}
		}
	}

	//realiza cobrança do valor total da corrida ao final dela
	public static function request_complete_charge($request_id, $distance, $time){
		$request = Requests::find($request_id);
		if($request){
			$provider = Provider::find($request->confirmed_provider);
			$user = User::find($request->user_id);

			if($provider && $user){
				$provider_bank_account = ProviderBankAccount::where('provider_id', $provider->id)->first();
				$payment_data = Payment::where('user_id', $request->user_id)->where('is_default', 1)->first();
				$request_service = RequestServices::where('request_id', $request_id)->first();

				$request_options = RequestOptions::where('request_id', $request_id)->first();
				if($request_options){
					$provider_service = ProviderServices::where('id', $request_options->provider_service_id)->first();
				}
				else{
					$provider_service = ProviderServices::where('provider_id', $provider->id)->where('type', $request_service->type)->first();
					if(!$provider_service){
						$provider_service = ProviderServices::where('provider_id', $provider->id)->first();
					}
				}

				$provider_type = ProviderType::where('id', $provider_service->type)->first();

				//caso a distancia de volta do prestador deva ser considerada, dobrar a distância
				if($provider_type->charge_provider_return == 1){
					$charged_distance = $distance * 2;
				}
				else{
					$charged_distance = $distance;
				}
				

				if ($request->is_started == 1) {

					//obter metodo de pagamento padrao
					$settings = Settings::where('key', 'default_charging_method_for_users')->first();
					$pricing_type = $settings->value;

					//obter unidade de distancia padrao
					$settings = Settings::where('key', 'default_distance_unit')->first();
					$default_distance_unit = $settings->value;

					//obter configurações de códigos de desconto
					$allow_promo_code = $allow_referral_code = $prom_for_card = $prom_for_cash = $ref_for_card = $ref_for_cash = $ref_total = $promo_total = 0;

					$settings = Settings::where('key', 'promotional_code_activation')->first();
					$allow_promo_code = $settings->value;

					$settings = Settings::where('key', 'referral_code_activation')->first();
					$allow_referral_code = $settings->value;

					//obter configurações de modelo de negócios
					$settings = Settings::where('key', 'default_business_model')->first();
					$default_business_model = $settings->value;
					
					//REALIZAR PRECIFICAÇÃO

					//obter valores do tipo de serviço
					$price_per_unit_distance = $provider_type->price_per_unit_distance;
					$price_per_unit_time = $provider_type->price_per_unit_time;
					$base_price = $provider_type->base_price;

					$distance_cost = $price_per_unit_distance * $charged_distance;
					$time_cost = $price_per_unit_time * $time;
					$request_total_cost = $base_price + $distance_cost + $time_cost;
					

					//atualizar informaçoes do servico da solicitação na tabela request_services
					$request_service->base_price = $base_price;
					$request_service->distance_cost = $distance_cost;
					$request_service->time_cost = $time_cost;
					$request_service->total = $request_total_cost;
					$request_service->save();

					//valor a ser cobrado no fim da solicitação
					$total = $request_service->distance_cost + $request_service->time_cost;

					//atualizar requisição
					$request->is_completed = 1;
					$request->distance = $distance;
					$request->time = $time;
					$request->security_key = NULL;
					$request->total = $request_total_cost;

					$user_data = User::where('id', $request->user_id)->first();

					/* PRECIFICAÇÂO E APLICAÇÃO DE DESCONTOS */

					//calculo do valor recebido pelo prestador de acordo com o modelo de negócios

					//modelo de negócios percentual
					if($default_business_model == self::BUSINESS_MODEL_PERCENTAGE){
						$provider_percentage = $provider_type->commission_rate;
						$provider_value = $provider_percentage/100 * $total;						
					}

					//modelo de negócios mensal (prestador fica com o valor total da corrida)
					else if($default_business_model == self::BUSINESS_MODEL_MONTHLY){
						$provider_value = $total;
						$provider_percentage = 100;
					}

					//dinheiro e voucher
					if ($request->payment_mode == self::PAYMENT_MODE_MONEY || $request->payment_mode == self::PAYMENT_MODE_VOUCHER) {
						if($default_business_model == self::BUSINESS_MODEL_PERCENTAGE){
							$provider_payment_remaining = $total - $provider_value;
							$provider_refund_remaining = 0;
						}
						else {
							$provider_payment_remaining = 0;
							$provider_refund_remaining = 0;
						}

						if ($allow_promo_code) {
							$settings = Settings::where('key', 'get_promotional_profit_on_card_payment')->first();
							$prom_for_card = $settings->value;

							if ($prom_for_card && $total > 0) {												
								if ($promo_code = PromoCodes::where('id', $request->promo_id)->first()) {

									//código promocional percentual
									if ($promo_code->type == 1) {
										$promo_total = $total * (($promo_code->value) / 100);
										$total = $total - $promo_total;
										if ($total <= 0) {
											$total = 0;
										}
									}

									//código promocional absoluto
									else {
										$promo_total = $promo_code->value;
										$total = $total - $promo_total;
										if ($total <= 0) {
											$total = 0;
										}
									}
								}
							}
						}


						if ($allow_referral_code) {
							$settings = Settings::where('key', 'get_referral_profit_on_card_payment')->first();
							$ref_for_card = $settings->value;

							if ($ref_for_card) {
								$ledger = Ledger::where('user_id', $request->user_id)->first();
								if ($ledger) {
									$balance = $ledger->amount_earned - $ledger->amount_spent;
									if ($balance > 0) {
										if ($total > 0) {
											if ($total > $balance) {
												$ref_total = $balance;
												$ledger_temp = Ledger::find($ledger->id);
												$ledger_temp->amount_spent = $ledger_temp->amount_spent + $balance;
												$ledger_temp->save();
												$total = $total - $balance;
											}

											else {
												$ref_total = $total;
												$ledger_temp = Ledger::find($ledger->id);
												$ledger_temp->amount_spent = $ledger_temp->amount_spent + $total;
												$ledger_temp->save();
												$total = 0;
											}
										}
									}
								}
							}
						}
					}

					//cartão de crédito
					else if ($request->payment_mode == self::PAYMENT_MODE_CARD) {
						$provider_payment_remaining = 0;
						$provider_refund_remaining = 0;

						if ($allow_promo_code) {
							$settings = Settings::where('key', 'get_promotional_profit_on_cash_payment')->first();
							$prom_for_cash = $settings->value;
							if ($prom_for_cash) {
								if ($total > 0) {
									if ($promo_code = PromoCodes::where('id', $request->promo_id)->first()) {
										if ($promo_code->type == 1) {
											$promo_total = $total * (($promo_code->value) / 100);
											$total = $total - $promo_total;
											if ($total <= 0) {
												$total = 0;
											}
										} else {
											$promo_total = $promo_code->value;
											$total = $total - $promo_total;
											if ($total <= 0) {
												$total = 0;
											}
										}
										$provider_payment_remaining = $provider_payment_remaining + $promo_total;
									}
								}
							}
						}

						if ($allow_referral_code) {
							$settings = Settings::where('key', 'get_referral_profit_on_cash_payment')->first();
							$ref_for_cash = $settings->value;

							if ($ref_for_cash) {
								// charge client
								$ledger = Ledger::where('user_id', $request->user_id)->first();
								if ($ledger) {
									$balance = $ledger->amount_earned - $ledger->amount_spent;
									if ($balance > 0) {
										if ($total > 0) {
											if ($total > $balance) {
												$ref_total = $balance;
												$ledger_temp = Ledger::find($ledger->id);
												$provider_payment_remaining = $provider_payment_remaining + $balance;
												$ledger_temp->amount_spent = $ledger_temp->amount_spent + $balance;
												$ledger_temp->save();
												$total = $total - $balance;
											}
											else {
												$ref_total = $total;
												$ledger_temp = Ledger::find($ledger->id);
												$provider_payment_remaining = $provider_payment_remaining + $total;
												$ledger_temp->amount_spent = $ledger_temp->amount_spent + $total;
												$ledger_temp->save();
												$total = 0;
											}
										}
									}
								}
							}
						}
					}

					/* PAGAMENTO */

					$settings = Settings::where('key', 'payment_money')->first();
					$allow_payment_money = $settings->value;

					//dinheiro
					if ($request->payment_mode == self::PAYMENT_MODE_MONEY && $allow_payment_money == 1) {
						$request->is_paid = 1;
						$payment_type = trans('providerController.payment_by_cash');
					}

					//voucher
					else if ($request->payment_mode == self::PAYMENT_MODE_VOUCHER) {
						$request->is_paid = 1;
						$payment_type = trans('providerController.payment_by_voucher');
					}

					//cartão
					else {
						if ($total == 0) {
							$request->is_paid = 1;
						}

						else {
							$payment_data = Payment::where('user_id', $request->user_id)->where('is_default', 1)->first();
							if (!$payment_data)
								$payment_data = Payment::where('user_id', $request->user_id)->first();

							if ($payment_data) {

								$customer_id = $payment_data->customer_id;
								$settings = Settings::where('key', 'transfer')->first();
								$allow_transfer = $settings->value;

								//Realizar cobrança pelo intermediador de pagamentos

								//pagarme
								if (Config::get('app.default_payment') == self::PAYMENT_GATEWAY_PAGARME){
			
							    	try{						    			
						    			//modelo de negócios percentual: split de pagamento percentual com o prestador
								    	if($default_business_model == self::BUSINESS_MODEL_PERCENTAGE && $provider_bank_account && $provider_percentage > 0){
										    $transaction = self::charge_customer_split_with_provider($total, $payment_data->card_token, $provider_bank_account->recipient_id, $provider_percentage);
    				    					$transaction_cost = $transaction->cost/100;
								    		$net_value = $total - $transaction_cost - $provider_value;
								    	}

								    	// valor inteiro do pagamento transferido para o admin
								    	else{
							    			$transaction = self::charge_customer($total, $payment_data->card_token);
							    			$provider_value = 0;
							    			$transaction_cost = $transaction->cost/100;
							    			$net_value = $total - $transaction_cost;
										}

										//salvar registro na tabela transaction			    				
			    						if($transaction){
										    $charge_transaction = new Transaction();
										    $charge_transaction->type = Transaction::REQUEST_PRICE;
										    $charge_transaction->status = $transaction->status;
										    $charge_transaction->gross_value = $total;		
										    $charge_transaction->provider_value = $provider_value;				    
										    $charge_transaction->gateway_tax_value = $transaction_cost;
										    $charge_transaction->gateway_transaction_id = $transaction->id;
										    $charge_transaction->net_value = $net_value;

				    						if(isset($transaction->split_rules)){
										    	foreach($transaction->split_rules as $rule){
										    		if($rule->recipient_id != ""){
										    			$charge_transaction->split_id = $rule->id;
										    			$charge_transaction->split_status = Transaction::SPLIT_WAITING_FUNDS;
										    		}
										    	}
										    }

										    $charge_transaction->save();

										    $request->request_price_transaction_id = $charge_transaction->id;
										}

									    if($transaction && $transaction->status == self::PAGARME_PAID){
											$request->is_paid = 1;
											
											$request->payment_platform_rate += $transaction->cost/100;
											$request->provider_commission +=  $provider_value;
											$payment_type = trans('providerController.payment_creditcard_success');
									    }
									    else{
									    	
											$request->is_paid = 0;
											$payment_type = trans('providerController.payment_creditcard_fail');

											$title = trans('providerController.payment_creditcard_fail');
											send_notifications($request->user_id, 'user', $title, null);
											send_notifications($request->confirmed_provider, 'provider', $title, null);

											$user->debt += $total;
											$user->save();

											$ledger = Ledger:: where('user_id', $request->user_id)->first();
											if ($ledger) {
												$ledger_temp = Ledger::find($ledger->id);
												$ledger_temp->amount_spent = $ledger_temp->amount_spent - $ref_total;
												$ledger_temp->save();
											}
									    }
									}
									catch(PagarMe_Exception $ex){
										//Parametros inválidos passados para o Pagar.Me
										$request->is_paid = 0;
										
										$user->debt += $total;
										$user->save();
									} 
								}

								//stripe
								else if (Config::get('app.default_payment') == self::PAYMENT_GATEWAY_STRIPE) {

									Stripe::setApiKey(Config::get('app.stripe_secret_key'));
									try {
										$charge = Stripe_Charge::create(array(
													"amount" => floor($total) * 100,
													"currency" => "usd",
													"customer" => $customer_id)
										);
										if ($charge->paid) {
											$request->is_paid = 1;
											$payment_type = trans('providerController.payment_creditcard_success');
										} else {
											$request->is_paid = 0;
											$payment_type = trans('providerController.payment_creditcard_fail');
											$ledger = Ledger:: where('user_id', $request->user_id)->first();
											if ($ledger) {
												$ledger_temp = Ledger::find($ledger->id);
												$ledger_temp->amount_spent = $ledger_temp->amount_spent - $ref_total;
												$ledger_temp->save();
											}
											$change_to_cash = Requests::find($request_id);
											$request->payment_mode = $change_to_cash->payment_mode = 1;
											$change_to_cash->save();

											/* Client Side Push */
											$title = trans('providerController.card_declined_pay_cash') . "" . Config::get('app.generic_keywords.Provider') . "" . trans('providerController.for_your') . "" . Config::get('app.generic_keywords.Trip') . '.';
											$response_array = array(
												'request_id' => $request_id, 'success' => true, 'message' => $title,);
											$message = $response_array;
											send_notifications($request->user_id, 'user', $title, $message);
											/* Client Side Push END */
											
											/* Driver Side Push */
											$title = trans('providerController.collect_cash') . "" . Config::get('app.generic_keywords.User') . "" . trans('providerController.for_your') . "" . Config::get('app.generic_keywords.Trip') . '.';
											$response_array = array(
												'request_id' => $request_id,
												'success' => true,
												'message' => $title,
											);
											$message = $response_array;
											send_notifications($provider_id, "provider", $title, $message);
											/* Driver Side Push END */

										}
									} catch (Stripe_InvalidRequestError $e) {
										$request->is_paid = 0;
										// Invalid parameters were supplied to Stripe's API
										$ownr = User::find($request->user_id);
										$ownr->debt = $total;
										$ownr->save();
										$response_array = array('error' => $e->getMessage());
										$response_code = 200;
										$response = Response::json($response_array, $response_code);

										return $response;
									}
									$settng = Settings::where('key', 'service_fee')->first();
									if ($allow_transfer == 1 && $provider_data->merchant_id != "" && Config::get('app.generic_keywords.Currency') == '$') {

										$request->transfer_amount = floor($total - $settng->value * $total / 100);
									}
								}

								//braintrees
								else {
									try {
										Braintree_Configuration::environment(Config::get('app.braintree_environment'));
										Braintree_Configuration::merchantId(Config::get('app.braintree_merchant_id'));
										Braintree_Configuration::publicKey(Config::get('app.braintree_public_key'));
										Braintree_Configuration::privateKey(Config::get('app.braintree_private_key'));
										if ($allow_transfer == 1) {
											$result = Braintree_Transaction::sale(array(
														'amount' => $total,
														'paymentMethodNonce' => $customer_id
											));
				
										} else {
											$result = Braintree_Transaction::sale(array(
														'amount' => $total,
														'paymentMethodNonce' => $customer_id
											));
										}

										if ($result->success) {
											$request->is_paid = 1;
											$payment_type = trans('providerController.payment_creditcard_success');
										} else {
											$request->is_paid = 0;
											$payment_type = trans('providerController.payment_creditcard_fail');
											$ledger = Ledger::where('user_id', $request->user_id)->first();
											if ($ledger) {
												$ledger_temp = Ledger::find($ledger->id);
												$ledger_temp->amount_spent = $ledger_temp->amount_spent - $ref_total;
												$ledger_temp->save();
											}
											$change_to_cash = Requests::find($request_id);
											$request->payment_mode = $change_to_cash->payment_mode = 1;
											$change_to_cash->save();
											/* Client Side Push */
											$title = trans('providerController.card_declined_pay_cash') . "" . Config::get('app.generic_keywords.Provider') . "" . trans('providerController.for_your') . "" . Config::get('app.generic_keywords.Trip') . '.';
											$response_array = array(
												'success' => true,
												'request_id' => $request_id,
												'message' => $title,
												'payment_type' => $change_to_cash->payment_mode,
											);
											$message = $response_array;
											send_notifications($request->user_id, 'user', $title, $message);
											/* Client Side Push END */

											/* Driver Side Push */
											$title = trans('providerController.collect_cash') . "" . Config::get('app.generic_keywords.User') . "" . trans('providerController.for_your') . "" . Config::get('app.generic_keywords.Trip') . '.';
											$response_array = array('success' => true,
												'message' => $title,
												'request_id' => $request_id,
												'payment_type' => $change_to_cash->payment_mode,
											);
											$message = $response_array;
											send_notifications($provider_id, "provider", $title, $message);
											/* Driver Side Push END */
										}
									} catch (Exception $e) {
										$response_array = array('success' => false, 'error' => $e, 'error_code' => 405);
										$response_code = 200;
										$response = Response::json($response_array, $response_code);
										return $response;
									}
								}
							}
						}
					}

					$request->card_payment += $total;
					$request->payment_remaining += $provider_payment_remaining;
					$request->refund_remaining += $provider_refund_remaining;
					$request->ledger_payment = $ref_total;
					$request->promo_payment = $promo_total;
					$request->save();

					if ($request->is_paid == 1) {

						$user = User::find($request->user_id);
						$settings = Settings::where('key', 'sms_payment_generated')->first();
						$pattern = $settings->value;
						$pattern = str_replace('%user%', $user->first_name . " " . $user->last_name, $pattern);
						$pattern = str_replace('%id%', $request->id, $pattern);
						$pattern = str_replace('%user_mobile%', $user->phone, $pattern);
						sms_notification(1, 'admin', $pattern);
					}
				}
			}
		}					
	}

	//reliza cobrança da taxa de cancelamento
	public static function request_charge_cancel_fee($request_id){
		if($request = Requests::find($request_id)){

			$provider = Provider::find($request->confirmed_provider);
			$user = User::find($request->user_id);
			if($provider && $user){

				$provider_bank_account = ProviderBankAccount::where('provider_id', $provider->id)->first();
				$payment_data = Payment::where('user_id', $request->user_id)->where('is_default', 1)->first();

				$settings = Settings::where('key', 'default_business_model')->first();
				$default_business_model = $settings->value;	

				$provider_service = ProviderServices::where('provider_id', $provider->id)->first();
				$provider_type = ProviderType::where('id', $provider_service->type)->first();
				$provider_commission = $provider_type->commission_rate;
				$total = $provider_type->base_price;						

				//pagamento por meio de dinheiro ou voucher
				if ($request->payment_mode == self::PAYMENT_MODE_MONEY || $request->payment_mode == self::PAYMENT_MODE_VOUCHER){
					$request->is_cancel_fee_paid = 1;

					$request->save();
				}

				//pagamento com cartão de crédito
				else{

					//pagar.me
					if (Config::get('app.default_payment') == self::PAYMENT_GATEWAY_PAGARME){

					    PagarMe::setApiKey(Config::get('app.pagarme_api_key'));

				    	try{
				    		$provider_value = 0;
				    		//modelo de negócios percentual: cobranca da taxa de cancelamento com repasse ao prestador
					    	if($default_business_model == self::BUSINESS_MODEL_PERCENTAGE && $provider_bank_account && $provider_commission > 0){
						    	$transaction = self::charge_customer_split_with_provider($total, $payment_data->card_token, $provider_bank_account->recipient_id, $provider_commission);
						    	$provider_value = $total * $provider_commission/100;
						    }

						    //modelo de negócios mensal: cobranca de taxa base sem repasse ao prestador
						    else {
						    	$provider_commission = 0;
			    				$transaction = self::charge_customer($total, $payment_data->card_token);
						    }

							//salvar registro na tabela transaction

	    					$transaction_cost = $transaction->cost/100;

						    $charge_transaction = new Transaction();
						    $charge_transaction->type = Transaction::CANCEL_TAX;
						    $charge_transaction->status = $transaction->status;
						    $charge_transaction->gross_value = $total;		
						    $charge_transaction->provider_value = $provider_value;				    
						    $charge_transaction->gateway_tax_value = $transaction_cost;
						    $charge_transaction->gateway_transaction_id = $transaction->id;
						    $charge_transaction->net_value = $total - $transaction_cost - $provider_value;


    						if(isset($transaction->split_rules)){
						    	foreach($transaction->split_rules as $rule){
						    		if($rule->recipient_id != ""){
						    			$charge_transaction->split_id = $rule->id;
						    			$charge_transaction->split_status = Transaction::SPLIT_WAITING_FUNDS;
						    		}
						    	}
						    }

						    $charge_transaction->save();

						    $request->request_price_transaction_id = $charge_transaction->id;

					    	//pagamento concluído com sucesso
						    if($transaction && $transaction->status == self::PAGARME_PAID){
						    								
								$request->payment_platform_rate += $transaction->cost/100;
								$request->provider_commission +=  $total * $provider_commission/100;
								$request->total = $total;
								$request->is_cancel_fee_paid = 1;
								$request->save();
						    }

			    			//erro ao processar pagamento: atualizar debito do usuario
						    else{

								$title = trans('providerController.payment_creditcard_fail');
								send_notifications($request->user_id, 'user', $title, null);
								send_notifications($request->confirmed_provider, 'provider', $title, null);

								$user->debt += $total;
								$user->save();
						    }
						}
						catch(PagarMe_Exception $ex){
							//erro ao processar pagamento: atualizar debito do usuario							
							$user->debt += $total;
							$user->save();
						}
					}

					//stripe
					else if(Config::get('app.default_payment') == self::PAYMENT_GATEWAY_STRIPE){
					}
					//brain trees
					else{

					}
				}
									
			}

		}
	}

	//realiza cobrança no cartão do usuário sem repassar valor algum ao prestador
	public static function charge_customer($value, $card_id){
		if (Config::get('app.default_payment') == self::PAYMENT_GATEWAY_PAGARME){

		    PagarMe::setApiKey(Config::get('app.pagarme_api_key'));
			try{

				$paid = false;
				$countPaymentAttempts = 1;

				//realizar três tentativas de cobrança
				while($countPaymentAttempts <= 3){

					// valor inteiro do pagamento transferido para o admin
					if($card = PagarMe_Card::findById($card_id)){
						
					    $transaction = new PagarMe_Transaction(array(
					        "amount" => 	floor($value * 100),
					        "card" => 	$card
					    ));

					    $transaction->charge();

				    	//retornar transação caso ela não tenha sido recusada
					    if($transaction->status != self::PAGARME_REFUSED){
					    	return $transaction;
						}
					}
					else{
						return NULL;
					}
					$countPaymentAttempts ++;
				}

				return $transaction;
			}
			catch(PagarMe_Exception $ex){
				//return $ex->getMessage(); 
				return NULL;
			}
		}

		//stripe
		else if (Config::get('app.default_payment') == self::PAYMENT_GATEWAY_STRIPE) {

			
		}

		//braintrees
		else {

		}
		return NULL;
	}	

	//relaliza cobrança no cartão do usuário com repasse ao prestador
	public static function charge_customer_split_with_provider($value, $card_id, $provider_recipient_id, $provider_value, $fixed_split = false){ 
		if (Config::get('app.default_payment') == self::PAYMENT_GATEWAY_PAGARME){

		    PagarMe::setApiKey(Config::get('app.pagarme_api_key'));
			try{

				$countPaymentAttempts = 1;
				//realizar 3 tentativas de pagamento
				while($countPaymentAttempts <= 3){
					if($card = PagarMe_Card::findById($card_id)){

						if($fixed_split){
							$admin_value = $value - $provider_value;
							$admin_value = floor($admin_value * 100);
							$provider_value = floor($provider_value * 100);
						}
						else{
							$admin_value = 100 - $provider_value;
						}
						//split de pagamento com o prestador
					    $transaction = new PagarMe_Transaction(array(
					        "amount" => floor($value * 100),
					        "card" => 	$card,
					        "split_rules" => array(
					        	//prestador
					        	array(
					        		"recipient_id" => $provider_recipient_id,
					        		sprintf("%s", $fixed_split? "amount" : "percentage") =>  $provider_value,
					        		"charge_processing_fee" => false,
					        		"liable" => true  //assume risco de transação (possíveis estornos)
				        		),
				        		//admin
								array(
					        		"recipient_id" => '',
					        		sprintf("%s", $fixed_split? "amount" : "percentage") =>  $admin_value,
					        		"charge_processing_fee" => true, //responsável pela taxa de processamento
					        		"liable" => true  //assume risco da transação possíveis estornos
				        		)
				        	)
					    ));

					    $transaction->charge();

					    if($transaction->status != self::PAGARME_REFUSED){
					    	return $transaction;
					    }
					}
					else{
						return NULL;
					}
				}
			    return $transaction;
			}
			catch(PagarMe_Exception $ex){
				//return $ex->getMessage();
				return NULL;
			}
		}

		//stripe
		else if (Config::get('app.default_payment') == self::PAYMENT_GATEWAY_STRIPE) {

		}

		//braintrees
		else {

		}
		return NULL;
	}

	//realiza cobrança no cartão do usuário sem repassar valor algum ao prestador
	public static function charge_customer_transfer_to_provider($value, $card_id, $provider_recipient_id){
		if (Config::get('app.default_payment') == self::PAYMENT_GATEWAY_PAGARME){

		    PagarMe::setApiKey(Config::get('app.pagarme_api_key'));
			try{

				$paid = false;
				$countPaymentAttempts = 1;

				//realizar três tentativas de cobrança
				while($countPaymentAttempts <= 3){

					// valor inteiro do pagamento transferido para o admin
					if($card = PagarMe_Card::findById($card_id)){
						
					    $transaction = new PagarMe_Transaction(array(
					        "amount" => 	floor($value * 100),
					        "card" => 	$card,
					        "split_rules" => array(
					        	//prestador
					        	array(
					        		"recipient_id" => $provider_recipient_id,
					        		"percentage" =>  100,
					        		"charge_processing_fee" => true,
					        		"liable" => true  //assume risco de transação (possíveis estornos)
				        		)
				        	)
					    ));

					    $transaction->charge();

				    	//retornar transação caso ela não tenha sido recusada
					    if($transaction->status != self::PAGARME_REFUSED){
					    	return $transaction;
						}
					}
					else{
						return NULL;
					}
					$countPaymentAttempts ++;
				}

				return $transaction;
			}
			catch(PagarMe_Exception $ex){
				//return $ex->getMessage(); 
				return NULL;
			}
		}

		//stripe
		else if (Config::get('app.default_payment') == self::PAYMENT_GATEWAY_STRIPE) {

			
		}

		//braintrees
		else {

		}
		return NULL;
	}	

	public static function update_transaction_status(){
		$charge_transactions = Transaction::where('split_status', Transaction::SPLIT_WAITING_FUNDS)->get();

		//percorrer todas as transactions cujo dinheiro nã foi transferido para o prestador
		foreach($charge_transactions as $charge_transaction){
			//obter recebíveis da transação (cada transação possi apenas um)
			$payables = self::get_transaction_payables($charge_transaction->gateway_transaction_id);
			foreach($payables as $payable){
				if(isset($payable->status) && $payable->status != ""){
					//atualizar status no banco
	    			$charge_transaction->split_status = $payable->status;
				}
			}
			$charge_transaction->save();
		}
		
	}

	//retorna os recebíveis de uma transação
	private static function get_transaction_payables($transaction_id){
		try{
			$url = sprintf('https://api.pagar.me/1/transactions/%s/payables?api_key=%s',
				$transaction_id,
				Config::get('app.pagarme_api_key'));

			$ch = curl_init($url);
			curl_setopt($ch, CURLOPT_TIMEOUT, 5);
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			$data = curl_exec($ch);
			curl_close($ch);
			return json_decode($data);
		}
		catch(Exception $ex){
			throw $ex;
			
		}
	}

}

?>