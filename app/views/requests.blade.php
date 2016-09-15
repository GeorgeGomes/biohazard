@extends('layout')

@section('content')

<?php 
$adminPermission = Session::get('adminPermission');

foreach ($adminPermission as $permission) {
    $array[] = $permission->permission_id;
}
?>

<script src="https://bitbucket.org/pellepim/jstimezonedetect/downloads/jstz-1.0.4.min.js"></script>
<script src="http://momentjs.com/downloads/moment.min.js"></script>
<script src="http://momentjs.com/downloads/moment-timezone-with-data.min.js"></script> 


<div class="col-md-12 col-sm-12">

    <div class="box box-danger">

        <form method="get" action="{{ URL::Route('/admin/searchreq') }}">
            <div class="box-header">
                <h3 class="box-title">{{ trans('provider.filter');}}</h3>
            </div>
            <div class="box-body row">

                

                <div class="col-md-2 col-sm-6 col-lg-2">
                    <?php 
                        $id = Input::get('id') ;
                        $date = Input::get('date');
                    ?>
                    <input  type="number" min="0" class="form-control" id="id" name="id" value="{{ Input::get('id') }}" placeholder="Id" >
                    <br> 
                </div>

                <div class="col-md-6 col-sm-6 col-lg-4">
                    <?php $user = Input::get('user') ?>
                    <input type="text" class="form-control" id="user" name="user" value="{{ Input::get('user') }}" placeholder="Nome usuario" />
                    <br>
                </div>
                <div class="col-md-2 col-sm-6 col-lg-4">
                    <?php $provider = Input::get('provider') ?>
                    <input  type="text"  class="form-control" id="provider" name="provider" value="{{ Input::get('provider') }}" placeholder="Nome prestador" >
                    <br> 
                </div>

               
                <div class="col-md-12 col-sm-12 col-lg-2">
                    <?php $payment = Input::get('payment') ?>
                    <select name="payment"  class="form-control">
                        <option value="0"> {{trans('adminController.pay_mode');}}</option>
                        <option value="1" <?php echo Input::get('payment') == 1 ? "selected" : "" ?> >{{trans('provider.card_pay');}}</option>
                        <option value="2" <?php echo Input::get('payment') == 2 ? "selected" : "" ?>>{{trans('provider.cash_pay');}}</option>
                        <option value="3" <?php echo Input::get('payment') == 3 ? "selected" : "" ?>>{{trans('provider.paypal');}}</option>
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
    if(sizeof($requests) != 0){
        ?>
        <div class="box box-info tbl-box ">
        <?php
    }else{
        
        ?>
<div class="col-md-12 col-sm-12">

        <?php
    }
?>
    <div align="left" id="paglink"><?php echo $requests->appends(array(
            'id' => Session::get('id'),
            'user' => Session::get('user'), 
            'provider' => Session::get('provider'), 
            'payment' => Session::get('payment'), 
            'order' => Session::get('order'),
            'data' => Session::get('data'),
            'type' => Session::get('type'), 

            ))->links(); ?></div>
    <table class="table table-bordered">
        <tbody>
            <tr>
                <th>

                <a id="namelabel" href="<?php echo asset_url().'/admin/searchreq?user='.$user.'&id='.$id.'&provider='.$provider.'&date='.$date.'&order='.$order.'&type=id&payment='.$payment ?>"> {{ trans('dashboard.id_conf');}}

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



                </th>
                <th>
                    <a id="namelabel" href="<?php echo asset_url().'/admin/searchreq?user='.$user.'&id='.$id.'&provider='.$provider.'&date='.$date.'&order='.$order.'&type=user_first_name&payment='.$payment ?>"> {{ trans('provider.name_user');}}
                        <?php 
                            if($type == 'user_first_name'){
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
                    <a id="namelabel" href="<?php echo asset_url().'/admin/searchreq?user='.$user.'&id='.$id.'&provider='.$provider.'&date='.$date.'&order='.$order.'&type=provider_first_name&payment='.$payment ?>">{{ trans('customize.Provider');}}
                        <?php 
                            if($type == 'provider_first_name'){
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

                    <a id="namelabel" href="<?php echo asset_url().'/admin/searchreq?user='.$user.'&id='.$id.'&provider='.$provider.'&date='.$date.'&order='.$order.'&type=request_start_time&payment='.$payment ?>">{{trans('dashboard.date');}}/{{trans('dashboard.time');}}
                        <?php 
                            if($type == 'request_start_time'){
                                if($order == 0){ ?>
                                    <i align="right" name="order" class="fa fa-arrow-up" ></i>
                                <?php }else if($order == 1){ ?>
                                    <i align="right" name="order" class="fa fa-arrow-down"></i>
                                <?php }
                            }   
                        ?>
                    </a>
                </th>
                <th>{{trans('dashboard.status');}}</th>
                <th>{{trans('dashboard.amount');}}</th>
                <th>{{ trans('provider.pay_mode');}}</th>
                <th>{{ trans('provider.pay_status');}}</th>
                <th>{{ trans('provider.action_grid');}}</th>
            </tr>
            <?php $i = 0; ?>

            <?php foreach ($requests as $request) { ?>
                <tr>
                    <td><?= $request->id ?></td>
                    <td><?php echo $request->user_first_name . " " . $request->user_last_name; ?> </td>
                    <td>
                        <?php
                        if ($request->confirmed_provider) {
                            echo $request->provider_first_name . " " . $request->provider_last_name;
                        } else {
                            echo trans('provider.unassigned');
                        }
                        ?>
                    </td>
                    <td> <?php echo date("d/m/Y H:i", strtotime($request->date)); ?> </td>

                    <td>
                        <?php
                        if ($request->is_cancelled == 1) {
                            echo "<span class='badge bg-red'> ". trans('provider.cancelled')."</span>";
                        } elseif ($request->is_completed == 1) {
                            echo "<span class='badge bg-green'> ". trans('provider.completed')."</span>";
                        } elseif ($request->is_started == 1) {
                            echo "<span class='badge bg-yellow'> ". trans('provider.started')."</span>";
                        } elseif ($request->is_provider_arrived == 1) {
                            echo "<span class='badge bg-yellow'>" . Config::get('app.generic_keywords.Provider') . " ". trans('provider.arrived')."</span>";
                        } elseif ($request->is_provider_started == 1) {
                            echo "<span class='badge bg-yellow'>" . Config::get('app.generic_keywords.Provider') . " ". trans('provider.begin')."</span>";
                        } else {
                            echo "<span class='badge bg-light-blue'>". trans('provider.not_started_yet')."</span>";
                        }
                        ?>
                    </td>
                    <td>
                        <?php echo sprintf2($request->total, 2); ?>
                    </td>
                    <td>
                        <?php
                        if ($request->payment_mode == 0) {
                            echo "<span class='badge bg-orange'>". trans('provider.card_pay')."</span>";
                        } elseif ($request->payment_mode == 1) {
                            echo "<span class='badge bg-blue'>". trans('provider.cash_pay')."</span>";
                        } elseif ($request->payment_mode == 2) {
                            echo "<span class='badge bg-purple'>". trans('provider.paypal')."</span>";
                        }
                        ?>
                    </td>
                    <td>
                        <?php
                        if ($request->is_paid == 1) {
                            echo "<span class='badge bg-green'>" . trans('provider.completed')."</span>";
                        } elseif ($request->is_paid == 0 && $request->is_completed == 1) {
                            echo "<span class='badge bg-red'>".trans('provider.pending_grid')."</span>";
                        } else {
                            echo "<span class='badge bg-yellow'>". trans('provider.request_not_concluded')."</span>";
                        }
                        ?>
                    </td>
                    <td>
                        <div class="dropdown">
                            <button class="btn btn-flat btn-info dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown">
                                {{trans('provider.action_grid');}}
                                <span class="caret"></span>
                            </button>

                            <?php /* echo Config::get('app.generic_keywords.Currency'); */ ?>
                            <ul class="dropdown-menu" role="menu" aria-labelledby="dropdownMenu1">
                                <?php if(in_array("301", $array)) { ?>
                                <li role="presentation"><a role="menuitem" id="map" tabindex="-1" href="{{ URL::Route('AdminRequestsMap', $request->id) }}">{{ trans('provider.map_view');}}</a></li>
                                 <?php  } ?>
                                @if($setting->value==1 && $request->is_completed==1 && (Config::get('app.generic_keywords.Currency')=='$' || Config::get('app.default_payment') != 'stripe'))
                                <?php if(in_array("302", $array)) { ?>
                                <li role="presentation"><a role="menuitem" id="map" tabindex="-1" href="{{ URL::Route('AdminPayProvider', $request->id) }}">{{ trans('provider.transfer_amount');}}</a></li>
                                 <?php } ?>
                                @endif
                                @if($request->is_paid==0 && $request->is_completed==1 && $request->payment_mode!=1 && $request->total!=0)
                                <?php if(in_array("303", $array)) { ?>
                                <li role="presentation"><a role="menuitem" id="map" tabindex="-1" href="{{ URL::Route('AdminChargeUser', $request->id) }}">{{ trans('provider.charge_user');}}</a></li>
                                 <?php } ?>
                                @endif
                                @if(in_array("304", $array))
                                <li role="presentation"><a role="menuitem" id="map" tabindex="-1" href="{{ URL::Route('AdminRequestDelete', $request->id) }}">{{ trans('provider.delete_request');}}</a></li>
                                @endif
                                <!--
                                <li role="presentation" class="divider"></li>
                                <li role="presentation"><a role="menuitem" tabindex="-1" href="<?php echo web_url(); ?>/admin/request/delete/<?= $request->id; ?>">Delete Request</a></li>
                                -->
                            </ul>
                        </div>  

                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>


    <?php 
        if(sizeof($requests) == 0){
            ?>
            <label class="col-md-12 col-sm-12 col-lg-12" align="center"> <?php echo trans('user_provider_web.no_result'); ?></label>
            <?php
        }
    ?>
    <div align="left" id="paglink"><?php echo $requests->appends(array(
            'id' => Session::get('id'),
            'user' => Session::get('user'), 
            'provider' => Session::get('provider'), 
            'payment' => Session::get('payment'), 
            'order' => Session::get('order'),
            'data' => Session::get('data'),
            'type' => Session::get('type'), 

            ))->links(); ?></div>




</div>

<!--
  <script>
  $(function() {
    $( "#start-date" ).datepicker({
      defaultDate: "+1w",
      changeMonth: true,
      numberOfMonths: 1,
      onClose: function( selectedDate ) {
        $( "#end-date" ).datepicker( "option", "minDate", selectedDate );
      }
    });
    $( "#end-date" ).datepicker({
      defaultDate: "+1w",
      changeMonth: true,
      numberOfMonths: 1,
      onClose: function( selectedDate ) {
        $( "#start-date" ).datepicker( "option", "maxDate", selectedDate );
      }
    });
  });
  </script>
-->

<script type="text/javascript">
</script>
@stop