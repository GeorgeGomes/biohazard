@extends('web.providerLayout')

@section('content')
<?php
	//var_dump(Session::all());
?>

<div class="col-md-12 mt">
	<!--
	@if($user->picture == "")
	<div class="alert alert-danger">
		<b>{{ "Por favor, selecione uma foto de perfil..." }}</b> 
	</div>
	@endif-->
	@if(Session::has('error'))
	<div class="alert alert-danger">
		<b>{{ Session::get('error') }}</b> 
	</div>
	@endif
	@if(Session::has('success'))
	<div class="alert alert-success">
		<b>{{ Session::get('success') }}</b> 
	</div>
	@endif
	@if(isset($error))
	<div class="alert alert-danger">
		<b>{{ $error }}</b> 
	</div>
	@endif
	@if(isset($success))
	<div class="alert alert-success">
		<b>{{ $success }}</b> 
	</div>
	@endif
			 
	<!--
	<div class="content-panel">
		<h4>{{trans('user_provider_web.availability_update');}}</h4><br>
		<form>
			<div class="form-group" >
				<label class="col-sm-2 control-label" id="flow22" for="availability">{{trans('user_provider_web.availability');}}</label>
				<div class="col-sm-6">
					<input type="checkbox" data-toggle="switch" name="availability" id="availability" <?= $user->is_active ? "checked" : "" ?>/>
				</div>
			</div>
		</form>
	</div> -->

	<div class="content-panel">

		<br><h4>{{trans('user_provider_web.update_perfil');}}</h4><br>
		<form class="form-horizontal style-form" method="post" action="{{ URL::Route('updateProviderProfile') }}" enctype="multipart/form-data">
			<div class="form-group">
				<label class="col-sm-2 control-label" for="first_name">{{trans('provider.first_name');}}</label>
				<div class="col-sm-6">
					<input type="text" class="form-control" name="first_name" id="first_name" maxlength="30" value="{{ $user->first_name }}" readonly="readonly">
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-2 control-label" for="last_name">{{trans('provider.last_name');}}</label>
				<div class="col-sm-6">
					<input type="text" class="form-control" name="last_name" id="last_name" maxlength="30" value="{{ $user->last_name }}" readonly="readonly">
				</div>
			</div>
			
			<div class="form-group">
				<span id="no_mobile_error1" style="display: none"> </span>
				<label class="col-sm-2 control-label" for="phone">{{ trans('provider.phone_grid');}}</label>
				<div class="col-sm-6">
					<input type="tel" name="phone" id="phone" maxlength="18" class="form-control intl-tel-input" required placeholder="*   {{trans('login.mobile_number');}}" value="{{ $user->phone }}">

					<input type="text" name="valid_phone" id="valid_phone" value=""  style="visibility:hidden;" />
				</div>
			</div>

			<div class="form-group">
				<label class="col-sm-2 control-label" for="picture">{{ trans('provider.picture_grid');}}</label>
				<div class="col-sm-1">
					<img src="<?php
					if ($user->picture == "") {
						echo asset_url() . "/web/default_profile.png";
					} else {
						echo $user->picture;
					}
					?>" class="img-circle" width="60">
				</div>
				<div class="col-sm-5" style="position:relative;top:15px;">
					<input type="file" class="form-control" name="picture" id="picture" >
					<div id="upload-demo"></div>
					<input type="hidden" id="picture_cropped" name="picture_cropped">
					<div class="cropped"></div>
				</div>
			</div>
			
			<div class="form-group">
				<label class="col-sm-2 control-label" for="car_number">{{trans('provider.car_plate');}}</label>
				<div class="col-sm-6">
					<input type="text" class="form-control" name="car_number" id="car_number" maxlength="15" value="{{ $user->car_number }}" readonly="readonly">
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-2 control-label" for="car_brand">{{trans('provider.car_brand');}}</label>
				<div class="col-sm-6">
					<input type="text" class="form-control" name="car_brand" id="car_brand" maxlength="15" value="{{ $user->car_brand }}" readonly="readonly">
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-2 control-label" for="car_model">{{trans('provider.car_model');}}</label>
				<div class="col-sm-6">
					<input type="text" class="form-control" name="car_model" id="car_model" maxlength="15" value="{{ $user->car_model }}" readonly="readonly">
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-2 control-label" for="cep">{{trans('provider.zipcode');}}</label>
				<div class="col-sm-6">
					<input type="text" class="form-control" name="zipcode" id="cep" maxlength="10" value="{{ $user->zipcode }}">
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-2 control-label" for="address">{{trans('provider.address');}}</label>
				<div class="col-sm-6">
					<input type="text" class="form-control" name="address" id="address" maxlength="30" value="{{ $user->address }}">
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-2 col-md-2 control-label" id="address_number">{{trans('provider.address_number');}}</label>
				<div class="col-sm-2">
					<input type="text" class="form-control" name="address_number" id="address_number" maxlength="10" value="{{ $user->address_number }}">
				</div>
				<label class="col-sm-2 col-md-1 control-label" for="address_complements">{{trans('provider.address_complements');}}</label>
				<div class="col-sm-3">
					<input type="text" class="form-control" name="address_complements" id="address_complements" maxlength="10" value="{{ $user->address_complements }}">
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-2 control-label" for="address_neighbour">{{trans('provider.address_neighbour');}}</label>
				<div class="col-sm-6">
					<input type="text" class="form-control" name="address_neighbour" id="address_neighbour" maxlength="20" value="{{ $user->address_neighbour }}">
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-2 control-label" for="address_city">{{trans('provider.address_city');}}</label>
				<div class="col-sm-6">
					<input type="text" class="form-control" name="address_city" id="address_city" maxlength="20" value="{{ $user->address_city }}">
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-2 control-label" for="state">{{trans('provider.state');}}</label>
				<div class="col-sm-6">
					<input type="text" class="form-control" name="state" id="state" maxlength="20" value="{{ $user->state }}">
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-2 control-label" for="country">{{trans('provider.country');}}</label>
				<div class="col-sm-6">
					<input type="text" class="form-control" name="country" id="country" maxlength="20" value="{{ $user->country }}">
				</div>
			</div>


			<div class="form-group">
				<label class="col-sm-2 control-label">{{trans('user_provider_web.timezone');}}</label>
				<div class="col-sm-6">
					<select class="form-control" id="timezone" name="timezone">
						<option value="Pacific/Midway" <?php
						if ($user->timezone == 'Pacific/Midway') {
							echo "selected";
						}
						?>>(UTC-11:00) Midway Island</option>
						<option value="Pacific/Samoa" <?php
						if ($user->timezone == 'Pacific/Samoa') {
							echo "selected";
						}
						?>>(UTC-11:00) Samoa</option>
						<option value="Pacific/Honolulu" <?php
						if ($user->timezone == 'Pacific/Honolulu') {
							echo "selected";
						}
						?>>(UTC-10:00) Hawaii</option>
						<option value="US/Alaska" <?php
						if ($user->timezone == 'US/Alaska') {
							echo "selected";
						}
						?>>(UTC-09:00) Alaska</option>
						<option value="America/Los_Angeles" <?php
						if ($user->timezone == 'America/Los_Angeles') {
							echo "selected";
						}
						?>>(UTC-08:00) Pacific Time (US &amp; Canada)</option>
						<option value="America/Tijuana" <?php
						if ($user->timezone == 'America/Tijuana') {
							echo "selected";
						}
						?>>(UTC-08:00) Tijuana</option>
						<option value="US/Arizona" <?php
						if ($user->timezone == 'US/Arizona') {
							echo "selected";
						}
						?>>(UTC-07:00) Arizona</option>
						<option value="America/Chihuahua" <?php
						if ($user->timezone == 'America/Chihuahua') {
							echo "selected";
						}
						?>>(UTC-07:00) Chihuahua</option>
						<option value="America/Chihuahua" <?php
						if ($user->timezone == 'America/Chihuahua') {
							echo "selected";
						}
						?>>(UTC-07:00) La Paz</option>
						<option value="America/Mazatlan" <?php
						if ($user->timezone == 'America/Mazatlan') {
							echo "selected";
						}
						?>>(UTC-07:00) Mazatlan</option>
						<option value="US/Mountain" <?php
						if ($user->timezone == 'US/Mountain') {
							echo "selected";
						}
						?>>(UTC-07:00) Mountain Time (US &amp; Canada)</option>
						<option value="America/Managua" <?php
						if ($user->timezone == 'America/Managua') {
							echo "selected";
						}
						?>>(UTC-06:00) Central America</option>
						<option value="US/Central" <?php
						if ($user->timezone == 'US/Central') {
							echo "selected";
						}
						?>>(UTC-06:00) Central Time (US &amp; Canada)</option>
						<option value="America/Mexico_City" <?php
						if ($user->timezone == 'America/Mexico_City') {
							echo "selected";
						}
						?>>(UTC-06:00) Guadalajara</option>
						<option value="America/Mexico_City" <?php
						if ($user->timezone == 'America/Mexico_City') {
							echo "selected";
						}
						?>>(UTC-06:00) Mexico City</option>
						<option value="America/Monterrey" <?php
						if ($user->timezone == 'America/Monterrey') {
							echo "selected";
						}
						?>>(UTC-06:00) Monterrey</option>
						<option value="Canada/Saskatchewan" <?php
						if ($user->timezone == 'Canada/Saskatchewan') {
							echo "selected";
						}
						?>>(UTC-06:00) Saskatchewan</option>
						<option value="America/Bogota" <?php
						if ($user->timezone == 'America/Bogota') {
							echo "selected";
						}
						?>>(UTC-05:00) Bogota</option>
						<option value="US/Eastern" <?php
						if ($user->timezone == 'US/Eastern') {
							echo "selected";
						}
						?>>(UTC-05:00) Eastern Time (US &amp; Canada)</option>
						<option value="US/East-Indiana" <?php
						if ($user->timezone == 'US/East-Indiana') {
							echo "selected";
						}
						?>>(UTC-05:00) Indiana (East)</option>
						<option value="America/Lima" <?php
						if ($user->timezone == 'America/Lima') {
							echo "selected";
						}
						?>>(UTC-05:00) Lima</option>
						<option value="America/Bogota" <?php
						if ($user->timezone == 'America/Bogota') {
							echo "selected";
						}
						?>>(UTC-05:00) Quito</option>
						<option value="Canada/Atlantic" <?php
						if ($user->timezone == 'Canada/Atlantic') {
							echo "selected";
						}
						?>>(UTC-04:00) Atlantic Time (Canada)</option>
						<option value="America/Caracas" <?php
						if ($user->timezone == 'America/Caracas') {
							echo "selected";
						}
						?>>(UTC-04:30) Caracas</option>
						<option value="America/La_Paz" <?php
						if ($user->timezone == 'America/La_Paz') {
							echo "selected";
						}
						?>>(UTC-04:00) La Paz</option>
						<option value="America/Santiago" <?php
						if ($user->timezone == 'America/Santiago') {
							echo "selected";
						}
						?>>(UTC-04:00) Santiago</option>
						<option value="Canada/Newfoundland" <?php
						if ($user->timezone == 'Canada/Newfoundland') {
							echo "selected";
						}
						?>>(UTC-03:30) Newfoundland</option>
						<option value="America/Sao_Paulo" <?php
						if ($user->timezone == 'America/Sao_Paulo') {
							echo "selected";
						}
						?>>(UTC-03:00) Brasilia</option>
						<option value="America/Argentina/Buenos_Aires" <?php
						if ($user->timezone == 'America/Argentina/Buenos_Aires') {
							echo "selected";
						}
						?>>(UTC-03:00) Buenos Aires</option>
						<option value="America/Argentina/Buenos_Aires" <?php
						if ($user->timezone == 'America/Argentina/Buenos_Aires') {
							echo "selected";
						}
						?>>(UTC-03:00) Georgetown</option>
						<option value="America/Godthab" <?php
						if ($user->timezone == 'America/Godthab') {
							echo "selected";
						}
						?>>(UTC-03:00) Greenland</option>
						<option value="America/Noronha" <?php
						if ($user->timezone == 'America/Noronha') {
							echo "selected";
						}
						?>>(UTC-02:00) Mid-Atlantic</option>
						<option value="Atlantic/Azores" <?php
						if ($user->timezone == 'Atlantic/Azores') {
							echo "selected";
						}
						?>>(UTC-01:00) Azores</option>
						<option value="Atlantic/Cape_Verde" <?php
						if ($user->timezone == 'Atlantic/Cape_Verde') {
							echo "selected";
						}
						?>>(UTC-01:00) Cape Verde Is.</option>
						<option value="Africa/Casablanca" <?php
						if ($user->timezone == 'Africa/Casablanca') {
							echo "selected";
						}
						?>>(UTC+00:00) Casablanca</option>
						<option value="Europe/London" <?php
						if ($user->timezone == 'Europe/London') {
							echo "selected";
						}
						?>>(UTC+00:00) Edinburgh</option>
						<option value="Etc/Greenwich" <?php
						if ($user->timezone == 'Etc/Greenwich') {
							echo "selected";
						}
						?>>(UTC+00:00) Greenwich Mean Time : Dublin</option>
						<option value="Europe/Lisbon" <?php
						if ($user->timezone == 'Europe/Lisbon') {
							echo "selected";
						}
						?>>(UTC+00:00) Lisbon</option>
						<option value="Europe/London" <?php
						if ($user->timezone == 'Europe/London') {
							echo "selected";
						}
						?>>(UTC+00:00) London</option>
						<option value="Africa/Monrovia" <?php
						if ($user->timezone == 'Africa/Monrovia') {
							echo "selected";
						}
						?>>(UTC+00:00) Monrovia</option>
						<option value="UTC" <?php
						if ($user->timezone == 'UTC') {
							echo "selected";
						}
						?>>(UTC+00:00) UTC</option>
						<option value="Europe/Amsterdam" <?php
						if ($user->timezone == 'Europe/Amsterdam') {
							echo "selected";
						}
						?>>(UTC+01:00) Amsterdam</option>
						<option value="Europe/Belgrade" <?php
						if ($user->timezone == 'Europe/Belgrade') {
							echo "selected";
						}
						?>>(UTC+01:00) Belgrade</option>
						<option value="Europe/Berlin" <?php
						if ($user->timezone == 'Europe/Berlin') {
							echo "selected";
						}
						?>>(UTC+01:00) Berlin</option>
						<option value="Europe/Berlin" <?php
						if ($user->timezone == 'Europe/Berlin') {
							echo "selected";
						}
						?>>(UTC+01:00) Bern</option>
						<option value="Europe/Bratislava" <?php
						if ($user->timezone == 'Europe/Bratislava') {
							echo "selected";
						}
						?>>(UTC+01:00) Bratislava</option>
						<option value="Europe/Brussels" <?php
						if ($user->timezone == 'Europe/Brussels') {
							echo "selected";
						}
						?>>(UTC+01:00) Brussels</option>
						<option value="Europe/Budapest" <?php
						if ($user->timezone == 'Europe/Budapest') {
							echo "selected";
						}
						?>>(UTC+01:00) Budapest</option>
						<option value="Europe/Copenhagen" <?php
						if ($user->timezone == 'Europe/Copenhagen') {
							echo "selected";
						}
						?>>(UTC+01:00) Copenhagen</option>
						<option value="Europe/Ljubljana" <?php
						if ($user->timezone == 'Europe/Ljubljana') {
							echo "selected";
						}
						?>>(UTC+01:00) Ljubljana</option>
						<option value="Europe/Madrid" <?php
						if ($user->timezone == 'Europe/Madrid') {
							echo "selected";
						}
						?>>(UTC+01:00) Madrid</option>
						<option value="Europe/Paris" <?php
						if ($user->timezone == 'Europe/Paris') {
							echo "selected";
						}
						?>>(UTC+01:00) Paris</option>
						<option value="Europe/Prague" <?php
						if ($user->timezone == 'Europe/Prague') {
							echo "selected";
						}
						?>>(UTC+01:00) Prague</option>
						<option value="Europe/Rome" <?php
						if ($user->timezone == 'Europe/Rome') {
							echo "selected";
						}
						?>>(UTC+01:00) Rome</option>
						<option value="Europe/Sarajevo" <?php
						if ($user->timezone == 'Europe/Sarajevo') {
							echo "selected";
						}
						?>>(UTC+01:00) Sarajevo</option>
						<option value="Europe/Skopje" <?php
						if ($user->timezone == 'Europe/Skopje') {
							echo "selected";
						}
						?>>(UTC+01:00) Skopje</option>
						<option value="Europe/Stockholm" <?php
						if ($user->timezone == 'Europe/Stockholm') {
							echo "selected";
						}
						?>>(UTC+01:00) Stockholm</option>
						<option value="Europe/Vienna" <?php
						if ($user->timezone == 'Europe/Vienna') {
							echo "selected";
						}
						?>>(UTC+01:00) Vienna</option>
						<option value="Europe/Warsaw" <?php
						if ($user->timezone == 'Europe/Warsaw') {
							echo "selected";
						}
						?>>(UTC+01:00) Warsaw</option>
						<option value="Africa/Lagos" <?php
						if ($user->timezone == 'Africa/Lagos') {
							echo "selected";
						}
						?>>(UTC+01:00) West Central Africa</option>
						<option value="Europe/Zagreb" <?php
						if ($user->timezone == 'Europe/Zagreb') {
							echo "selected";
						}
						?>>(UTC+01:00) Zagreb</option>
						<option value="Europe/Athens" <?php
						if ($user->timezone == 'Europe/Athens') {
							echo "selected";
						}
						?>>(UTC+02:00) Athens</option>
						<option value="Europe/Bucharest" <?php
						if ($user->timezone == 'Europe/Bucharest') {
							echo "selected";
						}
						?>>(UTC+02:00) Bucharest</option>
						<option value="Africa/Cairo" <?php
						if ($user->timezone == 'Africa/Cairo') {
							echo "selected";
						}
						?>>(UTC+02:00) Cairo</option>
						<option value="Africa/Harare" <?php
						if ($user->timezone == 'Africa/Harare') {
							echo "selected";
						}
						?>>(UTC+02:00) Harare</option>
						<option value="Europe/Helsinki" <?php
						if ($user->timezone == 'Europe/Helsinki') {
							echo "selected";
						}
						?>>(UTC+02:00) Helsinki</option>
						<option value="Europe/Istanbul" <?php
						if ($user->timezone == 'Europe/Istanbul') {
							echo "selected";
						}
						?>>(UTC+02:00) Istanbul</option>
						<option value="Asia/Jerusalem" <?php
						if ($user->timezone == 'Asia/Jerusalem') {
							echo "selected";
						}
						?>>(UTC+02:00) Jerusalem</option>
						<option value="Europe/Helsinki" <?php
						if ($user->timezone == 'Europe/Helsinki') {
							echo "selected";
						}
						?>>(UTC+02:00) Kyiv</option>
						<option value="Africa/Johannesburg" <?php
						if ($user->timezone == 'Africa/Johannesburg') {
							echo "selected";
						}
						?>>(UTC+02:00) Pretoria</option>
						<option value="Europe/Riga" <?php
						if ($user->timezone == 'Europe/Riga') {
							echo "selected";
						}
						?>>(UTC+02:00) Riga</option>
						<option value="Europe/Sofia" <?php
						if ($user->timezone == 'Europe/Sofia') {
							echo "selected";
						}
						?>>(UTC+02:00) Sofia</option>
						<option value="Europe/Tallinn" <?php
						if ($user->timezone == 'Europe/Tallinn') {
							echo "selected";
						}
						?>>(UTC+02:00) Tallinn</option>
						<option value="Europe/Vilnius" <?php
						if ($user->timezone == 'Europe/Vilnius') {
							echo "selected";
						}
						?>>(UTC+02:00) Vilnius</option>
						<option value="Asia/Baghdad" <?php
						if ($user->timezone == 'Asia/Baghdad') {
							echo "selected";
						}
						?>>(UTC+03:00) Baghdad</option>
						<option value="Asia/Kuwait" <?php
						if ($user->timezone == 'Asia/Kuwait') {
							echo "selected";
						}
						?>>(UTC+03:00) Kuwait</option>
						<option value="Europe/Minsk" <?php
						if ($user->timezone == 'Europe/Minsk') {
							echo "selected";
						}
						?>>(UTC+03:00) Minsk</option>
						<option value="Africa/Nairobi" <?php
						if ($user->timezone == 'Africa/Nairobi') {
							echo "selected";
						}
						?>>(UTC+03:00) Nairobi</option>
						<option value="Asia/Riyadh" <?php
						if ($user->timezone == 'Asia/Riyadh') {
							echo "selected";
						}
						?>>(UTC+03:00) Riyadh</option>
						<option value="Europe/Volgograd" <?php
						if ($user->timezone == 'Europe/Volgograd') {
							echo "selected";
						}
						?>>(UTC+03:00) Volgograd</option>
						<option value="Asia/Tehran" <?php
						if ($user->timezone == 'Asia/Tehran') {
							echo "selected";
						}
						?>>(UTC+03:30) Tehran</option>
						<option value="Asia/Muscat" <?php
						if ($user->timezone == 'Asia/Muscat') {
							echo "selected";
						}
						?>>(UTC+04:00) Abu Dhabi</option>
						<option value="Asia/Baku" <?php
						if ($user->timezone == 'Asia/Baku') {
							echo "selected";
						}
						?>>(UTC+04:00) Baku</option>
						<option value="Europe/Moscow" <?php
						if ($user->timezone == 'Europe/Moscow') {
							echo "selected";
						}
						?>>(UTC+04:00) Moscow</option>
						<option value="Asia/Muscat" <?php
						if ($user->timezone == 'Asia/Muscat') {
							echo "selected";
						}
						?>>(UTC+04:00) Muscat</option>
						<option value="Europe/Moscow" <?php
						if ($user->timezone == 'Europe/Moscow') {
							echo "selected";
						}
						?>>(UTC+04:00) St. Petersburg</option>
						<option value="Asia/Tbilisi" <?php
						if ($user->timezone == 'Asia/Tbilisi') {
							echo "selected";
						}
						?>>(UTC+04:00) Tbilisi</option>
						<option value="Asia/Yerevan" <?php
						if ($user->timezone == 'Asia/Yerevan') {
							echo "selected";
						}
						?>>(UTC+04:00) Yerevan</option>
						<option value="Asia/Kabul" <?php
						if ($user->timezone == 'Asia/Kabul') {
							echo "selected";
						}
						?>>(UTC+04:30) Kabul</option>
						<option value="Asia/Karachi" <?php
						if ($user->timezone == 'Asia/Karachi') {
							echo "selected";
						}
						?>>(UTC+05:00) Islamabad</option>
						<option value="Asia/Karachi" <?php
						if ($user->timezone == 'Asia/Karachi') {
							echo "selected";
						}
						?>>(UTC+05:00) Karachi</option>
						<option value="Asia/Tashkent" <?php
						if ($user->timezone == 'Asia/Tashkent') {
							echo "selected";
						}
						?>>(UTC+05:00) Tashkent</option>
						<option value="Asia/Calcutta" <?php
						if ($user->timezone == 'Asia/Calcutta') {
							echo "selected";
						}
						?>>(UTC+05:30) Chennai</option>
						<option value="Asia/Kolkata" <?php
						if ($user->timezone == 'Asia/Kolkata') {
							echo "selected";
						}
						?>>(UTC+05:30) Kolkata</option>
						<option value="Asia/Calcutta" <?php
						if ($user->timezone == 'Asia/Calcutta') {
							echo "selected";
						}
						?>>(UTC+05:30) Mumbai</option>
						<option value="Asia/Calcutta" <?php
						if ($user->timezone == 'Asia/Calcutta') {
							echo "selected";
						}
						?>>(UTC+05:30) New Delhi</option>
						<option value="Asia/Calcutta" <?php
						if ($user->timezone == 'Asia/Calcutta') {
							echo "selected";
						}
						?>>(UTC+05:30) Sri Jayawardenepura</option>
						<option value="Asia/Katmandu" <?php
						if ($user->timezone == 'Asia/Katmandu') {
							echo "selected";
						}
						?>>(UTC+05:45) Kathmandu</option>
						<option value="Asia/Almaty" <?php
						if ($user->timezone == 'Asia/Almaty') {
							echo "selected";
						}
						?>>(UTC+06:00) Almaty</option>
						<option value="Asia/Dhaka" <?php
						if ($user->timezone == 'Asia/Dhaka') {
							echo "selected";
						}
						?>>(UTC+06:00) Astana</option>
						<option value="Asia/Dhaka" <?php
						if ($user->timezone == 'Asia/Dhaka') {
							echo "selected";
						}
						?>>(UTC+06:00) Dhaka</option>
						<option value="Asia/Yekaterinburg" <?php
						if ($user->timezone == 'Asia/Yekaterinburg') {
							echo "selected";
						}
						?>>(UTC+06:00) Ekaterinburg</option>
						<option value="Asia/Rangoon" <?php
						if ($user->timezone == 'Asia/Rangoon') {
							echo "selected";
						}
						?>>(UTC+06:30) Rangoon</option>
						<option value="Asia/Bangkok" <?php
						if ($user->timezone == 'Asia/Bangkok') {
							echo "selected";
						}
						?>>(UTC+07:00) Bangkok</option>
						<option value="Asia/Bangkok" <?php
						if ($user->timezone == 'Asia/Bangkok') {
							echo "selected";
						}
						?>>(UTC+07:00) Hanoi</option>
						<option value="Asia/Jakarta" <?php
						if ($user->timezone == 'Asia/Jakarta') {
							echo "selected";
						}
						?>>(UTC+07:00) Jakarta</option>
						<option value="Asia/Novosibirsk" <?php
						if ($user->timezone == 'Asia/Novosibirsk') {
							echo "selected";
						}
						?>>(UTC+07:00) Novosibirsk</option>
						<option value="Asia/Hong_Kong" <?php
						if ($user->timezone == 'Asia/Hong_Kong') {
							echo "selected";
						}
						?>>(UTC+08:00) Beijing</option>
						<option value="Asia/Chongqing" <?php
						if ($user->timezone == 'Asia/Chongqing') {
							echo "selected";
						}
						?>>(UTC+08:00) Chongqing</option>
						<option value="Asia/Hong_Kong" <?php
						if ($user->timezone == 'Asia/Hong_Kong') {
							echo "selected";
						}
						?>>(UTC+08:00) Hong Kong</option>
						<option value="Asia/Krasnoyarsk" <?php
						if ($user->timezone == 'Asia/Krasnoyarsk') {
							echo "selected";
						}
						?>>(UTC+08:00) Krasnoyarsk</option>
						<option value="Asia/Kuala_Lumpur" <?php
						if ($user->timezone == 'Asia/Kuala_Lumpur') {
							echo "selected";
						}
						?>>(UTC+08:00) Kuala Lumpur</option>
						<option value="Australia/Perth" <?php
						if ($user->timezone == 'Australia/Perth') {
							echo "selected";
						}
						?>>(UTC+08:00) Perth</option>
						<option value="Asia/Singapore" <?php
						if ($user->timezone == 'Asia/Singapore') {
							echo "selected";
						}
						?>>(UTC+08:00) Singapore</option>
						<option value="Asia/Taipei" <?php
						if ($user->timezone == 'Asia/Taipei') {
							echo "selected";
						}
						?>>(UTC+08:00) Taipei</option>
						<option value="Asia/Ulan_Bator" <?php
						if ($user->timezone == 'Asia/Ulan_Bator') {
							echo "selected";
						}
						?>>(UTC+08:00) Ulaan Bataar</option>
						<option value="Asia/Urumqi" <?php
						if ($user->timezone == 'Asia/Urumqi') {
							echo "selected";
						}
						?>>(UTC+08:00) Urumqi</option>
						<option value="Asia/Irkutsk" <?php
						if ($user->timezone == 'Asia/Irkutsk') {
							echo "selected";
						}
						?>>(UTC+09:00) Irkutsk</option>
						<option value="Asia/Tokyo" <?php
						if ($user->timezone == 'Asia/Tokyo') {
							echo "selected";
						}
						?>>(UTC+09:00) Osaka</option>
						<option value="Asia/Tokyo" <?php
						if ($user->timezone == 'Asia/Tokyo') {
							echo "selected";
						}
						?>>(UTC+09:00) Sapporo</option>
						<option value="Asia/Seoul" <?php
						if ($user->timezone == 'Asia/Seoul') {
							echo "selected";
						}
						?>>(UTC+09:00) Seoul</option>
						<option value="Asia/Tokyo" <?php
						if ($user->timezone == 'Asia/Tokyo') {
							echo "selected";
						}
						?>>(UTC+09:00) Tokyo</option>
						<option value="Australia/Adelaide" <?php
						if ($user->timezone == 'Australia/Adelaide') {
							echo "selected";
						}
						?>>(UTC+09:30) Adelaide</option>
						<option value="Australia/Darwin" <?php
						if ($user->timezone == 'Australia/Darwin') {
							echo "selected";
						}
						?>>(UTC+09:30) Darwin</option>
						<option value="Australia/Brisbane" <?php
						if ($user->timezone == 'Australia/Brisbane') {
							echo "selected";
						}
						?>>(UTC+10:00) Brisbane</option>
						<option value="Australia/Canberra" <?php
						if ($user->timezone == 'Australia/Canberra') {
							echo "selected";
						}
						?>>(UTC+10:00) Canberra</option>
						<option value="Pacific/Guam" <?php
						if ($user->timezone == 'Pacific/Guam') {
							echo "selected";
						}
						?>>(UTC+10:00) Guam</option>
						<option value="Australia/Hobart" <?php
						if ($user->timezone == 'Australia/Hobart') {
							echo "selected";
						}
						?>>(UTC+10:00) Hobart</option>
						<option value="Australia/Melbourne" <?php
						if ($user->timezone == 'Australia/Melbourne') {
							echo "selected";
						}
						?>>(UTC+10:00) Melbourne</option>
						<option value="Pacific/Port_Moresby" <?php
						if ($user->timezone == 'Pacific/Port_Moresby') {
							echo "selected";
						}
						?>>(UTC+10:00) Port Moresby</option>
						<option value="Australia/Sydney" <?php
						if ($user->timezone == 'Australia/Sydney') {
							echo "selected";
						}
						?>>(UTC+10:00) Sydney</option>
						<option value="Asia/Yakutsk" <?php
						if ($user->timezone == 'Asia/Yakutsk') {
							echo "selected";
						}
						?>>(UTC+10:00) Yakutsk</option>
						<option value="Asia/Vladivostok" <?php
						if ($user->timezone == 'Asia/Vladivostok') {
							echo "selected";
						}
						?>>(UTC+11:00) Vladivostok</option>
						<option value="Pacific/Auckland" <?php
						if ($user->timezone == 'Pacific/Auckland') {
							echo "selected";
						}
						?>>(UTC+12:00) Auckland</option>
						<option value="Pacific/Fiji" <?php
						if ($user->timezone == 'Pacific/Fiji') {
							echo "selected";
						}
						?>>(UTC+12:00) Fiji</option>
						<option value="Pacific/Kwajalein" <?php
						if ($user->timezone == 'Pacific/Kwajalein') {
							echo "selected";
						}
						?>>(UTC+12:00) International Date Line West</option>
						<option value="Asia/Kamchatka" <?php
						if ($user->timezone == 'Asia/Kamchatka') {
							echo "selected";
						}
						?>>(UTC+12:00) Kamchatka</option>
						<option value="Asia/Magadan" <?php
						if ($user->timezone == 'Asia/Magadan') {
							echo "selected";
						}
						?>>(UTC+12:00) Magadan</option>
						<option value="Pacific/Fiji" <?php
						if ($user->timezone == 'Pacific/Fiji') {
							echo "selected";
						}
						?>>(UTC+12:00) Marshall Is.</option>
						<option value="Asia/Magadan" <?php
						if ($user->timezone == 'Asia/Magadan') {
							echo "selected";
						}
						?>>(UTC+12:00) New Caledonia</option>
						<option value="Asia/Magadan" <?php
						if ($user->timezone == 'Asia/Magadan') {
							echo "selected";
						}
						?>>(UTC+12:00) Solomon Is.</option>
						<option value="Pacific/Auckland" <?php
						if ($user->timezone == 'Pacific/Auckland') {
							echo "selected";
						}
						?>>(UTC+12:00) Wellington</option>
						<option value="Pacific/Tongatapu" <?php
						if ($user->timezone == 'Pacific/Tongatapu') {
							echo "selected";
						}
						?>>(UTC+13:00) Nuku'alofa</option>
					</select>
				</div>
			</div>
			<span class="col-sm-2"></span>
			<button id="update" type="submit" class="btn btn-info">{{trans('user_provider_web.update_perfil');}}</button>
			<!--<button type="reset" class="btn btn-info">{{trans('user_provider_web.reset');}}</button>-->

		</form>
	</div>

	<div class="content-panel">
		<h4>{{trans('user_provider_web.change_password');}}</h4><br>
		<form class="form-horizontal style-form" method="post" action="{{ URL::Route('updateProviderPassword') }}">
			<div class="form-group">
				<label class="col-sm-2 control-label">{{trans('login.old_password');}}</label>
				<div class="col-sm-6" for="current_password">
					<input type="password" class="form-control" id="current_password" maxlength="20" name="current_password" value="">
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-2 control-label" for="new_password">{{trans('login.new_password');}}</label>
				<div class="col-sm-6">
					<input type="password" class="form-control" id="new_password" maxlength="20" name="new_password" value="">
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-2 control-label" for="confirm_password">{{trans('login.confirm_password');}}</label>
				<div class="col-sm-6">
					<input type="password" class="form-control" id="confirm_password" maxlength="20" name="confirm_password" value="">
				</div>
			</div>
			<span class="col-sm-2"></span>
			<button id="pass" type="submit" class="btn btn-info">{{trans('user_provider_web.change_password');}}</button>
			<!--<button type="reset" class="btn btn-info">{{trans('user_provider_web.reset');}}</button>-->

		</form>
	</div>
</div>

<!-- tel input libs -->
<link rel="stylesheet" href="{{ asset_url(); }}/library/telinput/css/intlTelInput.css">
<script src="{{ asset_url(); }}/library/telinput/js/intlTelInput.min.js"></script>
<style type="text/css">
	.intl-tel-input {width: 100%;}
</style>

<script type="text/javascript">
	/**
	 *  Fill user address info based on the zipcode
	 */
	$("#cep").focusout(function(){
		var typedZipCode = $("#cep").val(); 
		var result = typedZipCode.match(/\d+/g);
		
		var requestAddr="https://api.postmon.com.br/v1/cep/"+result[0]+result[1]+result[2];
		
		$.get(requestAddr, function(data, status){
			$("#address").val(data.logradouro);
			$("#address_neighbour").val(data.bairro);
			$("#address_city").val(data.cidade);
			$("#state").val(data.estado_info.nome);
			// TODO: Country must be selected from a dropdown list
			$("#country").val("Brasil");
		});
	});
</script>

<script type="text/javascript">
	$("#availability").change(function () {
		$.ajax({
			type: 'post',
			url: '{{ URL::Route('toggle_availability') }}',
			success: function (msg) {
				console.log("{{trans('user_provider_web.state_change')}}");
			},
			processData: false,
		});
	});
</script>

<script type="text/javascript">
$( document ).ready(function() {
	var $uploadCrop;

	function readFile(input) {
		if (input.files && input.files[0]) {
			var reader = new FileReader();          
			reader.onload = function (e) {
				$uploadCrop.croppie('bind', {
					url: e.target.result
				});
				$('.upload-demo').addClass('ready');
			}
			reader.readAsDataURL(input.files[0]);
		}
	}

    $uploadCrop = $('#upload-demo').croppie({
        viewport: {
            width: 200,
            height: 200,
            type: 'square'
        },
        boundary: {
            width: 300,
            height: 300
        }
    });

    $('#picture').on('change', function () { readFile(this); });
    $('#update').on('click', function (ev) {
        $uploadCrop.croppie('result', {
            type: 'canvas',
            size: 'original'
        }).then(function (resp) {
			document.querySelector('.cropped').innerHTML = '<img src="'+resp+'" height="200" width="200">';
         	$('#picture_cropped').val(resp);
        });
    });

});
</script>

<script type="text/javascript">
	// Apply a mask in phone and cep field as document is ready
	$(document).ready(function() {
		var varCEP = $('#cep').val();
		
		if (varCEP || varCEP.length != 0) {
			$('#cep').val($('#cep').val().replace(/(\d{2})(\d{3})(\d{3})/, "$1.$2-$3"));
		}

		var telInput = $("#phone");
		var validPhone = $("#valid_phone");

		// initialise plugin /*
		telInput.intlTelInput({
		  utilsScript: "{{ asset_url(); }}/library/telinput/js/utils.js",
		  formatOnInit: false
		});


		
		// on blur: validate
		telInput.blur(function() {

			if ($.trim(telInput.val())) {
				if (telInput.intlTelInput("isValidNumber")) {
					validPhone.val(1);
					//formValidator.element('#valid_phone');
				} else {
					validPhone.val('');
					//formValidator.element('#valid_phone');
				}
		  	}
		});

	});

	function mascara(o,f){
		v_obj=o
		v_fun=f
		setTimeout("execmascara()",1)
	}
	function execmascara(){
		v_obj.value=v_fun(v_obj.value)
	}
	
	//Regex para o CEP
	function zipcodeMask(v){
		v=v.replace(/\D/g,"");						//Remove tudo o que não é dígito
		v=v.replace(/^(\d{2})(\d)/g,"$1.$2");		//Coloca ponto apos os dois primeiros digitos
		v=v.replace(/(\d{3})(\d{1,})$/,"$1-$2");	//Coloca hífen entre o quinto e o sexto dígito
		return v;
	}
	function removePhoneMask(v){
		v=v.replace(/\D/g,"");
	}
	function removeZipCodeMask(v){
		v=v.replace(/\D/g,"");
	}
	function id( el ){
		return document.getElementById( el );
	}
	window.onload = function() {

		id('cep').oninput = function() {
			mascara( this, zipcodeMask );
		}
	}

	var tour = new Tour(
	{
		name: "providerappProfile",
		template: "<div class='popover tour'><div class='arrow'></div><h3 class='popover-title'></h3><div class='popover-content'></div><div class='popover-navigation'><button class='btn btn-default' data-role='prev'>{{ trans("user_provider_web.prev_tour_step"); }}</button><button class='btn btn-default' data-role='next'>{{ trans("user_provider_web.next_tour_step"); }}</button><button class='btn btn-default' data-role='end'>{{ trans("user_provider_web.end_tour"); }}</button></div></div>",
	});

	// Add your steps. Not too many, you don't really want to get your users sleepy
	tour.addSteps([
		{
			element: "#flow22",
			title: "{{ trans("user_provider_web.availability_setting"); }}",
			content: "{{ trans("user_provider_web.availability_change_message"); }}",
			placement: "top",
		},
	]);

	// Initialize the tour
	tour.init();

	// Start the tour
	tour.start();
</script>

@stop 