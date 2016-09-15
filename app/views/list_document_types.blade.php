@extends('layout')
 
@section('content')
<?php 
$adminPermission = Session::get('adminPermission');

foreach ($adminPermission as $permission) {
	$array[] = $permission->permission_id;
}
?>

<?php if(in_array("801", $array)) { ?>
<a id="adddoc" href="{{ URL::Route('AdminDocumentTypesEdit', 0) }}"><input type="button" class="btn btn-info btn-flat btn-block" value="{{trans('provider.add_new_doc_type')}}"></a>
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
					 <div align="left" id="paglink"><?php echo $types->appends(array('type'=>Session::get('type'), 'valu'=>Session::get('valu')))->links(); ?></div>
				<table class="table table-bordered">
								<tbody>
										<tr>
												<th>{{trans('map.id');}}</th>
												<th>{{ trans('provider.name_grid');}}</th>
												<th>{{ trans('provider.action_grid');}}</th>

										</tr>

							<?php foreach ($types as $type) { ?>
							<tr>
								<td><?= $type->id ?></td>
								<td><?= $type->name ?>
									<?php if($type->is_default){ ?>
										 <font style="color:green">(Default)</font>
									<?php } ?>
								</td>
								<td>
								<?php if(in_array("802", $array)) { ?>
									<a id="edit" href="{{ URL::Route('AdminDocumentTypesEdit', $type->id) }}"><input type="button" class="btn btn-success" value="{{trans('provider.edit')}}"></a>
								<?php } ?>
								<?php if(!$type->is_default){ ?>
								<?php if(in_array("803", $array)) { ?>
									<a id="delete" href="{{ URL::Route('AdminDocumentTypesDelete', $type->id) }}"><input type="button" class="btn btn-danger" value="{{trans('reviews.delete');}}"></a>
									<?php } ?>
								<?php } ?></td>
							</tr>
							<?php } ?>
					</tbody>
				</table>

				 <div align="left" id="paglink"><?php echo $types->appends(array('type'=>Session::get('type'), 'valu'=>Session::get('valu')))->links(); ?></div>

				</div>


@stop