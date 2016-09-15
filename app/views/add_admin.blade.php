
@extends('layout')

@section('content')

<div class="box box-success">
<br/>
<br/>
										@if (Session::has('msg'))
										<h4 class="alert alert-info">
										{{{ Session::get('msg') }}} 
										{{{Session::put('msg',NULL)}}}
										</h4>
									 @endif
								<br/>

										<div class="box-body ">
						<form method="post" action="{{ URL::Route('AdminAdminsAdd') }}">
									<div class="form-group">
										<div class="row">
											<div class="col-xs-6">
												<label class="control-label">{{ trans('provider.mail_grid');}}</label>
												<input class="form-control" type="text" name="username" placeholder="{{ trans('provider.mail_grid');}}" style="margin:0px">
											</div>
										</div>
									</div>
									<div class="form-group">
										<div class="row">
											<div class="col-xs-6">
												<label class="control-label">{{ trans('login.password');}}</label>
												<input type="password" class="form-control" name="password" placeholder="{{ trans('login.password');}}" style="margin:0px">
											</div>
										</div>
									</div>
									<div class="form-group">
										<label>{{trans('login.access')}}</label> </br>
											@foreach($permission as $permissions)
												<input class="icheckbox_minimal" id="permission" name="permission[]" type="checkbox" value="{{$permissions->id}}">
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
													<input class="icheckbox_minimal" id="permission" name="permission[]" type="checkbox" value="{{$permissionTree->id}}">
													{{trans('add_admin_permission.'.$permissionTree->name)}}
													<br/>
												@endforeach
											@endforeach
									</div>
								</div>
							<div class="box-footer">
								<button type="submit" id="btnsearch" class="btn btn-flat btn-block btn-success">{{ trans('adminController.add_admin');}}</button>
							</div>
						</form>
					</div>
</div>
										
<style type="text/css">
	label1 {
	    padding-left: 20px;
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