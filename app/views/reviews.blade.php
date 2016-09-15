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

<div class="box box-danger">
    <form method="get" action="{{ URL::Route('/admin/searchrev') }}" >
            <div class="box-header">
                <h3 class="box-title">{{ trans('dashboard.filter'); }}</h3>
            </div>
            <div class="box-body row">
                <div class="col-md-6 col-sm-12">
                    <select id="searchdrop" class="form-control" name="type">
                        <option value="user" id="user">{{ trans('provider.name_user');}}</option>
                        <option value="provider" id="provider">{{ trans('customize.Provider');}}</option>
                    </select>
                    <br>
                </div>
                <div class="col-md-6 col-sm-12">
                    <input class="form-control" type="text" name="valu" value="<?php if(Session::has('valu')){echo Session::get('valu');} ?>" id="insearch" placeholder="{{ trans('provider.key_word');}}"/>
                    <br>
                </div>
            </div>
            <div class="box-footer">
                <button type="submit" id="btnsearch" class="btn btn-flat btn-block btn-success">{{ trans('provider.search');}}</button>
            </div>
    </form>
</div>

<div class="box box-info tbl-box">
    <!-- Custom Tabs -->
    <div class="nav-tabs-custom">
        <ul class="nav nav-tabs">
            <?php if(in_array("501", $array)) { ?>
            <li class="active"><a href="#tab_1" data-toggle="tab" aria-expanded="true">{{trans('reviews.provider_rate');}}</a></li>
            <?php } ?>
            <?php if(in_array("502", $array)) { ?>
            <li class=""><a href="#tab_2" data-toggle="tab" aria-expanded="false">{{trans('reviews.user_rate');}}</a></li>
            <?php } ?>
        </ul>
        <div class="tab-content">
            <div class="tab-pane active" id="tab_1">
                <div align="left" id="paglink"><?php echo $provider_reviews->appends(array('type'=>Session::get('type'), 'valu'=>Session::get('valu')))->links(); ?></div>
                <table class="table table-bordered">
                    <tbody>
                        <tr>
                            <th>{{ trans('provider.name_user');}}</th>
                            <th>{{ trans('customize.Provider');}}</th>
                            <th>{{trans('reviews.rating');}}</th>
                            <th>{{trans('reviews.date_time');}}</th>
                            <th>{{trans('reviews.comment');}}</th>
                            <?php if(in_array("503", $array)) { ?>
                            <th>{{trans('reviews.action');}}</th>
                            <?php } ?>
                        </tr>
                        <?php $i =0; ?>
                        <?php foreach ($provider_reviews as $reviewp) { ?>
                            <tr>
                                <td><?php echo $reviewp->user_first_name." ".$reviewp->user_last_name; ?> </td>
                                <td><?php echo $reviewp->provider_first_name." ".$reviewp->provider_last_name; ?> </td>
                                <td><?= $reviewp->rating ?></td>
                                <td>
                                    <?php 
                                        $format = Settings::getDefaultDateFormat();
                                        $date = new DateTime($reviewp->created_at);
                                        
                                        echo date_format($date, $format);
                                        
                                    ?>
                                </td>
                                
                                <!-- <td id= 'Datetime<?php echo $i; ?>' >
                                <script>
                                var timezone = jstz.determine();
                                 // console.log(timezone.name());
                                var timevar = moment.utc("<?php echo $reviewp->created_at; ?>");
                                timevar.toDate();
                                timevar.tz(timezone.name());
                                // console.log(timevar);
                                document.getElementById("Datetime<?php echo $i; ?>").innerHTML = timevar;
                                <?php  $i++; ?>
                                </script> -->
                                
                                <td><?= $reviewp->comment ?></td>
                                <?php if(in_array("503", $array)) { ?>
                                <td><a href="{{ URL::Route('AdminReviewsDelete', $reviewp->review_id) }}"><input type="button" class="btn btn-success" value="{{trans('reviews.delete');}}"></a></td>
                                <?php } ?>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
                <div align="left" id="paglink"><?php echo $provider_reviews->appends(array('type'=>Session::get('type'), 'valu'=>Session::get('valu')))->links(); ?></div>
            </div><!-- /.tab-pane -->
             <div class="tab-pane" id="tab_2">
                <table class="table table-bordered">
                    <tbody>
                        <tr>
                            <th>{{trans('reviews.provider_name');}}</th>
                            <th>{{ trans('customize.User');}}</th>
                            <th>{{trans('reviews.rating');}}</th>
                            <th>{{trans('reviews.date_time');}}</th>
                            <th>{{trans('reviews.comment');}}</th>
                            <?php if(in_array("503", $array)) { ?>
                            <th>{{trans('reviews.action');}}</th>
                            <?php } ?>
                        </tr>
                        <?php $i =0; ?>
                        <?php foreach ($user_reviews as $reviewu) { ?>
                            <tr>
                                <td><?php echo $reviewu->provider_first_name." ".$reviewu->provider_last_name; ?> </td>
                                <td><?php echo $reviewu->user_first_name." ".$reviewu->user_last_name; ?> </td>
                                <td><?= $reviewu->rating ?></td>
                                <td>
                                    <?php 
                                        $format = Settings::getDefaultDateFormat();
                                        $date = new DateTime($reviewu->created_at);
                                        echo date_format($date, $format);

                                    ?>
                                </td>



                                <!-- <td id= 'time<?php echo $i; ?>' >
                                <script>
                                var timezone = jstz.determine();
                                 // console.log(timezone.name());
                                var timevar = moment.utc("<?php echo $reviewu->created_at; ?>");
                                timevar.toDate();
                                timevar.tz(timezone.name());
                                // console.log(timevar);
                                document.getElementById("time<?php echo $i; ?>").innerHTML = timevar;
                                <?php  $i++; ?>
                                </script> -->

                                <td><?= $reviewu->comment ?></td>
                                <?php if(in_array("503", $array)) { ?>
                                <td><a href="{{ URL::Route('AdminReviewsDeleteUser', $reviewu->review_id) }}"><input type="button" class="btn btn-success" value="{{trans('reviews.delete');}}"></a></td>
                                <?php } ?>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
                <div align="left" id="paglink"><?php echo $provider_reviews->appends(array('type'=>Session::get('type'), 'valu'=>Session::get('valu')))->links(); ?></div>
           </div>
       </div>
   </div>
</div>

@stop