
@extends('layout')

@section('content')

<div class="box box-primary">
	<div class="box-header">
		<h3 class="box-title"><?= $title ?></h3>
	</div>
	
	@if($success == 1)
	<div class="alert alert-danger">
		{{trans('provider.profile_type_updated')}}
	</div>
	@endif
	@if($success == 2)
	<div class="alert alert-danger">
		{{trans('provider.went_wrong')}}
	</div>
	@endif
	@if($success == 3)
	<div class="alert alert-danger">
		{{trans('provider.price_zero')}}
	</div>
	@endif
	@if($success == 4)
	<div class="alert alert-danger">
		{{trans('provider.image_size')}}
	</div>
	@endif

	
	<form method="post" id="basic" action="{{ URL::Route('AdminNgoUpdate') }}"  enctype="multipart/form-data">
		<input type="hidden" name="id" value="{{ $id }}">
		<input type="hidden" name="networking_code" id="networking_code" value="{{ $networkingCodeID }}">
		
		<div class="form-group col-md-12 col-sm-12">
			<a href="{{ URL::Route('AdminNgo') }}">
				<input type="button" class="btn btn-info btn-flat btn-block" value="Voltar">
			</a>
		</div>
		
		@if($id != 0)
		<div class="form-group col-md-12 col-sm-12">
			<h2>{{trans('ngo.myCode')}}: {{$myCode}}</h2>	
		</div>
		
		<div class="form-group col-md-12 col-sm-12">
			<h3>{{trans('ngo.networkingCode')}}: {{$networkingCode}}</h3>
		</div>
		@endif
		
		<div class="form-group col-md-12 col-sm-12">
			<label>{{trans('ngo.name');}}</label>
			<input type="text" class="form-control" name="name" placeholder="{{trans('ngo.name');}}" value="<?= $name ?>">
		</div>
		
		<div class="form-group col-md-12 col-sm-12">
			<label>{{trans('ngo.description');}}</label>
			<input type="text" class="form-control" name="description" placeholder="{{trans('ngo.description');}}" value="<?= $description ?>">
		</div>
		
		<div class="form-group col-md-12 col-sm-12">
			<label>{{trans('ngo.website');}}</label>
			<input type="text" class="form-control" name="website" placeholder="{{trans('ngo.website');}}" value="<?= $website ?>">
		</div>
		
		<div class="form-group col-md-12 col-sm-12">
			<label>{{trans('ngo.address');}}</label>
			<input type="text" class="form-control" name="address" placeholder="{{trans('ngo.address');}}" value="<?= $address ?>">
		</div>
		
		<div class="form-group col-md-12 col-sm-12">
			<label>{{trans('ngo.phone');}}</label>
			<input type="text" class="form-control" name="phone" placeholder="{{trans('ngo.phone');}}" value="<?= $phone ?>">
		</div>

		
		<div class="form-group col-md-6 col-sm-6">
			<label>{{trans('ngo.logotype');}}</label>
			<input type="file" name="logotype" class="form-control" >
			<br>
			<?php if ($logotype != "") { ?>
				<img src="<?= $logotype; ?>" height="50" width="50">
			<?php } else { ?>
				<img src="{{ asset_url(); }}/image/placeholder.png" class="img-rounded" height="50" width="50">
			<?php } ?><br>

			<p class="help-block">{{trans('provider.image_upload');}}</p>
		</div>

		<div class="box-footer">
			@if($id != 0)
			<button type="submit" id="add" class="btn btn-primary btn-flat btn-block">{{trans('provider.save');}}</button>			
			@else
			<button type="button" id="addNetworkingCode" class="btn btn-primary btn-flat btn-block">{{trans('provider.save');}}</button>	
			@endif
		</div>
	</form>
</div>

<script type="text/javascript">
	$("#basic").validate({
		rules: {
			name: "required",
			description: "required",
			address: "required"
		},
		messages:{
			name: "Nome é obrigatório!",
			description: "Descrição é obrigatório!",
			address: "Endereço é obrigatório!"
		}
	});

	$(function() {
		//$('#color').colorpicker();
	});
</script>

<script>
$('.regChecked').click(function(){

	var notChecked = true;
    if ($(this).is(':checked')) {
        $("#sub_category").val(1);
    } else {
		if($('.regChecked:checked').length == 0) {
    		$("#sub_category").val(0);
    	}
    }
});

$('#addNetworkingCode').click(function(){
	console.log("addNetworkingCode")
	if($("#basic").valid()){
		$('#myModal1').show();
	}
})
</script>


<div class="modal fade" id="myModal1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Digite o código de seu patrocinador!</h4>
      </div>
      <div class="modal-body">
		<div id="contentSearchCodeStep1">
			<input type="text" class="form-control" name="searchCode" id="searchCode" placeholder="" value="">
			<button type="button" id="searchNetworkingCode">Buscar</button>
			<div id="contentCodeError"></div>
		</div>
		<div id="contentSearchCodeStep2" style="display:none">
			<div id="contentCode"></div>
			<button type="button" id="searchNetworkingCodeYes">Sim</button>
			<button type="button" id="searchNetworkingCodeNo">Não</button>
		</div>
      </div>
    </div>
  </div>
</div>

<script>


$("#searchNetworkingCodeYes").click(function(){
	$('#basic').submit();
});

$("#searchNetworkingCodeNo").click(function(){
	$("#networking_code").val("");
	$("#contentSearchCodeStep1").show();
	$("#contentSearchCodeStep2").hide();
	$("#contentCode").html("");
});

$("#searchNetworkingCode").click(function(){
		$("#contentCodeError").html("");
	$.ajax({
		url: "{{ asset_url(); }}/api/v1/searchCode/" + $("#searchCode").val(),
		method: 'get',
		success: function(result){
			console.log("success");
			console.log(result);
			$("#contentSearchCodeStep1").hide();
			$("#contentSearchCodeStep2").show();
			$("#contentCode").html("Opa!<br/>Foi o " + result.name + " que te indicou?");
			
			$("#networking_code").val(result.my_code);
			console.log(result.my_code);
			
		},
		error: function(result){
			console.log("error");
			$("#contentCodeError").html("Ops! não encontramos ninguém com esse código!");
			
			
		},
		complete: function(result){
			console.log("complete");
		}
	})
})
</script>



@stop