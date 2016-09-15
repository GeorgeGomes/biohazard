
@extends('layout')

@section('content')

<div class="box box-primary">
	<div class="box-header">
		<h3 class="box-title"><?= $title ?></h3>
	</div><!-- /.box-header -->
	<!-- form start -->
	<form method="post" id="basic" action="{{ URL::Route('AdminProviderTypeUpdate') }}"  enctype="multipart/form-data">
		<input type="hidden" name="id" value="<?= $id ?>">
		
		<div class="form-group col-md-12 col-sm-12">
			<a href="{{ URL::Route('AdminProviderTypes') }}">
				<input type="button" class="btn btn-info btn-flat btn-block" value="Voltar">
			</a>
		</div>
		
		<div class="form-group col-md-12 col-sm-12">
			<label>{{trans('provider.type_name');}}</label>
			<input type="text" class="form-control" name="name" placeholder="{{trans('provider.type_name');}}" name="name" value="<?= $name ?>">
		</div>

		<div class="form-group col-md-6 col-sm-6">
			<label>{{trans('provider.base_price');}}</label> <span id="no_amount_error1" style="display: none"> </span>
			<span id="no_zero_error1" style="display: none"> </span>
			<input type="text" class="form-control" onkeypress="return Isamount(event, 1);" placeholder="{{trans('provider.base_price');}}" name="base_price" value="<?= $base_price ?>" onblur="notzero(1)" id="no_zero_1" required="">
		</div>


		<div class="form-group col-md-6 col-sm-6">
			<label>{{trans('provider.distance_base_price');}}</label>
			<select name="base_distance" class="form-control">
				<?php
				for ($i = 1; $i <= 25; $i++) {
					if ($base_distance == $i) {
						?>
						<option value="<?= $i ?>" selected=""><?= $i . " " . $unit_set ?></option>
					<?php } else { ?>
						<option value="<?= $i ?>" ><?= $i . " " . $unit_set ?></option>
						<?php
					}
				}
				?>
			</select>
		</div>


        <div class="form-group col-md-6 col-sm-6">
            <label>{{trans('provider.maximum_distance');}}</label>
            <span id="no_amount_error3" style="display: none"> </span>
            <span id="no_zero_error3" style="display: none"> </span>
            <input type="text" class="form-control" onkeypress="return Isamount(event, 3);" placeholder="{{trans('provider.maximum_distance');}}" name="maximum_distance" value="{{ $maximum_distance  }}" onblur="notzero(3)" id="no_zero_3" required="">
        </div>

        <div class="form-group col-md-6 col-sm-6">
            <label> {{trans('provider.charge_provider_return');}}</label>
			<select name="charge_provider_return" class="form-control">
				<option value="0" >{{trans('provider.no');}} </option>
				<option value="1" <?= $charge_provider_return == 1? "selected" : ""  ?>>
					{{trans('provider.yes');}} 
				</option>
			</select>
        </div>
        
		<div class="form-group col-md-6 col-sm-6">
			<label>{{trans('provider.price_unit_distance');}}</label> <span id="no_amount_error2" style="display: none"> </span>
			<span id="no_zero_error2" style="display: none"> </span>
			<input type="text" class="form-control" onkeypress="return Isamount(event, 2);" placeholder="{{trans('provider.price_unit_distance');}}" name="distance_price" value="<?= $price_per_unit_distance ?>" onblur="notzero(2)" id="no_zero_2" required="">
		</div>
		<div class="form-group col-md-6 col-sm-6">
			<label>{{trans('provider.price_unit_time');}}</label> <span id="no_amount_error3" style="display: none"> </span>
			<span id="no_zero_error3" style="display: none"> </span>
			<input type="text" class="form-control" onkeypress="return Isamount(event, 3);" placeholder="{{trans('provider.price_unit_time');}}" name="time_price" value="<?= $price_per_unit_time ?>" onblur="notzero(3)" id="no_zero_3" required="">
		</div>

		<?php if($business_model == RequestCharging::BUSINESS_MODEL_PERCENTAGE){ ?>
	        <div class="form-group col-md-12 col-sm-12">
	            <label>{{trans('provider.commission_rate');}}</label>
	            <span id="no_amount_error3" style="display: none"> </span>
	            <span id="no_zero_error3" style="display: none"> </span>
	            <input type="text" class="form-control" onkeypress="return Isamount(event, 3);" placeholder="{{trans('provider.commission_rate');}}" name="commission_rate" value="{{ $commission_rate  }}" onblur="notzero(3)" id="no_zero_3" required="">
	        </div>
        <?php } ?>

		<!-- <div class="form-group col-md-6 col-sm-6">
			<label>{{trans('provider.max_size');}}</label> <span id="no_number_error1" style="display: none"> </span>
			<span id="no_zero_error4" style="display: none"> </span>
			<input type="text" class="form-control" onkeypress="return IsNumeric(event, 1);" placeholder="{{trans('provider.max_size');}}" name="max_size" value="<?= $max_size ?>"  onblur="notzero(4)" id="no_zero_4" required="">
		</div> -->

		<div class="form-group col-md-6 col-sm-6">
			<label>{{trans('provider.color');}}</label>

			<div id="layout_color" name="layout_color" class="input-group colorpicker-component">
			    <input id="color" name="color" type="text" value="{{ $color }}" class="form-control"></input>
				<span class="input-group-addon"><i></i></span>
			</div>
			
		</div>
		<div class="form-group col-md-6 col-sm-6">
			<label>{{trans('provider.destination_visible');}}</label>
			
			<select name="destination_visible" class="form-control">
				<?php if ($destination_visible == 1) { ?>
					<option value="1" selected="">{{trans('provider.visible');}}</option>
					<option value="0" >{{trans('provider.invisible');}}</option>
				<?php } else { ?>
					<option value="1" >{{trans('provider.visible');}}</option>
					<option value="0" selected="">{{trans('provider.invisible');}}</option>
				<?php } ?>
			</select>

		</div>

		<div class="form-group col-md-6 col-sm-6">
			<label>{{trans('provider.icon');}}</label>
			<input type="file" name="icon" class="form-control" >
			<br>
			<?php if ($icon != "") { ?>
				<img src="<?= $icon; ?>" height="50" width="50">
			<?php } else { ?>
				<img src="{{ asset_url(); }}/image/placeholder.png" class="img-rounded" height="50" width="50">
			<?php } ?><br>
			<p class="help-block">{{trans('provider.image_upload');}}</p>
		</div>
		<div class="form-group col-md-6 col-sm-6">
			<label>{{trans('provider.icon_maps');}}</label>
			<input type="file" name="icon_maps" class="form-control" >
			<br>
			<?php if ($icon_maps != "") { ?>
				<img src="<?= $icon_maps; ?>" height="50" width="50">
			<?php } else { ?>
				<img src="{{ asset_url(); }}/image/placeholder.png" class="img-rounded" height="50" width="50">
			<?php } ?><br>

			<p class="help-block">{{trans('provider.image_upload');}}</p>
		</div>
    	<div class="form-group col-md-6 col-sm-6">
			<label>{{trans('provider.optionalScreen');}}</label>
			<select name="sub_category_screen_visible" class="form-control" id="sub_category">
				<?php if ($sub_category_screen_visible == 1) { ?>
					<option value="0" >{{trans('provider.invisible');}}</option>
					<option value="1" selected="">{{trans('provider.visible');}}</option>
				<?php } else { ?>
					<option value="0" selected="">{{trans('provider.invisible');}}</option>
					<option value="1" >{{trans('provider.visible');}}</option>
				<?php } ?>
			</select>
		</div>
		
		<?php if (!$is_default == 1) { ?>
			<div class="form-group col-md-6 col-sm-6">
				<label>{{trans('provider.visivel');}}</label>
				<select name="is_visible" class="form-control">
					<?php if ($is_visible == 1) { ?>
						<option value="0" class="regSelect">{{trans('provider.invisible');}}</option>
						<option value="1" class="regSelect" selected="">{{trans('provider.visible');}}</option>
					<?php } else { ?>
						<option value="0" class="regSelect" selected="">{{trans('provider.invisible');}}</option>
						<option value="1" class="regSelect">{{trans('provider.visible');}}</option>
					<?php } ?>
				</select>
			</div>
		<?php } ?>

		@if($sub_category_screen_visible == 0)
			@if(count($categories))
			<div class="form-group col-md-12 " id="subCategpryId" style="display: none;">
				<label>{{trans('provider.categories')}}</label> </br>
					@foreach($categories as $category)
						<input class="regChecked" <?=($category->hasAssociationByTypeId($id) ? 'checked' : '')?> id="category-<?=$category->id?>" name="categories[]" type="checkbox" value="{{$category->id}}">
						<label for="category-<?=$category->id?>">{{ $category->name}}</label>
					@endforeach
			</div>
			@endif
		@endif
		@if($sub_category_screen_visible == 1)
			@if(count($categories))
			<div class="form-group col-md-12 " id="subCategpryId">
				<label>{{trans('provider.categories')}}</label> </br>
					@foreach($categories as $category)
						<input class="regChecked" <?=($category->hasAssociationByTypeId($id) ? 'checked' : '')?> id="category-<?=$category->id?>" name="categories[]" type="checkbox" value="{{$category->id}}">
						<label for="category-<?=$category->id?>">{{ $category->name}}</label>
					@endforeach
			</div>
			@endif
		@endif

		<?php if (!$is_default == 1) { ?>
			<div class="form-group col-md-6 col-sm-6">
				<label for="is_default">
					<input type="checkbox" name="is_default" value="1">
				{{trans('provider.default');}}</label>
			</div>
		<?php } else { ?>
			<input type="hidden" name="is_default" value="1">
			<input type="hidden" name="is_visible" value="1">
		<?php } ?>

		<div class="box-footer">
			<button type="submit" id="add" class="btn btn-primary btn-flat btn-block">{{trans('provider.save');}}</button>
		</div>
	</form>
</div>

<?php if ($success == 1) { ?>
	<script type="text/javascript">
		alert('{{trans('provider.profile_type_updated')}}');
		document.location.href = "{{ URL::Route('AdminProviderTypes') }}";
	</script>
<?php } ?>
<?php if ($success == 2) { ?>
	<script type="text/javascript">
		alert('{{trans('provider.went_wrong')}}');
	</script>
<?php } ?>
<?php if ($success == 3) { ?>
	<script type="text/javascript">
		alert('{{trans('provider.price_zero')}}');
	</script>
<?php } ?>
<?php if ($success == 4) { ?>
	<script type="text/javascript">
		alert('{{trans('provider.image_size')}}');
	</script>
<?php } ?>

<script type="text/javascript">
	$("#basic").validate({
		rules: {
			name: "required",
		}
	});

	$(function() {
		$('#color').colorpicker();
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

$("#sub_category").change(function() {
    if ($("#sub_category").val() == '0') {
        $('.regChecked:checked').prop('checked', false);
    	$('#subCategpryId').hide();
    } else {
    	$('#subCategpryId').show();
    }
});



</script>

<link href="<?php echo asset_url(); ?>/library/colorpicker/css/bootstrap-colorpicker.min.css" rel="stylesheet">
<script src="<?php echo asset_url(); ?>/library/colorpicker/js/bootstrap-colorpicker.min.js"></script>

@stop