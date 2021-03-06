<title></title>
<!--main div--><?php
        $settings = Settings::where('key', 'default_distance_unit')->first();
        $unit = $settings->value;
        if ($unit == 0) {
            $unit_set = trans('email.kms');
        } elseif ($unit == 1) {
            $unit_set = trans('email.miles');
        } 
        ?>
<div style="margin: 0px auto;width: 90%; padding: 15px;background-color: #ffffff;color: #545454;"><!-- logo -->
<div>
<div style="float: left;"><img alt="logo" src="{{ asset_url() }}/image/email_logo.png" style="width: 200px;padding: 0px 0px 12px 0px;" /><img alt="logo" src="{{ asset_url() }}/image/e_receipt.png" style="width: 150px;padding: 0px 0px 12px 50px;" /></div>
<!-- data -->

<div style="float: right;padding:15px 0px 0px 0px;">{{date('d M Y')}}</div>
<!-- clear floats-->

<div style="clear: both;"> </div>
</div>

<div style="background-color: #FFFFFF;">
<div style="background-color: #f3f3f3;border-bottom: 2px solid #c3c3c3;color: #000000;">
<p style="float: left;margin: 0px;font-size: 2em; font-weight: bold; padding: 10px 0.6em;"><?php echo Config::get('app.generic_keywords.Currency'); ?>{{ $vars['request']->total }}</p>

<p style="color: #999999;float: right;margin: 0px;font-size: 16px; font-weight: normal; padding: 18px 0.6em 0px 0px;">{{trans('email.Thanks_for_choosing')}} <?= Config::get('app.website_title') ?>,{{ $vars['provider']->first_name }} {{ $vars['provider']->last_name }}</p>

<div style="clear: both;"> </div>
</div>

<div style="padding: 1.5em;color: #000000;">
<div style="background-color: #f3f3f3;border: 2px solid #c3c3c3;border-radius: 5px;width: 45%;float: left;"><img alt="map location" src="<?php echo $vars['map_url']; ?>" style="border-bottom: 2px solid #c3c3c3;width: 100%;margin-bottom: 3%;" />
<div style="padding: 0px; padding: 0px 0px 0px 20px;z-index: 1000;"><img alt="point icon green" src="http://d1a3f4spazzrp4.cloudfront.net/receipt-new/marker-start@2x.png" style="width: 18px; padding: 0px; margin: 10px 0px 0px 0px;" /> <span style="font-size: 20px;font-weight: bold;position: absolute; margin: 5px 0px 0px 5px;">{{ date('h:i A',strtotime($vars['start']->created_at)) }}</span>

<p style="padding: 0px 0px 0px 25px; margin: 0px; font-size: 14px;color: #999999;">{{ $vars['start_address'] }}</p>
</div>

<div style="padding: 0px; padding: 0px 0px 0px 20px;z-index: 1000;border-bottom: 2px solid #c3c3c3;padding-bottom: 15px;"><img alt="point icon green" src="http://d1a3f4spazzrp4.cloudfront.net/receipt-new/marker-finish@2x.png" style="width: 18px; padding: 0px; margin: 10px 0px 0px 0px;" /> <span style="font-size: 20px;font-weight: bold;position: absolute; margin: 5px 0px 0px 5px;margin: 6px 0px 0px 5px;">{{ date('h:i A',strtotime($vars['end']->created_at)) }}</span>

<p style="padding: 0px 0px 0px 25px; margin: 0px; font-size: 14px;color: #999999;">{{ $vars['end_address'] }}</p>
</div>

<div style="margin: 15px 0px;padding: 0px;text-align: center;font-size: 14px;">
<div style="float: left;width: 33.32%;">
<p style="color: #999999;margin: 0px;padding: 0px;text-transform: uppercase;font-size: 10px;">{{trans('email.car')}}</p>

<p style="margin: 0px;padding: 0px;">{{ $vars['type_name'] }}</p>
</div>

<div style="float: left;width: 33.32%;">
<p style="color: #999999;margin: 0px;padding: 0px;text-transform: uppercase;font-size: 10px;"><?php echo $unit_set; ?></p>

<p style="margin: 0px;padding: 0px;">{{ $vars['request']->distance }}</p>
</div>

<div style="float: left;width: 33.32%;">
<p style="color: #999999;margin: 0px;padding: 0px;text-transform: uppercase;font-size: 10px;">{{trans('email.trip_time')}}</p>

<p style="margin: 0px;padding: 0px;">{{ $vars['request']->time }}</p>
</div>

<div style="clear: both;"> </div>
</div>
</div>

<div style="width: 50%;float: right;">
<div>
<div style="width: 30%;float: left;border-bottom: 1px solid #c3c3c3;padding-top: 8px;"> </div>

<div style="width: 40%;float: left;text-transform: uppercase;text-align: center;font-size: 12px;">{{trans('email.fare_breakdown')}}</div>

<div style="width: 30%;float: left;border-bottom: 1px solid #c3c3c3;padding-top: 8px;"> </div>

<h1 style="clear: both;"> </h1>
</div>

<div style="color: #999999;font-size: 14px;">
<div>
<p style="margin: 0px;padding: 0px;float: left;">{{trans('email.Base_Fare')}}</p>

<p style="margin: 0px;padding: 0px;float: right;color: #000000;">{{ $vars['base_price']}}</p>

<h1 style="clear: both;"> </h1>
</div>

<div>
<p style="margin: 0px;padding: 0px;float: left;">{{trans('email.Distance_Price')}}</p>

<p style="margin: 0px;padding: 0px;float: right;color: #000000;">{{ $vars['dist_cost'] }}</p>

<h1 style="clear: both;"> </h1>
</div>

<div>
<p style="margin: 0px;padding: 0px;float: left;">{{trans('email.Time_Price')}}</p>

<p style="margin: 0px;padding: 0px;float: right;color: #000000;">{{ $vars['time_cost'] }}</p>

<h1 style="clear: both;"> </h1>
</div>

<div>
<p style="margin: 0px;padding: 0px;float: left;">{{trans('email.Referral_Bonus')}}</p>

<p style="margin: 0px;padding: 0px;float: right;color: #000000;">{{ $vars['ref_bonus'] }}</p>

<h1 style="clear: both;"> </h1>
</div>

<div>
<p style="margin: 0px;padding: 0px;float: left;">{{trans('email.Promotional_Bonus')}}</p>

<p style="margin: 0px;padding: 0px;float: right;color: #000000;">{{ $vars['promo_bonus'] }}</p>

<h1 style="clear: both;"> </h1>
</div>

<div>
<p style="border-bottom: 1px solid #c3c3c3;"> </p>
</div>

<div style="color: #000000;font-weight: bold;">
<p style="margin: 0px;padding: 0px;float: left;">{{trans('email.Subtotal')}}</p>

<p style="margin: 0px;padding: 0px;float: right;"><?php echo Config::get('app.generic_keywords.Currency'); ?>{{ $vars['request']->card_payment }}</p>

<h1 style="clear: both;"> </h1>
</div>

<div>
<p style="margin: 0px;padding: 0px;width: 65%;float: left;text-align: right;"> </p>

<p style="margin: 0px;padding: 0px;float: right;color: #000000;font-weight: bold;"> </p>

<h1 style="clear: both;"> </h1>
</div>

<div>
<p style="border-bottom: 1px solid #c3c3c3;"> </p>
</div>

<div>
<p style="margin: 0px;padding: 0px;float: left;text-transform: uppercase;font-size: 8px;">{{trans('email.charged')}}</p>

<p style="margin: 0px;padding: 0px;float: right;color: #000000;font-weight: bold;font-size: 16px;"><?php echo Config::get('app.generic_keywords.Currency'); ?>{{ $vars['request']->card_payment }}</p>

<p style="margin: 0px;padding-top: 12px;float: none;text-transform: uppercase;font-size: 8px;"><img alt="visa imag" src="https://lh5.googleusercontent.com/-aHdei-YVTWQ/VQEwBhTTxQI/AAAAAAAABtQ/NSWCRSGRGUw/w50-h31-no/small-visa.png" style="width: 20px;" /> <span style="font-size: 14px;text-transform: none;position: absolute;margin: -2px 0px 0px 5px;;">{{trans('email.Personal')}}...</span></p>

<h1 style="clear: both;"> </h1>
</div>

<div>
<p style="border-bottom: 1px solid #c3c3c3;"> </p>
</div>
</div>
</div>

<h1 style="clear: both;"> </h1>
</div>

<div style="padding-top: 3px;">
<p style="border-bottom: 1px solid #c3c3c3;"> </p>
</div>

<div style="margin: 0px 0px 0px 1.5em;color: #000000;">
<div style="margin: 15px 0px;width: 50px;border-radius: 150px; width: 90%;"><img alt="user image" height="50" src="{{ $vars['provider']->picture }}" style="width: 50px; height: 50px;border-radius: 150px;border: 2px solid #999999;float: left;" width="50" />
<p style="float: left;margin: 5px 0 0 5px;padding: 0px;">{{trans('email.You_rode_with')}} {{ $vars['provider']->first_name }} {{ $vars['provider']->last_name }}<br />
{{trans('email.Transportation_network_company')}}: <?php /* echo "App Name"; */echo ucwords(Config::get('app.website_title')); ?>.</p>

<h1 style="clear: both;"> </h1>
</div>
</div>

<div style="padding: 15px 0px 0px 0px;color: #545454;background-color: #ffffff">
<div style="margin: 15px 25px 15px 0;width: 50px;width: 48%;float: left; border-right: 1px solid rgba(255,255,255,.2);"><img alt="user image" height="45" src="https://lh6.googleusercontent.com/3Hn74JQ-hjKXIFGivc6jL5hKf7WVydnBHYNPhaYdJJI=s512-no" style="float: left;" width="45" />
<p style="float: right;margin: 5px 0 0 5px;padding: 0px;width: 81%;"><span style="color: #000000;font-size: 12px;font-weight: bold;"><?= Config::get('app.website_title') ?> {{trans('email.Support')}}</span><br />
<span style="font-size: 12px;"><a href="{{ web_url() }}" style="color: #000000;">{{trans('email.Contact_us')}}</a> {{trans('email.with_questions')}}.<br />
{{trans('email.Leave_something_behind')}}? <a href="{{ web_url() }}" style="color: #FFFFFF;">{{trans('email.Track_it_down')}}.</a></span></p>

<h1 style="clear: both;"> </h1>
</div>

<div style="margin: 15px 0px;width: 50px;border-radius: 150px; width: 47%;float: left;"><img alt="user image" height="45" src="https://lh3.googleusercontent.com/-Kz8To9O9BD4/VQK6ZB9YEmI/AAAAAAAABuA/SsmgdIT9UHs/s512-no/gift-icon-0926005203.png" style="float: left;" width="45" />
<p style="float: right;margin: 5px 0 0 5px;padding: 0px;width: 81%; font-size: 12px;font-weight: bold;">{{trans('email.Give')}} <?php echo Config::get('app.generic_keywords.Currency'); ?>30, {{trans('email.Get')}} <?php echo Config::get('app.generic_keywords.Currency'); ?>30</p>

<p style="float: right;margin: 5px 0 0 5px;padding: 0px;width: 81%;font-size: 12px; position: relative;margin-top: 5px;">{{trans('email.Share_code')}}: a134z</p>

<div style="padding: 2px; float:right;color:#FFFFFF;position: relative;margin-top: -15px;"><a href="#" style="border: 0px; text-decoration: none;"><img height="25px" src="https://lh6.googleusercontent.com/-N24iGXCS1B0/VQK6ZxCvrfI/AAAAAAAABuU/HoVX671-mOs/s100-no/social-facebook.png" /> </a> <a href="#" style="border: 0px; text-decoration: none;"> <img height="25px" src="https://lh4.googleusercontent.com/-y8piQto1dWw/VQK6aItATiI/AAAAAAAABuY/4pVa-sGn3qk/s100-no/social-twitter.png" /> </a> <a href="#" style="border: 0px; text-decoration: none;"> <img height="25px" src="https://lh4.googleusercontent.com/-C1g1EvHxUzs/VQK6ZKokMLI/AAAAAAAABuQ/aqfjBHrW6pU/s100-no/ios7-email.png" /> </a></div>

<h1 style="clear: both;"> </h1>
</div>

<h1 style="clear: both;"> </h1>

<p style="text-align: center; font-size: 12px;margin-top: 30px;">{{trans('email.any_question')}} {{ $vars['admin_eamil'] }}</p>
</div>
</div>
</div>
