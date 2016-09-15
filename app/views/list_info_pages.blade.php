@extends('layout')

@section('content')
<?php 
$adminPermission = Session::get('adminPermission');

foreach ($adminPermission as $permission) {
	$array[] = $permission->permission_id;
}
?>
<?php if(in_array("601", $array)) { ?>
<a id="addinfo" href="{{ URL::Route('AdminInformationEdit', 0) }}"><input type="button" class="btn btn-info btn-flat btn-block" value="{{trans('blade.new_page')}}"></a>
<?php } ?>
<br>


					<div class="box box-danger">

					   <form method="get" action="{{ URL::Route('/admin/searchinfo', 0) }}">
								<div class="box-header">
									<h3 class="box-title">{{ trans('dashboard.filter'); }}</h3>
								</div>
								<div class="box-body row">

								<div class="col-md-6 col-sm-12">

								<select id="searchdrop" class="form-control" name="type">
									<option value="infoid" id="infoid">{{ trans('provider.id_grid');}}</option>
									<option value="infotitle" id="infotitle">{{ trans('provider.title');}}</option>
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
					<div align="left" id="paglink"><?php echo $informations->appends(array('type'=>Session::get('type'), 'valu'=>Session::get('valu')))->links(); ?></div>
						<table class="table table-bordered">
							<tbody>
								<tr>
									<th>{{ trans('provider.id_grid');}}</th>
									<th>{{ trans('adminController.title');}}</th>
									<th>{{ trans('adminController.type');}}</th>
									<th>{{ trans('provider.action_grid');}}</th>

								</tr>
								<?php foreach ($informations as $information) { ?>
								<tr>
									<td><?= $information->id ?></td>
									<td><?= $information->title ?></td>
									<td> 
										<?php
											if ($information->type == 'user') {
												echo trans('adminController.client');
											} else if($information->type == 'provider'){
												echo trans('adminController.provider');
											}else{
												echo trans('adminController.both');
											}
										?>
									</td>
									<td>
									<?php if(in_array("602", $array)) { ?>
										<a id="edit" href="{{ URL::Route('AdminInformationEdit', $information->id) }}"><input type="button" class="btn btn-success" value="{{trans('blade.edit')}}"></a>
									<?php } ?>
									<?php if(in_array("603", $array)) { ?>
										<a id="delete" href="{{ URL::Route('AdminInformationDelete', $information->id) }}"><input type="button" class="btn btn-danger" value="{{trans('blade.delete')}}"></a>
									<?php } ?>
									</td>
								</tr>
								<?php } ?>
							</tbody>
						</table>
					<div align="left" id="paglink"><?php echo $informations->appends(array('type'=>Session::get('type'), 'valu'=>Session::get('valu')))->links(); ?></div>
				</div>
@stop