@extends('web.layout')
@section('content')
<div class="col-md-12 mt">
    @if(Session::has('message'))
    <!--<div class="alert alert-{{ Session::get('type') }}">
        <b>{{ Session::get('message') }}</b> 
    </div>-->
    @endif

    <div class="content-panel">
        <table class="table table-hover" id="trip-table">
            <thead>
                <tr>
                    <th>{{trans('user_provider_web.date_schedule');}}</th>
                    <th>{{trans('user_provider_web.time_schedule');}}</th>
                    <th>{{trans('user_provider_web.timezone_schedule');}}</th>
                    <th>{{trans('user_provider_web.source_address');}}</th>
                    <th>{{trans('user_provider_web.destine_address');}}</th>
                    <th>{{trans('user_provider_web.promo_code');}}</th>
                    <th>{{ trans('provider.pay_mode');}}</th>
                    <th>{{ trans('provider.action_grid');}}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($requests as $request)
                <tr class="trip-basic" data-id="{{ route('/user/trip',$request->id)}}">
                    <td>{{ date("d M Y", strtotime($request->start_time)) }}</td>
                    <td>{{ date("g:iA", strtotime($request->start_time)) }}</td>
                    <td>{{ $request->time_zone }}</td>
                    <td>{{ $request->src_address }}</td>
                    <td>{{ $request->dest_address }}</td>
                    <td>
                        <?php
                        if ($request->promo_code == "" || $request->promo_code == NULL) {
                            echo "<span class='badge bg-red'>" . Config::get('app.blank_fiend_val') . "</span>";
                        } else {
                            echo $request->promo_code;
                        }
                        ?>
                    </td>
                    <td>
                        <?php
                        if ($request->payment_mode == 0) {
                            echo "<span class='badge bg-orange'>". trans('provider.card_pay');."</span>";
                        } elseif ($request->payment_mode == 1) {
                            echo "<span class='badge bg-blue'>". trans('provider.cash_pay');."</span>";
                        } elseif ($request->payment_mode == 2) {
                            echo "<span class='badge bg-purple'>". trans('provider.paypal');."</span>";
                        }
                        ?>
                    </td>
                    <td>
                        <a href="{{route('/user/deletescheduledtrips')."?id=". $request->id}}" class="btn btn-info">{{trans('provider.delete_grid');}}</a>
                    </td>
                </tr>
                <tr class="trip-detail" style="display:none;">
                    <td colspan="4"><center>{{trans('user_provider_web.Loading');}}...</center></td>
            </tr>
            @endforeach

            </tbody>
        </table>
    </div>
</div>

<!--script for this page-->
<script type="text/javascript">
    var tour = new Tour(
            {
                name: "userappHome",
            });

    // Add your steps. Not too many, you don't really want to get your users sleepy
    tour.addSteps([
        {
            element: "#flow1",
            title: "{{trans('user_provider_web.request_trip');}}",
            content: "{{trans('user_provider_web.request_trip_message');}}",
        }
    ]);

    // Initialize the tour
    tour.init();

    // Start the tour
    tour.start();
</script>

@stop 