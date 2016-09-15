
@extends('layout')

@section('content')

<div class="box box-primary">
              
                                <!-- form start -->
                               <form method="post" id="main-form" action="{{ URL::Route('AdminUserUpdate') }}"  enctype="multipart/form-data">
            <input type="hidden" name="id" value="<?= $owner->id ?>">

                                    <div class="box-body">
                                        <div class="form-group">
                                            <label>{{trans('provider.first_name');}}</label>
                                            <input type="text" class="form-control" name="first_name" value="<?= $owner->first_name ?>" placeholder="{{trans('provider.first_name');}}" >

                                        </div>

                                        <div class="form-group">
                                            <label>{{trans('provider.last_name');}}</label>
                                            <input class="form-control" type="text" name="last_name" value="<?= $owner->last_name ?>" placeholder="{{trans('provider.last_name');}}">


                                
                                        </div>

                                         <div class="form-group">
                                            <label>{{ trans('provider.mail_grid');}}</label>
                                            <input class="form-control" type="email" name="email" value="<?= $owner->email ?>" placeholder="{{trans('provider.mail_grid');}}">

                                
                                        </div>

                                         <div class="form-group">
                                            <label>{{ trans('provider.phone_grid');}}</label>
                                            <input class="form-control" type="text" name="phone" value="<?= $owner->phone ?>" placeholder="{{trans('provider.phone_grid');}}">

                                
                                        </div>


                                         <div class="form-group">
                                            <label>{{trans('provider.address');}}</label>
                                            <input class="form-control" type="text" name="address" value="<?= $owner->address ?>" placeholder="{{trans('provider.address');}}">


                                        </div>


                                         <div class="form-group">
                                            <label>{{trans('provider.state');}}</label>
                                            <input class="form-control" type="text" name="state" value="<?= $owner->state ?>" placeholder="{{trans('provider.state');}}">

                                        </div>



                                        <div class="form-group">
                                            <label>{{trans('provider.zipcode');}}</label>
                                            <input class="form-control" type="text" name="zipcode" value="<?= $owner->zipcode ?>" placeholder="{{trans('provider.zipcode');}}">

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
    alert("{{trans('blade.user_perfil_updated');}}");
</script>
<?php } ?>
<?php
if($success == 2) { ?>
<script type="text/javascript">
    alert("{{trans('blade.user_perfil_not_updated');}}");
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
</script>

@stop