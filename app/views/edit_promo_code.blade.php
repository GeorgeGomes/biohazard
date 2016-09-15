@extends('layout')

@section('content')

<script src="<?php echo asset_url(); ?>/javascript/jquery.maskMoney.min.js" type="text/javascript"></script>

@if (Session::has('msg'))
<h4 class="alert alert-info">
    {{{ Session::get('msg') }}}
    {{{Session::put('msg',NULL)}}}
</h4>
@endif
<div class="box box-primary">
    <div class="box-header">
        <h3 class="box-title">{{trans('payment.code_promo_add');}}</h3>
    </div><!-- /.box-header -->
    <!-- form start -->
    <form role="form" id="form" method="post" action="{{ URL::Route('AdminPromoUpdate') }}"  enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?= $id ?>">
        <div class="box-body">
            <div class="form-group col-md-6 col-sm-6">
                <label>{{trans('payment.code_promo_name');}}</label>
                <input type="text" class="form-control" name="code_name" value="{{$promo_code? $promo_code->coupon_code : ''}}"placeholder="Código Promocional" >
            </div>
            <div class="form-group col-md-6 col-sm-6">
                <label>{{trans('payment.user_allowed');}}</label>
                <span id="no_number_error1" style="display: none"> </span>
                <input class="form-control" type="text" name="code_uses" value="{{$promo_code? $promo_code->uses : ''}}" placeholder="Quantidade de Usuários" onkeypress="return IsNumeric(event, 1);">
            </div>

            <div class="form-group col-md-6 col-sm-6">
                <label>{{trans('payment.code_promo_type');}}</label>
                <div class="input-group" style="width:100%;">
                    <input id="code_percentage" name="code_type" type="radio" value="1" checked> {{trans('payment.percentage');}}
                    <input id="code_value" name="code_type" type="radio" value="2" {{ $promo_code && $promo_code->type == '2'? 'checked' : ''}}> {{trans('payment.absolute');}}
                </div>
            </div>

            <div class="form-group col-md-6 col-sm-6">
                <label>{{trans('payment.code_promo_amount');}}</label>
                <span id="no_amount_error1" style="display: none"></span>
                <input class="form-control" type="text" name="code_value" id="value" value="{{$promo_code? $promo_code->value : ''}}" placeholder="Bônus Promocional" onkeypress="return Isamount(event, 1);">
            </div>

            <div class="form-group col-md-6 col-sm-6">
                <label>{{trans('dashboard.start_date');}}</label>
                <br>
                <input type="text" class="form-control" style="overflow:hidden;" id="start-date" name="start_date" value="{{$promo_code? date("d/m/Y", strtotime(trim($promo_code->start_date))) : ''}}" placeholder="{{trans('dashboard.start_date');}}">
            </div>

            <div class="form-group col-md-6 col-sm-6">
                <label>{{trans('dashboard.date_expire');}}</label>
                <br>
                <input type="text" class="form-control" style="overflow:hidden;" id="end-date" name="code_expiry" placeholder="{{trans('dashboard.date_expire');}}"  value="{{$promo_code? date("d/m/Y", strtotime(trim($promo_code->expiry))) : ''}}">
            </div>
        </div><!-- /.box-body -->
        <div class="box-footer">
            <button type="submit" class="btn btn-primary btn-flat btn-block">{{trans('keywords.save_change');}}</button>
        </div>
    </form>
</div>
<script type="text/javascript">
    $("#form").validate({
        rules: {
            code_name: "required",
            code_value: "required",
            code_uses: "required",
            code_expiry: "required",
        }
    });

</script>

<script>
    $(function () {
        $("#start-date").datepicker({
            defaultDate: "+1w",
            changeMonth: true,
            numberOfMonths: 1,
            dateFormat: 'dd/mm/yy',
            onClose: function (selectedDate) {
                $("#end-date").datepicker("option", "minDate", selectedDate);
            }
        });
        $("#end-date").datepicker({
            defaultDate: "+1w",
            changeMonth: true,
            numberOfMonths: 1,
            dateFormat: 'dd/mm/yy',
            onClose: function (selectedDate) {
                $("#start-date").datepicker("option", "maxDate", selectedDate);
            }
        });
    });
</script>

<script type="text/javascript">

    function id( el ){
        return document.getElementById( el );
    }

    function maskCodeValue($type){
        if($type == "percentage"){
            $("#value").maskMoney('destroy');
            $("#value").mask('##0%', {reverse: true});
        }
        else{ 
            $("#value").unmask();
            $("#value").maskMoney({prefix:'R$ ', thousands:'.', decimal:','});
        }
    }

    $(document).ready(function() {

        if($('#code_percentage').is(':checked')){
            console.log("PERCENTAGE");
            maskCodeValue("percentage");
        }
        else{
            console.log("VALUE");
            maskCodeValue("value");
        }

        id('code_percentage').onchange = function() {
            maskCodeValue("percentage");
        }

        id('code_value').onchange = function() {
            maskCodeValue("value");
        }

    });

</script>


@stop