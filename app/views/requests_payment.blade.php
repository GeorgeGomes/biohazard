@extends('layout')
@section('content')

<div class="page-content">

    <div class="box box-info tbl-box">
        <div class="portlet-body flip-scroll">


            <table class="table table-bordered table-striped table-condensed flip-content">
                <thead class="flip-content">
                    <tr>
                        <th>{{trans('map.id');}}</th>
                        <th>{{ trans('payment.trip');}}</th>
                        <th>{{ trans('payment.total');}}</th>
                        <th>{{ trans('payment.week_finish');}}</th>
                        <th>{{ trans('payment.download');}}</th>

                    </tr>
                </thead>
                <tbody>

                    <?php
                    $i = 0;
                    $start;
                    $end;
                    foreach ($requests as $request) {

                        if ($i == 0) {
                            $start = 0;
                            $end = $request->id;
                            $startdate = 0;
                            $enddate = $request->created_at;
                        }
                        if ($i != 0) {

                            $start = $end - 1;
                            $end = $request->id;

                            $startdate = strtotime($enddate);
                            $startdate = strtotime("+1 days", $startdate);
                            $startdate = date('Y-m-d H:m:s', $startdate);
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
                            <td><?= Config::get('app.currency') . sprintf2(($request->total - $request->pay_to_provider + $request->take_from_provider), 2) ?></td>
                            <td><?php
                                //echo $request->created_at;
                                $formate = 'Y-m-d H:i:s';
                                $displaydate = Config::get('app.appdate');

                                if (date('N', strtotime($request->created_at)) == 1) {
                                    $dateweek = strtotime(date($formate, strtotime($request->created_at)) . " -6 days");
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
                                ?>
                            </td>
                            <td>
                                <form method="post" action="<?= web_url(); ?>/admin/requests_pdf">
                                    <input type="hidden" name="id" value="<?php echo $request->id; ?>">
                                    <input type="hidden" name="weekend" value="<?php echo $weekend; ?>">
                                    <input type="hidden" name="total" value="<?php echo $request->total; ?>">
                                    <input type="hidden" name="trips" value="<?php echo $request->trips; ?>">
                                    <input type="hidden" name="pay_to_provider" value="<?php echo $request->pay_to_provider; ?>">
                                    <input type="hidden" name="take_from_provider" value="<?php echo $request->take_from_provider; ?>">
                                    <input type="submit" class="btn blue" value="visualizar">
                                </form>
                            </td>
                        </tr>
                        <?php
                        $i++;
                    }
                    ?>
                </tbody>
            </table>

            <div align="right" id="paglink"><?php echo $requests->appends(array('type' => Session::get('type'), 'valu' => Session::get('valu')))->links(); ?></div>



        </div>
    </div>
</div>


<script>

    $('#datetimepicker').datetimepicker({value: '2015/04/15 05:03', step: 10});

    $('#some_class').datetimepicker();
    $('#some_class1').datetimepicker();


</script>
@stop