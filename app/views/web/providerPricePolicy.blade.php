@extends('web.providerLayout')

@section('content')

<div>
	<form method="post" action="{{ URL::Route('ProviderPricePolicyUpdate') }}"  enctype="multipart/form-data">
		<div>
			<table class="table table-bordered">
				<thead>
					<tr>
						<th colspan="4" style="text-align: center;">{{ trans('provider.price_spreadsheet'); }}</th>
					</tr>
					<tr>
						<th>{{ trans('provider.services'); }}</th>
						<th>{{ trans('provider.categories'); }}</th>
						<th>{{ trans('provider.providers_price'); }}</th>
						<th>{{ trans('provider.exceeded_km'); }}</th>
					</tr>
				</thead>
				<tbody>
					@foreach($providerTypes as $providerType)
						<tr>
							<?php
								$offeredServiceNames = array();
								$existingCategories = array();
								$offeredCategories = array();
								
								$filteredPrices = $prices->filter(function ($item) use ($providerType) {
									return $item->type == $providerType->id;
								});
								
								$filteredPrices->all();
								
								foreach ($filteredPrices as $price) {
									$existingCategories[$price->type][] = $price->category;
									if ($price->is_visible == true) {
										$offeredCategories[$price->type][] = $price->category;
										$offeredServiceNames[] = $price->getType->name;
									}
								}
							?>
							<td rowspan="{{ count($filteredPrices) }}" style="vertical-align: middle;"><label><input id="provider_type-{{$providerType->id}}" name="provider_type[]" type="checkbox" value="{{$providerType->id}}" disabled="disabled" <?php
								if (!empty($offeredServiceNames)) {
									if (in_array($providerType->name, $offeredServiceNames))
										echo "checked='checked'";
								}
							?>> {{$providerType->name}}</label></td>
							
							<?php
								//The column Provider Types uses rowspan
								$contador=0;
							?>
							
							@foreach($filteredPrices as $price)
								@if ($contador == 0)
									@if (in_array($price->getTypeCategory->id, $existingCategories[$providerType->id]))
										<td><label class="provider_type_category-{{$providerType->id}}"><input id="provider_type_category-{{$price->id}}" name="provider_type_category_{{$providerType->id}}[]" type="checkbox" value="{{$price->getTypeCategory->id}}" disabled="disabled" <?php 
											if(array_key_exists($providerType->id, $offeredCategories) && in_array($price->getTypeCategory->id, $offeredCategories[$providerType->id])){
												echo "checked='checked'";
											}
										?>> {{$price->getTypeCategory->name}}</label></td>
										
										<td><input class="form-control provider_type_category-{{$price->id}}" type="text" id="base_price_provider-{{ $price->id }}" name="base_price_provider-{{ $price->id }}" value="<?= 100 * $price->base_price_provider ?>" disabled="disabled"></td>
										<td><input class="form-control provider_type_category-{{$price->id}}" type="text" id="price_per_unit_distance-{{ $price->id }}" name="price_per_unit_distance-{{ $price->id }}" value="<?= 100 * $price->price_per_unit_distance ?>" disabled="disabled"></td>
										
										<?php $contador++; ?>
									@endif
								@else
									@if (in_array($price->getTypeCategory->id, $existingCategories[$providerType->id]))
										</tr>
										<tr>
										<td><label class="provider_type_category-{{$providerType->id}}"><input id="provider_type_category-{{$price->id}}" name="provider_type_category_{{$providerType->id}}[]" type="checkbox" value="{{$price->getTypeCategory->id}}" disabled="disabled" <?php 
											if(array_key_exists($providerType->id, $offeredCategories) && in_array($price->getTypeCategory->id, $offeredCategories[$providerType->id])){
												echo "checked='checked'";
											}
										?>> {{$price->getTypeCategory->name}}</label></td>
										
										<td><input class="form-control provider_type_category-{{$price->id}}" type="text" id="base_price_provider-{{ $price->id }}" name="base_price_provider-{{ $price->id }}" value="<?= 100 * $price->base_price_provider ?>" disabled="disabled"></td>
										<td><input class="form-control provider_type_category-{{$price->id}}" type="text" id="price_per_unit_distance-{{ $price->id }}" name="price_per_unit_distance-{{ $price->id }}" value="<?= 100 * $price->price_per_unit_distance ?>" disabled="disabled"></td>
										
										<?php $contador++; ?>
									@endif
								@endif
							@endforeach
							
						</tr>
					@endforeach
				</tbody>
			</table>
			<br>
			<!--<button type="submit" class="btn btn-primary btn-flat btn-block">{{trans('provider.save');}}</button>
			<br><br>-->
			<p>{{ trans('provider.price_spreadsheet_utilities'); }}</p>
			<p>{{ trans('provider.price_spreadsheet_base_value'); }}</p>
		</div>
	</form>
</div>

<script type="text/javascript">
	$(document).ready(function() {
		//controle dos checkbox de categorias atendidas
		@foreach($providerTypes as $providerType)
			<?php 
				$filteredPrices = $prices->filter(function ($item) use ($providerType) {
					return $item->type == $providerType->id;
				});
				
				$filteredPrices->all();
			?>
			
			if($('#provider_type-{{$providerType->id}}').is(":checked")) {
				$('.provider_type_category-{{$providerType->id}}').css("display", "block");
			}
			else{
				$('.provider_type_category-{{$providerType->id}}').css("display", "none");
			}
			
			@foreach($filteredPrices as $price)
				if($('#provider_type_category-{{$price->id}}').is(":checked")) {
					$('.provider_type_category-{{$price->id}}').css("display", "block");
				}
				else{
					$('.provider_type_category-{{$price->id}}').css("display", "none");
				}
			@endforeach
			
			$('#provider_type-{{$providerType->id}}').change(function() {
				if(this.checked) {
					$('.provider_type_category-{{$providerType->id}}').css("display", "block");
					@foreach($filteredPrices as $price)
						$("#provider_type_category-{{$price->id}}").trigger("change");
					@endforeach
				}
				else{
					$('.provider_type_category-{{$providerType->id}}').css("display", "none");
					@foreach($filteredPrices as $price)
						$('.provider_type_category-{{$price->id}}').css("display", "none");
					@endforeach
				}
			});
			
			@foreach($filteredPrices as $price)
				$('#provider_type_category-{{$price->id}}').change(function() {
					if(this.checked) {
						$('.provider_type_category-{{$price->id}}').css("display", "block");
					}
					else{
						$('.provider_type_category-{{$price->id}}').css("display", "none");
					}
				});
			@endforeach
		@endforeach
	});
</script>

<script src="<?php echo asset_url(); ?>/javascript/jquery.maskMoney.min.js" type="text/javascript"></script>

<script type="text/javascript">
$(function() {
	@foreach ($prices as $price)
		$("#base_price_provider-{{ $price->id }}").maskMoney({prefix:'R$ ', thousands:'.', decimal:',', affixesStay: true});
		$("#base_price_provider-{{ $price->id }}").maskMoney('mask');
		$("#price_per_unit_distance-{{ $price->id }}").maskMoney({prefix:'R$ ', thousands:'.', decimal:',', affixesStay: true});
		$("#price_per_unit_distance-{{ $price->id }}").maskMoney('mask');
	@endforeach
});
</script>

@stop