@extends('web.providerLayout')

@section('content')

<div class="col-lg-12 col-sm-12 mb">
    @if(Session::has('message'))
    <div class="alert alert-{{ Session::get('type') }}">
        <b>{{ Session::get('message') }}</b> 
    </div>
    @endif
    <div class="form-panel">
        <h4 class="mb"></i> {{trans('dashboard.filter');}}</h4>
        <form class="form-inline" role="form" method="get" action="{{ URL::Route('ProviderTrips') }}">
            <div class="form-group">
                <label class="sr-only" for="exampleInputEmail2">{{trans('dashboard.start_date');}}</label>
                <input type="text" class="form-control" id="start-date" name="start_date" value="{{ Input::get('start_date') }}" placeholder="{{trans('dashboard.start_date');}}">
            </div>
            <div class="form-group">
                <label class="sr-only" for="exampleInputPassword2">{{trans('dashboard.end_date');}}</label>
                <input type="text" class="form-control" id="end-date" name="end_date" placeholder="{{trans('dashboard.end_date');}}"  value="{{ Input::get('end_date') }}">
            </div>
            <button id="filter" type="submit" name="submit" value="filter" class="btn btn-theme"><i class="fa fa-filter fa-x"></i> {{trans('dashboard.filter');}}</button>
            <button id="download" type="submit" name="submit" value="export" class="btn btn-theme"><i class="fa fa-download fa-x"></i> {{trans('dashboard.down_report');}}</button>
        </form>
    </div>
</div>

<div class="col-md-3 col-sm-3 mb" style="height:150px;">
    <div class="darkblue-panel pn"  style="height:200px;">
        <div class="darkblue-header">
            <h5>{{trans('user_provider_web.request_total');}}</h5>
        </div>
        <h1 class="mt"><i class="fa fa-user fa-2x"></i></h1>
        <p>  </p>
        <footer>
            <div class="centered">
                <h5><i class=""></i> {{ $total_rides }}</h5>
            </div>
        </footer>
    </div>
</div>
<div class="col-md-3 col-sm-3 mb" style="height:150px;">
    <div class="darkblue-panel pn"  style="height:200px;">
        <div class="darkblue-header">
            <h5>{{trans('user_provider_web.distance_total');}}</h5>
        </div>
        <h1 class="mt"><i class="fa fa-map-marker fa-2x"></i></h1>
        <p>  </p>
        <footer>
            <div class="centered">
                <h5><i class=""></i> {{ sprintf2($total_distance, 2) }} {{trans('user_provider_web.distance_unit');}}</h5>
            </div>
        </footer>
    </div>
</div>
<div class="col-md-3 col-sm-3 mb" style="height:150px;">
    <div class="darkblue-panel pn"  style="height:200px;">
        <div class="darkblue-header">
            <h5>{{trans('user_provider_web.avarage_rate');}}</h5>
        </div>
        <h1 class="mt"><i class="fa fa-star fa-2x"></i></h1>
        <p>  </p>
        <footer>
            <div class="centered">
                <h5><i class=""></i>{{ sprintf2($average_rating, 2) }}</h5>
            </div>
        </footer>
    </div>
</div>

<div class="col-md-3 col-sm-3 mb" style="height:150px;">
    <div class="darkblue-panel pn"  style="height:200px;">
        <div class="darkblue-header">
            <h5>{{trans('user_provider_web.total_won');}}</h5>
        </div>
        <h1 class="mt"><i class="fa fa-money fa-2x"></i></h1>
        <p>  </p>
        <footer>
            <div class="centered">
                <h5><i class=""></i> <?php echo $currency; ?> {{ sprintf2($total_earnings, 2) }}</h5>
            </div>
        </footer>
    </div>
</div>

<div class="col-md-12 mt" style="position:relative;top:25px;" >
    <div class="content-panel">
        <table class="table table-hover" id="trip-table">
            <thead>
                <tr>
                    <th>{{trans('user_provider_web.date');}}</th>
                    <th>{{trans('customize.User');}}</th>
                    <th>{{trans('user_provider_web.earning');}}</th>
                    <th>{{trans('provider.service_type');}}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($requests as $request)
                <tr class="trip-basic" data-id="{{ route('ProviderTripDetail',$request->id)}}">
                <?php 
                    setlocale(LC_ALL, "pt_BR", "pt_BR.iso-8859-1", "pt_BR.utf-8", "portuguese");
                    date_default_timezone_set('America/Sao_Paulo');
                ?>
                    <td>{{ strftime('%A, %d de %B de %Y',strtotime($request->request_start_time)) }}</td>
                    <td>{{ $request->first_name }} {{ $request->last_name }}</td>
                    <td>{{ sprintf2($request->total, 2) }}</td>
                    <td>{{ $request->type }}</td>
                </tr>
                <tr class="trip-detail" style="display:none;">
                    <td colspan="4"><center>{{trans('user_provider_web.Loading')}}...</center></td>
            </tr>
            @endforeach

            </tbody>
        </table>
    </div>
</div>

<script type="text/javascript">

    $(function () {
        $(".trip-basic").click(function () {
            var $this = $(this);
            var id = $(this).data('id');
            $this.next().toggle();
            $.ajax({url: id,
                type: 'get',
                success:
                    function (msg) {
                        if (msg === 'false') {
                            alert('{{trans('user_provider_web.no_result')}}');
                        }
                        else {
                            $this.next().html(msg);
                        }
                    }
            });
        });
    });

</script>

<script>
    $(function () {
        $("#start-date").datepicker({
            defaultDate: "+1w",
            changeMonth: true,
            numberOfMonths: 1,
            defaultTime: "00:00",
            onClose: function (selectedDate) {
                $("#end-date").datepicker("option", "minDate", selectedDate);
            }
        });
        $("#end-date").datepicker({
            defaultDate: "+1w",
            changeMonth: true,
            numberOfMonths: 1,
            defaultTime: "23:59",
            onClose: function (selectedDate) {
                $("#start-date").datepicker("option", "maxDate", selectedDate);
            }
        });
    });
</script>


<script type="text/javascript">
    var tour = new Tour(
            {
                name: "providerappTrips",
                template:  "<div class='popover tour'><div class='arrow'></div><h3 class='popover-title'></h3><div class='popover-content'></div><div class='popover-navigation'><button class='btn btn-default' data-role='prev'>{{ trans("user_provider_web.prev_tour_step"); }}</button><button class='btn btn-default' data-role='next'>{{ trans("user_provider_web.next_tour_step"); }}</button><button class='btn btn-default' data-role='end'>{{ trans("user_provider_web.end_tour"); }}</button></div></div>",
            });

    // Add your steps. Not too many, you don't really want to get your users sleepy
    tour.addSteps([
        {
            element: "#flow21",
            title: "{{ trans("user_provider_web.availability_setting"); }}",
            content: "{{ trans("user_provider_web.availability_change_message"); }}",
        },
    ]);

    tour.addSteps([
        {
            element: "#flow23",
            title: "{{trans('user_provider_web.documents');}}",
            content: "{{trans('user_provider_web.documents_message');}}",
        },
    ]);

    // Initialize the tour
    tour.init();

    // Start the tour
    tour.start();
</script>


@stop 