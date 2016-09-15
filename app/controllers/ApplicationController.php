<?php

class ApplicationController extends BaseController {

    private function _braintreeConfigure() {
        Braintree_Configuration::environment(Config::get('app.braintree_environment'));
        Braintree_Configuration::merchantId(Config::get('app.braintree_merchant_id'));
        Braintree_Configuration::publicKey(Config::get('app.braintree_public_key'));
        Braintree_Configuration::privateKey(Config::get('app.braintree_private_key'));
    }

    public function pages() {
        $informations = Information::WhereNotNull('id');
        $user_type = trim(Input::get('user_type'));
        if($user_type && $user_type != 'both') {
            $informations->where('type', '=', $user_type);
            $informations->orWhere('type', '=', 'both');
        }
        elseif($user_type && $user_type == 'both'){
            $informations->where('type', '=', 'both');
        }
        
        $informations_array = array();
        foreach ($informations->get() as $information) {
            $data = array();
            $data['id'] = $information->id;
            $data['title'] = $information->title;
            //$data['content'] = $information->content;
            $data['icon'] = $information->icon;
            $data['type'] = $information->type;
            array_push($informations_array, $data);
        }
        $response_array = array();
        $response_array['success'] = true;
        $response_array['informations'] = $informations_array;
        $response_code = 200;
        $response = Response::json($response_array, $response_code);
        return $response;
    }

    public function get_page() {
        $id = Request::segment(3);
        $information = Information::find($id);
        $response_array = array();
        if ($information) {
            $response_array['success'] = true;
            $response_array['title'] = $information->title;
            $response_array['content'] = $information->content;
            $response_array['icon'] = $information->icon;
        } else {
            $response_array['success'] = false;
        }
        $response_code = 200;
        $response = Response::json($response_array, $response_code);
        return $response;
    }

    public function types() {
        $types = ProviderType::where('is_visible', '=', 1)->get();
        $type_array = array();
        $settunit = Settings::where('key', 'default_distance_unit')->first();
        $maxCancelTime = Settings::where('key', 'cancel_maximum_trip_time')->first();
        $unit = $settunit->value;
        if ($unit == 0) {
            $unit_set = 'km';
        } elseif ($unit == 1) {
            $unit_set = 'miles';
        }
        /* $currency_selected = Keywords::find(5); */
        foreach ($types as $type) {
            $data = array();
            $data['id']                     = $type->id;
            $data['name']                   = $type->name;
            $data['min_fare']               = currency_converted($type->base_price);
            $data['max_size']               = $type->max_size;
            $data['icon']                   = $type->icon;
            $data['icon_maps']              = $type->icon_maps;
            $data['is_default']             = $type->is_default;
            $data['destination_visible']    = $type->destination_visible;
            $data['color']                  = $type->color;
            $data['price_per_unit_time']    = currency_converted($type->price_per_unit_time);
            $data['price_per_unit_distance']= currency_converted($type->price_per_unit_distance);
            $data['base_price']             = currency_converted($type->base_price);
            $data['base_distance']          = currency_converted($type->base_distance);
            $data['maximum_distance']       = currency_converted($type->maximum_distance);
            $data['charge_provider_return'] = $type->charge_provider_return;
            $data['sub_category_screen_visible']    =   $type->sub_category_screen_visible;
            $data['currency']               = Config::get('app.generic_keywords.Currency');
            $data['unit']                   = $unit_set;

            $queryServices = ProviderServices::where('provider_id', 0)
                    ->where('type', $type->id)
                    ->where('is_visible', '=', 1)
                    ->get();
            
            $service_array = array();
            foreach ($queryServices as $queryService) {
                $services = array();
                $services['id'] = $queryService->category;
                $services['type_id'] = $queryService->type;
                $services['category_id'] = $queryService->category;
                $services['name'] = $queryService->getTypeCategory->name;
                $services['price_per_unit_distance'] = $queryService->price_per_unit_distance;
                $services['price_per_unit_time'] = $queryService->price_per_unit_time;
                $services['base_price'] = $queryService->base_price;
                $services['base_distance'] = $queryService->base_distance;
                $services['base_time'] = $queryService->base_time;
                $services['distance_unit'] = $queryService->distance_unit;
                $services['time_unit'] = $queryService->time_unit;
                $services['base_price_provider'] = $queryService->base_price_provider;
                $services['base_price_user'] = $queryService->base_price_user;
                $services['commission_rate'] = $queryService->commission_rate;
                array_push($service_array, $services);
            }

            $data['categories'] = $service_array;
            array_push($type_array, $data);
        }
        $response_array = array();
        $response_array['success'] = true;
        $response_array['max_cancel_time'] = ($maxCancelTime ? $maxCancelTime->value : 5);
        $response_array['types'] = $type_array;
        $response_code = 200;
        $response = Response::json($response_array, $response_code);
        return $response;
    }

    public function forgot_password() {
        $type = Input::get('type');
        $email = Input::get('email');
        if ($type == 1) {
            // Provider
            $provider_data = Provider::where('email', $email)->first();
            if ($provider_data) {
                $provider = Provider::find($provider_data->id);
                $new_password = time();
                $new_password .= rand();
                $new_password = sha1($new_password);
                $new_password = substr($new_password, 0, 8);
                $provider->password = Hash::make($new_password);
                $provider->save();

                $settings = Settings::where('key', 'admin_email_address')->first();
                $admin_email = $settings->value;
                $login_url = web_url() . "/provider/signin";
                $pattern = array('name' => $provider->first_name . " " . $provider->last_name, 'admin_eamil' => $admin_email, 'new_password' => $new_password, 'login_url' => $login_url);
                $subject = trans('user_provider_controller.new_password');
                email_notification($provider->id, 'provider', $pattern, $subject, 'reset_password', "imp");

                $response_array = array();
                $response_array['success'] = true;
                $response_code = 200;
                $response = Response::json($response_array, $response_code);
                return $response;
            } else {
                // $response_array = array('success' => false, 'error' => 'This Email is not Registered', 'error_messages' => array('This Email is not Registered'), 'error_code' => 425);
                $response_array = array('success' => false, 'error' => trans('user_provider_controller.mail_not_registered'), 'error_messages' => array(trans('user_provider_controller.mail_not_registered')), 'error_code' => 425);
                $response_code = 200;
                $response = Response::json($response_array, $response_code);
                return $response;
            }
        } else {
            $user_data = User::where('email', $email)->first();
            if ($user_data) {

                $user = User::find($user_data->id);
                $new_password = time();
                $new_password .= rand();
                $new_password = sha1($new_password);
                $new_password = substr($new_password, 0, 8);
                $user->password = Hash::make($new_password);
                $user->save();

                $settings = Settings::where('key', 'admin_email_address')->first();
                $admin_email = $settings->value;
                $login_url = web_url() . "/user/signin";
                $pattern = array('name' => $user->first_name . " " . $user->last_name, 'admin_eamil' => $admin_email, 'new_password' => $new_password, 'login_url' => $login_url);
                $subject = trans('user_provider_controller.new_password');
                email_notification($user->id, 'user', $pattern, $subject, 'reset_password', "imp");


                $response_array = array();
                $response_array['success'] = true;
                $response_code = 200;
                $response = Response::json($response_array, $response_code);
                return $response;
            } else {
                //$response_array = array('success' => false, 'error' => 'This Email is not Registered', 'error_messages' => array('This Email is not Registered'), 'error_code' => 425);
                $response_array = array('success' => false, 'error' => trans('user_provider_controller.mail_not_registered'), 'error_messages' => array(trans('user_provider_controller.mail_not_registered')), 'error_code' => 425);
                $response_code = 200;
                $response = Response::json($response_array, $response_code);
                return $response;
            }
        }
    }

    public function token_braintree() {
        $this->_braintreeConfigure();
        $clientToken = Braintree_ClientToken::generate();
        $response_array = array('success' => true, 'clientToken' => $clientToken);
        $response_code = 200;
        return Response::json($response_array, $response_code);
    }

    //retorna configuracoes de pagamento
    public function get_payment_settings(){

        $vSettings = Settings::where('key', 'payment_voucher')->first();
        $mSettings = Settings::where('key', 'payment_money')->first();
        $cSettings = Settings::where('key', 'payment_card')->first();

        //$arrayPayments = Settings::getPaymentsArray();

        $response_array = array('success' => true, 
            'payment_platform' => Config::get('app.default_payment'), 
            'payment_voucher' => $vSettings->value,
            'payment_card' => $cSettings->value, 
            'payment_money' => $mSettings->value);

        $response_code = 200;
        return Response::json($response_array, $response_code);
    }

    //retorna configuracoes de pagamento
    public function get_settings(){

        $response_array = array('success' => true, 
            'has_categories' => 1,
            'distance_count_on_provider_start' => Settings::getDistanceCountOnProviderStart(),
            'visible_value_to_provider' => Settings::getVisibleValueToProvider(),
            'marker_maximum_arrival_time_visible' => Settings::getMarkerMaximumArrivalTimeVisible(),
            'show_user_register' => Settings::getShowUserRegister()
        );

        $response_code = 200;
        return Response::json($response_array, $response_code);
    }

}
