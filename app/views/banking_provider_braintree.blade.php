@extends('layout')

@section('content')

<link href="{{asset('css/bootstrap-datetimepicker.min.css')}}" rel="stylesheet">





  <div class="box box-primary">
                                <div class="box-header">
                                    <h3 class="box-title">{{trans('provider.bank_detail');}}</h3>
                                </div><!-- /.box-header -->

              <form method="post" action="{{ URL::Route('AdminProviderBBanking') }}" id="addressformadmin"  enctype="multipart/form-data">
                <div class="form-group" style="margin-left:10px;margin-right:10px;">
                <input type="hidden" name="id" value="<?= $provider->id ?>">
                <input type="text" name="first_name" class="form-control" placeholder="{{trans('provider.first_name');}}" value="{{ $provider -> first_name }}" required><br>
                <input type="text" name="last_name" class="form-control" placeholder="{{trans('provider.last_name');}}" value="{{$provider -> last_name }}" required><br>
                <input type="text" name="email" class="form-control" placeholder="{{trans('provider.mail_grid');}}" value="{{$provider -> email }}" required><br>
                <input type="text" name="phone" class="form-control" placeholder="{{trans('provider.phone_grid');}}" value="{{$provider -> phone }}" required><br>
                <input type='text' name="dob" class="form-control" placeholder="{{trans('dashboard.date_birth');}}" id='datetimepicker6' required><br>
                <script type="text/javascript">
                    $(function () {
                        $('#datetimepicker6').datetimepicker({
                          pickTime: false,
                      });
                    });
                </script>
                <input type="text" name="ssn" class="form-control" placeholder="{{trans('provider.ssn');}}" required><br>
                <label>{{trans('provider.address');}}</label>
                <input type="text" name="streetAddress" class="form-control" placeholder="{{trans('blade.street');}}" required><br>
                <input type="text" name="locality" class="form-control" placeholder="{{trans('blade.locale');}}" required><br>
                <input type="text" name="region" class="form-control" placeholder="{{trans('blade.region');}}" required><br>
                <input type="text" name="postalCode" class="form-control" placeholder="{{trans('provider.zipcode');}}" required><br>
                <label>Banco</label>
                <input type="text" name="bankemail" class="form-control" value="{{$provider -> email }}" required><br>
                <input type="text" name="bankphone" class="form-control" value="{{$provider -> phone }}" required><br>
                <input type="text" name="accountNumber" class="form-control" placeholder="{{trans('provider.account_number');}}" required><br>
                <input type="text" name="routingNumber" class="form-control" placeholder="{{trans('provider.routing_number');}}" required><br>
                <br><input type="submit" value="{{trans('keywords.save_change');}}" class="btn btn-green">
                </div>
              </form>


</div>




<script type="text/javascript" src="{{asset('js/moment.js')}}"></script>
<script type="text/javascript" src="{{asset('js/bootstrap-datetimepicker.js')}}"></script>
<?php
if($success == 1) { ?>
<script type="text/javascript">
    alert({{ trans('customize.Provider')}}."{{trans('keywords.config_wrong_alert');}}";);
</script>
<?php } ?>
<?php
if($success == 2) { ?>
<script type="text/javascript">
    alert("{{trans('keywords.config_wrong_alert');}}");
</script>
<?php } ?>


@stop