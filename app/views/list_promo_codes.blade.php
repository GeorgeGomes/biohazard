@extends('layout')
@section('content')
<?php 
$adminPermission = Session::get('adminPermission');

foreach ($adminPermission as $permission) {
	$array[] = $permission->permission_id;
}
?>
<?php if(in_array("901", $array)) { ?>
<div class="row">
	<div class="col-md-12 col-sm-12">
		<a id="addpromo" href="{{ URL::Route('AdminPromoCodeEdit', 0) }}"><button class="btn btn-flat btn-block btn-info" type="button">{{trans('provider.add_promo_code')}}</button></a>
		<br/>
	</div>
</div>
<?php } ?>
<div class="col-md-12 col-sm-12">
	<div class="box box-danger">
		<form method="get" action="{{ URL::Route('/admin/searchpromo') }}">
			<div class="box-header">
				<h3 class="box-title">{{ trans('dashboard.filter'); }}</h3>
			</div>

			<div class="box-body row">
				<div class="col-md-2 col-sm-6 col-lg-2">
					<?php $id = Input::get('id') ?>
		            <input  type="number" min="0" class="form-control" id="id" name="id" value="{{ Input::get('id') }}" placeholder="Id" >
		            <br> 
		        </div>

		        <div class="col-md-6 col-sm-6 col-lg-5">
		        	<?php $coupon = Input::get('coupon') ?>
		            <input type="text" class="form-control" id="coupon" name="coupon" value="{{ Input::get('coupon') }}" placeholder="{{trans('adminController.promo_code');}}" />
		            <br>
		        </div>
		        <div class="col-md-2 col-sm-6 col-lg-2">
					<?php $type = Input::get('type') ?>
		            <select name="type"  class="form-control">
	                    <option value="0"> {{trans('adminController.type');}}</option>
	                    <option value="1" <?php echo Input::get('type') == 1 ? "selected" : "" ?> >{{trans('adminController.percentage');}}</option>
	                    <option value="2" <?php echo Input::get('type') == 2 ? "selected" : "" ?>>{{trans('adminController.value');}}</option>
	                </select>
		            <br> 
		        </div>

		       
		        <div class="col-md-12 col-sm-12 col-lg-2">
		           	<?php $state = Input::get('state') ?>
		            <select name="state"  class="form-control">
	                    <option value="0"> {{trans('adminController.state');}}</option>
	                    <option value="1" <?php echo Input::get('state') == 1 ? "selected" : "" ?> >{{trans('adminController.Active');}}</option>
	                    <option value="2" <?php echo Input::get('state') == 2 ? "selected" : "" ?>>{{trans('adminController.Desactive');}}</option>
	                </select>
					 <br/>
		        </div>

			</div>

			<div class="box-footer">
				<button type="submit" id="btnsearch" class="btn btn-flat btn-block btn-success">{{ trans('provider.search');}}</button>
			</div>
		</form>
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
    if(sizeof($promo_codes) != 0){
        ?>
        <div class="box box-info tbl-box ">
        <?php
    }else{
        
        ?>
<div class="col-md-12 col-sm-12">

        <?php
    }
?>
	<div align="left" id="paglink"><?php echo $promo_codes->appends(array(
			'id' => Session::get('id'),
			'coupon' => Session::get('coupon'), 
			'type' => Session::get('type'), 
			'order' => Session::get('order'), 
			'state' => Session::get('state'),
			'order_type' => Session::get('order_type'), 

			))->links(); ?></div>
	<table class="table table-bordered">
		<tbody>
			<tr>
				<th>

					<a id="namelabel" href="<?php echo asset_url().'/admin/searchpromo?coupon='.$coupon.'&id='.$id.'&type='.$type.'&state='.$state.'&order='.$order.'&order_type=id' ?>"> {{ trans('map.id');}} 

						<?php 
							if($order_type == 'id'){
								if($order == 0){ ?>
									<i align="right" name="order" class="fa fa-arrow-up" ></i>
								<?php }else if($order == 1){ ?>
									<i align="right" name="order" class="fa fa-arrow-down"></i>
								<?php }
							}	
						?>
					</a>


				</th>
				<th>
					<a id="namelabel" href="<?php echo asset_url().'/admin/searchpromo?coupon='.$coupon.'&id='.$id.'&type='.$type.'&state='.$state.'&order='.$order.'&order_type=coupon_code' ?>"> {{ trans('adminController.promo_code');}} 

						<?php 
							if($order_type == 'coupon_code'){
								if($order == 0){ ?>
									<i align="right" name="order" class="fa fa-arrow-up" ></i>
								<?php }else if($order == 1){ ?>
									<i align="right" name="order" class="fa fa-arrow-down"></i>
								<?php }
							}	
						?>
					</a>
				</th>
				<th>{{trans('blade.value')}}</th>
				<th>{{trans('blade.user_remain')}}</th>
				<th>{{ trans('provider.state');}}</th>
				<th>{{trans('blade.expired');}}</th>
				<th>{{trans('dashboard.start_date');}}</th>
				<th>{{trans('dashboard.date_expire');}}</th>
				<th style="width: 105px;">{{ trans('provider.action_grid');}}</th>
			</tr>
			<?php foreach ($promo_codes as $promo) { ?>
				<tr>
					<td><?= $promo->id ?></td>
					<td><?= $promo->coupon_code ?></td>
					<td><?php
						if ($promo->type == 1) {
							echo $promo->value . " %";
						} elseif ($promo->type == 2) {
							echo Config::get('app.currency') . " " . $promo->value;
						}
						?></td>
					<td><?= $promo->uses ?></td>
					<td><?php
						if ($promo->state == 1) {
							echo trans('blade.active');
						} elseif ($promo->state == 0) {
							echo trans('blade.expired');
						} elseif ($promo->state == 2) {
							echo trans('blade.desactive');
						} elseif ($promo->state == 3) {
							echo trans('blade.limit_max');
						}
						?></td>
					<td>
						<?php
						if (date("Y-m-d H:i:s") < date("Y-m-d H:i:s", strtotime(trim($promo->start_date)))) {
							echo "<span class='badge bg-blue'>". trans('blade.desactive') ."</span>";
						} else if (date("Y-m-d H:i:s") >= date("Y-m-d H:i:s", strtotime(trim($promo->expiry)))) {
							echo "<span class='badge bg-red'>" . trans('blade.expired') . "</span>";
						} else {
							echo "<span class='badge bg-green'>". trans('blade.active') . "</span>";
						}
						?>
					</td>
					<td><?= date("d M Y g:i:s A", strtotime(trim($promo->start_date))) ?></td>
					<td><?= date("d M Y g:i:s A", strtotime(trim($promo->expiry))) ?></td>
					<td>
						<div class="dropdown">
							<button class="btn btn-flat btn-info dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown">
								{{trans('provider.action_grid');}}
								<span class="caret"></span>
							</button>
							<ul class="dropdown-menu" role="menu" aria-labelledby="dropdownMenu1">
								<?php if(in_array("902", $array)) { ?>
								<li role="presentation"><a role="menuitem" tabindex="-1" id="edit" href="{{ URL::Route('AdminPromoCodeEdit',$promo->id) }}">{{trans('blade.edit_promo_code')}}</a></li>
								<?php } ?> 
								<?php if ($promo->state == 1) { ?>
									<?php if(in_array("904", $array)) { ?>
									<li role="presentation"><a role="menuitem" tabindex="-1" id="edit" href="{{ URL::Route('AdminPromoCodeDeactivate',$promo->id) }}">{{trans('blade.desatived')}}</a></li>
									<?php } ?>
								<?php } elseif ($promo->state == 2) { ?>
									<?php if(in_array("903", $array)) { ?>
									<li role="presentation"><a role="menuitem" tabindex="-1" id="edit" href="{{ URL::Route('AdminPromoCodeActivate',$promo->id) }}">{{trans('blade.actived')}}</a></li>
									<?php } ?>
								<?php } ?>
								<!--li role="presentation"><a role="menuitem" tabindex="-1" id="history" href="">View History</a></li>
								<li role="presentation"><a role="menuitem" tabindex="-1" id="coupon" href="">Delete</a></li-->
							</ul>
						</div>
					</td>
				</tr>
			<?php } ?>
		</tbody>
	</table>
	<?php 
        if(sizeof($promo_codes) == 0){
            ?>
            <label class="col-md-12 col-sm-12 col-lg-12" align="center"> <?php echo trans('user_provider_web.no_result'); ?></label>
            <?php
        }
    ?>
	<div align="left" id="paglink"><?php echo $promo_codes->appends(array(
			'id' => Session::get('id'),
			'coupon' => Session::get('coupon'), 
			'type' => Session::get('type'), 
			'order' => Session::get('order'), 
			'state' => Session::get('state'),
			'order_type' => Session::get('order_type'), 

			))->links(); ?></div>
</div>
	
@stop