@extends('layout')

@section('content')
<?php 
$adminPermission = Session::get('adminPermission');

foreach ($adminPermission as $permission) {
	$array[] = $permission->permission_id;
}
?>

<?php if(in_array("701", $array)) { ?>
<a id="addtype" href="{{ URL::Route('AdminProviderTypeEdit', 0) }}"><input type="button" class="btn btn-info btn-flat btn-block" value="{{ trans('provider.add_new_provider_type');}}"></a>
<?php } ?>

<br>
<div class="col-md-6 col-sm-12">
	<div class="box box-danger">
		<form method="get" action="{{ URL::Route('/admin/sortpvtype') }}">
			<div class="box-header">
				<h3 class="box-title">{{ trans('provider.sort');}}</h3>
			</div>
			
			<div class="box-body row">
				<div class="col-md-6 col-sm-12">
					<select class="form-control" id="sortdrop" name="type">
						<option value="provid" <?php
						if (isset($_GET['type']) && $_GET['type'] == 'provid') {
							echo 'selected="selected"';
						}
						?> id="provid">{{trans('provider.id_provider');}}</option>
						<option value="pvname" <?php
						if (isset($_GET['type']) && $_GET['type'] == 'pvname') {
							echo 'selected="selected"';
						}
						?> id="pvname">{{trans('provider.name_provider');}}</option>
					</select>
					<br>
				</div>
				<div class="col-md-6 col-sm-12">
					<select class="form-control" id="sortdroporder" name="valu">
						<option value="asc" <?php
						if (isset($_GET['valu']) && $_GET['valu'] == 'asc') {
							echo 'selected="selected"';
						}
						?> id="asc">{{ trans('provider.asc');}}</option>
						<option value="desc" <?php
						if (isset($_GET['valu']) && $_GET['valu'] == 'desc') {
							echo 'selected="selected"';
						}
						?> id="desc">{{ trans('provider.desc');}}</option>
					</select>
					<br>
				</div>
			</div>

			<div class="box-footer">
				<button type="submit" id="btnsort" class="btn btn-flat btn-block btn-success">{{ trans('provider.sort');}}</button>
			</div>
		</form>
	</div>
</div>

<div class="col-md-6 col-sm-12">
	<div class="box box-danger">
		<form method="get" action="{{ URL::Route('/admin/searchpvtype') }}">
			<div class="box-header">
				<h3 class="box-title">{{ trans('dashboard.filter'); }}</h3>
			</div>
			<div class="box-body row">
				<div class="col-md-6 col-sm-12">
					<select id="searchdrop" class="form-control" name="type">
						<option value="provid" id="provid">{{trans('provider.id_provider');}}</option>
						<option value="provname" id="provname">{{trans('provider.name_provider');}}</option>
					</select>
					<br>
				</div>
				
				<div class="col-md-6 col-sm-12">
					<input class="form-control" type="text" name="valu" id="insearch" placeholder="{{ trans('provider.key_word');}}"/>
					<br>
				</div>
			</div>

			<div class="box-footer">
				<button type="submit" id="btnsearch" class="btn btn-flat btn-block btn-success">{{ trans('provider.search');}}</button>
			</div>
		</form>
	</div>
</div>

<div class="box box-info tbl-box">
	<div align="left" id="paglink"><?php echo $types->appends(array('type' => Session::get('type'), 'valu' => Session::get('valu')))->links(); ?></div>
	<table class="table table-bordered">
		<tbody>
			<tr>
				<th>{{trans('provider.name_grid');}}</th>
				<th>{{trans('map.icon');}}</th>
				<th>{{trans('provider.distance_base_price');}}</th>
				<th>{{trans('provider.base_price');}}</th>
				<th>{{trans('provider.price_unit_distance');}}</th>
				<th>{{trans('provider.price_unit_time');}}</th>
				<th>{{trans('provider.visivel');}}</th>
				<th>{{trans('reviews.action');}}</th>
			</tr> 
			<?php foreach ($types as $type) {
				?>
				<tr>
					
					<td>[{{$type->id}}] - {{$type->name}}
						<?php if ($type->is_default) { ?>
							<font style="color:green">(Default)</font>
						<?php } ?>
					</td>
					<td>
						@if ($type->icon != "")
							<div><img src="{{$type->icon}}" height="40" width="40" title="{{trans('map.icon');}}" alt="{{trans('map.icon');}}"></div>
						@else
							<div><img src="{{ asset_url(); }}/image/placeholder.png" class="img-rounded" height="40" width="40"></div>
						@endif
						@if ($type->icon_maps != "")
							<div><img src="{{$type->icon_maps}}" height="40" width="40" title="{{trans('map.icon_maps');}}" alt="{{trans('map.icon_maps');}}"></div>
						@else
							<div><img src="{{ asset_url(); }}/image/placeholder.png" class="img-rounded" height="40" width="40"></div>
						@endif
					</td>
					<td><?= $type->base_distance . " " . $unit_set ?></td>
					<td><?= sprintf2($type->base_price, 2) ?></td>
					<td><?= sprintf2($type->price_per_unit_distance, 2) ?></td>
					<td><?= sprintf2($type->price_per_unit_time, 2) ?></td>
					<td>
						<?php
						if ($type->is_visible == 1) {
							echo "<span class='badge bg-green'>".trans('provider.visible')."</span>";
						} else {
							echo "<span class='badge bg-red'>".trans('provider.invisible')."</span>";
						}
						?>
					</td>
					<td>
						<?php if(in_array("702", $array)) { ?>
						<a href="{{ URL::Route('AdminProviderTypeEdit', $type->id) }}"><input type="button" class="btn btn-success" value="{{trans('provider.edit')}}"></a>
						<?php } ?>
						<?php if(in_array("703", $array)) { ?>
						<?php /* if (!$type->is_default) { ?>
						  <a href="{{ URL::Route('AdminProviderTypeDelete', $type->id) }}"><input type="button" class="btn btn-danger" value="Delete"></a>
						  <?php } */ ?>
						<?php } ?>
					</td>
				</tr>
			<?php } ?>
		</tbody>
	</table>
	<div align="left" id="paglink"><?php echo $types->appends(array('type' => Session::get('type'), 'valu' => Session::get('valu')))->links(); ?></div>
</div>

@stop