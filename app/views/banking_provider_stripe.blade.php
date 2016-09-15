@extends('layout')

@section('content')

<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>

<script type="text/javascript" src="https://js.stripe.com/v2/"></script>

<script type="text/javascript">
// This identifies your website in the createToken call below
            Stripe.setPublishableKey('<?= Config::get("app.stripe_publishable_key"); ?>');
            var stripeResponseHandler = function(status, response) {
            var $form = $('#payment-form');
                    if (response.error) {
            // Show the errors on the form
            $form.find('.payment-errors').text(response.error.message);
                    $form.find('button').prop('disabled', false);
            } else {
            var token = response.id;
                    // Insert the token into the form so it gets submitted to the server
                    $form.append($('<input type="hidden" id="stripeToken" name="stripeToken" />').val(token));
                    // and re-submit

                    jQuery($form.get(0)).submit();
            }
            };
            jQuery(function($) {

            $('#payment-form').submit(function(e) {
            console.log($('#stripeToken').length);
                    if ($('#stripeToken').length == 0)
            {
            var $form = $(this);
                    // Disable the submit button to prevent repeated clicks
                    $form.find('button').prop('disabled', true);
                    Stripe.bankAccount.createToken($form, stripeResponseHandler);
                    Stripe.bankAccount.createToken({
                    country: $('.country').val(),
                            currency: 'USD',
                            routing_number: $('.routing-number').val(),
                            account_number: $('.account-number').val()
                    }, stripeResponseHandler);
                    // Prevent the form from submitting with the default action
                    return false;
            }
            });
            });
// ...
</script>

<div class="box box-primary">
    <div class="box-header">
        <h3 class="box-title">{{trans('provider.bank_detail');}}</h3>
    </div><!-- /.box-header -->

    <form method="post" action="{{ URL::Route('AdminProviderSBanking') }}" id="payment-form"  enctype="multipart/form-data">

        <div class="form-group" style="margin-left:10px;margin-right:10px;">
            <input type="hidden" name="id" value="<?= $provider->id ?>">
            <input type="text" name="first_name" class="form-control" placeholder="{{trans('provider.first_name');}}" value="{{ $provider -> first_name }}" required><br>
            <input type="text" name="last_name" class="form-control" placeholder="{{trans('provider.last_name');}}" value="{{$provider -> last_name }}" required><br>
            <input type="text" size="20" data-stripe="country" class="form-control" id="country" value="US" placeholder="{{trans('user.pay_method');}}" disabled /><br>
            <input type="text" size="20" data-stripe="accountNumber" placeholder="{{trans('provider.account_number');}}" class="form-control" name="account_number" required /><br>
            <input type="text" size="40" data-stripe="routingNumber"  class="form-control" placeholder="{{trans('provider.routing_number');}}" name="routing_number" required /><br>
            <select name="type" class="form-control">
                <option value="individual">{{trans('provider.individual');}}</option>
                <option value="corporate">{{trans('provider.coorporative');}}</option>
            </select><br>
            <input type="text" name="email" class="form-control" placeholder="{{trans('provider.mail_grid');}}" value="{{$provider -> email }}" required><br>
            <br><input type="submit" value="{{trans('keywords.save_change');}}" class="btn btn-block btn-flat btn-success">
        </div>
    </form>
</div>

<script type="text/javascript">
            $("#payment-form").validate({
    rules: {
    first_name: "required",
            last_name: "required",
            country: "required",
            email: {
            required: true,
                    email: true
            },
            type: "required",
            account_number: "required",
            routing_number: "required",
    }
    });</script>


<?php if ($success == 1) { ?>
    <script type="text/javascript">
            alert('{{trans('keywords.profile_updated');}}');
<?php } ?>
<?php if ($success == 2) { ?>
    <script type="text/javascript">
             alert('{{trans('keywords.config_wrong_alert');}}');
    </script>
<?php } ?>


@stop