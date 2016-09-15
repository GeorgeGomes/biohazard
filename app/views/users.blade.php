@extends('layout')

@section('content')
<?php 
$adminPermission = Session::get('adminPermission');

foreach ($adminPermission as $permission) {
	$array[] = $permission->permission_id;
}
?>
@if(Session::has('msg'))
<div class="alert alert-success"><b><?php
		echo Session::get('msg');
		Session::put('msg', NULL);
		?></b></div>
@endif

<form method="get" action="{{ URL::Route('/admin/searchur') }}" onload="setRadioVisibilityOnLoad()">
	<div class="col-md-22 col-sm-22">

		<div class="box box-danger">

		
			<div class="box-header">
				<h3 class="box-title">{{ trans('dashboard.filter'); }}</h3>
			</div>

			<div class="box-body row">
				<div class="col-md-6 col-sm-6 col-lg-2">
					<?php $id = Input::get('id') ?>
		            <input  type="number" min="0" class="form-control" id="id" name="id" value="{{ Input::get('id') }}" placeholder="Id" >
		            
		        </div>

		        <div class="col-md-6 col-sm-6 col-lg-4">
		        	<?php $name = Input::get('name') ?>
		            <input type="text" class="form-control" id="name" name="name" value="{{ Input::get('name') }}" placeholder="{{trans('providerController.name');}}" />
		          
		        </div>

		        <div class="col-md-6 col-sm-6 col-lg-6">
		        	<?php $email = Input::get('email') ?>
		            <input type="text" class="form-control" id="email" name="email" value="{{ Input::get('email') }}" placeholder="Email" >
		           
		        </div>
	        </div>
	        <div class="box-body row">
		        <div class="col-md-6 col-sm-6 col-lg-6">
		        	<?php $state = Input::get('state') ?>
		            <input type="text" class="form-control" id="state" name="state" value="{{ Input::get('state') }}" placeholder="{{trans('providerController.state');}}" >
		          
		        </div>

		        <div class="col-md-6 col-sm-6 col-lg-6">
		        	<?php $address = Input::get('address') ?>
		            <input type="text" class="form-control" id="address" name="address" value="{{ Input::get('address') }}" placeholder="{{trans('providerController.address');}}" >
		            
		        </div>
	        </div>

	        <div class="box-body row">
		        <div class="col-md-12 col-sm-12 col-lg-12">
		            
		            <label for="payment_card">{{trans('adminController.debt');}}</label>
		           	<?php $debt = Input::get('debt') ?>
		            <select name="debt"  class="form-control">
	                    <option value="0"> {{trans('adminController.debt');}}</option>
	                    <option value="1" <?php echo Input::get('debt') == 1 ? "selected" : "" ?> >{{trans('adminController.yes');}}</option>
	                    <option value="2" <?php echo Input::get('debt') == 2 ? "selected" : "" ?>>{{trans('adminController.no');}}</option>
	                </select>
					 <br/>
		        </div>
	        </div>
	        

			<div class="box-footer" align="right">
		        <button type="submit" name="btnsearch" class="btn btn-flat btn-block btn-success " value="Filter_Data">{{trans('provider.search');}}</button>
		    </div>
		</div>
	</div>

	<?php 
		if( $order==0){
			$order = 1;
		} else if( $order==1){
			$order = 0;
		} 
	;?>

	<?php 
    if(sizeof($users) != 0){
        ?>
        <div class="box box-info tbl-box ">
        <?php
    }else{
        
	        ?>
	<div class="col-md-12 col-sm-12">

	        <?php
	    }
	?>

		<div align="left" id="paglink"><?php echo $users->appends(array(
			'id' => Session::get('id'),
			'name' => Session::get('name'), 
			'email' => Session::get('email'),
			'address' => Session::get('address'),
			'debt' => Session::get('debt'),  
			'type' => Session::get('type'), 
			'order' => Session::get('order'), 
			'state' => Session::get('state'), 

			))->links(); ?></div>

		<table class="table table-bordered">
			<tbody>
				<tr>
					
					<th>
						<div  title="{{ trans('provider.id'); }}" >							

							<a id="namelabel" onclick="updateOrdenation()" href="<?php echo asset_url().'/admin/searchur?name='.$name.'&id='.$id.'&debt='.$debt.'&state='.$state.'&address='.$address.'&email='.$email.'&order='.$order.'&type=id' ?>"> {{ trans('map.id');}} 

								<?php
									if($type == 'id'){
										if($order == 0){ ?>
											<i align="right" name="order" class="fa fa-arrow-up" ></i>
										<?php }else if($order == 1){ ?>
											<i align="right" name="order" class="fa fa-arrow-down"></i>
										<?php }
									}	
								?>
							</a>
						</div>
					</th>
					<th>
						
						<div  title="{{trans('provider.name');}}" >
							<a id="namelabel" onclick="updateOrdenation()" href="<?php echo  asset_url().'/admin/searchur?name='.$name.'&id='.$id.'&debt='.$debt.'&state='.$state.'&address='.$address.'&email='.$email.'&order='.$order.'&type=first_name' ?>"> {{ trans('provider.name_grid');}} 
							 

								<?php
									if($type == 'first_name'){
										if($order == 0){ ?>
											<i align="right" name="order" class="fa fa-arrow-up" ></i>
										<?php }else if($order == 1){ ?>
											<i align="right" name="order" class="fa fa-arrow-down"></i>
										<?php }
									}	
								?>
							</a>
						</div>
					</th>
					<th>
						
						 
						<div  title="{{ trans('provider.mail'); }}" >


							<a id="maillabel" onclick="updateOrdenation()" href="<?php echo  asset_url().'/admin/searchur?name='.$name.'&id='.$id.'&debt='.$debt.'&state='.$state.'&address='.$address.'&email='.$email.'&order='.$order.'&type=email' ?>"> {{ trans('provider.mail_grid');}} 

								<?php
									if($type == 'email'){
										if($order == 0){ ?>
											<i align="right" name="order" class="fa fa-arrow-up" ></i>
										<?php }else if($order == 1){ ?>
											<i align="right" name="order" class="fa fa-arrow-down"></i>
										<?php }else{ ?>
											<i align="right" name="order" class="fa fa-arrows-v"></i> <?php
										}
									}
								?>
							</a>
						</div>
					</th>
					<th>{{ trans('provider.phone_grid');}}</th>
					<th>{{ trans('user.debt');}}</th>
					<th>{{ trans('user.refered_by');}}</th>
					<th>{{ trans('provider.action_grid');}}</th>
					
				</tr>

				<?php foreach ($users as $user) { ?>
					<tr>
						<td><?= $user->id ?></td>
						<td><?php echo $user->first_name . " " . $user->last_name; ?> </td>
						<td><?= $user->email ?></td>
						<td><?= $user->phone ?></td>
						<td><?= sprintf2($user->debt, 2) ?></td>
						<?php
						$refer = User::where('id', $user->referred_by)->first();
						if ($refer) {
							$referred = $refer->first_name . " " . $refer->last_name;
						} else {
							$referred = trans('user.none');
						}
						?>
						<td><?php echo $referred; ?></td>
						<td>
							<div class="dropdown">
								<button class="btn btn-flat btn-info dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown">
									{{trans('provider.action_grid');}}
									<span class="caret"></span>
								</button>
								<ul class="dropdown-menu" role="menu" aria-labelledby="dropdownMenu1">
									<?php if(in_array("401", $array)) { ?>
									<li role="presentation"><a role="menuitem" tabindex="-1" id="edit" href="{{ URL::Route('AdminUserEdit', $user->id) }}">{{ trans('user.edit');}}</a></li>
									<?php } ?>
									<?php if(in_array("402", $array)) { ?>
									<li role="presentation"><a role="menuitem" tabindex="-1" id="history" href="{{ URL::Route('AdminUserHistory',$user->id) }}">{{trans('provider.view_history_grid');}}</a></li>
									<?php } ?>
									<?php if(in_array("403", $array)) { ?>
									<li role="presentation"><a role="menuitem" tabindex="-1" id="coupon" href="{{ URL::Route('AdminUserReferral', $user->id) }}">{{ trans('user.cupon_detail');}}</a></li>
									<?php } ?>
									<?php if(in_array("404", $array)) { ?>
										<li role="presentation"><a role="menuitem" tabindex="-1" id="add_req" href="{{ URL::Route('AdminAddRequest', $user->id) }}">{{ trans('user.add_request');}}</a></li>           
									<?php } ?>
									<?php if(in_array("405", $array)) { ?>
									<li role="presentation"><a role="menuitem" tabindex="-1" id="add_req" onclick="return confirm('{{ trans('provider.delete_message') . ' ' . $user->first_name . ' ' . $user->last_name . '?'; }}')" href="{{ URL::Route('AdminDeleteUser', $user->id) }}">{{trans('provider.delete_grid');}}</a></li>
									<?php } ?>
								</ul>
							</div>
						</td>
					</tr>
				<?php } ?>
			</tbody>
		</table>
		<?php if(sizeof($users) == 0){ ?>
			<label class="col-md-12 col-sm-12 col-lg-12" align="center">{{ trans('user_provider_web.no_result'); }}</label>
		<?php } ?>

		<div align="left" id="paglink"><?php echo $users->appends(array(
			'id' => Session::get('id'),
			'name' => Session::get('name'), 
			'email' => Session::get('email'),
			'address' => Session::get('address'),
			'debt' => Session::get('debt'),  
			'type' => Session::get('type'), 
			'order' => Session::get('order'), 
			'state' => Session::get('state'), 

			))->links(); ?></div>

	</div>
</form>

@stop