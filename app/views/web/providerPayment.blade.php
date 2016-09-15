@extends('web.providerLayout')

@section('content')

<div class="col-md-12 mt" style="position:relative;top:25px;" >
    <div class="content-panel">
        <table class="table table-hover" id="trip-table">
            <thead>
                <tr>
                    <th>{{trans('map.id');}}</th>
                    <th>{{ trans('payment.trip');}}</th>
                    <th>{{ trans('payment.total');}}</th>
                    <th>{{ trans('payment.week_finish');}}</th>
                    <th>{{ trans('provider.status_grid');}}</th>
                    <th>{{ trans('payment.download');}}</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $i = 0;
                $startno;
                $end;
                foreach ($requests as $request) {

                    if ($i == 0) {
                        $startno = 0;
                        $end = $request->id;
                        $startdate = 0;
                        $enddate = $request->created_at;
                    }
                    if ($i != 0) {

                        $startno = $end - 1;
                        $end = $request->id;

                        $startdate = strtotime($enddate);
                        $startdate = strtotime("+1 days", $startdate);
                        $startdate = date('Y-m-d H:i:s', $startdate);
                        $enddate = $request->created_at;
                    }

                    if ($i == 1) {
                        $end = $request->id;
                        $enddate = $request->created_at;
                    }
                    ?>
                    <tr>

                        <td><?= $request->id ?> </td>
                        <td><?= $request->trips ?> </td>
                        <td><?= Config::get('app.currency') . sprintf2(($request->total2),2) ?></td>
                        <td><?php
                            $formate = 'Y-m-d H:i:s';
                            $displaydate = Config::get('app.appdate');
                            // echo  $request->created_at;
                            //echo  date('N', strtotime($request->created_at));
                            if (date('N', strtotime($request->created_at)) == 1) {
                                $dateweek = strtotime(date($formate, strtotime($request->created_at)) . " +6 days");
                                echo $weekend = date($displaydate, $dateweek);
//                            echo "<br>";
//                          echo   date('l', $dateweek);
                            }


                            if (date('N', strtotime($request->created_at)) == 2) {

                                $dateweek = strtotime(date($formate, strtotime($request->created_at)) . " +5 days");
                                echo $weekend = date($displaydate, $dateweek);
//                            echo "<br>";
//                          echo   date('l', $dateweek);
                            } else if (date('N', strtotime($request->created_at)) == 3) {
                                $dateweek = strtotime(date($formate, strtotime($request->created_at)) . " +4 days");
                                echo $weekend = date($displaydate, $dateweek);
//                             echo "<br>";
//                          echo   date('l', $dateweek);
                            } else if (date('N', strtotime($request->created_at)) == 4) {
                                $dateweek = strtotime(date($formate, strtotime($request->created_at)) . " +3 days");
                                echo $weekend = date($displaydate, $dateweek);
//                             echo "<br>";
//                          echo   date('l', $dateweek);
                            } else if (date('N', strtotime($request->created_at)) == 5) {
                                $dateweek = strtotime(date($formate, strtotime($request->created_at)) . " +2 days");
                                echo $weekend = date($displaydate, $dateweek);
//                             echo "<br>";
//                          echo   date('l', $dateweek);
                            } else if (date('N', strtotime($request->created_at)) == 6) {
                                $dateweek = strtotime(date($formate, strtotime($request->created_at)) . " +1 days");
                                echo $weekend = date($displaydate, $dateweek);
//                             echo "<br>";
//                          echo   date('l', $dateweek);
                            } else if (date('N', strtotime($request->created_at)) == 7) {

                                $dateweek = strtotime(date($formate, strtotime($request->created_at)) . " +0 days");
                                echo $weekend = date($displaydate, $dateweek);
//                             echo "<br>";
//                          echo   date('l', $dateweek);
                            }
                            ?> </td>
                        <td>
                            <?php
                            $start = date("Y-m-d 00:00:00", strtotime('monday this week'));
                            $end_week = date("Y-m-d 23:59:59", strtotime("sunday this week"));

                            $curentdate = date('Y-m-d 11:59:59', strtotime($weekend));


                            if ($curentdate >= $start && $curentdate <= $end_week) {
                                echo "<span class='badge bg-green'>". trans('map.in_queue')."</span>";
                            } else {
                                echo "&nbsp;&nbsp;  <span class='badge bg-blue'>". trans('user_provider_web.process')."</span>";
                            }
                            ?>


                        </td>


                        <td>

                            <a href="<?php echo web_url(); ?>/provider/providers_payout?<?php
                            echo 'start=' . $end;
                            echo '&end=' . $startno . '&startdate=' . $enddate . '&enddate=' . $enddate . '&weekend=' . $weekend;
                            ?> " target="_blank">Html</a></td>
                    </tr>
                            <?php
                            $i++;
                        }
                        ?>

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
                                alert('Dados Não Encontrados');
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
            onClose: function (selectedDate) {
                $("#end-date").datepicker("option", "minDate", selectedDate);
            }
        });
        $("#end-date").datepicker({
            defaultDate: "+1w",
            changeMonth: true,
            numberOfMonths: 1,
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
            title: "{{trans('user_provider_web.availability_setting');}}",
            content: "{{trans('user_provider_web.availability_perfil');}}",
        },
    ]);

    // Initialize the tour
    tour.init();

    // Start the tour
    tour.start();
</script>


@stop 