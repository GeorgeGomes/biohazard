<td colspan="4">
    <div class="row">
        <div id="trip-map" class="col-lg-4">
            <img width="250" height="250" src="{{ $map_url }}">
        </div>
        <div id="trip-info" class="col-lg-4">
            <div class="col-lg-12">
                <?php 
                    setlocale(LC_ALL, "pt_BR", "pt_BR.iso-8859-1", "pt_BR.utf-8", "portuguese");
                    date_default_timezone_set('America/Sao_Paulo');
                ?>
                <h2><?php echo $currency . ' '; ?> {{ sprintf2(($request->total - $request->ledger_payment), 2)}}</h2>
                <p>{{ strftime('%A, %d de %B de %Y',strtotime($request->request_start_time)) }}</p>
            </div>
            <div class="col-lg-12">
                <br>
            </div>
            <div class="col-lg-12">
                <span class="glyphicon glyphicon-record" style="color:green" aria-hidden="true"></span>
                <span>{{ date('h:i A',strtotime($start->created_at)) }}</span>
                <div>
                    {{ $start_address }}
                </div>
            </div>
            <div class="col-lg-12">
                <br>
            </div>
            <div class="col-lg-12">
                <span class="glyphicon glyphicon-record" style="color:red" aria-hidden="true"></span>
                <span>{{ date('h:i A',strtotime($end->created_at)) }}</span>
                <div>
                    {{ $end_address }}
                </div>
            </div>

        </div>
        <div id="trip-action" class="col-lg-4">

            <div class="col-lg-12">
                <div class="col-lg-2">
                </div>
                <div class="col-lg-2">
                    <p><img src="{{ $user->picture }}" class="img-circle" width="50"></p>
                </div>
                <div class="col-lg-8">
                    <div class="col-lg-12">
                        <b>{{ $user->first_name }} {{ $user->last_name }}</b>
                    </div>
                    <div class="col-lg-12">
                        @for ($i = 1; $i <= $rating; $i++)
                        <span><img src="{{ asset_url() }}/web/star.png"></span>
                        @endfor

                    </div>
                </div>

            </div>

            <div class="col-lg-12" style="top:5px;">
                <center>
                    <b>{{trans('user_provider_web.fare_Detail');}}</b>
                    <table id="fare-table" style="position:relative;top:15px;">
                        <tr>
                            <td align="left">{{trans('user_provider_web.base_fare');}}</td>
                            <td align="right">{{ sprintf2($request_services->base_price, 2) }}</td>
                        </tr>
                        <tr>
                            <td>{{trans('user_provider_web.distance');}}</td>
                            <td align="right">{{ sprintf2($request_services->distance_cost, 2) }}</td>
                        </tr>
                        <tr style="border-bottom: #cccccc solid 1px">
                            <td>{{trans('dashboard.time');}}</td>
                            <td align="right">{{ sprintf2($request_services->time_cost,2) }}</td>
                        </tr>
                        <tr>
                            <td>{{trans('user_provider_web.cost');}}</td>
                            <td align="right">{{ sprintf2($request_services->total,2) }}</td>
                        </tr>
                        <tr style="border-bottom: #cccccc solid 1px">
                            <td>{{trans('user_provider_web.promotion');}}</td>
                            <td align="right">-{{ $request->ledger_payment }}</td>
                        </tr>
                        <tr>
                            <td><b>{{trans('user_provider_web.charged');}}</b></td>
                            <td align="right"><b><?php if ($request->payment_mode == 1) { ?> {{ sprintf2($request->total, 2) }} <?php } else { ?>{{ sprintf2($request->card_payment, 2) }} <?php } ?></b></td>
                        </tr>
                    </table>
                </center>
            </div>
            <div class="col-lg-12">

            </div>

        </div>
</td>