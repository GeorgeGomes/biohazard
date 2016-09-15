@extends('layout')
@section('content')
<!-- <div class="row"> -->
    <div class="row">
        <div class="box box-primary">
            <div class="box-header">
                <h3 class="box-title">{{trans('keywords.customize_backend_word');}}</h3>
            </div><!-- /.box-header -->
            <!-- form start -->
            <form method="post" action="{{ URL::Route('AdminKeywordsSave') }}"  enctype="multipart/form-data">
                <div class="box-body">
                    <div class="form-group">
                        <label> 1. {{trans('customize.Provider');}} </label>
                        <input class="form-control" type="text" name="key_provider" value="{{ Config::get('app.generic_keywords.Provider') }}" placeholder="{{trans('customize.Provider');}}">
                    </div>
                    <div class="form-group">
                        <label> 2. {{trans('customize.User');}} </label>
                        <input class="form-control" type="text" name="key_user" value="{{ Config::get('app.generic_keywords.User') }}" placeholder="{{trans('customize.User');}}">
                    </div>
                    <div class="form-group">
                        <label> 3. {{trans('keywords.taxi');}} </label>
                        <input class="form-control" type="text" name="key_taxi" value="{{ Config::get('app.generic_keywords.Services') }}" placeholder="{{trans('keywords.taxi');}}">
                    </div>
                    <div class="form-group">
                        <label> 4. {{trans('customize.Trip');}} </label>
                        <input class="form-control" type="text" name="key_trip" value="{{ Config::get('app.generic_keywords.Trip') }}" placeholder="{{trans('customize.Trip');}} ">
                    </div>
                    <div class="form-group">
                        <label> 5. {{trans('keywords.coin');}} </label>
                        <select name="key_currency" id="currencies" class="form-control">
                            <option value="AUD" <?php
                            if (Config::get('app.generic_keywords.Currency') == 'AUD') {
                                echo "selected";
                            }
                            ?>>{{trans('keywords.australia');}}</option> 
                            <option value="CAD" <?php
                            if (Config::get('app.generic_keywords.Currency') == 'CAD') {
                                echo "selected";
                            }
                            ?>>{{trans('keywords.canada');}}</option>
                            <option value="CHF" <?php
                            if (Config::get('app.generic_keywords.Currency') == 'CHF') {
                                echo "selected";
                            }
                            ?>>{{trans('keywords.switzerland');}}</option>
                            <option value="DKK" <?php
                            if (Config::get('app.generic_keywords.Currency') == 'DKK') {
                                echo "selected";
                            }
                            ?>>{{trans('keywords.denmark');}}</option>
                            <option value="EUR" <?php
                            if (Config::get('app.generic_keywords.Currency') == 'EUR') {
                                echo "selected";
                            }
                            ?>>{{trans('keywords.europe');}}</option>
                            <option value="GBP" <?php
                            if (Config::get('app.generic_keywords.Currency') == 'GBP') {
                                echo "selected";
                            }
                            ?>>{{trans('keywords.england');}}</option> 
                            <option value="HKD" <?php
                            if (Config::get('app.generic_keywords.Currency') == 'HKD') {
                                echo "selected";
                            }
                            ?>>{{trans('keywords.hong_kong');}}</option>
                            <option value="JPY" <?php
                            if (Config::get('app.generic_keywords.Currency') == 'JPY') {
                                echo "selected";
                            }
                            ?>>{{trans('keywords.japan');}}</option>
                            <option value="MXN" <?php
                            if (Config::get('app.generic_keywords.Currency') == 'MXN') {
                                echo "selected";
                            }
                            ?>>{{trans('keywords.mexico');}}</option>
                            <option value="NZD" <?php
                            if (Config::get('app.generic_keywords.Currency') == 'NZD') {
                                echo "selected";
                            }
                            ?>>{{trans('keywords.new_zealand');}}</option>
                            <option value="PHP" <?php
                            if (Config::get('app.generic_keywords.Currency') == 'PHP') {
                                echo "selected";
                            }
                            ?>>{{trans('keywords.philippines');}}</option>
                            <option value="SEK" <?php
                            if (Config::get('app.generic_keywords.Currency') == 'SEK') {
                                echo "selected";
                            }
                            ?>>{{trans('keywords.sweden');}}</option>
                            <option value="SGD" <?php
                            if (Config::get('app.generic_keywords.Currency') == 'SGD') {
                                echo "selected";
                            }
                            ?>>{{trans('keywords.singapore');}}</option>
                            <option value="SPL" <?php
                            if (Config::get('app.generic_keywords.Currency') == 'SPL') {
                                echo "selected";
                            }
                            ?>>{{trans('keywords.seborga');}}</option>
                            <option value="THB" <?php
                            if (Config::get('app.generic_keywords.Currency') == 'THB') {
                                echo "selected";
                            }
                            ?>>{{trans('keywords.thailand');}} </option>
                            <option value="$" <?php
                            if (Config::get('app.generic_keywords.Currency') == '$') {
                                echo "selected";
                            }
                            ?>>{{trans('keywords.united_states');}}</option>
                            <option value="ZAR" <?php
                            if (Config::get('app.generic_keywords.Currency') == 'ZAR') {
                                echo "selected";
                            }
                            ?>>{{trans('keywords.south_africa');}}</option>
                            <option value="R$" <?php
                            if (Config::get('app.generic_keywords.Currency') == 'R$') {
                                echo "selected";
                            }
                            ?>>{{trans('keywords.brazil');}}</option>

                        </select>
                    </div>
                    <?php
                    if (isset($keywords)) {
                        foreach ($keywords as $keyword) {
                            if ($keyword->id != 5) {
                                /* if ($keyword->id < 5) {
                                  ?>

                                  <div class="form-group">
                                  <label>
                                  <?php
                                  echo $keyword->id . ". " . ucfirst($keyword->alias);
                                  ?>
                                  </label>
                                  <input class="form-control" type="text" name="{{$keyword->id}}" value="{{$keyword->keyword}}" />
                                  </div>
                                  <?php
                                  } */
                            } else {
                                ?>
                                <!--<div class="form-group">
                                    <label>
                                <?php
                                echo $keyword->id . ". " . ucfirst($keyword->alias);
                                ?>
                                    </label>
                                    <select name="{{$keyword->id}}" id="currencies" class="form-control">
                                        <option value="AUD" <?php
                                if ($keyword->keyword == 'AUD') {
                                    echo "selected";
                                }
                                ?>>Australia Dollar</option> 
                                        <option value="CAD" <?php
                                if ($keyword->keyword == 'CAD') {
                                    echo "selected";
                                }
                                ?>>Canada Dollar</option>
                                        <option value="CHF" <?php
                                if ($keyword->keyword == 'CHF') {
                                    echo "selected";
                                }
                                ?>>Switzerland Franc</option>
                                        <option value="DKK" <?php
                                if ($keyword->keyword == 'DKK') {
                                    echo "selected";
                                }
                                ?>>Denmark Krone</option>
                                        <option value="EUR" <?php
                                if ($keyword->keyword == 'EUR') {
                                    echo "selected";
                                }
                                ?>>Euro Member Countries</option>
                                        <option value="GBP" <?php
                                if ($keyword->keyword == 'GBP') {
                                    echo "selected";
                                }
                                ?>>United Kingdom Pound</option> 
                                        <option value="HKD" <?php
                                if ($keyword->keyword == 'HKD') {
                                    echo "selected";
                                }
                                ?>>Hong Kong Dollar</option>
                                        <option value="JPY" <?php
                                if ($keyword->keyword == 'JPY') {
                                    echo "selected";
                                }
                                ?>>Japan Yen</option>
                                        <option value="MXN" <?php
                                if ($keyword->keyword == 'MXN') {
                                    echo "selected";
                                }
                                ?>>Mexico Peso</option>
                                        <option value="NZD" <?php
                                if ($keyword->keyword == 'NZD') {
                                    echo "selected";
                                }
                                ?>>New Zealand Dollar</option>
                                        <option value="PHP" <?php
                                if ($keyword->keyword == 'PHP') {
                                    echo "selected";
                                }
                                ?>>Philippines Peso</option>
                                        <option value="SEK" <?php
                                if ($keyword->keyword == 'SEK') {
                                    echo "selected";
                                }
                                ?>>Sweden Krona</option>
                                        <option value="SGD" <?php
                                if ($keyword->keyword == 'SGD') {
                                    echo "selected";
                                }
                                ?>>Singapore Dollar</option>
                                        <option value="SPL" <?php
                                if ($keyword->keyword == 'SPL') {
                                    echo "selected";
                                }
                                ?>>Seborga Luigino</option>
                                        <option value="THB" <?php
                                if ($keyword->keyword == 'THB') {
                                    echo "selected";
                                }
                                ?>>Thailand Baht</option>
                                        <option value="$" <?php
                                if ($keyword->keyword == '$') {
                                    echo "selected";
                                }
                                ?>>United States Dollar</option>
                                        <option value="ZAR" <?php
                                if ($keyword->keyword == 'ZAR') {
                                    echo "selected";
                                }
                                ?>>South Africa Rand</option>
                                <option value="R$" <?php
                                if ($keyword->keyword == 'R$') {
                                    echo "selected";
                                }
                                ?>>Real Brasileiro</option>
                                    </select>
                                </div>-->
                                <?php
                            }
                        }
                    }
                    ?>
                    <div class="form-group">
                        <label>{{trans('keywords.icon_trip');}}</label>

                        <select class="form-control" style="font-family: 'FontAwesome', Helvetica;" name="total_trip">
                            <?php foreach ($icons as $key) { ?>
                                <option value="<?php echo $key->id; ?>" 
                                <?php
                                /* $icon = Keywords::where('keyword', 'total_trip')->first(); */
                                if (Config::get('app.generic_keywords.total_trip') == $key->id) {
                                    echo "selected";
                                }
                                ?> >
                                            <?php echo "  " . $key->icon_code . "  " . trans('keywords.' . $key->icon_name); ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>{{trans('keywords.icon_trip_complete');}}</label>

                        <select class="form-control" style="font-family: 'FontAwesome', Helvetica;" name="completed_trip">
                            <?php foreach ($icons as $key) { ?>
                                <option value="<?php echo $key->id; ?>" 
                                <?php
                                /* $icon = Keywords::where('keyword', 'completed_trip')->first(); */
                                if (Config::get('app.generic_keywords.completed_trip') == $key->id) {
                                    echo "selected";
                                }
                                ?> >
                                            <?php echo "  " . $key->icon_code . "  " . trans('keywords.' . $key->icon_name); ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>{{trans('keywords.icon_trip_cancel');}}</label>

                        <select class="form-control" style="font-family: 'FontAwesome', Helvetica;" name="cancelled_trip">
                            <?php foreach ($icons as $key) { ?>
                                <option value="<?php echo $key->id; ?>" 
                                <?php
                                /* $icon = Keywords::where('keyword', 'cancelled_trip')->first(); */
                                if (Config::get('app.generic_keywords.cancelled_trip') == $key->id) {
                                    echo "selected";
                                }
                                ?> >
                                            <?php echo "  " . $key->icon_code . "  " . trans('keywords.' . $key->icon_name); ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>{{trans('keywords.icon_total_pay');}}</label>

                        <select class="form-control" style="font-family: 'FontAwesome', Helvetica;" name="total_payment">
                            <?php foreach ($icons as $key) { ?>
                                <option value="<?php echo $key->id; ?>" 
                                <?php
                                /* $icon = Keywords::where('keyword', 'total_payment')->first(); */
                                if (Config::get('app.generic_keywords.total_payment') == $key->id) {
                                    echo "selected";
                                }
                                ?> >
                                            <?php echo "  " . $key->icon_code . "  " . trans('keywords.' . $key->icon_name); ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>{{trans('keywords.icon_referral_pay');}}</label>

                        <select class="form-control" style="font-family: 'FontAwesome', Helvetica;" name="credit_payment">
                            <?php foreach ($icons as $key) { ?>
                                <option value="<?php echo $key->id; ?>" 
                                <?php
                                /* $icon = Keywords::where('keyword', 'credit_payment')->first(); */
                                if (Config::get('app.generic_keywords.credit_payment') == $key->id) {
                                    echo "selected";
                                }
                                ?> >
                                            <?php echo "  " . $key->icon_code . "  " . trans('keywords.' . $key->icon_name); ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>{{trans('keywords.icon_card_pay');}}</label>

                        <select class="form-control" style="font-family: 'FontAwesome', Helvetica;" name="card_payment">
                            <?php foreach ($icons as $key) { ?>
                                <option value="<?php echo $key->id; ?>" 
                                <?php
                                /* $icon = Keywords::where('keyword', 'card_payment')->first(); */
                                if (Config::get('app.generic_keywords.card_payment') == $key->id) {
                                    echo "selected";
                                }
                                ?> >
                                            <?php echo "  " . $key->icon_code . "  " . trans('keywords.' . $key->icon_name); ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>{{trans('keywords.icon_cash_pay');}}</label>

                        <select class="form-control" style="font-family: 'FontAwesome', Helvetica;" name="cash_payment">
                            <?php foreach ($icons as $key) { ?>
                                <option value="<?php echo $key->id; ?>" 
                                <?php
                                /* $icon = Keywords::where('keyword', 'card_payment')->first(); */
                                if (Config::get('app.generic_keywords.cash_payment') == $key->id) {
                                    echo "selected";
                                }
                                ?> >
                                            <?php echo "  " . $key->icon_code . "  " . trans('keywords.' . $key->icon_name); ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>{{trans('keywords.icon_promo_pay');}}</label>

                        <select class="form-control" style="font-family: 'FontAwesome', Helvetica;" name="promotional_payment">
                            <?php foreach ($icons as $key) { ?>
                                <option value="<?php echo $key->id; ?>" 
                                <?php
                                /* $icon = Keywords::where('keyword', 'card_payment')->first(); */
                                if (Config::get('app.generic_keywords.promotional_payment') == $key->id) {
                                    echo "selected";
                                }
                                ?> >
                                            <?php echo "  " . $key->icon_code . "  " . trans('keywords.' . $key->icon_name); ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>{{trans('keywords.icon_schedules_total');}}</label>

                        <select class="form-control" style="font-family: 'FontAwesome', Helvetica;" name="schedules_icon">
                            <?php foreach ($icons as $key) { ?>
                                <option value="<?php echo $key->id; ?>" 
                                <?php
                                /* $icon = Keywords::where('keyword', 'card_payment')->first(); */
                                if (Config::get('app.generic_keywords.schedules_icon') == $key->id) {
                                    echo "selected";
                                }
                                ?> >
                                            <?php echo "  " . $key->icon_code . "  " . trans('keywords.' . $key->icon_name); ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>
                    <!--<div class="form-group">
                        <label> Referral Prefix </label>
                        <input class="form-control" type="text" name="key_ref_pre" value="{{ Config::get('app.referral_prefix') }}" placeholder="Fixed Prefix for Referral Code">
                    </div>-->
                    <input class="form-control" type="hidden" name="key_ref_pre" value="{{ Config::get('app.referral_prefix') }}" placeholder="Prefixo Fíxo para Código de Desconto">

                </div>



                <div class="box-footer">
                    <button type="submit" class="btn btn-primary btn-flat btn-block">{{trans('keywords.save_change');}}</button>
                </div>
            </form>
        </div>

    <!-- </div>
    <div class="col-md-6 col-sm-12">
        <div class="box box-primary">
            <div class="box-header">
                <h3 class="box-title">{{trans('keywords.edit_word_menu_app');}}</h3>
            </div>
            <form role="form" method="POST" action="{{ URL::Route('AdminUIKeywordsSave') }}"  enctype="multipart/form-data">
                <div class="box-body">
                    <div class="form-group">
                        <label >{{trans('customize.Dashboard');}}</label>
                        <input type="text" class="form-control" name="val_dashboard" placeholder="Keyword for Provider" value="{{$Uikeywords['keyDashboard']}}">
                    </div>
                    <div class="form-group">
                        <label >{{trans('customize.map_view');}}</label>
                        <input type="text" class="form-control" name="val_map_view" placeholder="Keyword for Provider" value="{{$Uikeywords['keyMap_View']}}">
                    </div>
                    <div class="form-group">
                        <label >{{trans('customize.Provider');}}</label>
                        <input type="text" class="form-control" name="val_provider" placeholder="Keyword for Provider" value="{{$Uikeywords['keyProvider']}}">
                    </div>
                    <div class="form-group">
                        <label >{{trans('customize.User');}}</label>
                        <input type="text" class="form-control" name="val_user" placeholder="Keyword for User" value="{{$Uikeywords['keyUser']}}">
                    </div>
                    <div class="form-group">
                        <label >{{trans('keywords.taxi');}}</label>
                        <input type="text" class="form-control" name="val_taxi" placeholder="Keyword for Taxi" value="{{$Uikeywords['keyTaxi']}}">
                    </div>
                    <div class="form-group">
                        <label >{{trans('customize.Trip');}}</label>
                        <input type="text" class="form-control" name="val_trip" placeholder="Keyword for Trip" value="{{$Uikeywords['keyTrip']}}">
                    </div>
                    <div class="form-group">
                        <label>{{trans('keywords.walking');}}</label>
                        <input type="text" class="form-control" name="val_walk" placeholder="Keyword for Walk" value="{{$Uikeywords['keyWalk']}}">
                    </div>
                    <div class="form-group">
                        <label>{{trans('customize.Request');}}</label>
                        <input type="text" class="form-control" name="val_request" placeholder="Keyword for Request" value="{{$Uikeywords['keyRequest']}}">
                    </div>
                    <div class="form-group">
                        <label>{{trans('customize.Schedules');}}</label>
                        <input type="text" class="form-control" name="val_schedules" placeholder="Keyword for Schedules" value="{{$Uikeywords['keySchedules']}}">
                    </div>
                    <div class="form-group">
                        <label>{{trans('keywords.week_report');}}</label>
                        <input type="text" class="form-control" name="val_weekstatement" placeholder="Keyword for Weekly Statement" value="{{$Uikeywords['keyWeekStatement']}}">
                    </div>
                    <div class="form-group">
                        <label>{{trans('keywords.reviews');}}</label>
                        <input type="text" class="form-control" name="val_reviews" placeholder="Keyword for Reviews" value="{{$Uikeywords['keyReviews']}}">
                    </div>
                    <div class="form-group">
                        <label>{{trans('keywords.info');}}</label>
                        <input type="text" class="form-control" name="val_information" placeholder="Keyword for Information" value="{{$Uikeywords['keyInformation']}}">
                    </div>
                    <div class="form-group">
                        <label>{{trans('customize.Types');}}</label>
                        <input type="text" class="form-control" name="val_types" placeholder="Keyword for Types" value="{{$Uikeywords['keyTypes']}}">
                    </div>
                    <div class="form-group">
                        <label>{{trans('customize.Documents');}}</label>
                        <input type="text" class="form-control" name="val_documents" placeholder="Keyword for Documents" value="{{$Uikeywords['keyDocuments']}}">
                    </div>
                    <div class="form-group">
                        <label>{{trans('keywords.promo_code');}}</label>
                        <input type="text" class="form-control" name="val_promo_codes" placeholder="Keyword for Promo Codes" value="{{$Uikeywords['keyPromo_Codes']}}">
                    </div>
                    <div class="form-group">
                        <label>{{trans('customize.Customize');}}</label>
                        <input type="text" class="form-control" name="val_customize" placeholder="Keyword for Customize" value="{{$Uikeywords['keyCustomize']}}">
                    </div>
                    <div class="form-group">
                        <label>{{trans('keywords.pay_detail');}}</label>
                        <input type="text" class="form-control" name="val_payment_details" placeholder="Keyword for Payment Details" value="{{$Uikeywords['keyPayment_Details']}}">
                    </div>
                    <div class="form-group">
                        <label>{{trans('customize.Settings');}}</label>
                        <input type="text" class="form-control" name="val_settings" placeholder="Keyword for Settings" value="{{$Uikeywords['keySettings']}}">
                    </div>
                    <div class="form-group">
                        <label>{{trans('customize.Admin');}}</label>
                        <input type="text" class="form-control" name="val_admin" placeholder="Keyword for Admin Button" value="{{$Uikeywords['keyAdmin']}}">
                    </div>
                    <div class="form-group">
                        <label>{{trans('customize.admin_control');}}</label>
                        <input type="text" class="form-control" name="val_admin_control" placeholder="Keyword for Admin Control Button" value="{{$Uikeywords['keyAdmin_Control']}}">
                    </div>
                    <div class="form-group">
                        <label>{{trans('keywords.logout');}}</label>
                        <input type="text" class="form-control" name="val_log_out" placeholder="Keyword for Log Out Button" value="{{$Uikeywords['keyLog_Out']}}">
                    </div>
                </div>
                <div class="box-footer">
                    <button type="submit" class="btn btn-primary btn-flat btn-block">{{trans('keywords.save_change');}}</button>
                </div>
            </form>
        </div>
    </div>
</div> -->
<script type="text/javascript">
    function Checkfiles()
    {
        var fup = document.getElementById('logo');
        var fileName = fup.value;
        if (fileName != '')
        {
            var ext = fileName.substring(fileName.lastIndexOf('.') + 1);
            if (ext == "PNG" || ext == "png")
            {
                return true;
            }
            else
            {
                alert("{{trans('keywords.logo_alert');}}");
                return false;
            }
        }
        var fup = document.getElementById('icon');
        var fileName1 = fup.value;
        if (fileName1 != '')
        {
            var ext = fileName1.substring(fileName1.lastIndexOf('.') + 1);

            if (ext == "ICO" || ext == "ico")
            {
                return true;
            }
            else
            {
                alert("{{trans('keywords.ico_alert');}}");
                return false;
            }
        }
    }
</script>
<?php if ($success == 1) { ?>
    <script type="text/javascript">
        alert('{{trans('keywords.config_update_alert');}}');
    </script>
<?php } ?>
<?php if ($success == 2) { ?>
    <script type="text/javascript">
        alert('{{trans('keywords.config_wrong_alert');}}');
    </script>
<?php } ?>
<script>
    $(function () {
        $("[data-toggle='tooltip']").tooltip();
    });
</script>
<script type="text/javascript">
    $("#currencies").change(function () {
        var currency_selected = $("#currencies option:selected").val();
        console.log(currency_selected);
        $.ajax({
            type: "POST",
            url: "{{route('adminCurrency')}}",
            data: {'currency_selected': currency_selected},
            success: function (data) {
                if (data.success == true) {
                    console.log(data.rate);
                } else {
                    console.log(data.error_message);
                }
            }
        });
    });
</script>
@stop