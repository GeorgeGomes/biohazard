@extends('web.providerLayout')

@section('content')

<div class="col-md-12 mt">

    <div class="content-panel">

        <br><h4>{{trans('user_provider_web.bank_account_data');}}</h4><br>
        <form class="form-horizontal style-form" method="post" action="{{ URL::Route('updateProviderBankAccount') }}" enctype="multipart/form-data">
            <div class="form-group">
                <label class="col-sm-2 col-sm-2 control-label">{{trans('user_provider_web.holder_name');}}</label>
                <div class="col-sm-6">
                    <input type="text" class="form-control" name="holder" minlength="5" maxlength="30" value="{{ $bank_account? $bank_account->holder : Input::old("holder") }}">
                    <h5><span class="label label-info">     
                        {{trans('user_provider_web.holder_name_info');}}
                    </span></h5>
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-2 col-sm-2 control-label">
                    {{trans('user_provider_web.holder_document');}}
                </label> 
                <div class="col-sm-6">
                    <div class="input-group" style="width:100%;">
                        <input id="option_cpf" type="radio" name="option_document" value="individual" {{ !$bank_account || $bank_account->person_type == 'individual'? 'checked': '' }}> CPF
                        <input id="option_cnpj" type="radio" name="option_document" value="company" {{ $bank_account && $bank_account->person_type == 'company'? 'checked': '' }}> CNPJ
                        <input type="text" class="form-control" id="document" name="document" value="{{ $bank_account? $bank_account->document : Input::old("document") }}">
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-2 col-sm-2 control-label">{{trans('user_provider_web.bank');}}</label>
                <div class="col-sm-6">
                    <select class="form-control" id="bank_id" name="bank_id"> 
                        @foreach($banks as $bank)
                            <option  {{ ( $bank_account && $bank_account->bank_id == $bank->id ) || (Input::old("bank_id") == $bank->id) ? "selected" : ""}} value="{{ $bank->id }}">
                                {{ $bank->code }} - {{ $bank->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-2 col-sm-2 control-label">{{trans('user_provider_web.agency');}}</label>
                <div class="col-sm-6">
                    <input type="text" class="form-control" name="agency" maxlength="5" value="{{ $bank_account? $bank_account->agency  : Input::old("agency") }}">
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-2 col-sm-2 control-label">{{trans('user_provider_web.account_number');}}</label>
                <div class="col-sm-3">
                    <input type="text" class="form-control" name="account" maxlength="10" value="{{ $bank_account? $bank_account->account : Input::old("account") }}">
                </div>

                <label class="col-sm-2 col-sm-2 control-label">{{trans('user_provider_web.account_digit');}}</label>
                <div class="col-sm-1">
                    <input type="text" class="form-control" name="account_digit" maxlength="1" value="{{ $bank_account? $bank_account->account_digit :  Input::old("account_digit") }}">
                </div>
            </div>
     
            <span class="col-sm-7"></span>
            <button id="update" type="submit" class="btn btn-info">{{trans('user_provider_web.save');}}</button>

        </form>
    </div>

</div>

<script type="text/javascript">

    function id( el ){
        return document.getElementById( el );
    }

    function mascara(o,f){
        v_obj=o
        v_fun=f
        setTimeout("execmascara()",1)
    }
    function execmascara(){
        v_obj.value=v_fun(v_obj.value)
    }

    function cpfMask(v){
        v=v.replace(/\D/g,"")                    //Remove tudo o que não é dígito
        v=v.replace(/(\d{3})(\d)/,"$1.$2")       //Coloca um ponto entre o terceiro e o quarto dígitos
        v=v.replace(/(\d{3})(\d)/,"$1.$2")       //Coloca um ponto entre o terceiro e o quarto dígitos
                                                 //de novo (para o segundo bloco de números)
        v=v.replace(/(\d{3})(\d{1,2})$/,"$1-$2") //Coloca um hífen entre o terceiro e o quarto dígitos
        return v;
    }

    function cnpjMask(v) {
        v = v.replace( /\D/g , ""); //Remove tudo o que não é dígito
        v = v.replace( /^(\d{2})(\d)/ , "$1.$2"); //Coloca ponto entre o segundo e o terceiro dígitos
        v = v.replace( /^(\d{2})\.(\d{3})(\d)/ , "$1.$2.$3"); //Coloca ponto entre o quinto e o sexto dígitos
        v = v.replace( /\.(\d{3})(\d)/ , ".$1/$2"); //Coloca uma barra entre o oitavo e o nono dígitos
        v = v.replace( /(\d{4})(\d)/ , "$1-$2"); //Coloca um hífen depois do bloco de quatro dígitos
        return v;
    }

    function maskDocument(type){
        if(type == "cpf"){
            $("#document").attr('maxlength','14');
            mascara( id('document'), cpfMask );
            $('#document').keydown(function(){
                mascara( this, cpfMask );
            });
        }
        else{
            $("#document").attr('maxlength','18');
            mascara( id('document'), cnpjMask );
            $('#document').keydown(function(){
                mascara( this, cnpjMask );
            });
        }
    }

    $(document).ready(function() {
    
        if($('#option_cpf').is(':checked')){
            maskDocument("cpf");
        }
        else{
            maskDocument("cnpj");
        }

        $('#option_cpf').change(function() {
            maskDocument("cpf");
        });

        $('#option_cnpj').change(function() {
            maskDocument("cnpj");
        });
    });


</script>


@stop 