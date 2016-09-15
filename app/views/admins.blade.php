@extends('layout')

@section('content')
<?php 
$adminPermission = Session::get('adminPermission');

foreach ($adminPermission as $permission) {
	$array[] = $permission->permission_id;
}
?>

<?php if(in_array("1401", $array)) { ?>
<a id="addinfo" href="{{ URL::Route('AdminAddAdmin') }}"><input type="button" class="btn btn-info btn-flat btn-block" value="{{ trans('adminController.add_admin');}}"></a>
<?php } ?>
<br >
<div class="box box-success">
 <div align="left" id="paglink"><?php echo $admin->links(); ?></div>
				<table class="table table-bordered">
				 <thead>
						<tr>
							<th>{{ trans('adminController.admin_id');}}</th>
							<th>{{ trans('login.user_name');}}</th>
							<th>{{trans('provider.address');}}</th>
							<th>{{ trans('provider.action_grid');}}</th>
						</tr>
					</thead> 
					<tbody>
						
							<?php foreach ($admin as $admins) { ?>
							<tr>
								<td>{{$admins->id}}</td>
								<td>{{$admins->username}}</td>
								<td><?php if($admins->address != NULL){ echo $admins->address; }else{ echo "";} ?> </td>
								<td >
									<div class="dropdown">
									  <button class="btn btn-flat btn-info dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown">
										{{ trans('provider.action_grid');}}
										<span class="caret"></span>
									  </button>
									  <ul class="dropdown-menu" role="menu" aria-labelledby="dropdownMenu1">
										<?php if(in_array("1402", $array)) { ?>
										<li role="presentation"><a role="menuitem" tabindex="-1" href="{{ URL::Route('AdminAdminsEdit', $admins->id) }}">{{ trans('adminController.edit_admin');}}</a></li>
										<?php } ?>
										<?php if(in_array("1403", $array)) { ?>
										<li role="presentation"><a role="menuitem" tabindex="-1" href="{{ URL::Route('AdminAdminsDelete', $admins->id) }}">{{ trans('adminController.delete_admin');}}</a></li>
										<?php } ?>
									  </ul>
									</div>
								</td>
							</tr>
							<?php } ?>
					</tbody>
				</table>
		 


</div>

		<div align="left" id="paglink"><?php echo $admin->links(); ?></div>
	  </div>
@stop