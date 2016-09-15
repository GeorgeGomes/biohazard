@extends('layout')

@section('content')

<div class="box box-primary">
	<ul class="nav nav-tabs">
		<li class="active"><a data-toggle="tab" href="#home">
			<div class="box-header">
				<h3 class="box-title">{{ trans('user_provider_web.personal_info') }}</h3>
			</div><!-- /.box-header -->
		</a></li>
		<li><a data-toggle="tab" href="#spreadsheet">
			<div class="box-header">
				<h3 class="box-title">{{ trans('user_provider_web.price_policy') }}</h3>
			</div><!-- /.box-header -->
		</a></li>
		<li><a data-toggle="tab" href="#history">
			<div class="box-header">
				<h3 class="box-title">{{ trans('user_provider_web.attendance_history') }}</h3>
			</div><!-- /.box-header -->
		</a></li>
		<li><a data-toggle="tab" href="#bank_account">
			<div class="box-header">
				<h3 class="box-title">{{ trans('user_provider_web.bank_account') }}</h3>
			</div><!-- /.box-header -->
		</a></li>
		<li><a data-toggle="tab" href="#change_password">
			<div class="box-header">
				<h3 class="box-title">{{trans('user_provider_web.change_password') }}</h3>
			</div><!-- /.box-header -->
		</a></li>
	</ul>
	
	<br/>
		@if (Session::has('msg'))
			<h4 class="alert alert-info">
				{{{ Session::get('msg') }}}
				{{{Session::put('msg',NULL)}}}
			</h4>
		@endif
	<br/>

	<div class="tab-content">

		<!-- Personal info tab -->
    	<div id="home" class="tab-pane fade in active">
			<!-- form start -->
			<form method="post" id="main-form" action="{{ URL::Route('AdminProviderUpdate') }}"  enctype="multipart/form-data">
				<input type="hidden" name="id" value="<?= $provider->id ?>">

				<div class="box-body">
					<div class="form-group">
						<label for="first_name">{{trans('provider.first_name');}}</label>
						<input type="text" class="form-control" id="first_name" name="first_name" value="<?= $provider->first_name ?>" placeholder="{{trans('provider.first_name');}}" >
					</div>

					<div class="form-group">
						<label for="last_name">{{trans('provider.last_name');}}</label>
						<input class="form-control" type="text" id="last_name" name="last_name" value="<?= $provider->last_name ?>" placeholder="{{trans('provider.last_name');}}">
					</div>

					<div class="form-group">
						<label for="status">{{trans('provider.status_grid');}}</label>
						<select class="form-control" id="status" name="status">
							<option value="APROVADO"
								@if($provider->status->name == "APROVADO")
									selected
								@endif >
								{{trans('provider.approved_grid')}} </option>
							<option value="REJEITADO"
								@if($provider->status->name == "REJEITADO")
									selected
								@endif
							> {{trans('provider.rejected_grid')}} </option>
							<option value="EM_ANALISE"
								@if($provider->status->name == "EM_ANALISE")
									selected
								@endif
							> {{trans('provider.analysis_grid')}} </option>
							<option value="SUSPENSO"
								@if($provider->status->name == "SUSPENSO")
									selected
								@endif
							> {{trans('provider.suspended_grid')}} </option>
							<option value="PENDENTE"
								@if($provider->status->name == "PENDENTE")
									selected
								@endif
							>{{trans('provider.pending_grid')}}  </option>
							<option value="INATIVO"
								@if($provider->status->name == "INATIVO")
									selected
								@endif
							>{{trans('provider.inactive_grid')}}  </option>
						</select>
					</div>

					<div class="form-group">
						<label for="email">{{ trans('provider.mail_grid');}}</label>
						<input class="form-control" type="email" id="email" name="email" value="<?= $provider->email ?>" placeholder="{{trans('provider.mail_grid');}}" >
					</div>

					<div class="form-group">
						<label for="phone">{{ trans('provider.phone_grid');}}</label>
						<input class="form-control" type="text" name="phone" id="phone" maxlength="15" value="<?= $provider->phone ?>" placeholder="{{trans('provider.phone_grid');}}">

						<input type="text" name="valid_phone" id="valid_phone" value=""  style="visibility:hidden;" />
					</div>

					<div class="form-group">
						<label for="cep">{{trans('provider.zipcode');}}</label>
						<input class="form-control" type="text" name="zipcode" id="cep" maxlength="10" value="<?= $provider->zipcode ?>" placeholder="{{trans('provider.zipcode');}}">
					</div>
					
					<div class="form-group">
						<label for="address">{{trans('provider.address');}}</label>
						<input class="form-control" type="text" name="address" id="address" value="<?= $provider->address ?>" placeholder="{{trans('provider.address');}}">
					</div>
					<div class="form-group">
						<label for="address_number">{{trans('provider.address_number');}}</label>
						<input type="text" class="form-control" name="address_number" id="address_number" value="{{ $provider->address_number }}">
					</div>
					<div class="form-group">
						<label for="address_complements">{{trans('provider.address_complements');}}</label>
						<input type="text" class="form-control" name="address_complements" id="address_complements" value="{{ $provider->address_complements }}">
					</div>
					
					<div class="form-group">
						<label for="address_neighbour">{{trans('provider.address_neighbour');}}</label>
						<input type="text" class="form-control" name="address_neighbour" id="address_neighbour" value="{{ $provider->address_neighbour }}">
					</div>
					<div class="form-group">
						<label for="address_city">{{trans('provider.address_city');}}</label>
						<input type="text" class="form-control" name="address_city" id="address_city" value="{{ $provider->address_city }}">
					</div>

					<div class="form-group">
						<label for="state">{{trans('provider.state');}}</label>
						<input class="form-control" type="text" name="state" id="state" value="<?= $provider->state ?>" placeholder="{{trans('provider.state');}}">
					</div>
					<div class="form-group">
						<label for="country">{{trans('provider.country');}}</label>
						<input class="form-control" type="text" name="country" id="country" value="<?= $provider->country ?>" placeholder="{{trans('provider.country');}}">
					</div>
					
					<div class="form-group">
						<label for="car_number">{{trans('provider.car_plate');}}</label>
						<input class="form-control" type="text" name="car_number" id="car_number" value="<?= $provider->car_number ?>" placeholder="{{trans('provider.car_plate');}}">
					</div>
					<div class="form-group">
						<label for="car_brand">{{trans('provider.car_brand');}}</label>
						<input class="form-control" type="text" name="car_brand" id="car_brand" value="<?= $provider->car_brand ?>" placeholder="{{trans('provider.car_brand');}}">
					</div>
					<div class="form-group">
						<label for="car_model">{{trans('provider.car_model');}}</label>
						<input class="form-control" type="text" name="car_model" id="car_model" value="<?= $provider->car_model ?>" placeholder="{{trans('provider.car_model');}}">
					</div>


					<div class="form-group">
						<br>
						<label for="pic">{{ trans('provider.picture_grid');}}</label>
						<input class="form-control" type="file" class="form-control" name="pic" id="pic" >
						<br>
						<img src="<?= $provider->picture; ?>" height="50" width="50"><br>
						<p class="help-block"{{trans('provider.message_jpg_png');}}/p>
						<div id="upload-demo"></div>
						<input type="hidden" id="picture_cropped" name="picture_cropped">
						<div class="cropped"></div>
					</div>
					<div class="form-group">
						<label>{{ trans('customize.created_at');}}: </label>
						<p><?= date("d/m/Y", strtotime($provider->created_at)) . " - " . substr($provider->created_at, 11) ?></p>
					</div>
					<div class="form-group">
						<label>{{ trans('customize.updated_at');}}: </label>
						<p><?= date("d/m/Y", strtotime($provider->updated_at)) . " - " . substr($provider->updated_at, 11)  ?></p>
					</div>
					<div class="form-group">
						<label>{{ trans('provider.on_duty_now');}}: </label>
						<?php
						$request = DB::table('request')
								->select('id')
								->where('request.is_started', 1)
								->where('request.is_completed', 0)
								->where('confirmed_provider', $provider->id);
						$count = $request->count();
						if ($count > 0) {
							echo trans('provider.yes');
						} else {
							echo trans('provider.no');
						}
						?>
					</div>
					<div class="form-group">
						<label>{{trans('provider.provider_is_active');}}: </label>
						<?php
						$request = DB::table('provider')
								->select('id')
								->where('provider.is_active', 1)
								->where('provider.id', $provider->id);
						$count = $request->count();
						if ($count > 0) {
							echo trans('provider.yes');
						} else {
							echo trans('provider.no');
						}
						?>
					</div>
					
				</div><!-- /.box-body -->

				<div class="box-footer">
					<button id="update" type="submit" class="btn btn-primary btn-flat btn-block">{{trans('keywords.save_change');}}</button>
				</div>
			</form>
		</div>
	
		<!-- Price policy tab -->
		<div id="spreadsheet" class="tab-pane fade">
		
			<form method="post" action="{{ URL::Route('AdminProviderPricePolicyUpdate') }}"  enctype="multipart/form-data">

				<input type="hidden" name="id-provider" value="<?= $provider->id ?>">
				
				<div style="margin: 15px;">
					<table class="table table-bordered">
						<thead>
							<tr>
								<th colspan="5" style="text-align: center;">{{ trans('provider.price_spreadsheet'); }}</th>
							</tr>
							<tr>
								<th>{{ trans('provider.services'); }}</th>
						@if(ProviderTypeCategory::count())
								<th>{{ trans('provider.categories'); }}</th>
								<th>{{ trans('provider.providers_price'); }}</th>
								<th>{{ trans('provider.users_price'); }}</th>
								<th>{{ trans('provider.exceeded_km'); }}</th>
						@endif
							</tr>
						</thead>
						<tbody>
						@foreach($providerTypes as $providerType)
							
						<?php
							$categories = $providerType->getDefaultCategories();

							$contador=0;
						?>

							@if(count($categories))
								
								@foreach($categories as $category)
							<tr>
						<?php
								$providerService = $category->getAssociationByProviderIdAndTypeId($provider->id, $providerType->id);


								//The column Provider Types uses rowspan
								
								
								if($contador == 0) {	?>
								<!-- nome do tipo de servico -->
								<td rowspan="{{ count($categories) }}" style="vertical-align: middle;">
									<label>
										<input id="provider_type-{{$providerType->id}}" name="provider_type[{{$providerType->id}}][selected]" type="checkbox" value="{{$providerType->id}}" <?=($providerType->hasAssociationByProviderId($provider->id) ? 'checked' : null)?>> {{$providerType->name}}
									</label>
								</td>

							<?		$contador++;
								} ?>

								<!-- nome da categoria -->
								<td>
									<label class="provider_type_category-{{$providerType->id}}">
									<input id="provider_type_category-{{$category->id}}" name="provider_type[{{$providerType->id}}][categories][{{$category->id}}][selected]" type="checkbox" value="{{$category->id}}" <?=($providerService ? 'checked' : null)?>> {{$category->name}}</label>
								</td>
								
								<!-- preco do provedor -->
								<td>
									<input class="form-control maskMoney" type="text" id="base_price_provider>" name="provider_type[{{$providerType->id}}][categories][{{$category->id}}][base_price_provider]" value="<?= ($providerService ? number_format(($providerService->base_price_provider),2) : 0 ) ?>">
								</td>
								
								<!-- preco do usuario -->

								<td>
									<input class="form-control maskMoney" type="text" id="base_price_user" name="provider_type[{{$providerType->id}}][categories][{{$category->id}}][base_price_user]" value="<?= ($providerService ? number_format(($providerService->base_price_user),2) : 0 ) ?>">
								</td>

								<!-- preco por km rodado -->
								<td>
									<input class="form-control maskMoney" type="text" id="price_per_unit_distance" name="provider_type[{{$providerType->id}}][categories][{{$category->id}}][price_per_unit_distance]" value="<?= ($providerService ? number_format(($providerService->price_per_unit_distance),2) : 0 ) ?>">
								</td>

							</tr>
								@endforeach
							@else
							<!-- somente servicos -->
							<tr>
								<!-- nome do tipo de servico -->
								<td style="vertical-align: middle;">
									<label>
										<input id="provider_type-{{$providerType->id}}" name="provider_type[{{$providerType->id}}][selected]" type="checkbox" value="{{$providerType->id}}" <?=($providerType->hasAssociationByProviderId($provider->id) ? 'checked' : null)?>> {{$providerType->name}}
									</label>
								</td>
							</tr>
							@endif
							
						@endforeach
						</tbody>
					</table>
					<br>
					<button type="submit" id="send-data" class="btn btn-primary btn-flat btn-block">{{trans('provider.save');}}</button>
					<br><br>
					<p>{{ trans('provider.price_spreadsheet_utilities'); }}</p>
					<p>{{ trans('provider.price_spreadsheet_base_value'); }}</p>
				</div>
			</form>
		</div>

		<!-- Attendance History Tab -->
		<div id="history" class="tab-pane fade">
			<form method="post" action="{{ URL::Route('AdminProviderHistoryUpdate') }}"  enctype="multipart/form-data">
				<input type="hidden" name="id-provider-history" value="<?= $provider->id ?>">

				<div style="margin: 15px;">

					<div class="form-group">
						<textarea name="attendance-notes" id="attendance-notes">
						{{$provider->attendance_history}}
						</textarea>
					</div>

					<button type="submit" class="btn btn-primary btn-flat btn-block">{{trans('provider.save');}}</button>

				</div>

			</form>
		</div>

		<!-- Bank Accont Tab -->
		<div id="bank_account" class="tab-pane fade">
			<form id="bank-account-form" method="post" action="{{ URL::Route('AdminProviderBankAccountUpdate') }}"  enctype="multipart/form-data">
				<input type="hidden" name="id-provider-bank-account" value="<?= $provider->id ?>">

				<div style="margin: 15px;">
				
					<div class="form group">
						<div class="col-md-12 col-sm-12">
			                <label >{{trans('user_provider_web.holder_name');}}</label>			          
			                <input type="text" id="holder" class="form-control" minlength="5" maxlength="30" name="holder" value="{{ $bank_account? $bank_account->holder : "" }}">
						  	<h4><span class="label label-info">		
						  		{{trans('user_provider_web.holder_name_info');}}
						  	</span></h4>
			            </div>

			            <div class="col-md-12 col-sm-12">
			                
		                	<label >{{trans('user_provider_web.holder_document');}}</label> 
		                	<div class="input-group" style="width:100%;">
								<input id="option_cpf" type="radio" name="option_document" value="individual" {{ !$bank_account || $bank_account->person_type == 'individual'? 'checked': '' }}> CPF
								<input id="option_cnpj" type="radio" name="option_document" value="company" {{ $bank_account && $bank_account->person_type == 'company'? 'checked': '' }}> CNPJ
			                	<input type="text" class="form-control" id="document" name="document" value="{{ $bank_account? $bank_account->document : "" }}">
		                	</div>

			            </div>

			            <div class="col-md-12 col-sm-12">
			                <label >{{trans('user_provider_web.bank');}}</label>
			                
			                <select class="form-control" id="bank_id" name="bank_id">
			                    @foreach($banks as $bank)
			                        <option {{ $bank_account && $bank_account->bank_id == $bank->id? "selected" : ""}} value="{{ $bank->id }}"> 
			                            {{ $bank->code }} - {{ $bank->name }}
			                        </option>
			                    @endforeach
			                </select>
			                
			            </div>

			            <div class="col-md-12 col-sm-12">
			                <label >{{trans('user_provider_web.agency');}}</label>
			                
			                <input type="text" class="form-control" name="agency" maxlength="10" value="{{ $bank_account? $bank_account->agency  : "" }}">
			                
			            </div>

			            <div class="col-md-6 col-sm-12">
			                <label>{{trans('user_provider_web.account_number');}}</label>                
			                <input type="text" class="form-control" name="account" maxlength="10" value="{{ $bank_account? $bank_account->account : "" }}">
			            </div>
			            <div class="col-md-6 col-sm-12">
			                <label>{{trans('user_provider_web.account_digit');}}</label>                
			                <input type="text" class="form-control" name="account_digit" maxlength="1" value="{{ $bank_account? $bank_account->account_digit : "" }}">
			                
			            </div>
		            </div>	
		            
	            	<div class="box-footer">
						<button type="submit" class="btn btn-primary btn-flat btn-block"  >{{trans('provider.save');}}</button>
					</div>
				</div>

			</form>
		</div>
		<!-- end bank account tab -->
		<div id="change_password" class="tab-pane fade">

			<form class="form-horizontal style-form" method="post" action="{{ URL::Route('AdminProviderPasswordUpdate') }}"  style="margin: 15px;">
			
				<input type="hidden" name="provider_id" value="<?= $provider->id ?>">

				<div class="form-group">
					<label class="col-sm-2 control-label" for="new_password">{{trans('login.new_password');}}</label>
					<div class="col-sm-6">
						<input type="password" class="form-control" id="new_password" name="new_password" value="">
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-2 control-label" for="confirm_password">{{trans('login.confirm_password');}}</label>
					<div class="col-sm-6">
						<input type="password" class="form-control" id="confirm_password" name="confirm_password" value="">
					</div>
				</div>
				<span class="col-sm-2"></span>
				<button id="pass" type="submit" class="btn btn-info">{{trans('user_provider_web.change_password');}}</button>
				<button type="reset" class="btn btn-info">{{trans('user_provider_web.reset');}}</button>

			</form>
		</div>
	</div>
</div>

<?php if ($success == 1) { ?>
	<script type="text/javascript">
			alert(''.Config::get('app.generic_keywords.Provider').'{{trans('keywords.config_wrong_alert');}}');</script>
<?php } ?>
<?php if ($success == 2) { ?>
	<script type="text/javascript">
				alert("{{trans('keywords.config_wrong_alert');}}");
	</script>
<?php } ?>

<script type="text/javascript">
	/**
	 *  Fill user address info based on the zipcode
	 */
	$("#cep").focusout(function(){
		var typedZipCode = $("#cep").val(); 
		var result = typedZipCode.match(/\d+/g);
		
		var resquestAddr="https://api.postmon.com.br/v1/cep/"+result[0]+result[1]+result[2];
		
		$.get(resquestAddr, function(data, status){
			$("#address").val(data.logradouro);
			$("#address_neighbour").val(data.bairro);
			$("#address_city").val(data.cidade);
			$("#state").val(data.estado_info.nome);
			// TODO: Country must be selected from a dropdown list
			$("#country").val("Brasil");
		});
	});

	// Apply a mask in phone and cep field as document is ready
	$(document).ready(function() {
	
		var varCEP = $('#cep').val();
	
		if (varCEP || varCEP.length != 0) {
			$('#cep').val($('#cep').val().replace(/(\d{2})(\d{3})(\d{3})/, "$1.$2-$3"));
		}
	});

	function mascara(o,f){
		v_obj=o
		v_fun=f
		setTimeout("execmascara()",1)
	}
	function execmascara(){
		v_obj.value=v_fun(v_obj.value)
	}
	//Regex para o número de telefone
	function phoneMask(v){
		v=v.replace(/\D/g,"");						//Remove tudo o que não é dígito
		v=v.replace(/^(\d{2})(\d)/g,"($1) $2");		//Coloca parênteses em volta dos dois primeiros dígitos
		v=v.replace(/(\d)(\d{4})$/,"$1-$2");		//Coloca hífen entre o quarto e o quinto dígitos
		return v;
	}
	//Regex para o CEP
	function zipcodeMask(v){
		v=v.replace(/\D/g,"");						//Remove tudo o que não é dígito
		v=v.replace(/^(\d{2})(\d)/g,"$1.$2");		//Coloca ponto apos os dois primeiros digitos
		v=v.replace(/(\d{3})(\d{1,})$/,"$1-$2");	//Coloca hífen entre o quinto e o sexto dígito
		return v;
	}
	//Regex para o CPF
	function cpfMask(v){
        v=v.replace(/\D/g,"")                    //Remove tudo o que não é dígito
        v=v.replace(/(\d{3})(\d)/,"$1.$2")       //Coloca um ponto entre o terceiro e o quarto dígitos
        v=v.replace(/(\d{3})(\d)/,"$1.$2")       //Coloca um ponto entre o terceiro e o quarto dígitos
                                                 //de novo (para o segundo bloco de números)
        v=v.replace(/(\d{3})(\d{1,2})$/,"$1-$2") //Coloca um hífen entre o terceiro e o quarto dígitos
        return v;
	}

	function cnpjMask(v) {
	    v = v.replace( /\D/g , ""); //Remove tudo o que não é dígito
	    v = v.replace( /^(\d{2})(\d)/ , "$1.$2"); //Coloca ponto entre o segundo e o terceiro dígitos
	    v = v.replace( /^(\d{2})\.(\d{3})(\d)/ , "$1.$2.$3"); //Coloca ponto entre o quinto e o sexto dígitos
	    v = v.replace( /\.(\d{3})(\d)/ , ".$1/$2"); //Coloca uma barra entre o oitavo e o nono dígitos
	    v = v.replace( /(\d{4})(\d)/ , "$1-$2"); //Coloca um hífen depois do bloco de quatro dígitos
	    return v;
	}

	function removePhoneMask(v){
		v=v.replace(/\D/g,"");
	}
	function removeZipCodeMask(v){
		v=v.replace(/\D/g,"");
	}
	function removeCpfMask(v){
		v=v.replace(/\D/g,"");
	}
	function id( el ){
		return document.getElementById( el );
	}

	function validateFields(){
		if($('#option_cpf').is(':checked')){ 
			console.log("validateCPF");
		}
		else{
			console.log("validateCNPJ");
		}
		return false;
	}

	function maskDocument(type){
		if(type == "cpf"){			
			$('#document').attr('maxlength','14');			
			mascara(id('document'), cpfMask );
			id('document').oninput = function() {
				mascara( this, cpfMask );
			}
		}
		else{
			$('#document').attr('maxlength','18');
			mascara( id('document'), cnpjMask );
			id('document').oninput = function() {
				mascara( this, cnpjMask );
			}
		}
	}

	window.onload = function() {
		
		id('cep').oninput = function() {
			mascara( this, zipcodeMask );
		}
	}

	$(document).ready(function() {
	
		if($('#option_cpf').is(':checked')){
			maskDocument("cpf");
		}
		else{
			maskDocument("cnpj");
		}

		id('option_cpf').onchange = function() {
			maskDocument("cpf");
		}

		id('option_cnpj').onchange = function() {
			maskDocument("cnpj");
		}

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

    $('#pic').on('change', function () { readFile(this); });
    $('#update').on('click', function (ev) {
        $uploadCrop.croppie('result', {
            type: 'canvas',
            size: 'original'
        }).then(function (resp) {
         	$('#picture_cropped').val(resp);
        });
    });

});
</script>

<script src="<?php echo asset_url(); ?>/javascript/jquery.maskMoney.min.js" type="text/javascript"></script>

<script type="text/javascript">
	$(document).ready(function() {

		var formValidator = $("#main-form").validate({
			rules: {
				first_name: "required",
				last_name: "required",
				country: "required",
				email: {
					required: true,
					email: true
				},
				state: "required",
				address: "required",
				zipcode: {
					required: true
				},
				phone: {
					required: true
				},
				valid_phone: "required",
			},
			messages: {
				first_name: "{{trans('user_provider_controller.first_name_needed')}}",
				last_name: "{{trans('user_provider_controller.last_name_needed')}}",
				email: {
					required: "{{trans('user_provider_controller.mail_needed')}}",
					email: "{{trans('user_provider_controller.mail_invalid')}}",
				},
				password: {
					required: "{{trans('user_provider_controller.password_needed')}}",
					minlength: "{{trans('user_provider_controller.password_invalid')}}",
				},
				phone: "{{trans('user_provider_controller.phone_needed')}}",
				valid_phone: "{{trans('providerController.invalid_phone_number')}}",
				provider_type: "{{trans('providerController.choose_service')}}"
			},
		});

		$("#bank-account-form").validate({
			rules: {
				holder: "required",
				document: "required",
				agency: "required",
				account: "required",
				account_digit: "required"
			}
		});

		$(".maskMoney").maskMoney({prefix:'R$ ', thousands:'.', decimal:','});

		$("#send-data").click( function() {			
			$(".maskMoney").each(function(){
				$(this).val($(this).maskMoney('unmasked')[0]);
			});;
		});

	 	var telInput = $("#phone"),
		  	validPhone = $("#valid_phone");
		  	
		var checkPhoneFieldEnter = 0;

		// initialise plugin
		telInput.intlTelInput({
		  utilsScript: "{{ asset_url(); }}/library/telinput/js/utils.js",
		  formatOnInit: false
		});


		if (checkPhoneFieldEnter == 0) {
			validPhone.val(1);
		}
		
		// on blur: validate
		telInput.blur(function() {

			if ($.trim(telInput.val())) {
				checkPhoneFieldEnter = 1;
				if (telInput.intlTelInput("isValidNumber")) {
					validPhone.val(1);
					formValidator.element('#valid_phone');
				} else {
					validPhone.val('');
					formValidator.element('#valid_phone');
				}
		  	}
		});

		//controle dos checkbox de categorias atendidas
	});
</script>

<!-- tel input libs -->
<link rel="stylesheet" href="{{ asset_url(); }}/library/telinput/css/intlTelInput.css">
<script src="{{ asset_url(); }}/library/telinput/js/intlTelInput.min.js"></script>
<style type="text/css">
	.intl-tel-input {width: 100%;}
</style>

<script src="{{ asset_url(); }}/javascript/ckeditor/ckeditor.js"></script>
<script>
	// Replace the <textarea id="attendance-notes"> with a CKEditor
	// instance, using default configuration.
	CKEDITOR.replace( 'attendance-notes' );
</script>



@stop