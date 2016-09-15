@extends('layout')

@section('content')

                  @if (Session::has('msg'))
                    <h4 class="alert alert-info">
                    {{{ Session::get('msg') }}}
                    {{{Session::put('msg',NULL)}}}
                    </h4>
                   @endif


                 <div class="box box-primary">
                                <div class="box-header">
                                    <h3 class="box-title">{{trans('blade.add');}} {{ trans('customize.Provider');}}</h3>
                                </div><!-- /.box-header -->
                                <!-- form start -->
                                <form role="form" class="form" id="main-form" method="post" action="{{ URL::Route('AdminProviderUpdate') }}"  enctype="multipart/form-data">
                                <input type="hidden" name="id" value="0>">

                                    <div class="box-body">
                                        <div class="form-group">
                                            <label>{{trans('provider.first_name');}}</label>
                                            <input type="text" class="form-control" name="first_name" value="" placeholder="{{trans('provider.first_name');}}" >
                                          
                                        </div>

                                        <div class="form-group">
                                            <label>{{trans('provider.last_name');}}</label>
                                            <input class="form-control" type="text" name="last_name" value="" placeholder="{{trans('provider.last_name');}}">
                                
                                        </div>

                                         <div class="form-group">
                                            <label>{{ trans('provider.mail_grid');}}</label>
                                            <input class="form-control" type="email" name="email" value="" placeholder="{{trans('provider.mail_grid');}}">
                                
                                        </div>

                                         <div class="form-group">
                                            <label>{{ trans('provider.phone_grid');}}</label>
                                            <input class="form-control" type="text" name="phone" value="" placeholder="{{trans('provider.phone_grid');}}">
                                
                                        </div>

                                         <div class="form-group">
                                            <label>{{ trans('provider.bio_grid');}}</label>
                                            <input class="form-control" type="text" name="bio" value="" placeholder="{{trans('provider.bio_grid');}}">
                                
                                        </div>


                                         <div class="form-group">
                                            <label>{{trans('provider.address');}}</label>
                                            <input class="form-control" type="text" name="address" value="" placeholder="{{trans('provider.address');}}">
                                
                                        </div>


                                         <div class="form-group">
                                            <label>{{trans('provider.state');}}</label>
                                            <input class="form-control" type="text" name="state" value="" placeholder="{{trans('provider.state');}}">
                                
                                        </div>


                                         <div class="form-group">
                                            <label>{{trans('provider.country');}}</label>
                                            <input class="form-control" type="text" name="country" value="" placeholder="{{trans('provider.country');}}">
                                
                                        </div>

                                        <div class="form-group">
                                            <label>{{trans('provider.zipcode');}}</label>
                                            <input class="form-control" type="text" name="zipcode" value="" placeholder="{{trans('provider.zipcode');}}">
                                
                                        </div>


                                        <div class="form-group">
                                            <label>{{ trans('provider.picture_grid');}}</label>
                                            <input class="form-control" type="file" name="pic" >
                                            <p class="help-block">{{trans('blade.please_image');}}</p>
                                        </div>
                                   
                                    </div><!-- /.box-body -->

                                    <div class="box-footer">
                                        <button type="submit" class="btn btn-primary btn-flat btn-block">{{trans('blade.save');}}</button>
                                    </div>
                                </form>
                            </div>

<script type="text/javascript">
$("#main-form").validate({
  rules: {
    first_name: "required",
    last_name: "required",
    country: "required",
    email: {
      required: true,
      email: true
    },
    state: "required",
    address: "required",
    bio: "required",
    zipcode: {
    required: true,
    digits: true,
  },
   phone: {
    required: true,
    digits: true,
  }


  }
});
</script>


@stop