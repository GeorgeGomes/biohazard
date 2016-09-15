@extends('layout')

@section('content')

<p align="right"><a href="{{ URL::Route('AdminSettings') }}"><button class="btn btn-info btn-flat">{{trans('setting.back')}}</button></a></p>




<div class="box box-primary">

	<div class="box-header">
		<h3 class="box-title">{{trans('setting.sms_configuration')}}</h3>
	</div><!-- /.box-header 
	<!-- form start -->
	<form role="form" method="POST" action="{{ URL::Route('AdminInstallFinish') }}">

		<div class="box-body">
			<div class="form-group">
				<label>{{trans('setting.sid_twilio_account')}}</label>

				<input type="text"  name="twillo_account_sid" class="form-control" placeholder="{{trans('setting.sid_twilio_account')}}" value="{{$install['twillo_account_sid']?$install['twillo_account_sid']:''}}">

			</div>
			<div class="form-group">
				<label>{{trans('setting.token_twilio_auth')}}</label>
				<input type="text" name="twillo_auth_token" class="form-control" placeholder="{{trans('setting.token_twilio_auth')}}" value="{{$install['twillo_auth_token']?$install['twillo_auth_token']:''}}">

			</div>
			<div class="form-group">
				<label>{{trans('setting.twilio_number')}}</label>

				<input type="text" name="twillo_number" class="form-control" placeholder="{{trans('setting.twilio_number')}}" value="{{$install['twillo_number']?$install['twillo_number']:''}}">

			</div>




		</div><!-- /.box-body -->

		<div class="box-footer">


			<button type="submit" name="sms" class="btn btn-primary btn-flat btn-block">{{trans('keywords.save_change');}}</button>
		</div>
	</form>
</div>


<div class="box box-primary">

	<div class="box-header">
		<h3 class="box-title">{{trans('setting.url_configuration')}}</h3>
	</div><!-- /.box-header 
	<!-- form start -->
	<form role="form" method="POST" action="{{ URL::Route('UpdateSetSiteDirectory') }}">

		<div class="box-body">
			<div class="form-group">
				<label>{{trans('setting.public_directory')}}</label>

				<input type="text"  name="website_directory" class="form-control" placeholder="{{trans('setting.public_directory')}}" value="{{ Settings::getWebsiteDirectory() }}">

			</div>

			<div class="form-group">
				<label>{{trans('setting.public_url_website')}}</label>

				<input type="text"  name="website_url" class="form-control" placeholder="{{trans('setting.public_url_website')}}" value="{{ Settings::getWebsiteUrl() }}">

			</div>

			<div class="form-group">
				<label>{{trans('setting.public_directory_provider')}}</label>

				<input type="text"  name="provider_directory" class="form-control" placeholder="{{trans('setting.public_directory_provider')}}" value="{{ Settings::getProviderDirectory() }}">

			</div>

			<div class="form-group">
				<label>{{trans('setting.public_url_website_provider')}}</label>

				<input type="text"  name="provider_url" class="form-control" placeholder="{{trans('setting.public_url_website_provider')}}" value="{{ Settings::getProviderUrl() }}">

			</div>


		</div><!-- /.box-body -->

		<div class="box-footer">


			<button type="submit" name="sms" class="btn btn-primary btn-flat btn-block">{{trans('keywords.save_change');}}</button>
		</div>
	</form>
</div>






<div class="box box-primary">
	<div class="box-header">
		<h3 class="box-title">{{trans('setting.mail_settings')}}</h3>
	</div><!-- /.box-header -->
	<!-- form start -->
	<form role="form" method="POST" action="{{ URL::Route('AdminInstallFinish') }}">

		<div class="box-body">
			<div class="form-group">
				<label>{{trans('setting.mail_provider_settings')}}</label>
				<select name="mail_driver" id="mail_driver" class="form-control">
					<option value=''>---{{trans('setting.choose_one_mail')}}---</option>
					<option value="mail" <?php
					if ($install['mail_driver'] == 'mail') {
						echo 'selected';
					} else {
						echo'';
					}
					?>>{{trans('setting.mail')}}</option>
					<option value="mandrill"  <?php
					if ($install['mail_driver'] == 'mandrill') {
						echo 'selected';
					} else {
						echo'';
					}
					?>>{{trans('setting.mandrill')}}</option>

					<option value="sendgrid"  <?php
					if ($install['mail_driver'] == 'sendgrid') {
						echo 'selected';
					} else {
						echo'';
					}
					?>>{{trans('setting.sendgrid')}}</option>

				</select>

			</div>
			<div class="form-group">
				<label>{{trans('setting.mail_address')}}</label> <span id="no_email_error1" style="display: none"> </span>
				<input type="text" class="form-control"  name="email_address" placeholder="{{trans('setting.mail_address')}}" value="{{$install['email_address']?$install['email_address']:''}}"  onblur="ValidateEmail(1)" id="email_check1" required="" >


			</div>
			<div class="form-group">
				<label>{{trans('setting.show_name')}}</label>
				<input type="text" class="form-control"  name="email_name" placeholder="{{trans('setting.show_name')}}" value="{{$install['email_name']?$install['email_name']:''}}">


			</div>

			<div class="form-group" id="mandrill1" style="display:<?php
			if ($install['mail_driver'] == 'mandrill')
				echo 'block';
			else
				echo 'none';
			?>">
				<label>{{trans('setting.mandrill_secret')}}</label>
				<input type="text" class="form-control"  name="mandrill_secret" placeholder="{{trans('setting.mandrill_secret')}}" value="{{Config::get('services.mandrill.secret')?Config::get('services.mandrill.secret'):''}}">

			</div>
			<div class="form-group" id="mandrill2" style="display:<?php
			if ($install['mail_driver'] == 'mandrill')
				echo 'block';
			else
				echo 'none';
			?>">
				<label>{{trans('setting.mandrill_host_name')}}</label>
				<input type="text" class="form-control"  name="host_name" placeholder="{{trans('setting.mandrill_host_name')}}" value="{{$install['host']?$install['host']:''}}">

			</div>
			<div class="form-group" id="mandrill3" style="display:<?php
			if ($install['mail_driver'] == 'mandrill')
				echo 'block';
			else
				echo 'none';
			?>">
				<label>{{trans('setting.mandrill_user_name')}}</label>
				<input type="text" class="form-control"  name="user_name" placeholder="{{trans('setting.mandrill_user_name')}}" value="{{$install['mandrill_username']?$install['mandrill_username']:''}}">

			</div>

			<div class="form-group" id="sendgrid1" style="display:<?php
			if ($install['mail_driver'] == 'sendgrid')
				echo 'block';
			else
				echo 'none';
			?>">
				<label>{{trans('setting.sendgrid_secret')}}</label>
				<input type="text" class="form-control"  name="sendgrid_secret" placeholder="{{trans('setting.sendgrid_secret')}}" value="{{Config::get('services.sendgrid.secret')?Config::get('services.sendgrid.secret'):''}}">

			</div>
			<div class="form-group" id="sendgrid2" style="display:<?php
			if ($install['mail_driver'] == 'sendgrid')
				echo 'block';
			else
				echo 'none';
			?>">
				<label>{{trans('setting.sendgrid_host_name')}}</label>
				<input type="text" class="form-control"  name="host_name" placeholder="{{trans('setting.sendgrid_host_name')}}" value="{{$install['host']?$install['host']:''}}">

			</div>
			<div class="form-group" id="sendgrid3" style="display:<?php
			if ($install['mail_driver'] == 'sendgrid')
				echo 'block';
			else
				echo 'none';
			?>">
				<label>{{trans('setting.sendgrid_user_name')}}</label>
				<input type="text" class="form-control"  name="user_name2" placeholder="{{trans('setting.sendgrid_user_name')}}" value="{{$install['sendgrid_username']?$install['sendgrid_username']:''}}">

			</div>

		</div><!-- /.box-body -->

		<div class="box-footer">

			<button type="submit" name="mail" class="btn btn-primary btn-flat btn-block">{{trans('keywords.save_change');}}</button>
		</div>
	</form>
</div>



<div class="box box-primary">
	<div class="box-header">
		<h3 class="box-title">{{trans('setting.pay_config')}}</h3>
	</div><!-- /.box-header -->
	<!-- form start -->
	<form role="form" method="POST" action="{{ URL::Route('AdminInstallFinish') }}">

		<div class="box-body">

			<div class="form-group">
				<label>{{trans('setting.default_business_model')}}</label>
				<select name="default_business_model" id="default_business_model" class="form-control">

					<option value="percentage" {{$install['default_business_model'] == 'percentage'? 'selected' : ''}}>
						{{trans('setting.percentage')}}
					</option>

					<option value="monthly" {{$install['default_business_model'] == 'monthly'? 'selected' : ''}}>     
						{{trans('setting.monthly')}}
					</option>

					<option value="pifou" {{$install['default_business_model'] == 'pifou'? 'selected' : ''}}>     
						{{trans('setting.pifou')}}
					</option>

				</select>

			</div>

			<div class="form-group">

				<label>{{trans('setting.provider_transfer_interval')}}</label> <br/>
				
				<select name="provider_transfer_interval" id="provider_transfer_interval" class="form-control">

					<option value="daily" {{$install['provider_transfer_interval'] == 'daily'? 'selected' : ''}}>
						{{trans('setting.daily')}}
					</option>

					<option value="weekly" {{$install['provider_transfer_interval'] == 'weekly'? 'selected' : ''}}>
						{{trans('setting.weekly')}}
					</option>

					<option value="monthly" {{$install['provider_transfer_interval'] == 'monthly'? 'selected' : ''}}>
						{{trans('setting.monthly_2')}}
					</option>

				</select>
				
			</div>

			<div id="provider_transfer_day_div" class="form-group" >
				<label>{{trans('setting.provider_transfer_day')}}</label>


				<select name="provider_transfer_day_weekly" id="provider_transfer_day_weekly" class="form-control">

					<option value="1"  {{ $install['provider_transfer_day'] == "1"? "selected" : "" }}>
						{{trans('setting.monday')}}
					</option>
					<option value="2"  {{ $install['provider_transfer_day'] == "2"? "selected" : "" }}>
						{{trans('setting.tuesday')}}
					</option>
					<option value="3"  {{ $install['provider_transfer_day'] == "3"? "selected" : "" }}>
						{{trans('setting.wednesday')}}
					</option>
					<option value="4"  {{ $install['provider_transfer_day'] == "4"? "selected" : "" }}>
						{{trans('setting.thursday')}}
					</option>
					<option value="5"  {{ $install['provider_transfer_day'] == "5"? "selected" : "" }}>
						{{trans('setting.friday')}}
					</option>

				</select>

				<input type="text" class="form-control" id="provider_transfer_day_monthly" name="provider_transfer_day_monthly"  value="{{$install['provider_transfer_day']? $install['provider_transfer_day']:''}}">
			</div>

			<div class="form-group">

				<label>{{trans('setting.payment_methods')}}</label> <br/>
				
				<input name="payment_money" type="checkbox" {{$install['payment_money'] == '1'? 'checked' : ''}}>
				{{trans('setting.money');}}
				
				<input name="payment_card" type="checkbox" {{$install['payment_card'] == '1'? 'checked' : ''}}>
				{{trans('setting.card');}}
				
				<input  name="payment_voucher" type="checkbox"{{$install['payment_voucher'] == '1'? 'checked' : ''}} >
				{{trans('setting.voucher');}}
			</div>


			<div class="form-group">
				<label>{{trans('setting.default_pay_gate')}}</label>
				<select name="default_payment" id="default_payment" class="form-control">

					<option value="pagarme"  <?=Config::get('app.default_payment') == 'pagarme'? "selected" : "" ?>>
						{{trans('setting.pagarme')}}
					</option>

					<option value="stripe" <?=Config::get('app.default_payment') == 'stripe'? "selected" : "" ?>>     
						{{trans('setting.stripe')}}
					</option>

					<option value="braintree"  <?=Config::get('app.default_payment') == 'braintree'? "selected" : "" ?> >     {{trans('setting.braintree')}}
					</option>
				</select>

			</div>


			<div class="form-group pagarme">
				<label>{{trans('setting.pagarme_api_key')}}</label>
				<input type="text" class="form-control"  name="pagarme_api_key" placeholder="{{trans('setting.pagarme_api_key')}}" value="{{$install['pagarme_api_key']?$install['pagarme_api_key']:''}}">

			</div>

			<div class="form-group pagarme">
				<label>{{trans('setting.pagarme_encryption_key')}}</label>
				<input type="text" class="form-control"  name="pagarme_encryption_key" placeholder="{{trans('setting.pagarme_encryption_key')}}" value="{{$install['pagarme_encryption_key']?$install['pagarme_encryption_key']:''}}">

			</div>

			<div class="form-group stripe">
				<label>{{trans('setting.stripe_secret')}}</label>
				<input type="text" class="form-control"  name="stripe_secret_key" placeholder="{{trans('setting.stripe_secret')}}" value="{{$install['stripe_secret_key']?$install['stripe_secret_key']:''}}">

			</div>

			<div class="form-group stripe">
				<label>{{trans('setting.stripe_public')}}</label>
				<input type="text" class="form-control"  name="stripe_publishable_key" placeholder="{{trans('setting.stripe_public')}}" value="{{$install['stripe_publishable_key']?$install['stripe_publishable_key']:''}}">

			</div>


			<div class="form-group braintree" style="display:none" >
				<label>{{trans('setting.braintree_environment')}}</label>
				<input type="text" class="form-control"  name="braintree_environment" placeholder="{{trans('setting.braintree_environment')}}" value="{{$install['braintree_environment']?$install['braintree_environment']:''}}">

			</div>

			<div class="form-group braintree" style="display:none" >
				<label>{{trans('setting.braintree_id')}}</label>
				<input type="text" class="form-control"  name="braintree_merchant_id" placeholder="{{trans('setting.braintree_id')}}" value="{{$install['braintree_merchant_id']?$install['braintree_merchant_id']:''}}">

			</div>

			<div class="form-group braintree" style="display:none" >
				<label>{{trans('setting.braintree_public')}}</label>
				<input type="text" class="form-control"  name="braintree_public_key" placeholder="{{trans('setting.braintree_public')}}" value="{{$install['braintree_public_key']?$install['braintree_public_key']:''}}">

			</div>

			<div class="form-group braintree" style="display:none" >
				<label>{{trans('setting.braintree_secret')}}</label>
				<input type="text" class="form-control"  name="braintree_private_key" placeholder="{{trans('setting.braintree_secret')}}" value="{{$install['braintree_private_key']?$install['braintree_private_key']:''}}">

			</div>

			<div class="form-group braintree" style="display:none" >
				<label>{{trans('setting.braintree_cript')}}</label>
				<input type="text" class="form-control"  name="braintree_cse" placeholder="{{trans('setting.braintree_cript')}}" value="{{$install['braintree_cse']?$install['braintree_cse']:''}}">

			</div>

		</div><!-- /.box-body -->

		<div class="box-footer">
			<button type="submit" name="payment" class="btn btn-primary btn-flat btn-block">{{trans('keywords.save_change');}}</button>
		</div>
	</form>
</div>

<div class="box box-primary">

	<div class="box-header">
		<h3 class="box-title">{{trans('setting.Certificates')}}</h3>
	</div>
	<form role="form" method="POST" action="{{ URL::Route('AdminAddCerti') }}" enctype="multipart/form-data">
		<div class="box-body">
			<h3>{{trans('setting.ios')}}</h3>
			<div class="form-group">
				<label>{{trans('setting.Certificates_type')}}</label>
				<select class="form-control" name="cert_type_a">
					<?php
					if ($install['customer_certy_type']) {
						?>
						<option value="0">{{trans('setting.Sandbox')}}</option>
						<option value="1" selected="">{{trans('setting.production')}}</option>
					<?php } else { ?>
						<option value="0" selected="">{{trans('setting.Sandbox')}}</option>
						<option value="1">{{trans('setting.production')}}</option>
					<?php } ?>
				</select>
			</div>
			<div class="form-group col-sm-4">
				<label>{{trans('setting.user')}}</label>
				<input type="file" class="form-control" name="user_certi_a" value="<?= $install['customer_certy_url'] ?>">
			</div>
			<div class="form-group col-sm-4">
				<label>{{trans('setting.user_passphrase')}}</label>
				<input type="text" class="form-control" name="user_pass_a" value="<?= $install['customer_certy_pass'] ?>">
			</div>
			<div class="form-group col-sm-4">
				<label>&nbsp;</label>
				<a href="<?= $install['customer_certy_url'] ?>" target="_blank"><span class="btn btn-default form-control">{{trans('setting.see_down')}}</span></a>
			</div>
			<div class="form-group col-sm-4">
				<label>{{trans('customize.Provider')}}</label>
				<input class="form-control" type="file" name="prov_certi_a" value="<?= $install['provider_certy_url'] ?>">
			</div>

			<div class="form-group col-sm-4">
				<label>{{trans('setting.provider_passphrase')}}</label>
				<input class="form-control" type="text" name="prov_pass_a" value="<?= $install['provider_certy_pass'] ?>">
			</div>
			<div class="form-group col-sm-4">
				<label>&nbsp;</label>
				<a href="<?= $install['provider_certy_url'] ?>" target="_blank"><span class="btn btn-default form-control">{{trans('setting.see_down')}}</span></a>
			</div>
			<div class="form-group col-sm-6">
				<label>{{trans('setting.ios')}} <?= Config::get('app.generic_keywords.User') ?> {{trans('setting.link')}}</label>
				<input class="form-control" type="text" name="ios_client_app_url" value="<?= Config::get('app.ios_client_app_url') ?>">
			</div>
			<div class="form-group col-sm-6">
				<label>{{trans('setting.ios')}} <?= Config::get('app.generic_keywords.Provider') ?> {{trans('setting.link')}}</label>
				<input class="form-control" type="text" name="ios_provider_app_url" value="<?= Config::get('app.ios_provider_app_url') ?>">
			</div>
			<!--<div class="form-group">
				<label>Choose Default</label>
				<select class="form-control" name="cert_default">
					<option value="0" <?php
			if ($cert_def != 1) {
				echo "selected";
			}
			?>>{{trans('setting.Sandbox')}}</option>
					<option value="1" <?php
			if ($cert_def == 1) {
				echo "selected";
			}
			?>>Production</option>
				</select>
			</div>-->
		</div>
		<hr>
		<div class="box-body">
			<h3>{{trans('setting.gcm')}}</h3>
			<div class="form-group">
				<label>{{trans('setting.gcm_key')}}</label>
				<input type="text" class="form-control" name="gcm_key" placeholder="{{trans('setting.gcm_key')}}" value="<?= $install['gcm_browser_key'] ?>">
			</div>
			<div class="form-group col-sm-6">
				<label>{{trans('setting.android')}} <?= Config::get('app.generic_keywords.User') ?> {{trans('setting.link')}}</label>
				<input class="form-control" type="text" name="android_client_app_url" value="<?= Config::get('app.android_client_app_url') ?>">
			</div>
			<div class="form-group col-sm-6">
				<label>{{trans('setting.android')}} <?= Config::get('app.generic_keywords.Provider') ?> {{trans('setting.link')}}</label>
				<input class="form-control" type="text" name="android_provider_app_url" value="<?= Config::get('app.android_provider_app_url') ?>">
			</div>
			<!-- <div class="box-footer">
				<button type="submit" class="btn btn-primary btn-flat btn-block">{{trans('keywords.save_change');}}</button>
			</div> -->
		</div>

		<?php
			$maps_key = Settings::where('key', 'google_maps_api_key')->first();
		?>

		<div class="box-body">
			<h3>{{trans('setting.maps_api_key')}}</h3>
			<div class="form-group">
				<label>{{trans('setting.navigation_key')}}</label>
				<input type="text" class="form-control" name="maps_key" placeholder="{{trans('setting.navigation_key')}}" value="{{$maps_key->value}}">
			</div>
			<div class="box-footer">
				<button type="submit" class="btn btn-primary btn-flat btn-block">{{trans('keywords.save_change');}}</button>
			</div>
		</div>
	</form>
</div>
<script type="text/javascript">
	$(document).ready(function () {
		var val = $("#provider_transfer_interval").val();
		console.log(val);
		if(val == 'daily'){
			$("#provider_transfer_day_div").hide();
		}
		else if (val == 'weekly') {
			$("#provider_transfer_day_div").show();
			$("#provider_transfer_day_weekly").show();            
			$("#provider_transfer_day_monthly").hide();
		}
		else {
			$("#provider_transfer_day_div").show();
			$("#provider_transfer_day_weekly").hide();            
			$("#provider_transfer_day_monthly").show();
		}

		var payment = '<?php echo Config::get('app.default_payment'); ?>';
		if(payment == 'pagarme'){
			$(".braintree").hide();            
			$(".stripe").hide();
			$(".pagarme").show();
		}
		else if (payment == 'stripe') {
			$(".braintree").hide();
			$(".pagarme").hide();
			$(".stripe").show();
		}
		else {
			$(".stripe").hide();
			$(".pagarme").hide();
			$(".braintree").show();
		}

	});

	$(function () {
		$("#default_storage").change(function () {
			val = $("#default_storage").val();
			if (val == 2) {
				$("#s3").show();
			}
			else {
				$("#s3").hide();
			}
		});
	});
</script>
<script type="text/javascript">
	$(function () {
		$("#mail_driver").change(function () {
			val = $("#mail_driver").val();
			if (val == 'mandrill') {
				$("#mandrill1").fadeIn(300);
				$("#mandrill2").fadeIn(300);
				$("#mandrill3").fadeIn(300);

				$("#sendgrid1").fadeOut(300);
				$("#sendgrid2").fadeOut(300);
				$("#sendgrid3").fadeOut(300);

			}else if(val == 'sendgrid'){
				$("#sendgrid1").fadeIn(300);
				$("#sendgrid2").fadeIn(300);
				$("#sendgrid3").fadeIn(300);
			
				$("#mandrill1").fadeOut(300);
				$("#mandrill2").fadeOut(300);
				$("#mandrill3").fadeOut(300);
			}
			else {
				$("#mandrill1").fadeOut(300);
				$("#mandrill2").fadeOut(300);
				$("#mandrill3").fadeOut(300);
				$("#sendgrid1").fadeOut(300);
				$("#sendgrid2").fadeOut(300);
				$("#sendgrid3").fadeOut(300);
			}
		});
	});
</script>
<script type="text/javascript">
	$(function () {
		$("#provider_transfer_interval").change(function () {
			val = $("#provider_transfer_interval").val();

			if(val == 'daily'){
				$("#provider_transfer_day_div").hide();
			}
			else if (val == 'weekly') {
				$("#provider_transfer_day_div").show();
				$("#provider_transfer_day_weekly").show();            
				$("#provider_transfer_day_monthly").hide();
			}
			else {
				$("#provider_transfer_day_div").show();
				$("#provider_transfer_day_weekly").hide();            
				$("#provider_transfer_day_monthly").show();
			}
		});

		$("#default_payment").change(function () {
			val = $("#default_payment").val();

			if(val == 'pagarme'){
				$(".braintree").hide();            
				$(".stripe").hide();
				$(".pagarme").show();
			}
			else if (val == 'stripe') {
				$(".braintree").hide();
				$(".pagarme").hide();
				$(".stripe").show();
			}
			else {
				$(".stripe").hide();
				$(".pagarme").hide();
				$(".braintree").show();
			}
		});
	});
</script>
<?php if ($success == 1) { ?>
	<script type="text/javascript">
	
		alert("{{trans('keywords.config_update_alert');}}");
	
	</script>
<?php } ?>
<?php if ($success == 2) { ?>
	<script type="text/javascript">
		alert("{{trans('keywords.config_wrong_alert');}}");
	</script>
<?php } ?>
<?php if ($success == 3) { ?>
	<script type="text/javascript">
		alert("{{trans('keywords.please_pem_cert');}}");
	</script>
<?php } ?>
<?php if ($success == 4) { ?>
	<script type="text/javascript">
		alert("{{trans('keywords.android_gcm_empty');}}");
	</script>
<?php } ?>
<?php if ($success == 5) { ?>
	<script type="text/javascript">
		alert("{{trans('keywords.mobile_notification_not_changed');}}");
	</script>
<?php } ?>

<script>
	$(function () {
		$("[data-toggle='tooltip']").tooltip();
	});
</script>
@stop