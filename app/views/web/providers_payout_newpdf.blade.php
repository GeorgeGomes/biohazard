@extends('onwlayout.layout_html')
@section('content')

<style>
    .total{
        font-size: 45px;
        font: bold;

    }
    .heading{
        background-color: #2866A1;
        color: #ffffff;
    }
    .even{
        background-color: #f2f2f4;
    }
    .hr{
        margin: 7px 0;
        border: 0;
        border-top: 2px solid rgb(181, 181, 181);
        border-bottom: 0;
    }
    .headother{
        font-size: 15px;
        font: bold;
        width: 200px;
    }

    table{border: 0px}

    .table>tbody>tr>td, .table>tfoot>tr>td {
        padding: 8px;
        line-height: 1.42857143;
        vertical-align: top;
        border-top: 0px solid #ddd;
    }

    tr:nth-child(even) {background: #f2f2f4;}
    tr:nth-child(odd) {background: #ffffff;}

</style>
<div class="page-content">

    <div class="row col-md-12">
        <div class="col-md-6">
            <?php
            ?> 

        </div>
        <div class="col-md-6 align-right right_side">
            <h2>{{Config::get('app.website_title')}} </h2>
        </div>
    </div>

    <br>
    <div class="row col-md-12">
        <div class="col-md-6">
            <div class="col-md-4">{{trans('payout.total_payout');}}<br>
                <p class="total">  <?php
                    $totalweek = weektotal($requests);
                    $payment_remaining_total = payment_remaining_total($requests);
                    $refund_remaining_total = refund_remaining_total($requests);

                    $ridercut = $payment_remaining_total - $refund_remaining_total;
                    echo"<h2>" . Config::get('app.currency') . $totalpayout = $totalweek - $ridercut . "</h2>";
                    ?></p>
            </div>

        </div>
    </div>
    <br>
    <div class="row col-md-12">
        <br>
        <div class="col-md-6">
           <?php 
                setlocale(LC_ALL, 'pt_BR', 'pt_BR.utf-8', 'pt_BR.utf-8', 'portuguese');
                date_default_timezone_set('America/Sao_Paulo');
            ?>
            {{trans('payout.period_ending');}}: <?php echo strftime('%d de %B, %Y', strtotime(Input::get('weekend'))); ?>
        </div>
    </div>

    <div class="row col-md-12 heading" style="margin-left: 0;">
        <div class="col-md-6">

            {{trans('payout.driver_earnings');}}
        </div>
    </div>
    <table class="table">
        <tbody>
            <tr>
                <td><b>{{trans('payout.drivers');}}</b></td>
                <td><b>{{trans('payout.fare');}}</b></td>
                <td><b>{{Config::get('app.website_title')}} {{trans('payout.Fee');}}</b></td>
                <td><b>{{trans('payout.earnings');}}</b></td>
            </tr>


            <?php
            $i = 0;

            foreach ($providers as $provider) {
                ?><tr>
                    <td></td>
                    <td><?= $provider->first_name . " " . $provider->last_name; ?></td>
                    <td><?= sprintf2(($provider->totalpayment), 2); ?></td>
                    <td><?= sprintf2(($provider->refund_remaining + $provider->payment_remaining), 2); ?></td>
                    <td><?= sprintf2(($provider->totalpayment - $provider->refund_remaining + $provider->payment_remaining), 2); ?></td>
                    <td></td>
                </tr>

                <?php
            }
            ?>




        </tbody>

    </table>

    <div class="row col-md-12">
        <hr class="hr">
    </div>




    <div class="box box-info tbl-box">


        <?php
        $drivername = '';
        foreach ($providers as $provider) {

            if ($provider->total_requests != 0) {
                ?>



                <?php
                $weektotal = 0;
                foreach ($requests as $request) {
                    if($request->card_payment == 0) ///Se valor é 0 significa que pagamento foi feito em dinheiro
                    {
                        $request->card_payment = $request->total;
                    }
                    if ($provider->id == $request->confirmed_provider) {
                        $weektotal = $weektotal + $request->card_payment;
                    }
                }
                ?>

                <table class="table">
                    <tbody>
                        <?php
                        $date = 0;
                        $datewisetotal = 0;
                        $recorddis = 0;
                        $daypayout = 0;
                        foreach ($requests as $request) {
                            if($request->card_payment == 0) ///Se valor é 0 significa que pagamento foi feito em dinheiro
                            {
                                $request->card_payment = $request->total;
                            }
                            if ($provider->id == $request->confirmed_provider) {
                                if ($date == 0) {
                                    $recorddis = 1;
                                    $date = date('Y-M-d', strtotime($request->date));
                                    ?>
                                    <?php
                                }
                                if ($date > date('Y-m-d', strtotime($request->date))) {
                                    $date = date('Y-m-d', strtotime($request->date));

                                    setlocale(LC_ALL, 'pt_BR', 'pt_BR.utf-8', 'pt_BR.utf-8', 'portuguese');
                                    date_default_timezone_set('America/Sao_Paulo');

                                    $datedis = strftime('%d de %B, %Y', strtotime($request->date));

                                    $daypatyout = patyoutday($requests, $date, $provider->id);
                                    ?>
                                    <tr style="background-color: #ffffff;">

                                        <?php
                                        if ($drivername != $request->provider_first_name . " " . $request->provider_last_name) {
                                            ?>
                                            <td colspan="7" style="text-align:left ">



                                            </td>

                                            <?php
                                        } else {
                                            ?>
                                    <hr class='hr'>
                                    <?php
                                    echo '<td colspan="4"><span class="headother"><b>' . strtoupper($datedis) . ' | </b></span></td>';



                                    echo '<td colspan="3"><span class="align right_side"><b>' . Config::get('app.currency') . $daypatyout . '<b></span></td>';
                                    echo "<br>";
                                }
                                ?>




                                </tr>

                                <tr><td colspan="7" style="text-align: left">

                                        <?php
                                        if ($drivername != $request->provider_first_name . " " . $request->provider_last_name) {
                                            ?>

                                            <?php echo "<b><h2> " . $request->provider_first_name . " " . $request->provider_last_name . ": ". trans('payout.Payment_Statement') ."</h2></b>";
                                            ?>
                                            <br>
                                            {{trans('payout.total_payout');}}
                                            <br>
                                            <?php echo Config::get('app.currency'); ?> <?php
                                            $weektotal = driverweek($requests, $provider->id);
                                            $payment_remaining_total = payment_remaining_total($requests);
                                            $refund_remaining_total = refund_remaining_total($requests);

                                            $ridercut = $payment_remaining_total - $refund_remaining_total;
                                            echo $totalpayout = $weektotal - $ridercut;
                                        }
                                        ?>


                                    </td>
                                </tr>

                                <?php
                                if ($drivername != $request->provider_first_name . " " . $request->provider_last_name) {
                                    ?>
                                    <tr>
                                        <th><b style="color: #000033;">{{trans('payout.date_time');}}</b></th>
                                        <th><b style="color: #000033;">{{trans('payout.trip_id');}}</b></th>
                                        <th><b style="color: #000033;">{{trans('payout.type');}}</b></th>

                                        <th><b style="color: #000033;">{{trans('payout.payment_type');}}</b></th>
                                        <th><b style="color: #000033;">{{trans('payout.fare');}}</b></th>
                                        <th><b style="color: #000033;">{{Config::get('app.website_title')}} {{trans('payout.Fee');}}</b></th>
                                        <th><b style="color: #000033;">{{trans('payout.earnings');}}</b></th>

                                    </tr>
                                    <?php
                                    $weektotal = driverweek($requests, $provider->id);
                                    $payment_remaining_total = payment_remaining_total($requests);
                                    $refund_remaining_total = refund_remaining_total($requests);
                                    ?>
                                    <tr><td><b>{{trans('payout.week_totals');}}</b></td>
                                        <td></td>
                                        <td></td><td></td>
                                        <td><?php echo "<b>" . Config::get('app.currency') . $weektotal . "</b>"; ?></td>
                                        <td><?php echo "<b>(" . Config::get('app.currency') . sprintf2(($payment_remaining_total - $refund_remaining_total), 2) . ");</b>" ?></td>
                                        <td><?php echo "<b>" . Config::get('app.currency') . sprintf2(($weektotal + $payment_remaining_total - $refund_remaining_total), 2) . "</b>"; ?></td>

                                    </tr>
                                    <tr>
                                        <td colspan="7" style="background-color: #ffffff;">
                                            <hr class="hr">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="7" style="text-align: left;background-color: #ffffff;">
                                            <?php
                                            //

                                            echo '<span class="headother"><b>' . strtoupper($datedis) . '</b></span>';

                                            echo '<span class="align right_side"><b>' . Config::get('app.currency') . $daypatyout . '</b></span>';
                                            ?>
                                        </td>
                                    </tr>
                                    <?php
                                    $drivername = $request->provider_first_name . " " . $request->provider_last_name;
                                }
                                ?>
                                <?php
                                $daypayout = 0;
                                $daypayout +=$request->card_payment;
                            } else {
                                $daypayout +=$request->card_payment;
                            }
                            ?>


                            <tr>
                                <?php
                                setlocale(LC_ALL, 'pt_BR', 'pt_BR.utf-8', 'pt_BR.utf-8', 'portuguese');
                                date_default_timezone_set('America/Sao_Paulo');
                                ?>
                                <td><?php echo strftime('%d de %B, %Y', strtotime($request->request_start_time)); ?> </td>
                                <td><?= $request->id ?></td>
                                <td><?php
                                    $provider_type = DB::table('provider_type')->where('id', '=', $request->type)->first();

                                    //echo $provider_type->name;
                                    ?></td>





                                <td><?php
                                    if ($request->cash_or_card) {
                                        echo trans('payout.cash');
                                    } else {
                                        echo trans('payout.credit_card');
                                    }
                                    ?></td>
                                <td>
                                    <?php echo Config::get('app.currency') . sprintf2(($request->card_payment), 2); ?>
                                </td>
                                <td>
                                    <?php echo '(' . Config::get('app.currency') . sprintf2(($request->payment_remaining - $request->refund_remaining), 2) . ')'; ?>
                                </td>
                                <td>
                                    <?php echo Config::get('app.currency') . sprintf2(($request->card_payment + $request->payment_remaining - $request->refund_remaining), 2); ?>
                                </td>
                            </tr>
                            <?php
                        }
                        ?>
                        <?php
                    }
                    ?>  </tbody>
                </table>
                <?php
            }
        }
        ?>
    </div>
</div>


@stop