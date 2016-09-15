
@extends('layout')

@section('content')

<!-- tel input libs -->
<link rel="stylesheet" href="{{ asset_url(); }}/library/telinput/css/intlTelInput.css">
<script src="{{ asset_url(); }}/library/telinput/js/intlTelInput.min.js"></script>
<style type="text/css">
  .intl-tel-input {width: 100%;}
</style>

<div class="box box-primary">

  <!-- form start -->
 <form method="post" id="main-form" action="{{ URL::Route('AdminUserUpdate') }}"  enctype="multipart/form-data">
      <input type="hidden" name="id" value="<?= $user->id ?>">

      <div class="box-body">
          <div class="form-group">
              <label>{{trans('provider.first_name');}}</label>
              <input type="text" class="form-control" name="first_name" value="<?= $user->first_name ?>" placeholder="{{trans('provider.first_name');}}" >

          </div>

          <div class="form-group">
              <label>{{trans('provider.last_name');}}</label>
              <input class="form-control" type="text" name="last_name" value="<?= $user->last_name ?>" placeholder="{{trans('provider.last_name');}}">


  
          </div>

           <div class="form-group">
              <label>{{ trans('provider.mail_grid');}}</label>
              <input class="form-control" type="email" name="email" value="<?= $user->email ?>" placeholder="{{trans('provider.mail_grid');}}">

  
          </div>

           <div class="form-group">
              <label>{{ trans('provider.phone_grid');}}</label>

              <input class="form-control" type="text" name="phone" id="phone" maxlength="15" value="<?= $user->phone ?>" placeholder="{{trans('provider.phone_grid');}}">

              <input type="text" name="valid_phone" id="valid_phone" value=""  style="visibility:hidden;" />
  
          </div>
     
      </div><!-- /.box-body -->

      <div class="box-footer">
        <button type="submit" id="edit" class="btn btn-primary btn-flat btn-block">{{trans('keywords.save_change');}}</button>
      </div>
  </form>
</div>



<?php
if($success == 1) { ?>
<script type="text/javascript">
    alert('{{trans(blade.user_perfil_updated)}}');
</script>
<?php } ?>
<?php
if($success == 2) { ?>
<script type="text/javascript">
    alert('{{trans(blade.user_perfil_not_updated)}}');
</script>
<?php } ?>

<script type="text/javascript">
$("#main-form").validate({
  rules: {
    first_name: "required",
    last_name: "required",

    email: {
      required: true,
      email: true
    },

   phone: {
    required: true,
    digits: true,
  }


  }
});

$(document).ready(function() {
    var telInput = $("#phone"),
        validPhone = $("#valid_phone");
        
    var checkPhoneFieldEnter = 0;

    // initialise plugin
    telInput.intlTelInput({
      utilsScript: "{{ asset_url(); }}/library/telinput/js/utils.js",
      formatOnInit: false
    });


    if (checkPhoneFieldEnter == 0) {
      validPhone.val(1);
    }
    
    // on blur: validate
    telInput.blur(function() {

      if ($.trim(telInput.val())) {
        checkPhoneFieldEnter = 1;
        if (telInput.intlTelInput("isValidNumber")) {
          validPhone.val(1);
          formValidator.element('#valid_phone');
        } else {
          validPhone.val('');
          formValidator.element('#valid_phone');
        }
        }
    });
});
</script>

@stop