@extends('layout')
 
@section('content') 
<?php 
$adminPermission = Session::get('adminPermission');

foreach ($adminPermission as $permission) {
	$array[] = $permission->permission_id;
}
?>

<?php if(in_array("801", $array)) { ?>
<a id="adddoc" href="{{ URL::Route('AdminNgoEdit', 0) }}"><input type="button" class="btn btn-info btn-flat btn-block" value="{{trans('ngo.addNgo')}}"></a>
<?php } ?>

<br>


					<div class="box box-danger">

					   <form method="get" action="{{ URL::Route('/admin/searchdoc') }}">
								<div class="box-header">
									<h3 class="box-title">{{ trans('dashboard.filter'); }}</h3>
								</div>
								<div class="box-body row">

								<div class="col-md-6 col-sm-12">

								<select id="searchdrop" class="form-control" name="type">
									<option value="docid" id="docid">{{trans('map.id');}}</option>
									<option value="docname" id="docname">{{ trans('provider.name_grid');}}</option>
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



				<div class="box box-info tbl-box">
					 <div align="left" id="paglink"><?php echo $ngos->appends(array('type'=>Session::get('type'), 'valu'=>Session::get('valu')))->links(); ?></div>
				<table class="table table-bordered">
								<tbody>
										<tr>
												<th>{{trans('map.id');}}</th>
												<th>{{ trans('provider.name_grid');}}</th>
												<th>{{ trans('provider.action_grid');}}</th>

										</tr>

							<?php foreach ($ngos as $ngo) { ?>
							<tr>
								<td>{{ $ngo->id }}</td>
								<td>{{ $ngo->name }}</td>
								<td>
								@if(in_array("802", $array))
									<a id="edit" href="{{ URL::Route('AdminNgoEdit', $ngo->id) }}"><input type="button" class="btn btn-success" value="{{trans('provider.edit')}}"></a>
								@endif
								@if(in_array("803", $array))
									<a id="delete" href="{{ URL::Route('AdminNgoDelete', $ngo->id) }}"><input type="button" class="btn btn-danger" value="{{trans('reviews.delete');}}"></a>
								@endif
							</tr>
							<?php } ?>
					</tbody>
				</table>

				 <div align="left" id="paglink"><?php echo $ngos->appends(array('type'=>Session::get('type'), 'valu'=>Session::get('valu')))->links(); ?></div>

				</div>


@stop