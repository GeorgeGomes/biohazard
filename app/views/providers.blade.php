@extends('layout')

@section('content')

<?php 
$adminPermission = Session::get('adminPermission');
$array_permissions = array() ;
foreach ($adminPermission as $permission) {
    $array_permissions[] = $permission->permission_id;
}
?>
<!--<div class="row">
    <div class="col-md-12 col-sm-12">
        <a id="addpro" href="{{ URL::Route('AdminProviderAdd') }}"><button class="btn btn-flat btn-block btn-info" type="button">Add Provider</button></a>
        <br/>
    </div>
</div>-->
<div class="col-md-12 col-sm-12">
    <div class="box box-danger">
        <form method="get" action="{{ URL::Route('/admin/sortpv') }}">
            <div class="box-header">
                <h3 class="box-title">{{ trans('provider.filter');}}</h3>
            </div>
            <div class="box-body row">
                <div class="col-md-6 col-sm-6 col-lg-2">
                    <?php $id = Input::get('id') ?>
                    <input  type="number" min="0" class="form-control" id="id" name="id" value="{{ Input::get('id') }}" placeholder="Id" >
                    
                </div>

                <div class="col-md-6 col-sm-6 col-lg-4">
                    <?php $name = Input::get('name') ?>
                    <input type="text" class="form-control" id="name" name="name" value="{{ Input::get('name') }}" placeholder="{{trans('providerController.name');}}" >
                   
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
                    <?php $city = Input::get('city') ?>
                    <input type="text" class="form-control" id="city" name="city" value="{{ Input::get('city') }}" placeholder="{{trans('providerController.city');}}" >
                   
                </div>    
            </div>

            <div class="box-body row">

                <div class="col-md-12 col-sm-3 col-lg-3">
                    <?php $status = Input::get('status') ?>
                    <select name="status"  class="form-control" >
                        <option value="0"> Status</option>
                        <option value="APROVADO" <?php echo Input::get('status') == "APROVADO" ? "selected" : "" ?> >{{trans('adminController.approved');}}</option>
                        <option value="REJEITADO" <?php echo Input::get('status') == "REJEITADO" ? "selected" : "" ?>>{{trans('adminController.decline');}}</option>
                        <option value="EM_ANALISE" <?php echo Input::get('status') == "EM_ANALISE" ? "selected" : "" ?> >{{trans('adminController.analyse');}}</option>
                        <option value="SUSPENSO" <?php echo Input::get('status') == "SUSPENSO" ? "selected" : "" ?>>{{trans('adminController.suspend');}}</option>
                        <option value="PENDENTE" <?php echo Input::get('status') == "PENDENTE" ? "selected" : "" ?>>{{trans('adminController.pendent');}}</option>
                        <option value="INATIVO" <?php echo Input::get('status') == "INATIVO" ? "selected" : "" ?>>{{trans('adminController.inactive');}}</option>
                    </select>
                
                </div>

                 <div class="col-md-6 col-sm-3 col-lg-3">
                    <?php $brand = Input::get('brand') ?>
                    <input type="text" class="form-control" id="brand" name="brand" value="{{ Input::get('brand') }}" placeholder="{{trans('providerController.brand_number');}}" > 
                </div>
            </div>
            
            <div class="box-footer" align="right">
                <button type="submit" name="btnsearch" class="btn btn-flat btn-block btn-success " value="Filter_Data">{{trans('provider.search');}}</button>
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

<div class="col-md-12 col-sm-12">
    <?php if (Session::get('che')) { ?>
        <a id="providers" href="{{ URL::Route('AdminProviders') }}"><button class="col-md-12 col-sm-12 btn btn-warning" type="button">{{ trans('provider.all_provider');}}</button></a><br/>
    <?php } else { ?>
        <a id="currently" href="{{ URL::Route('AdminProviderCurrent') }}"><button class="col-md-12 col-sm-12 btn btn-warning"  type="button">{{ trans('provider.on_duty_now');}}</button></a><br/>
    <?php } ?>
    <br><br>
</div>
<?php 
    if(sizeof($providers) != 0){
        ?>
        <div class="box box-info tbl-box ">
        <?php
    }else{
        
        ?>
<div class="col-md-12 col-sm-12">

        <?php
    }
?>

    <div align="left" id="paglink"><?php echo $providers->appends(array(
    'id' => Session::get('id'),
    'name' => Session::get('name'), 
    'email' => Session::get('email'),
    'city' => Session::get('city'),
    'status' => Session::get('status'),  
    'type' => Session::get('type'), 
    'order' => Session::get('order'), 
    'state' => Session::get('state'), 
    'brand' => Session::get('brand'), 

    ))->links(); ?></div>
    <table class="table table-bordered">
        <tbody><tr>
                <th>

                    <div  title="<?php echo trans('provider.id'); ?>" >
                        <a id="idlabel"  href="<?php echo asset_url().'/admin/sortpv?name='.$name.'&brand='.$brand.'&id='.$id.'&status='.$status.'&state='.$state.'&city='.$city.'&email='.$email.'&order='.$order.'&type=id' ?>"> {{ trans('map.id');}} 

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
                    <div  title="<?php echo trans('provider.name'); ?>" >
                        <a id="namelabel" href="<?php echo asset_url().'/admin/sortpv?name='.$name.'&brand='.$brand.'&id='.$id.'&status='.$status.'&state='.$state.'&city='.$city.'&email='.$email.'&order='.$order.'&type=first_name' ?>"> {{ trans('provider.name_grid');}} 

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

                    <div  title="<?php echo trans('provider.email'); ?>" >
                        <a id="emaillabel"  href="<?php echo asset_url().'/admin/sortpv?name='.$name.'&brand='.$brand.'&id='.$id.'&status='.$status.'&state='.$state.'&city='.$city.'&email='.$email.'&order='.$order.'&type=email' ?>"> {{ trans('provider.mail_grid');}} 

                            <?php 
                                if($type == 'email'){
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
                <th>{{ trans('provider.phone_grid');}}</th>
                <th>{{ trans('provider.picture_grid');}}</th>
                <th>{{ trans('provider.location');}}</th>
                <th>{{ trans('provider.total_request_grid');}}</th>
                <th>{{ trans('provider.accept_rate_grid');}}</th>
                <th>{{ trans('provider.status_grid');}}</th>
                <th>{{ trans('provider.action_grid');}}</th>
            </tr>
            @foreach ($providers as $provider)
                <tr>
                    <td><?= $provider->id ?>  



                    </td>
                    <td><?php echo $provider->first_name . " " . $provider->last_name; ?> </td>
                    <td><?= $provider->email ?></td>
                    <td><?= $provider->phone ?></td>
                    <td><a href="<?php echo $provider->picture; ?> target="_blank" onclick="window.open('<?php echo $provider->picture; ?>', 'popup', 'height=500px, width=400px'); return false;"">{{ trans('provider.view_photo_grid');}}</a></td>
                    <td>{{ $provider->getLocation() }}</td>
                    <td><?= $provider->total_requests ?></td>
                    <td><?php
                        if ($provider->total_requests != 0) {
                            echo round(($provider->accepted_requests / $provider->total_requests) * 100, 2);
                        } else {
                            
                        }
                        ?> %</td>
                    <td>
                        @if ($provider->status_name && strcmp($provider->status_name, "APROVADO") == 0 )
                            <span class='label label-success'>{{ trans('provider.approved_grid'); }}</span>
                        @elseif ($provider->status_name && strcmp($provider->status_name, "REJEITADO") == 0)
                            <span class='label label-danger'>{{ trans('provider.rejected_grid'); }}</span>
                        @elseif ($provider->status_name && strcmp($provider->status_name, "EM_ANALISE") == 0)
                            <span class='label label-warning'>{{ trans('provider.analysis_grid'); }}</span>
                        @elseif ($provider->status_name && strcmp($provider->status_name, "PENDENTE") == 0)
                            <span class='label label-primary'>{{ trans('provider.pending_grid'); }}</span>
                        @elseif ($provider->status_name && strcmp($provider->status_name, "INATIVO") == 0)
                            <span class='label label-danger'>{{ trans('provider.inactive_grid'); }}</span>
                        @else
                            <span class='label label-default'>{{ trans('provider.suspended_grid'); }}</span>
                        @endif

                    </td>
                    <td>
                        <div class="dropdown">
                            <button class="btn btn-flat btn-info dropdown-toggle" type="button" id="dropdownMenu1" name="action" data-toggle="dropdown">
                                {{trans('provider.action_grid');}}
                                <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu" role="menu" aria-labelledby="dropdownMenu1">

                                <!-- EDITAR -->
                                @if(in_array("201", $array_permissions))                                     
                                    <li role="presentation">
                                        <a role="menuitem" tabindex="-1" href="{{ URL::Route('AdminProviderEdit', $provider->id) }}">{{trans('provider.edit_detail_grid');}}</a>
                                    </li>
                                @endif

                                <!-- HISTORICO  -->
                                @if(in_array("208", $array_permissions))
                                    <li role="presentation">
                                        <a role="menuitem" id="history" tabindex="-1" href="{{ URL::Route('AdminProviderHistory', $provider->id) }}">{{trans('provider.view_history_grid');}}</a>
                                    </li>
                                @endif

                                <!-- ALTERAÇÔES DE STATUS -->
                                @if ($provider->status_name != "APROVADO")
                                    @if (in_array("209", $array_permissions))
                                        <li role="presentation">
                                            <a role="menuitem" id="approve" tabindex="-1" href="{{ URL::Route('AdminProviderChangeStatus', array('APROVADO', $provider->id)) }}" onclick="return confirm('{{ trans('provider.approve_message') . ' ' . $provider->first_name . ' ' . $provider->last_name . '?'; }}')">{{trans('provider.approve_grid');}}</a>
                                        </li>
                                    @endif
                                @endif

                                @if ($provider->status_name != "REJEITADO")
                                    @if (in_array("203", $array_permissions))
                                        <li role="presentation">
                                            <a role="menuitem" id="decline" tabindex="-1" href="{{ URL::Route('AdminProviderChangeStatus', array('REJEITADO', $provider->id)) }}" onclick="return confirm('{{ trans('provider.decline_message') . ' ' . $provider->first_name . ' ' . $provider->last_name . '?'; }}')">{{trans('provider.decline_grid');}}</a>
                                        </li>
                                    @endif
                                @endif

                                @if ($provider->status_name != "EM_ANALISE")
                                    @if (in_array("204", $array_permissions))
                                        <li role="presentation">
                                            <a role="menuitem" id="analyse" tabindex="-1" href="{{ URL::Route('AdminProviderChangeStatus', array('EM_ANALISE', $provider->id)) }}" onclick="return confirm('{{ trans('provider.analyse_message') . ' ' . $provider->first_name . ' ' . $provider->last_name . '?'; }}')">{{trans('provider.analyse_grid');}}</a>
                                        </li>
                                    @endif
                                @endif

                                @if ($provider->status_name != "SUSPENSO")
                                    @if (in_array("205", $array_permissions))
                                        <li role="presentation">
                                            <a role="menuitem" id="suspend" tabindex="-1" href="{{ URL::Route('AdminProviderChangeStatus', array('SUSPENSO', $provider->id)) }}" onclick="return confirm('{{ trans('provider.suspend_message') . ' ' . $provider->first_name . ' ' . $provider->last_name . '?'; }}')">{{trans('provider.suspend_grid');}}</a>
                                        </li>
                                    @endif
                                @endif

                                @if ($provider->status_name != "PENDENTE")
                                    @if (in_array("210", $array_permissions))
                                        <li role="presentation">
                                            <a role="menuitem" id="pendent" tabindex="-1" href="{{ URL::Route('AdminProviderChangeStatus', array('PENDENTE', $provider->id)) }}" onclick="return confirm('{{ trans('provider.pendent_message') . ' ' . $provider->first_name . ' ' . $provider->last_name . '?'; }}')">{{trans('provider.set_pending_grid');}}</a>
                                        </li>
                                    @endif
                                @endif

                                @if ($provider->status_name != "INATIVO")
                                    @if (in_array("211", $array_permissions))
                                        <li role="presentation">
                                            <a role="menuitem" id="deactivate" tabindex="-1" href="{{ URL::Route('AdminProviderChangeStatus', array('INATIVO', $provider->id)) }}" onclick="return confirm('{{ trans('provider.inactive_message') . ' ' . $provider->first_name . ' ' . $provider->last_name . '?'; }}')">{{trans('provider.deactivate_grid');}}</a>
                                        </li>
                                    @endif
                                @endif

                                <!-- DELETAR -->
                                @if (in_array("206", $array_permissions))
                                    <li role="presentation">
                                        <a role="menuitem" id="delete" tabindex="-1" href="{{ URL::Route('AdminProviderDelete', $provider->id) }}" onclick="return confirm('{{ trans('provider.delete_message') . ' ' . $provider->first_name . ' ' . $provider->last_name . '?'; }}')">{{trans('provider.delete_grid');}}</a>
                                    </li>
                                @endif


                                <?php if(in_array("202", $array_permissions)) { ?>
                                <?php
                                $provider_doc = ProviderDocument::where('provider_id', $provider->id)->first();
                                if ($provider_doc != NULL) {
                                    ?>
                                    <li role="presentation"><a id="view_provider_doc" role="menuitem" tabindex="-1" href="{{ URL::Route('AdminProviderDocuments', $provider->id) }}">{{trans('provider.view_documents_grid');}}</a></li>
                                <?php } else { ?>
                                    <li role="presentation"><a id="view_provider_doc" role="menuitem" tabindex="-1" href="{{ URL::Route('AdminProviderDocuments', $provider->id) }}"><span class='badge bg-red'>{{trans('provider.no_documents_grid');}}</span></a></li>
                                <?php } ?>
                                <?php } ?>
                                <!--<li role="presentation"><a role="menuitem" id="history" tabindex="-1" href="{{ web_url().'/admin/provider/documents/'.$provider->id }}">View Documents</a></li>-->
                            </ul>
                        </div>
                    </td>
                </tr>


            @endforeach
        </tbody>
        

        </table>
        <?php 
            if(sizeof($providers) == 0){
                ?>
                <label class="col-md-12 col-sm-12 col-lg-12" align="center"> <?php echo trans('user_provider_web.no_result'); ?></label>
                <?php
            }
        ?>


       
    <div align="left" id="paglink"><?php echo $providers->appends(array(
    'id' => Session::get('id'),
    'name' => Session::get('name'), 
    'email' => Session::get('email'),
    'city' => Session::get('city'),
    'status' => Session::get('status'),  
    'type' => Session::get('type'), 
    'order' => Session::get('order'), 
    'state' => Session::get('state'), 
    'brand' => Session::get('brand'), 

    ))->links(); ?></div>
</div>

@stop