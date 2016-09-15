@extends('web.layout')

@section('content')

<div class="col-md-12 mt">

    @if(Session::has('message'))
    <div class="alert alert-{{ Session::get('type') }}">
        <b>{{ Session::get('message') }}</b> 
    </div>
    @endif

    <div class="content-panel" >
        <div class="row">
            <div class="col-lg-5"><h4>{{trans('user.pay_method');}}</h4> </div>
            <div class="col-lg-6"><button class="btn btn-primary" id="add-new"> <i class="fa fa-plus"></i> </button></div>


                <?php if(Config::get('app.default_payment') == 'pagarme'){  ?>

                <div class="col-lg-12" id="add-card" style="display:none;">
                    <form action="{{route('userpayments')}}" method="POST" id="payment-form" class="col-lg-6">

                        <div class="col-lg-12">
                            <div class="col-lg-6">
                                <br><span class="payment-errors" style="color:red"></span><br>

                                <input id="card_holder" type="text" size="50" class="form-control" placeholder="{{trans('user.card_holder_name');}}" name="text" />
                                <br>
                            </div>
                        </div>

                        <div class="col-lg-12">
                            <div class="col-lg-6">

                                <input id="card_number" type="text" maxlength="20" size="20" class="form-control" placeholder="{{trans('user.card_numer');}}" name="number" />
                                <br>
                            </div>
                        </div>

                        <div class="col-lg-12">
                            <div class="col-lg-2">
                                <span id="no_number_error1" style="display: none"> </span>
                                <input id="card_cvv" type="text" maxlength="4" size="4" id="cvv"  class="form-control" name="cvv" placeholder="{{trans('user.cvv');}}" onkeypress="return IsNumeric(event, 1);" />
                                <br>
                            </div>
                        </div>

                        <div class="col-lg-12" >
                            <span class="col-lg-3">
                                <span id="no_number_error2" style="display: none"> </span>
                                <input id="card_exp_month" type="text"  maxlength="2" size="2"  name="month" placeholder="MM" class="form-control" onkeypress="return IsNumeric(event, 2);" />
                            </span>
                            <span class="col-lg-3">
                                <span id="no_number_error3" style="display: none"> </span>
                                <input id="card_exp_year" type="text"  maxlength="4" size="4" name="year" placeholder="YYYY" class="form-control" onkeypress="return IsNumeric(event, 3);" />
                            </span>
                        </div>
                        <div class="col-lg-12" >
                            <span class="col-lg-3"><br>
                                <button id="payment" type="submit" class="btn btn-success"placeholder="{{trans('user.save_card');}}">{{trans('user.save_card');}}</button>
                            </span>
                        </div>

                    </form>

                </div>
            <?php } ?>

            <?php if (Config::get('app.default_payment') == 'stripe') { ?>
                <div class="col-lg-12" id="add-card" style="display:none;">
                    <form action="{{route('userpayments')}}" method="POST" id="payment-form" class="col-lg-6">

                        <div class="col-lg-12">
                            <div class="col-lg-6">
                                <br><span class="payment-errors" style="color:red"></span><br>

                                <input type="text" size="20" data-stripe="number" class="form-control" placeholder="{{trans('user.pay_method');}}" name="number" />
                                <br>
                            </div>
                        </div>

                        <div class="col-lg-12">
                            <div class="col-lg-2">
                                <span id="no_number_error1" style="display: none"> </span>
                                <input type="text" size="4" id="cvv" data-stripe="cvc"  class="form-control" name="cvv" placeholder="{{trans('user.cvv');}}" onkeypress="return IsNumeric(event, 1);" />
                                <br>
                            </div>
                        </div>

                        <div class="col-lg-12" >
                            <span class="col-lg-3">
                                <span id="no_number_error2" style="display: none"> </span>
                                <input type="text" size="2" data-stripe="exp-month" name="month" placeholder="MM" class="form-control" onkeypress="return IsNumeric(event, 2);" />
                            </span>
                            <span class="col-lg-3">
                                <span id="no_number_error3" style="display: none"> </span>
                                <input type="text" size="4" data-stripe="exp-year" name="year" placeholder="YYYY" class="form-control" onkeypress="return IsNumeric(event, 3);" />
                            </span>
                        </div>
                        <div class="col-lg-12" >
                            <span class="col-lg-3"><br>
                                <button id="payment" type="submit" class="btn btn-success"placeholder="{{trans('user.save_card');}}">{{trans('user.save_card');}}</button>
                            </span>
                        </div>

                    </form>

                </div>


            <?php } else { ?>

                <div class="col-lg-12" id="add-card" style="display:none;">
                    <form action="{{route('userpayments')}}" method="POST" id="braintree-payment-form" class="col-lg-6">

                        <div class="col-lg-12">
                            <div class="col-lg-6">
                                <br><span class="payment-errors" style="color:red"></span><br>

                                <input type="text" size="20" data-encrypted-name="number"  class="form-control" placeholder="{{trans('user.pay_method');}}" name="number" />
                                <br>
                            </div>
                        </div>

                        <div class="col-lg-12">
                            <div class="col-lg-2">
                                <input type="text" size="4"  data-encrypted-name="cvv" class="form-control" placeholder="{{trans('user.cvv');}}" />
                                <br>
                            </div>
                        </div>

                        <div class="col-lg-12" >
                            <span class="col-lg-3">
                                <input type="text" size="2" name="month" placeholder="MM" class="form-control" />
                            </span>
                            <span class="col-lg-3">
                                <input type="text" size="4" name="year" placeholder="YYYY" class="form-control" />
                            </span>
                        </div>
                        <div class="col-lg-12" >
                            <span class="col-lg-3"><br>
                                <input type="submit" id="submit" class="btn btn-success" value="{{trans('user.save_card')}}"/>
                            </span>
                        </div>

                    </form>

                    <script src="https://js.braintreegateway.com/v1/braintree.js"></script>
                    <script>
                                    var braintree = Braintree.create("<?= Config::get('app.braintree_cse'); ?>");
                                    braintree.onSubmitEncryptForm('braintree-payment-form');
                    </script>

                </div>

            <?php } ?>
            <?php if ($payments) { ?>
                @foreach ( $payments as $payment)
                <div class="col-lg-12" style="padding-left:20px;">
                    <div class="col-lg-3" style="padding:10px">
                        <img src="{{ asset_url() }}/web/credit_card.png">&nbsp;&nbsp;&nbsp;{{trans('user.credit_card');}}
                    </div>
                    <div class="col-lg-3" style="padding:10px;padding-top:15px;">
                        **** {{ $payment->last_four }}
                    </div>
                    <div class="col-lg-3" style="padding:10px;padding-top:15px;">
                        <a href="{{route('/user/payment/delete',$payment->id)}}">{{trans('provider.delete_grid');}}</a>
                    </div>
                </div>
                @endforeach
            <?php } ?>
            <div class="col-lg-7"  ></div>

            <div class="col-lg-12" style="padding:20px;" >
                <h4 >{{trans("user_provider_web.refund_credit");}}</h4>
                <div class="col-lg-6" >
                    <div class="col-lg-6"  style="padding-top:10px">
                        {{trans("user_provider_web.total_referal");}}
                    </div>
                    <div class="col-lg-2"  style="padding-top:10px;text-align:right;">
                        <?php
                            if((strcasecmp($ledger->total_referrals, null) == 0)|| ($ledger->total_referrals == 0)){
                                echo 0;
                            } else {
                                echo $ledger->total_referrals;
                            }
                        ?>
                    </div>
                    <div class="col-lg-6"  style="padding-top:10px">
                        {{trans("user_provider_web.credit_earned");}}
                    </div>
                    <div class="col-lg-2"  style="padding-top:10px;text-align:right;">
                        {{ $ledger?round($ledger->amount_earned):0 }}
                    </div>
                    <div class="col-lg-6"  style="padding-top:10px;">
                        {{trans("user_provider_web.credit_spent");}}
                    </div>
                    <div class="col-lg-2"  style="padding-top:10px;text-align:right;">
                        {{ $ledger?round($ledger->amount_spent):0 }}
                    </div>
                    <div class="col-lg-6"  style="padding-top:10px;border-top: #cccccc solid 1px;">
                        <b>{{trans("user_provider_web.balance_credits");}}</b>
                    </div>
                    <div class="col-lg-2"  style="padding-top:10px;text-align:right;border-top: #cccccc solid 1px;">
                        <b>{{ $ledger?round($ledger->amount_earned - $ledger->amount_spent):0 }}</b>
                    </div>
                </div>
            </div>

            <div class="col-lg-7" style="border-bottom: #cccccc solid 1px;" ></div>

            <div class="col-lg-12" style="padding:20px;">
                <h4>{{trans('user.referrals');}}</h4><br>
                <div class="col-lg-6" >
                    <form method="post" action="{{route('/user/update_code')}}">
                        <div class="col-lg-6"  style="padding-top:10px">
                            <input type="text" class="form-control" placeholder="{{trans('user.referrals_code');}}" name="code" value="{{ $ledger?$ledger->referral_code:'' }}">
                        </div>
                        <div class="col-lg-2"  style="padding-top:10px;text-align:right;">
                            <button type="submit" class="btn btn-theme" placeholder="{{trans('user.change_code');}}">{{trans('user.change_code');}}</button>
                        </div>
                    </form>
                </div>
            </div>



        </div>
    </div>

</div>


<script type="text/javascript">

    $(function () {
        $("#add-new").click(function () {
            $("#add-card").toggle();
        });
    });
</script>



@stop 