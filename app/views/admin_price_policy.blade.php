@extends('layout')

@section('content')

<a href="{{ URL::Route('AdminProviderTypes') }}">
	<input type="button" class="btn btn-info btn-flat btn-block" value="Administrar Tipos de Serviços">
</a>
<br><br>

<form method="post" action="{{ URL::Route('AdminPricePolicyUpdate') }}"  enctype="multipart/form-data">
	<input type="hidden" name="id" value="<?= $id ?>">
	
	<div>
		<table class="table table-bordered">
			<thead>
				<tr>
					<th colspan="5" style="text-align: center;">{{ trans('provider.price_spreadsheet'); }}</th>
				</tr>
				<tr>
					<th>{{ trans('provider.services'); }}</th>
					<th>{{ trans('provider.categories'); }}</th>
					<th>{{ trans('provider.providers_price'); }}</th>
					<th>{{ trans('provider.users_price'); }}</th>
					<th>{{ trans('provider.exceeded_km'); }}</th>
				</tr>
			</thead>
			<tbody>
				@foreach ($prices as $price)
					<tr>
						<td>{{ $price->getType->name }}</td>
						<td>{{ $price->getTypeCategory->name }}</td>
						<td>
							<input class="form-control maskMoney" type="text" id="base_price_provider-{{ $price->id }}" name="base_price_provider-{{ $price->id }}" value="<?= number_format($price->base_price_provider, 2) ?>">
						</td>
						<td>
							<input class="form-control maskMoney" type="text" id="base_price_user-{{ $price->id }}" name="base_price_user-{{ $price->id }}" value="<?= number_format($price->base_price_user, 2) ?>">
						</td>
						<td>
							<input class="form-control maskMoney" type="text" id="price_per_unit_distance-{{ $price->id }}" name="price_per_unit_distance-{{ $price->id }}" value="<?= number_format($price->price_per_unit_distance, 2) ?>">
						</td>
					</tr>
				@endforeach
			</tbody>
		</table>
		<br>
		<button id="send-data" type="submit" class="btn btn-primary btn-flat btn-block">{{trans('provider.save');}}</button>
		<br><br>
		<p>{{ trans('provider.price_spreadsheet_utilities'); }}</p>
		<p>{{ trans('provider.price_spreadsheet_base_value'); }}</p>
	</div>
</form>

<script src="<?php echo asset_url(); ?>/javascript/jquery.maskMoney.min.js" type="text/javascript"></script>

<script type="text/javascript">
$(function() {

	$(".maskMoney").maskMoney({prefix:'R$ ', thousands:'.', decimal:','});
	
	$("#send-data").click( function() {
		$(".maskMoney").each(function(){
			$(this).val($(this).maskMoney('unmasked')[0]);
		});;
	});
});
</script>
@stop