@extends('web.layout')

@section('content')

<div class="col-md-12 mt">

    @if(Session::has('message'))
    <div class="alert alert-{{ Session::get('type') }}">
        <b>{{ Session::get('message') }}</b> 
    </div>
    @endif

    @if(Session::has('status') && Session::get('status') == 5)
    <div class="alert alert-success">
        <b>{{trans("user_provider_web.request_completed_provider");}}</b>
    </div>
    @endif

    @if(Session::has('status') && Session::get('status') == 1)
    <div class="alert alert-success">
        <b>{{trans("user_provider_web.provider_accept_request");}}</b> 
    </div>
    @endif

    @if(isset($message))
    <div class="alert alert-success">
        <b>{{$message}}</b> 
    </div>
    @endif
    <div class="content-panel" style="min-height:600px;">
        <h4>{{trans('user_provider_web.trip_status');}}</h4><br>
        <div class="col-md-6">
            <div class="col-md-8">
                <br>
                <div id="map-canvas"></div>
            </div>
        </div>
        <div  class="col-md-6">

            <div class="col-md-12">
                <div  class="col-md-12">
                    <h3>{{trans('adminController.id');}} #<?= $request->id ?></h3>
                </div>
                <?php if (isset($request->confirmed_provider) && $request->confirmed_provider != 0) { ?>
                    @if($status == 4 && $eta_value == 1)

                    <div class="col-lg-12" >
                        <label id="showeta"></label>
                        <br>
                        <input type="button" class="btn btn-primary" value="{{trans('user_provider_web.view_my_eta');}}" href="{{ route('userrequestETA') }}" id="showetabutton" style="float:left">

                        <a href="javascript:void(0)" id="eta"><button class="btn btn-primary" style="float:right" ><i class="fa fa-share-alt"></i> {{trans('user_provider_web.share_eta');}}</button></a>
                    </div>
                    @endif
                    <input type="hidden" id="request_id" value="{{$request->id}}">
                    <div  class="col-md-12">
                        <div class="col-lg-12" style="height:50px;postion:relative;top:30px;">
                            <b>{{trans('user_provider_web.provider_profile');}}</b>
                        </div>

                        <div class="col-lg-2">
                            <p><a href="#"><img src="<?php if ($provider->picture != '') {
                    echo $provider->picture;
                } else {
                    echo asset_url() . '/web/default_profile.png';
                } ?>" class="img-circle" width="50"></a></p>
                        </div>
                        <div class="col-lg-8">
                            <div class="col-lg-12">
                                <b>{{ isset($provider->first_name)?$provider->first_name:'' }} {{ isset($provider->last_name)?$provider->last_name:'' }}</b>
                            </div>
                            <div class="col-lg-12">
                                @for ($i = 1; $i <= $rating; $i++)
                                <span><img src="{{ asset_url() }}/web/star.png"></span>
                                @endfor

                            </div>

                        </div>              

                        <div class="col-lg-12">
                            <form id="eta_form" style='display:none; padding-top:20px;' method="post" action="<?php echo URL::Route('etaweb') ?>">
                                <input class="col-sm-12 form-control" name="mail_ids" id="mail_ids" required placeholder="{{trans('user_provider_web.enter_mail_comma');}}">
                                <input type="hidden" name="request_id" value="{{ Session::get('request_id') }}">
                                <input type="hidden" name="duration" id='duration'>
                                <input type="hidden" name="destination" id='destination' value="<?php if ($request != '') {
                    echo $request->D_latitude . ',' . $request->D_longitude;
                } else {
                    echo '';
                } ?>">
                                <input type="hidden" name="source" id='source' value="<?php if ($provider_detail != '') {
                    echo $provider_detail->latitude . ',' . $provider_detail->longitude;
                } else {
                    echo '';
                } ?>">
                                <input type="Submit" class="btn btn-warning" value="{{trans('user_provider_web.share_detail');}}" style="margin-top:20px;">
                            </form>
                        </div>

                    </div>
<?php } ?>

<?php if (Session::has('status') && Session::get('status') == 5 && $payment_mode == 2 && $request->payment_id == NULL) { ?>
                    <div class="col-lg-7">  
                        <a href="{{route('user/paybypalweb',Session::get('request_id'))}}" data-paypal-button="true"><img src="//www.paypalobjects.com/en_US/i/btn/btn_paynow_LG.gif" alt="Pay Now" /></a>
                    </div>
<?php } elseif (Session::has('status') && Session::get('status') == 5) { ?>
    <?php if (Session::get('skipReview') == NULL) { ?>
                        <div class="col-lg-12" id="review">
                            <h3>{{trans('user_provider_web.Leave_your_Review');}}</h3>
                            <form method="post" action="{{route('/user/post-review')}}">
                                <input type="hidden" name="request_id" value="{{ Session::get('request_id') }}">
                                <div class="col-lg-7">
                                    <select class="form-control" name="rating">
                                        <option value="5">5 {{trans("user_provider_web.star");}}s</option>
                                        <option value="4">4 {{trans("user_provider_web.star");}}s</option>
                                        <option value="3">3 {{trans("user_provider_web.star");}}s</option>
                                        <option value="2">2 {{trans("user_provider_web.star");}}s</option>
                                        <option value="1">1 {{trans("user_provider_web.star");}}</option>
                                    </select>
                                    <br>
                                    <textarea class="form-control" rows="5" name="review"></textarea>
                                    <br>
                                    <input type="Submit" class="btn btn-primary" value="{{trans('user_provider_web.submit_review');}}" id="flow7">
                                    <a href="{{route('userSkipReview',Session::get('request_id'))}}"><span class="btn btn-default">{{trans('user_provider_web.skip');}}</span></a>

                                </div>
                            </form>
                        </div>
    <?php } ?>
<?php } ?>

                @if(Session::has('status') && Session::get('status') == 0)
                <div class="col-lg-12" >
                    <br><a href="{{route('/user/trip/cancel',$request->id)}}"><button class="btn btn-primary" id="flow6">"{{trans('user_provider_web.cancel_trip');}}"</button></a>
                </div>
                @endif

            </div>

        </div>
    </div>

</div>
<style>
    #map-canvas {
        height: 300px;
        width: 500px;
        margin: 0px;
        padding: 0px
    }
</style>

<script type="text/javascript">
    $(document).ready(function () {
        console.log("status: " + <?php echo $status ?>);

        $('#showetabutton').click(function () {
            //$("#fare").attr("href");
            var url = $("#showetabutton").attr("href");
            var formdata = '';
            $.ajax(url, {
                data: formdata,
                type: "GET",
                success: function (response) {
                    if (response.success)
                    {
                        $("#showeta").html(response.eta)
                    }
                    else
                    {
                        $("#showeta").html("{{trans('user_provider_web.something_wrong');}}");
                    }
                }
            });
        });

        $("#skip-btn").click(function () {
            $("#review").fadeOut
        });
    });
</script>

<?php 
$mapsKey = Settings::where('key', 'google_maps_api_key')->first();
$link = "https://maps.googleapis.com/maps/api/js?key=" . $mapsKey->value . "&v=3.exp&libraries=places";
?>
<script src=<?php echo $link?>></script>
<script type="text/javascript">
     function initialize_map(lat, lng) {

         latitude = parseFloat(lat);
         longitude = parseFloat(lng);
         var myLatlng = new google.maps.LatLng(latitude, longitude);
         var mapOptions = {
             zoom: 16,
             center: myLatlng
         }
         var map = new google.maps.Map(document.getElementById('map-canvas'), mapOptions);

         var marker = new google.maps.Marker({
             position: myLatlng,
             map: map,
             title: '{{trans('user_provider_web.hello_world');}}',
             draggable: false,
         });


     }

</script>

<script type="text/javascript">
    initialize_map(<?= $user->latitude ?>,<?= $user->longitude ?>);

    $(document).ready(function () {
        $("#eta").click(function () {
            $("#eta_form").toggle(300);
        });
        // getting estimated time from google
        var directionsService = new google.maps.DirectionsService();

        var request = {
            origin: "<?php if ($request != '') {
    echo $request->D_latitude . ',' . $request->D_longitude;
} else {
    echo '';
} ?>",
            destination: "<?php echo $request->D_latitude . ',' . $request->D_longitude; ?>",
            travelMode: google.maps.DirectionsTravelMode.DRIVING
        };

        directionsService.route(request, function (response, status) {
            //Check if status Ok
            if (status == google.maps.GeocoderStatus.OK) {
                //Distance will be in response.routes[0].legs[0].distance.text
                // console.log(response)
                $('#duration').val(response.routes[0].legs[0].duration.text);
            }
            $('#duration').val('Undefined');
        });
    });
</script>

<script type="text/javascript">
    var tour = new Tour(
            {
                name: "userapprequeststatus",
            });

    // Add your steps. Not too many, you don't really want to get your users sleepy
    tour.addSteps([
        {
            element: "#flow6",
            title: "{{trans('user_provider_web.cancel_trip');}}",
            content: "{{trans('user_provider_web.next_step_message');}}"
        },
        {
            element: "#flow7",
            title: "{{trans('user_provider_web.leave_review');}}",
            content: "{{trans('user_provider_web.leave_review_message');}}"
        },
    ]);

    // Initialize the tour
    tour.init();

    // Start the tour
    tour.start();
</script>

@stop 