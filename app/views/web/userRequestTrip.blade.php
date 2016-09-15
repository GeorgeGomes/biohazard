@extends('web.layout')

@section('content')

<div class="col-md-12 mt">

    @if(Session::has('message'))
    <div class="alert alert-{{ Session::get('type') }}">
        <b>{{ Session::get('message') }}</b> 
    </div>

    @endif
    <div id='msg'></div>   
    <div class="content-panel">
        <div class="row">
            <h4>{{trans("user_provider_web.trip_request");}}</h4><br>
            <div class="col-md-11">
                <div class="row">
                    <div class="col-md-6">
                        <input type="text" class="form-control" name="my_address" id="my-address" placeholder="{{ trans('map.source_address');}}" style="margin-bottom:10px;width:65%;float:left;" onblur="codeAddress(1);">
                        <button id="getCords" class="btn btn-success pull-right" onClick="codeAddress(1);">{{trans('map.find_location');}}</button>

                        <div id="map-canvas" style="width:100%;"></div>
                    </div>

                    <div class="col-md-6" id='destination' style="display:none">
                        <input type="text" class="form-control" name="my_dest" id="my-dest"  placeholder="{{ trans('map.destine_address');}}" style="margin-bottom:10px;width:65%;float:left;" onblur="codeAddress(2);">
                        <button id="getCords" class="btn btn-success pull-right" onClick="codeAddress(2);">{{trans('map.find_location');}}</button>

                        <div id="map-dest" style="width:100%;height:300px;"></div>
                    </div>

                </div>

                <form  id="request-form" style="display:none;">
                    <div class="form-group">
                        <br>
                        <div class="col-sm-12">
                            <label class="col-sm-12 col-sm-12 control-label">{{trans('provider.service_type');}}</label>
                        </div>
                        <br>
                        <select name="type" class="form-control" id="flow4">
                            <option value=''>--{{trans("user_provider_web.select_type");}}--</option>
                            <?php foreach ($types as $type) { ?>
                                <option value="<?= $type->id ?>"><?= $type->name ?></option>
                            <?php } ?>
                        </select>
                        <br>
                        <select id="provider" class="form-control" name='provider' style="display:none">

                        </select>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-12">
                            <label class="col-sm-12 col-sm-12 control-label">{{trans("user_provider_web.like_pay");}}</label>
                        </div>
                        <br>
                        <select name="payment_type" class="form-control" required>
                            <option value="0" <?php
                            if ($payment_option['stored_cards'] == 0) {
                                echo 'hidden';
                            }
                            ?>>{{ trans('provider.card_pay');}}</option>
                            <option value="1" <?php 
                            if ($payment_option['cod'] == 0) {
                                echo 'hidden';
                            }
                            ?>>{{ trans('provider.cash_pay');}}</option>
                            <option value="2" <?php
                            if ($payment_option['paypal'] == 0) {
                                echo 'hidden';
                            }
                            ?>>{{ trans('adminController.paypal');}}</option>
                        </select>
                        <br>
                    </div>
                    <div class="form-group">
                        <?php
                        $promosett = Settings::where('key', 'promo_code')->first();
                        if ($promosett->value == 1) {
                            ?>
                            <input type="text" class="form-control" name="promo_code" id="promo_code" placeholder="Código Promocional">
                        <?php } ?>
                    </div>
                    <input type="hidden" name="latitude" id="latitude">
                    <input type="hidden" name="longitude" id="longitude">
                    <input type="hidden" name="dest" id="destination_req" value='{{$destination}}'>
                    <input type="hidden" name="d_latitude" id="d_latitude">
                    <input type="hidden" name="d_longitude" id="d_longitude">
                    <input type="hidden" name="selection" id="selection" value='{{$selection}}'>
                    <?php if ($destination == 1) { ?>
    <!-- <input type="button" class="btn btn-primary" value="Calculate Estimated Fare" href="{{ route('userrequestFare') }}" id="fare">-->
                    <?php } ?>
                    <input type="submit" class="btn btn-primary" value="{{trans('user_provider_web.trip_request');}}" id="flow5">
                    <br>

                    </div>
                </form>
                <div class="form-group">
                    <div class="col-sm-12" id="farediv" >
                        <label><br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{trans('user_provider_web.trip_price');}}:</label><label id="faretoatal"></label>
                    </div></div>
            </div>
        </div>
    </div>




</div>

<script type="text/javascript">

// provider manual automatic toggle script 

    $(document).ready(function () {
        var latitude = $("#latitude").val();
        var longitude = $("#longitude").val();
        var selection = $('#selection').val();
        var destination = $('#destination_req').val();

        if (destination == 1) {
            $('#destination').show();
        }

        $("#flow4").change(function () {
            initialize_map($("#latitude").val(), $("#longitude").val());
        });
        $("#flow4").change(function () {
            if (selection == '2') {

                var type = $(this).val();

                var dataString = 'longitude=' + $("#longitude").val() + '&latitude=' + $("#latitude").val() + '&type=' + type;
                console.log(dataString)
                $.ajax({
                    type: "POST",
                    url: "<?php echo URL::Route('nearby') ?>",
                    data: dataString,
                    success: function (res) {
                        $('#provider').empty();
                        $('#provider').fadeIn(300);
                        $('#provider').append("<option value=''>--{{trans('user_provider_web.select_provider');}}--</option>");
                        $('#provider').append(res);
                    }
                });
            }
            return false;
        });

        $('#request-form').submit(function () {
            if (selection == '2') {
                var provider_value = $('#provider').val();
                var type_value = $('#flow4').val();
                if (type_value == '' || provider_value == '') {
                    $('#msg').empty();
                    var msg = '<div class="alert alert-danger"><b>{{trans('user_provider_web.select_provider');}}</b></div';
                    $('#msg').append(msg);
                    return false;
                } else {
                    $('#request-form').attr('action', "<?php echo route('manualrequest') ?>");
                    $('#request-form').attr('method', "post");
                    return true;
                }
            }
            if ($('#selection').val() == 1 || $('#selection').val() == '')
            {
                $('#request-form').attr('action', "<?php echo route('userrequesttrips') ?>");
                $('#request-form').attr('method', "post");
                return true;
            }

        });


    });

    $("#farediv").hide();

    $('#fare').click(function () {
        $("#farediv").show();
        //$("#fare").attr("href");
        var url = $("#fare").attr("href");
        var formdata = $("#request-form").serialize();
        console.log(formdata)
        $.ajax(url, {
            data: formdata,
            type: "GET",
            success: function (response) {
                if (response.success)
                {
                    $("#faretoatal").html(response.total)
                }
                else
                {
                    $("#test1").html("{{trans('user_provider_web.something_wrong');}}");
                }
            }
        });

    });

</script>

<script type="text/javascript">

    // destination map script

    function init_map(lati, lngi) {
        var mapOptions = {
            center: {lat: lati, lng: lngi},
            zoom: 16,
            scrollwheel: false,
        };
        var map = new google.maps.Map(document.getElementById('map-dest'),
                mapOptions);
        var myLatlng = new google.maps.LatLng(lati, lngi);
        var marker = new google.maps.Marker({
            position: myLatlng,
            map: map,
            title: '{{trans('map.you');}}',
            animation: google.maps.Animation.DROP,
            draggable: true,
        });

        var infowindow = new google.maps.InfoWindow({
            content: "{{trans('map.select_destine_address');}}"
        });
        google.maps.event.addListener(marker, 'click', function () {
            infowindow.open(map, marker);
            if (marker.getAnimation() != null) {
                marker.setAnimation(null);
            } else {
                marker.setAnimation(google.maps.Animation.BOUNCE);
            }
        });
        infowindow.open(map, marker);



        google.maps.event.addListener(marker, 'dragend', function () {
            // updating the marker position
            var latLng2 = marker.getPosition();
            var geocoder = new google.maps.Geocoder();
            document.getElementById("d_latitude").value = latLng2.lat();
            document.getElementById("d_longitude").value = latLng2.lng();

            var latlngplace = new google.maps.LatLng(latLng2.lat(), latLng2.lng());
            geocoder.geocode({'latLng': latlngplace}, function (results, status) {
                if (status == google.maps.GeocoderStatus.OK) {
                    if (results[1]) {
                        document.getElementById("my-dest").value = results[1].formatted_address;
                    } else {
                        alert('{{ trans('map.address_not_found');}}');
                    }
                } else {
                    alert('{{ trans('map.geo_fail');}}: ' + status);
                }
            });

        });


    }
    google.maps.event.addDomListener(window, 'load', init_map);

</script>

<script>
    // source map script
    var gmarkers = [];

    function initialize_map(lat, lng) {
        $("#request-form").show();
        latitude = parseFloat(lat);
        longitude = parseFloat(lng);
        var marker_icon = '<?php echo asset_url() . "/web/images/map_uberx.png"; ?>';

        var myLatlng = new google.maps.LatLng(latitude, longitude);
        var mapOptions = {
            zoom: 16,
            center: myLatlng,
            scrollwheel: false,
        }

        var map = new google.maps.Map(document.getElementById('map-canvas'), mapOptions);


        var marker_you = new google.maps.Marker({
            position: myLatlng,
            map: map,
            title: 'Você!',
            draggable: true,
        });


        var latitude = $("#latitude").val();
        var longitude = $("#longitude").val();
        var data = 'longitude=' + lng + '&latitude=' + lat + '&type=' + $("#flow4").val();

        $.ajax({
            type: "GET",
            url: "<?php echo URL::Route('/find') ?>",
            data: data,
            success: function (response) {


                if (response.success) {
                    for (i = 0; i < response.inc; i++) {
                        var marker_ll = new google.maps.LatLng(response.provider[i][2], response.provider[i][3]);
                        var result = new google.maps.Marker({
                            position: marker_ll,
                            map: map,
                            icon: marker_icon,
                            title: response.provider[i][1],
                        });
                        gmarkers.push(result);
                    }
                }
            }
        });


        google.maps.event.addListener(marker_you, 'dragend', function () {
            // updating the marker position
            var latLng = marker_you.getPosition();
            var geocoder = new google.maps.Geocoder();
            document.getElementById("latitude").value = latLng.lat();
            document.getElementById("longitude").value = latLng.lng();


            $("#flow4").trigger("change");

            for (var i = 0; i < gmarkers.length; i++) {
                gmarkers[i].setMap(null);
            }

            var latitude = latLng.lat();
            var longitude = latLng.lng();
            gmarkers = [];

            var data = 'longitude=' + longitude + '&latitude=' + latitude + '&type=' + $("#flow4").val();

            $.ajax({
                type: "GET",
                url: "<?php echo URL::Route('/find') ?>",
                data: data,
                success: function (response) {

                    if (response.success) {

                        for (i = 0; i < response.inc; i++) {
                            var marker_ll = new google.maps.LatLng(response.provider[i][2], response.provider[i][3]);
                            var result = new google.maps.Marker({
                                position: marker_ll,
                                map: map,
                                icon: marker_icon,
                                title: response.provider[i][1],
                            });
                            gmarkers.push(result);

                        }
                    }
                }
            });

            var latlngplace = new google.maps.LatLng(latLng.lat(), latLng.lng());
            geocoder.geocode({'latLng': latlngplace}, function (results, status) {
                if (status == google.maps.GeocoderStatus.OK) {
                    if (results[1]) {
                        document.getElementById("my-address").value = results[1].formatted_address;
                    } else {
                        alert('{{ trans('map.address_not_found');}}');
                    }
                } else {
                    alert('{{ trans('map.geo_fail');}}: ' + status);
                }
            });

        });

    }

</script>


<!--script for this page-->
<script type="text/javascript">
    var tour = new Tour(
            {
                name: "userapprequest",
            });

    // Add your steps. Not too many, you don't really want to get your users sleepy
    tour.addSteps([
        {
            element: "#flow2",
            title: "{{trans('user_provider_web.choose_address');}}",
            content: "{{trans('user_provider_web.choose_address_message');}}",
        },
        {
            element: "#flow3",
            title: "{{trans('user_provider_web.adjust_location');}}",
            content: "{{trans('user_provider_web.adjust_location_message');}}"
        },
        {
            element: "#flow4",
            title: "{{trans('user_provider_web.choose_service');}}",
            content: "{{trans('user_provider_web.choose_service_message');}}"
        },
        {
            element: "#flow5",
            title: "{{trans('user_provider_web.make_request');}}",
            content: "{{trans('user_provider_web.make_request_message');}}",
        },
    ]);
    // Initialize the tour
    tour.init();

    // Start the tour
    tour.start();
</script>



@stop 