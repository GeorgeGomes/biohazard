@extends('layout')

@section('content')


                        <div class="row">
                        <div class="col-lg-6 col-xs-6">
                            <!-- small box -->
                            <div class="small-box bg-aqua">
                                <div class="inner">
                                    <h3>
                                       {{ $ledger?$ledger->total_referrals:0 }}
                                    </h3>
                                    <p>
                                        {{trans('user.total_referrals')}}
                                    </p>
                                </div>
                                <div class="icon">
                                    {{$ledger?$ledger->referral_code:0}}
                                </div>
                              
                            </div>
                        </div><!-- ./col -->
                        <div class="col-lg-6 col-xs-6">
                            <!-- small box -->
                            <div class="small-box bg-yellow">
                                <div class="inner">
                                    <h3>
                                       {{ $ledger?round($ledger->amount_earned):0 }}
                                    </h3>
                                    <p>
                                        {{trans('user.credit_earned')}}
                                    </p>
                                </div>
                                <div class="icon">
                                    <i class="ion ion-cash"></i>
                                </div>
                                
                            </div>
                        </div><!-- ./col -->
                        <div class="col-lg-6 col-xs-6">
                            <!-- small box -->
                            <div class="small-box bg-red">
                                <div class="inner">
                                    <h3>
                                        {{ $ledger?round($ledger->amount_spent):0 }}
                                    </h3>
                                    <p>
                                       {{trans('user.credit_spent')}}
                                    </p>
                                </div>
                                <div class="icon">
                                    <i class="ion ion-battery-low"></i>
                                </div>
                                
                            </div>
                        </div><!-- ./col -->
                        <div class="col-lg-6 col-xs-6">
                            <!-- small box -->
                            <div class="small-box bg-purple">
                                <div class="inner">
                                    <h3>
                                        {{ $ledger?round($ledger->amount_earned - $ledger->amount_spent):0 }}
                                    </h3>
                                    <p>
                                       {{trans('user.credit_balance')}}
                                    </p>
                                </div>
                                <div class="icon">
                                    <i class="ion ion-pie-graph"></i>
                                </div>
                                
                            </div>
                        </div><!-- ./col -->
      
                    </div>




                    <div class="col-md-6 col-sm-12">

                    <div class="box box-danger">

                        <form method="get" action="{{ URL::Route('/admin/sortur') }}">
                                <div class="box-header">
                                    <h3 class="box-title">{{ trans('provider.sort');}}</h3>
                                </div>
                                <div class="box-body row">

                                <div class="col-md-6 col-sm-12">
                                    <select class="form-control" id="searchdrop" name="type">

                                    <option value="userid" <?php if(isset($_GET['type']) && $_GET['type']=='userid') {echo 'selected="selected"';}?> id="provid">{{ trans('user.id');}}</option>
                                    <option value="username" <?php if(isset($_GET['type']) && $_GET['type']=='username') {echo 'selected="selected"';}?> id="pvname">{{ trans('provider.name_user');}}</option>
                                    <option value="useremail" <?php if(isset($_GET['type']) && $_GET['type']=='useremail') {echo 'selected="selected"';}?> id="pvemail">{{ trans('user.mail');}}</option>
                                </select>
                                    <br>
                                </div>
                                <div class="col-md-6 col-sm-12">
                                    <select class="form-control" id="searchdroporder" name="valu">
                                    <option value="asc" <?php if(isset($_GET['valu']) && $_GET['valu']=='asc') {echo 'selected="selected"';}?> id="asc">{{ trans('provider.asc');}}</option>
                                    <option value="desc" <?php if(isset($_GET['valu']) && $_GET['valu']=='desc') {echo 'selected="selected"';}?> id="desc">{{ trans('provider.desc');}}</option>
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

                       <form method="get" action="{{ URL::Route('/admin/searchur') }}">
                                <div class="box-header">
                                    <h3 class="box-title">{{ trans('dashboard.filter'); }}</h3>
                                </div>
                                <div class="box-body row">

                                <div class="col-md-6 col-sm-12">

                                <select class="form-control" id="searchdrop" name="type">
                                  <option value="userid" id="userid">{{ trans('user.id');}}</option>
                                  <option value="username" id="username">{{ trans('provider.name_user');}}</option>
                                  <option value="useremail" id="useremail">{{ trans('user.mail');}}</option>
                                  <option value="useraddress" id="useraddress">{{trans('user.address');}}</option>
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
                <div align="left" id="paglink"><?php echo $users->appends(array('type'=>Session::get('type'), 'valu'=>Session::get('valu')))->links(); ?></div>
                <table class="table table-bordered">
                                <tbody>
                                        <tr>
                                            <th>{{trans('map.id');}}</th>
                                            <th>{{ trans('provider.name_grid');}}</th>
                                            <th>{{ trans('provider.mail_grid');}}</th>
                                            <th>{{ trans('provider.phone_grid');}}</th>
                                            <th>{{trans('provider.address');}}</th>
                                            <th>{{trans('provider.state');}}</th>
                                            <th>{{trans('provider.zipcode');}}</th>

                                        </tr>
                                     <?php foreach ($users as $user) { ?>
                                    <tr>
                                        <td><?= $user->id ?></td>
                                        <td><?php echo $user->first_name." ".$user->last_name; ?> </td>
                                        <td><?= $user->email ?></td>
                                        <td><?= $user->phone ?></td>
                                        <td><?= $user->address ?></td>
                                        <td><?= $user->state ?></td>
                                        <td><?= $user->zipcode ?></td>
                                        </tr>
                                    <?php } ?>
                    </tbody>
                </table>

                <div align="left" id="paglink"><?php echo $users->appends(array('type'=>Session::get('type'), 'valu'=>Session::get('valu')))->links(); ?></div>
                </div>




@stop