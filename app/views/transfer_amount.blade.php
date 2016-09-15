@extends('layout')

@section('content')

<div class="box box-success">
    <div class="row">
        <div class="col-lg-6 col-xs-6">
            <!-- small box -->
            <div class="small-box bg-aqua">
                <div class="inner">
                    <h3>
                        <?php
                        $user = User::where('id', $request->user_id)->first();
                        if ($user != NULL) {
                            echo $user->first_name . ' ' . $user->last_name;
                        }
                        ?>
                    </h3>
                    <p>
                        {{trans('customize.User')}}
                    </p>
                </div>
                <div class="icon">
                    <i class="ion ion-person-add"></i>
                </div>

            </div>
        </div><!-- ./col -->
        <div class="col-lg-6 col-xs-6">
            <!-- small box -->
            <div class="small-box bg-yellow">
                <div class="inner">
                    <h3>
                        <?php
                        $provider = Provider::where('id', $request->confirmed_provider)->first();
                        if ($provider != NULL) {
                            echo $provider->first_name . ' ' . $provider->last_name;
                        }
                        ?>
                    </h3>
                    <p>
                        {{trans('customize.Provider')}}
                    </p>
                </div>
                <div class="icon">
                    <i class="ion ion-person"></i>
                </div>

            </div>
        </div><!-- ./col -->
        <div class="col-lg-6 col-xs-6">
            <!-- small box -->
            <div class="small-box bg-red">
                <div class="inner">
                    <h3>
                        {{ $request->card_payment }}
                    </h3>
                    <p>
                        {{trans('payment.Payment')}}
                    </p>
                </div>
                <div class="icon">
                    <i class="ion ion-cash"></i>
                </div>

            </div>
        </div><!-- ./col -->
        <div class="col-lg-6 col-xs-6">
            <!-- small box -->
            <div class="small-box bg-purple">
                <div class="inner">
                    <h3>
                        {{$request->ledger_payment}}
                    </h3>
                    <p>
                    {{trans('payment.Referral_payment')}}
                    </p>
                </div>
                <div class="icon">
                    <i class="ion ion-ios-people"></i>
                </div>

            </div>
        </div><!-- ./col -->
        <div class="col-lg-6 col-xs-6">
            <!-- small box -->
            <div class="small-box bg-purple">
                <div class="inner">
                    <h3>
                        {{$request->refund}}
                    </h3>
                    <p>
                     {{trans('payment.Refund')}}
                    </p>
                </div>
                <div class="icon">
                    <i class="ion ion-social-usd"></i>
                </div>

            </div>
        </div>
        <div class="col-lg-6 col-xs-6">
            <!-- small box -->
            <div class="small-box bg-purple">
                <div class="inner">
                    <h3>
                        {{$request->transfer_amount}}
                    </h3>
                    <p>
                     {{trans('payment.Transfer')}}
                    </p>
                </div>
                <div class="icon">
                    <i class="ion ion-paper-airplane"></i>
                </div>

            </div>
        </div>

    </div>

    <div class="box box-primary">
        <form method="post" action="{{ URL::Route('AdminProviderPay') }}"  enctype="multipart/form-data">
            <input type="hidden" name="id" value="0>">
            <input type="text" name="request_id" value="{{$request->id}}" hidden>
            <div class="box-body">
                <div class="form-group">
                    <label>{{trans('dashboard.amount');}}</label>
                    <input class="form-control" type="text" name="amount" value="">                                                                               
                </div>
                <div class="box-footer">
                    <button type="submit" id="theme" class="btn btn-primary btn-flat btn-block">{{trans('payment.transfer_amount')}}</button>
                </div>
        </form>
    </div>
</div>


@stop