
@extends('layout')

@section('content')

<div class="box box-success">
	<br/>
	@if (Session::has('msg'))
		<h4 class="alert alert-info">
		{{{ Session::get('msg') }}}
		{{{Session::put('msg',NULL)}}}
		</h4>
	@endif
	<br/>
	<div class="box-body ">
		<form method="post" action="{{ URL::Route('AdminAdminsUpdate') }}"  enctype="multipart/form-data">
			<input type="hidden" name="id" value="<?= $admin->id ?>">
			<div class="form-group">
				<div class="row">
					<div class="col-xs-6">
						<label class="control-label">{{ trans('login.user_name');}}</label>
						<input type="text" class="form-control" name="username" value="{{$admin->username}}" style="margin:0px"/>
					</div>
				</div>
				<br>
				<div class="row">
					<div class="col-xs-6">
						<label class="control-label">{{ trans('login.old_password');}}</label>
						<input class="form-control" type="password" name="old_password" placeholder="{{ trans('login.old_password');}} " style="margin:0px">
					</div>
				</div>
				<br>
				<div class="row">
					<div class="col-xs-6">
						<label class="control-label">{{ trans('login.new_password');}}</label>
						<input type="password" class="form-control" name="new_password" placeholder="{{ trans('login.new_password');}}" style="margin:0px">
					</div>
				</div>
				<br>
				<div class="form-group">
					<label>{{trans('login.access')}}</label> </br>
					@foreach($permission as $permissions)
						<?php 
						foreach ($adminPermission as $admPermission) {
							if(is_object($admPermission)) {
								$ids[] = $admPermission->permission_id;
							} else {
								$ids[] = $admPermission;
							}
							
						}
						?>
						<input class="icheckbox_minimal" id="permission" name="permission[]" type="checkbox" value="{{$permissions->id}}"
						<?php
							if(!empty($ids)){
								if(in_array($permissions->id, $ids)) {
									echo "checked='checked'";
								}
							}
						?>>
						{{trans('add_admin_permission.'.$permissions->name)}}
						<?php if($permissions->name == "index"){ ?>
							<b> - {{trans('add_admin_permission.index2')}}</b>
						<?php } ?>
						<br/>
						<?php 
							$permissionsTree = DB::table('permission_sub_action')->where('parent_id', $permissions->id)->get();
						?>
						@foreach($permissionsTree as $permissionTree)
							<label1></label>
								<input class="icheckbox_minimal" id="permission" name="permission[]" type="checkbox" value="{{$permissionTree->id}}"
								<?php
									if(!empty($ids)){
										if(in_array($permissionTree->id, $ids)) {
											echo "checked='checked'";
										}
								}
								?>>
							{{trans('add_admin_permission.'.$permissionTree->name)}}
							<br/>
						@endforeach
					@endforeach
				</div>
			</div>
			<div class="box-footer">
																	
				<button type="submit" id="btnsearch" class="btn btn-flat btn-block btn-success">{{trans('keywords.save_change');}}</button>                       
			</div>
		</form>
	</div>
</div>									 

<?php
if($success == 1) { ?>
<script type="text/javascript">
		alert('{{ trans('adminController.update_success');}}');
</script>
<?php } ?>
<?php
if($success == 2) { ?>
<script type="text/javascript">
		alert('{{trans('keywords.config_wrong_alert');}}');
</script>
<?php } ?>

<style type="text/css">
	label1 {
			padding-left: 30px;
	}
	input {
			width: 13px;
			height: 13px;
			padding: 5px;
			margin: 5px;
			vertical-align: bottom;
			position: relative;
			top: -1px;
	}
</style>

<style type="text/css">
	label2 {
			padding-left: 40px;
	}
	input {
			width: 26px;
			height: 26px;
			padding: 10px;
			margin: 10px;
			vertical-align: bottom;
			position: relative;
			top: -1px;
	}
</style>

<style type="text/css">
	label3 {
			padding-left: 60px;
	}
	input {
			width: 39px;
			height: 39px;
			padding: 15px;
			margin: 15px;
			vertical-align: bottom;
			position: relative;
			top: -1px;
	}
</style>
@stop